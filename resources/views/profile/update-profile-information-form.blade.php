<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile - Katkooti Hospital</title>
    
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    
    <style>
        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .card-header {
            background: linear-gradient(45deg, #00d9a6, #00aeff);
            color: white;
        }
        .btn-primary {
            background: linear-gradient(45deg, #00d9a6, #00aeff);
            border: none;
        }
    </style>
</head>
<body>
    @include('components.patientNavbar')
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Update Profile Information</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group mb-3">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ auth()->user()->name ?? old('name') }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ auth()->user()->email ?? old('email') }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="phone">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="{{ auth()->user()->phone ?? old('phone') }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ auth()->user()->date_of_birth }}">
                            </div>

                            <div class="form-group mb-3">
                                <label for="gender">Gender</label>
                                <select class="form-control" id="gender" name="gender">
                                    <option value="male" {{ auth()->user()->gender == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ auth()->user()->gender == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="blood_group">Blood Group</label>
                                <select class="form-control" id="blood_group" name="blood_group">
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                                        <option value="{{ $group }}" {{ auth()->user()->blood_group == $group ? 'selected' : '' }}>
                                            {{ $group }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="existing_conditions">Existing Medical Conditions</label>
                                <textarea class="form-control" id="existing_conditions" name="existing_conditions" rows="3">{{ auth()->user()->existing_conditions }}</textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label for="current_medications">Current Medications</label>
                                <textarea class="form-control" id="current_medications" name="current_medications" rows="3">{{ auth()->user()->current_medications }}</textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label for="allergies">Allergies</label>
                                <textarea class="form-control" id="allergies" name="allergies" rows="3">{{ auth()->user()->allergies }}</textarea>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
