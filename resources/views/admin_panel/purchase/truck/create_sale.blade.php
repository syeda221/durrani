@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .header-gradient-card {
        background: linear-gradient(135deg, #065f46 0%, #047857 100%);
        color: #ffffff;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 24px;
    }
    .stock-badge {
        font-size: 0.78rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 12px;
    }
    .cost-badge {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
    }
</style>

<div class="container-fluid px-3 py-3">
    <form id="truckSaleForm" method="POST" action="{{ route('truck-purchase.sale.store', $consignment->id) }}">
        @csrf

        {{-- HEADER BANNER --}}
        <div class="header-gradient-card shadow-sm">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-truck-ramp-box fs-3 text-white"></i>
                        <h4 class="mb-0 fw-bold text-white">Direct Route Sale from Truck</h4>
                    </div>
                    <p class="text-emerald-100 text-light mb-0 mt-1 small opacity-75">
                        Gari se direct customer ko deliver karein. Har vendor ki kharid price dekh kar sale price set karein.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark fs-6 px-3 py-2 font-monospace">
                        Truck: {{ $consignment->truck_no }}
                    </span>
                    <a href="{{ route('truck-purchase.show', $consignment->id) }}" class="btn btn-outline-light btn-sm px-3">
                        <i class="fas fa-arrow-left me-1"></i> Back to Truck
                    </a>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- CUSTOMER & SALE INFO CARD --}}
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-bold text-muted small">
                            <i class="fas fa-user-check text-primary me-1"></i> Select Customer (گاہک کا نام) <span class="text-danger">*</span>
                        </label>
                        <select class="form-select select2-customer" name="customer_id" required style="height: 38px;">
                            <option value="" selected disabled>-- Choose Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->customer_name }} ({{ $c->mobile ?: 'No phone' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">
                            <i class="fas fa-calendar-alt text-secondary me-1"></i> Delivery / Sale Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" name="sale_date" value="{{ date('Y-m-d') }}" required style="height: 38px;">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">
                            <i class="fas fa-comment-alt text-secondary me-1"></i> Delivery Remarks / City Note
                        </label>
                        <input type="text" class="form-control" name="comment" placeholder="e.g. Delivered at Faisalabad bypass..." style="height: 38px;">
                    </div>
                </div>
            </div>
        </div>

        {{-- AVAILABLE STOCK IN TRUCK TABLE --}}
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-cubes-stacked text-success me-2"></i>Available Stock Currently on Truck #{{ $consignment->truck_no }}
                    </h6>
                    <small class="text-muted">Har item ki Purchase Cost (خرید ریٹ) dekh kar sale rate aur qty enter karein.</small>
                </div>
                <span class="badge bg-success-subtle text-success border border-success px-3 py-1">
                    {{ $availableItems->count() }} Batches on Truck
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 4%;" class="text-center">Select</th>
                                <th style="width: 20%;">Product / Item</th>
                                <th style="width: 14%;">Vendor Source</th>
                                <th style="width: 12%;" class="text-end bg-warning-subtle text-dark fw-bold">Pur. Cost (خرید ریٹ)</th>
                                <th style="width: 10%;" class="text-center">In Truck</th>
                                <th style="width: 11%;" class="text-center">Deliver Qty</th>
                                <th style="width: 12%;" class="text-end">Sale Rate (Price)</th>
                                <th style="width: 7%;" class="text-end">Discount</th>
                                <th style="width: 10%;" class="text-end">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($availableItems as $idx => $tItem)
                                @php
                                    $cost = (float) $tItem->purchase_price;
                                    $defaultSalePrice = (float)($tItem->product->sale_price_per_piece ?: $cost);
                                @endphp
                                <tr class="sale-row" data-row-id="{{ $tItem->id }}" data-purchase-cost="{{ $cost }}">
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input item-select-check" style="cursor: pointer;" checked>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $tItem->product->item_name ?? 'Product #'.$tItem->product_id }}</div>
                                        @if(!empty($tItem->product->item_code))
                                            <span class="text-muted small font-monospace">Code: {{ $tItem->product->item_code }}</span>
                                        @endif
                                        <input type="hidden" name="items[{{ $idx }}][truck_consignment_item_id]" value="{{ $tItem->id }}">
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1">
                                            <i class="fas fa-tag me-1"></i> {{ $tItem->vendor->name ?? 'Vendor' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="cost-badge font-monospace fs-6">
                                            Rs. {{ number_format($cost, 2) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success font-monospace px-2 py-1 fs-6">
                                            {{ (float)$tItem->remaining_qty }} {{ $tItem->unit ?: 'Pcs' }}
                                        </span>
                                    </td>
                                    <td>
                                        <input type="number" step="0.001" min="0" max="{{ (float)$tItem->remaining_qty }}" 
                                               class="form-control form-control-sm text-center fw-bold sell-qty-input" 
                                               name="items[{{ $idx }}][qty]" 
                                               value="0" 
                                               style="height: 36px;">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control form-control-sm text-end fw-semibold sell-price-input" 
                                               name="items[{{ $idx }}][price]" 
                                               value="{{ $defaultSalePrice }}" 
                                               style="height: 36px;">
                                        <div class="text-end margin-indicator fw-semibold" style="font-size: 0.72rem;"></div>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control form-control-sm text-end sell-disc-input" 
                                               name="items[{{ $idx }}][discount]" 
                                               value="0.00" 
                                               style="height: 36px;">
                                    </td>
                                    <td class="text-end fw-bold font-monospace line-total-display text-dark">
                                        Rs. 0.00
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-light p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <span class="text-muted small">Deliver Qty:</span>
                            <span class="fw-bold text-dark font-monospace ms-1 fs-6" id="displayTotalQty">0</span>
                        </div>
                        <div class="border-start ps-3">
                            <span class="text-muted small">Total Cost:</span>
                            <span class="fw-bold text-muted font-monospace ms-1 fs-6" id="displayTotalCost">Rs. 0.00</span>
                        </div>
                        <div class="border-start ps-3">
                            <span class="text-muted small">Gross Sale:</span>
                            <span class="fw-bold text-dark font-monospace ms-1 fs-6" id="displaySubtotal">Rs. 0.00</span>
                        </div>
                        <div class="border-start ps-3">
                            <span class="text-muted small">Est. Profit:</span>
                            <span class="fw-bold font-monospace ms-1 fs-6" id="displayProfit">Rs. 0.00</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-1">
                            <span class="text-muted small">Overall Disc:</span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end" id="inputSaleDiscount" name="discount" value="0.00" style="width: 100px;">
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="text-muted small">Paid on Spot:</span>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end fw-bold text-success" id="inputSalePaid" name="paid_amount" value="0.00" placeholder="0.00" style="width: 110px;">
                        </div>
                        <div class="border-start ps-3">
                            <span class="text-muted small">Net Bill:</span>
                            <span class="fw-bold text-success font-monospace ms-1 fs-5" id="displayNetBill">Rs. 0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BOTTOM SUBMIT BUTTON --}}
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('truck-purchase.show', $consignment->id) }}" class="btn btn-outline-secondary px-4">
                Cancel
            </a>
            <button type="submit" class="btn btn-success btn-lg px-4 fw-bold shadow" id="btnSubmitTruckSale">
                <i class="fas fa-check-circle me-1"></i> Save Sale & Deliver from Truck
            </button>
        </div>

    </form>
</div>

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/vendors/select2/js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('.select2-customer').select2({
        placeholder: '-- Choose Customer --',
        width: '100%'
    });

    // Row selection toggle
    $('.item-select-check').on('change', function() {
        const row = $(this).closest('.sale-row');
        if (!$(this).is(':checked')) {
            row.find('.sell-qty-input').val(0);
        }
        calculateTotals();
    });

    // Input changes
    $(document).on('input', '.sell-qty-input, .sell-price-input, .sell-disc-input', function() {
        const row = $(this).closest('.sale-row');
        const max = parseFloat(row.find('.sell-qty-input').attr('max')) || 0;
        let qty = parseFloat(row.find('.sell-qty-input').val()) || 0;

        if (qty > max) {
            qty = max;
            row.find('.sell-qty-input').val(max);
            Swal.fire('Limit Exceeded', `Maximum available on this truck is ${max}`, 'warning');
        }

        const price = parseFloat(row.find('.sell-price-input').val()) || 0;
        const disc = parseFloat(row.find('.sell-disc-input').val()) || 0;
        const cost = parseFloat(row.data('purchase-cost')) || 0;
        const total = Math.max(0, (qty * price) - disc);

        row.find('.line-total-display').text('Rs. ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        // Margin indicator
        const margin = price - cost;
        const ind = row.find('.margin-indicator');
        if (margin > 0) {
            ind.html(`<span class="text-success"><i class="fas fa-arrow-up"></i> Profit: Rs. ${margin.toFixed(2)}</span>`);
        } else if (margin < 0) {
            ind.html(`<span class="text-danger"><i class="fas fa-arrow-down"></i> Loss: Rs. ${Math.abs(margin).toFixed(2)}</span>`);
        } else {
            ind.html(`<span class="text-muted">At Cost</span>`);
        }

        calculateTotals();
    });

    $('#inputSaleDiscount').on('input', function() {
        calculateTotals();
    });

    function calculateTotals() {
        let grandSubtotal = 0;
        let grandCost = 0;
        let grandQty = 0;

        $('.sale-row').each(function() {
            const isChecked = $(this).find('.item-select-check').is(':checked');
            if (isChecked) {
                const qty = parseFloat($(this).find('.sell-qty-input').val()) || 0;
                const price = parseFloat($(this).find('.sell-price-input').val()) || 0;
                const cost = parseFloat($(this).data('purchase-cost')) || 0;
                const disc = parseFloat($(this).find('.sell-disc-input').val()) || 0;
                const total = Math.max(0, (qty * price) - disc);

                grandQty += qty;
                grandSubtotal += total;
                grandCost += (qty * cost);
            }
        });

        const overallDisc = parseFloat($('#inputSaleDiscount').val()) || 0;
        const net = Math.max(0, grandSubtotal - overallDisc);
        const profit = net - grandCost;

        $('#displayTotalQty').text(grandQty.toFixed(2));
        $('#displayTotalCost').text('Rs. ' + grandCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#displaySubtotal').text('Rs. ' + grandSubtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#displayNetBill').text('Rs. ' + net.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        const pEl = $('#displayProfit');
        if (profit >= 0) {
            pEl.removeClass('text-danger').addClass('text-success').text('+ Rs. ' + profit.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        } else {
            pEl.removeClass('text-success').addClass('text-danger').text('- Rs. ' + Math.abs(profit).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        }
    }

    $('#truckSaleForm').on('submit', function(e) {
        let totalQty = 0;
        $('.sell-qty-input').each(function() {
            totalQty += parseFloat($(this).val()) || 0;
        });

        if (totalQty <= 0) {
            e.preventDefault();
            Swal.fire('No Quantity Entered', 'Please enter quantity to deliver for at least one item.', 'warning');
            return false;
        }

        $('#btnSubmitTruckSale').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Saving Sale...');
    });

    // Initial triggers for margin indicators
    $('.sale-row').each(function() {
        const price = parseFloat($(this).find('.sell-price-input').val()) || 0;
        const cost = parseFloat($(this).data('purchase-cost')) || 0;
        const margin = price - cost;
        const ind = $(this).find('.margin-indicator');
        if (margin > 0) {
            ind.html(`<span class="text-success"><i class="fas fa-arrow-up"></i> Profit: Rs. ${margin.toFixed(2)}</span>`);
        } else if (margin < 0) {
            ind.html(`<span class="text-danger"><i class="fas fa-arrow-down"></i> Loss: Rs. ${Math.abs(margin).toFixed(2)}</span>`);
        } else {
            ind.html(`<span class="text-muted">At Cost</span>`);
        }
    });

    calculateTotals();
});
</script>
@endsection
