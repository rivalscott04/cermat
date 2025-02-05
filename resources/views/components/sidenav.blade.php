<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <div
                        class="avatar bg-{{ ['primary', 'success', 'info', 'warning', 'danger'][array_rand(['primary', 'success', 'info', 'warning', 'danger'])] }} mb-1">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="text-medium mt-3 block">
                            {{ Auth::user()->name }}
                        </span>
                    </a>
                </div>

                <div class="logo-element">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo">
                </div>
            </li>

            @if (Auth::user()->role == 'admin')
                <li>
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fa fa-dashboard"></i>
                        <span class="nav-label">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}">
                        <i class="fa fa-user"></i>
                        <span class="nav-label">User</span>
                    </a>
                </li>
            @endif

            @if (Auth::user()->role == 'user')
                <li>
                    <a href="{{ route('user.profile', ['userId' => Auth::user()->id]) }}">
                        <i class="fa fa-user-circle"></i>
                        <span class="nav-label">Profile</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('kecermatan') }}">
                        <i class="fa fa-user"></i>
                        <span class="nav-label">Tes Kecermatan</span>
                    </a>
                </li>
            @endif

            <li>
                @if (Auth::user()->role == 'admin')
                    <a href="{{ route('admin.riwayat.tes') }}">
                        <i class="fa fa-history"></i>
                        <span class="nav-label">Riwayat Tes</span>
                    </a>
                @else
                    <a href="{{ route('kecermatan.riwayat', ['userId' => Auth::user()->id]) }}">
                        <i class="fa fa-history"></i>
                        <span class="nav-label">Riwayat Tes</span>
                    </a>
                @endif
            </li>

        </ul>
    </div>
</nav>
