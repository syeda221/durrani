@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Premium ERP Form Styling */
    .truck-create-wrapper {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1e293b;
    }

    /* Top Hero Banner */
    .hero-header-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        border-radius: 12px;
        padding: 22px 28px;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.15);
        position: relative;
        overflow: hidden;
    }
    .hero-header-banner::after {
        content: '\f0d1';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: 25px;
        bottom: -20px;
        font-size: 8rem;
        opacity: 0.05;
        color: #ffffff;
        pointer-events: none;
    }

    /* Section Cards */
    .erp-section-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .erp-section-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 20px;
    }

    /* Form Inputs */
    .erp-input-group .form-control,
    .erp-input-group .form-select,
    .form-control-erp {
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        font-size: 0.86rem;
        font-weight: 500;
        color: #1e293b;
        height: 38px;
        transition: all 0.2s;
    }
    .form-control-erp:focus,
    .erp-input-group .form-control:focus,
    .erp-input-group .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }
    .erp-input-group .input-group-text {
        background-color: #f1f5f9;
        border: 1px solid #cbd5e1;
        color: #64748b;
        font-size: 0.85rem;
    }

    /* Vendor Card */
    .vendor-card {
        border: 2px solid #cbd5e1;
        border-radius: 12px;
        background: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.03);
        transition: all 0.2s ease;
    }
    .vendor-card:hover {
        border-color: #94a3b8;
    }
    .vendor-card-header {
        background: #f1f5f9;
        border-bottom: 2px solid #cbd5e1;
        padding: 12px 18px;
        border-radius: 10px 10px 0 0;
    }
    .vendor-badge {
        font-size: 0.85rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 20px;
        background: #2563eb;
        color: white;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Item Table inside Vendor Card */
    .table-items th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 10px;
        border-bottom: 2px solid #cbd5e1;
    }
    .table-items td {
        padding: 7px 8px;
        vertical-align: middle;
    }
    .table-items .form-control-sm,
    .table-items .form-select-sm {
        height: 35px;
        font-size: 0.84rem;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
    }
    .table-items .form-control-sm:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
    }

    /* Add Vendor Button (Big Dashed) */
    .btn-add-vendor-dashed {
        border: 2px dashed #0284c7 !important;
        color: #0284c7 !important;
        background: #f0f9ff !important;
        font-weight: 700 !important;
        border-radius: 12px !important;
        padding: 16px !important;
        transition: all 0.2s !important;
        cursor: pointer;
        display: block;
        width: 100%;
        text-align: center;
    }
    .btn-add-vendor-dashed:hover {
        background: #e0f2fe !important;
        color: #0369a1 !important;
        border-color: #0369a1 !important;
        transform: translateY(-1px);
    }

    /* Floating Sticky Summary Bar */
    .summary-bar {
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(10px);
        border: 2px solid #cbd5e1;
        border-radius: 12px;
        padding: 14px 22px;
        position: sticky;
        bottom: 12px;
        z-index: 1000;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    /* Select2 Tweaks */
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
    .select2-dropdown {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important;
        z-index: 99999 !important;
    }
</style>

<div class="truck-create-wrapper container-fluid px-3 py-3">
    <form id="truckPurchaseForm" method="POST" action="{{ route('truck-purchase.store') }}">
        @csrf

        {{-- HERO HEADER BANNER --}}
        <div class="hero-header-banner">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary px-3 py-1 font-monospace fs-6">
                            Consignment # {{ $nextConsignmentNo }}
                        </span>
                        <span class="badge bg-info-subtle text-info border border-info px-2 py-1 small">
                            Multi-Vendor Inward
                        </span>
                    </div>
                    <h3 class="fw-bold text-white mt-2 mb-1">
                        <i class="fas fa-truck-moving text-info me-2"></i> Truck Purchase & Inward Receiving
                    </h3>
                    <p class="text-light opacity-75 mb-0 small" style="max-width: 800px;">
                        Ek hi truck mein mukhtalif vendors ka maal ek screen se record karein. Har vendor ka separate purchase bill autogenerate ho kar ledger mein credit hoga aur stock truck consignment inventory mein balance hoga.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('truck-purchase.index') }}" class="btn btn-outline-light btn-sm px-3 fw-semibold">
                        <i class="fas fa-list me-1"></i> All Truck Purchases
                    </a>
                    <input type="hidden" name="consignment_no" value="{{ $nextConsignmentNo }}">
                </div>
            </div>
        </div>

        {{-- STEP 1: TRUCK & LOGISTICS DETAILS --}}
        <div class="erp-section-card mb-4">
            <div class="erp-section-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-dark rounded-circle p-2"><i class="fas fa-truck"></i></span>
                    <h6 class="mb-0 fw-bold text-dark">Step 1: Truck & Logistics Information (ٹرک، ڈرائیور اور ٹرانسپورٹ کی معلومات)</h6>
                </div>
                <span class="text-muted small">* Required fields marked with red asterisk</span>
            </div>
            <div class="p-3 p-md-4">
                <div class="row g-3">
                    {{-- Truck / Vehicle Number --}}
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-bold text-dark small mb-1">
                            <i class="fas fa-truck text-primary me-1"></i> Truck / Vehicle No <span class="text-danger">*</span>
                        </label>
                        <div class="input-group erp-input-group">
                            <span class="input-group-text"><i class="fas fa-car-side"></i></span>
                            <input type="text" class="form-control text-uppercase fw-bold font-monospace" id="truckNoInput" name="truck_no" list="truckSuggestions" placeholder="e.g. LES-4580, TK-921" required autofocus>
                        </div>
                        <datalist id="truckSuggestions">
                            @foreach($savedTrucks as $st)
                                <option value="{{ $st->truck_no }}" data-driver="{{ $st->driver_name }}" data-phone="{{ $st->driver_phone }}" data-transporter="{{ $st->transporter_name }}">
                                    {{ $st->driver_name ? $st->driver_name . ' (' . $st->truck_no . ')' : $st->truck_no }}
                                </option>
                            @endforeach
                        </datalist>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Type new or select past truck from list</div>
                    </div>

                    {{-- Driver Name --}}
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-bold text-dark small mb-1">
                            <i class="fas fa-user text-secondary me-1"></i> Driver Name (ڈرائیور کا نام)
                        </label>
                        <div class="input-group erp-input-group">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            <input type="text" class="form-control" id="driverNameInput" name="driver_name" placeholder="Driver Full Name">
                        </div>
                    </div>

                    {{-- Driver Phone --}}
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label fw-bold text-dark small mb-1">
                            <i class="fas fa-phone text-secondary me-1"></i> Driver Contact #
                        </label>
                        <div class="input-group erp-input-group">
                            <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                            <input type="text" class="form-control" id="driverPhoneInput" name="driver_phone" placeholder="03xx-xxxxxxx">
                        </div>
                    </div>

                    {{-- Transporter / Cargo Company --}}
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label fw-bold text-dark small mb-1">
                            <i class="fas fa-shipping-fast text-secondary me-1"></i> Transporter / Cargo
                        </label>
                        <div class="input-group erp-input-group">
                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                            <input type="text" class="form-control" id="transporterInput" name="transporter_name" placeholder="e.g. Bilal Cargo, Direct">
                        </div>
                    </div>

                    {{-- Bilty Number --}}
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label fw-bold text-dark small mb-1">
                            <i class="fas fa-file-invoice text-secondary me-1"></i> Bilty / Tracking #
                        </label>
                        <div class="input-group erp-input-group">
                            <span class="input-group-text"><i class="fas fa-receipt"></i></span>
                            <input type="text" class="form-control font-monospace" name="bilty_no" placeholder="Bilty #">
                        </div>
                    </div>

                    {{-- Receiving Date --}}
                    <div class="col-lg-3 col-md-4">
                        <label class="form-label fw-bold text-dark small mb-1">
                            <i class="fas fa-calendar-alt text-secondary me-1"></i> Arrival Date <span class="text-danger">*</span>
                        </label>
                        <div class="input-group erp-input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                            <input type="date" class="form-control" name="arrival_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    {{-- Target Receiving Warehouse --}}
                    <div class="col-lg-3 col-md-4">
                        <label class="form-label fw-bold text-dark small mb-1">
                            <i class="fas fa-warehouse text-secondary me-1"></i> Default Receiving Warehouse <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-control-erp" name="warehouse_id" required>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->is_shop ? '🏪 ' : '🏢 ' }}{{ $wh->warehouse_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Total Truck Freight / Kiraya --}}
                    <div class="col-lg-3 col-md-4">
                        <label class="form-label fw-bold text-dark small mb-1">
                            <i class="fas fa-money-bill-wave text-success me-1"></i> Total Truck Freight (کرایہ)
                        </label>
                        <div class="input-group erp-input-group">
                            <span class="input-group-text fw-bold text-success">Rs.</span>
                            <input type="number" step="0.01" min="0" class="form-control fw-bold font-monospace" id="totalTruckFreight" name="total_freight" value="0.00">
                        </div>
                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">Automatically distributed among vendors</div>
                    </div>

                    {{-- Consignment Remarks / Gate Note --}}
                    <div class="col-lg-3 col-md-12">
                        <label class="form-label fw-bold text-dark small mb-1">
                            <i class="fas fa-comment-alt text-secondary me-1"></i> Remarks / Gate Note
                        </label>
                        <input type="text" class="form-control form-control-erp" name="remarks" placeholder="Any special notes or weight slip details...">
                    </div>
                </div>
            </div>
        </div>

        {{-- STEP 2: VENDORS & PRODUCTS SECTION HEADER --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fas fa-boxes-packing text-primary me-2"></i>Step 2: Vendors & Products in this Truck
                </h5>
                <p class="text-muted small mb-0">Har vendor ka alag box banayein. Unke items, rate, boxes/loose enter karein. Bill auto-generate hoga.</p>
            </div>
            <button type="button" class="btn btn-primary px-3 shadow-sm btnAddVendorCard">
                <i class="fas fa-plus-circle me-1"></i> + Add Another Vendor
            </button>
        </div>

        {{-- DYNAMIC VENDOR CARDS CONTAINER --}}
        <div id="vendorCardsContainer">
            {{-- Initialized dynamically by JS --}}
        </div>

        {{-- BIG DASHED BUTTON TO ADD NEXT VENDOR --}}
        <div class="mb-4">
            <button type="button" class="btn btn-add-vendor-dashed btnAddVendorCard shadow-sm">
                <i class="fas fa-plus-circle me-2 fs-5"></i> + Add Another Vendor to this Truck (اگلا وینڈر شامل کریں)
            </button>
        </div>

        {{-- STICKY FLOATING SUMMARY BAR --}}
        <div class="summary-bar">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex flex-wrap align-items-center gap-3 gap-md-4">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.72rem;">Total Vendors</div>
                        <div class="fw-bold fs-5 text-primary" id="summaryTotalVendors">1</div>
                    </div>
                    <div class="border-start ps-3">
                        <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.72rem;">Total Loaded Qty</div>
                        <div class="fw-bold fs-5 text-dark font-monospace" id="summaryTotalQty">0.00</div>
                    </div>
                    <div class="border-start ps-3">
                        <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.72rem;">Items Subtotal</div>
                        <div class="fw-bold fs-5 text-dark font-monospace" id="summarySubtotal">Rs. 0.00</div>
                    </div>
                    <div class="border-start ps-3">
                        <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.72rem;">Total Kiraya (Freight)</div>
                        <div class="fw-bold fs-5 text-info font-monospace" id="summaryTotalFreight">Rs. 0.00</div>
                    </div>
                    <div class="border-start ps-3">
                        <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.72rem;">Grand Total Net</div>
                        <div class="fw-bold fs-4 text-success font-monospace" id="summaryGrandTotal">Rs. 0.00</div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 ms-auto">
                    <a href="{{ route('truck-purchase.index') }}" class="btn btn-outline-secondary px-3">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-success btn-lg px-4 fw-bold shadow" id="btnSubmitTruckPurchase">
                        <i class="fas fa-check-circle me-2"></i> Save Consignment & Create Bills
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

{{-- SCRIPT ASSETS (JQUERY FIRST) --}}
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
            <input type="text" class="form-control form-control-sm item-unit text-center bg-light" name="vendors[${vIdx}][items][${rIdx}][unit]" value="Pcs" readonly>
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
            <input type="text" class="form-control form-control-sm item-line-total text-end fw-bold bg-light font-monospace" readonly value="0.00">
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
            <div class="d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0">
                <span class="vendor-badge"><i class="fas fa-user-tag"></i> Vendor #${vNum}</span>
                <div style="min-width: 280px;" class="flex-grow-1">
                    <select class="form-select select2-vendor" name="vendors[${vIdx}][vendor_id]" required>
                        <option value="" selected disabled>-- Select Vendor --</option>
                        ${globalVendorOptions}
                    </select>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="width: 220px;">
                    <span class="input-group-text bg-white text-muted">Vendor Inv#</span>
                    <input type="text" class="form-control" name="vendors[${vIdx}][vendor_bill_no]" placeholder="Manual Ref / Slip">
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm btnRemoveVendor" title="Remove this vendor card" ${vNum === 1 ? 'style="display:none;"' : ''}>
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>

        <div class="p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-items align-middle mb-2">
                    <thead>
                        <tr>
                            <th style="width: 34%;">Product / Item</th>
                            <th style="width: 10%;" class="text-center">Unit</th>
                            <th style="width: 14%;" class="text-center">Boxes / Loose</th>
                            <th style="width: 11%;" class="text-center">Total Qty</th>
                            <th style="width: 13%;" class="text-end">Pur. Rate (خرید)</th>
                            <th style="width: 9%;" class="text-end">Discount</th>
                            <th style="width: 13%;" class="text-end">Line Total</th>
                            <th style="width: 4%; text-align: center;"><i class="fas fa-cog"></i></th>
                        </tr>
                    </thead>
                    <tbody class="vendor-items-tbody">
                        ${createItemRowHtml(vIdx, 0)}
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-2">
                <button type="button" class="btn btn-sm btn-outline-primary btnAddItemRow fw-semibold">
                    <i class="fas fa-plus me-1"></i> Add Product Row
                </button>

                <div class="d-flex flex-wrap align-items-center gap-3 bg-light p-2 px-3 rounded border">
                    <div>
                        <span class="text-muted small">Items Total:</span>
                        <span class="fw-bold text-dark font-monospace vendor-subtotal ms-1">Rs. 0.00</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="text-muted small">Vendor Disc:</span>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm vendor-bill-discount text-end" name="vendors[${vIdx}][discount]" value="0.00" style="width: 85px;">
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="text-muted small">Kiraya Share:</span>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm vendor-freight-share text-end" name="vendors[${vIdx}][freight_share]" value="0.00" style="width: 85px;">
                    </div>
                    <div class="border-start ps-2">
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
            Swal.fire('Info', 'Har vendor card mein kam az kam aik product lazmi hai.', 'info');
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
                    btn.prop('disabled', false).html('<i class="fas fa-check-circle me-2"></i> Save Consignment & Create Bills');
                }
            },
            error: function(xhr) {
                let msg = 'Error saving truck purchase.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire('Error', msg, 'error');
                btn.prop('disabled', false).html('<i class="fas fa-check-circle me-2"></i> Save Consignment & Create Bills');
            }
        });
    });
});
</script>
@endsection
