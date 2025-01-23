@extends('layouts.app')

@section('content')
@include('partials.patientNavbar')
<!-- Replace general navbar with patient's navbar -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Welcome') }}</div>

                <div class="card-body">
                    @if (Auth::check())
                    {{ __('You are logged in!') }}
                    @else
                    {{ __('Welcome to our hospital system! Please log in or register to continue.') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection