<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Katkooti - Login/Register</title>

    <!-- Bootstrap and Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/maicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/animate/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <style>
        .form-group {
            margin-bottom: 15px;
        }

        .btn-block {
            width: 100%;
        }

        .toggle-form {
            cursor: pointer;
            color: #007bff;
        }

        .toggle-form:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .card {
                margin-top: 20px;
                padding: 15px;
            }
        }

        @media (max-width: 576px) {
            .card {
                margin-top: 10px;
                padding: 10px;
            }
        }
    </style>
</head>

<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <!-- Logo or leave empty -->
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
        @endif

        <div class="text-center">
            <a class="navbar-brand" href="#" style="display: flex; align-items: center; gap: 10px;">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Katkooti Logo" style="height: 40px;">
                <span>Katkooti</span>
            </a>
            <h5 class="mb-4" id="form-title">Login to your account</h5>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf
            <div class="form-group">
                <label for="user_type">User Type</label>
                <select class="form-control" id="user_type" name="user_type" required>
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>

            <div class="text-center mt-4">
                <a href="#" class="toggle-form">Don't have an account? Sign up</a>
            </div>
        </form>

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register') }}" class="signup-form" style="display:none">
            @csrf
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="signup-email">Email address</label>
                <input type="email" class="form-control" id="signup-email" name="email" required>
            </div>

            <div class="form-group">
                <label for="signup-password">Password</label>
                <input type="password" class="form-control" id="signup-password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" class="form-control" id="confirm-password" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Sign Up</button>

            <div class="text-center mt-4">
                <a href="#" class="toggle-form">Already have an account? Login</a>
            </div>
        </form>

    </x-authentication-card>
</x-guest-layout>

<!-- Scripts (Place after guest-layout closure) -->
<script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/wow/wow.min.js') }}"></script>
<script src="{{ asset('assets/js/theme.js') }}"></script>

<script>
    $(document).ready(function () {
        $('.toggle-form').click(function (e) {
            e.preventDefault();
            $('.login-form, .signup-form').toggle();
            $('#form-title').text(function (_, text) {
                return text === 'Login to your account' ? 'Sign Up for an account' : 'Login to your account';
            });
        });
    });
</script>