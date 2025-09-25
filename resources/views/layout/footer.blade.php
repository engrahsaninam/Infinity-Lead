<footer id="footer" class="footer">
    <div class="footer-newsletter d-none">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-6">
                    <h4>Join Our Newsletter</h4>
                    <p>Subscribe to our newsletter and receive the latest news about our products and services!</p>
                    <form action="forms/newsletter.php" method="post" class="php-email-form">
                        <div class="newsletter-form"><input type="email" name="email"><input type="submit" value="Subscribe"></div>
                        <div class="loading">Loading</div>
                        <div class="error-message"></div>
                        <div class="sent-message">Your subscription request has been sent. Thank you!</div>
                    </form>
                </div>
            </div>
        </div>
    </div>




    <section id="call-to-action" class="call-to-action section " style="background: url('{{url('assets/img/gradient-cover.png')}}')">
        <div class="container" >
            <div class="row" >
                <div class="col-xl-9 text-center text-xl-start">
                    <h3>Ready to 10x your pipeline?</h3>
                    <p>Send your first cold email campaign today, risk-free.</p>
                </div>
                <div class="col-xl-3 cta-btn-container text-center">
                    <a class="cta-btn align-middle" href="#">Try for free <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>

        </div>

    </section><!-- /Call To Action Section -->



    <div class="container footer-top">
        <div class="row ">
            <div class="col-lg-4 col-md-6 footer-about">
                <a href="" class="d-flex align-items-center ml-0 pl-0">
                    <img src="{{url('logo.png')}}" alt="" width="200" class="p-0 img-fluid">
                </a>
                <div class="footer-contact pt-3">
                    <p>A108 Adam Street</p>
                    <p>128 City Road, London, EC1V 2NX</p>
                  <!--  <p class="mt-3"><strong>Phone:</strong> <span>+447479203293</span></p>
                    <p><strong>Email:</strong> <span>info@simplysendin.com</span></p> -->
                </div>
            </div>

            <div class="col-lg-2 col-md-3 footer-links">
                <h4>Useful Links</h4>
                <ul>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{url('')}}">Home</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{url('about')}}">About us</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{url('contact')}}">Contact</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{url('pricing')}}">Pricing</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-3 footer-links">
                <h4>Others Links</h4>
                <ul>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{url('privacy-policy')}}">Privacy Policy</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="{{url('terms-and-conditions')}}">Terms and Conditions</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-12">
                <h4>Follow Us</h4>
                <div class="social-links d-flex">
                 <!--   <a href=""><i class="bi bi-twitter-x"></i></a> 
                    <a href=""><i class="bi bi-facebook"></i></a> -->
                    <a href="https://www.instagram.com/infinitylead.ltd/"><i class="bi bi-instagram"></i></a>
                    <a href="https://www.linkedin.com/company/infinityleadio/about/"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>

        </div>
    </div>
    <div class="container copyright text-center mt-4">
        <p>© <span>Copyright</span> <strong class="px-1 sitename">INFINITY LEAD LTD</strong> <span>All Rights Reserved</span></p>
    </div>
</footer>
