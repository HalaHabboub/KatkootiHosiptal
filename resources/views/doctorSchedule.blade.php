@extends('layouts.app')

@section('content')
<x-doctorNavbar />
<div class="doctor-schedule-wrapper">
    <div class="page-banner overlay-dark bg-image" style="background-image: url({{ asset('assets/img/bg_image_1.jpg') }});">
        <div class="banner-section">
            <div class="container text-center wow fadeInUp">
                <nav aria-label="Breadcrumb">
                    <ol class="breadcrumb breadcrumb-dark bg-transparent justify-content-center py-0 mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('doctor.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Schedule</li>
                    </ol>
                </nav>
                <h1 class="font-weight-normal">Dr. {{ Auth::user()->name }}'s Schedule</h1>
            </div>
        </div>
    </div>

    <div class="page-section">
        <div class="container">
            <div class="row">
                <!-- Doctor Info Card -->
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <img src="{{ asset('assets/img/doctors/doctor_1.jpg') }}" class="rounded-circle mb-3" width="120">
                            <h5>Dr. {{ Auth::user()->name }}</h5>
                            <p class="text-muted">{{ Auth::user()->department->name ?? 'Department' }}</p>
                            <div class="d-flex justify-content-between mt-3">
                                <span>Today's Patients</span>
                                <span class="badge badge-primary">{{ $todayAppointments ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>


                

                <!-- Weekly Calendar and Appointments -->
                <div class="col-md-9">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">This Week's Appointments</h5>
                        </div>
                        <div class="card-body">
                            @foreach($weekDays as $date => $appointments)
                                <div class="day-section mb-4">
                                    <h6>{{ Carbon\Carbon::parse($date)->format('l, M d') }}</h6>
                                    @if($appointments->count() > 0)
                                        @foreach($appointments as $appointment)
                                        <div class="time-slot status-{{ $appointment->status }}">
                                            <div class="row align-items-center">
                                                <div class="col-md-2">
                                                    <strong>{{ Carbon\Carbon::parse($appointment->date_time)->format('h:i A') }}</strong>
                                                </div>
                                                <div class="col-md-4">
                                                    <div>{{ $appointment->patient->name }}</div>
                                                    <small class="text-muted">{{ $appointment->type }}</small>
                                                </div>
                                                <div class="col-md-3">
                                                    <span class="badge badge-{{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>
                                                </div>
                                                <div class="col-md-3 btn-container">
                                                    <a href="{{ route('appointment.details', ['appointmentId' => $appointment->appointment_id]) }}" class="btn btn-sm btn-view-details">
                                                        View
                                                    </a>
                                                    <button class="btn btn-sm btn-view-details bg-success text-white" 
                                                        onclick="updateStatus('{{ $appointment->appointment_id }}', 'confirmed')">
                                                        Approve
                                                    </button>
                                                    <button class="btn btn-sm btn-view-details bg-danger text-white" 
                                                        onclick="updateStatus('{{ $appointment->appointment_id }}', 'cancelled')">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No appointments scheduled</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('head-scripts')
<script>
window.updateStatus = function(appointmentId, status) {
    if (!confirm('Are you sure you want to ' + status + ' this appointment?')) {
        return;
    }

    if (!appointmentId) {
        console.error('No appointment ID provided');
        return;
    }

    const token = document.querySelector('meta[name="csrf-token"]').content;
    const url = "{{ route('appointments.updateStatus', ['appointment' => ':id']) }}".replace(':id', appointmentId);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => {
        console.log('Response:', response); // Debug log
        return response.json();
    })
    .then(data => {
        console.log('Data:', data); // Debug log
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating appointment status');
    });
}
</script>
@endsection


