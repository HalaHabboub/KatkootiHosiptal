@extends('layouts.app')

@section('title', 'Our Doctors')

@section('content')
<style>
    .card-doctor {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        height: 100%;
    }
    .card-doctor .header {
        position: relative;
        width: 100%;
    }
    .card-doctor .header img {
        width: 100%;
        object-fit: cover;
    }
    .card-doctor .body {
        padding: 1rem;
        width: 100%;
    }
    .col-md-6.col-lg-4.py-3 {
        display: flex;
        justify-content: center;
    }
</style>

<div class="page-banner overlay-dark bg-image" style="background-image: url({{ asset('assets/img/bg_image_1.jpg') }});">
    <div class="banner-section">
        <div class="container text-center wow fadeInUp">
            <nav aria-label="Breadcrumb">
                <ol class="breadcrumb breadcrumb-dark bg-transparent justify-content-center py-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Doctors</li>
                </ol>
            </nav>
            <h1 class="font-weight-normal">Our Doctors</h1>
        </div>
    </div>
</div>

<div class="page-section bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row">
                    @foreach($doctors as $doctor)
                    <div class="col-md-6 col-lg-4 py-3 wow zoomIn">
                        <div class="card-doctor">
                            <div class="header">
                                <img src="{{ asset('storage/' . $doctor->image) }}" alt="Doctor {{ $doctor->name }}">
                                <div class="meta">
                                    <a href="#" onclick="alert('Phone: {{ $doctor->phone }}')" title="{{ $doctor->phone }}">
                                        <span class="mai-call"></span>
                                    </a>
                                </div>
                            </div>
                            <div class="body">
                                <p class="text-xl mb-0">{{ $doctor->name }}</p>
                                <span class="text-sm text-grey">{{ $doctor->specialization }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/vendor/wow/wow.min.js') }}"></script>
<script>
    new WOW().init();
</script>
@endsection