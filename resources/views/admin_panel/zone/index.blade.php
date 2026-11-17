@extends('admin_panel.layout.app')

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="mb-0 text-dark font-weight-bold">Zones & Regions</h4>
                                <small class="text-muted">Manage sales regions and distribution zones</small>
                            </div>
                            @can('zones.create')
                                <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#createModal" data-bs-toggle="modal" data-bs-target="#createModal" id="btnOpenCreate">
                                    <i class="fa fa-plus-circle mr-1"></i> Add New Zone
                                </button>
                            @endcan
                        </div>

                        <div class="card border-0 shadow-sm rounded-lg" style="background-color: white;">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="default-datatable" class="table table-hover table-striped align-middle mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 80px;" class="text-center">ID</th>
                                                <th>Zone / Region Name</th>
                                                <th style="width: 140px;" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($zones as $zone)
                                                <tr id="row-{{ $zone->id }}">
                                                    <td class="text-center font-weight-bold text-muted">#{{ $zone->id }}</td>
                                                    <td class="font-weight-bold text-dark zone-title">{{ $zone->zone }}</td>
                                                    <td class="text-center">
                                                        <div class="d-inline-flex gap-1">
                                                            @can('zones.edit')
                                                                <button type="button" class="btn btn-sm btn-outline-primary edit-btn" data-id="{{ $zone->id }}" title="Edit Zone">
                                                                    <i class="fa fa-pencil"></i>
                                                                </button>
                                                            @endcan
                                                            @can('zones.delete')
                                                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $zone->id }}" data-url="{{ route('zone.delete', $zone->id) }}" title="Delete Zone">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CREATE MODAL -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="myform" id="createZoneForm" action="{{ route('zone.store') }}" method="POST">
                @csrf
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold text-white" id="createModalLabel">
                            <i class="fa fa-map-marker mr-1"></i> Add New Zone
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark mb-1">Zone / Region Name <span class="text-danger">*</span></label>
                            <input type="text" name="zone" id="create_zone_input" class="form-control form-control-lg" placeholder="e.g. Lahore Central, Peshawar, etc." required autofocus autocomplete="off" />
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                        @can('zones.create')
                            <button type="submit" class="btn btn-primary px-4 save-btn">
                                <i class="fa fa-save mr-1"></i> Save Zone
                            </button>
                        @endcan
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="editform" id="editZoneForm" action="{{ route('zone.store') }}" method="POST">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id" value="" />
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title font-weight-bold text-white" id="editModalLabel">
                            <i class="fa fa-pencil-square-o mr-1"></i> Edit Zone
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark mb-1">Zone / Region Name <span class="text-danger">*</span></label>
                            <input type="text" name="zone" class="form-control form-control-lg" id="edit_zone" required autocomplete="off" />
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                        @can('zones.edit')
                            <button type="submit" class="btn btn-primary px-4 save-btn">
                                <i class="fa fa-check mr-1"></i> Update Zone
                            </button>
                        @endcan
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable safely
        if ($.fn.DataTable && $('#default-datatable').length) {
            $('#default-datatable').DataTable({
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                order: [[0, 'desc']]
            });
        }

        // Reset and autofocus create modal
        $('#createModal').on('shown.bs.modal', function () {
            $('#create_zone_input').val('').focus();
        });

        // CREATE FORM AJAX SUBMIT
        $('#createZoneForm').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            var $submitBtn = $(form).find('.save-btn');
            var origBtnHtml = $submitBtn.html();

            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving...');

            var formData = new FormData(form);
            $.ajax({
                type: 'POST',
                url: $(form).attr('action'),
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(res) {
                    $('#createModal').modal('hide');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Success!',
                            text: res.message || 'Zone created successfully.',
                            icon: 'success',
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        location.reload();
                    }
                },
                error: function(xhr) {
                    $submitBtn.prop('disabled', false).html(origBtnHtml);
                    var errMsg = 'Unable to create zone. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Error!',
                            html: errMsg,
                            icon: 'error'
                        });
                    } else {
                        alert(errMsg);
                    }
                }
            });
        });

        // EDIT MODAL DATA LOAD
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            var $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                type: 'GET',
                url: "{{ url('zones/edit') }}/" + id,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(res) {
                    $btn.prop('disabled', false);
                    $('#edit_id').val(res.id);
                    $('#edit_zone').val(res.zone);
                    $('#editModal').modal('show');
                },
                error: function(xhr) {
                    $btn.prop('disabled', false);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', 'Failed to fetch zone details.', 'error');
                    } else {
                        alert('Failed to fetch zone details.');
                    }
                }
            });
        });

        // EDIT FORM AJAX SUBMIT
        $('#editZoneForm').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            var $submitBtn = $(form).find('.save-btn');
            var origBtnHtml = $submitBtn.html();

            $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Updating...');

            var formData = new FormData(form);
            $.ajax({
                type: 'POST',
                url: $(form).attr('action'),
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(res) {
                    $('#editModal').modal('hide');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Updated!',
                            text: res.message || 'Zone updated successfully.',
                            icon: 'success',
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        location.reload();
                    }
                },
                error: function(xhr) {
                    $submitBtn.prop('disabled', false).html(origBtnHtml);
                    var errMsg = 'Unable to update zone. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Error!',
                            html: errMsg,
                            icon: 'error'
                        });
                    } else {
                        alert(errMsg);
                    }
                }
            });
        });

        // DELETE HANDLER
        $(document).on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            var url = $(this).data('url');

            var doDelete = function() {
                $.ajax({
                    type: 'GET',
                    url: url,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function(res) {
                        $('#row-' + id).fadeOut(300, function() {
                            $(this).remove();
                        });
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Deleted!',
                                text: res.message || 'Zone has been deleted.',
                                icon: 'success',
                                timer: 1200,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        var errMsg = 'Unable to delete zone.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', errMsg, 'error');
                        } else {
                            alert(errMsg);
                        }
                    }
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to delete this zone?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        doDelete();
                    }
                });
            } else {
                if (confirm('Are you sure you want to delete this zone?')) {
                    doDelete();
                }
            }
        });
    });
</script>
@endpush
