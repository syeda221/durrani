<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truck Inward Receiving Note - {{ $consignment->consignment_no }}</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 24px;
            color: #1e293b;
            font-size: 13px;
            background: #fff;
        }
        .no-print-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f1f5f9;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            border: 1px solid #cbd5e1;
        }
        .btn-print {
            padding: 8px 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            background: #0284c7;
            color: #fff;
            border: none;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .btn-print:hover {
            background: #0369a1;
        }

        /* Header Document */
        .header-doc {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 14px;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .company-title {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 2px;
        }
        .company-sub {
            font-size: 12px;
            color: #64748b;
        }
        .doc-badge {
            text-align: right;
        }
        .doc-title {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0284c7;
        }
        .doc-ref {
            font-family: monospace;
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2px;
        }

        /* Meta Box Grid */
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 20px;
        }
        .meta-item {
            font-size: 12px;
            margin-bottom: 5px;
        }
        .meta-item strong {
            display: inline-block;
            min-width: 105px;
            color: #475569;
        }
        .meta-item span {
            font-weight: 600;
            color: #0f172a;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 7px 10px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.4px;
        }
        .vendor-header-bar {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            padding: 8px 12px;
            font-size: 12px;
            border-radius: 6px 6px 0 0;
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }
        .section-header-bar {
            background-color: #e2e8f0;
            color: #1e293b;
            font-weight: 700;
            padding: 8px 12px;
            font-size: 12px;
            border-radius: 6px 6px 0 0;
            margin-top: 20px;
            border: 1px solid #cbd5e1;
            border-bottom: none;
        }

        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; }

        /* Signatures */
        .signatures-grid {
            margin-top: 45px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            page-break-inside: avoid;
        }
        .sig-box {
            border-top: 1.5px solid #0f172a;
            text-align: center;
            padding-top: 8px;
            font-weight: 700;
            font-size: 12px;
            color: #334155;
        }

        @media print {
            .no-print-bar { display: none !important; }
            body { padding: 0; }
            th { background-color: #f1f5f9 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .vendor-header-bar { background-color: #0f172a !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .section-header-bar { background-color: #e2e8f0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    {{-- Screen Control Bar --}}
    <div class="no-print-bar">
        <div>
            <strong>Truck Consignment Inward Note</strong> — {{ $consignment->consignment_no }}
        </div>
        <div>
            <button class="btn-print" onclick="window.print()">
                🖨️ Print Receiving Gate Slip
            </button>
        </div>
    </div>

    {{-- Header --}}
    <div class="header-doc">
        <div>
            <div class="company-title">{{ \App\Models\Setting::get('company_name', 'Yasir Pipe Store') }}</div>
            <div class="company-sub">{{ \App\Models\Setting::get('company_address', 'Main Warehouse & Distribution Center') }}</div>
            <div class="company-sub">Phone: {{ \App\Models\Setting::get('company_phone', '0300-xxxxxxx') }}</div>
        </div>
        <div class="doc-badge">
            <div class="doc-title">Truck Inward Gate Pass</div>
            <div class="doc-ref">{{ $consignment->consignment_no }}</div>
            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                Printed: {{ date('d/m/Y h:i A') }}
            </div>
        </div>
    </div>

    {{-- Meta Grid --}}
    <div class="meta-grid">
        <div>
            <div class="meta-item"><strong>Consignment #:</strong> <span class="font-mono">{{ $consignment->consignment_no }}</span></div>
            <div class="meta-item"><strong>Arrival Date:</strong> <span>{{ $consignment->arrival_date ? $consignment->arrival_date->format('d/m/Y') : '—' }}</span></div>
            <div class="meta-item"><strong>Target Store:</strong> <span>{{ $consignment->warehouse->warehouse_name ?? 'Main Warehouse' }}</span></div>
        </div>
        <div>
            <div class="meta-item"><strong>Truck / Vehicle:</strong> <span class="font-mono" style="font-size: 14px;">{{ $consignment->truck_no }}</span></div>
            <div class="meta-item"><strong>Driver Name:</strong> <span>{{ $consignment->driver_name ?: '—' }}</span></div>
            <div class="meta-item"><strong>Driver Mobile:</strong> <span>{{ $consignment->driver_phone ?: '—' }}</span></div>
        </div>
        <div>
            <div class="meta-item"><strong>Transporter:</strong> <span>{{ $consignment->transporter_name ?: 'Direct Cargo' }}</span></div>
            <div class="meta-item"><strong>Bilty / Track #:</strong> <span class="font-mono">{{ $consignment->bilty_no ?: 'None' }}</span></div>
            <div class="meta-item"><strong>Truck Kiraya:</strong> <span>Rs. {{ number_format($consignment->total_freight, 2) }}</span></div>
        </div>
    </div>

    {{-- Vendor Purchases Section --}}
    <div style="font-weight: 800; font-size: 13px; text-transform: uppercase; margin-bottom: 8px; color: #0f172a;">
        📦 Inward Vendor Bills & Loaded Merchandise
    </div>

    @foreach($consignment->purchases as $pIdx => $purchase)
        <div class="vendor-header-bar">
            <span>Vendor #{{ $pIdx + 1 }}: {{ $purchase->vendor->name ?? 'Walk-in Vendor' }}</span>
            <span class="font-mono">Bill No: {{ $purchase->invoice_no }} {{ $purchase->vendor_bill_no ? ' | Ref: '.$purchase->vendor_bill_no : '' }}</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;" class="text-center">#</th>
                    <th style="width: 45%;">Product Description</th>
                    <th style="width: 15%;" class="text-center">Boxes / Loose</th>
                    <th style="width: 15%;" class="text-center">Loaded Qty</th>
                    <th style="width: 20%;" class="text-end">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $iIdx => $item)
                    <tr>
                        <td class="text-center">{{ $iIdx + 1 }}</td>
                        <td><strong>{{ $item->product->item_name ?? 'Product #'.$item->product_id }}</strong></td>
                        <td class="text-center">
                            @if($item->boxes_qty > 0 || $item->loose_qty > 0)
                                {{ (int)$item->boxes_qty }} Box, {{ (int)$item->loose_qty }} Loose
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-center font-mono"><strong>{{ (float)$item->qty }}</strong> {{ $item->unit ?: 'Pcs' }}</td>
                        <td class="text-end font-mono">Rs. {{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #f8fafc; font-weight: bold;">
                    <td colspan="4" class="text-end">Vendor Total Net:</td>
                    <td class="text-end font-mono">Rs. {{ number_format($purchase->net_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @endforeach

    {{-- Route Sales Section --}}
    @if($consignment->sales->isNotEmpty())
        <div class="section-header-bar" style="background-color: #d1fae5; border-color: #a7f3d0;">
            🚚 Route Dispatches & Direct Deliveries Made from this Truck
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Invoice #</th>
                    <th style="width: 25%;">Customer</th>
                    <th style="width: 40%;">Delivered Products</th>
                    <th style="width: 20%;" class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consignment->sales as $sale)
                    <tr>
                        <td class="font-mono"><strong>{{ $sale->invoice_no }}</strong></td>
                        <td>{{ $sale->customer_relation->customer_name ?? $sale->customer_relation->name ?? 'Walk-in' }}</td>
                        <td>
                            @foreach($sale->items as $sItm)
                                <div>• {{ $sItm->product->item_name ?? 'Product' }}: <strong>{{ (float)$sItm->qty }}</strong> {{ $sItm->unit ?: 'Pcs' }}</div>
                            @endforeach
                        </td>
                        <td class="text-end font-mono"><strong>Rs. {{ number_format($sale->total_net, 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Live Stock Inventory Balance Table --}}
    <div class="section-header-bar" style="background-color: #fef3c7; border-color: #fde68a;">
        ⚖️ Truck Inventory Audit & Remaining Balance (ٹرک میں بچا ہوا مال)
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 38%;">Product</th>
                <th style="width: 22%;">Vendor Source</th>
                <th style="width: 13%;" class="text-center">Loaded</th>
                <th style="width: 13%;" class="text-center">Sold on Route</th>
                <th style="width: 14%;" class="text-center">Remaining</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consignment->items as $tItm)
                <tr>
                    <td><strong>{{ $tItm->product->item_name ?? 'Product' }}</strong></td>
                    <td>{{ $tItm->vendor->name ?? 'Vendor' }}</td>
                    <td class="text-center font-mono">{{ (float)$tItm->loaded_qty }} {{ $tItm->unit ?: 'Pcs' }}</td>
                    <td class="text-center font-mono">{{ (float)$tItm->sold_qty }} {{ $tItm->unit ?: 'Pcs' }}</td>
                    <td class="text-center font-mono" style="font-weight: 800; {{ (float)$tItm->remaining_qty > 0 ? 'color: #047857;' : 'color: #94a3b8;' }}">
                        {{ (float)$tItm->remaining_qty }} {{ $tItm->unit ?: 'Pcs' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Grand Totals Strip --}}
    <div style="background: #f1f5f9; padding: 12px 18px; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: bold; display: flex; justify-content: space-between; font-size: 13px;">
        <span>Total Vendors in Truck: {{ $consignment->purchases->count() }}</span>
        <span>Initial Loaded Units: {{ (float)$consignment->items->sum('loaded_qty') }}</span>
        <span>Remaining Balance Units: {{ (float)$consignment->items->sum('remaining_qty') }}</span>
        <span>Total Purchase Amount: Rs. {{ number_format($consignment->purchases->sum('net_amount'), 2) }}</span>
    </div>

    {{-- Signatures --}}
    <div class="signatures-grid">
        <div class="sig-box">
            Driver / Transport Rep
        </div>
        <div class="sig-box">
            Gate / Receiving Incharge
        </div>
        <div class="sig-box">
            Authorized Accountant / Manager
        </div>
    </div>

</body>
</html>
