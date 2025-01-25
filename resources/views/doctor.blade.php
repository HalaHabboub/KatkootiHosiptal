@extends('layouts.app')

@section('title', 'Doctor Dashboard')

@section('content')
@include('components.doctorNavbar')

<div class="page-hero bg-image overlay-dark" style="background-image: url({{ asset('assets/img/bg_image_1.jpg') }});">
    <div class="hero-section">
        <div class="container text-center wow zoomIn">
            <span class="subhead">Providing Excellence in Pediatric Care</span>
            <h1 class="display-4">
                Welcome, Dr. {{ Auth::user()->name }}
            </h1>
        </div>
    </div>
</div>

<div class="page-section">
    <div class="container">
        <h2 class="text-center mb-4">Today's Appointments</h2>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appointment)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($appointment->date_time)->format('h:i A') }}</td>
                        <td>{{ $appointment->patient->name }}</td>
                        <td>
                            @php
                                $statusClass = match($appointment->status) {
                                    'confirmed' => 'status-confirmed',
                                    'pending' => 'status-pending',
                                    'cancelled' => 'status-cancelled',
                                    default => 'status-default'
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('appointment.details', ['appointmentId' => $appointment->appointment_id]) }}" 
                               class="btn btn-sm btn-primary">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection

