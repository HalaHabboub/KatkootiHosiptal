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
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appointment)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($appointment->date_time)->format('h:i A') }}</td>
                        <td>{{ $appointment->patient->name }}</td>
                        <td>{{ $appointment->type }}</td>
                        <td>
                            <span class="badge badge-{{ $appointment->status == 'Confirmed' ? 'success' : 'warning' }}">
                                {{ $appointment->status }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary action-btn" 
                                data-toggle="modal" 
                                data-target="#detailsModal"
                                data-name="{{ $appointment->patient->name }}"
                                data-time="{{ \Carbon\Carbon::parse($appointment->date_time)->format('h:i A') }}"
                                data-type="{{ $appointment->type }}"
                                data-age="{{ $appointment->patient->age }}"
                                data-contact="{{ $appointment->patient->phone }}"
                                data-email="{{ $appointment->patient->email }}">
                                View Details
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Appointment Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Appointment Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Patient Name:</strong> <span id="modal-name"></span></p>
                <p><strong>Appointment Time:</strong> <span id="modal-time"></span></p>
                <p><strong>Appointment Type:</strong> <span id="modal-type"></span></p>
                <p><strong>Age:</strong> <span id="modal-age"></span></p>
                <p><strong>Contact:</strong> <span id="modal-contact"></span></p>
                <p><strong>Email:</strong> <span id="modal-email"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.action-btn').click(function() {
        $('#modal-name').text($(this).data('name'));
        $('#modal-time').text($(this).data('time'));
        $('#modal-type').text($(this).data('type'));
        $('#modal-age').text($(this).data('age'));
        $('#modal-contact').text($(this).data('contact'));
        $('#modal-email').text($(this).data('email'));
    });
});
</script>
@endsection