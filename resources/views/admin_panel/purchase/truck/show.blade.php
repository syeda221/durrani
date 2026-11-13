@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .consignment-header-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }
    .info-tile {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 8px;
        padding: 12px 16px;
    }
    .section-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 24px;
        background: #ffffff;
        overflow: hidden;
    }
    .section-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 20px;
    }
    .nav-tabs-custom .nav-link {
        font-weight: 600;
        color: #64748b;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 10px 18px;
    }
    .nav-tabs-custom .nav-link.active {
        color: #0284c7;
        border-bottom-color: #0284c7;
        background: transparent;
    }
</style>

<div class="container-fluid px-3 py-3">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- TOP CONSIGNMENT HEADER CARD --}}
    <div class="consignment-header-card shadow">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary px-3 py-1 font-monospace fs-6">
                        {{ $consignment->consignment_no }}
                    </span>
                    @if($consignment->status === 'in_transit')
                        <span class="badge bg-warning text-dark px-3 py-1 fw-bold fs-6">
                            <i class="fas fa-truck-moving me-1"></i> In Transit (On Route)
                        </span>
                    @else
                        <span class="badge bg-success px-3 py-1 fw-bold fs-6">
                            <i class="fas fa-check-double me-1"></i> Completed & Unloaded
                        </span>
                    @endif
                </div>

                <h3 class="fw-bold mb-1 text-white">
                    <i class="fas fa-truck text-info me-2"></i> Truck #{{ $consignment->truck_no }} Consignment
                </h3>
                <p class="text-slate-300 text-light opacity-75 mb-0 small">
                    Loaded on {{ $consignment->arrival_date ? $consignment->arrival_date->format('d M, Y') : '—' }}
                    @if($consignment->status === 'completed' && $consignment->unloaded_at)
                        | Unloaded to {{ $consignment->unloadedWarehouse->warehouse_name ?? 'Warehouse' }} on {{ \Carbon\Carbon::parse($consignment->unloaded_at)->format('d/m/Y') }}
                    @endif
                </p>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('truck-purchase.index') }}" class="btn btn-outline-light btn-sm px-3">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>

                @if($consignment->status === 'in_transit')
                    {{-- SALE FROM TRUCK BUTTON --}}
                    <a href="{{ route('truck-purchase.sale.create', $consignment->id) }}" class="btn btn-success btn-sm px-3 fw-bold shadow">
                        <i class="fas fa-cart-plus me-1"></i> + Sale / Deliver from this Truck
                    </a>

                    {{-- UNLOAD TO WAREHOUSE BUTTON --}}
                    <button type="button" class="btn btn-warning btn-sm px-3 fw-bold text-dark shadow" data-bs-toggle="modal" data-bs-target="#unloadModal">
                        <i class="fas fa-warehouse me-1"></i> 📥 Unload Remaining to Warehouse
                    </button>
                @endif

                <a href="{{ route('truck-purchase.print', $consignment->id) }}" target="_blank" class="btn btn-info btn-sm px-3 fw-bold">
                    <i class="fas fa-print me-1"></i> Print Summary
                </a>
            </div>
        </div>

        {{-- METRICS TILES --}}
        <div class="row g-3">
            <div class="col-md-3">
                <div class="info-tile">
                    <div class="text-light opacity-75 small text-uppercase fw-semibold">Truck & Transporter</div>
                    <div class="fs-4 fw-bold text-white mt-1">{{ $consignment->truck_no }}</div>
                    <div class="small text-info">{{ $consignment->transporter_name ?: 'Direct Cargo' }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-tile">
                    <div class="text-light opacity-75 small text-uppercase fw-semibold">Driver Details</div>
                    <div class="fs-5 fw-bold text-white mt-1">{{ $consignment->driver_name ?: '—' }}</div>
                    <div class="small text-light opacity-75"><i class="fas fa-phone me-1"></i>{{ $consignment->driver_phone ?: 'No phone' }}</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="info-tile">
                    <div class="text-light opacity-75 small text-uppercase fw-semibold">Bilty / Tracking #</div>
                    <div class="fs-5 fw-bold text-white mt-1 font-monospace">{{ $consignment->bilty_no ?: 'None' }}</div>
                    <div class="small text-light opacity-75">Freight: Rs. {{ number_format($consignment->total_freight, 2) }}</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="info-tile">
                    <div class="text-light opacity-75 small text-uppercase fw-semibold">Initial Loaded Stock</div>
                    <div class="fs-5 fw-bold text-primary font-monospace mt-1 text-info">
                        {{ (float)$consignment->items->sum('loaded_qty') }} Units
                    </div>
                    <div class="small text-light opacity-75">{{ $consignment->purchases->count() }} Vendors</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="info-tile">
                    <div class="text-light opacity-75 small text-uppercase fw-semibold">Remaining in Truck</div>
                    @php
                        $remainingTotal = (float)$consignment->items->sum('remaining_qty');
                    @endphp
                    <div class="fs-4 fw-bold font-monospace mt-1 {{ $remainingTotal > 0 ? 'text-warning' : 'text-success' }}">
                        {{ $remainingTotal }} Units
                    </div>
                    <div class="small text-light opacity-75">{{ $consignment->sales->count() }} Direct Deliveries</div>
                </div>
            </div>
        </div>

        @if($consignment->remarks)
            <div class="mt-3 p-2 rounded bg-white bg-opacity-10 text-light small">
                <i class="fas fa-info-circle me-1 text-info"></i> <strong>Remarks:</strong> {{ $consignment->remarks }}
            </div>
        @endif
    </div>

    {{-- NAVIGATION TABS --}}
    <ul class="nav nav-tabs nav-tabs-custom mb-3" id="truckTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="balance-tab" data-bs-toggle="tab" href="#balancePane" role="tab">
                <i class="fas fa-scale-balanced me-1"></i> Live Truck Stock Balance (ٹرک میں موجود مال)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="sales-tab" data-bs-toggle="tab" href="#salesPane" role="tab">
                <i class="fas fa-shipping-fast me-1"></i> Route Sales & Deliveries ({{ $consignment->sales->count() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="purchases-tab" data-bs-toggle="tab" href="#purchasesPane" role="tab">
                <i class="fas fa-file-invoice me-1"></i> Initial Vendor Purchases ({{ $consignment->purchases->count() }})
            </a>
        </li>
    </ul>

    <div class="tab-content" id="truckTabsContent">

        {{-- TAB 1: LIVE TRUCK STOCK BALANCE SHEET --}}
        <div class="tab-pane fade show active" id="balancePane" role="tabpanel">
            <div class="section-card shadow-sm">
                <div class="section-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-clipboard-check text-primary me-2"></i>Live Truck Stock Breakdown (Vendor-wise)
                        </h6>
                        <small class="text-muted">Dekhein ke kis vendor ka kitna maal load hua tha, kitna sale ho gaya, aur truck me kitna bacha hai.</small>
                    </div>
                    @if($consignment->status === 'in_transit' && $remainingTotal > 0)
                        <a href="{{ route('truck-purchase.sale.create', $consignment->id) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-plus me-1"></i> Sale from this Truck
                        </a>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th style="width: 30%;">Product / Item Description</th>
                                <th style="width: 15%;">Vendor Source</th>
                                <th style="width: 10%;" class="text-center">Unit</th>
                                <th style="width: 10%;" class="text-center bg-primary-subtle text-primary fw-bold">Loaded</th>
                                <th style="width: 10%;" class="text-center bg-danger-subtle text-danger fw-bold">Sold</th>
                                <th style="width: 10%;" class="text-center bg-secondary-subtle fw-bold">Unloaded</th>
                                <th style="width: 10%;" class="text-center bg-success-subtle text-success fw-bold">Remaining in Truck</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($consignment->items as $idx => $item)
                                <tr>
                                    <td class="text-center text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->product->item_name ?? 'Product #'.$item->product_id }}</div>
                                        @if(!empty($item->product->item_code))
                                            <span class="text-muted small font-monospace">Code: {{ $item->product->item_code }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-tag me-1 text-primary"></i> {{ $item->vendor->name ?? 'Vendor #'.$item->vendor_id }}
                                        </span>
                                    </td>
                                    <td class="text-center text-muted">{{ $item->unit ?: 'Pcs' }}</td>
                                    <td class="text-center fw-bold font-monospace fs-6">
                                        {{ (float)$item->loaded_qty }}
                                    </td>
                                    <td class="text-center font-monospace text-danger fw-semibold">
                                        {{ (float)$item->sold_qty }}
                                    </td>
                                    <td class="text-center font-monospace text-secondary">
                                        {{ (float)$item->unloaded_qty }}
                                    </td>
                                    <td class="text-center font-monospace">
                                        @if((float)$item->remaining_qty > 0)
                                            <span class="badge bg-success fs-6 px-3 py-1">
                                                {{ (float)$item->remaining_qty }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary px-2 py-1">
                                                0 (Finished)
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No items recorded in this truck consignment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">Total Units:</td>
                                <td class="text-center font-monospace text-primary fs-6">{{ (float)$consignment->items->sum('loaded_qty') }}</td>
                                <td class="text-center font-monospace text-danger fs-6">{{ (float)$consignment->items->sum('sold_qty') }}</td>
                                <td class="text-center font-monospace text-secondary fs-6">{{ (float)$consignment->items->sum('unloaded_qty') }}</td>
                                <td class="text-center font-monospace text-success fs-5">{{ $remainingTotal }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 2: ROUTE SALES / DIRECT DELIVERIES --}}
        <div class="tab-pane fade" id="salesPane" role="tabpanel">
            <div class="section-card shadow-sm">
                <div class="section-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-truck-ramp-box text-success me-2"></i>Sales & Dispatches Made Directly From this Truck
                        </h6>
                        <small class="text-muted">Tamam customers jinko raste mein is truck se maal deliver kiya gaya.</small>
                    </div>
                    @if($consignment->status === 'in_transit' && $remainingTotal > 0)
                        <a href="{{ route('truck-purchase.sale.create', $consignment->id) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-plus me-1"></i> New Direct Sale
                        </a>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer Name</th>
                                <th>Delivery Date</th>
                                <th>Products Delivered</th>
                                <th class="text-end">Bill Amount</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($consignment->sales as $sale)
                                <tr>
                                    <td>
                                        <span class="fw-bold font-monospace text-primary">{{ $sale->invoice_no }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $sale->customer_relation->customer_name ?? $sale->customer_relation->name ?? 'Walk-in Customer' }}</div>
                                        <small class="text-muted">{{ $sale->customer_relation->mobile ?? $sale->customer_relation->phone ?? '' }}</small>
                                    </td>
                                    <td>
                                        {{ $sale->created_at ? \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td>
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($sale->items as $sItem)
                                                <li>
                                                    <i class="fas fa-check text-success me-1"></i>
                                                    {{ $sItem->product->item_name ?? 'Product' }}: 
                                                    <strong>{{ (float)$sItem->qty }}</strong> {{ $sItem->unit ?: 'Pcs' }}
                                                    @if($sItem->vendor_id)
                                                        <span class="text-muted">({{ \App\Models\Vendor::find($sItem->vendor_id)->name ?? 'Vendor' }})</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="text-end fw-bold font-monospace text-success fs-6">
                                        Rs. {{ number_format($sale->total_net, 2) }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ url('sale/'.$sale->id) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="View Sale Invoice">
                                            <i class="fas fa-print me-1"></i> Print Bill
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-truck-loading fs-2 text-secondary opacity-50 mb-2 d-block"></i>
                                        Abhi tak is truck se koi direct customer sale nahi hui.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 3: VENDOR PURCHASES --}}
        <div class="tab-pane fade" id="purchasesPane" role="tabpanel">
            @foreach($consignment->purchases as $pIndex => $p)
                <div class="section-card shadow-sm mb-3">
                    <div class="section-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">
                                Vendor #{{ $pIndex + 1 }}: {{ $p->vendor->name ?? 'Vendor' }}
                            </span>
                            <span class="text-muted small">
                                Bill No: <strong class="text-dark font-monospace">{{ $p->invoice_no }}</strong>
                                @if($p->vendor_bill_no) (Ref: {{ $p->vendor_bill_no }}) @endif
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('purchase.invoice', $p->id) }}" target="_blank" class="btn btn-outline-primary btn-sm px-3">
                                <i class="fas fa-print me-1"></i> Print Vendor Bill
                            </a>
                        </div>
                    </div>

                    <div class="p-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle mb-2">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 5%;" class="text-center">#</th>
                                        <th style="width: 45%;">Product Description</th>
                                        <th style="width: 15%;" class="text-center">Unit</th>
                                        <th style="width: 15%;" class="text-center">Loaded Qty</th>
                                        <th style="width: 20%;" class="text-end">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($p->items as $iIndex => $item)
                                        <tr>
                                            <td class="text-center text-muted">{{ $iIndex + 1 }}</td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $item->product->item_name ?? 'Product #'.$item->product_id }}</div>
                                            </td>
                                            <td class="text-center text-muted">{{ $item->unit ?: 'Pcs' }}</td>
                                            <td class="text-center fw-bold font-monospace">{{ (float)$item->qty }}</td>
                                            <td class="text-end fw-bold font-monospace">Rs. {{ number_format($item->line_total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr class="table-success">
                                        <td colspan="4" class="text-end fw-bold">Vendor Net Payable:</td>
                                        <td class="text-end fw-bold text-success font-monospace fs-6">Rs. {{ number_format($p->net_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>

{{-- MODAL: UNLOAD REMAINING STOCK TO WAREHOUSE --}}
<div class="modal fade" id="unloadModal" tabindex="-1" aria-labelledby="unloadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('truck-purchase.unload', $consignment->id) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title fw-bold text-dark" id="unloadModalLabel">
                        <i class="fas fa-warehouse me-2"></i> Unload Remaining Stock to Warehouse
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i>
                        Truck mein is waqt kul <strong>{{ $remainingTotal }} Units</strong> bacha hua maal mojood hai. Yeh sara bacha hua maal select kiye gaye warehouse ke physical stock mein add ho jaye ga aur truck consignment **Completed** mark ho jaye gi.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Select Destination Warehouse (کس گودام میں اتارنا ہے؟)</label>
                        <select class="form-select" name="warehouse_id" required>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ $wh->id == $consignment->warehouse_id ? 'selected' : '' }}>
                                    {{ $wh->is_shop ? '🏪 ' : '🏢 ' }}{{ $wh->warehouse_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <p class="text-muted small mb-0">
                        * Unload karne ke baad is truck se mazeed direct sales nahi ki ja sakengi kyunke gari ka safar mukammal ho chuka hoga.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark">
                        <i class="fas fa-check-circle me-1"></i> Confirm Unload & Complete Trip
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/vendors/bootstrap5/js/bootstrap.bundle.min.js') }}"></script>
@endsection
