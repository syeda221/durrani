@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .truck-container {
        border-radius: 14px;
        background-color: #ffffff;
        padding: 24px;
        font-size: 0.88rem;
    }
    .header-gradient-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.15);
    }
    .vendor-card {
        border: 2px solid #cbd5e1;
        border-radius: 12px;
        background: #ffffff;
        transition: all 0.2s ease;
        margin-bottom: 24px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.04);
    }
    .vendor-card:hover {
        border-color: #94a3b8;
    }
    .vendor-card-header {
        background: #f1f5f9;
        border-bottom: 2px solid #cbd5e1;
        padding: 14px 20px;
        border-radius: 10px 10px 0 0;
    }
    .vendor-badge {
        font-size: 0.9rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 20px;
        background: #0284c7;
        color: white;
    }
    .table-items th {
        background-color: #f8fafc;
        color: #334155;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 8px;
        border-bottom: 2px solid #cbd5e1;
    }
    .table-items td {
        padding: 6px 8px;
        vertical-align: middle;
    }
    .form-control-sm, .form-select-sm {
        height: 36px;
        font-size: 0.85rem;
        border-radius: 6px;
    }
    .summary-bar {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 24px;
        position: sticky;
        bottom: 15px;
        z-index: 100;
        box-shadow: 0 -4px 16px rgba(0,0,0,0.08);
    }
    .btn-add-vendor {
        border: 2px dashed #0284c7 !important;
        color: #0284c7 !important;
        background: #f0f9ff !important;
        font-weight: 700 !important;
        border-radius: 10px !important;
        padding: 14px !important;
        transition: all 0.2s !important;
        cursor: pointer;
    }
    .btn-add-vendor:hover {
        background: #e0f2fe !important;
        color: #0369a1 !important;
        border-color: #0369a1 !important;
    }
    /* Select2 UI adjustments */
    .select2-container .select2-selection--single {
        height: 36px !important;
        display: flex !important;
        align-items: center !important;
        border: 1px solid #ced4da !important;
        border-radius: 6px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px !important;
    }
</style>

<div class="container-fluid px-3 py-3">
    <form id="truckPurchaseForm" method="POST" action="{{ route('truck-purchase.store') }}">
        @csrf

        {{-- TOP HEADER BANNER --}}
        <div class="header-gradient-card">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-truck-moving fs-3 text-info"></i>
                        <h4 class="mb-0 fw-bold text-white">Truck Purchase (Multi-Vendor Inward Consignment)</h4>
                    </div>
                    <p class="text-slate-300 text-light mb-0 mt-1 small opacity-75">
                        Ek truck mein aane wale mukhtalif vendors ka maal ek hi screen se record karein. Har vendor ka apna bill autogenerate ho kar ledger me enter hoga.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('truck-purchase.index') }}" class="btn btn-outline-light btn-sm px-3">
                        <i class="fas fa-list me-1"></i> All Truck Consignments
                    </a>
                    <span class="badge bg-primary fs-6 px-3 py-2 font-monospace">
                        Ref: {{ $nextConsignmentNo }}
                    </span>
                    <input type="hidden" name="consignment_no" value="{{ $nextConsignmentNo }}">
                </div>
            </div>
        </div>

        {{-- TRUCK & SHIPMENT DETAILS CARD --}}
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-dark rounded-circle p-2"><i class="fas fa-truck"></i></span>
                    <h6 class="mb-0 fw-bold text-dark">Step 1: Truck & Driver Details (ٹرک اور ڈرائیور کی معلومات)</h6>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    {{-- Truck / Vehicle Number --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">
                            <i class="fas fa-truck text-primary me-1"></i> Truck / Vehicle No <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control fw-bold text-uppercase" id="truckNoInput" name="truck_no" list="truckSuggestions" placeholder="e.g. LES-4580, TK-921" required style="height: 38px;">
                            <datalist id="truckSuggestions">
                                @foreach($savedTrucks as $st)
                                    <option value="{{ $st->truck_no }}" data-driver="{{ $st->driver_name }}" data-phone="{{ $st->driver_phone }}" data-transporter="{{ $st->transporter_name }}">
                                        {{ $st->driver_name ? $st->driver_name . ' (' . $st->truck_no . ')' : $st->truck_no }}
                                    </option>
                                @endforeach
                            </datalist>
                        </div>
                        <small class="text-muted" style="font-size: 0.72rem;">Type new or select from suggestions</small>
                    </div>

                    {{-- Driver Name --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">
                            <i class="fas fa-user text-secondary me-1"></i> Driver Name (ڈرائیور کا نام)
                        </label>
                        <input type="text" class="form-control" id="driverNameInput" name="driver_name" placeholder="Driver Full Name" style="height: 38px;">
                    </div>

                    {{-- Driver Phone --}}
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-muted small">
                            <i class="fas fa-phone text-secondary me-1"></i> Driver Mobile / Contact
                        </label>
                        <input type="text" class="form-control" id="driverPhoneInput" name="driver_phone" placeholder="03xx-xxxxxxx" style="height: 38px;">
                    </div>

                    {{-- Transporter / Goods Forwarder --}}
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-muted small">
                            <i class="fas fa-shipping-fast text-secondary me-1"></i> Transporter / Cargo
                        </label>
                        <input type="text" class="form-control" id="transporterInput" name="transporter_name" placeholder="Transport Co." style="height: 38px;">
                    </div>

                    {{-- Bilty Number --}}
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-muted small">
                            <i class="fas fa-file-invoice text-secondary me-1"></i> Bilty No / Tracking #
                        </label>
                        <input type="text" class="form-control" name="bilty_no" placeholder="Bilty #" style="height: 38px;">
                    </div>

                    {{-- Receiving Date --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">
                            <i class="fas fa-calendar-alt text-secondary me-1"></i> Arrival Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" name="arrival_date" value="{{ date('Y-m-d') }}" required style="height: 38px;">
                    </div>

                    {{-- Target Receiving Warehouse --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">
                            <i class="fas fa-warehouse text-secondary me-1"></i> Receiving Warehouse <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" name="warehouse_id" required style="height: 38px;">
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->is_shop ? '🏪 ' : '🏢 ' }}{{ $wh->warehouse_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Total Truck Freight / Kiraya --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">
                            <i class="fas fa-money-bill-wave text-success me-1"></i> Total Truck Kiraya (Freight)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted small">Rs.</span>
                            <input type="number" step="0.01" min="0" class="form-control fw-bold" id="totalTruckFreight" name="total_freight" value="0.00" style="height: 38px;">
                        </div>
                    </div>

                    {{-- General Remarks --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">
                            <i class="fas fa-comment-alt text-secondary me-1"></i> Consignment Remarks / Gate Note
                        </label>
                        <input type="text" class="form-control" name="remarks" placeholder="Any special notes or weight slip..." style="height: 38px;">
                    </div>
                </div>
            </div>
        </div>

        {{-- VENDORS SECTION HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fas fa-boxes-packing text-primary me-2"></i>Step 2: Vendors & Products in this Truck
                </h5>
                <p class="text-muted small mb-0">Har vendor ka alag box hai. Aap ek se zyada vendors aur unke items add kar sakte hain.</p>
            </div>
            <button type="button" class="btn btn-primary px-3 shadow-sm btnAddVendorCard">
                <i class="fas fa-plus-circle me-1"></i> + Add Another Vendor to this Truck
            </button>
        </div>

        {{-- CONTAINER FOR DYNAMIC VENDOR CARDS --}}
        <div id="vendorCardsContainer">
            {{-- Initialized by JS --}}
        </div>

        {{-- ADD VENDOR BUTTON (BIG DASHED) --}}
        <div class="mb-4">
            <button type="button" class="btn w-100 btn-add-vendor btnAddVendorCard shadow-sm">
                <i class="fas fa-plus-circle me-2 fs-5"></i> + Add Another Vendor to this Truck (اگلا وینڈر شامل کریں)
            </button>
        </div>

        {{-- BOTTOM STICKY SUMMARY BAR --}}
        <div class="summary-bar">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-4">
                    <div>
                        <div class="text-muted small text-uppercase">Total Vendors</div>
                        <div class="fw-bold fs-5 text-primary" id="summaryTotalVendors">1</div>
                    </div>
                    <div class="border-start ps-3">
                        <div class="text-muted small text-uppercase">Total Items / Qty</div>
                        <div class="fw-bold fs-5 text-dark" id="summaryTotalQty">0</div>
                    </div>
                    <div class="border-start ps-3">
                        <div class="text-muted small text-uppercase">Merchandise Subtotal</div>
                        <div class="fw-bold fs-5 text-dark font-monospace" id="summarySubtotal">Rs. 0.00</div>
                    </div>
                    <div class="border-start ps-3">
                        <div class="text-muted small text-uppercase">Total Kiraya (Freight)</div>
                        <div class="fw-bold fs-5 text-info font-monospace" id="summaryTotalFreight">Rs. 0.00</div>
                    </div>
                    <div class="border-start ps-3">
                        <div class="text-muted small text-uppercase">Grand Total Net</div>
                        <div class="fw-bold fs-4 text-success font-monospace" id="summaryGrandTotal">Rs. 0.00</div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary px-3" onclick="window.history.back()">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success btn-lg px-4 fw-bold shadow" id="btnSubmitTruckPurchase">
                        <i class="fas fa-check-circle me-2"></i> Save Truck Consignment & Generate Bills
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

{{-- SCRIPT ASSETS (JQUERY FIRST!) --}}
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/vendors/select2/js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Vendor and Product options compiled cleanly from PHP
const globalVendorOptions = `@foreach($vendors as $v)<option value="{{ $v->id }}">{{ addslashes($v->name) }} ({{ $v->phone ?: 'No phone' }})</option>@endforeach`;

const globalProductOptions = `@foreach($products as $p)<option value="{{ $p->id }}" data-price="{{ $p->purchase_price_per_piece ?? 0 }}" data-ppb="{{ $p->pieces_per_box ?? 1 }}" data-unit="{{ $p->unit->name ?? $p->unit_name ?? 'Pcs' }}" data-mode="{{ $p->size_mode }}">{{ $p->item_code ? '[' . $p->item_code . '] ' : '' }}{{ addslashes($p->item_name) }}</option>@endforeach`;

let vendorGlobalCounter = 0;

function createItemRowHtml(vIdx, rIdx) {
    return `
    <tr class="item-row">
        <td>
            <select class="form-select form-select-sm select2-product item-product-select" name="vendors[${vIdx}][items][${rIdx}][product_id]" required>
                <option value="" selected disabled>Search Product...</option>
                ${globalProductOptions}
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm item-unit text-center" name="vendors[${vIdx}][items][${rIdx}][unit]" value="Pcs" readonly>
        </td>
        <td>
            <div class="d-flex gap-1">
                <input type="number" step="1" min="0" class="form-control form-control-sm item-boxes text-center" name="vendors[${vIdx}][items][${rIdx}][boxes_qty]" placeholder="Box" title="Cartons/Boxes">
                <input type="number" step="1" min="0" class="form-control form-control-sm item-loose text-center" name="vendors[${vIdx}][items][${rIdx}][loose_qty]" placeholder="Loose" title="Loose Pieces">
            </div>
        </td>
        <td>
            <input type="number" step="0.001" min="0.001" class="form-control form-control-sm item-qty text-center fw-bold" name="vendors[${vIdx}][items][${rIdx}][qty]" value="1" required>
        </td>
        <td>
            <input type="number" step="0.01" min="0" class="form-control form-control-sm item-price text-end fw-semibold" name="vendors[${vIdx}][items][${rIdx}][price]" value="0.00" required>
        </td>
        <td>
            <input type="number" step="0.01" min="0" class="form-control form-control-sm item-disc text-end" name="vendors[${vIdx}][items][${rIdx}][item_discount]" value="0.00">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm item-line-total text-end fw-bold bg-light" readonly value="0.00">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-outline-danger btn-sm p-1 px-2 btnRemoveItemRow" title="Delete Row">
                <i class="fas fa-times"></i>
            </button>
        </td>
    </tr>`;
}

function createVendorCardHtml(vIdx, vNum) {
    return `
    <div class="vendor-card" data-vendor-index="${vIdx}">
        <div class="vendor-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="vendor-badge"><i class="fas fa-user-tag me-1"></i> Vendor #${vNum}</span>
                <div style="min-width: 280px;">
                    <select class="form-select select2-vendor" name="vendors[${vIdx}][vendor_id]" required>
                        <option value="" selected disabled>-- Select Vendor --</option>
                        ${globalVendorOptions}
                    </select>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <span class="input-group-text bg-white text-muted">Vendor Inv#</span>
                    <input type="text" class="form-control" name="vendors[${vIdx}][vendor_bill_no]" placeholder="Manual Ref">
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm btnRemoveVendor" title="Remove this vendor card" ${vNum === 1 ? 'style="display:none;"' : ''}>
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>

        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-items align-middle mb-2">
                    <thead>
                        <tr>
                            <th style="width: 35%;">Product / Item</th>
                            <th style="width: 12%;">Unit</th>
                            <th style="width: 12%;">Boxes/Loose</th>
                            <th style="width: 12%;">Qty</th>
                            <th style="width: 12%;">Purchase Rate</th>
                            <th style="width: 10%;">Discount</th>
                            <th style="width: 12%;">Line Total</th>
                            <th style="width: 5%; text-align: center;"><i class="fas fa-cog"></i></th>
                        </tr>
                    </thead>
                    <tbody class="vendor-items-tbody">
                        ${createItemRowHtml(vIdx, 0)}
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2">
                <button type="button" class="btn btn-sm btn-outline-primary btnAddItemRow">
                    <i class="fas fa-plus me-1"></i> Add Product Row
                </button>

                <div class="d-flex align-items-center gap-3 bg-light p-2 px-3 rounded border">
                    <div>
                        <span class="text-muted small">Items Total:</span>
                        <span class="fw-bold text-dark font-monospace vendor-subtotal ms-1">Rs. 0.00</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="text-muted small">Vendor Disc:</span>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm vendor-bill-discount text-end" name="vendors[${vIdx}][discount]" value="0.00" style="width: 90px;">
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="text-muted small">Kiraya Share:</span>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm vendor-freight-share text-end" name="vendors[${vIdx}][freight_share]" value="0.00" style="width: 90px;">
                    </div>
                    <div>
                        <span class="text-muted small">Net Bill:</span>
                        <span class="fw-bold text-success font-monospace vendor-net ms-1 fs-6">Rs. 0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>`;
}

$(document).ready(function() {
    // 1. Initialize first vendor card
    addNewVendor();

    function initSelect2(context) {
        context.find('.select2-vendor').select2({
            placeholder: '-- Select Vendor --',
            width: '100%'
        });

        context.find('.select2-product').select2({
            placeholder: 'Search Product...',
            width: '100%'
        });
    }

    function addNewVendor() {
        const vNum = $('.vendor-card').length + 1;
        const vIdx = vendorGlobalCounter++;
        const cardHtml = createVendorCardHtml(vIdx, vNum);
        const $card = $(cardHtml);

        $('#vendorCardsContainer').append($card);
        initSelect2($card);

        if ($('.vendor-card').length > 1) {
            $('.btnRemoveVendor').show();
        }

        distributeFreightEqually();
        calculateAllTotals();
        return $card;
    }

    // Add Vendor Card on Button Click (Delegated)
    $(document).on('click', '.btnAddVendorCard', function(e) {
        e.preventDefault();
        const $card = addNewVendor();
        $('html, body').animate({
            scrollTop: $card.offset().top - 80
        }, 300);
    });

    // Remove Vendor Card
    $(document).on('click', '.btnRemoveVendor', function(e) {
        e.preventDefault();
        if ($('.vendor-card').length > 1) {
            $(this).closest('.vendor-card').remove();
            // Re-index vendor badge numbers
            $('.vendor-card').each(function(i) {
                $(this).find('.vendor-badge').html(`<i class="fas fa-user-tag me-1"></i> Vendor #${i + 1}`);
            });
            if ($('.vendor-card').length === 1) {
                $('.btnRemoveVendor').hide();
            }
            distributeFreightEqually();
            calculateAllTotals();
        }
    });

    // Add Item Row (Delegated)
    $(document).on('click', '.btnAddItemRow', function(e) {
        e.preventDefault();
        const $card = $(this).closest('.vendor-card');
        const vIdx = $card.data('vendor-index');
        const $tbody = $card.find('.vendor-items-tbody');
        const rIdx = $tbody.find('.item-row').length;

        const rowHtml = createItemRowHtml(vIdx, rIdx);
        const $newRow = $(rowHtml);
        $tbody.append($newRow);

        $newRow.find('.select2-product').select2({
            placeholder: 'Search Product...',
            width: '100%'
        });

        calculateVendorTotals($card);
    });

    // Remove Item Row
    $(document).on('click', '.btnRemoveItemRow', function(e) {
        e.preventDefault();
        const $tbody = $(this).closest('.vendor-items-tbody');
        const $card = $(this).closest('.vendor-card');
        if ($tbody.find('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            calculateVendorTotals($card);
        } else {
            Swal.fire('Info', 'At least one item row is required for this vendor.', 'info');
        }
    });

    // Auto-fill Truck Details when selected from suggestions
    $('#truckNoInput').on('input change', function() {
        const val = $(this).val().trim().toUpperCase();
        const opt = $('#truckSuggestions option').filter(function() {
            return $(this).val().toUpperCase() === val;
        });

        if (opt.length > 0) {
            const driver = opt.data('driver');
            const phone = opt.data('phone');
            const transporter = opt.data('transporter');

            if (driver) $('#driverNameInput').val(driver);
            if (phone) $('#driverPhoneInput').val(phone);
            if (transporter) $('#transporterInput').val(transporter);
        }
    });

    // Auto-fill product price, unit and ppb when product selected
    $(document).on('change', '.item-product-select', function() {
        const row = $(this).closest('.item-row');
        const selected = $(this).find(':selected');

        const price = parseFloat(selected.data('price')) || 0;
        const unit = selected.data('unit') || 'Pcs';

        row.find('.item-price').val(price.toFixed(2));
        row.find('.item-unit').val(unit);

        calculateLineTotal(row);
    });

    // Carton / Loose Qty calculation
    $(document).on('input', '.item-boxes, .item-loose', function() {
        const row = $(this).closest('.item-row');
        const selected = row.find('.item-product-select :selected');
        const ppb = parseFloat(selected.data('ppb')) || 1;

        const boxes = parseInt(row.find('.item-boxes').val()) || 0;
        const loose = parseInt(row.find('.item-loose').val()) || 0;

        if (boxes > 0 || loose > 0) {
            const totalCartons = boxes + (loose / ppb);
            row.find('.item-qty').val(totalCartons.toFixed(3));
        }

        calculateLineTotal(row);
    });

    // Calculation triggers
    $(document).on('input', '.item-qty, .item-price, .item-disc', function() {
        const row = $(this).closest('.item-row');
        calculateLineTotal(row);
    });

    $(document).on('input', '.vendor-bill-discount, .vendor-freight-share', function() {
        const card = $(this).closest('.vendor-card');
        calculateVendorTotals(card);
    });

    $('#totalTruckFreight').on('input', function() {
        distributeFreightEqually();
        calculateAllTotals();
    });

    function calculateLineTotal(row) {
        const qty = parseFloat(row.find('.item-qty').val()) || 0;
        const price = parseFloat(row.find('.item-price').val()) || 0;
        const disc = parseFloat(row.find('.item-disc').val()) || 0;

        const total = Math.max(0, (qty * price) - disc);
        row.find('.item-line-total').val(total.toFixed(2));

        const card = row.closest('.vendor-card');
        calculateVendorTotals(card);
    }

    function calculateVendorTotals(card) {
        let subtotal = 0;
        card.find('.item-line-total').each(function() {
            subtotal += parseFloat($(this).val()) || 0;
        });

        const discount = parseFloat(card.find('.vendor-bill-discount').val()) || 0;
        const freight = parseFloat(card.find('.vendor-freight-share').val()) || 0;

        const net = Math.max(0, subtotal - discount + freight);

        card.find('.vendor-subtotal').text('Rs. ' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        card.find('.vendor-net').text('Rs. ' + net.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        calculateAllTotals();
    }

    function distributeFreightEqually() {
        const totalFreight = parseFloat($('#totalTruckFreight').val()) || 0;
        const cards = $('.vendor-card');
        if (cards.length > 0 && totalFreight > 0) {
            const share = (totalFreight / cards.length).toFixed(2);
            cards.each(function() {
                $(this).find('.vendor-freight-share').val(share);
                calculateVendorTotals($(this));
            });
        }
    }

    function calculateAllTotals() {
        let grandSubtotal = 0;
        let grandQty = 0;
        let totalFreight = parseFloat($('#totalTruckFreight').val()) || 0;
        let vendorCount = $('.vendor-card').length;

        $('.vendor-card').each(function() {
            $(this).find('.item-row').each(function() {
                grandQty += parseFloat($(this).find('.item-qty').val()) || 0;
                grandSubtotal += parseFloat($(this).find('.item-line-total').val()) || 0;
            });
        });

        let grandNet = 0;
        $('.vendor-card').each(function() {
            const disc = parseFloat($(this).find('.vendor-bill-discount').val()) || 0;
            const fr = parseFloat($(this).find('.vendor-freight-share').val()) || 0;
            let sub = 0;
            $(this).find('.item-line-total').each(function() { sub += parseFloat($(this).val()) || 0; });
            grandNet += Math.max(0, sub - disc + fr);
        });

        $('#summaryTotalVendors').text(vendorCount);
        $('#summaryTotalQty').text(grandQty.toFixed(2));
        $('#summarySubtotal').text('Rs. ' + grandSubtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#summaryTotalFreight').text('Rs. ' + totalFreight.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#summaryGrandTotal').text('Rs. ' + grandNet.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }

    // AJAX Form Submission
    $('#truckPurchaseForm').on('submit', function(e) {
        e.preventDefault();

        // Validation check
        let hasErrors = false;
        $('.vendor-card').each(function(idx) {
            const vId = $(this).find('.select2-vendor').val();
            if (!vId) {
                Swal.fire('Vendor Missing', `Please select vendor for Vendor #${idx + 1}`, 'warning');
                hasErrors = true;
                return false;
            }
            let rowCount = 0;
            $(this).find('.item-row').each(function() {
                const pId = $(this).find('.item-product-select').val();
                if (pId) rowCount++;
            });
            if (rowCount === 0) {
                Swal.fire('Items Missing', `Please select at least one product for Vendor #${idx + 1}`, 'warning');
                hasErrors = true;
                return false;
            }
        });

        if (hasErrors) return;

        const btn = $('#btnSubmitTruckPurchase');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Saving Purchases & Stock...');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: res.message,
                        confirmButtonText: 'View Consignment'
                    }).then(() => {
                        window.location.href = res.redirect_url;
                    });
                } else {
                    Swal.fire('Error', res.message || 'Error occurred while saving.', 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-check-circle me-2"></i> Save Truck Consignment & Generate Bills');
                }
            },
            error: function(xhr) {
                let msg = 'Error saving truck purchase.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire('Error', msg, 'error');
                btn.prop('disabled', false).html('<i class="fas fa-check-circle me-2"></i> Save Truck Consignment & Generate Bills');
            }
        });
    });
});
</script>
@endsection
