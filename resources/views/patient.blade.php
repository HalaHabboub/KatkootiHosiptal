@extends('layouts.app')

@section('title', 'Katkooti Children\'s Hospital')

@section('content')
@include('components.patientNavbar')
 <!-- Landing page -->

<!-- Hero Section -->
<div class="page-hero bg-image overlay-dark" style="background-image: url({{ asset('assets/img/bg_image_1.jpg') }});">
    <div class="hero-section">
        <div class="container text-center wow zoomIn">
            <span class="subhead">Compassionate healthcare for your little ones</span>
            <h1 class="display-4">
                @auth('patient')
                Welcome, {{ Auth::guard('patient')->user()->name }}
                @else
                Welcome to Katkooti Children's Hospital
                @endauth
            </h1>
            @auth('patient')
            <a href="#" class="btn btn-primary">Book your appointment</a>
            @else
            <a href="{{ route('login') }}" class="btn btn-primary">Login to Book Appointment</a>
            @endauth
        </div>
    </div>
</div>

<!-- Services Section -->
<div class="bg-light">
    <div class="page-section py-3 mt-md-n5 custom-index">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-4 py-3 py-md-0">
                    <div class="card-service wow fadeInUp">
                        <div class="circle-shape bg-secondary text-white">
                            <span class="mai-calendar-outline"></span>
                        </div>
                        <p><span>Book</span> Appointments</p>
                    </div>
                </div>
                <div class="col-md-4 py-3 py-md-0">
                    <div class="card-service wow fadeInUp">
                        <div class="circle-shape bg-primary text-white">
                            <span class="mai-clipboard-outline"></span>
                        </div>
                        <p><span>Track</span> Appointments</p>
                    </div>
                </div>
                <div class="col-md-4 py-3 py-md-0">
                    <div class="card-service wow fadeInUp">
                        <div class="circle-shape bg-accent text-white">
                            <span class="mai-people-outline"></span>
                        </div>
                        <p><span>View</span> All Doctors</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Welcome Section -->
<div class="page-section pb-0">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 py-3 wow fadeInUp">
                <h1>Welcome to Katkooti Children's Hospital</h1>
                <p class="text-grey mb-4">
                    At Katkooti Children's Hospital, we are dedicated to providing compassionate and comprehensive
                    healthcare for your little ones. Our team of highly skilled and experienced doctors is committed to
                    ensuring the best possible care for your children. We offer a wide range of medical services, from
                    routine check-ups to specialized treatments, all in a child-friendly environment. Our
                    state-of-the-art facilities and advanced medical technologies enable us to deliver top-notch
                    healthcare services. We believe in a holistic approach to healthcare, focusing not only on treating
                    illnesses but also on promoting overall well-being and preventive care. Trust us to be your partner
                    in your child's health journey.
                </p>
                <a href="{{ route('about') }}" class="btn btn-primary">Learn More</a>
            </div>
            <div class="col-lg-6 wow fadeInRight" data-wow-delay="400ms">
                <div class="img-place custom-img-1">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="">
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/owl-carousel/js/owl.carousel.min.js') }}"></script>
<script src="../assets/js/jquery-3.5.1.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/owl-carousel/js/owl.carousel.min.js"></script>
<script src="../assets/vendor/wow/wow.min.js"></script>
<script src="../assets/js/theme.js"></script>
@endpush