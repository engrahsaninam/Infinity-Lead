<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

        <a href="{{url('/')}}" class="logo d-flex align-items-center me-auto">
            <h1 class="sitename">
                <img src="{{url('logo white.png')}}" alt="" height="50">
            </h1>
        </a>

        <nav id="navmenu" class="navmenu" >
            <ul>
                <li class="dropdown ">
                    <a href="#" @class(in_array(Route::currentRouteName(), array('campaigns', 'sales.crm', 'lead.finder', 'email.verifier', 'email.finder', )) ? 'active' : '')><span>Products</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                    <ul class="multi-column-dropdown">
                        <div class="container p-md-5">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <a href="{{url('campaigns')}}" class="item">
                                        <img src="{{url('assets/icons/cold.png')}}" >
                                        <div>
                                            <h4>Campaigns</h4>
                                            <p>Send cold emails at scale by creating campaigns. Create email sequences with automatic follow-ups.</p>
                                        </div>
                                    </a>
                                    <a href="{{url('lead-finder')}}" class="item">
                                        <img src="{{url('assets/icons/cold.png')}}" >
                                        <div>
                                            <h4>Lead Finder</h4>
                                            <p>Export leads with their email addresses as a .csv file from LinkedIn Sales Navigator.</p>
                                        </div>
                                    </a>
                                    <a href="{{url('email-verifier')}}" class="item">
                                        <img src="{{url('assets/icons/cold.png')}}" >
                                        <div>
                                            <h4> Email Verifier</h4>
                                            <p>Verify if an email address is valid. Sending emails to invalid addresses will hurt your deliverability.</p>
                                        </div>
                                    </a>

                                </div>
                                <div class="col-md-6 col-12">
                                    <a href="{{url('sales-crm')}}" class="item">
                                        <img src="{{url('assets/icons/cold.png')}}" >
                                        <div>
                                            <h4>Sales CRM</h4>
                                            <p>Easy-to-use sales CRM software. Create custom columns, forecast future revenue and manage leads.</p>
                                        </div>
                                    </a>
                                    <a href="{{url('email-finder')}}" class="item">
                                        <img src="{{url('assets/icons/cold.png')}}" >
                                        <div>
                                            <h4> Email Finder</h4>
                                            <p>
                                                Enter the name and website of someone, and our algorithm will find their email address.
                                            </p>
                                        </div>
                                    </a>

                                    <a href="" class="item d-none">
                                        <img src="{{url('assets/icons/question.png')}}" >
                                        <div>
                                            <p>
                                                Are you looking for an email warm-up feature? Read our article that explains why email warm-up tools can actually hurt your deliverability.
                                                <b>Read more</b>
                                            </p>
                                        </div>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </ul>
                </li>


                <li><a href="{{url('pricing')}}" class="{{Route::currentRouteName() === 'pricing' ? 'active' : ''}}">Pricing</a></li>
                <li><a href="{{url('about')}}" class="{{Route::currentRouteName() === 'about' ? 'active' : ''}}">About</a></li>
                <li><a href="{{url('contact')}}" class="{{Route::currentRouteName() === 'contact' ? 'active' : ''}}">Contact</a></li>

            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <a class="btn-getstarted" href="{{url('login')}}">LOGIN</a>
        <a class="btn-getstarted bg-white text-primary ms-2" href="{{url('user-register')}}">SIGN UP FREE <i class="bi bi-arrow-right"></i></a>
    </div>
</header>
<style>
    .header .logo img{
        max-height: 64px !important;
        margin-right: 8px;
    }
</style>