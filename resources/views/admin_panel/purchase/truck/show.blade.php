@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Premium ERP Styling */
    .truck-show-wrapper {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1e293b;
    }

    /* Top Hero Header Card */
    .hero-header-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        border-radius: 14px;
        padding: 24px 28px;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.15);
        position: relative;
        overflow: hidden;
    }
    .hero-header-card::after {
        content: '\f0d1';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: 25px;
        bottom: -25px;
        font-size: 8.5rem;
        opacity: 0.05;
        color: #ffffff;
        pointer-events: none;
    }

    /* Metric Tiles inside Hero */
    .info-tile-pro {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 10px;
        padding: 14px 18px;
        transition: all 0.2s;
    }
    .info-tile-pro:hover {
        background: rgba(255, 255, 255, 0.12);
    }

    /* Modern Tabs */
    .nav-tabs-erp {
        border-bottom: 2px solid #e2e8f0;
        gap: 8px;
    }
    .nav-tabs-erp .nav-link {
        font-weight: 600;
        font-size: 0.9rem;
        color: #64748b;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 12px 20px;
        border-radius: 0;
        transition: all 0.2s;
    }
    .nav-tabs-erp .nav-link:hover {
        color: #1e293b;
    }
    .nav-tabs-erp .nav-link.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
        background: transparent;
        font-weight: 700;
    }

    /* Section Cards */
    .erp-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .erp-card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 20px;
    }

    /* Tables */
    .table-detail th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #cbd5e1;
        padding: 12px 14px;
    }
    .table-detail td {
        padding: 12px 14px;
        vertical-align: middle;
        font-size: 0.88rem;
    }

    /* Pulse dot */
    .pulse-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #f59e0b;
        box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
        animation: pulse-orange 1.8s infinite;
    }
    @keyframes pulse-orange {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(245, 158, 11, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }
</style>

<div class="truck-show-wrapper container-fluid px-3 py-3">

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 4px solid #10b981 !important;">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle fs-5 text-success"></i>
                <div class="fw-medium text-dark">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 4px solid #ef4444 !important;">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-exclamation-circle fs-5 text-danger"></i>
                <div class="fw-medium text-dark">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $totalLoaded = (float) $consignment->items->sum('loaded_qty');
        $totalSold = (float) $consignment->items->sum('sold_qty');
        $totalUnloaded = (float) $consignment->items->sum('unloaded_qty');
        $remainingTotal = (float) $consignment->items->sum('remaining_qty');
        $percentDispatched = $totalLoaded > 0 ? min(100, round((($totalSold + $totalUnloaded) / $totalLoaded) * 100)) : 100;
    @endphp

    {{-- HERO HEADER CARD --}}
    <div class="hero-header-card shadow">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary px-3 py-1 font-monospace fs-6">
                        {{ $consignment->consignment_no }}
                    </span>
                    @if($consignment->status === 'in_transit')
                        <span class="badge bg-warning bg-opacity-25 text-warning border border-warning px-3 py-1 fw-bold fs-6 d-inline-flex align-items-center gap-2">
                            <span class="pulse-dot"></span> In Transit (راستے میں)
                        </span>
                    @else
                        <span class="badge bg-success bg-opacity-25 text-success border border-success px-3 py-1 fw-bold fs-6 d-inline-flex align-items-center gap-2">
                            <i class="fas fa-check-double"></i> Completed & Unloaded (مکمل)
                        </span>
                    @endif
                </div>

                <h3 class="fw-bold mb-1 text-white d-flex align-items-center gap-2">
                    <i class="fas fa-truck text-info"></i> Truck #{{ $consignment->truck_no }} Consignment
                </h3>
                <p class="text-light opacity-75 mb-0 small">
                    Arrival / Loaded on: <strong>{{ $consignment->arrival_date ? $consignment->arrival_date->format('d M, Y') : '—' }}</strong>
                    @if($consignment->status === 'completed' && $consignment->unloaded_at)
                        | Offloaded to <strong>{{ $consignment->unloadedWarehouse->warehouse_name ?? 'Warehouse' }}</strong> on {{ \Carbon\Carbon::parse($consignment->unloaded_at)->format('d M, Y') }}
                    @endif
                </p>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('truck-purchase.index') }}" class="btn btn-outline-light btn-sm px-3 fw-semibold">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>

                @if($consignment->status === 'in_transit')
                    {{-- DIRECT SALE BUTTON --}}
                    @if($remainingTotal > 0)
                        <a href="{{ route('truck-purchase.sale.create', $consignment->id) }}" class="btn btn-success btn-sm px-3 fw-bold shadow">
                            <i class="fas fa-cart-plus me-1"></i> + Direct Sale from Truck
                        </a>
                    @endif

                    {{-- UNLOAD TO WAREHOUSE BUTTON --}}
                    <button type="button" class="btn btn-warning btn-sm px-3 fw-bold text-dark shadow" data-bs-toggle="modal" data-bs-target="#unloadModal">
                        <i class="fas fa-warehouse me-1"></i> 📥 Unload Remaining to Warehouse
                    </button>
                @endif

                <a href="{{ route('truck-purchase.print', $consignment->id) }}" target="_blank" class="btn btn-info btn-sm px-3 fw-bold text-white shadow-sm">
                    <i class="fas fa-print me-1"></i> Print Inward Slip
                </a>
            </div>
        </div>

        {{-- METRICS TILES GRID --}}
        <div class="row g-3">
            {{-- Vehicle & Transporter --}}
            <div class="col-xl-3 col-md-6">
                <div class="info-tile-pro">
                    <div class="text-light opacity-75 small text-uppercase fw-semibold" style="font-size: 0.72rem;">Vehicle & Transporter</div>
                    <div class="fs-4 fw-bold text-white mt-1 font-monospace">{{ $consignment->truck_no }}</div>
                    <div class="small text-info text-truncate">{{ $consignment->transporter_name ?: 'Direct Cargo / Private' }}</div>
                </div>
            </div>

            {{-- Driver Info --}}
            <div class="col-xl-3 col-md-6">
                <div class="info-tile-pro">
                    <div class="text-light opacity-75 small text-uppercase fw-semibold" style="font-size: 0.72rem;">Driver Details</div>
                    <div class="fs-5 fw-bold text-white mt-1">{{ $consignment->driver_name ?: '—' }}</div>
                    <div class="small text-light opacity-75">
                        @if($consignment->driver_phone)
                            <a href="tel:{{ $consignment->driver_phone }}" class="text-light text-decoration-none">
                                <i class="fas fa-phone-alt text-success me-1"></i>{{ $consignment->driver_phone }}
                            </a>
                        @else
                            No phone recorded
                        @endif
                    </div>
                </div>
            </div>

            {{-- Bilty & Freight --}}
            <div class="col-xl-2 col-md-4">
                <div class="info-tile-pro">
                    <div class="text-light opacity-75 small text-uppercase fw-semibold" style="font-size: 0.72rem;">Bilty & Kiraya</div>
                    <div class="fs-5 fw-bold text-white mt-1 font-monospace">{{ $consignment->bilty_no ?: 'None' }}</div>
                    <div class="small text-warning">Kiraya: Rs. {{ number_format($consignment->total_freight, 0) }}</div>
                </div>
            </div>

            {{-- Loaded Stock --}}
            <div class="col-xl-2 col-md-4">
                <div class="info-tile-pro">
                    <div class="text-light opacity-75 small text-uppercase fw-semibold" style="font-size: 0.72rem;">Loaded Stock</div>
                    <div class="fs-5 fw-bold text-info font-monospace mt-1">
                        {{ number_format($totalLoaded, 1) }} Units
                    </div>
                    <div class="small text-light opacity-75">{{ $consignment->purchases->count() }} Vendors Loaded</div>
                </div>
            </div>

            {{-- Remaining in Truck --}}
            <div class="col-xl-2 col-md-4">
                <div class="info-tile-pro" style="border-left: 3px solid {{ $remainingTotal > 0 ? '#f59e0b' : '#10b981' }};">
                    <div class="text-light opacity-75 small text-uppercase fw-semibold" style="font-size: 0.72rem;">Remaining in Truck</div>
                    <div class="fs-4 fw-bold font-monospace mt-1 {{ $remainingTotal > 0 ? 'text-warning' : 'text-success' }}">
                        {{ number_format($remainingTotal, 1) }}
                    </div>
                    <div class="small text-light opacity-75">{{ $consignment->sales->count() }} Route Deliveries</div>
                </div>
            </div>
        </div>

        @if($consignment->remarks)
            <div class="mt-3 p-2 px-3 rounded bg-white bg-opacity-10 text-light small d-flex align-items-center gap-2">
                <i class="fas fa-info-circle text-info"></i>
                <div><strong>Consignment Remarks:</strong> {{ $consignment->remarks }}</div>
            </div>
        @endif
    </div>

    @php
        $truckCost = $consignment->total_purchase_cost;
        $truckSales = $consignment->total_sales_revenue;
        $truckSoldCost = $consignment->sold_goods_cost;
        $truckProfit = $consignment->realized_profit;
        $truckMargin = $consignment->profit_margin_percent;
        $truckRemainingValue = $consignment->remaining_stock_value;
        $truckUnloadedValue = $consignment->unloaded_stock_value;
    @endphp

    {{-- TRUCK PROFITABILITY & FINANCIAL OVERVIEW CARDS --}}
    <div class="row g-3 mb-4">
        {{-- Total Loaded Cost --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100" style="border-left: 4px solid #64748b !important;">
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Total Loaded Cost (خرید لاگت)</div>
                <div class="fs-4 fw-bold font-monospace text-dark mt-1">Rs. {{ number_format($truckCost, 2) }}</div>
                <div class="text-muted small mt-1">
                    Kiraya (Freight): <span class="fw-semibold text-dark">Rs. {{ number_format($consignment->total_freight, 0) }}</span>
                </div>
            </div>
        </div>

        {{-- Direct Route Sales --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100" style="border-left: 4px solid #0284c7 !important;">
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Direct Route Sales (فروخت آمدن)</div>
                <div class="fs-4 fw-bold font-monospace text-primary mt-1">Rs. {{ number_format($truckSales, 2) }}</div>
                <div class="text-muted small mt-1">
                    {{ $consignment->sales->count() }} Invoices Delivered on Route
                </div>
            </div>
        </div>

        {{-- Realized Direct Profit --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100" style="border-left: 4px solid {{ $truckProfit >= 0 ? '#10b981' : '#ef4444' }} !important;">
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Route Sales Profit (خالص منافع)</div>
                <div class="fs-4 fw-bold font-monospace {{ $truckProfit >= 0 ? 'text-success' : 'text-danger' }} mt-1">
                    {{ $truckProfit >= 0 ? '+' : '' }} Rs. {{ number_format($truckProfit, 2) }}
                </div>
                <div class="text-muted small mt-1 d-flex align-items-center gap-2">
                    <span class="badge {{ $truckProfit >= 0 ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger' }} fw-bold">
                        Margin: {{ $truckMargin }}%
                    </span>
                    <span class="text-muted" style="font-size: 0.73rem;">Sold Cost: Rs. {{ number_format($truckSoldCost, 0) }}</span>
                </div>
            </div>
        </div>

        {{-- Remaining / Unloaded Stock Asset --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100" style="border-left: 4px solid #f59e0b !important;">
                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Stock Asset Value (باقی مال کی مالیت)</div>
                <div class="fs-4 fw-bold font-monospace text-warning mt-1">
                    Rs. {{ number_format($truckRemainingValue + $truckUnloadedValue, 2) }}
                </div>
                <div class="text-muted small mt-1" style="font-size: 0.73rem;">
                    <span>Truck: <strong>Rs. {{ number_format($truckRemainingValue, 0) }}</strong></span> | 
                    <span>Unloaded: <strong>Rs. {{ number_format($truckUnloadedValue, 0) }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    {{-- NAVIGATION TABS --}}
    <ul class="nav nav-tabs nav-tabs-erp mb-3" id="truckTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="balance-tab" data-bs-toggle="tab" href="#balancePane" role="tab">
                <i class="fas fa-scale-balanced me-1"></i> Live Truck Stock Balance (ٹرک میں موجود مال)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="profit-tab" data-bs-toggle="tab" href="#profitPane" role="tab">
                <i class="fas fa-chart-pie text-success me-1"></i> 💰 Truck Profit & Loss (نفع و نقصان کی رپورٹ)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="sales-tab" data-bs-toggle="tab" href="#salesPane" role="tab">
                <i class="fas fa-shipping-fast me-1"></i> Route Sales & Direct Deliveries ({{ $consignment->sales->count() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="purchases-tab" data-bs-toggle="tab" href="#purchasesPane" role="tab">
                <i class="fas fa-file-invoice me-1"></i> Vendor Purchase Bills ({{ $consignment->purchases->count() }})
            </a>
        </li>
    </ul>

    <div class="tab-content" id="truckTabsContent">

        {{-- TAB 1: LIVE TRUCK STOCK BALANCE SHEET --}}
        <div class="tab-pane fade show active" id="balancePane" role="tabpanel">
            <div class="erp-card shadow-sm">
                <div class="erp-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="fas fa-clipboard-check text-primary"></i>
                            Live Truck Stock Breakdown (Vendor-wise)
                        </h6>
                        <small class="text-muted">Detailed audit of what was loaded, what has been sold on route, and what remains on the truck.</small>
                    </div>
                    @if($consignment->status === 'in_transit' && $remainingTotal > 0)
                        <a href="{{ route('truck-purchase.sale.create', $consignment->id) }}" class="btn btn-sm btn-success fw-semibold">
                            <i class="fas fa-cart-plus me-1"></i> Deliver / Sell from Truck
                        </a>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-detail align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th style="width: 28%;">Product / Item Description</th>
                                <th style="width: 15%;">Vendor Source</th>
                                <th style="width: 8%;" class="text-center">Unit</th>
                                <th style="width: 11%;" class="text-center bg-primary-subtle text-primary fw-bold">Loaded</th>
                                <th style="width: 11%;" class="text-center bg-danger-subtle text-danger fw-bold">Sold on Route</th>
                                <th style="width: 11%;" class="text-center bg-secondary-subtle text-secondary fw-bold">Offloaded</th>
                                <th style="width: 11%;" class="text-center bg-success-subtle text-success fw-bold">Remaining in Truck</th>
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
                                            <span class="badge bg-secondary px-2 py-1 text-white">
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

        {{-- TAB: TRUCK PROFIT & LOSS STATEMENT --}}
        <div class="tab-pane fade" id="profitPane" role="tabpanel">
            <div class="erp-card shadow-sm mb-4">
                <div class="erp-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="fas fa-chart-line text-success"></i>
                            Truck Profit & Loss Breakdown (ٹرک کا نفع و نقصان)
                        </h6>
                        <small class="text-muted">Item-by-item analysis: Purchase Cost, Sold Revenue, Realized Profit, and Remaining Stock Asset.</small>
                    </div>
                    <span class="badge {{ $truckProfit >= 0 ? 'bg-success' : 'bg-danger' }} fs-6 px-3 py-2 font-monospace">
                        Net Route Profit: {{ $truckProfit >= 0 ? '+' : '' }} Rs. {{ number_format($truckProfit, 2) }}
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-detail align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 4%;" class="text-center">#</th>
                                <th style="width: 22%;">Product Description</th>
                                <th style="width: 12%;">Vendor</th>
                                <th style="width: 10%;" class="text-end">Pur. Cost</th>
                                <th style="width: 10%;" class="text-center">Loaded / Sold</th>
                                <th style="width: 11%;" class="text-end">Sale Revenue</th>
                                <th style="width: 10%;" class="text-end">COGS (خرید)</th>
                                <th style="width: 11%;" class="text-end bg-success-subtle text-success fw-bold">Profit (منافع)</th>
                                <th style="width: 10%;" class="text-end">Stock Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sumLoadedCost = 0;
                                $sumSoldRevenue = 0;
                                $sumSoldCost = 0;
                                $sumProfit = 0;
                                $sumStockAsset = 0;
                            @endphp
                            @forelse($consignment->items as $idx => $tItem)
                                @php
                                    $cost = (float) $tItem->purchase_price;
                                    $loadedQty = (float) $tItem->loaded_qty;
                                    $soldQty = (float) $tItem->sold_qty;
                                    $remainingQty = (float) $tItem->remaining_qty;
                                    $unloadedQty = (float) $tItem->unloaded_qty;

                                    // Direct revenue from sales matching this truck item
                                    $itemRevenue = (float) \App\Models\SaleItem::where('truck_consignment_item_id', $tItem->id)->sum('total');
                                    $itemSoldCost = $soldQty * $cost;
                                    $itemProfit = $itemRevenue - $itemSoldCost;
                                    $itemMargin = $itemRevenue > 0 ? round(($itemProfit / $itemRevenue) * 100, 1) : 0;
                                    $itemStockValue = ($remainingQty + $unloadedQty) * $cost;

                                    $sumLoadedCost += ($loadedQty * $cost);
                                    $sumSoldRevenue += $itemRevenue;
                                    $sumSoldCost += $itemSoldCost;
                                    $sumProfit += $itemProfit;
                                    $sumStockAsset += $itemStockValue;
                                @endphp
                                <tr>
                                    <td class="text-center text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $tItem->product->item_name ?? 'Product #'.$tItem->product_id }}</div>
                                        @if(!empty($tItem->product->item_code))
                                            <span class="text-muted small font-monospace">Code: {{ $tItem->product->item_code }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $tItem->vendor->name ?? 'Vendor' }}
                                        </span>
                                    </td>
                                    <td class="text-end font-monospace">
                                        Rs. {{ number_format($cost, 2) }}
                                    </td>
                                    <td class="text-center">
                                        <div class="font-monospace fw-semibold">{{ $soldQty }} / {{ $loadedQty }}</div>
                                        <small class="text-muted" style="font-size: 0.72rem;">{{ $tItem->unit ?: 'Pcs' }}</small>
                                    </td>
                                    <td class="text-end font-monospace fw-bold text-primary">
                                        Rs. {{ number_format($itemRevenue, 2) }}
                                    </td>
                                    <td class="text-end font-monospace text-muted">
                                        Rs. {{ number_format($itemSoldCost, 2) }}
                                    </td>
                                    <td class="text-end font-monospace fw-bold {{ $itemProfit >= 0 ? 'text-success bg-success-subtle' : 'text-danger bg-danger-subtle' }}">
                                        <div>{{ $itemProfit >= 0 ? '+' : '' }} Rs. {{ number_format($itemProfit, 2) }}</div>
                                        @if($itemRevenue > 0)
                                            <div style="font-size: 0.72rem;">({{ $itemMargin }}%)</div>
                                        @endif
                                    </td>
                                    <td class="text-end font-monospace text-warning fw-semibold">
                                        Rs. {{ number_format($itemStockValue, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">No items available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light fw-bold">
                            <tr>
                                <td colspan="5" class="text-end">Consignment Totals:</td>
                                <td class="text-end font-monospace text-primary fs-6">Rs. {{ number_format($sumSoldRevenue, 2) }}</td>
                                <td class="text-end font-monospace text-muted fs-6">Rs. {{ number_format($sumSoldCost, 2) }}</td>
                                <td class="text-end font-monospace {{ $sumProfit >= 0 ? 'text-success bg-success-subtle' : 'text-danger bg-danger-subtle' }} fs-5">
                                    {{ $sumProfit >= 0 ? '+' : '' }} Rs. {{ number_format($sumProfit, 2) }}
                                </td>
                                <td class="text-end font-monospace text-warning fs-6">Rs. {{ number_format($sumStockAsset, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- FINANCIAL HIGHLIGHT CARD --}}
                <div class="p-3 bg-light border-top">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <span class="text-muted small">1. Total Purchases Cost:</span>
                            <div class="fw-bold font-monospace text-dark fs-6">Rs. {{ number_format($truckCost, 2) }}</div>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted small">2. Total Direct Sales:</span>
                            <div class="fw-bold font-monospace text-primary fs-6">Rs. {{ number_format($truckSales, 2) }}</div>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted small">3. Realized Cash Profit:</span>
                            <div class="fw-bold font-monospace text-success fs-5">
                                + Rs. {{ number_format($truckProfit, 2) }} ({{ $truckMargin }}%)
                            </div>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted small">4. Stock Value (Truck + Warehouse):</span>
                            <div class="fw-bold font-monospace text-warning fs-6">Rs. {{ number_format($truckRemainingValue + $truckUnloadedValue, 2) }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- TAB 2: ROUTE SALES / DIRECT DELIVERIES --}}
        <div class="tab-pane fade" id="salesPane" role="tabpanel">
            <div class="erp-card shadow-sm">
                <div class="erp-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="fas fa-truck-ramp-box text-success"></i>
                            Sales & Dispatches Made Directly From this Truck
                        </h6>
                        <small class="text-muted">Customers who received delivery straight from this vehicle on route.</small>
                    </div>
                    @if($consignment->status === 'in_transit' && $remainingTotal > 0)
                        <a href="{{ route('truck-purchase.sale.create', $consignment->id) }}" class="btn btn-sm btn-success fw-semibold">
                            <i class="fas fa-plus me-1"></i> New Direct Sale
                        </a>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-detail align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 14%;">Invoice #</th>
                                <th style="width: 22%;">Customer Name</th>
                                <th style="width: 14%;">Delivery Date</th>
                                <th style="width: 30%;">Products Delivered</th>
                                <th style="width: 12%;" class="text-end">Bill Amount</th>
                                <th style="width: 8%;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($consignment->sales as $sale)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank" class="fw-bold font-monospace text-primary text-decoration-none">
                                            {{ $sale->invoice_no }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $sale->customer_relation->customer_name ?? $sale->customer_relation->name ?? 'Walk-in Customer' }}</div>
                                        <small class="text-muted">{{ $sale->customer_relation->mobile ?? $sale->customer_relation->phone ?? '' }}</small>
                                    </td>
                                    <td>
                                        {{ $sale->created_at ? \Carbon\Carbon::parse($sale->created_at)->format('d M, Y') : '—' }}
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
                                        <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="View Sale Invoice">
                                            <i class="fas fa-print me-1"></i> Print Bill
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-truck-loading fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                        <div class="fw-bold fs-6">No Route Sales Yet</div>
                                        <p class="small text-muted mb-3">Abhi tak is truck se koi direct customer sale nahi hui.</p>
                                        @if($consignment->status === 'in_transit' && $remainingTotal > 0)
                                            <a href="{{ route('truck-purchase.sale.create', $consignment->id) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-cart-plus me-1"></i> Make First Sale from Truck
                                            </a>
                                        @endif
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
                <div class="erp-card shadow-sm mb-3">
                    <div class="erp-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">
                                <i class="fas fa-user-tag me-1"></i> Vendor #{{ $pIndex + 1 }}: {{ $p->vendor->name ?? 'Vendor' }}
                            </span>
                            <span class="text-muted small">
                                Bill No: <strong class="text-dark font-monospace">{{ $p->invoice_no }}</strong>
                                @if($p->vendor_bill_no) (Ref: {{ $p->vendor_bill_no }}) @endif
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('purchase.invoice', $p->id) }}" target="_blank" class="btn btn-outline-primary btn-sm px-3 fw-semibold">
                                <i class="fas fa-print me-1"></i> Print Vendor Bill
                            </a>
                        </div>
                    </div>

                    <div class="p-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-detail align-middle mb-2">
                                <thead>
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
                                <tfoot>
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
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title fw-bold text-dark" id="unloadModalLabel">
                        <i class="fas fa-warehouse me-2"></i> Unload Remaining Stock to Warehouse
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 shadow-sm">
                        <i class="fas fa-info-circle me-1"></i>
                        Truck mein is waqt kul <strong>{{ $remainingTotal }} Units</strong> bacha hua maal mojood hai. Yeh sara bacha hua maal muntakhib kiye gaye warehouse ke physical stock mein jama ho jaye ga aur consignment **Completed** ho jaye gi.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Select Destination Warehouse (کس گودام میں اتارنا ہے؟)</label>
                        <select class="form-select" name="warehouse_id" required style="height: 40px;">
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ $wh->id == $consignment->warehouse_id ? 'selected' : '' }}>
                                    {{ $wh->is_shop ? '🏪 ' : '🏢 ' }}{{ $wh->warehouse_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <p class="text-muted small mb-0">
                        * Note: Unload hone ke baad truck ka trip band ho jaye ga aur mazeed direct sales nahi ho sakengi.
                    </p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark px-4 shadow-sm">
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
