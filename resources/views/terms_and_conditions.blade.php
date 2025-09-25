@extends('layout.master')
@section('content')
    <section id="hero" class="hero section dark-background">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7 text-center" data-aos="zoom-out">
                    <h1 class="text-uppercase">Terms and Conditions</h1>
                    <h3 class="text-uppercase">Understand the rules of our website.
                    </h3>
                </div>

            </div>
        </div>
    </section>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <section id="blog-details" class="blog-details section">
                    <div class="container" data-aos="fade-up">
                        <article class="article">
                            <div class="article-content" data-aos="fade-up" data-aos-delay="100">
                                <div class="content">
                                    {!! $content !!}
                                </div>
                            </div>
                        </article>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
