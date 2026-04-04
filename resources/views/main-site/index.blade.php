<!-- resources/views/home.blade.php -->

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
        .banner_section .banner_content2 h2 {
            font-size: 44px;
            line-height: 1.15;
            margin-bottom: 10px;
        }

        .banner_section .banner_content2 h4 {
            font-size: 24px;
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .banner_section .banner_content2 p {
            font-size: 16px;
            line-height: 1.45;
            margin-bottom: 10px;
        }

        .banner_section .banner_content2 [lang="ar"] {
            font-size: 0.9em;
        }

        @media (max-width: 991px) {
            .banner_section .banner_content2 h2 {
                font-size: 34px;
            }

            .banner_section .banner_content2 h4 {
                font-size: 20px;
            }

            .banner_section .banner_content2 p {
                font-size: 15px;
            }
        }

        @media (max-width: 767px) {
            .banner_section .banner_content2 h2 {
                font-size: 28px;
                line-height: 1.2;
            }

            .banner_section .banner_content2 h4 {
                font-size: 18px;
            }

            .banner_section .banner_content2 p {
                font-size: 14px;
                line-height: 1.35;
            }
        }
    </style>

    
    <!-- FancyBox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox/dist/jquery.fancybox.min.css">
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

     <script src="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox/dist/jquery.fancybox.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    


@if(session('success') || session('error'))
<script>
    $(document).ready(function() {
        $.fancybox.open({
            src: '<div class="row" style="width:350px; position: relative;">' +
                    @if(session('success')) 
                        '<div class="alert alert-success" role="alert">' +
                            '<i class="fa fa-check-circle" style="font-size: 20px;"></i> {{ session('success') }}' +
                        '</div>' +
                    @elseif(session('error')) 
                        '<div class="alert alert-danger" role="alert">' +
                            '<i class="fa fa-exclamation-circle" style="font-size: 20px;"></i> {{ session('error') }}' +
                        '</div>' +
                    @endif
                    '<button type="button" class="btn-close" aria-label="Close" style="position: absolute; top: 10px; right: 10px; border: none; background: transparent;">' +
                        '<i class="fa fa-times" style="font-size: 20px;"></i>' +
                    '</button>' +
                 '</div>',
            type: 'html',
            opts: {
                padding: 20,
                width: 'auto',
                height: 'auto',
                maxWidth: 500,
                maxHeight: 'auto',
                modal: false,  
                clickOutside: true,  
                afterShow: function(instance, current) {
                    $('.btn-close').on('click', function() {
                        $.fancybox.close();
                    });
                }
            }
        });
    });
</script>
@endif




@endpush


@section('title', 'Home')

@section('header')
    <!-- START HEADER -->
    <header class="header_wrap fixed-top light_skin sticky_light_skin main_menu_uppercase transparent_header dd_light_skin">

     <!--   <header class="header_wrap fixed-top header_with_topbar dark_skin main_menu_uppercase" style="background-color:black;"> -->

        <div class="container">

            @include('partials.nav')

        </div>
    </header>
    <!-- END HEADER -->
@endsection

@section('content')


<!-- START SECTION BANNER -->
<div class="banner_section full_screen staggered-animation-wrap pattern_banner_bottom">
    <div id="carouselExampleControls" class="carousel slide carousel-fade carousel_style2 light_arrow" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active background_bg overlay_bg_40" data-img-src="/assets/images/banner5.jpg">
                <div class="banner_slide_content">
                    <div class="container"><!-- STRART CONTAINER -->
                        <div class="row">
                            <div class="col-lg-7 col-md-12 col-sm-12">
                                <div class="banner_content2 text_white">
                                    <h2 class="staggered-animation" data-animation="fadeInUp" data-animation-delay="0.2s">Authentic Italian Dishes, Made with Heart</h2>
                                    <h2 class="staggered-animation" dir="rtl" lang="ar" data-animation="fadeInUp" data-animation-delay="0.3s">أطباق إيطالية أصلية متحضّرة بحب</h2>
                                    <p class="staggered-animation" data-animation="fadeInUp" data-animation-delay="0.4s">From handmade pasta to wood-fired pizza, Palombini brings the true taste of Italy to your table.</p>
                                    <p class="staggered-animation" dir="rtl" lang="ar" data-animation="fadeInUp" data-animation-delay="0.5s">من الباستا المصنوعة بإيدينا للبيتزا في فرن الحطب، بالومبيني بيقدّم طعم إيطاليا الحقيقي على سفرتكم.</p>
                                    <a class="btn btn-default rounded-0 staggered-animation" href="{{ route('menu') }}" data-animation="fadeInUp" data-animation-delay="0.6s"><x-bi en="View Menu" ar="اعرض المنيو" /></a>
                                </div>
                            </div>
                        </div>
                    </div><!-- END CONTAINER-->
                </div>
            </div>
            <div class="carousel-item background_bg overlay_bg_60" data-img-src="/assets/images/banner2.jpg">
                <div class="banner_slide_content">
                    <div class="container"><!-- STRART CONTAINER -->
                        <div class="row justify-content-center">
                            <div class="col-lg-7 col-md-12 col-sm-12 text-center">
                                <div class="banner_content2 text_white">
                                    <h2 class="staggered-animation" data-animation="fadeInUp" data-animation-delay="0.2s">Fresh Ingredients, Classic Italian Flavor</h2>
                                    <h2 class="staggered-animation" dir="rtl" lang="ar" data-animation="fadeInUp" data-animation-delay="0.3s">مكونات طازة ونكهة إيطالية كلاسيك</h2>
                                    <p class="staggered-animation" data-animation="fadeInUp" data-animation-delay="0.4s">Every plate is prepared with premium ingredients, balanced flavors, and the signature Palombini Italian touch.</p>
                                    <p class="staggered-animation" dir="rtl" lang="ar" data-animation="fadeInUp" data-animation-delay="0.5s">كل طبق بيتحضّر بمكونات عالية الجودة، ونكهات متوازنة، ولمسة بالومبيني الإيطالية المميزة.</p>
                                    <a class="btn btn-default rounded-0 staggered-animation" href="{{ route('menu') }}" data-animation="fadeInUp" data-animation-delay="0.6s"><x-bi en="View Menu" ar="اعرض المنيو" /></a>
                                    <a class="btn btn-default rounded-0 staggered-animation" href="{{ route('customer.cart') }}" data-animation="fadeInUp" data-animation-delay="0.6s"><x-bi en="Book a Table" ar="احجز طاولة" /></a>
                                </div>
                            </div>
                        </div>
                    </div><!-- END CONTAINER-->
                </div>
            </div>
            <div class="carousel-item background_bg overlay_bg_40" data-img-src="/assets/images/banner6.jpeg">
                <div class="banner_slide_content">
                    <div class="container"><!-- STRART CONTAINER -->
                        <div class="row justify-content-md-end">
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="banner_content2 text_white">
                                    <h4 class="staggered-animation text_default" data-animation="fadeInUp" data-animation-delay="0.2s">Palombini Italian Experience</h4>
                                    <h4 class="staggered-animation text_default" dir="rtl" lang="ar" data-animation="fadeInUp" data-animation-delay="0.25s">تجربة بالومبيني الإيطالية</h4>
                                    <h2 class="staggered-animation" data-animation="fadeInUp" data-animation-delay="0.3s">Your Favorite Italian Moment Starts Here</h2>
                                    <h2 class="staggered-animation" dir="rtl" lang="ar" data-animation="fadeInUp" data-animation-delay="0.35s">لحظتكم الإيطالية المفضلة بتبدأ من هنا</h2>
                                    <p class="staggered-animation" data-animation="fadeInUp" data-animation-delay="0.4s">Enjoy a complete dine-in experience with warm hospitality and comforting Italian dishes made fresh for your table.</p>
                                    <p class="staggered-animation" dir="rtl" lang="ar" data-animation="fadeInUp" data-animation-delay="0.5s">استمتعوا بتجربة أكل داخل المطعم مع ضيافة دافئة وأطباق إيطالية مريحة متحضرة طازة لسفرتكم.</p>
                                    <a class="btn btn-default rounded-0 staggered-animation" href="{{ route('menu') }}" data-animation="fadeInUp" data-animation-delay="0.6s"><x-bi en="View Menu" ar="اعرض المنيو" /></a>
                                    <a class="btn btn-default rounded-0 staggered-animation" href="{{ route('customer.cart') }}" data-animation="fadeInUp" data-animation-delay="0.6s"><x-bi en="Book a Table" ar="احجز طاولة" /></a>
                                </div>
                            </div>
                        </div>
                    </div><!-- END CONTAINER-->
                </div>
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev"><i class="ion-chevron-left"></i></a>
        <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next"><i class="ion-chevron-right"></i></a>
    </div>
</div>
<!-- END SECTION BANNER -->

 
 

    <!-- START SECTION INTRO -->
    <div class="section pb_70">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10">
                    <div class="heading_s1 animation text-center" data-animation="fadeInUp" data-animation-delay="0.02s">
                        <div class="sub_heading font_style1">Welcome to Palombini Cafe</div>
                        <div class="sub_heading font_style1" dir="rtl" lang="ar">أهلًا بيكم في Palombini Cafe</div>
                        <h2>Your Italian Moment, Every Day</h2>
                        <h2 dir="rtl" lang="ar">لحظتكم الإيطالية كل يوم</h2>
                    </div>
                    <div class="small_divider clearfix"></div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10 text-center animation" data-animation="fadeInUp" data-animation-delay="0.04s">
                    <p>
                        Palombini Cafe brings authentic Italian dining into a warm, modern space—serving handcrafted pasta, wood-fired pizza, rich espresso, and timeless desserts for every cozy meal or special gathering.<br>
                        <span dir="rtl" lang="ar">Palombini Cafe بيقدم الأكل الإيطالي الأصلي في مكان دافي وعصري—من الباستا المصنوعة بإيدينا والبيتزا في فرن الحطب، لإسبريسو غني وحلويات كلاسيكية، لكل أكلة حلوة أو مناسبة مميزة.</span>
                    </p>
                    <a class="btn btn-default rounded-0 mt-2 mr-2" href="{{ route('menu') }}"><x-bi en="Explore Menu" ar="استكشف المنيو" /></a>
                    <a class="btn btn-default rounded-0 mt-2 mr-2" href="{{ route('about') }}"><x-bi en="Our Story" ar="قصتنا" /></a>
                    <a class="btn btn-default rounded-0 mt-2" href="{{ route('contact') }}"><x-bi en="Visit Us" ar="زورونا" /></a>
                </div>
            </div>
        </div>
    </div>
    <!-- END SECTION INTRO -->
@if(config('services.table_booking.allow'))
<!-- START SECTION CTA -->
<div class="section background_bg" data-img-src="/assets/images/cta_bg.jpg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-7 col-md-9 animation text-center" data-animation="fadeInUp" data-animation-delay="0.02s">
                <div class="heading_s1 heading_light">
                    <span class="sub_heading font_style1">Experience True Flavor</span>
                    <h2>Where Meals Bring Us Together</h2>
                </div>
                <p class="text-white">Celebrate the joy of dining with authentic African dishes, crafted to bring families and friends closer with every bite.</p>
                <a class="btn btn-white rounded-0" href="{{ route('menu') }}">Order Now</a>
                <div class="large_divider clearfix"></div>
            </div>
        </div>
    </div>
</div>
<!-- END SECTION CTA -->


    <!-- START SECTION BOOK TABLE -->
    <div class="section pt-0 small_pb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="overlap_table_box">
                        <div class="row align-content-end flex-row-reverse">
                            <div class="col-lg-7 animation" data-animation="fadeInUp" data-animation-delay="0.2s">
                                <div class="book_table">
                                    <div class="medium_divider clearfix"></div>
                                    <div class="heading_s1 mb-md-0">
                                        <span class="sub_heading font_style1">Reservations</span>
                                        <h2>Book A Table</h2>
                                    </div>
                                    <div class="small_divider clearfix"></div>
                                    <div class="field_form form_style1">
                                        <form method="post" action="{{ route('table.booking') }}" name="enq">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <div class="input_group">
                                                        <input required="required" placeholder="Name" class="form-control rounded-0" name="name" type="text">
                                                        <div class="input_icon">
                                                            <i class="fa fa-user"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <div class="input_group">
                                                        <input required="required" placeholder="Email Address" class="form-control rounded-0" name="email" type="email">
                                                        <div class="input_icon">
                                                            <i class="fa fa-envelope"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <div class="input_group">
                                                        <input placeholder="Time" class="form-control rounded-0 timepicker" data-theme="red" name="time" type="text">
                                                        <div class="input_icon">
                                                            <i class="far fa-clock"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <div class="input_group">
                                                        <input required="required" placeholder="Mobile No." class="form-control rounded-0" name="phone" type="tel">
                                                        <div class="input_icon">
                                                            <i class="ti-mobile"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <div class="input_group">
                                                        <input placeholder="Select Date" class="form-control rounded-0 datepicker" name="date" type="text">
                                                        <div class="input_icon">
                                                            <i class="far fa-calendar-alt"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <div class="custom_select">
                                                        <select class="form-control rounded-0" name="persons">
                                                            <option value="">Select Person</option>
                                                            <option value="1">1 Person</option>
                                                            <option value="2">2 Persons</option>
                                                            <option value="3">3 Persons</option>
                                                            <option value="4">4 Persons</option>
                                                            <option value="5">5 Persons</option>
                                                            <option value="6">6 Persons</option>
                                                            <option value="7">7 Persons</option>
                                                            <option value="8">8 Persons</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <button type="submit" title="Submit Your Message!" class="btn btn-default rounded-0" name="submit" value="Submit">Book Now</button>
                                                </div>
                                            </div>
                                        </form>
                                        
                                    </div>
                                    <div class="medium_divider clearfix"></div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="chef_image">
                                    <img src="/assets/images/chef.png" alt="chef"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END SECTION BOOK TABLE -->
@endif

 @if(!$testimonies->isEmpty())
<!-- START SECTION TESTIMONIAL -->
<div class="section bg_linen pb_70">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 animation" data-animation="fadeInUp" data-animation-delay="0.02s">
                <div class="heading_s1 text-center">
                    <span class="sub_heading font_style1">Testimonial</span>
                    <h2>Our Customers Say!</h2>
                </div>
                <p class="text-center leads">Hear what our happy customers have to say about their experience with us.</p>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 animation" data-animation="fadeInUp" data-animation-delay="0.03s">
                <div class="testimonial_slider testimonial_style2 carousel_slider owl-carousel owl-theme" data-margin="10" data-loop="true" data-autoplay="true" data-responsive='{"0":{"items": "1"}, "767":{"items": "2"}, "1199":{"items": "3"}}'>

                    @foreach($testimonies as $testimony)

                    <div class="testimonial_box">
                        <div class="author_info">
                            <div class="author_name">
                                <h5>{{ $testimony->name }}</h5>
                             </div>
                        </div>
                        <div class="testimonial_desc">
                            <p>{{ Str::limit($testimony->content, 300) }}</p>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>
<!-- END SECTION TESTIMONIAL -->
@endif

@if(!$blogs->isEmpty())
<!-- START SECTION BLOG -->
<div class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 animation" data-animation="fadeInUp" data-animation-delay="0.2s">
                <div class="heading_s1 text-center">
                    <span class="sub_heading font_style1">From The Blog</span>
                    <h2>Our Latest News</h2>
                </div>
                <p class="text-center leads">Explore the stories behind our rich African flavors, our passion for suya, and the art of charcoal grilling.</p>
            </div>
        </div>
        <div class="row justify-content-center">


           
                @forelse($blogs as $blog)
                    <div class="d-flex col-lg-4 col-md-6 animation" data-animation="fadeInUp" data-animation-delay="0.2s">
                        <div class="blog_post blog_style2 box_shadow1">
                            <div class="blog_img">
                                <a href="{{ route('blog.view', $blog->id) }}">
                                    <img src="{{ asset('storage/' . $blog->image) }}" alt="blog_small_img1">
                                </a>
                                <span class="post_date">
                                    <strong>{{ $blog->created_at->format('d') }}</strong> {{ $blog->created_at->format('M') }}
                                </span>
                            </div>
                            <div class="blog_content">
                                <div class="blog_text">
                             
                                    <h5 class="blog_title"><a href="#">{{ $blog->name }}</a></h5>
                                    <p>{{ Str::limit(strip_tags($blog->content), 50) }}</p>

                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p>No blogs found.</p>
                @endforelse
          
            
            
        </div>
    </div>
</div>
<!-- END SECTION BLOG -->

@endif

 
@endsection


 
