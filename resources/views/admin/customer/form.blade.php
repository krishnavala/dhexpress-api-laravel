@extends('admin.layouts.app')
@section('title')
    <title>{{ $formTitle }} &mdash; {{ config('app.name', 'Laravel') }}</title>
@endsection
@section('header-content')
    <h1>{{ $formTitle }}</h1>
    @include('admin.layouts.partials.breadcrumb-section')
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('admin/css/datepicker/bootstrap-datepicker.min.css') }}">
<style>
    .mt-3{
        margin-top: 2rem!important;
    }
</style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="post" name="customer-form" id="customer-form" action="{{ route('admin.customer.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="customerID" class="form-control" id="uuId" value="{{ $customerID }}">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('forms.customer.group') }} *</label>
                                    <input type="text" name="group" maxlength="50" class="form-control" id="group" value="{{ !empty($customer->customerDetail->group) ? $customer->customerDetail->group : old('group') }}" placeholder="{{ __('forms.customer.placeholder.group') }}" min="1" maxlength="50" >
                                    <span class="text-danger">{{ $errors->has('group') ? $errors->first('group') : '' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('forms.customer.customer_code') }} *</label>
                                    <div class="input-group input-group-sm">
                                    <input type="text" name="customer_code" maxlength="50" class="form-control" id="customer_code" value="{{ !empty($customer->customer_code) ? $customer->customer_code : old('customer_code') }}" placeholder="{{ __('forms.customer.placeholder.customer_code') }}" min="1" maxlength="50"  @if(!empty($customer->id)) disabled="disabled" @endif>
                                        <span class="input-group-append">
                                            <button type="button" onclick='searchByCode();' class="btn btn-primary btn-sm"><i class="fa fa-search" aria-hidden="true"></i></button>
                                        </span>
                                    </div>
                                    <label id="customer_code-error" style="display: none;" class="error" for="customer_code">Client Code is required.</label>
                                </div>
                            </div>
                            <!-- <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('forms.customer.customer_code') }} *</label>
                                    <input type="text" name="customer_code" maxlength="50" class="form-control" id="customer_code" value="{{ !empty($customer->customer_code) ? $customer->customer_code : old('customer_code') }}" placeholder="{{ __('forms.customer.placeholder.customer_code') }}" min="1" maxlength="50"  @if(!empty($customer->id)) disabled="disabled" @endif>
                                    <span class="text-danger">{{ $errors->has('customer_code') ? $errors->first('customer_code') : '' }}</span>
                                </div>
                            </div> -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('forms.customer.customer_name') }} *</label>
                                    <input type="text" name="customer_name" maxlength="50" class="form-control" id="customer_name" value="{{ !empty($customer->customerDetail->customer_name) ? $customer->customerDetail->customer_name : old('customer_name') }}" placeholder="{{ __('forms.customer.placeholder.customer_name') }}" min="1" maxlength="50" >
                                    <span class="text-danger">{{ $errors->has('customer_name') ? $errors->first('customer_name') : '' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('forms.customer.pin_code') }} *</label>
                                    <input type="number" name="pin_code" maxlength="50" class="form-control" id="pin_code" value="{{ !empty($customer->customerDetail->pin_code) ? $customer->customerDetail->pin_code : old('pin_code') }}" placeholder="{{ __('forms.customer.placeholder.pin_code') }}" min="1" maxlength="6">
                                    <span class="text-danger">{{ $errors->has('pin_code') ? $errors->first('pin_code') : '' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('forms.customer.contact_no') }} *</label>
                                    <input type="number" name="contact_no" maxlength="10" class="form-control" id="contact_no" value="{{ !empty($customer->customerDetail->contact_no) ? $customer->customerDetail->contact_no : old('contact_no') }}" placeholder="{{ __('forms.customer.placeholder.contact_no') }}" min="1" maxlength="10">
                                    <span class="text-danger">{{ $errors->has('contact_no') ? $errors->first('contact_no') : '' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('forms.customer.invoice') }} *</label>
                                    <input type="text" name="invoice" maxlength="50" class="form-control" id="invoice" value="{{ !empty($customer->customerDetail->invoice) ? $customer->customerDetail->invoice : old('invoice') }}" placeholder="{{ __('forms.customer.placeholder.invoice') }}" min="1" maxlength="10">
                                    <span class="text-danger">{{ $errors->has('invoice') ? $errors->first('invoice') : '' }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('forms.customer.address') }} *</label>
                                    <textarea rows="20" placeholder="{{ __('forms.customer.address') }}" name="address" class="form-control ckeditor" id="address" > {{ !empty($customer->customerDetail->address)?html_entity_decode($customer->customerDetail->address):old('address') }}</textarea>
                                    <span class="text-danger">{{ $errors->has('address') ? $errors->first('address') : '' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('forms.customer.remarks') }} </label>
                                    <textarea rows="20" placeholder="{{ __('forms.customer.remarks') }}" name="remarks" class="form-control ckeditor" id="remarks" > {{ !empty($customer->customerDetail->remarks)?html_entity_decode($customer->customerDetail->remarks):old('remarks') }}</textarea>
                                    <span class="text-danger">{{ $errors->has('remarks') ? $errors->first('remarks') : '' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <button type="submit" name="type" value="1" id="save_form" class="btn btn-primary btn-submit">{{ __('general.save') }}</button>
                        <button type="submit" name="type" value="2" id="save_move_form" class="btn btn-primary btn-submit">{{ __('general.save_move') }}</button>
                        <button type="button"  id="save_form_disabled"  disabled="true" class="btn btn-primary btn-submit" style="display: none;">{{ __('general.submitting') }}
                                <span class="submit-loader_show"> <i class="fas fa-spinner fa-spin"></i> </span>
                        </button>
                        <button type="button" onclick="location.href='{{ route('admin.customer') }}'" class="btn btn-outline-secondary mr-1">{{ __('general.cancel') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('page-script')
    <script src="{!! asset('ckeditor/ckeditor.js') !!}"></script>
    <script src="{!! asset('admin/js/jquery-validation/jsvalidation.min.js') !!}"></script>
    <script src="{!! asset('admin/js/jquery-validation/additional-methods.js') !!}"></script>
    <script src="{!! asset('admin/js/datepicker/bootstrap-datepicker.min.js') !!}"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            jQuery.validator.addMethod('ckrequired', function (value, element, params) {
                var idname = jQuery(element).attr('id');
                var messageLength =  jQuery.trim ( CKEDITOR.instances[idname].getData().replace(/&nbsp;/g, ' ').trim() );
                console.log(messageLength,'messageLengthmessageLengthmessageLength');
                return !params  || messageLength.length !== 0 ||  messageLength !== '';
            }, "{{ __('admin_message.customer.address') }}");

            $('#customer-form').validate({
                ignore: [],
                debug: false,
                rules: {
                    'group': {
                        required: true,
                    },
                    'customer_code': {
                        required: true
                    },
                    'customer_name': {
                        required: true,
                    },
                    'pin_code': {
                        required: true,
                    },
                    'contact_no' : {
                        required:true
                    },
                    'invoice' : {
                        required:true
                    },
                    'address' : {
                        ckrequired:true,
                        validateEmptySpace:true
                    }
                   
                },
                messages: {
                    
                    'group': {
                        required: "{{ __('admin_message.customer.group') }}"
                    },
                    'customer_code': {
                        required: "{{ __('admin_message.customer.customer_code') }}"
                    },
                    'customer_name': {
                        required: "{{ __('admin_message.customer.customer_name') }}"
                    },
                    'pin_code': {
                        required: "{{ __('admin_message.customer.pin_code') }}"
                    },
                    'contact_no': {
                        required: "{{ __('admin_message.customer.contact_no') }}"
                    },
                    'invoice': {
                        required: "{{ __('admin_message.customer.invoice') }}"
                    },
                    'address': {
                        ckrequired: "{{ __('admin_message.customer.address') }}",
                    }
                    
                    
                }
            });
           
        });
        $(document).on('submit', '#customer-form', function() {
             $('#save_form').hide();
             $('#save_move_form').hide();
             $('#save_form_disabled').show();
             $(".submit-loader").show();
        });
        
        function searchByCode()
        {
            // $('.remove_order_product').prop('disabled', true);  
            var route = "{{ route('admin.customer.search') }}";
            var searchCode =$("#customer_code").val();
            var data = {
              searchCode: searchCode,
              _token:$('meta[name="csrf-token"]').attr('content')
            };
            $.ajax({
              method: "GET",
              url: route,
              data: data,
              success: function (res) {
              stockQuantityError = false;
              if(res.customer)
              {
                $("#uuId").val(res.customer?.id);
                $("#group").val(res.customer?.customer_detail.group);
                $("#customer_name").val(res.customer?.customer_detail.customer_name);
                $("#pin_code").val(res.customer?.customer_detail.pin_code);
                $("#contact_no").val(res.customer?.customer_detail.contact_no);
                $("#invoice").val(res.customer?.customer_detail.invoice);
                $("#address").val(res.customer?.customer_detail.address);
              }
              else
              {
                alert('22');
              }   
                     
              },
              error: function(res){
                    laravel.error(res.message);
                    $('.remove_order_product').prop('disabled', false);
              }
            });
        }

    </script>
@endsection
