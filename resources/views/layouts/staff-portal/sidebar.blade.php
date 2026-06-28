<div class="page-sidebar">
    <div class="main-header-left d-none d-lg-block">
        <div class="logo-wrapper">
            <a href="{{ route('staff.dashboard') }}">
                <img class="d-none d-lg-block blur-up lazyloaded sidebar-logo" src="{{ logo() }}" alt="">
            </a>
        </div>
    </div>
    <div class="sidebar custom-scrollbar">
        <a href="javascript:void(0)" class="sidebar-back d-lg-none d-block">
            <i class="fa fa-times" aria-hidden="true"></i>
        </a>
        <div class="sidebar-user">
            <img class="img-60" src="{{ asset('assets/admin/images/logo/sun-icon.png') }}" alt="#">
            <div>
                <h6 class="f-14">{{ admin()->first_name }}</h6>
                <p>Staff Portal</p>
            </div>
        </div>
        <ul class="sidebar-menu">
            <x-dashboard.sidebar.single-link title="Overview" link="{{ route('staff.dashboard') }}" icon="home"/>
            <li class="sidebar-main-title"><div><h6>Guest Requests</h6></div></li>
            <x-dashboard.sidebar.single-link :permissions="['guest-requests.list']" title="All Requests" link="{{ route('staff.requests.index') }}" icon="clipboard"/>
            <x-dashboard.sidebar.single-link :permissions="['guest-requests.list']" title="My Assignments" link="{{ route('staff.requests.index', ['mine' => 1]) }}" icon="user-check"/>
            @if(admin()->hasAnyRole(['Administrator', 'super_admin', 'operator']))
                <li class="sidebar-main-title"><div><h6>Admin</h6></div></li>
                <x-dashboard.sidebar.single-link title="Full Dashboard" link="{{ route('dashboard') }}" icon="grid"/>
            @endif
            <x-dashboard.sidebar.single-link title="Logout" link="{{ route('logout') }}" icon="log-in"/>
        </ul>
    </div>
</div>
