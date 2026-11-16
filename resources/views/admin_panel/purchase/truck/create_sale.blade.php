@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Premium ERP Styling */
    .truck-sale-wrapper {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1e293b;
    }

    /* Hero Banner */
    .hero-sale-banner {
        background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
        color: #ffffff;
        border-radius: 12px;
        padding: 22px 28px;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(6, 78, 59, 0.2);
        position: relative;
        overflow: hidden;
    }
    .hero-sale-banner::after {
        content: '\f48b';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: 25px;
        bottom: -25px;
        font-size: 8.5rem;
        opacity: 0.06;
        color: #ffffff;
        pointer-events: none;
    }

    /* Cards */
    .sale-section-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .sale-section-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 20px;
    }

    /* Inputs */
    .form-control-erp {
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        font-size: 0.86rem;
        font-weight: 500;
        color: #1e293b;
        height: 38px;
        transition: all 0.2s;
    }
    .form-control-erp:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.15);
    }

    /* Table */
    .table-sale th {
        background-color: #f8fafc;
        color: #334155;
        font-weight: 700;
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 10px;
        border-bottom: 2px solid #cbd5e1;
    }
    .table-sale td {
        padding: 10px 10px;
        vertical-align: middle;
        font-size: 0.875rem;
    }
    .table-sale tbody tr {
        transition: background-color 0.15s ease;
    }
    .table-sale tbody tr:hover {
        background-color: #f0fdf4;
    }

    /* Badges */
    .cost-badge {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 6px;
        font-family: monospace;
        font-size: 0.85rem;
    }
    .stock-pill {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        font-family: monospace;
        font-size: 0.85rem;
    }

    /* Sticky Footer Bar */
    .sale-summary-bar {
        background: rgba(255, 255, 255, 0.97);
        backdrop-filter: blur(10px);
        border: 2px solid #cbd5e1;
        border-radius: 12px;
        padding: 14px 22px;
        position: sticky;
        bottom: 12px;
        z-index: 1000;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    /* Select2 */
    .select2-container .select2-selection--single {
        height: 38px !important;
        display: flex !important;
        align-items: center !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 7px !important;
        font-size: 0.85rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
</style>

<div class="truck-sale-wrapper container-fluid px-3 py-3">
    <form id="truckSaleForm" method="POST" action="{{ route('truck-purchase.sale.store', $consignment->id) }}">
        @csrf

        {{-- HERO HEADER BANNER --}}
        <div class="hero-sale-banner">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-white text-dark fw-bold font-monospace px-3 py-1 fs-6">
                            <i class="fas fa-truck text-success me-1"></i> {{ $consignment->truck_no }}
                        </span>
                        <span class="badge bg-emerald-700 bg-opacity-75 text-white border border-light border-opacity-25 px-2 py-1 small">
                            Ref: {{ $consignment->consignment_no }}
                        </span>
                    </div>
                    <h3 class="fw-bold text-white mt-2 mb-1">
                        <i class="fas fa-truck-ramp-box text-warning me-2"></i> Direct Route Sale from Truck
                    </h3>
                    <p class="text-light opacity-90 mb-0 small" style="max-width: 820px;">
                        Gari se direct customer ko deliver karein (راستے میں مال کی فروخت). Har product ki purchase cost (خرید ریٹ) dekh kar sale price set karein. Real-time profit display hoga.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('truck-purchase.show', $consignment->id) }}" class="btn btn-outline-light btn-sm px-3 fw-semibold">
                        <i class="fas fa-arrow-left me-1"></i> Back to Truck Details
                    </a>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 4px solid #ef4444 !important;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-exclamation-circle fs-5 text-danger"></i>
                    <div class="fw-medium text-dark">{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- STEP 1: CUSTOMER & DELIVERY INFO --}}
        <div class="sale-section-card mb-4">
            <div class="sale-section-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success rounded-circle p-2"><i class="fas fa-user-check"></i></span>
                    <h6 class="mb-0 fw-bold text-dark">Customer & Delivery Information (کسٹمر اور ڈیلیوری کی معلومات)</h6>
                </div>
                <span class="text-muted small">Select customer receiving goods from this truck</span>
            </div>
            <div class="p-3 p-md-4">
                <div class="row g-3">
                    <div class="col-lg-5 col-md-6">
                        <label class="form-label fw-bold text-dark small mb-1">
                            <i class="fas fa-user text-success me-1"></i> Select Customer (گاہک کا نام) <span class="text-danger">*</span>
                        </label>
                        <select class="form-select select2-customer" name="customer_id" required>
                            <option value="" selected disabled>-- Search & Choose Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">
                                    {{ $c->customer_name }} {{ $c->mobile ? '('.$c->mobile.')' : '' }} {{ $c->city ? ' - '.$c->city : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-bold text-dark small mb-1">
                            <i class="fas fa-calendar-alt text-secondary me-1"></i> Delivery / Sale Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control form-control-erp" name="sale_date" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <label class="form-label fw-bold text-dark small mb-1">
                            <i class="fas fa-location-dot text-danger me-1"></i> Delivery Spot / Route Note
                        </label>
                        <input type="text" class="form-control form-control-erp" name="comment" placeholder="e.g. Delivered at Faisalabad Bypass / Shop delivery...">
                    </div>
                </div>
            </div>
        </div>

        {{-- STEP 2: AVAILABLE STOCK IN TRUCK TABLE --}}
        <div class="sale-section-card mb-4">
            <div class="sale-section-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-cubes-stacked text-success"></i>
                        Available Stock Currently Loaded on Truck #{{ $consignment->truck_no }}
                    </h6>
                    <small class="text-muted">Enter Qty to deliver and Sale Rate. Purchase cost is shown for each vendor batch so you can price profitably.</small>
                </div>
                <span class="badge bg-success-subtle text-success border border-success px-3 py-2 fw-bold">
                    <i class="fas fa-boxes me-1"></i> {{ $availableItems->count() }} Batches on Truck
                </span>
            </div>

            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-sale align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 4%;" class="text-center">Select</th>
                                <th style="width: 22%;">Product / Item Description</th>
                                <th style="width: 13%;">Vendor Source</th>
                                <th style="width: 12%;" class="text-end">Pur. Cost (خرید)</th>
                                <th style="width: 11%;" class="text-center">On Truck</th>
                                <th style="width: 12%;" class="text-center">Deliver Qty</th>
                                <th style="width: 13%;" class="text-end">Sale Rate (فروخت)</th>
                                <th style="width: 6%;" class="text-end">Disc.</th>
                                <th style="width: 11%;" class="text-end">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($availableItems as $idx => $tItem)
                                @php
                                    $cost = (float) $tItem->purchase_price;
                                    $defaultSalePrice = (float)($tItem->product->sale_price_per_piece ?: $cost);
                                @endphp
                                <tr class="sale-row" data-row-id="{{ $tItem->id }}" data-purchase-cost="{{ $cost }}">
                                    {{-- Checkbox --}}
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input item-select-check" style="cursor: pointer; transform: scale(1.15);" checked>
                                    </td>

                                    {{-- Product Description --}}
                                    <td>
                                        <div class="fw-bold text-dark">{{ $tItem->product->item_name ?? 'Product #'.$tItem->product_id }}</div>
                                        @if(!empty($tItem->product->item_code))
                                            <span class="text-muted small font-monospace">Code: {{ $tItem->product->item_code }}</span>
                                        @endif
                                        <input type="hidden" name="items[{{ $idx }}][truck_consignment_item_id]" value="{{ $tItem->id }}">
                                    </td>

                                    {{-- Vendor Source --}}
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1">
                                            <i class="fas fa-tag me-1"></i> {{ $tItem->vendor->name ?? 'Vendor' }}
                                        </span>
                                    </td>

                                    {{-- Purchase Cost --}}
                                    <td class="text-end">
                                        <span class="cost-badge">
                                            Rs. {{ number_format($cost, 2) }}
                                        </span>
                                    </td>

                                    {{-- Current Available on Truck --}}
                                    <td class="text-center">
                                        <span class="stock-pill">
                                            {{ (float)$tItem->remaining_qty }} {{ $tItem->unit ?: 'Pcs' }}
                                        </span>
                                    </td>

                                    {{-- Deliver Qty Input --}}
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.001" min="0" max="{{ (float)$tItem->remaining_qty }}" 
                                                   class="form-control text-center fw-bold sell-qty-input" 
                                                   name="items[{{ $idx }}][qty]" 
                                                   value="0" 
                                                   style="height: 36px; border: 1px solid #cbd5e1; border-radius: 6px;">
                                        </div>
                                        <div class="text-center text-muted small mt-1" style="font-size: 0.7rem;">Max: {{ (float)$tItem->remaining_qty }}</div>
                                    </td>

                                    {{-- Sale Price Input + Profit Indicator --}}
                                    <td>
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control form-control-sm text-end fw-bold sell-price-input" 
                                               name="items[{{ $idx }}][price]" 
                                               value="{{ $defaultSalePrice }}" 
                                               style="height: 36px; border: 1px solid #cbd5e1; border-radius: 6px;">
                                        <div class="text-end margin-indicator fw-semibold mt-1" style="font-size: 0.72rem;"></div>
                                    </td>

                                    {{-- Line Discount --}}
                                    <td>
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control form-control-sm text-end sell-disc-input" 
                                               name="items[{{ $idx }}][discount]" 
                                               value="0.00" 
                                               style="height: 36px; border: 1px solid #cbd5e1; border-radius: 6px;">
                                    </td>

                                    {{-- Line Total --}}
                                    <td class="text-end fw-bold font-monospace line-total-display text-dark fs-6">
                                        Rs. 0.00
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- STICKY FLOATING SUMMARY BAR --}}
        <div class="sale-summary-bar">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                {{-- Left stats: Qty, Cost, Gross, Estimated Profit --}}
                <div class="d-flex flex-wrap align-items-center gap-3 gap-md-4">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.72rem;">Deliver Qty</div>
                        <div class="fw-bold fs-5 text-dark font-monospace" id="displayTotalQty">0.00</div>
                    </div>
                    <div class="border-start ps-3">
                        <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.72rem;">Goods Cost (خرید)</div>
                        <div class="fw-bold fs-5 text-muted font-monospace" id="displayTotalCost">Rs. 0.00</div>
                    </div>
                    <div class="border-start ps-3">
                        <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.72rem;">Gross Sale</div>
                        <div class="fw-bold fs-5 text-dark font-monospace" id="displaySubtotal">Rs. 0.00</div>
                    </div>
                    <div class="border-start ps-3">
                        <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.72rem;">Est. Net Profit (منافع)</div>
                        <div class="fw-bold fs-5 font-monospace text-success" id="displayProfit">Rs. 0.00</div>
                    </div>
                </div>

                {{-- Right inputs: Overall Disc, Paid, Net Bill, Submit --}}
                <div class="d-flex flex-wrap align-items-center gap-3 ms-auto">
                    <div class="d-flex align-items-center gap-1">
                        <label class="text-muted small mb-0 fw-semibold">Discount:</label>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end font-monospace" id="inputSaleDiscount" name="discount" value="0.00" style="width: 90px; height: 35px; border: 1px solid #cbd5e1;">
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <label class="text-muted small mb-0 fw-semibold">Paid (وصول):</label>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end fw-bold text-success font-monospace" id="inputSalePaid" name="paid_amount" value="0.00" placeholder="0.00" style="width: 100px; height: 35px; border: 1px solid #cbd5e1;">
                    </div>
                    <div class="border-start ps-3">
                        <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.72rem;">Net Bill (بل)</div>
                        <div class="fw-bold fs-4 text-success font-monospace" id="displayNetBill">Rs. 0.00</div>
                    </div>

                    <div class="d-flex align-items-center gap-2 ps-2">
                        <a href="{{ route('truck-purchase.show', $consignment->id) }}" class="btn btn-outline-secondary px-3">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-success btn-lg px-4 fw-bold shadow" id="btnSubmitTruckSale">
                            <i class="fas fa-check-circle me-1"></i> Save Sale & Deliver
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/vendors/select2/js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('.select2-customer').select2({
        placeholder: '-- Search & Choose Customer --',
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
            Swal.fire('Limit Exceeded', `Truck mein sirf ${max} units mojood hain.`, 'warning');
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
            Swal.fire('No Quantity Entered', 'Kam az kam aik product ki deliver quantity enter karein.', 'warning');
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
