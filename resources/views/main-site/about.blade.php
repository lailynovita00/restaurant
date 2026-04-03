
@extends('layouts.main-site')

@push('styles')
    
    <!-- Animation CSS -->
    <link rel="stylesheet" href="/assets/css/animate.css">	
    <!-- Latest Bootstrap min CSS -->
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Kaushan+Script&amp;display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans:100,100i,300,300i,400,400i,600,600i,700,700i&amp;display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&amp;display=swap" rel="stylesheet"> 
    <!-- Icon Font CSS -->
    <link rel="stylesheet" href="/assets/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/ionicons.min.css">
    <link rel="stylesheet" href="/assets/css/themify-icons.css">
    <link rel="stylesheet" href="/assets/css/linearicons.css">
    <link rel="stylesheet" href="/assets/css/flaticon.css">
    <!--- owl carousel CSS-->
    <link rel="stylesheet" href="/assets/owlcarousel/css/owl.carousel.min.css">
    <link rel="stylesheet" href="/assets/owlcarousel/css/owl.theme.css">
    <link rel="stylesheet" href="/assets/owlcarousel/css/owl.theme.default.min.css">
    <!-- Slick CSS -->
    <link rel="stylesheet" href="/assets/css/slick.css">
    <link rel="stylesheet" href="/assets/css/slick-theme.css">
    <!-- Magnific Popup CSS -->
    <link rel="stylesheet" href="/assets/css/magnific-popup.css">
    <!-- DatePicker CSS -->
    <link href="/assets/css/datepicker.min.css" rel="stylesheet">
    <!-- TimePicker CSS -->
    <link href="/assets/css/mdtimepicker.min.css" rel="stylesheet">
    <!-- Style CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/responsive.css">
    <link id="layoutstyle" rel="stylesheet" href="/assets/color/theme-brown.css">
    <style>
        .about-video-wrapper {
            position: relative;
            width: min(100%, 360px);
            aspect-ratio: 9 / 16;
            border-radius: 14px;
            overflow: hidden;
            background: #000;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.18);
            margin: 0 auto;
        }

        .about-video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .about-video-card {
            background-color: #fff;
        }

        @media (max-width: 991px) {
            .about-video-wrapper {
                width: min(100%, 320px);
                margin-top: 20px;
            }
        }
    </style>
@endpush

@push('scripts')
    <!-- Latest jQuery --> 
    <script src="/assets/js/jquery-1.12.4.min.js"></script> 
    <!-- Latest compiled and minified Bootstrap --> 
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script> 
    <!-- owl-carousel min js  --> 
    <script src="/assets/owlcarousel/js/owl.carousel.min.js"></script> 
    <!-- magnific-popup min js  --> 
    <script src="/assets/js/magnific-popup.min.js"></script> 
    <!-- waypoints min js  --> 
    <script src="/assets/js/waypoints.min.js"></script> 
    <!-- parallax js  --> 
    <script src="/assets/js/parallax.js"></script> 
    <!-- countdown js  --> 
    <script src="/assets/js/jquery.countdown.min.js"></script> 
    <!-- jquery.countTo js  -->
    <script src="/assets/js/jquery.countTo.js"></script>
    <!-- imagesloaded js --> 
    <script src="/assets/js/imagesloaded.pkgd.min.js"></script>
    <!-- isotope min js --> 
    <script src="/assets/js/isotope.min.js"></script>
    <!-- jquery.appear js  -->
    <script src="/assets/js/jquery.appear.js"></script>
    <!-- jquery.dd.min js -->
    <script src="/assets/js/jquery.dd.min.js"></script>
    <!-- slick js -->
    <script src="/assets/js/slick.min.js"></script>
    <!-- DatePicker js -->
    <script src="/assets/js/datepicker.min.js"></script>
    <!-- TimePicker js -->
    <script src="/assets/js/mdtimepicker.min.js"></script>
    <!-- scripts js --> 
    <script src="/assets/js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endpush


@section('title', 'About')


@section('header')
    <!-- START HEADER -->
        <header class="header_wrap fixed-top header_with_topbar light_skin main_menu_uppercase">
        <div class="container">
            @include('partials.nav')
        </div>
    </header>
    <!-- END HEADER -->
@endsection


@section('content')

        <!-- START SECTION BREADCRUMB -->
        <div class="breadcrumb_section background_bg overlay_bg_50 page_title_light" data-img-src="/assets/images/about_bg.jpg">
            <div class="container"><!-- STRART CONTAINER -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title">
                            <h1><x-bi en="About Us" ar="عنّا" /></h1>
                        </div>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}"><x-bi en="Home" ar="الرئيسية" /></a></li>
                            <li class="breadcrumb-item active"><x-bi en="About Us" ar="عنّا" /></li>
                        </ol>
                    </div>
                </div>
            </div><!-- END CONTAINER-->
        </div>
        <!-- END SECTION BREADCRUMB -->

<!-- START SECTION ABOUT -->
<div class="section">
	<div class="container">
    	<div class="row align-items-center">
            <div class="col-lg-6">
                <div class="about_box box_shadow1">
                    <div class="heading_s1">
                        <span class="sub_heading font_style1">About Us</span>
                        <h2>{{ config('site.name') }}</h2>
                    </div>
                    <p>Welcome to Palombini Cafe, where we bring the heart and soul of authentic Italian cuisine to your table. From handcrafted pasta and wood-fired pizzas to rich risottos and classic desserts, every dish is made to capture the true flavors of Italy.</p>
                    <p>At Palombini Cafe, we are passionate about using the freshest ingredients, traditional recipes, and a touch of creativity to create an unforgettable dining experience. Join us and savor the warmth, comfort, and elegance of Italian culinary tradition in every bite.</p>
                    <p dir="rtl" lang="ar">أهلًا بيكم في Palombini Cafe، المكان اللي بنقدملكم فيه روح المطبخ الإيطالي الأصيل على سفرتكم. من الباستا المصنوعة بإيدينا والبيتزا المخبوزة في فرن الحطب، للريزوتو الغني والحلويات الكلاسيكية، كل طبق معمول علشان ينقلكم لطعم إيطاليا الحقيقي.</p>
                    <p dir="rtl" lang="ar">في Palombini Cafe، إحنا شغوفين باستخدام أحسن المكونات الطازة، والوصفات التقليدية، ولمسة إبداع عشان نصنع تجربة أكل لا تُنسى. انضموا لينا واستمتعوا بدفء وراحة وأناقة تقاليد المطبخ الإيطالي في كل لقمة.</p>
                </div>
            </div>
            
	        <div class="col-lg-6">	
                <div class="fancy_style1 about-video-card">
                    <div class="about-video-wrapper">
                        <iframe
                            src="https://drive.google.com/file/d/1M59cyFWXC_ngSxFT4G1hAaarCjJWXuYV/preview"
                            title="Palombini Cafe Video"
                            allow="autoplay; encrypted-media; picture-in-picture"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END SECTION ABOUT --> 
        <!-- START SECTION CTA -->
        <div class="section background_bg" data-img-src="/assets/images/cta_bg.jpg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-8 animation" data-animation="fadeInUp" data-animation-delay="0.02s">
                        <div class="heading_s1 heading_light">
                            <span class="sub_heading font_style1">Experience Authentic Italian Flavors</span>
                            <h2>{{ config('site.name') }}: A Taste of Italy</h2>
                        </div>
                        <p class="text-white">Embark on a culinary journey with {{ config('site.name') }}, where we celebrate the timeless flavors of Italy. From handcrafted pasta and wood-fired pizza to rich risottos and classic desserts, every dish is prepared to deliver an unforgettable dining experience.</p>
                        <p class="text-white" dir="rtl" lang="ar">ابدأوا رحلة أكل مميزة مع {{ config('site.name') }}، حيث نحتفل بنكهات إيطاليا الأصيلة اللي عمرها ما بتقدم. من الباستا المصنوعة بإيدينا والبيتزا في فرن الحطب، للريزوتو الغني والحلويات الكلاسيكية، كل طبق متحضّر علشان يقدّم لكم تجربة لا تُنسى.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- END SECTION CTA -->


<!-- START SECTION FEATURES -->
<div class="section pb_70">
    <div class="container">
        <div class="row justify-content-center">
            <!-- Authentic Italian Cuisine -->
            <div class="col-lg-4 col-md-6">
                <div class="icon_box icon_box_style1 text-center animation" data-animation="fadeInUp" data-animation-delay="0.02s">
                    <div class="icon">
                        <i class="flaticon-dining-table"></i>
                    </div>
                    <div class="icon_box_content">
                        <h5 class="text-uppercase">Authentic Italian Cuisine</h5>
                        <p>Savor traditional Italian dishes crafted with care to preserve the rich culinary heritage of Italy.</p>
                        <p dir="rtl" lang="ar">استمتعوا بأطباق إيطالية تقليدية معمولة بعناية للحفاظ على التراث الغني للمطبخ الإيطالي.</p>
                    </div>
                </div>
            </div>

            <!-- Homemade Goodness -->
            <div class="col-lg-4 col-md-6">
                <div class="icon_box icon_box_style1 text-center animation" data-animation="fadeInUp" data-animation-delay="0.03s">
                    <div class="icon">
                        <i class="flaticon-contact"></i>
                    </div>
                    <div class="icon_box_content">
                        <h5 class="text-uppercase">Handcrafted Freshness</h5>
                        <p>Our meals are made with fresh ingredients and time-honored recipes, bringing comfort and quality to every table.</p>
                        <p dir="rtl" lang="ar">أكلاتنا بتتعمل بمكونات طازة ووصفات متوارثة، علشان نوصل الراحة والجودة لكل ترابيزة.</p>
                    </div>
                </div>
            </div>

            <!-- Satisfying Every Bite -->
            <div class="col-lg-4 col-md-6">
                <div class="icon_box icon_box_style1 text-center animation" data-animation="fadeInUp" data-animation-delay="0.04s">
                    <div class="icon">
                        <i class="flaticon-restaurant"></i>
                    </div>
                    <div class="icon_box_content">
                        <h5 class="text-uppercase">Memorable Every Bite</h5>
                        <p>Enjoy balanced flavors, elegant presentation, and dishes designed to make every bite truly memorable.</p>
                        <p dir="rtl" lang="ar">استمتعوا بنكهات متوازنة، وتقديم أنيق، وأطباق معمولة علشان كل لقمة تفضل في الذاكرة.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END SECTION FEATURES -->

         
 
@endsection


 
