@extends('layouts.app')
@section('css')
<style>
    input[type=submit]:disabled {
        cursor: no-drop;
    }
</style>
@endsection
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <span><h3>Task</h3></span>
                        <a href="{{ route('user.task.create') }}" class="btn btn-outline-primary add-data"
                            data-title="Add Task">Add</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover data-table w-100">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="50%">title</th>
                                    <th width="10%">Status</th>
                                    <th width="25%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        $(function() {

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('user.task.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'completed',
                        name: 'completed',
                        render: function (data, type, row, meta){
                            return `<select class="task-status" data-href="{{ route('user.task.status') }}" data-id="${row.id}">
                                        <option value="1" ${row.completed == '1' ? 'selected' : ''}>Completed</option>
                                        <option value="0" ${row.completed == '0' ? 'selected' : ''}>Incompleted</option>
                                    </select>`;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });

        $(document).on('click', '.add-data', function(e) {
            e.preventDefault();

            $('.common-modal').modal('show');
            let title = $(this).data('title');
            let url = $(this).attr('href');
            $('.modal-title').html(title);
            $.ajax({
                url: url,
                type: "GET",
                success: function(response) {
                    $('.modal-body').html(response);
                }
            })
        })

        $(document).on('click', '#task-submit', function(e) {
            e.preventDefault();
            $(this).prop('disabled', true);
            let url = $('#task-form').attr('action');
            var formData = new FormData($("#task-form")[0]);
            // console.log("formData => ", formData);

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(res) {
                    $('.error').text("");
                    if (res.error) {
                        for (const [key, value] of Object.entries(res.errors)) {
                            console.log("#" + key + "error");
                            $("#" + key + "error").text(value);
                        }
                        $('#task-submit').prop('disabled', false);
                        if (res.alert) {
                            Swal.fire({
                                position: "top-end",
                                icon: "error",
                                title: res.msg,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    } else {
                        $('.common-modal').modal('hide');
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: res.msg,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        $('.data-table').DataTable().ajax.reload();
                    }
                }
            })
        })

        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');

            Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            if (res.success) {
                                $('.data-table').DataTable().ajax.reload();
                            }
                        }
                    })
                    Swal.fire({
                        title: "Deleted!",
                        text: "Your file has been deleted.",
                        icon: "success"
                    });
                }
            });
        })

        $(document).on('click', '.view-btn', function(e) {
            e.preventDefault();
            $('.common-modal').modal('show');
            let title = $(this).data('title');
            let url = $(this).attr('href');
            $('.modal-title').html(title);
            $.ajax({
                url: url,
                type: "GET",
                success: function(response) {
                    $('.modal-body').html(response);
                }
            })
        })

        $(document).on('change', '.task-status', function(e) {
            e.preventDefault();
            let url = $(this).data('href');
            let id = $(this).data('id');
            let val = $(this).val();
            
            $.ajax({
                url: url,
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    id: id,
                    value: val,
                },
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: res.msg,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        $('.data-table').DataTable().ajax.reload();
                    }
                }
            })
        })
    </script>
@endsection