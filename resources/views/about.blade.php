@extends('layout.master')
@section('content')
    <section id="hero" class="hero section dark-background">
        <div class="container">
            <div class="row pt-4 justify-content-center">
                <div class="col-md-7" data-aos="zoom-out">
                    <h1 >At InfinityLead, we believe in making cold email simple</h1>
                    <p >The world of sales and cold email can be complicated. We believe that it doesn’t have to be. This belief guides how we build our product.</p>
                </div>
            </div>
        </div>
    </section>
    <section id="team" class="team section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Team</h2>
            <p>We are a small team of dedicated entrepreneurs, salespeople and developers. Our HQ is located in San Juan, Puerto Rico.</p>
        </div>

        <div class="container">

            <div class="row gy-4">
                @foreach ($teams as $team)
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="team-member d-flex align-items-start">
                            <div class="pic"><img src="{{ $team->profile }}" class="img-fluid" alt=""></div>
                            <div class="member-info">
                                <h4>{{ $team->name }}</h4>
                                <span>{{ $team->title }}</span>
                                <div class="social">
                                    <a href="{{ $team->twitter_url }}"><i class="bi bi-twitter-x"></i></a>
                                    <a href="{{ $team->facebook_url }}"><i class="bi bi-facebook"></i></a>
                                    <a href="{{ $team->instagram_url }}"><i class="bi bi-instagram"></i></a>
                                    <a href="{{ $team->linkedin_url }}"> <i class="bi bi-linkedin"></i> </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
