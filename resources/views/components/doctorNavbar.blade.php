<nav class="navbar navbar-expand-lg navbar-light shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand" href="{{ url('/') }}" style="display: flex; align-items: center; gap: 10px;">
      <img src="{{ asset('assets/img/logo.png') }}" alt="Katkooti Logo" style="height: 40px;">
      <span>Katkooti</span>
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupport" aria-controls="navbarSupport" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupport">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item {{ Request::is('doctor/dashboard') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('doctor.dashboard') }}">Dashboard</a>
        </li>
        {{-- <li class="nav-item {{ Request::is('doctor/schedule') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('doctor.schedule') }}">Doctor Schedule</a>
        </li> --}}
        <li class="nav-item">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-primary ml-lg-3">Logout</button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>
