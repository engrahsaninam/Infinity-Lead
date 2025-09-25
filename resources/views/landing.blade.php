@extends('layout.master')
@section('content')
<section id="hero" class="hero section dark-background">
    <div class="container">
        <div class="row pt-4">
            <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="zoom-out">
                <h1 class="no-bg bg-white">Cold Email Infrastructure That Doesn’t Get You Iced</h1>
                <p>Send More Emails, Get More Replies, and Book More Meetings.</p>
                <div class="d-flex">
                    <a href="#about" class="btn-get-started">START FOR FREE</a>
                    <a href="https://youtu.be/e7domKfqRDM?si=jW0YJu6Y1Akx3ozx" target="_blank" class="glightbox- btn-watch-video d-flex align-items-center"><i class="bi bi-play-circle"></i><span>Watch Video</span></a>
                </div>
            </div>
            <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-out" data-aos-delay="200">
                <img src="assets/img/hero-img.png" class="img-fluid animated" alt="">
            </div>
        </div>

        <h4 class="text-center no-bg bg-white">TRUSTED BY 2,000+ BUSINESSES</h4>
        <div class="swiper init-swiper bg-light rounded-5 p-3">
            <script type="application/json" class="swiper-config">
                {
                  "loop": true,
                  "speed": 600,
                  "autoplay": {
                    "delay": 5000
                  },
                  "slidesPerView": "auto",
                  "pagination": {
                    "el": ".swiper-pagination",
                    "type": "bullets",
                    "clickable": true
                  },
                  "breakpoints": {
                    "320": {
                      "slidesPerView": 2,
                      "spaceBetween": 40
                    },
                    "480": {
                      "slidesPerView": 3,
                      "spaceBetween": 60
                    },
                    "640": {
                      "slidesPerView": 4,
                      "spaceBetween": 80
                    },
                    "992": {
                      "slidesPerView": 5,
                      "spaceBetween": 120
                    },
                    "1200": {
                      "slidesPerView": 6,
                      "spaceBetween": 120
                    }
                  }
                }
            </script>
            <div class="swiper-wrapper align-items-center">
                <div class="swiper-slide"><img src="assets/img/clients/clients-1.webp" class="img-fluid" alt=""></div>
                <div class="swiper-slide"><img src="assets/img/clients/clients-2.webp" class="img-fluid" alt=""></div>
                <div class="swiper-slide"><img src="assets/img/clients/clients-3.webp" class="img-fluid" alt=""></div>
                <div class="swiper-slide"><img src="assets/img/clients/clients-4.webp" class="img-fluid" alt=""></div>
                <div class="swiper-slide"><img src="assets/img/clients/clients-5.webp" class="img-fluid" alt=""></div>
                <div class="swiper-slide"><img src="assets/img/clients/clients-6.webp" class="img-fluid" alt=""></div>
                <div class="swiper-slide"><img src="assets/img/clients/clients-7.webp" class="img-fluid" alt=""></div>
                <div class="swiper-slide"><img src="assets/img/clients/clients-8.webp" class="img-fluid" alt=""></div>
            </div>
        </div>


    </div>

</section>




<section id="work-process" class="work-process section">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>WHY Infinity Lead?</h2>
        <p>Never land in spam again</p>
    </div><!-- End Section Title -->

    <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-5">

            <div class="col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="steps-item">
                    <div class="steps-image">
                        <img src="assets/img/steps/img.png" alt="Step 1" class="img-fluid" loading="lazy">
                    </div>
                    <div class="steps-content">
                        <div class="steps-number">1</div>
                        <h3>Access 200M verified leads on LinkedIn.</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="steps-item">
                    <div class="steps-image">
                        <img src="assets/img/steps/img_1.png" alt="Step 2" class="img-fluid" loading="lazy">
                    </div>
                    <div class="steps-content">
                        <div class="steps-number">2</div>
                        <h3>Use AI to personalize emails.</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3" data-aos="fade-up" data-aos-delay="400">
                <div class="steps-item">
                    <div class="steps-image">
                        <img src="assets/img/steps/img_2.png" alt="Step 3" class="img-fluid" loading="lazy">
                    </div>
                    <div class="steps-content">
                        <div class="steps-number">3</div>
                        <h3>Send unlimited emails and avoid spam.</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3" data-aos="fade-up" data-aos-delay="500">
                <div class="steps-item">
                    <div class="steps-image">
                        <img src="assets/img/steps/img_3.png" alt="Step 4" class="img-fluid" loading="lazy">
                    </div>
                    <div class="steps-content">
                        <div class="steps-number">4</div>
                        <h3>Consistently book more meetings.</h3>
                    </div>
                </div>
            </div>


        </div>
    </div>
</section>


<section id="skills" class="skills section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row p-5 align-items-center">
            <div class="col-lg-7 pt-4 pt-lg-0 content">
                <h6>Leverage AI</h6>
                <h3 class="mb-5 text-secondary">Replace spintax with GPT-4o</h3>
                <div class="steps-content mb-5">
                    <div class="steps-features">
                        <div class="feature-item mt-3">
                            <p>We have integrated OpenAI's GPT-4o into InfinityLead so that it slightly rewrites each email that you send so that none of your leads receive the exact same email copy.</p>
                        </div>
                        <div class="feature-item mt-3">
                            <p>This helps prevent your emails from going to spam. If you send the same email too many times, then email service providers will mark your emails as spam.</p>
                        </div>
                    </div>
                </div>
                <div class="pricing">
                    <a href="#" class="buy-btn mb-4">Get started for free <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-lg-5 d-flex align-items-center justify-content-end">
                <img src="{{url('assets/img/features/img.png')}}" class="img-fluid" alt="" width="">
            </div>

        </div>
    </div>
</section>

<section id="skills" class="skills section light-background">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row p-5 align-items-center">
            <div class="col-lg-5 d-flex align-items-center justify-content-end">
                <img src="{{url('assets/img/features/img_1.png')}}" class="img-fluid" alt="" width="">
            </div>
            <div class="col-lg-7 pt-4 pt-lg-0 content">
                <h6>Get more responses</h6>
                <h3 class="mb-5 text-secondary">Send emails with automatic follow-ups</h3>
                <div class="steps-content mb-5">
                    <div class="steps-features">
                        <div class="feature-item mt-3">
                            <p>
                                Send cold email sequences by creating campaigns. You can choose to either send emails one at a time, or at scale by uploading a list of leads.
                            </p>
                        </div>
                        <div class="feature-item mt-3">
                            <p>
                                Increase your sales with automatic follow-up emails. Never forget to follow-up with a prospect again.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="pricing">
                    <a href="#" class="buy-btn mb-4">Get started for free <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>


        </div>
    </div>
</section>


<section id="services" class="services section ">
    <div class="container section-title" data-aos="fade-up">
        <h2 class="">Tired of your cold emails getting lost in the spam folder?</h2>
        <p class="ps-5 pe-5 ms-5 me-5">We set up rock-solid email infrastructure that gets your messages delivered and opened. Our proven system is designed to boost your response rates and help you turn those emails into valuable sales meetings.</p>
    </div>

    <div class="container">

        <div class="row gy-4">
            @foreach($features as $k=>$feature)
                <div class="col d-flex" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-item position-relative @if($k==2)active @endif" >
                        <div class="icon"><img src="{{url($feature['icon'])}}" class="icon"></div>
                        <h4><a href="" class="stretched-link">{{$feature['title']}}</a></h4>
                        <p>{{$feature['description']}}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>



<section id="faq-2" class="faq-2 section light-background">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>Frequently Asked Questions</h2>
    </div><!-- End Section Title -->

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-10">

                <div class="faq-container">
                    @foreach($faqs as $k=>$faq)
                        <div class="faq-item " data-aos="fade-up" data-aos-delay="200">
                            <i class="faq-icon bi bi-question-circle"></i>
                            <h3>{{$faq['q']}}</h3>
                            <div class="faq-content">
                                <p>
                                    {!! $faq['a'] !!}
                                </p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div>
                    @endforeach
                </div>

            </div>

        </div>

    </div>

</section><!-- /Faq 2 Section -->



<!-- Call To Action Section -->


<section id="testimonials" class="testimonials section">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>Testimonials</h2>
        <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p>
    </div><!-- End Section Title -->

    <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="swiper init-swiper">
            <script type="application/json" class="swiper-config">
                {
                  "loop": true,
                  "speed": 600,
                  "autoplay": {
                    "delay": 5000
                  },
                  "slidesPerView": "auto",
                  "pagination": {
                    "el": ".swiper-pagination",
                    "type": "bullets",
                    "clickable": true
                  }
                }
            </script>
            <div class="swiper-wrapper">

            @foreach ($testimonials as $testimonial)
            
                <div class="swiper-slide">
                    <div class="testimonial-item">
                        <img src="{{ $testimonial->profile }}" class="testimonial-img" alt="">
                        <h3>{{ $testimonial->name }}</h3>
                        <h4>{{ $testimonial->title }}</h4>
                        <div class="stars">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $testimonial->rating)
                                    <i class="bi bi-star-fill"></i>
                                @else
                                    <i class="bi bi-star"></i>
                                @endif
                            @endfor
                        </div></p>
                        <p>
                            <i class="bi bi-quote quote-icon-left"></i>
                            <span>
                                {{ $testimonial->feedback }}
                            </span>
                            <i class="bi bi-quote quote-icon-right"></i>
                        </p>
                    </div>
                </div>
            @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>

    </div>

</section>






<section id="about" class="about d-none section">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>Tired of your cold emails getting lost in the spam folder?</h2>
    </div><!-- End Section Title -->

    <div class="container">

        <div class="row gy-4">

            <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore
                    magna aliqua.
                </p>
                <ul>
                    <li><i class="bi bi-check2-circle"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo consequat.</span></li>
                    <li><i class="bi bi-check2-circle"></i> <span>Duis aute irure dolor in reprehenderit in voluptate velit.</span></li>
                    <li><i class="bi bi-check2-circle"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo</span></li>
                </ul>
            </div>

            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                <p>Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. </p>
                <a href="#" class="read-more"><span>Read More</span><i class="bi bi-arrow-right"></i></a>
            </div>

        </div>

    </div>

</section><!-- /About Section -->
<section id="why-us" class="section d-none why-us light-background" data-builder="section">

    <div class="container-fluid">

        <div class="row gy-4">

            <div class="col-lg-7 d-flex flex-column justify-content-center order-2 order-lg-1">

                <div class="content px-xl-5" data-aos="fade-up" data-aos-delay="100">
                    <h3><span>Eum ipsam laborum deleniti </span><strong>velit pariatur architecto aut nihil</strong></h3>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Duis aute irure dolor in reprehenderit
                    </p>
                </div>

                <div class="faq-container px-xl-5" data-aos="fade-up" data-aos-delay="200">

                    <div class="faq-item faq-active">

                        <h3><span>01</span> Non consectetur a erat nam at lectus urna duis?</h3>
                        <div class="faq-content">
                            <p>Feugiat pretium nibh ipsum consequat. Tempus iaculis urna id volutpat lacus laoreet non curabitur gravida. Venenatis lectus magna fringilla urna porttitor rhoncus dolor purus non.</p>
                        </div>
                        <i class="faq-toggle bi bi-chevron-right"></i>
                    </div><!-- End Faq item-->

                    <div class="faq-item">
                        <h3><span>02</span> Feugiat scelerisque varius morbi enim nunc faucibus a pellentesque?</h3>
                        <div class="faq-content">
                            <p>Dolor sit amet consectetur adipiscing elit pellentesque habitant morbi. Id interdum velit laoreet id donec ultrices. Fringilla phasellus faucibus scelerisque eleifend donec pretium. Est pellentesque elit ullamcorper dignissim. Mauris ultrices eros in cursus turpis massa tincidunt dui.</p>
                        </div>
                        <i class="faq-toggle bi bi-chevron-right"></i>
                    </div><!-- End Faq item-->

                    <div class="faq-item">
                        <h3><span>03</span> Dolor sit amet consectetur adipiscing elit pellentesque?</h3>
                        <div class="faq-content">
                            <p>Eleifend mi in nulla posuere sollicitudin aliquam ultrices sagittis orci. Faucibus pulvinar elementum integer enim. Sem nulla pharetra diam sit amet nisl suscipit. Rutrum tellus pellentesque eu tincidunt. Lectus urna duis convallis convallis tellus. Urna molestie at elementum eu facilisis sed odio morbi quis</p>
                        </div>
                        <i class="faq-toggle bi bi-chevron-right"></i>
                    </div><!-- End Faq item-->

                </div>

            </div>

            <div class="col-lg-5 order-1 order-lg-2 why-us-img">
                <img src="assets/img/why-us.png" class="img-fluid" alt="" data-aos="zoom-in" data-aos-delay="100">
            </div>
        </div>

    </div>

</section><!-- /Why Us Section -->
<section id="portfolio" class="portfolio d-none section">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>Portfolio</h2>
        <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p>
    </div><!-- End Section Title -->

    <div class="container">

        <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">

            <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
                <li data-filter="*" class="filter-active">All</li>
                <li data-filter=".filter-app">App</li>
                <li data-filter=".filter-product">Card</li>
                <li data-filter=".filter-branding">Web</li>
            </ul><!-- End Portfolio Filters -->

            <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
                    <img src="assets/img/portfolio/portfolio-portrait-1.webp" class="img-fluid" alt="">
                    <div class="portfolio-info">
                        <h4>App 1</h4>
                        <p>Lorem ipsum, dolor sit</p>
                        <a href="assets/img/portfolio/portfolio-portrait-1.webp" title="App 1" data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                        <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                    </div>
                </div><!-- End Portfolio Item -->

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
                    <img src="assets/img/portfolio/portfolio-1.webp" class="img-fluid" alt="">
                    <div class="portfolio-info">
                        <h4>Product 1</h4>
                        <p>Lorem ipsum, dolor sit</p>
                        <a href="assets/img/portfolio/portfolio-1.webp" title="Product 1" data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                        <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                    </div>
                </div><!-- End Portfolio Item -->

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
                    <img src="assets/img/portfolio/portfolio-3.webp" class="img-fluid" alt="">
                    <div class="portfolio-info">
                        <h4>Branding 1</h4>
                        <p>Lorem ipsum, dolor sit</p>
                        <a href="assets/img/portfolio/portfolio-3.webp" title="Branding 1" data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                        <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                    </div>
                </div><!-- End Portfolio Item -->

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
                    <img src="assets/img/portfolio/portfolio-4.webp" class="img-fluid" alt="">
                    <div class="portfolio-info">
                        <h4>App 2</h4>
                        <p>Lorem ipsum, dolor sit</p>
                        <a href="assets/img/portfolio/portfolio-4.webp" title="App 2" data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                        <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                    </div>
                </div><!-- End Portfolio Item -->

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
                    <img src="assets/img/portfolio/portfolio-portrait-2.webp" class="img-fluid" alt="">
                    <div class="portfolio-info">
                        <h4>Product 2</h4>
                        <p>Lorem ipsum, dolor sit</p>
                        <a href="assets/img/portfolio/portfolio-portrait-2.webp" title="Product 2" data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                        <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                    </div>
                </div><!-- End Portfolio Item -->

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
                    <img src="assets/img/portfolio/portfolio-portrait-3.webp" class="img-fluid" alt="">
                    <div class="portfolio-info">
                        <h4>Branding 2</h4>
                        <p>Lorem ipsum, dolor sit</p>
                        <a href="assets/img/portfolio/portfolio-portrait-3.webp" title="Branding 2" data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                        <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                    </div>
                </div><!-- End Portfolio Item -->

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
                    <img src="assets/img/portfolio/portfolio-7.webp" class="img-fluid" alt="">
                    <div class="portfolio-info">
                        <h4>App 3</h4>
                        <p>Lorem ipsum, dolor sit</p>
                        <a href="assets/img/portfolio/portfolio-7.webp" title="App 3" data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                        <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                    </div>
                </div><!-- End Portfolio Item -->

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
                    <img src="assets/img/portfolio/portfolio-8.webp" class="img-fluid" alt="">
                    <div class="portfolio-info">
                        <h4>Product 3</h4>
                        <p>Lorem ipsum, dolor sit</p>
                        <a href="assets/img/portfolio/portfolio-8.webp" title="Product 3" data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                        <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                    </div>
                </div><!-- End Portfolio Item -->

                <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
                    <img src="assets/img/portfolio/portfolio-9.webp" class="img-fluid" alt="">
                    <div class="portfolio-info">
                        <h4>Branding 3</h4>
                        <p>Lorem ipsum, dolor sit</p>
                        <a href="assets/img/portfolio/portfolio-9.webp" title="Branding 2" data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                        <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                    </div>
                </div><!-- End Portfolio Item -->

            </div><!-- End Portfolio Container -->

        </div>

    </div>

</section><!-- /Portfolio Section -->
<section id="recent-blog-postst" class="recent-blog-postst d-none section light-background">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>Recent Blog Posts</h2>
        <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p>
    </div><!-- End Section Title -->

    <div class="container">

        <div class="row gy-5">

            <div class="col-xl-4 col-md-6">
                <div class="post-item position-relative h-100" data-aos="fade-up" data-aos-delay="100">

                    <div class="post-img position-relative overflow-hidden">
                        <img src="assets/img/blog/blog-post-1.webp" class="img-fluid" alt="">
                        <span class="post-date">December 12</span>
                    </div>

                    <div class="post-content d-flex flex-column">

                        <h3 class="post-title">Eum ad dolor et. Autem aut fugiat debitis</h3>

                        <div class="meta d-flex align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person"></i> <span class="ps-2">Julia Parker</span>
                            </div>
                            <span class="px-3 text-black-50">/</span>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-folder2"></i> <span class="ps-2">Politics</span>
                            </div>
                        </div>

                        <hr>

                        <a href="blog-details.html" class="readmore stretched-link"><span>Read More</span><i class="bi bi-arrow-right"></i></a>

                    </div>

                </div>
            </div><!-- End post item -->

            <div class="col-xl-4 col-md-6">
                <div class="post-item position-relative h-100" data-aos="fade-up" data-aos-delay="200">

                    <div class="post-img position-relative overflow-hidden">
                        <img src="assets/img/blog/blog-post-2.webp" class="img-fluid" alt="">
                        <span class="post-date">July 17</span>
                    </div>

                    <div class="post-content d-flex flex-column">

                        <h3 class="post-title">Et repellendus molestiae qui est sed omnis</h3>

                        <div class="meta d-flex align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person"></i> <span class="ps-2">Mario Douglas</span>
                            </div>
                            <span class="px-3 text-black-50">/</span>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-folder2"></i> <span class="ps-2">Sports</span>
                            </div>
                        </div>

                        <hr>

                        <a href="blog-details.html" class="readmore stretched-link"><span>Read More</span><i class="bi bi-arrow-right"></i></a>

                    </div>

                </div>
            </div><!-- End post item -->

            <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="post-item position-relative h-100">

                    <div class="post-img position-relative overflow-hidden">
                        <img src="assets/img/blog/blog-post-3.webp" class="img-fluid" alt="">
                        <span class="post-date">September 05</span>
                    </div>

                    <div class="post-content d-flex flex-column">

                        <h3 class="post-title">Quia assumenda est et veritati tirana ploder</h3>

                        <div class="meta d-flex align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person"></i> <span class="ps-2">Lisa Hunter</span>
                            </div>
                            <span class="px-3 text-black-50">/</span>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-folder2"></i> <span class="ps-2">Economics</span>
                            </div>
                        </div>

                        <hr>

                        <a href="blog-details.html" class="readmore stretched-link"><span>Read More</span><i class="bi bi-arrow-right"></i></a>

                    </div>

                </div>
            </div><!-- End post item -->

        </div>

    </div>

</section><!-- /Recent Blog Postst Section -->
@endsection
