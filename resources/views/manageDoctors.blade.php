@extends('layouts.app')

@section('title', 'Manage Doctors')

@section('content')
@include('components.adminNavbar')

<div class="page-section">
    <div class="container">
        <h1 class="text-center mb-5">Manage Doctors</h1>
        
        <!-- Add New Doctor Button -->
        <div class="text-right mb-4">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addDoctorModal">
                Add New Doctor
            </button>
        </div>

        <!-- Doctors Table -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($doctors as $doctor)
                    <tr>
                        <td>{{ $doctor->name }}</td>
                        <td>{{ $doctor->email }}</td>
                        <td>{{ $doctor->department->name }}</td>
                        <td>{{ $doctor->phone }}</td>
                        <td>
                            <span class="badge {{ $doctor->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($doctor->status) }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editDoctorModal{{ $doctor->doctor_id }}">Edit</button>
                            <form action="{{ route('admin.doctors.delete', ['doctor' => $doctor->doctor_id]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Remove</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Doctor Modal -->
                    <div class="modal fade" id="editDoctorModal{{ $doctor->doctor_id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Doctor</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <form action="{{ route('admin.doctors.update', ['doctor' => $doctor->doctor_id]) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $doctor->name }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" name="email" class="form-control" value="{{ $doctor->email }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Department</label>
                                            <select name="department_id" class="form-control" required>
                                                @foreach($departments as $department)
                                                    <option value="{{ $department->department_id }}" 
                                                        {{ $doctor->department_id == $department->department_id ? 'selected' : '' }}>
                                                        {{ $department->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Specialization</label>
                                            <input type="text" name="specialization" class="form-control" 
                                                value="{{ $doctor->specialization }}" placeholder="e.g., Pediatric Surgery">
                                        </div>
                                        <div class="form-group">
                                            <label>Qualification</label>
                                            <input type="text" name="qualification" class="form-control" 
                                                value="{{ $doctor->qualification }}" placeholder="e.g., MBBS, MD, Ph.D.">
                                        </div>
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="text" name="phone" class="form-control" value="{{ $doctor->phone }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Profile Image</label>
                                            @if($doctor->image)
                                                <div class="mb-2">
                                                    <img src="{{ asset($doctor->image) }}" alt="Current profile image" width="100">
                                                </div>
                                            @endif
                                            <input type="file" name="image" class="form-control" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Update Doctor</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Doctor Modal -->
<div class="modal fade" id="addDoctorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Doctor</h5>
                <button type="button" class="close" data-dismiss="modal">&times;"></button>
            </div>
            <form action="{{ route('admin.doctors.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Profile Image</label>
                        <input type="file" name="image" class="form-control-file" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department_id" class="form-control" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->department_id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Specialization</label>
                        <input type="text" name="specialization" class="form-control" placeholder="e.g., Pediatric Surgery">
                    </div>
                    <div class="form-group">
                        <label>Qualification</label>
                        <input type="text" name="qualification" class="form-control" placeholder="e.g., MBBS, MD, Ph.D.">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Doctor</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
