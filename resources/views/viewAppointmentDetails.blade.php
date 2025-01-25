@extends('layouts.app')

@section('title', 'Appointment Details')

@section('content')
@if(Auth::guard('doctor')->check())
    @include('components.doctorNavbar')
@elseif(Auth::guard('admin')->check())
    @include('components.adminNavbar')
@endif

<div class="page-section">
    <div class="container">
        @if(isset($appointment) && isset($patient))
            <h1 class="text-center mb-5">Appointment Details</h1>
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Personal Information</h5>
                            <hr>
                            <p class="mb-1"><strong>Name:</strong> {{ $patient->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Date of Birth:</strong> {{ $patient->date_of_birth ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Age:</strong> {{ isset($patient->date_of_birth) ? \Carbon\Carbon::parse($patient->date_of_birth)->age : 'N/A' }} years</p>
                            <p class="mb-1"><strong>Gender:</strong> {{ $patient->gender ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Blood Group:</strong> {{ $patient->blood_group ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Contact:</strong> {{ $patient->phone ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $patient->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Medical Information</h5>
                            <hr>
                            <p class="mb-1"><strong>Existing Conditions:</strong> {{ $patient->existing_conditions ?? 'None reported' }}</p>
                            <p class="mb-1"><strong>Current Medications:</strong> {{ $patient->current_medications ?? 'None reported' }}</p>
                            <p class="mb-1"><strong>Allergies:</strong> {{ $patient->allergies ?? 'None reported' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Appointment Information</h5>
                            <hr>
                            <p class="mb-1"><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->date_time)->format('Y-m-d') }}</p>
                            <p class="mb-1"><strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->date_time)->format('h:i A') }}</p>
                            <p class="mb-1"><strong>Status:</strong> {{ $appointment->status }}</p>
                            <p class="mb-1"><strong>Message:</strong> {{ $appointment->message ?? 'No message' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-danger">
                <h4>Error</h4>
                <p>Could not load appointment details. Please try again.</p>
            </div>
        @endif
        <div class="text-center mt-4">
            <a href="{{ Auth::guard('admin')->check() ? route('admin.dashboard') : route('doctor.dashboard') }}" 
               class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</div>
@endsection
