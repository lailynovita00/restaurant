@once
    @push('styles')
        <style>
            .footer-mobile-compact .footer_top {
                padding-bottom: 40px;
            }

            .footer-brand-copy {
                max-width: 560px;
            }

            .footer-contact-list li,
            .footer-links-list li {
                line-height: 1.4;
            }

            @media (max-width: 767.98px) {
                .footer-mobile-compact .footer_top {
                    padding-top: 36px;
                    padding-bottom: 18px;
                }

                .footer-mobile-compact .footer_logo {
                    margin-bottom: 12px;
                }

                .footer-mobile-compact .footer_logo img {
                    max-height: 58px;
                }

                .footer-mobile-compact footer p,
                .footer-mobile-compact p {
                    font-size: 12px;
                    line-height: 1.45;
                }

                .footer-mobile-compact .footer-brand-copy {
                    display: -webkit-box;
                    line-clamp: 4;
                    -webkit-line-clamp: 4;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                    margin-bottom: 10px;
                }

                .footer-mobile-compact .widget {
                    margin-bottom: 14px;
                }

                .footer-mobile-compact .widget_title {
                    margin-bottom: 10px;
                    font-size: 15px;
                }

                .footer-mobile-compact .footer-links-col,
                .footer-mobile-compact .footer-contact-col {
                    flex: 0 0 50%;
                    max-width: 50%;
                }

                .footer-mobile-compact .footer-links-list li {
                    padding-bottom: 6px;
                }

                .footer-mobile-compact .footer-links-list li a,
                .footer-mobile-compact .footer-contact-list li,
                .footer-mobile-compact .footer-contact-list li a,
                .footer-mobile-compact .footer-contact-list li p {
                    font-size: 12px;
                    line-height: 1.35;
                }

                .footer-mobile-compact .footer-contact-list > li {
                    margin-bottom: 8px;
                }

                .footer-mobile-compact .footer-contact-list li i {
                    margin-right: 6px;
                    margin-top: 2px;
                    font-size: 14px;
                }

                .footer-mobile-compact .contact_info i + *,
                .footer-mobile-compact .contact_info span + * {
                    max-width: calc(100% - 24px);
                    font-size: 12px;
                    line-height: 1.35;
                }

                .footer-mobile-compact .social_icons li {
                    margin-right: 4px;
                }

                .footer-mobile-compact .social_icons.social_style1 li a {
                    height: 32px;
                    width: 32px;
                    line-height: 32px;
                    font-size: 13px;
                }

                .footer-mobile-compact .bottom_footer {
                    padding: 14px 0;
                }

                .footer-mobile-compact .bottom_footer p {
                    font-size: 11px;
                }
            }
        </style>
    @endpush
@endonce

<!-- START FOOTER -->
<footer class="footer_dark pattern_top background_bg overlay_bg_80 footer-mobile-compact" data-img-src="/assets/images/footer_bg.jpg">
	<div class="footer_top">
        <div class="container">
            <div class="row">
	            <div class="col-12 col-md-6">
                	<div class="widget">
                        <div class="footer_logo">
                            <a href="index-6.html"><img src="/assets/images/palombini-logo.png" alt="Palombini Cafe Logo"></a>
                        </div>
                        <p class="footer-brand-copy"><x-bi en="At {{ config('site.name') }}, we pride ourselves on bringing you the authentic flavors of West Africa. Our expertly crafted dishes and warm hospitality create a dining experience you won't forget." ar="في {{ config('site.name') }}، بنفخر إننا بنقدم لك نكهات غرب أفريقيا الأصلية. أطباقنا متحضرة باحتراف وضيافتنا الدافئة بتخلي تجربتك لا تُنسى." /></p>
                    </div>
                    <div class="widget">
                        <ul class="social_icons social_white social_style1 rounded_social">
                                @foreach($socialMediaHandles as $handle)
                                <li>
                                    @if($handle->social_media === 'facebook')
                                        <a href="{{ "https://www.facebook.com/" . $handle->handle }}" target="_blank"><i class="fa fa-facebook-square"></i></a>
                                    @elseif($handle->social_media === 'instagram')
                                        <a href="{{ "https://www.instagram.com/" . $handle->handle }}" target="_blank"><i class="fa fa-instagram"></i></a>
                                    @elseif($handle->social_media === 'youtube')
                                        <a href="{{ "https://www.youtube.com/" .$handle->handle }}" target="_blank"><i class="fa fa-youtube"></i></a>
                                    @elseif($handle->social_media === 'tiktok')
                                        <a href="{{ "https://www.tiktok.com/@" . $handle->handle }}" target="_blank"><i class="fa fa-globe"></i></a>
                                    @endif
                                </li>
                                @endforeach                      
                        </ul>
                    </div>
        		</div>
        	            <div class="col-6 col-md-3 footer-links-col">
                	<div class="widget">
                        <h6 class="widget_title"><x-bi en="Links" ar="روابط" /></h6>
                    <ul class="widget_links footer-links-list">
                            <li><a href="{{ route('home') }}"><x-bi en="Home" ar="الرئيسية" /></a></li>
                            <li><a href="{{ route('menu') }}"><x-bi en="Our Menu" ar="المنيو" /></a></li>
                            <li><a href="{{ route('about') }}"><x-bi en="About us" ar="عنّا" /></a> </li>
                            <li><a href="{{ route('contact') }}"><x-bi en="Contact us" ar="اتصل بنا" /></a></li>
                            
                            <li> <a href="https://wa.me/{{ config('site.phone') }}" target="_blank" ><i class="fa fa-whatsapp"></i> <x-bi en="Chat us on WhatsApp" ar="كلمنا على واتساب" /></a></li>
                        </ul>
                    </div>
                </div>
        	            <div class="col-6 col-md-3 footer-contact-col">
                	<div class="widget">
                        <h6 class="widget_title"><x-bi en="Contact Info" ar="معلومات التواصل" /></h6>
                    <ul class="contact_info contact_info_light footer-contact-list">
                            <li> <i class="ti-location-pin"></i> <p>{{ config('site.address') }}</p></li>
                            <li> <i class="ti-map"></i> <a href="{{ config('site.google_maps_link') }}" target="_blank" rel="noopener noreferrer">Google Maps</a></li>

                            <li> <i class="ti-email"></i>  <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a> </li>
                        
                            <li> <i class="ti-mobile"></i> <p>{{ config('site.phone') }}</p> </li>


                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="bottom_footer border-top-tran">
                    <div class="row">
                        <div class="col-12">
                            <p class="mb-0 text-center"><script>document.write(new Date().getFullYear());</script> &copy; <x-bi en="All Rights Reserved" ar="كل الحقوق محفوظة" /></p>
                        </div>
                        <div class="col-12 d-none">
                            <ul class="list_none footer_link text-center">
                                <li><a href="{{ route('privacy.policy') }}"><x-bi en="Privacy Policy" ar="سياسة الخصوصية" /></a></li>
                                <li><a href="{{ route('terms.conditions') }}"><x-bi en="Terms & Conditions" ar="الشروط والأحكام" /></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- END FOOTER -->
