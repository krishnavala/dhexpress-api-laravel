@extends('admin.layouts.app')
@section('title')
    <title>{{ $title }} &mdash; {{ config('app.name', 'Laravel') }}</title>
@endsection
@section('header-content')
    <h1>{{ $title }}</h1>
    @include('admin.layouts.partials.breadcrumb-section')
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('admin/css/datepicker/bootstrap-datepicker.min.css') }}">
    <style>
        .expired{
            color :red !important;
        }
        .btn-label {
	position: relative;
	display: inline-block;
	padding: 6px 6px;
	border-radius: 3px 0 0 3px;
    cursor: pointer;  
   }
    .fas{
        font-size:16px !important;  
    }
    .text-success{
        color: #47c363 !important;
    }
    .text-danger{
        color: #fc544b !important;
    }
    .text-info{
        color: #0da8ee !important;
    }
    .text-warning{
        color: #ffa426 !important;
    }
    .text-default{
        color: #555555 !important;
    }
    .border-1 {
     border-bottom: 3px solid #80808033 !important;
    }
    .border-2 {
     border-top: 3px solid #80808033 !important;
    }
    .model-custome-height{
        max-height: calc(100vh - 250px);
        overflow-y: auto;
        overflow-x: hidden;
    }
   
       
    </style>  
@endsection
@section('content')

    <div class="row">
        <div class="col-lg-12 col-md-12 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-6 col-lg-3">
                        <div class="form-group">
                            <label>{{ __('datatable.order.select_expected_delivery_date') }}</label>
                            <input class="form-control expected_delivery_date" id="expected_delivery_date" name="expected_delivery_date" type="date" value="" onkeydown="return false">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="order-reminder" style="width:100%;">
                            <thead>
                                <tr>
                                    <th>{{ __('datatable.order.order_number') }}</th>
                                    <th>{{ __('datatable.order.expected_delivery_date') }}</th>
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
@section('modal-content')
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
@endsection
@section('page-script')
<script src="{!! asset('admin/js/datepicker/bootstrap-datepicker.min.js') !!}"></script>
    <script type="text/javascript">
        
        $(document).ready(function() {
            orderReminder();
        });

        function orderReminder() {
            var url = "{{ route('admin.orderreminder.list') }}";
            var table = $('#order-reminder').DataTable({
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
                    data: function (d) {
                        d.expected_delivery_date = $('#expected_delivery_date').val(),
                        d._token = "{{ csrf_token() }}"
                    }
                },
                "columns": [{
                        "data": "order_number",
                        orderable: true
                    },
                    {
                        "data": "expected_delivery_date",
                        orderable: true
                    },
                    {
                        "data": "detail",
                        orderable: false
                    }
                ]
            });
            $('#expected_delivery_date').change(function(){
                table.draw();
            });
        }
    </script>
@endsection
