<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Registration - Katkooti Hospital</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/maicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/animate/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    
    <style>
        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background: linear-gradient(45deg, #00d9a6, #00aeff);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        .form-control:focus {
            border-color: #00d9a6;
            box-shadow: 0 0 0 0.2rem rgba(0, 217, 166, 0.25);
        }
        .btn-primary {
            background: linear-gradient(45deg, #00d9a6, #00aeff);
            border: none;
            padding: 10px 30px;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #00aeff, #00d9a6);
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Katkooti Logo" style="height: 40px;">
                    <span>Katkooti Hospital</span>
                </a>
            </div>
        </nav>
    </header>

    <div class="page-section">
        <div class="container">
            <h2 class="text-center wow fadeInUp mb-5">Complete Your Profile</h2>

            <form class="main-form" action="{{ route('profile.store') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Left Column - Personal Information -->
                    <div class="col-md-6 mb-4">
                        <div class="card wow fadeInLeft">
                            <div class="card-header">
                                <h5 class="mb-0">Personal Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="tel" name="phone" class="form-control" required 
                                           pattern="[0-9]{10}" 
                                           title="Please enter a valid 10-digit phone number"
                                           placeholder="Enter your phone number">
                                </div>
                                <div class="form-group">
                                    <label>Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select name="gender" class="form-control" required>
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Blood Group</label>
                                    <select name="blood_group" class="form-control" required>
                                        <option value="">Select Blood Group</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Medical History -->
                    <div class="col-md-6 mb-4">
                        <div class="card wow fadeInRight">
                            <div class="card-header">
                                <h5 class="mb-0">Medical History</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Existing Medical Conditions</label>
                                    <textarea name="existing_conditions" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Current Medications</label>
                                    <textarea name="current_medications" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Allergies</label>
                                    <textarea name="allergies" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-primary wow zoomIn">Save Profile</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/wow/wow.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme.js') }}"></script>
    <script>
        new WOW().init();
    </script>
</body>
</html>
