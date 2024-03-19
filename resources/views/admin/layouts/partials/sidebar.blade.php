<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{route('admin.dashboard')}}" title="{{ __('general.app_name') }}">
                <img src="{{ asset(config('constant.logo_path')) }}" alt="{{ __('general.app_name') }}">
            </a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{route('admin.dashboard')}}">
                <img src="{{ asset('images/logo-icon.png') }}" alt="{{ __('general.app_name') }}">
            </a>
        </div>
        <ul class="sidebar-menu">
            <li class="{{ (in_array(Request::route()->getName(),['admin.dashboard','admin.mostexpireditem.index','admin.lessavailablestock.index','admin.orderreminder.index'])?"active":"") }}">    
                <a class="nav-link" href="{{ route('admin.dashboard') }}"  data-toggle="tooltip" data-original-title="{{ __('pages.dashboard.title') }}"><i class="fas fa-fire"></i> <span>{{ __('pages.dashboard.title') }}</span></a>
            </li>
            <li class="{{ in_array(Request::route()->getName(),['admin.customer','admin.customer.add','admin.customer.edit']) ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('admin.customer') }}"  data-toggle="tooltip" data-original-title="{{ __('pages.customer.title') }}"><i class="fas fa-users "></i> <span>{{ __('pages.customer.title') }}</span></a>
            </li>
            <li class="{{ in_array(Request::route()->getName(),['admin.customer-pdf']) ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('admin.customer-pdf') }}"  data-toggle="tooltip" data-original-title="{{ __('pages.pdf.title') }}"><i class="far fa-address-card"></i> <span>{{ __('pages.pdf.title') }}</span></a>
            </li>
            <li class="{{ Request::route()->getName() == 'admin.users' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('admin.users') }}"  data-toggle="tooltip" data-original-title="{{ __('pages.user.title') }}"><i class="fas fa-th-list"></i> <span>{{ __('pages.user.title') }}</span></a>
            </li>
          
        </ul>
    </aside>
</div>