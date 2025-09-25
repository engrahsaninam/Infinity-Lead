@extends('layout.master')
@section('content')
    <section id="pricing" class="pricing section light-background mt-5 ">
        <div class="container mt-5 section-title" data-aos="fade-up">
            <h2>Pricing</h2>
        </div>

        <div class="container">

            <div class="row gy-4">

                <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="100">
                    <div class="pricing-item">
                        <h3>Free</h3>
                        <h4><sup>$</sup>0<span> / month</span></h4>
                        <ul>
                            <li title="Number of sender email accounts that you can connect to your InfinityLead account. You can connect multiple sender email accounts to a single campaign to scale your outreach without going to spam."><img src="{{url('assets/icons/check.png')}}" alt=""> <span>1 email account</span></li>
                            <li title="Number of emails you can send from your InfinityLead account each month."><img src="{{url('assets/icons/check.png')}}" alt=""> <span>Unlimited emails</span></li>
                            <li title="Number of leads that can be assigned to you in the Sales CRM."><img src="{{url('assets/icons/check.png')}}" alt=""> <span>Unlimited active leads</span></li>
                            <li title="Number of searches that you can perform each month with our Lead Finder &amp; Email Finder tools (requires active ZeroBounce integration)."><img src="{{url('assets/icons/check.png')}}" alt=""> <span>Unlimited email searches</span></li>
                            <li title="Number of email verifications that you can perform each month using our Email Verifier tool (requires active ZeroBounce integration)."><img src="{{url('assets/icons/check.png')}}" alt=""> <span>Unlimited email verifications</span></li>
                            <li><img src="{{url('assets/icons/check.png')}}" alt=""> <span>Sales CRM</span></li>
                            <li><img src="{{url('assets/icons/check.png')}}" alt=""> <span>Lead Finder</span></li>
                            <li title="Automatically rewrites each email that you send so that none of your leads receive the exact same email copy. This helps prevent your emails from going to spam."><img src="{{url('assets/icons/check.png')}}" alt=""> <span>OpenAI GPT model (most recent version)</span></li>
                            <li class="na d-none"><i class="bi bi-x"></i> <span>Massa ultricies mi quis hendrerit</span></li>
                        </ul>

                        <a href="#" class="buy-btn w-100 text-center mb-4">Get started for free <i class="bi bi-arrow-right"></i></a>
                        <p class="m-0 text-center text-muted">No credit card required.</p>
                        <p class="m-0 text-center text-muted">Cancel anytime, no contracts.</p>
                    </div>
                </div>

                <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="200">
                    <div class="pricing-item featured">
                        <h3>Basic</h3>
                        <h4><sup>$</sup>37<span> / month</span></h4>
                        <p class="p-0 m-0">Per user, per month</p>
                        <p class="p-0 m-0">Billed monthly</p>
                        <ul>

                            <li title="Number of sender email accounts that you can connect to your InfinityLead account. You can connect multiple sender email accounts to a single campaign to scale your outreach without going to spam."><img src="{{url('assets/icons/check.png')}}" alt=""> <span> 3 email accounts</span></li>
                            <li title="Number of emails you can send from your InfinityLead account each month."><img src="{{url('assets/icons/check.png')}}" alt=""> <span>Unlimited emails </span></li>
                            <li title="Number of leads that can be assigned to you in the Sales CRM."><img src="{{url('assets/icons/check.png')}}" alt=""> <span> Unlimited active leads</span></li>
                            <li title="Number of searches that you can perform each month with our Lead Finder &amp; Email Finder tools (requires active ZeroBounce integration)."><img src="{{url('assets/icons/check.png')}}" alt=""> <span> Unlimited email searches</span></li>
                            <li title="Number of email verifications that you can perform each month using our Email Verifier tool (requires active ZeroBounce integration)."><img src="{{url('assets/icons/check.png')}}" alt=""> <span> Unlimited email verifications</span></li>
                            <li ><img src="{{url('assets/icons/check.png')}}" alt=""> <span> Sales CRM</span></li>
                            <li ><img src="{{url('assets/icons/check.png')}}" alt=""> <span> Lead Finder</span></li>
                            <li title="Automatically rewrites each email that you send so that none of your leads receive the exact same email copy. This helps prevent your emails from going to spam."><img src="{{url('assets/icons/check.png')}}" alt=""> <span> OpenAI GPT model (most recent version)</span></li>

                        </ul>
                        <a href="#" class="buy-btn w-100 text-center mb-4">Get started for free <i class="bi bi-arrow-right"></i></a>
                        <p class="m-0 text-center text-muted">No credit card required.</p>
                        <p class="m-0 text-center text-muted">Cancel anytime, no contracts.</p>
                    </div>
                </div><!-- End Pricing Item -->

                <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="pricing-item">
                        <h3>Professional</h3>
                        <h4><sup>$</sup>97<span> / month</span></h4>
                        <p class="p-0 m-0">Per user, per month</p>
                        <p class="p-0 m-0">Billed monthly</p>
                        <ul>
                            <li title="Number of sender email accounts that you can connect to your InfinityLead account. You can connect multiple sender email accounts to a single campaign to scale your outreach without going to spam."><img src="{{url('assets/icons/check.png')}}" alt=""> <span> Unlimited email accounts</span></li>
                            <li title="Number of emails you can send from your InfinityLead account each month."><img src="{{url('assets/icons/check.png')}}" alt=""> <span> Unlimited emails</span></li>
                            <li title="Number of leads that can be assigned to you in the Sales CRM."><img src="{{url('assets/icons/check.png')}}" alt=""> <span> Unlimited active leads</span></li>
                            <li title="Number of searches that you can perform each month with our Lead Finder &amp; Email Finder tools (requires active ZeroBounce integration)."><img src="{{url('assets/icons/check.png')}}" alt=""> <span> Unlimited email searches</span></li>
                            <li title="Number of email verifications that you can perform each month using our Email Verifier tool (requires active ZeroBounce integration)."><img src="{{url('assets/icons/check.png')}}" alt=""> <span> Unlimited email verifications</span></li>
                            <li ><img src="{{url('assets/icons/check.png')}}" alt=""> <span> Sales CRM</span></li>
                            <li ><img src="{{url('assets/icons/check.png')}}" alt=""> <span> Lead Finder</span></li>
                            <li title="Automatically rewrites each email that you send so that none of your leads receive the exact same email copy. This helps prevent your emails from going to spam."><img src="{{url('assets/icons/check.png')}}" alt=""> <span> OpenAI GPT model (most recent version)</span></li>
                        </ul>

                        <a href="#" class="buy-btn w-100 text-center mb-4">Get started for free <i class="bi bi-arrow-right"></i></a>
                        <p class="m-0 text-center text-muted">No credit card required.</p>
                        <p class="m-0 text-center text-muted">Cancel anytime, no contracts.</p>

                    </div>
                </div><!-- End Pricing Item -->

            </div>

        </div>

    </section><!-- /Pricing Section -->
@endsection
