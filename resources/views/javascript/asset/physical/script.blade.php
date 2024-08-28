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
        $('#dt-physical').DataTable({
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
                    data: 'merk',
                    defaultContent: '-',
                },
                {
                    data: 'category',
                    defaultContent: '-',
                },
                {
                    data: 'assignTo',
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

    // function destroyRecord(id) {
    //     let token = $('meta[name="csrf-token"]').attr('content');

    //     Swal.fire({
    //         title: 'Are You Sure Want To Delete Record?',
    //         icon: 'question',
    //         showCancelButton: true,
    //         allowOutsideClick: false,
    //         customClass: {
    //             confirmButton: 'btn btn-primary mr-2 mb-3',
    //             cancelButton: 'btn btn-danger mb-3',
    //         },
    //         buttonsStyling: false,
    //         confirmButtonText: 'Yes',
    //         cancelButtonText: 'Cancel'
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             sweetAlertProcess();
    //             $.ajax({
    //                 url: '{{ url('master/physical') }}/' + id,
    //                 type: 'DELETE',
    //                 cache: false,
    //                 data: {
    //                     _token: token
    //                 },
    //                 success: function(data) {
    //                     location.reload();
    //                 },
    //                 error: function(xhr, error, code) {
    //                     sweetAlertError(error);
    //                 }
    //             });
    //         }
    //     })
    // }
</script>
