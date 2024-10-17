<script type="text/javascript">
    $("form").submit(function(e) {
        e.preventDefault();
        if (e.currentTarget.id == 'form-create') {
            if ($("input[name='asset_item_check[]']").val() === undefined) {
                sweetAlertWarning('Please Complete The Record!');
            } else {
                Swal.fire({
                    title: 'Are You Sure Want To Save Record?',
                    icon: 'question',
                    showCancelButton: true,
                    allowOutsideClick: false,
                    customClass: {
                        confirmButton: 'btn btn-primary mr-2 mb-3',
                        cancelButton: 'btn btn-danger mb-3',
                    },
                    buttonsStyling: false,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        sweetAlertProcess();
                        $('form').unbind('submit').submit();
                    }
                })
            }
        } else {
            Swal.fire({
                title: 'Are You Sure Want To Save Record?',
                icon: 'question',
                showCancelButton: true,
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'btn btn-primary mr-2 mb-3',
                    cancelButton: 'btn btn-danger mb-3',
                },
                buttonsStyling: false,
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    sweetAlertProcess();
                    $('#' + e.currentTarget.id).unbind('submit').submit();
                }
            })
        }
    });

    function dataTable() {
        const url = $('#url_dt').val();
        $('#dt-submission').DataTable({
            autoWidth: false,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: url,
                error: function(xhr, error, code) {
                    sweetAlertError(xhr.statusText);
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    width: '5%',
                    searchable: false
                },
                {
                    data: 'created_at',
                    defaultContent: '-',
                },
                {
                    data: 'type',
                    defaultContent: '-',
                },
                {
                    data: 'description',
                    defaultContent: '-',
                },
                {
                    data: 'status',
                    defaultContent: '-',
                },
                {
                    data: 'created_by',
                    defaultContent: '-',
                },
                {
                    data: 'action',
                    width: '24%',
                    defaultContent: '-',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    }

    function dataTableStaff() {
        const url = $('#url_dt').val();
        $('#dt-submission').DataTable({
            autoWidth: false,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: url,
                error: function(xhr, error, code) {
                    sweetAlertError(xhr.statusText);
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    width: '5%',
                    searchable: false
                },
                {
                    data: 'created_at',
                    defaultContent: '-',
                },
                {
                    data: 'type',
                    defaultContent: '-',
                },

                {
                    data: 'description',
                    defaultContent: '-',
                },
                {
                    data: 'status',
                    defaultContent: '-',
                },
                {
                    data: 'action',
                    width: '24%',
                    defaultContent: '-',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    }

    function addPhysicalAsset() {
        let asset = $('#asset_id').val();
        let assetName = $('#asset_id option:selected').text();
        let category = $('#category').val();
        let status = $('#status').val();
        let barcode = $('#barcode').val();
        let index = $("#asset tbody tr").length - 1;

        if (asset != '' && category != '' && status != '' && barcode != '') {

            let form_asset = $("#form_asset");
            let tr = $("<tr id='asset_tr_" + asset + "'></tr>");
            let td_asset = $("<td>" +
                "<input type='hidden' class='form-control' id='asset_id_" + asset + "' name='assets[" + asset +
                "][id]' value='" +
                asset +
                "'>" +

                "<input type='text' class='form-control' id='asset_name_" + asset + "' name='assets[" + asset +
                "][asset]' value='" +
                assetName +
                "' readonly>" +
                "</td>");

            let td_barcode = $("<td>" +
                "<input type='text' class='form-control' name='assets[" + asset + "][barcode]' value='" +
                barcode +
                "' readonly>" +
                "</td>");

            let td_category = $("<td>" +
                "<input type='text' class='form-control' name='assets[" + asset + "][category]' value='" +
                category +
                "' readonly>" +
                "</td>");

            let td_status = $("<td>" +
                "<input type='text' class='form-control' name='assets[" +
                asset + "][status]' value='" +
                status +
                "' readonly>" +
                "</td>");

            let td_del = $(
                "<td align='center'>" +
                "<button type='button' class='btn btn-sm btn-danger' title='Delete' onclick='deleteRow(" +
                asset +
                ")'>Delete</button>" +
                "<input type='hidden' class='form-control' name='asset_item_check[]' value='" +
                asset +
                "'>" +
                "</td>"
            );

            // Append Tr Element
            (tr.append(td_asset).append(td_barcode).append(td_category).append(td_status).append(
                td_del)).insertAfter(form_asset)

            // Append To Table
            $("#asset tbody").append(tr);

            // Reset Field Value
            $('#asset_id').val('').trigger('change');
            $('#category').val('');
            $('#status').val('');
            $('#barcode').val('');

            // Reset Field Value
            $('#asset_id option[value=' + asset + ']').each(function() {
                $(this).remove();
            });
            $('#asset_id').val('').trigger('change');

        } else {
            sweetAlertWarning('Please Complete The Record!');
        }
    }

    function deleteRow(id) {
        $('#asset_id').append($('<option>', {
            value: $('#asset_id_' + id).val(),
            text: $('#asset_name_' + id).val()
        }));
        $('#asset_tr_' + id).remove();
    }

    // kolom otomatis asset
    $(document).ready(function() {
        $('#asset_id').on('change', function() {
            var assetId = $(this).val();

            if (assetId) {

                $.ajax({
                    url: '{{ route('asset.physical.show', '') }}' + '/' + assetId,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var asset = response.data;
                            if (asset.status == 1) {
                                status = 'Good Condition'
                            } else if (asset.status == 2) {
                                status = 'Minor Damage'
                            } else if (asset.status == 3) {
                                status = 'Major Damage'
                            }

                            $('#category').val(asset
                                .category.name
                            );
                            $('#status').val(
                                status);
                            $('#barcode').val(asset
                                .barcode_code
                            );
                        } else {
                            alert('Invalid Request!');
                        }
                    },
                    error: function(xhr) {
                        sweetAlertError('Failed to fetch asset data.');
                    }
                });
            } else {

                $('#category').val('');
                $('#status').val('');
                $('#barcode').val('');
            }
        });
    });

    function destroyRecord(id) {
        let token = $('meta[name="csrf-token"]').attr('content');

        Swal.fire({
            title: 'Are You Sure Want To Delete Record?',
            icon: 'question',
            showCancelButton: true,
            allowOutsideClick: false,
            customClass: {
                confirmButton: 'btn btn-primary mr-2 mb-3',
                cancelButton: 'btn btn-danger mb-3',
            },
            buttonsStyling: false,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                sweetAlertProcess();
                $.ajax({
                    url: '{{ url('submission') }}/' + id,
                    type: 'DELETE',
                    cache: false,
                    data: {
                        _token: token
                    },
                    success: function(data) {
                        location.reload();
                    },
                    error: function(xhr, error, code) {
                        sweetAlertError(error);
                    }
                });
            }
        })
    }

    function approvedRecord(id) {
        let token = $('meta[name="csrf-token"]').attr('content');

        Swal.fire({
            title: 'Are You Sure Want To Approve Record?',
            icon: 'question',
            showCancelButton: true,
            allowOutsideClick: false,
            customClass: {
                confirmButton: 'btn btn-primary mr-2 mb-3',
                cancelButton: 'btn btn-danger mb-3',
            },
            buttonsStyling: false,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                sweetAlertProcess();
                $.ajax({
                    url: '{{ route('submission.approve') }}',
                    type: 'POST',
                    cache: false,
                    data: {
                        _token: token,
                        id: id
                    },
                    success: function(data) {
                        location.reload();
                    },
                    error: function(xhr, error, code) {
                        sweetAlertError(error);
                    }
                });
            }
        })
    }

    function rejectedRecord(id) {
        let token = $('meta[name="csrf-token"]').attr('content');

        Swal.fire({
            input: "textarea",
            inputLabel: "Reason",
            inputPlaceholder: "Type your reason here...",
            inputAttributes: {
                "aria-label": "Type your reason here"
            },
            customClass: {
                confirmButton: 'btn btn-primary mr-2 mb-3',
                cancelButton: 'btn btn-danger mb-3',
            },
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const reason = Swal.getInput().value;
                if (!reason) {
                    Swal.showValidationMessage(`Please Enter Reason`)
                }
                return {
                    reason: reason
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const reason = result.value.reason;
                sweetAlertProcess();
                $.ajax({
                    url: '{{ route('submission.reject') }}',
                    type: 'POST',
                    cache: false,
                    data: {
                        _token: token,
                        id: id,
                        reason: reason
                    },
                    success: function(data) {
                        location.reload();
                    },
                    error: function(xhr, error, code) {
                        sweetAlertError(error);
                    }
                });
            }
        })
    }
</script>
