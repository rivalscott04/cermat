<nav class="navbar-default navbar-static-side" role="navigation">
  <div class="sidebar-collapse">
    <ul class="nav metismenu" id="side-menu">
      <li class="nav-header">
        <div class="dropdown profile-element">
          <img alt="image" class="rounded-circle" src="{{ asset('img/profile_small.jpg') }}" />
          <a data-toggle="dropdown" class="dropdown-toggle" href="#">
            {{-- <span class="m-t-xs block font-bold">{{ Auth::user()->name }}</span> --}}
            <span class="text-muted block text-xs">Admin <b class="caret"></b></span>
          </a>
          <ul class="dropdown-menu animated fadeInRight m-t-xs">
            <li><a class="dropdown-item" href="">Profile</a></li>
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

      <li>
        <a href="layouts.html"><i class="fa fa-diamond"></i> <span class="nav-label">Layouts</span></a>
      </li>

      <li>
        <a href="{{ route('admin.users.index') }}"><i class="fa fa-user"></i> <span class="nav-label">User</span></a>
      </li>

      <li>
        <a href="{{ route('kecermatan') }}"><i class="fa fa-user"></i> <span class="nav-label">Tes
            Kecermatan</span></a>
      </li>

      <li>
        <a href="{{ route('kecermatan.riwayat', ['userId' => Auth::user()->id]) }}">
          <i class="fa fa-history"></i>
          <span class="nav-label">Riwayat Tes</span>
        </a>
      </li>

      <li>
        <a href="#"><i class="fa fa-table"></i> <span class="nav-label">Tables</span><span
            class="fa arrow"></span></a>
        <ul class="nav nav-second-level collapse">
          <li><a href="">Static Tables</a></li>
          <li><a href="">Data Tables</a></li>

        </ul>
      </li>
    </ul>
  </div>
</nav>
