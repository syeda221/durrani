<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Truck Inward Receiving Note - {{ $consignment->consignment_no }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 13px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .company-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .doc-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }
        .meta-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            background: #f9f9f9;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .meta-col {
            width: 32%;
        }
        .meta-item {
            margin-bottom: 4px;
        }
        .meta-item strong {
            display: inline-block;
            width: 100px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #eee;
            font-size: 12px;
        }
        .vendor-header {
            background-color: #e2e8f0;
            font-weight: bold;
            padding: 6px 8px;
        }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .sig-box {
            width: 28%;
            border-top: 1px solid #000;
            text-align: center;
            padding-top: 5px;
            font-weight: bold;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; font-size: 14px; cursor: pointer; background: #0284c7; color: #fff; border: none; border-radius: 4px;">
            🖨️ Print Receiving Note
        </button>
    </div>

    <div class="header">
        <div class="company-title">{{ \App\Models\Setting::get('company_name', 'Yasir Pipe Store') }}</div>
        <div>{{ \App\Models\Setting::get('company_address', 'Main Warehouse & Distribution Center') }}</div>
        <div class="doc-title">TRUCK INWARD RECEIVING NOTE (ٹرک انورڈ گیٹ سلپ)</div>
    </div>

    <div class="meta-box">
        <div class="meta-col">
            <div class="meta-item"><strong>Consignment #:</strong> {{ $consignment->consignment_no }}</div>
            <div class="meta-item"><strong>Arrival Date:</strong> {{ $consignment->arrival_date ? $consignment->arrival_date->format('d/m/Y') : '—' }}</div>
            <div class="meta-item"><strong>Warehouse:</strong> {{ $consignment->warehouse->warehouse_name ?? 'Main Store' }}</div>
        </div>
        <div class="meta-col">
            <div class="meta-item"><strong>Truck / Vehicle:</strong> {{ $consignment->truck_no }}</div>
            <div class="meta-item"><strong>Driver Name:</strong> {{ $consignment->driver_name ?: '—' }}</div>
            <div class="meta-item"><strong>Driver Mobile:</strong> {{ $consignment->driver_phone ?: '—' }}</div>
        </div>
        <div class="meta-col">
            <div class="meta-item"><strong>Transporter:</strong> {{ $consignment->transporter_name ?: 'Direct' }}</div>
            <div class="meta-item"><strong>Bilty No:</strong> {{ $consignment->bilty_no ?: 'None' }}</div>
            <div class="meta-item"><strong>Truck Freight:</strong> Rs. {{ number_format($consignment->total_freight, 2) }}</div>
        </div>
    </div>

    @foreach($consignment->purchases as $pIdx => $purchase)
        <div class="vendor-header">
            Vendor #{{ $pIdx + 1 }}: {{ $purchase->vendor->name ?? 'Walk-in Vendor' }} 
            (Bill No: {{ $purchase->invoice_no }} {{ $purchase->vendor_bill_no ? '| Ref: '.$purchase->vendor_bill_no : '' }})
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;" class="text-center">#</th>
                    <th style="width: 45%;">Product Description</th>
                    <th style="width: 15%;" class="text-center">Boxes / Loose</th>
                    <th style="width: 15%;" class="text-center">Total Qty</th>
                    <th style="width: 20%;" class="text-end">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $iIdx => $item)
                    <tr>
                        <td class="text-center">{{ $iIdx + 1 }}</td>
                        <td>{{ $item->product->item_name ?? $item->product->name ?? 'Product #'.$item->product_id }}</td>
                        <td class="text-center">
                            @if($item->boxes_qty > 0 || $item->loose_qty > 0)
                                {{ (int)$item->boxes_qty }} Box, {{ (int)$item->loose_qty }} Loose
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-center">{{ (float)$item->qty }} {{ $item->unit ?: 'Pcs' }}</td>
                        <td class="text-end">Rs. {{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end" style="font-weight: bold;">Vendor Total Payable:</td>
                    <td class="text-end" style="font-weight: bold;">Rs. {{ number_format($purchase->net_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @endforeach

    @if($consignment->sales->isNotEmpty())
        <div class="vendor-header" style="margin-top: 20px; background-color: #d1fae5;">
            ROUTE SALES & DIRECT DELIVERIES (گاہکوں کو کی گئی سیل)
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Invoice #</th>
                    <th style="width: 25%;">Customer</th>
                    <th style="width: 40%;">Products Delivered</th>
                    <th style="width: 20%;" class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consignment->sales as $sale)
                    <tr>
                        <td>{{ $sale->invoice_no }}</td>
                        <td>{{ $sale->customer_relation->name ?? 'Walk-in' }}</td>
                        <td>
                            @foreach($sale->items as $sItm)
                                <div>{{ $sItm->product->item_name ?? 'Product' }}: {{ (float)$sItm->qty }} {{ $sItm->unit ?: 'Pcs' }}</div>
                            @endforeach
                        </td>
                        <td class="text-end">Rs. {{ number_format($sale->total_net, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="vendor-header" style="margin-top: 20px; background-color: #fef3c7;">
        TRUCK INVENTORY SUMMARY & BALANCE (ٹرک میں موجود / بچا ہوا مال)
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 40%;">Product</th>
                <th style="width: 20%;">Vendor Source</th>
                <th style="width: 13%;" class="text-center">Loaded</th>
                <th style="width: 13%;" class="text-center">Sold</th>
                <th style="width: 14%;" class="text-center">Remaining Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consignment->items as $tItm)
                <tr>
                    <td>{{ $tItm->product->item_name ?? 'Product' }}</td>
                    <td>{{ $tItm->vendor->name ?? 'Vendor' }}</td>
                    <td class="text-center">{{ (float)$tItm->loaded_qty }} {{ $tItm->unit ?: 'Pcs' }}</td>
                    <td class="text-center">{{ (float)$tItm->sold_qty }} {{ $tItm->unit ?: 'Pcs' }}</td>
                    <td class="text-center" style="font-weight: bold; {{ (float)$tItm->remaining_qty > 0 ? 'color: #047857;' : 'color: #64748b;' }}">
                        {{ (float)$tItm->remaining_qty }} {{ $tItm->unit ?: 'Pcs' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 15px; background: #eee; padding: 10px; font-weight: bold; display: flex; justify-content: space-between;">
        <span>Total Vendors in Truck: {{ $consignment->purchases->count() }}</span>
        <span>Grand Total Purchase Amount: Rs. {{ number_format($consignment->purchases->sum('net_amount'), 2) }}</span>
    </div>

    <div class="signatures">
        <div class="sig-box">Driver Signature</div>
        <div class="sig-box">Warehouse Incharge</div>
        <div class="sig-box">Authorized Accountant</div>
    </div>
</body>
</html>
