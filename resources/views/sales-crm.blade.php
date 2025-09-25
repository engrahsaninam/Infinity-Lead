@extends('layout.master')
@section('content')
    <section id="hero" class="hero section dark-background">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7 text-center" data-aos="zoom-out">
                    <h1 class="text-uppercase">Sales CRM</h1>
                    <p>
                        Easy-to-use sales CRM software. Create custom columns, forecast future pipeline revenue, and make notes about each lead.
                    </p>
                </div>

            </div>
        </div>
    </section>
    <section id="skills" class="skills section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row p-5 align-items-center">
                <div class="col-lg-7 pt-4 pt-lg-0 content">
                    <h6>STAY ORGANIZED</h6>
                    <h3 class="mb-5 text-secondary">Create columns</h3>
                    <div class="steps-content mb-5">
                        <div class="steps-features">
                            <div class="feature-item mt-3">
                                <p>
                                    Customize columns for your sales process.
                                </p>
                            </div>
                            <div class="feature-item mt-3">
                                <p>
                                    You can change the name and color of each column. You can also move leads between columns.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="pricing">
                        <a href="#" class="buy-btn mb-4">Sign up free <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-5 d-flex align-items-center justify-content-end">
                    <img src="{{url('assets/img/crm-step1.png')}}" class="img-fluid" alt="" width="">
                </div>

            </div>
        </div>
    </section>
    <section id="skills" class="skills section">
        <div class="container" data-aos="fade-up" data-aos-delay="200">
            <div class="row p-5 align-items-center">
                <div class="col-lg-5 d-flex align-items-center justify-content-end">
                    <img src="{{url('assets/img/crm-step2.png')}}" class="img-fluid" alt="" width="">
                </div>
                <div class="col-lg-7 pt-4 pt-lg-0 content">
                    <h6>FORECAST REVENUE</h6>
                    <h3 class="mb-5 text-secondary">See total pipeline value</h3>
                    <div class="steps-content mb-5">
                        <div class="steps-features">
                            <div class="feature-item mt-3">
                                <p>
                                    Infinity Lead’s Sales CRM shows you the total value of all of your leads in your pipeline.

                                </p>
                            </div>
                            <div class="feature-item mt-3">
                                <p>
                                    This allows you to predict how much revenue your company will make if you close all of your leads.

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
