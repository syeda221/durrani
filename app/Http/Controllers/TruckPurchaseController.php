<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\InvoiceSeries;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\TruckConsignment;
use App\Models\TruckConsignmentItem;
use App\Models\Vendor;
use App\Models\VendorLedger;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TruckPurchaseController extends Controller
{
    /**
     * Display listing of all Truck Consignments
     */
    public function index(Request $request)
    {
        $query = TruckConsignment::with(['warehouse', 'purchases.vendor', 'items.product', 'items.vendor'])
            ->withCount(['purchases', 'sales']);

        if ($request->filled('truck_no')) {
            $query->where('truck_no', 'like', '%' . $request->truck_no . '%');
        }

        if ($request->filled('driver_name')) {
            $query->where('driver_name', 'like', '%' . $request->driver_name . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('arrival_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('arrival_date', '<=', $request->date_to);
        }

        $consignments = $query->orderByDesc('arrival_date')
            ->orderByDesc('id')
            ->paginate(15);

        $stats = [
            'total_consignments' => TruckConsignment::count(),
            'in_transit'         => TruckConsignment::where('status', 'in_transit')->count(),
            'completed'          => TruckConsignment::where('status', 'completed')->count(),
            'remaining_units'    => (float) TruckConsignmentItem::whereHas('consignment', function ($q) {
                $q->where('status', 'in_transit');
            })->sum('remaining_qty'),
        ];

        return view('admin_panel.purchase.truck.index', compact('consignments', 'stats'));
    }

    /**
     * Show form for Multi-Vendor Truck Purchase (Initial Truck Load)
     */
    public function create()
    {
        $vendors = Vendor::orderBy('name')->get();
        $warehouses = Warehouse::orderByRaw("id = 1 DESC, id ASC")->get();
        $products = Product::with('unit')
            ->select('id', 'item_name', 'item_code', 'purchase_price_per_piece', 'pieces_per_box', 'size_mode', 'unit_id')
            ->where('is_active', true)
            ->orderBy('item_name')
            ->get();

        $nextConsignmentNo = TruckConsignment::generateNextConsignmentNo();

        $savedTrucks = TruckConsignment::select('id', 'truck_no', 'driver_name', 'driver_phone', 'transporter_name')
            ->whereNotNull('truck_no')
            ->where('truck_no', '!=', '')
            ->orderByDesc('id')
            ->limit(100)
            ->get()
            ->unique('truck_no')
            ->take(50)
            ->values();

        return view('admin_panel.purchase.truck.create', compact(
            'vendors',
            'warehouses',
            'products',
            'nextConsignmentNo',
            'savedTrucks'
        ));
    }

    /**
     * Helper to parse cartons & loose pieces
     */
    public static function parseCartonQty($qty)
    {
        $qtyStr = (string) $qty;
        if (is_numeric($qtyStr)) {
            $qtyStr = rtrim(rtrim(sprintf('%.6f', (float) $qtyStr), '0'), '.');
        }
        $qtyStr = trim($qtyStr);
        if (str_starts_with($qtyStr, '.')) {
            $qtyStr = '0' . $qtyStr;
        }
        if (strpos($qtyStr, '.') !== false) {
            $parts = explode('.', $qtyStr);
            $boxes = (int) ($parts[0] ?? 0);
            $loose = (int) ($parts[1] ?? 0);
        } else {
            $boxes = (int) ($qtyStr ?: 0);
            $loose = 0;
        }
        return [$boxes, $loose];
    }

    /**
     * Upsert warehouse stocks
     */
    private function upsertStocks(int $productId, float $qtyPiecesDelta, int $warehouseId): void
    {
        $stock = WarehouseStock::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->first();

        $product = Product::find($productId);
        $ppb = ($product && $product->pieces_per_box > 0) ? (float) $product->pieces_per_box : 1.0;

        if ($stock) {
            $stock->total_pieces += $qtyPiecesDelta;
            $stock->quantity = $stock->total_pieces / $ppb;
            $stock->save();
        } else {
            WarehouseStock::create([
                'warehouse_id' => $warehouseId,
                'product_id'   => $productId,
                'total_pieces' => $qtyPiecesDelta,
                'quantity'     => $qtyPiecesDelta / $ppb,
            ]);
        }
    }

    /**
     * Store Multi-Vendor Truck Purchase (Initial Loading)
     */
    public function store(Request $request)
    {
        $request->validate([
            'truck_no'     => 'required|string|max:100',
            'arrival_date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'vendors'      => 'required|array|min:1',
            'vendors.*.vendor_id' => 'required|exists:vendors,id',
            'vendors.*.items'     => 'required|array|min:1',
            'vendors.*.items.*.product_id' => 'required|exists:products,id',
            'vendors.*.items.*.qty'        => 'required|numeric|min:0.001',
            'vendors.*.items.*.price'      => 'required|numeric|min:0',
        ]);

        try {
            $consignment = DB::transaction(function () use ($request) {
                // 1. Create Consignment Header with status: 'in_transit'
                $consignmentNo = $request->consignment_no ?: TruckConsignment::generateNextConsignmentNo();
                
                while (TruckConsignment::where('consignment_no', $consignmentNo)->exists()) {
                    $prefix = 'TRK-' . date('Ym') . '-';
                    $rand = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
                    $consignmentNo = $prefix . $rand;
                }

                $truckNo = trim($request->truck_no);
                $driverName = trim($request->driver_name ?? '');
                $driverPhone = trim($request->driver_phone ?? '');
                $transporterName = trim($request->transporter_name ?? '');
                $biltyNo = trim($request->bilty_no ?? '');
                $warehouseId = (int) $request->warehouse_id;
                $arrivalDate = $request->arrival_date ?: now()->toDateString();
                $totalFreight = (float) ($request->total_freight ?? 0);

                $consignment = TruckConsignment::create([
                    'consignment_no'   => $consignmentNo,
                    'truck_no'         => $truckNo,
                    'driver_name'      => $driverName,
                    'driver_phone'     => $driverPhone,
                    'transporter_name' => $transporterName,
                    'bilty_no'         => $biltyNo,
                    'warehouse_id'     => $warehouseId,
                    'arrival_date'     => $arrivalDate,
                    'total_freight'    => $totalFreight,
                    'status'           => 'in_transit', // Loaded & on route for direct deliveries
                    'remarks'          => $request->remarks,
                    'created_by'       => Auth::id() ?? 1,
                ]);

                // 2. Process each Vendor Block
                $vendorBlocks = (array) $request->input('vendors', []);
                $transactionService = app(TransactionService::class);

                foreach ($vendorBlocks as $vIndex => $vData) {
                    $vendorId = (int) ($vData['vendor_id'] ?? 0);
                    $itemsData = (array) ($vData['items'] ?? []);

                    if (!$vendorId || empty($itemsData)) {
                        continue;
                    }

                    // Generate next Invoice Series for Purchase (PINV)
                    $invoiceNo = InvoiceSeries::generateNextNo('PINV');
                    while (Purchase::where('invoice_no', $invoiceNo)->exists()) {
                        InvoiceSeries::incrementCounterForInvoice($invoiceNo);
                        $invoiceNo = InvoiceSeries::generateNextNo('PINV');
                    }
                    InvoiceSeries::incrementCounterForInvoice($invoiceNo);

                    $vendorBillNo = trim($vData['vendor_bill_no'] ?? '');
                    $vendorDiscount = (float) ($vData['discount'] ?? 0);
                    $freightShare = (float) ($vData['freight_share'] ?? 0);
                    $vendorNotes = trim($vData['notes'] ?? '');

                    // Create Purchase Record
                    $purchase = Purchase::create([
                        'purchase_type'        => 'direct_purchase',
                        'purchase_status'      => 'posted',
                        'status_purchase'      => 'approved',
                        'receiving_status'     => 'received',
                        'branch_id'            => 1,
                        'warehouse_id'         => $warehouseId,
                        'vendor_id'            => $vendorId,
                        'purchase_date'        => $arrivalDate,
                        'credit_days'          => 0,
                        'invoice_no'           => $invoiceNo,
                        'purchase_prefix'      => 'PINV',
                        'vendor_bill_no'       => $vendorBillNo,
                        'truck_consignment_id' => $consignment->id,
                        'truck_no'             => $truckNo,
                        'driver_name'          => $driverName,
                        'driver_phone'         => $driverPhone,
                        'transporter_name'     => $transporterName,
                        'bilty_no'             => $biltyNo,
                        'shipping_charges'     => $freightShare,
                        'discount'             => $vendorDiscount,
                        'note'                 => $vendorNotes ?: "Loaded on Truck #{$truckNo} (Consignment: {$consignmentNo})",
                        'subtotal'             => 0,
                        'extra_cost'           => 0,
                        'net_amount'           => 0,
                        'paid_amount'          => 0,
                        'due_amount'           => 0,
                    ]);

                    $subtotal = 0;

                    foreach ($itemsData as $itemRow) {
                        $productId = (int) ($itemRow['product_id'] ?? 0);
                        $qty = (float) ($itemRow['qty'] ?? 0);
                        $price = (float) ($itemRow['price'] ?? 0);
                        $itemDiscount = (float) ($itemRow['item_discount'] ?? 0);
                        $unit = trim($itemRow['unit'] ?? 'Pcs');

                        if (!$productId || $qty <= 0) {
                            continue;
                        }

                        $product = Product::find($productId);
                        $ppb = ($product && $product->pieces_per_box > 0) ? (float) $product->pieces_per_box : 1.0;
                        $sizeMode = $product->size_mode ?? 'default';

                        $boxesQty = (float) ($itemRow['boxes_qty'] ?? 0);
                        $looseQty = (float) ($itemRow['loose_qty'] ?? 0);

                        $lineTotal = ($qty * $price) - $itemDiscount;
                        $subtotal += $lineTotal;

                        // Create PurchaseItem
                        PurchaseItem::create([
                            'purchase_id'      => $purchase->id,
                            'product_id'       => $productId,
                            'warehouse_id'     => $warehouseId,
                            'qty'              => $qty,
                            'received_qty'     => $qty,
                            'price'            => $price,
                            'unit'             => $unit,
                            'item_discount'    => $itemDiscount,
                            'line_total'       => $lineTotal,
                            'boxes_qty'        => $boxesQty,
                            'loose_qty'        => $looseQty,
                            'pieces_per_box'   => $ppb,
                            'size_mode'        => $sizeMode,
                        ]);

                        // Record Inward Truck Item for Live Tracking
                        TruckConsignmentItem::create([
                            'truck_consignment_id' => $consignment->id,
                            'purchase_id'          => $purchase->id,
                            'vendor_id'            => $vendorId,
                            'product_id'           => $productId,
                            'unit'                 => $unit,
                            'loaded_qty'           => $qty,
                            'purchase_price'       => $price,
                            'sold_qty'             => 0,
                            'unloaded_qty'         => 0,
                            'remaining_qty'        => $qty,
                        ]);
                    }

                    // Update Purchase totals
                    $netAmount = max(0, $subtotal - $vendorDiscount + $freightShare);
                    $purchase->update([
                        'subtotal'   => $subtotal,
                        'net_amount' => $netAmount,
                        'due_amount' => $netAmount,
                    ]);

                    // Update Vendor Ledger (You owe vendor for the purchase)
                    $prevClosing = VendorLedger::where('vendor_id', $vendorId)->value('closing_balance') ?? 0;
                    VendorLedger::updateOrCreate(
                        ['vendor_id' => $vendorId],
                        [
                            'vendor_id'        => $vendorId,
                            'admin_or_user_id' => Auth::id() ?? 1,
                            'previous_balance' => $prevClosing,
                            'opening_balance'  => $prevClosing,
                            'closing_balance'  => $prevClosing + $netAmount,
                        ]
                    );

                    // Create Purchase Voucher for Accounting
                    try {
                        $transactionService->createPurchaseVoucher($purchase);
                    } catch (\Throwable $e) {
                        Log::error("Truck Purchase Voucher Error: " . $e->getMessage());
                    }
                }

                return $consignment;
            });

            if ($request->ajax()) {
                return response()->json([
                    'success'      => true,
                    'message'      => "Truck Consignment #{$consignment->consignment_no} loaded and registered in transit!",
                    'redirect_url' => route('truck-purchase.show', $consignment->id),
                ]);
            }

            return redirect()->route('truck-purchase.show', $consignment->id)
                ->with('success', "Truck Consignment #{$consignment->consignment_no} loaded successfully! You can now make direct sales from this truck on route.");

        } catch (\Throwable $e) {
            Log::error("Truck Purchase Store Error: " . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error saving truck purchase: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Display a specific Truck Consignment Summary & Live Balance Sheet
     */
    public function show($id)
    {
        $consignment = TruckConsignment::with([
            'warehouse',
            'unloadedWarehouse',
            'creator',
            'purchases.vendor',
            'items.product',
            'items.vendor',
            'sales.customer_relation',
            'sales.items.product',
        ])->findOrFail($id);

        $warehouses = Warehouse::orderByRaw("id = 1 DESC, id ASC")->get();

        return view('admin_panel.purchase.truck.show', compact('consignment', 'warehouses'));
    }

    /**
     * Show Form to Make a Direct Sale From this Truck
     */
    public function createSale($id)
    {
        $consignment = TruckConsignment::with([
            'items.product',
            'items.vendor'
        ])->findOrFail($id);

        if ($consignment->status === 'completed') {
            return redirect()->route('truck-purchase.show', $id)
                ->with('error', 'This truck consignment has already been unloaded and completed. No further sales can be made from it.');
        }

        $customers = Customer::where(function($q) {
            $q->where('status', 'active')->orWhereNull('status')->orWhere('status', '1');
        })->orderBy('customer_name')->get();
        $availableItems = $consignment->items->where('remaining_qty', '>', 0);

        if ($availableItems->isEmpty()) {
            return redirect()->route('truck-purchase.show', $id)
                ->with('error', 'All items on this truck have already been sold or unloaded.');
        }

        return view('admin_panel.purchase.truck.create_sale', compact('consignment', 'customers', 'availableItems'));
    }

    /**
     * Store Direct Sale From this Truck
     */
    public function storeSale(Request $request, $id)
    {
        $consignment = TruckConsignment::with('items')->findOrFail($id);

        if ($consignment->status === 'completed') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'This truck consignment has already been completed.'], 400);
            }
            return back()->with('error', 'This truck consignment has already been completed.');
        }

        // Filter out items with 0 or empty qty before validation
        $rawItems = (array) $request->input('items', []);
        $validItems = array_values(array_filter($rawItems, function ($item) {
            return isset($item['qty']) && (float) $item['qty'] > 0;
        }));
        $request->merge(['items' => $validItems]);

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_date'   => 'required|date',
            'items'       => 'required|array|min:1',
            'items.*.truck_consignment_item_id' => 'required|exists:truck_consignment_items,id',
            'items.*.qty'                       => 'required|numeric|min:0.001',
            'items.*.price'                     => 'required|numeric|min:0',
        ], [
            'customer_id.required' => 'Customer select karna lazmi hai.',
            'items.required'       => 'Kam az kam aik product ki deliver quantity (Qty > 0) enter karein.',
            'items.min'            => 'Kam az kam aik product ki deliver quantity (Qty > 0) enter karein.',
            'items.*.qty.min'      => 'Product ki quantity kam az kam 0.001 honi chahiye.',
        ]);

        try {
            $sale = DB::transaction(function () use ($request, $consignment) {
                // Generate next Invoice No for Sale (INV)
                $invoiceNo = InvoiceSeries::generateNextNo('INV');
                while (Sale::where('invoice_no', $invoiceNo)->exists()) {
                    InvoiceSeries::incrementCounterForInvoice($invoiceNo);
                    $invoiceNo = InvoiceSeries::generateNextNo('INV');
                }
                InvoiceSeries::incrementCounterForInvoice($invoiceNo);

                $customerId = (int) $request->customer_id;
                $saleDate = $request->sale_date ?: now()->toDateString();
                $discount = (float) ($request->discount ?? 0);
                $shipping = (float) ($request->shipping_charges ?? 0);
                $paidAmount = (float) ($request->paid_amount ?? 0);

                $sale = Sale::create([
                    'invoice_no'           => $invoiceNo,
                    'customer_id'          => $customerId,
                    'warehouse_id'         => $consignment->warehouse_id,
                    'truck_consignment_id' => $consignment->id,
                    'sale_status'          => 'posted',
                    'sale_type'            => 'direct_sale',
                    'delivery_status'      => 'delivered',
                    'total_items'          => 0,
                    'total_bill_amount'    => 0,
                    'total_net'            => 0,
                    'cash'                 => $paidAmount,
                    'comment'              => $request->comment ?: "Direct delivery from Truck #{$consignment->truck_no} (Consignment: {$consignment->consignment_no})",
                    'created_at'           => $saleDate,
                ]);

                $subtotal = 0;
                $totalPieces = 0;
                $itemsCount = 0;

                foreach ($request->input('items', []) as $line) {
                    $truckItemId = (int) ($line['truck_consignment_item_id'] ?? 0);
                    $sellQty = (float) ($line['qty'] ?? 0);
                    $sellPrice = (float) ($line['price'] ?? 0);
                    $lineDisc = (float) ($line['discount'] ?? 0);

                    if (!$truckItemId || $sellQty <= 0) {
                        continue;
                    }

                    $truckItem = TruckConsignmentItem::where('truck_consignment_id', $consignment->id)
                        ->lockForUpdate()
                        ->findOrFail($truckItemId);

                    if ($sellQty > (float)$truckItem->remaining_qty) {
                        throw new \Exception("Cannot sell {$sellQty} of {$truckItem->product->item_name}. Only {$truckItem->remaining_qty} remaining in truck.");
                    }

                    $lineTotal = ($sellQty * $sellPrice) - $lineDisc;
                    $subtotal += $lineTotal;
                    $totalPieces += $sellQty;
                    $itemsCount++;

                    // Create SaleItem
                    SaleItem::create([
                        'sale_id'                   => $sale->id,
                        'truck_consignment_id'      => $consignment->id,
                        'truck_consignment_item_id' => $truckItemId,
                        'vendor_id'                 => $truckItem->vendor_id,
                        'product_id'                => $truckItem->product_id,
                        'product_name'              => $truckItem->product->item_name ?? 'Product #'.$truckItem->product_id,
                        'qty'                       => $sellQty,
                        'total_pieces'              => $sellQty,
                        'delivered_qty'             => $sellQty,
                        'price'                     => $sellPrice,
                        'purchase_price'            => $truckItem->purchase_price,
                        'discount_amount'           => $lineDisc,
                        'total'                     => $lineTotal,
                        'unit_id'                   => $truckItem->product->unit_id,
                    ]);

                    // Update Truck Consignment Item Stock
                    $truckItem->sold_qty += $sellQty;
                    $truckItem->remaining_qty = max(0, $truckItem->loaded_qty - $truckItem->sold_qty - $truckItem->unloaded_qty);
                    $truckItem->save();
                }

                $totalNet = max(0, $subtotal - $discount + $shipping);

                $sale->update([
                    'total_items'       => $itemsCount,
                    'total_bill_amount' => $subtotal,
                    'total_net'         => $totalNet,
                ]);

                // Update Customer Ledger
                $lastLedger = CustomerLedger::where('customer_id', $customerId)->latest('id')->first();
                $prevBal = $lastLedger ? (float)$lastLedger->closing_balance : (float)(Customer::find($customerId)->previous_balance ?? 0);
                $newBal = $prevBal + $totalNet - $paidAmount;

                CustomerLedger::create([
                    'customer_id'      => $customerId,
                    'admin_or_user_id' => Auth::id() ?? 1,
                    'description'      => "Sale Invoice #{$invoiceNo} via Truck #{$consignment->truck_no}",
                    'previous_balance' => $prevBal,
                    'closing_balance'  => $newBal,
                    'opening_balance'  => 0,
                ]);

                return $sale;
            });

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Sale #{$sale->invoice_no} successfully delivered directly from Truck to Customer!",
                    'redirect_url' => route('truck-purchase.show', $consignment->id),
                ]);
            }
            return redirect()->route('truck-purchase.show', $consignment->id)
                ->with('success', "Sale #{$sale->invoice_no} successfully delivered directly from Truck to Customer!");

        } catch (\Throwable $e) {
            Log::error("Truck Sale Error: " . $e->getMessage());
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Unload Remaining Truck Stock to Main Warehouse
     */
    public function unloadToWarehouse(Request $request, $id)
    {
        $consignment = TruckConsignment::with(['items.product'])->findOrFail($id);

        if ($consignment->status === 'completed') {
            return back()->with('error', 'This truck consignment has already been completed and unloaded.');
        }

        $warehouseId = (int) ($request->warehouse_id ?: $consignment->warehouse_id ?: 1);

        try {
            DB::transaction(function () use ($consignment, $warehouseId) {
                $movRows = [];

                foreach ($consignment->items as $item) {
                    $remaining = (float) $item->remaining_qty;

                    if ($remaining > 0) {
                        $product = $item->product;
                        $ppb = ($product && $product->pieces_per_box > 0) ? (float) $product->pieces_per_box : 1.0;
                        $u = strtolower($item->unit ?? '');

                        if ($u === 'carton' || $u === 'ctn' || $u === 'box' || ($product && ($product->size_mode === 'by_cartons' || $product->size_mode === 'by_box'))) {
                            [$b, $l] = self::parseCartonQty($remaining);
                            $basePieces = ($b * $ppb) + $l;
                        } else {
                            $basePieces = $remaining;
                        }

                        // Add remaining to target warehouse stock
                        $this->upsertStocks($item->product_id, +$basePieces, $warehouseId);

                        // Stock Movement record
                        $movRows[] = [
                            'product_id' => $item->product_id,
                            'type'       => 'in',
                            'qty'        => $basePieces,
                            'ref_type'   => 'TRUCK_UNLOAD',
                            'ref_id'     => $consignment->id,
                            'note'       => "Remaining stock offloaded from Truck #{$consignment->truck_no} into Warehouse",
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        // Mark item as unloaded
                        $item->unloaded_qty += $remaining;
                        $item->remaining_qty = 0;
                        $item->save();
                    }
                }

                if (!empty($movRows)) {
                    DB::table('stock_movements')->insert($movRows);
                }

                $consignment->update([
                    'status'                => 'completed',
                    'unloaded_at'           => now(),
                    'unloaded_warehouse_id' => $warehouseId,
                ]);
            });

            return redirect()->route('truck-purchase.show', $consignment->id)
                ->with('success', "All remaining stock from Truck #{$consignment->truck_no} has been successfully unloaded into Warehouse. Consignment trip is marked Completed!");

        } catch (\Throwable $e) {
            Log::error("Truck Unload Error: " . $e->getMessage());
            return back()->with('error', "Error unloading stock: " . $e->getMessage());
        }
    }

    /**
     * Printable Truck Inward Gate Pass / Receiving Note
     */
    public function print($id)
    {
        $consignment = TruckConsignment::with([
            'warehouse',
            'unloadedWarehouse',
            'creator',
            'purchases.vendor',
            'items.product',
            'items.vendor',
            'sales.customer_relation',
            'sales.items.product'
        ])->findOrFail($id);

        return view('admin_panel.purchase.truck.print', compact('consignment'));
    }

    /**
     * Delete a consignment (if allowed)
     */
    public function destroy($id)
    {
        $consignment = TruckConsignment::with(['purchases', 'sales'])->findOrFail($id);

        if ($consignment->sales()->exists()) {
            return back()->with('error', "Cannot delete consignment because sales have already been made from this truck. Delete the sales first.");
        }

        foreach ($consignment->purchases as $p) {
            if ($p->returns()->exists()) {
                return back()->with('error', "Cannot delete consignment because Purchase #{$p->invoice_no} has returns.");
            }
        }

        DB::transaction(function () use ($consignment) {
            // If already unloaded, reverse warehouse stocks
            if ($consignment->status === 'completed') {
                foreach ($consignment->items as $item) {
                    if ((float)$item->unloaded_qty > 0) {
                        $this->upsertStocks($item->product_id, -(float)$item->unloaded_qty, $consignment->unloaded_warehouse_id ?: $consignment->warehouse_id);
                    }
                }
                DB::table('stock_movements')->where('ref_type', 'TRUCK_UNLOAD')->where('ref_id', $consignment->id)->delete();
            }

            foreach ($consignment->purchases as $purchase) {
                // Reverse vendor balance
                $prevClosing = VendorLedger::where('vendor_id', $purchase->vendor_id)->value('closing_balance') ?? 0;
                VendorLedger::where('vendor_id', $purchase->vendor_id)->update([
                    'closing_balance' => max(0, $prevClosing - $purchase->net_amount),
                ]);

                $purchase->items()->delete();
                $purchase->delete();
            }

            $consignment->items()->delete();
            $consignment->delete();
        });

        return redirect()->route('truck-purchase.index')->with('success', 'Truck Consignment deleted successfully.');
    }
}
