@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .table-consignments th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 0.82rem;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px 10px;
    }
    .table-consignments td {
        padding: 12px 10px;
        vertical-align: middle;
        font-size: 0.88rem;
    }
</style>

<div class="container-fluid px-3 py-3">
    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 bg-white p-3 rounded-3 shadow-sm border">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fas fa-truck-moving text-primary me-2"></i> Truck Consignments (ٹرک انورڈ لسٹ)
            </h4>
            <p class="text-muted small mb-0">Record, track route sales, and offload multi-vendor truck consignments into warehouses.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('truck-purchase.create') }}" class="btn btn-primary px-3 shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> New Truck Purchase
            </a>
        </div>
    </div>

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

    {{-- FILTER FORM --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('truck-purchase.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Truck / Vehicle No</label>
                    <input type="text" class="form-control form-control-sm" name="truck_no" value="{{ request('truck_no') }}" placeholder="Search Truck No...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Status</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="">All Statuses</option>
                        <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>In Transit (On Route)</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed (Unloaded)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">From Date</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">To Date</label>
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('truck-purchase.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- CONSIGNMENTS TABLE --}}
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover table-consignments mb-0">
                <thead>
                    <tr>
                        <th>Consignment #</th>
                        <th>Truck & Transporter</th>
                        <th>Driver Details</th>
                        <th>Status</th>
                        <th>Loaded / Remaining</th>
                        <th>Vendors / Bills</th>
                        <th class="text-end">Total Amount</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consignments as $c)
                        @php
                            $totalAmount = $c->purchases->sum('net_amount');
                            $totalLoaded = (float) $c->items->sum('loaded_qty');
                            $totalRemaining = (float) $c->items->sum('remaining_qty');
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('truck-purchase.show', $c->id) }}" class="fw-bold text-primary font-monospace text-decoration-none">
                                    {{ $c->consignment_no }}
                                </a>
                                @if($c->bilty_no)
                                    <div class="text-muted small">Bilty: <span class="fw-semibold">{{ $c->bilty_no }}</span></div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $c->truck_no }}</div>
                                <div class="text-muted small">{{ $c->transporter_name ?: 'Self / Direct' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $c->driver_name ?: '—' }}</div>
                                <div class="text-muted small">{{ $c->driver_phone ?: '' }}</div>
                            </td>
                            <td>
                                @if($c->status === 'in_transit')
                                    <span class="badge bg-warning text-dark px-2 py-1 fw-bold">
                                        <i class="fas fa-truck-moving me-1"></i> In Transit
                                    </span>
                                @else
                                    <span class="badge bg-success px-2 py-1 fw-bold">
                                        <i class="fas fa-check-double me-1"></i> Completed
                                    </span>
                                @endif
                                <div class="text-muted small mt-1">{{ $c->arrival_date ? $c->arrival_date->format('d/m/Y') : '—' }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge {{ $totalRemaining > 0 ? 'bg-success' : 'bg-secondary' }} font-monospace fs-6">
                                        {{ $totalRemaining }}
                                    </span>
                                    <span class="text-muted small">/ {{ $totalLoaded }} Units</span>
                                </div>
                                <div class="text-muted small mt-1">
                                    <span class="text-danger fw-semibold">{{ (float)$c->items->sum('sold_qty') }} sold</span> | 
                                    <span class="text-secondary">{{ (float)$c->items->sum('unloaded_qty') }} offloaded</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info border border-info px-2 py-1">
                                    <i class="fas fa-users me-1"></i> {{ $c->purchases_count }} Vendors
                                </span>
                                <div class="text-muted small text-truncate mt-1" style="max-width: 180px;">
                                    {{ $c->purchases->pluck('vendor.name')->filter()->unique()->join(', ') }}
                                </div>
                            </td>
                            <td class="text-end fw-bold text-success font-monospace fs-6">
                                Rs. {{ number_format($totalAmount, 2) }}
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('truck-purchase.show', $c->id) }}" class="btn btn-outline-primary" title="View Breakdown & Live Balance">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($c->status === 'in_transit' && $totalRemaining > 0)
                                        <a href="{{ route('truck-purchase.sale.create', $c->id) }}" class="btn btn-outline-success" title="Direct Sale from Truck">
                                            <i class="fas fa-cart-plus"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('truck-purchase.print', $c->id) }}" target="_blank" class="btn btn-outline-secondary" title="Print Receiving Note">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <form action="{{ route('truck-purchase.destroy', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this truck consignment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete Consignment">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-truck-loading fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                <div class="fw-bold fs-6">No Truck Consignments Found</div>
                                <p class="small text-muted mb-3">Abhi tak koi multi-vendor truck consignment add nahi hui.</p>
                                <a href="{{ route('truck-purchase.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus-circle me-1"></i> Add First Truck Purchase
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($consignments->hasPages())
            <div class="card-footer bg-white py-3 border-top">
                {{ $consignments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
