@if (Request::route()->getName() == 'admin.users')
<div class="section-header-breadcrumb">
    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('pages.dashboard.title') }}</a></div>
    <div class="breadcrumb-item">{{ __('pages.user.title') }}</div>
</div>
@elseif(Request::route()->getName() == 'admin.dashboard')
<div class="section-header-breadcrumb">
    <!-- <div class="breadcrumb-item active">{{ __('pages.dashboard.title') }}</div> -->
</div>
@elseif(Request::route()->getName() == 'admin.customer.manage.form')
<div class="section-header-breadcrumb">
    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __(__('pages.dashboard.title')) }}</a></div>
    <div class="breadcrumb-item"><a href="{{ route('admin.customer') }}">{{ __('pages.customer.title') }}</a></div>
    <div class="breadcrumb-item">{{ __('pages.customer.add') }}</div>
</div>
@elseif(Request::route()->getName() == 'admin.customer')
<div class="section-header-breadcrumb">
    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __(__('pages.dashboard.title')) }}</a></div>
    <div class="breadcrumb-item">{{ __('pages.customer.title') }}</div>
</div>
@elseif(Request::route()->getName() == 'admin.customer-pdf')
<div class="section-header-breadcrumb">
    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __(__('pages.dashboard.title')) }}</a></div>
    <div class="breadcrumb-item">{{ __('pages.pdf.title') }}</div>
</div>
@endif