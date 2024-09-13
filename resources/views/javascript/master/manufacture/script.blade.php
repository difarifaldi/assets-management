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
        console.log('dataTable function called');
        const url = $('#url_dt').val();
        $('#dt-manufacture').DataTable({
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
                    data: 'name',
                    defaultContent: '-',
                },
                {
                    data: 'address',
                    defaultContent: '-',
                },
                {
                    data: 'action',
                    width: '20%',
                    defaultContent: '-',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    }

    // Delete Record
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
                    url: '{{ url('master/manufacture') }}/' + id,
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

    // Add record
    function addRecord() {
        let token = $('meta[name="csrf-token"]').attr('content');

        Swal.fire({
            title: 'Add Manufacture',
            html: `
            <input type="text" id="name" class="swal2-input" placeholder="Enter Name">
            <input type="textarea" id="address" class="swal2-input" placeholder="Enter Address">
        `,
            customClass: {
                confirmButton: 'btn btn-primary mr-2 mb-3',
                cancelButton: 'btn btn-danger mb-3',
            },
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: 'Submit',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const name = Swal.getPopup().querySelector('#name').value;
                const address = Swal.getPopup().querySelector('#address').value;
                if (!name) {
                    Swal.showValidationMessage(`Please Enter Name`)
                }
                return {
                    name: name,
                    address: address
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const name = result.value.name;
                const address = result.value.address;
                sweetAlertProcess();
                $.ajax({
                    url: '{{ route('master.manufacture.store') }}',
                    type: 'POST',
                    cache: false,
                    data: {
                        _token: token,
                        name: name,
                        address: address
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

    // Update record
    function updateRecord(id) {
        let token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: `/master/manufacture/${id}/edit`,
            type: 'GET',
            cache: false,
            success: function(response) {
                console.log(response);
                const name = response.name || '-';
                const address = response.address || '-';
                Swal.fire({
                    title: 'Edit Manufacture',
                    html: `
        <input type="text" id="name" class="swal2-input" value="${name}">
        <input type="textarea" id="address" class="swal2-input" value="${address}">
    `,
                    customClass: {
                        confirmButton: 'btn btn-primary mr-2 mb-3',
                        cancelButton: 'btn btn-danger mb-3',
                    },
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: 'Submit',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        const name = Swal.getPopup().querySelector('#name').value;
                        const address = Swal.getPopup().querySelector('#address').value;
                        if (!name) {
                            Swal.showValidationMessage(`Please Enter Name`)
                        }
                        return {
                            name: name,
                            address: address
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const name = result.value.name;
                        const address = result.value.address;
                        const id = response.id; // Ambil ID dari response
                        sweetAlertProcess();
                        $.ajax({
                            url: '/master/manufacture/' + id, // Kirim ID dalam URL
                            type: 'POST',
                            cache: false,
                            data: {
                                id: id,
                                _token: token,
                                _method: 'PUT',
                                name: name,
                                address: address
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
            },
            error: function(xhr, status, error) {
                Swal.fire('Error!', 'Data could not be retrieved.', 'error');
            }
        });
    }
</script>
