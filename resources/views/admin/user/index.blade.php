@extends('admin.layouts.app')
@section('title')
<title>{{ __('pages.user.title') }} &mdash; {{ config('app.name', 'Laravel') }}</title>
@endsection
@section('header-content')
<h1>{{ __('pages.user.title') }}</h1>
@include('admin.layouts.partials.breadcrumb-section')
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="user_listing" style="width:100%;">
                        <thead>
                            <tr>
                                <th>{{ __('datatable.name') }}</th>
                                <th>{{ __('datatable.email') }}</th>
                                <th>{{ __('datatable.action') }}</th>
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

@section('page-script')
<script type="text/javascript">
    $(document).ready(function () {
        //load user data into table
        userList();
        $('body').on('click', '.delete_user', function (e) {
            e.preventDefault();
            // var requestId = $(this).attr('data-id');
            var data = {
                id: $(this).attr('data-id')
            };
            var confirmMsg = "{{ __('admin_message.confirm_user_delete') }}";
            var deleteRoute = "{{ route('admin.users.delete') }}";
            var tableId = "user_listing";
            //common delete function
            deleteFire(deleteRoute,data,tableId,confirmMsg);
        });
    });
    function userList() {
        // $('#user_listing').DataTable().ajax.reload();
        $('#user_listing').DataTable({
            "order": [0, "desc"],
            "responsive": true,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "lengthChange": true,
            "initComplete": function (settings, json) {},
            "ajax": {
                "url": "{{ route('admin.users.list') }}",
                "dataType": "json",
                "type": "POST",
                "data": {
                    "_token": "{{ csrf_token() }}",
                },
            },
            "columns": [
                {"data": "name",orderable: true},
                {"data": "email",orderable: true},
                {"data": "detail",orderable: false}
            ]
        });
    }
</script>
@endsection
