<nav class="navbar navbar-expand-lg navbar-light shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}" style="display: flex; align-items: center; gap: 10px;">
            <img src="{{ asset('assets/img/logo.png') }}" alt="One-Health Logo" style="height: 40px;">
            <span>Katkooti</span>
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupport"
            aria-controls="navbarSupport" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupport">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('about') }}">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('doctors.index') }}">View All Doctors</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary ml-lg-3" href="{{ route('login') }}">Login / Register</a>
                </li>
            </ul>
        </div>
    </div>
</nav>