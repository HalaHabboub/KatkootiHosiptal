<nav class="navbar navbar-expand-lg navbar-light shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; gap: 10px;">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Katkooti Logo" style="height: 40px;">
            <span>Katkooti</span>
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupport" aria-controls="navbarSupport" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupport">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item {{ Request::is('admin/doctors') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.doctors.manage') }}">Manage Doctors</a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger ml-lg-3">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
