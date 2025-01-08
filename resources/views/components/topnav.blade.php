<div class="row border-bottom">
  <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
      <a class="navbar-minimalize minimalize-styl-2 btn btn-primary" href="#"><i class="fa fa-bars"></i></a>
      <form role="search" class="navbar-form-custom" action="">
        <div class="form-group">
          <input type="text" placeholder="Search for something..." class="form-control" name="top-search"
            id="top-search">
        </div>
      </form>
    </div>
    <ul class="nav navbar-top-links navbar-right">
      <li>
        <span class="m-r-sm text-muted welcome-message">Welcome to Dashboard</span>
      </li>
      <li>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
            <i class="fa fa-sign-out"></i> Log out
          </a>
        </form>
      </li>
    </ul>
  </nav>
</div>
