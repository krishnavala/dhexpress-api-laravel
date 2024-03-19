@extends('admin.layouts.app')
@section('title')
    <title>{{ $title }} &mdash; {{ config('app.name', 'Laravel') }}</title>
@endsection
@section('header-content')
    <h1>{{ $title }}</h1>
   
    @include('admin.layouts.partials.breadcrumb-section')
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="contenttopbar">
                <ul class="d-flex align-content-center float-right assigned-order">
                    <a href="{{ $download_route }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ $download_title }}
                    </a>

                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="customer_listing" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>{{ __('datatable.code') }}</th>
                                    <th>{{ __('datatable.group') }}</th>
                                    <th>{{ __('datatable.name') }}</th>
                                    <th>{{ __('datatable.pin_code') }}</th>
                                    <th>{{ __('datatable.phone_no') }}</th>
                                    <th>{{ __('datatable.invoice') }}</th>
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
        $(document).ready(function() {
            customerList();
            $('body').on('click', '.delete_customer', function(e) {
                e.preventDefault();
                // var requestId = $(this).attr('data-id');
                var data = {
                    customer_uuid: $(this).attr('data-id')
                };
                var confirmMsg = "{{ __('admin_message.pdf_msg.confirm_delete') }}";
                var deleteRoute = "{{ route('admin.pdf.delete') }}";
                var tableId = "customer_listing";
                //common delete function
                deleteFire(deleteRoute, data, tableId, confirmMsg);
            });

        });

        function customerList() {
            //load user data into table
            var url = "{{ route('admin.customer-pdf.list') }}";
           
            $('#customer_listing').DataTable({
                "order": [],
                "responsive": true,
                "searching": true,
                "processing": true,
                "serverSide": true,
                "deferRender": true,
                "lengthChange": true,
                "initComplete": function(settings, json) {},
                "ajax": {
                    "url": url,
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        "_token": "{{ csrf_token() }}",
                    },
                },
                "columns": [{
                        "data": "customer_code",
                        orderable: true
                    },
                    {
                        "data": "group",
                        orderable: true
                    },
                    {
                        "data": "customer_name",
                        orderable: true
                    },
                    {
                        "data": "pin_code",
                        orderable: true
                    },
                    {
                        "data": "contact_no",
                        orderable: true
                    },
                    {
                        "data": "invoice",
                        orderable: true
                    },
                    {
                        "data": "action",
                        orderable: false
                    }
                ]
            });
        }
    </script>
@endsection
