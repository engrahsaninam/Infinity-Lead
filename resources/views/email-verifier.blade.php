@extends('layout.master')
@section('content')
    <section id="hero" class="hero section dark-background">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7 text-center" data-aos="zoom-out">
                    <h1 class="text-uppercase">Free Email Verifier Tool</h1>
                    <p>
                        Verify if an email address is valid with our 99% accurate email verification tool.
                    </p>
                </div>

            </div>
        </div>
    </section>
    <section id="skills" class="skills section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row p-5 align-items-center">
                <div class="col-lg-7 pt-4 pt-lg-0 content">
                    <h6>REDUCE BOUNCE RATE</h6>
                    <h3 class="mb-5 text-secondary">Why do you need to verify emails?
                    </h3>
                    <div class="steps-content mb-5">
                        <div class="steps-features">
                            <div class="feature-item mt-3">
                                <p>
                                    If you send too many emails to invalid email addresses, then you will have a high bounce rate.

                                </p>
                            </div>
                            <div class="feature-item mt-3">
                                <p>
                                    A high bounce rate will hurt your sender reputation score, meaning that email service providers such as Gmail, Outlook and Yahoo will send your emails to spam.
                                </p>
                            </div>
                            <div class="feature-item mt-3">
                                <p>
                                    Use our free Email Verifier to validate email addresses before sending cold emails. This will reduce your bounce rate and keep your emails out of spam.

                                </p>
                            </div>

                        </div>
                    </div>
                    <div class="pricing">
                        <a href="#" class="buy-btn mb-4">Sign up free <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-5 d-flex align-items-center justify-content-end">
                    <img src="{{url('assets/img/email-verified-step1.png')}}" class="img-fluid" alt="" width="">
                </div>

            </div>
        </div>
    </section>

@endsection
