@extends('admin.layouts.app')
@section('title')
    <title>{{ $title }} &mdash; {{ config('app.name', 'Laravel') }}</title>
@endsection
@section('header-content')
    <h1>{{ $title }}</h1>
    @include('admin.layouts.partials.breadcrumb-section')
@endsection
@section('css')
    <style>
        .expired{
            color :red !important;
        }
       
    </style>  
@endsection
@section('content')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="mostExpiredItemList" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>{{ __('datatable.name') }}</th>
                                    <th>{{ __('datatable.expdate') }}</th>
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
            mostExpiredItemList();
        });

        function mostExpiredItemList() {
            var url = "{{ route('admin.mostexpireditem.list') }}";
            $('#mostExpiredItemList').DataTable({
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
                        "data": "name",
                        orderable: true
                    },
                    {
                        "data": "date",
                        orderable: true
                    },
                    {
                        "data": "detail",
                        orderable: false
                    }
                ]
            });
        }
    </script>
@endsection
