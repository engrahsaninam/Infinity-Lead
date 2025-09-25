@extends('layout.master')
@section('content')
    <section id="hero" class="hero section dark-background">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7 text-center" data-aos="zoom-out">
                    <h1 class="text-uppercase">Lead Finder</h1>
                    <p>
                        Use our Lead Finder to export leads with their email addresses as a .csv file from LinkedIn Sales Navigator.
                    </p>
                </div>

            </div>
        </div>
    </section>
    <section id="skills" class="skills section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row p-5 align-items-center">
                <div class="col-lg-7 pt-4 pt-lg-0 content">
                    <h6 class="text-decoration-underline">1</h6>
                    <h3 class="mb-5 text-secondary">Sign up for Infinity Lead</h3>
                    <div class="steps-content mb-5">
                        <div class="steps-features">
                            <div class="feature-item mt-3">
                                <p>
                                    Sign up for an Infinity Lead account.
                                </p>
                            </div>
                            <div class="feature-item mt-3">
                                <p>
                                    You need to be logged into your account to use our extension.

                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="pricing">
                        <a href="#" class="buy-btn mb-4">Sign up free <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-5 d-flex align-items-center justify-content-end">
                    <img src="{{url('assets/img/lead-finder-step1.png')}}" class="img-fluid" alt="" width="">
                </div>

            </div>
        </div>
    </section>
    <section id="skills" class="skills section">
        <div class="container" data-aos="fade-up" data-aos-delay="200">
            <div class="row p-5 align-items-center">
                <div class="col-lg-5 d-flex align-items-center justify-content-end">
                    <img src="{{url('assets/img/lead-finder-step2.png')}}" class="img-fluid" alt="" width="">
                </div>
                <div class="col-lg-7 pt-4 pt-lg-0 content">
                    <h6 class="text-decoration-underline">2</h6>
                    <h3 class="mb-5 text-secondary">Install our extension
                    </h3>
                    <div class="steps-content mb-5">
                        <div class="steps-features">
                            <div class="feature-item mt-3">
                                <p>
                                    Visit the Chrome Web Store and install our free extension.


                                </p>
                            </div>
                            <div class="feature-item mt-3">
                                <p>
                                    Our extension allows you to export leads from your LinkedIn Sales Navigator account.


                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="pricing">
                        <a href="#" class="buy-btn mb-4">Try for free <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
