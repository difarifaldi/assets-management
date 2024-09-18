<script type="text/javascript">
    $("form").submit(function(e) {
        e.preventDefault();
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
    });

    function dataTable() {
        // console.log('dataTable function called');
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
        console.log('dataTable function called');
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
