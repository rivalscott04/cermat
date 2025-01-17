<nav class="navbar-default navbar-static-side" role="navigation">
  <div class="sidebar-collapse">
    <ul class="nav metismenu" id="side-menu">
      <li class="nav-header">
        <div class="dropdown profile-element">
          <img alt="image" class="rounded-circle" src="{{ asset('img/profile_small.jpg') }}" />
          <a data-toggle="dropdown" class="dropdown-toggle" href="#">
            {{-- <span class="m-t-xs block font-bold">{{ Auth::user()->name }}</span> --}}
            <span class="text-muted block text-xs">
              {{ Auth::user()->role == 'admin' ? 'Admin' : 'User' }}
              <b class="caret"></b>
            </span>
          </a>
          <ul class="dropdown-menu animated fadeInRight m-t-xs">
            <li><a class="dropdown-item" href="{{ route('user.profile', ['userId' => Auth::user()->id]) }}">Profile</a>
            </li>
            <li class="dropdown-divider"></li>
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a class="dropdown-item" href="{{ route('logout') }}"
                  onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
              </form>
            </li>
          </ul>
        </div>
        <div class="logo-element">
          IN+
        </div>
      </li>

      @if (Auth::user()->role == 'admin')
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
      @endif

      <li>
        <a href="{{ route('subscription.checkout') }}">
          <i class="fa fa-money"></i>
          <span class="nav-label">Pembayaran</span>
        </a>
      </li>

      <li>
        <a href="{{ route('kecermatan') }}">
          <i class="fa fa-user"></i>
          <span class="nav-label">Tes Kecermatan</span>
        </a>
      </li>

      <li>
        <a href="{{ route('kecermatan.riwayat', ['userId' => Auth::user()->id]) }}">
          <i class="fa fa-history"></i>
          <span class="nav-label">Riwayat Tes</span>
        </a>
      </li>
    </ul>
  </div>
</nav>
