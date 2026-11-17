@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Premium ERP Styling */
    .truck-dashboard-wrapper {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1e293b;
    }

    /* KPI Stat Cards */
    .stat-card-pro {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 20px;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .stat-card-pro:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(0,0,0,0.06);
        border-color: #cbd5e1;
    }
    .stat-card-pro .stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* Filter Card */
    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .filter-card .form-control,
    .filter-card .form-select {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 0.85rem;
        padding: 7px 12px;
        transition: all 0.2s;
    }
    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    /* Table Styling */
    .main-table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03);
        overflow: hidden;
    }
    .table-consignments {
        margin-bottom: 0;
    }
    .table-consignments thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 16px;
        white-space: nowrap;
    }
    .table-consignments tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 0.875rem;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .table-consignments tbody tr {
        transition: background-color 0.15s ease;
    }
    .table-consignments tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Plate badge for truck no */
    .plate-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #0f172a;
        color: #f8fafc;
        font-family: monospace;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 4px 10px;
        border-radius: 6px;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 4px rgba(15, 23, 42, 0.15);
    }

    /* Pulse animation for active transit */
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

    /* Stock progress bar */
    .stock-progress-track {
        height: 6px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 6px;
    }
    .stock-progress-fill {
        height: 100%;
        border-radius: 4px;
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    }

    /* Action buttons */
    .btn-action-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.15s ease;
    }
    .btn-action-icon:hover {
        transform: translateY(-1px);
    }
</style>

<div class="truck-dashboard-wrapper container-fluid px-3 py-3">

    {{-- TOP BAR WITH TITLE & QUICK ACTION --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 bg-white p-3 rounded-3 border shadow-sm">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                <i class="fas fa-truck-moving fs-3"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="fw-bold text-dark mb-0">Truck Consignments</h4>
                    <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1 rounded-pill small">
                        ٹرک انورڈ و سیل مینجمنٹ
                    </span>
                </div>
                <p class="text-muted small mb-0 mt-1">
                    Multi-vendor truck inward purchases, on-route direct deliveries, live in-truck balances & warehouse offloading.
                </p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('truck-purchase.create') }}" class="btn btn-primary px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2">
                <i class="fas fa-plus-circle"></i>
                <span>New Truck Purchase</span>
            </a>
        </div>
    </div>

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

    {{-- STATS KPI OVERVIEW CARDS --}}
    <div class="row g-3 mb-4">
        {{-- Total Consignments --}}
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card-pro d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase tracking-wider">Total Consignments</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $stats['total_consignments'] ?? $consignments->total() }}</h3>
                    <div class="text-muted small mt-1">Recorded truck arrivals</div>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-truck-ramp-box"></i>
                </div>
            </div>
        </div>

        {{-- Active On Route (In Transit) --}}
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card-pro d-flex align-items-center justify-content-between" style="border-left: 3px solid #f59e0b;">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase tracking-wider">In Transit (Active)</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0 d-flex align-items-center gap-2">
                        {{ $stats['in_transit'] ?? 0 }}
                        <span class="pulse-dot" title="Truck is on route"></span>
                    </h3>
                    <div class="text-warning fw-semibold small mt-1">Stock currently on route</div>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-route"></i>
                </div>
            </div>
        </div>

        {{-- Stock Remaining on Trucks --}}
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card-pro d-flex align-items-center justify-content-between" style="border-left: 3px solid #0284c7;">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase tracking-wider">Live Truck Stock</span>
                    <h3 class="fw-bold text-primary font-monospace mt-1 mb-0">
                        {{ number_format($stats['remaining_units'] ?? 0, 1) }}
                    </h3>
                    <div class="text-muted small mt-1">Units available for route sale</div>
                </div>
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
            </div>
        </div>

        {{-- Route Sales & Profit --}}
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card-pro d-flex align-items-center justify-content-between" style="border-left: 3px solid #10b981;">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase tracking-wider">Route Sales & Profit (منافع)</span>
                    <h3 class="fw-bold text-success font-monospace mt-1 mb-0">
                        + Rs. {{ number_format($stats['total_profit'] ?? 0, 0) }}
                    </h3>
                    <div class="text-muted small mt-1">
                        Sales: <strong class="text-dark">Rs. {{ number_format($stats['total_sales'] ?? 0, 0) }}</strong>
                    </div>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- SEARCH & FILTER FORM --}}
    <div class="filter-card p-3 mb-4">
        <form method="GET" action="{{ route('truck-purchase.index') }}" class="row g-2 align-items-end">
            <div class="col-lg-3 col-md-4">
                <label class="form-label small fw-bold text-muted mb-1">
                    <i class="fas fa-search me-1 text-primary"></i> Truck / Vehicle No
                </label>
                <input type="text" class="form-control" name="truck_no" value="{{ request('truck_no') }}" placeholder="e.g. LES-4580, TK-921...">
            </div>
            <div class="col-lg-2 col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">
                    <i class="fas fa-toggle-on me-1 text-primary"></i> Trip Status
                </label>
                <select class="form-select" name="status">
                    <option value="">All Statuses (تمام)</option>
                    <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>In Transit (راستے میں)</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed (مال اتر چکا)</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">
                    <i class="fas fa-calendar-alt me-1 text-primary"></i> From Date
                </label>
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-lg-2 col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">
                    <i class="fas fa-calendar-check me-1 text-primary"></i> To Date
                </label>
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-lg-3 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary px-3 flex-grow-1 d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-filter"></i>
                    <span>Apply Filter</span>
                </button>
                <a href="{{ route('truck-purchase.index') }}" class="btn btn-outline-secondary px-3" title="Reset Filters">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- CONSIGNMENTS TABLE CARD --}}
    <div class="main-table-card">
        <div class="table-responsive">
            <table class="table table-hover table-consignments align-middle">
                <thead>
                    <tr>
                        <th style="width: 13%;">Consignment Ref</th>
                        <th style="width: 16%;">Truck & Logistics</th>
                        <th style="width: 14%;">Driver Details</th>
                        <th style="width: 12%;">Status</th>
                        <th style="width: 18%;">Stock (Remaining / Loaded)</th>
                        <th style="width: 12%;">Vendors Loaded</th>
                        <th style="width: 15%;" class="text-end">Cost & Sales (Profit)</th>
                        <th style="width: 10%;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consignments as $c)
                        @php
                            $totalAmount = (float) $c->purchases->sum('net_amount');
                            $totalLoaded = (float) $c->items->sum('loaded_qty');
                            $totalRemaining = (float) $c->items->sum('remaining_qty');
                            $soldQty = (float) $c->items->sum('sold_qty');
                            $unloadedQty = (float) $c->items->sum('unloaded_qty');
                            $percentDispatched = $totalLoaded > 0 ? min(100, round((($soldQty + $unloadedQty) / $totalLoaded) * 100)) : 100;
                        @endphp
                        <tr>
                            {{-- Consignment Ref --}}
                            <td>
                                <a href="{{ route('truck-purchase.show', $c->id) }}" class="fw-bold text-primary font-monospace text-decoration-none d-block">
                                    <i class="fas fa-hashtag text-secondary me-1"></i>{{ $c->consignment_no }}
                                </a>
                                @if($c->bilty_no)
                                    <span class="badge bg-light text-muted border mt-1" style="font-size: 0.72rem;">
                                        Bilty: <strong class="text-dark">{{ $c->bilty_no }}</strong>
                                    </span>
                                @endif
                                <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                    {{ $c->arrival_date ? $c->arrival_date->format('d M, Y') : '—' }}
                                </div>
                            </td>

                            {{-- Truck & Transporter --}}
                            <td>
                                <div class="plate-badge mb-1">
                                    <i class="fas fa-truck"></i> {{ $c->truck_no }}
                                </div>
                                <div class="text-muted small text-truncate" style="max-width: 180px;">
                                    <i class="fas fa-shipping-fast text-secondary me-1"></i>{{ $c->transporter_name ?: 'Direct / Private Transport' }}
                                </div>
                                @if($c->total_freight > 0)
                                    <div class="text-muted small" style="font-size: 0.73rem;">
                                        Kiraya: <span class="fw-semibold text-dark">Rs. {{ number_format($c->total_freight, 0) }}</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Driver Details --}}
                            <td>
                                <div class="fw-semibold text-dark">
                                    <i class="fas fa-user-circle text-muted me-1"></i>{{ $c->driver_name ?: '—' }}
                                </div>
                                @if($c->driver_phone)
                                    <div class="small mt-1">
                                        <a href="tel:{{ $c->driver_phone }}" class="text-decoration-none text-muted">
                                            <i class="fas fa-phone-alt text-success me-1"></i>{{ $c->driver_phone }}
                                        </a>
                                    </div>
                                @endif
                            </td>

                            {{-- Status Badge --}}
                            <td>
                                @if($c->status === 'in_transit')
                                    <span class="badge bg-warning bg-opacity-20 text-dark border border-warning px-2 py-1 fw-bold d-inline-flex align-items-center gap-1">
                                        <span class="pulse-dot"></span> In Transit (راستے میں)
                                    </span>
                                @else
                                    <span class="badge bg-success bg-opacity-15 text-success border border-success px-2 py-1 fw-bold d-inline-flex align-items-center gap-1">
                                        <i class="fas fa-check-double"></i> Completed (اتار لیا)
                                    </span>
                                @endif
                                @if($c->status === 'completed' && $c->unloadedWarehouse)
                                    <div class="text-muted small mt-1" style="font-size: 0.72rem;">
                                        In: {{ $c->unloadedWarehouse->warehouse_name }}
                                    </div>
                                @endif
                            </td>

                            {{-- Stock Progress --}}
                            <td>
                                <div class="d-flex justify-content-between align-items-baseline mb-1">
                                    <span class="fw-bold font-monospace {{ $totalRemaining > 0 ? 'text-primary' : 'text-muted' }}">
                                        {{ number_format($totalRemaining, 1) }} left
                                    </span>
                                    <span class="text-muted small">/ {{ number_format($totalLoaded, 1) }} Total</span>
                                </div>
                                <div class="stock-progress-track" title="{{ $percentDispatched }}% dispatched/delivered">
                                    <div class="stock-progress-fill" style="width: {{ $percentDispatched }}%;"></div>
                                </div>
                                <div class="d-flex justify-content-between text-muted mt-1" style="font-size: 0.72rem;">
                                    <span><i class="fas fa-arrow-right-from-bracket text-danger me-1"></i>{{ number_format($soldQty, 1) }} sold</span>
                                    <span><i class="fas fa-warehouse text-secondary me-1"></i>{{ number_format($unloadedQty, 1) }} offloaded</span>
                                </div>
                            </td>

                            {{-- Vendors --}}
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1 rounded-pill">
                                    <i class="fas fa-users me-1"></i> {{ $c->purchases_count }} Vendors
                                </span>
                                <div class="text-muted small text-truncate mt-1" style="max-width: 170px;" title="{{ $c->purchases->pluck('vendor.name')->filter()->unique()->join(', ') }}">
                                    {{ $c->purchases->pluck('vendor.name')->filter()->unique()->join(', ') ?: 'No vendor names' }}
                                </div>
                            </td>

                            {{-- Total Cost & Sales / Profit --}}
                            <td class="text-end">
                                <div class="fw-bold text-dark font-monospace" style="font-size: 0.88rem;">
                                    Cost: Rs. {{ number_format($totalAmount, 0) }}
                                </div>
                                @if($c->sales_count > 0)
                                    <div class="text-muted small mt-1 font-monospace" style="font-size: 0.75rem;">
                                        Sale: <strong class="text-dark">Rs. {{ number_format($c->total_sales_revenue, 0) }}</strong>
                                    </div>
                                    <div class="mt-1">
                                        <span class="badge {{ $c->realized_profit >= 0 ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger' }} px-2 py-1 font-monospace" style="font-size: 0.72rem;">
                                            <i class="fas {{ $c->realized_profit >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} me-1"></i>
                                            Profit: Rs. {{ number_format($c->realized_profit, 0) }} ({{ $c->profit_margin_percent }}%)
                                        </span>
                                    </div>
                                @else
                                    <div class="text-muted small mt-1" style="font-size: 0.72rem;">No route sales yet</div>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    {{-- View Details --}}
                                    <a href="{{ route('truck-purchase.show', $c->id) }}" class="btn btn-outline-primary btn-action-icon" title="View Full Breakdown & Live Stock">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Direct Sale on Route (if In Transit and stock remaining) --}}
                                    @if($c->status === 'in_transit' && $totalRemaining > 0)
                                        <a href="{{ route('truck-purchase.sale.create', $c->id) }}" class="btn btn-success btn-action-icon text-white" title="Direct Sale / Delivery from Truck">
                                            <i class="fas fa-cart-plus"></i>
                                        </a>
                                    @endif

                                    {{-- Print Inward Slip --}}
                                    <a href="{{ route('truck-purchase.print', $c->id) }}" target="_blank" class="btn btn-outline-secondary btn-action-icon" title="Print Receiving Gate Pass">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    {{-- Delete Consignment --}}
                                    <form action="{{ route('truck-purchase.destroy', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Kia aap waqai is Truck Consignment ko delete karna chahte hain? Tamam vendor bills aur stock entries rollback ho jayengi.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-action-icon" title="Delete Consignment">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <div class="mb-3">
                                        <i class="fas fa-truck-moving text-muted" style="font-size: 3.5rem; opacity: 0.4;"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark">No Truck Consignments Found</h5>
                                    <p class="text-muted small mb-3">Abhi tak koi multi-vendor truck consignment add nahi hui ya filter ke mutabiq koi record nahi mila.</p>
                                    <a href="{{ route('truck-purchase.create') }}" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                                        <i class="fas fa-plus-circle me-1"></i> Add First Truck Purchase
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($consignments->hasPages())
            <div class="p-3 border-top bg-light d-flex justify-content-between align-items-center">
                <span class="text-muted small">Showing {{ $consignments->firstItem() }} - {{ $consignments->lastItem() }} of {{ $consignments->total() }}</span>
                <div>
                    {{ $consignments->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
