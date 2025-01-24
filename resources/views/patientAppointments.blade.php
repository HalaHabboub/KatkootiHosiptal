@extends('layouts.app')

@section('title', 'My Appointments')

@section('content')
<div class="page-banner overlay-dark bg-image" style="background-image: url(../assets/img/bg_image_1.jpg);">
    <div class="banner-section">
        <div class="container text-center wow fadeInUp">
            <nav aria-label="Breadcrumb">
                <ol class="breadcrumb breadcrumb-dark bg-transparent justify-content-center py-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Appointments</li>
                </ol>
            </nav>
            <h1 class="font-weight-normal">Manage Your Appointments</h1>
        </div>
    </div>
</div>

<div class="page-section">
    <div class="container">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card bg-light">
                    <div class="card-body text-center">
                        <h3 class="text-primary">{{ $stats['total'] }}</h3>
                        <p class="mb-0">Total</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-light">
                    <div class="card-body text-center">
                        <h3 class="text-info">{{ $stats['upcoming'] }}</h3>
                        <p class="mb-0">Upcoming</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-light">
                    <div class="card-body text-center">
                        <h3 class="text-success">{{ $stats['completed'] }}</h3>
                        <p class="mb-0">Completed</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-light">
                    <div class="card-body text-center">
                        <h3 class="text-danger">{{ $stats['cancelled'] }}</h3>
                        <p class="mb-0">Cancelled</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Doctor</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appointment)
                    <tr>
                        <td>
                            <strong>{{ $appointment->date_time->format('M d, Y') }}</strong><br>
                            <small class="text-muted">{{ $appointment->date_time->format('h:i A') }}</small>
                        </td>
                        <td>
                            {{ $appointment->doctor->name }}
                        </td>
                        <td>
                            <span class="status-badge badge-{{ $appointment->status }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </td>
                        <td>
                            @if($appointment->status !== 'cancelled' && $appointment->date_time > now())
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="showCancelModal('{{ $appointment->appointment_id }}')">
                                    Cancel
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Book New Appointment Form -->
        <div class="mt-5">
            <h3>Book New Appointment</h3>
            <form action="{{ route('appointments.store') }}" method="POST" class="mt-4">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Select Department and Doctor</label>
                            <div class="nested-dropdown">
                                <select class="form-control" id="nested-select" name="doctor_id" required>
                                    <option value="">Select a Department and Doctor</option>
                                    @foreach($departments as $department)
                                        <optgroup label="{{ $department->name }}">
                                            @forelse($department->doctors->where('status', 'active') as $doctor)
                                                <option value="{{ $doctor->doctor_id }}">
                                                    Dr. {{ $doctor->name }} - {{ $doctor->specialization }}
                                                </option>
                                            @empty
                                                <option value="" disabled>No doctors available</option>
                                            @endforelse
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Appointment Date & Time</label>
                            <input type="datetime-local" name="date_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label>Message (Optional)</label>
                            <textarea name="message" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Book Appointment</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="cancelForm" action="" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Reason for cancellation</label>
                        <textarea name="reason" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Confirm Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Replace existing dropdown styles with: */
.nested-dropdown select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background: #fff url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3E%3Cpath fill='%23343a40' d='M2 0L0 2h4zm0 5L0 3h4z'/%3E%3C/svg%3E") no-repeat right .75rem center;
    background-size: 8px 10px;
    cursor: pointer;
}

.nested-dropdown select:focus {
    outline: none;
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.nested-dropdown select optgroup {
    font-weight: bold;
    background-color: #f8f9fa;
    padding: 8px;
}

.nested-dropdown select option {
    padding: 8px;
    background-color: #fff;
}

.nested-dropdown select option:hover,
.nested-dropdown select option:focus {
    background-color: #007bff;
    color: #fff;
}

.nested-dropdown select optgroup option {
    padding-left: 20px;
}
</style>
@endpush

@push('scripts')
<script>
function showCancelModal(appointmentId) {
    const form = document.getElementById('cancelForm');
    form.action = `/appointments/${appointmentId}/cancel`;
    $('#cancelModal').modal('show');
}
</script>
@endpush