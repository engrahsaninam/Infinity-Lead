<!doctype html >
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title> InfinityLead</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="InfinityLead | Email Marketing Tool" name="description" />
    <meta content="Sheriff" name="author" />
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ URL::asset('assets/css/preloader.min.css') }}" type="text/css" />
    <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css') }}" rel="stylesheet">
</head>
<body data-topbar="dark">
    <div id="layout-wrapper">
        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="index" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('assets/images/logo-sm.svg') }}" alt="" height="30">
                        </span>
                            <span class="logo-lg">
                            <img src="{{ URL::asset('assets/images/logo-sm.svg') }}" alt="" height="24"> <span class="logo-txt">Dason</span>
                        </span>
                        </a>

                        <a href="index" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('assets/images/logo-sm.svg') }}" alt="" height="30">
                        </span>
                            <span class="logo-lg">
                            <img src="{{ URL::asset('assets/images/logo-sm.svg') }}" alt="" height="24"> <span class="logo-txt">Dason</span>
                        </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>

                    <!-- App Search-->
                    <form class="app-search d-none d-lg-block">
                        <div class="position-relative">
                            <input type="text" class="form-control" placeholder="Search...">
                            <button class="btn btn-primary" type="button"><i class="bx bx-search-alt align-middle"></i></button>
                        </div>
                    </form>
                </div>

                <div class="d-flex">

                    <div class="dropdown d-inline-block d-lg-none ms-2">
                        <button type="button" class="btn header-item" id="page-header-search-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="search" class="icon-lg"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                             aria-labelledby="page-header-search-dropdown">

                            <form class="p-3">
                                <div class="form-group m-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search ..." aria-label="Search Result">

                                        <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="dropdown d-none d-sm-inline-block">
                        <button type="button" class="btn header-item waves-effect"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @switch(Session::get('lang'))
                                @case('ru')
                                    <img src="{{ URL::asset('/assets/images/flags/russia.jpg')}}" alt="Header Language" height="16">
                                    @break
                                @case('it')
                                    <img src="{{ URL::asset('/assets/images/flags/italy.jpg')}}" alt="Header Language" height="16">
                                    @break
                                @case('de')
                                    <img src="{{ URL::asset('/assets/images/flags/germany.jpg')}}" alt="Header Language" height="16">
                                    @break
                                @case('es')
                                    <img src="{{ URL::asset('/assets/images/flags/spain.jpg')}}" alt="Header Language" height="16">
                                    @break
                                @default
                                    <img src="{{ URL::asset('/assets/images/flags/us.jpg')}}" alt="Header Language" height="16">
                            @endswitch
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">

                            <!-- item-->
                            <a href="{{ url('index/en') }}" class="dropdown-item notify-item language" data-lang="eng">
                                <img src="{{ URL::asset ('/assets/images/flags/us.jpg') }}" alt="user-image" class="me-1" height="12"> <span class="align-middle">English</span>
                            </a>
                            <!-- item-->
                            <a href="{{ url('index/es') }}" class="dropdown-item notify-item language" data-lang="sp">
                                <img src="{{ URL::asset ('/assets/images/flags/spain.jpg') }}" alt="user-image" class="me-1" height="12"> <span class="align-middle">Spanish</span>
                            </a>

                            <!-- item-->
                            <a href="{{ url('index/de') }}" class="dropdown-item notify-item language" data-lang="gr">
                                <img src="{{ URL::asset ('/assets/images/flags/germany.jpg') }}" alt="user-image" class="me-1" height="12"> <span class="align-middle">German</span>
                            </a>

                            <!-- item-->
                            <a href="{{ url('index/it') }}" class="dropdown-item notify-item language" data-lang="it">
                                <img src="{{ URL::asset ('/assets/images/flags/italy.jpg') }}" alt="user-image" class="me-1" height="12"> <span class="align-middle">Italian</span>
                            </a>

                            <!-- item-->
                            <a href="{{ url('index/ru') }}" class="dropdown-item notify-item language" data-lang="ru">
                                <img src="{{ URL::asset ('/assets/images/flags/russia.jpg') }}" alt="user-image" class="me-1" height="12"> <span class="align-middle">Russian</span>
                            </a>
                        </div>
                    </div>

                    <div class="dropdown d-none d-sm-inline-block">
                        <button type="button" class="btn header-item" id="mode-setting-btn">
                            <i data-feather="moon" class="icon-lg layout-mode-dark"></i>
                            <i data-feather="sun" class="icon-lg layout-mode-light"></i>
                        </button>
                    </div>

                    <div class="dropdown d-none d-lg-inline-block ms-1">
                        <button type="button" class="btn header-item"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="grid" class="icon-lg"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <div class="p-2">
                                <div class="row g-0">
                                    <div class="col">
                                        <a class="dropdown-icon-item" href="#">
                                            <img src="{{ URL::asset('assets/images/brands/github.png') }}" alt="Github">
                                            <span>GitHub</span>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a class="dropdown-icon-item" href="#">
                                            <img src="{{ URL::asset('assets/images/brands/bitbucket.png') }}" alt="bitbucket">
                                            <span>Bitbucket</span>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a class="dropdown-icon-item" href="#">
                                            <img src="{{ URL::asset('assets/images/brands/dribbble.png') }}" alt="dribbble">
                                            <span>Dribbble</span>
                                        </a>
                                    </div>
                                </div>

                                <div class="row g-0">
                                    <div class="col">
                                        <a class="dropdown-icon-item" href="#">
                                            <img src="{{ URL::asset('assets/images/brands/dropbox.png') }}" alt="dropbox">
                                            <span>Dropbox</span>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a class="dropdown-icon-item" href="#">
                                            <img src="{{ URL::asset('assets/images/brands/mail_chimp.png') }}" alt="mail_chimp">
                                            <span>Mail Chimp</span>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a class="dropdown-icon-item" href="#">
                                            <img src="{{ URL::asset('assets/images/brands/slack.png') }}" alt="slack">
                                            <span>Slack</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item noti-icon position-relative" id="page-header-notifications-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="bell" class="icon-lg"></i>
                            <span class="badge bg-danger rounded-pill">5</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                             aria-labelledby="page-header-notifications-dropdown">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0"> Notifications </h6>
                                    </div>
                                    <div class="col-auto">
                                        <a href="#!" class="small text-reset text-decoration-underline"> Unread (3)</a>
                                    </div>
                                </div>
                            </div>
                            <div data-simplebar style="max-height: 230px;">
                                <a href="#!" class="text-reset notification-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <img src="assets/images/users/avatar-3.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">James Lemire</h6>
                                            <div class="font-size-13 text-muted">
                                                <p class="mb-1">It will seem like simplified English.</p>
                                                <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span>1 hours ago</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <a href="#!" class="text-reset notification-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-sm me-3">
                                        <span class="avatar-title bg-primary rounded-circle font-size-16">
                                            <i class="bx bx-cart"></i>
                                        </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Your order is placed</h6>
                                            <div class="font-size-13 text-muted">
                                                <p class="mb-1">If several languages coalesce the grammar</p>
                                                <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span>3 min ago</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <a href="#!" class="text-reset notification-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 avatar-sm me-3">
                                        <span class="avatar-title bg-success rounded-circle font-size-16">
                                            <i class="bx bx-badge-check"></i>
                                        </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Your item is shipped</h6>
                                            <div class="font-size-13 text-muted">
                                                <p class="mb-1">If several languages coalesce the grammar</p>
                                                <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span>3 min ago</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                                <a href="#!" class="text-reset notification-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <img src="assets/images/users/avatar-6.jpg" class="rounded-circle avatar-sm" alt="user-pic">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Salena Layfield</h6>
                                            <div class="font-size-13 text-muted">
                                                <p class="mb-1">As a skeptical Cambridge friend of mine occidental.</p>
                                                <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span>1 min ago</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="p-2 border-top d-grid">
                                <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
                                    <i class="mdi mdi-arrow-right-circle me-1"></i> <span>View More..</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item right-bar-toggle me-2">
                            <i data-feather="settings" class="icon-lg"></i>
                        </button>
                    </div>

                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user" src="@if (Auth::user()->avatar != ''){{ URL::asset('images/'. Auth::user()->avatar) }}@else{{ URL::asset('assets/images/users/avatar-1.png') }}@endif" alt="Header Avatar">
                            <span class="d-none d-xl-inline-block ms-1 fw-medium">{{ Auth::user()->name }}</span>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a class="dropdown-item" href="apps-contacts-profile"><i class="mdi mdi-face-profile font-size-16 align-middle me-1"></i> @lang('translation.Profile')</a>
                            <a class="dropdown-item" href="auth-lock-screen"><i class="mdi mdi-lock font-size-16 align-middle me-1"></i> @lang('translation.Lock_Screen')</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item " href="javascript:void();" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bx bx-power-off font-size-16 align-middle me-1"></i> <span key="t-logout">@lang('translation.Logout')</span></a>
                            <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </header>
        <div class="vertical-menu">

            <div data-simplebar class="h-100">

                <!--- Sidemenu -->
                <div id="sidebar-menu">
                    <!-- Left Menu Start -->
                    <ul class="metismenu list-unstyled" id="side-menu">
                        <li class="menu-title" data-key="t-menu">@lang('translation.Menu')</li>

                        <li>
                            <a href="index">
                                <i data-feather="home"></i>
                                <span class="badge rounded-pill bg-soft-success text-success float-end">9+</span>
                                <span data-key="t-dashboard">@lang('translation.Dashboards')</span>
                            </a>
                        </li>

                        <li class="menu-title" data-key="t-apps">@lang('translation.Apps')</li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="shopping-cart"></i>
                                <span data-key="t-ecommerce">@lang('translation.Ecommerce')</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="ecommerce-products" key="t-products">@lang('translation.Products')</a></li>
                                <li><a href="ecommerce-product-detail" data-key="t-product-detail">@lang('translation.Product_Detail')</a></li>
                                <li><a href="ecommerce-orders" data-key="t-orders">@lang('translation.Orders')</a></li>
                                <li><a href="ecommerce-customers" data-key="t-customers">@lang('translation.Customers')</a></li>
                                <li><a href="ecommerce-cart" data-key="t-cart">@lang('translation.Cart')</a></li>
                                <li><a href="ecommerce-checkout" data-key="t-checkout">@lang('translation.Checkout')</a></li>
                                <li><a href="ecommerce-shops" data-key="t-shops">@lang('translation.Shops')</a></li>
                                <li><a href="ecommerce-add-product" data-key="t-add-product">@lang('translation.Add_Product')</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="apps-chat">
                                <i data-feather="message-square"></i>
                                <span data-key="t-chat">@lang('translation.Chat')</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="mail"></i>
                                <span data-key="t-email">@lang('translation.Email')</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="apps-email-inbox" data-key="t-inbox">@lang('translation.Inbox')</a></li>
                                <li><a href="apps-email-read" data-key="t-read-email">@lang('translation.Read_Email')</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="apps-calendar">
                                <i data-feather="calendar"></i>
                                <span data-key="t-calendar">@lang('translation.Calendars')</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="users"></i>
                                <span data-key="t-contacts">@lang('translation.Contacts')</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="apps-contacts-grid" data-key="t-user-grid">@lang('translation.User_Grid')</a></li>
                                <li><a href="apps-contacts-list" data-key="t-user-list">@lang('translation.User_List')</a></li>
                                <li><a href="apps-contacts-profile" data-key="t-profile">@lang('translation.Profile')</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="trello"></i>
                                <span data-key="t-tasks">@lang('translation.Tasks')</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="tasks-list" key="t-task-list">@lang('translation.Task_List')</a></li>
                                <li><a href="tasks-kanban" key="t-kanban-board">@lang('translation.Kanban_Board')</a></li>
                                <li><a href="tasks-create" key="t-create-task">@lang('translation.Create_Task')</a></li>
                            </ul>
                        </li>

                        <li class="menu-title" data-key="t-pages">@lang('translation.Pages')</li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="layers"></i>
                                <span data-key="t-authentication">@lang('translation.Authentication')</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="auth-login" data-key="t-login">@lang('translation.Login')</a></li>
                                <li><a href="auth-register" data-key="t-register">@lang('translation.Register')</a></li>
                                <li><a href="auth-recoverpw" data-key="t-recover-password">@lang('translation.Recover_Password')</a></li>
                                <li><a href="auth-lock-screen" data-key="t-lock-screen">@lang('translation.Lock_Screen')</a></li>
                                <li><a href="auth-logout" data-key="t-logout">@lang('translation.Logout')</a></li>
                                <li><a href="auth-confirm-mail" data-key="t-confirm-mail">@lang('translation.Confirm_Mail')</a></li>
                                <li><a href="auth-email-verification" data-key="t-email-verification">@lang('translation.Email_verification')</a></li>
                                <li><a href="auth-two-step-verification" data-key="t-two-step-verification">@lang('translation.Two_step_verification')</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="file-text"></i>
                                <span data-key="t-pages">@lang('translation.Pages')</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="pages-starter" key="t-starter-page">@lang('translation.Starter_Page')</a></li>
                                <li><a href="pages-maintenance" key="t-maintenance">@lang('translation.Maintenance')</a></li>
                                <li><a href="pages-comingsoon" key="t-coming-soon">@lang('translation.Coming_Soon')</a></li>
                                <li><a href="pages-timeline" key="t-timeline">@lang('translation.Timeline')</a></li>
                                <li><a href="pages-faqs" key="t-faqs">@lang('translation.FAQs')</a></li>
                                <li><a href="pages-pricing" key="t-pricing">@lang('translation.Pricing')</a></li>
                                <li><a href="pages-404" key="t-error-404">@lang('translation.Error_404')</a></li>
                                <li><a href="pages-500" key="t-error-500">@lang('translation.Error_500')</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="layouts-horizontal">
                                <i data-feather="layout"></i>
                                <span data-key="t-horizontal">@lang('translation.Horizontal')</span>
                            </a>
                        </li>

                        <li class="menu-title mt-2" data-key="t-components">@lang('translation.Components')</li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="briefcase"></i>
                                <span data-key="t-components">@lang('translation.Bootstrap')</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="ui-alerts" key="t-alerts">@lang('translation.Alerts')</a></li>
                                <li><a href="ui-buttons" key="t-buttons">@lang('translation.Buttons')</a></li>
                                <li><a href="ui-cards" key="t-cards">@lang('translation.Cards')</a></li>
                                <li><a href="ui-carousel" key="t-carousel">@lang('translation.Carousel')</a></li>
                                <li><a href="ui-dropdowns" key="t-dropdowns">@lang('translation.Dropdowns')</a></li>
                                <li><a href="ui-grid" key="t-grid">@lang('translation.Grid')</a></li>
                                <li><a href="ui-images" key="t-images">@lang('translation.Images')</a></li>
                                <li><a href="ui-modals" key="t-modals">@lang('translation.Modals')</a></li>
                                <li><a href="ui-offcanvas" key="t-offcanvas">@lang('translation.Offcanvas')</a></li>
                                <li><a href="ui-progressbars" key="t-progress-bars">@lang('translation.Progress_Bars')</a></li>
                                <li><a href="ui-placeholders" key="t-placeholders">@lang('translation.Placeholders')</a></li>
                                <li><a href="ui-tabs-accordions" key="t-tabs-accordions">@lang('translation.Tabs_&_Accordions')</a></li>
                                <li><a href="ui-typography" key="t-typography">@lang('translation.Typography')</a></li>
                                <li><a href="ui-video" key="t-video">@lang('translation.Video')</a></li>
                                <li><a href="ui-general" key="t-general">@lang('translation.General')</a></li>
                                <li><a href="ui-colors" key="t-colors">@lang('translation.Colors')</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="gift"></i>
                                <span data-key="t-ui-elements">@lang('translation.Extended')</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="extended-lightbox" data-key="t-lightbox">@lang('translation.Lightbox')</a></li>
                                <li><a href="extended-rangeslider" data-key="t-range-slider">@lang('translation.Range_Slider')</a></li>
                                <li><a href="extended-sweet-alert" data-key="t-sweet-alert">@lang('translation.Sweet_Alert') 2</a></li>
                                <li><a href="extended-session-timeout" data-key="t-session-timeout">@lang('translation.Session_Timeout')</a></li>
                                <li><a href="extended-rating" data-key="t-rating">@lang('translation.Rating')</a></li>
                                <li><a href="extended-notifications" data-key="t-notifications">@lang('translation.Notifications')</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);">
                                <i data-feather="box"></i>
                                <span class="badge rounded-pill bg-soft-danger text-danger float-end">7</span>
                                <span data-key="t-forms">@lang('translation.Forms')</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="form-elements" data-key="t-form-elements">@lang('translation.Basic_Elements')</a></li>
                                <li><a href="form-validation" data-key="t-form-validation">@lang('translation.Validation')</a></li>
                                <li><a href="form-advanced" data-key="t-form-advanced">@lang('translation.Advanced_Plugins')</a></li>
                                <li><a href="form-editors" data-key="t-form-editors">@lang('translation.Editors')</a></li>
                                <li><a href="form-uploads" data-key="t-form-upload">@lang('translation.File_Upload')</a></li>
                                <li><a href="form-wizard" data-key="t-form-wizard">@lang('translation.Wizard')</a></li>
                                <li><a href="form-mask" data-key="t-form-mask">@lang('translation.Mask')</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="sliders"></i>
                                <span data-key="t-tables">@lang('translation.Tables')</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="tables-basic" data-key="t-basic-tables">@lang('translation.Bootstrap_Basic')</a></li>
                                <li><a href="tables-datatable" data-key="t-data-tables">@lang('translation.Data_Tables')</a></li>
                                <li><a href="tables-responsive" data-key="t-responsive-table">@lang('translation.Responsive')</a></li>
                                <li><a href="tables-editable" data-key="t-editable-table">@lang('translation.Editable_Table')</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="pie-chart"></i>
                                <span data-key="t-charts">@lang('translation.Charts')</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="charts-apex" data-key="t-apex-charts">@lang('translation.Apex_Charts')</a></li>
                                <li><a href="charts-echart" data-key="t-e-charts">@lang('translation.E_Charts')</a></li>
                                <li><a href="charts-chartjs" data-key="t-chartjs-charts">@lang('translation.Chartjs')</a></li>
                                <li><a href="charts-knob" data-key="t-knob-charts">@lang('translation.Jquery_Knob')</a></li>
                                <li><a href="charts-sparkline" data-key="t-sparkline-charts">@lang('translation.Sparkline')</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="cpu"></i>
                                <span data-key="t-icons">@lang('translation.Icons')</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="icons-feather" data-key="t-feather">@lang('translation.Feather')</a></li>
                                <li><a href="icons-boxicons" data-key="t-boxicons">@lang('translation.Boxicons')</a></li>
                                <li><a href="icons-materialdesign" data-key="t-material-design">@lang('translation.Material_Design')</a></li>
                                <li><a href="icons-dripicons" data-key="t-dripicons">@lang('translation.Dripicons')</a></li>
                                <li><a href="icons-fontawesome" data-key="t-font-awesome">@lang('translation.Font_awesome') 5</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="map"></i>
                                <span data-key="t-maps">@lang('translation.Maps')</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="maps-google" data-key="t-g-maps">@lang('translation.Google')</a></li>
                                <li><a href="maps-vector" data-key="t-v-maps">@lang('translation.Vector')</a></li>
                                <li><a href="maps-leaflet" data-key="t-l-maps">@lang('translation.Leaflet')</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="share-2"></i>
                                <span data-key="t-multi-level">@lang('translation.Multi_Level')</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="javascript: void(0);" data-key="t-level-1-1">@lang('translation.Level_1.1')</a></li>
                                <li>
                                    <a href="javascript: void(0);" class="has-arrow" data-key="t-level-1-2">@lang('translation.Level_1.2')</a>
                                    <ul class="sub-menu" aria-expanded="true">
                                        <li><a href="javascript: void(0);" data-key="t-level-2-1">@lang('translation.Level_2.1')</a></li>
                                        <li><a href="javascript: void(0);" data-key="t-level-2-2">@lang('translation.Level_2.2')</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>

                    </ul>

                    <div class="card sidebar-alert shadow-none text-center mx-4 mb-0 mt-5">
                        <div class="card-body">
                            <img src="assets/images/giftbox.png" alt="">
                            <div class="mt-4">
                                <h5 class="alertcard-title font-size-16">Unlimited Access</h5>
                                <p class="font-size-13">Upgrade your plan from a Free trial, to select ‘Business Plan’.</p>
                                <a href="#!" class="btn btn-primary mt-2">Upgrade Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Sidebar -->
            </div>
        </div>
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">{!! 'Ecommerce' !!}</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{'Cart' }}</a></li>
                                        @if(isset($title))
                                            <li class="breadcrumb-item active">{{ 'Cart' }}</li>
                                        @endif
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end page title -->



                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table align-middle mb-0 table-nowrap">
                                            <thead class="table-light">
                                            <tr>
                                                <th>Product</th>
                                                <th>Product Desc</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th colspan="2">Total</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <img src="{{ URL::asset('assets/images/product/img-1.png') }}" alt="product-img"
                                                         title="product-img" class="avatar-md" />
                                                </td>
                                                <td>
                                                    <h5 class="font-size-14 text-truncate"><a href="{{ url('ecommerce-product-detail') }}" class="text-dark">Half sleeve T-shirt</a></h5>
                                                    <p class="mb-0">Color : <span class="fw-medium">Maroon</span></p>
                                                </td>
                                                <td>
                                                    $ 450
                                                </td>
                                                <td>
                                                    <div class="me-3" style="width: 120px;">
                                                        <input type="text" value="02" name="demo_vertical">
                                                    </div>
                                                </td>
                                                <td>
                                                    $ 900
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0);" class="action-icon text-danger"> <i class="mdi mdi-trash-can font-size-18"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <img src="{{ URL::asset('assets/images/product/img-2.png') }}" alt="product-img"
                                                         title="product-img" class="avatar-md" />
                                                </td>
                                                <td>
                                                    <h5 class="font-size-14 text-truncate"><a href="{{ url('ecommerce-product-detail') }}" class="text-dark">Light blue T-shirt</a></h5>
                                                    <p class="mb-0">Color : <span class="fw-medium">Light blue</span></p>
                                                </td>
                                                <td>
                                                    $ 225
                                                </td>
                                                <td>
                                                    <div class="me-3" style="width: 120px;">
                                                        <input type="text" value="01" name="demo_vertical">
                                                    </div>
                                                </td>
                                                <td>
                                                    $ 225
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0);" class="action-icon text-danger"> <i class="mdi mdi-trash-can font-size-18"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <img src="{{ URL::asset('assets/images/product/img-3.png') }}" alt="product-img"
                                                         title="product-img" class="avatar-md" />
                                                </td>
                                                <td>
                                                    <h5 class="font-size-14 text-truncate"><a href="{{ url('ecommerce-product-detail') }}" class="text-dark">Black Color T-shirt</a></h5>
                                                    <p class="mb-0">Color : <span class="fw-medium">Black</span></p>
                                                </td>
                                                <td>
                                                    $ 152
                                                </td>
                                                <td>
                                                    <div class="me-3" style="width: 120px;">
                                                        <input type="text" value="02" name="demo_vertical">
                                                    </div>
                                                </td>
                                                <td>
                                                    $ 304
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0);" class="action-icon text-danger"> <i class="mdi mdi-trash-can font-size-18"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <img src="{{ URL::asset('assets/images/product/img-4.png') }}" alt="product-img"
                                                         title="product-img" class="avatar-md" />
                                                </td>
                                                <td>
                                                    <h5 class="font-size-14 text-truncate"><a href="{{ url('ecommerce-product-detail') }}" class="text-dark">Hoodie (Blue)</a></h5>
                                                    <p class="mb-0">Color : <span class="fw-medium">Blue</span></p>
                                                </td>
                                                <td>
                                                    $ 145
                                                </td>
                                                <td>
                                                    <div class="me-3" style="width: 120px;">
                                                        <input type="text" value="02" name="demo_vertical">
                                                    </div>
                                                </td>
                                                <td>
                                                    $ 290
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0);" class="action-icon text-danger"> <i class="mdi mdi-trash-can font-size-18"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <img src="{{ URL::asset('assets/images/product/img-5.png') }}" alt="product-img"
                                                         title="product-img" class="avatar-md" />
                                                </td>
                                                <td>
                                                    <h5 class="font-size-14 text-truncate"><a href="{{ url('ecommerce-product-detail') }}" class="text-dark">Half sleeve T-Shirt</a></h5>
                                                    <p class="mb-0">Color : <span class="fw-medium">Light orange</span></p>
                                                </td>
                                                <td>
                                                    $ 138
                                                </td>
                                                <td>
                                                    <div class="me-3" style="width: 120px;">
                                                        <input type="text" value="01" name="demo_vertical">
                                                    </div>
                                                </td>
                                                <td>
                                                    $ 138
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0);" class="action-icon text-danger"> <i class="mdi mdi-trash-can font-size-18"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <img src="{{ URL::asset('assets/images/product/img-6.png') }}" alt="product-img"
                                                         title="product-img" class="avatar-md" />
                                                </td>
                                                <td>
                                                    <h5 class="font-size-14 text-truncate"><a href="{{ url('ecommerce-product-detail') }}" class="text-dark">Green color T-shirt</a></h5>
                                                    <p class="mb-0">Color : <span class="fw-medium">Green</span></p>
                                                </td>
                                                <td>
                                                    $ 152
                                                </td>
                                                <td>
                                                    <div class="me-3" style="width: 120px;">
                                                        <input type="text" value="02" name="demo_vertical">
                                                    </div>
                                                </td>
                                                <td>
                                                    $ 304
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0);" class="action-icon text-danger"> <i class="mdi mdi-trash-can font-size-18"></i></a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-sm-6">
                                            <a href="{{ url('ecommerce-products') }}" class="btn btn-secondary">
                                                <i class="mdi mdi-arrow-left me-1"></i> Continue Shopping </a>
                                        </div> <!-- end col -->
                                        <div class="col-sm-6">
                                            <div class="text-sm-end mt-2 mt-sm-0">
                                                <a href="{{ url('ecommerce-checkout') }}" class="btn btn-success">
                                                    <i class="mdi mdi-cart-arrow-right me-1"></i> Checkout </a>
                                            </div>
                                        </div> <!-- end col -->
                                    </div> <!-- end row-->
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">Card Details</h5>

                                    <div class="card bg-primary text-white visa-card mb-0">
                                        <div class="card-body">
                                            <div>
                                                <i class="bx bxl-visa visa-pattern"></i>

                                                <div class="float-end">
                                                    <i class="bx bxl-visa visa-logo display-3"></i>
                                                </div>

                                                <div>
                                                    <i class="bx bx-chip h1 text-warning"></i>
                                                </div>
                                            </div>

                                            <div class="row mt-5">
                                                <div class="col-3">
                                                    <p class="mb-2">
                                                        <i class="fas fa-star-of-life m-1"></i>
                                                        <i class="fas fa-star-of-life m-1"></i>
                                                        <i class="fas fa-star-of-life m-1"></i>
                                                    </p>
                                                </div>
                                                <div class="col-3">
                                                    <p class="mb-2">
                                                        <i class="fas fa-star-of-life m-1"></i>
                                                        <i class="fas fa-star-of-life m-1"></i>
                                                        <i class="fas fa-star-of-life m-1"></i>
                                                    </p>
                                                </div>
                                                <div class="col-3">
                                                    <p class="mb-2">
                                                        <i class="fas fa-star-of-life m-1"></i>
                                                        <i class="fas fa-star-of-life m-1"></i>
                                                        <i class="fas fa-star-of-life m-1"></i>
                                                    </p>
                                                </div>
                                                <div class="col-3">
                                                    <p class="mb-2">
                                                        <i class="fas fa-star-of-life m-1"></i>
                                                        <i class="fas fa-star-of-life m-1"></i>
                                                        <i class="fas fa-star-of-life m-1"></i>
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="mt-5">
                                                <h5 class="text-white float-end mb-0">12/22</h5>
                                                <h5 class="text-white mb-0">Fredrick Taylor</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-3">Order Summary</h4>

                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>
                                            <tr>
                                                <td>Grand Total :</td>
                                                <td>$ 1,857</td>
                                            </tr>
                                            <tr>
                                                <td>Discount : </td>
                                                <td>- $ 157</td>
                                            </tr>
                                            <tr>
                                                <td>Shipping Charge :</td>
                                                <td>$ 25</td>
                                            </tr>
                                            <tr>
                                                <td>Estimated Tax : </td>
                                                <td>$ 19.22</td>
                                            </tr>
                                            <tr>
                                                <th>Total :</th>
                                                <th>$ 1744.22</th>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- end table-responsive -->
                                </div>
                            </div>
                            <!-- end card -->
                        </div>
                    </div>

                </div>
                <!-- container-fluid -->
            </div>
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> © Dason.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Design & Develop by <a href="#!" class="text-decoration-underline">Themesdesign</a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <div class="right-bar">
        <div data-simplebar class="h-100">
            <div class="rightbar-title d-flex align-items-center bg-dark p-3">

                <h5 class="m-0 me-2 text-white">Theme Customizer</h5>

                <a href="javascript:void(0);" class="right-bar-toggle ms-auto">
                    <i class="mdi mdi-close noti-icon"></i>
                </a>
            </div>

            <!-- Settings -->
            <hr class="m-0" />

            <div class="p-4">
                <h6 class="mb-3">Layout</h6>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="layout"
                           id="layout-vertical" value="vertical">
                    <label class="form-check-label" for="layout-vertical">Vertical</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="layout"
                           id="layout-horizontal" value="horizontal">
                    <label class="form-check-label" for="layout-horizontal">Horizontal</label>
                </div>

                <h6 class="mt-4 mb-3 pt-2">Layout Mode</h6>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="layout-mode"
                           id="layout-mode-light" value="light">
                    <label class="form-check-label" for="layout-mode-light">Light</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="layout-mode"
                           id="layout-mode-dark" value="dark">
                    <label class="form-check-label" for="layout-mode-dark">Dark</label>
                </div>

                <h6 class="mt-4 mb-3 pt-2">Layout Width</h6>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="layout-width"
                           id="layout-width-fuild" value="fuild" onchange="document.body.setAttribute('data-layout-size', 'fluid')">
                    <label class="form-check-label" for="layout-width-fuild">Fluid</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="layout-width"
                           id="layout-width-boxed" value="boxed" onchange="document.body.setAttribute('data-layout-size', 'boxed'),document.body.setAttribute('data-sidebar-size', 'sm')">
                    <label class="form-check-label" for="layout-width-boxed">Boxed</label>
                </div>

                <h6 class="mt-4 mb-3 pt-2">Layout Position</h6>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="layout-position"
                           id="layout-position-fixed" value="fixed" onchange="document.body.setAttribute('data-layout-scrollable', 'false')">
                    <label class="form-check-label" for="layout-position-fixed">Fixed</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="layout-position"
                           id="layout-position-scrollable" value="scrollable" onchange="document.body.setAttribute('data-layout-scrollable', 'true')">
                    <label class="form-check-label" for="layout-position-scrollable">Scrollable</label>
                </div>

                <h6 class="mt-4 mb-3 pt-2">Topbar Color</h6>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="topbar-color"
                           id="topbar-color-light" value="light" onchange="document.body.setAttribute('data-topbar', 'light')">
                    <label class="form-check-label" for="topbar-color-light">Light</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="topbar-color"
                           id="topbar-color-dark" value="dark" onchange="document.body.setAttribute('data-topbar', 'dark')">
                    <label class="form-check-label" for="topbar-color-dark">Dark</label>
                </div>

                <h6 class="mt-4 mb-3 pt-2 sidebar-setting">Sidebar Size</h6>

                <div class="form-check sidebar-setting">
                    <input class="form-check-input" type="radio" name="sidebar-size"
                           id="sidebar-size-default" value="default" onchange="document.body.setAttribute('data-sidebar-size', 'lg')">
                    <label class="form-check-label" for="sidebar-size-default">Default</label>
                </div>
                <div class="form-check sidebar-setting">
                    <input class="form-check-input" type="radio" name="sidebar-size"
                           id="sidebar-size-compact" value="compact" onchange="document.body.setAttribute('data-sidebar-size', 'md')">
                    <label class="form-check-label" for="sidebar-size-compact">Compact</label>
                </div>
                <div class="form-check sidebar-setting">
                    <input class="form-check-input" type="radio" name="sidebar-size"
                           id="sidebar-size-small" value="small" onchange="document.body.setAttribute('data-sidebar-size', 'sm')">
                    <label class="form-check-label" for="sidebar-size-small">Small (Icon View)</label>
                </div>

                <h6 class="mt-4 mb-3 pt-2 sidebar-setting">Sidebar Color</h6>

                <div class="form-check sidebar-setting">
                    <input class="form-check-input" type="radio" name="sidebar-color"
                           id="sidebar-color-light" value="light" onchange="document.body.setAttribute('data-sidebar', 'light')">
                    <label class="form-check-label" for="sidebar-color-light">Light</label>
                </div>
                <div class="form-check sidebar-setting">
                    <input class="form-check-input" type="radio" name="sidebar-color"
                           id="sidebar-color-dark" value="dark" onchange="document.body.setAttribute('data-sidebar', 'dark')">
                    <label class="form-check-label" for="sidebar-color-dark">Dark</label>
                </div>
                <div class="form-check sidebar-setting">
                    <input class="form-check-input" type="radio" name="sidebar-color"
                           id="sidebar-color-brand" value="brand" onchange="document.body.setAttribute('data-sidebar', 'brand')">
                    <label class="form-check-label" for="sidebar-color-brand">Brand</label>
                </div>

                <h6 class="mt-4 mb-3 pt-2">Direction</h6>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="layout-direction"
                           id="layout-direction-ltr" value="ltr">
                    <label class="form-check-label" for="layout-direction-ltr">LTR</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="layout-direction"
                           id="layout-direction-rtl" value="rtl">
                    <label class="form-check-label" for="layout-direction-rtl">RTL</label>
                </div>

            </div>

        </div> <!-- end slimscroll-menu-->
    </div>
    <div class="rightbar-overlay"></div>

    <script src="{{ URL::asset('/assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/metismenu/metismenu.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/node-waves/node-waves.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/feather-icons/feather-icons.min.js') }}"></script>
    <!-- pace js -->
    <script src="{{ URL::asset('assets/libs/pace-js/pace-js.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/ecommerce-cart.init.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
</body>
</html>
