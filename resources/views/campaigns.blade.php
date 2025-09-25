@extends('layout.master')
@section('content')
    <section id="hero" class="hero section dark-background">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7 text-center" data-aos="zoom-out">
                    <h1 class="text-uppercase">Campaigns</h1>
                    <p>Send cold emails at scale by creating campaigns. Create email sequences with automatic follow-ups. Our email sending software is built for deliverability.</p>
                </div>

            </div>
        </div>
    </section>


    <section id="skills" class="skills section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row p-5 align-items-center">
                <div class="col-lg-7 pt-4 pt-lg-0 content">
                    <h6>Step 1</h6>
                    <h3 class="mb-5 text-secondary">Name your campaign</h3>
                    <div class="steps-content mb-5">
                        <div class="steps-features">
                            <div class="feature-item mt-3">
                                <p>
                                    After you click the “Create campaign” button, you can name it.
                                </p>
                            </div>
                            <div class="feature-item mt-3">
                                <p>
                                    Naming your campaign something relevant will help you identify it.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 d-flex align-items-center justify-content-end">
                    <img src="{{url('assets/img/camp-step1.png')}}" class="img-fluid" alt="" width="">
                </div>

            </div>
        </div>
    </section>
    <section id="skills" class="skills section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row p-5 align-items-center">
                <div class="col-lg-5 d-flex align-items-center justify-content-end">
                    <img src="{{url('assets/img/camp-step2.png')}}" class="img-fluid" alt="" width="">
                </div>
                <div class="col-lg-7 pt-4 pt-lg-0 content">
                    <h6>Step 2</h6>
                    <h3 class="mb-5 text-secondary">Select sender email accounts</h3>
                    <div class="steps-content mb-5">
                        <div class="steps-features">
                            <div class="feature-item mt-3">
                                <p>
                                    Infinity Lead allows you to connect an unlimited number of sender email accounts to a campaign.
                                </p>
                            </div>
                            <div class="feature-item mt-3">
                                <p>
                                    This allows you to scale your sending volume without going to spam (inbox rotation).

                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>





@endsection
