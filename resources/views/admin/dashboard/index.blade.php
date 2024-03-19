@extends('admin.layouts.app')
@section('title')
<title>{{ __('pages.dashboard.title') }} &mdash; {{ config('app.name', 'Laravel') }}</title>
@endsection
@section('header-content')
<h1>{{__("pages.dashboard.title")}}</h1>
@endsection
@section('css')
    
@endsection
@section('content')      
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
        <a href="{{ route('admin.customer') }}"><div class="card-icon bg-warning">
            <i class="fas fa-users"></i>
        </div></a>
        <div class="card-wrap">
            <div class="card-header">
            <a href="{{ route('admin.customer') }}"><h4>{{ __('pages.dashboard.customer') }}</h4></a>
            </div>
            <div class="card-body">
            {{ $customerCount }}
            </div>
        </div>
        </div>
    </div>
   
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
        <a href="{{ route('admin.customer-pdf') }}"><div class="card-icon bg-info">
            <i class="far fa-address-card"></i>
        </div></a>
        <div class="card-wrap">
            <div class="card-header">
            <a href="{{ route('admin.customer-pdf') }}"><h4>{{ __('pages.dashboard.pdf') }}</h4></a>
            </div>
            <div class="card-body">
            {{ $pdfCount }}
            </div>
        </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
        <a href="{{ route('admin.users') }}"><div class="card-icon bg-success">
            <i class="fas fa-th-list "></i>
        </div></a>
        <div class="card-wrap">
            <div class="card-header">
            <a href="{{ route('admin.users') }}"><h4>{{ __('pages.dashboard.user') }}</h4></a>
            </div>
            <div class="card-body">
            {{ $userCount }}
            </div>
        </div>
        </div>
    </div>
   
</div>


@endsection

@section('page-script')

@endsection