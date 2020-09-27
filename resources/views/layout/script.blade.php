	<script src="{{ asset('js/jquery.min.js') }}"></script>

    <script src="{{ asset('js/popper.min.js') }}"></script>
    
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>

    <script src="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js') }}"></script>

    
  <script src="{{ asset("js/jquery.min.js") }}"></script>
  <script src="{{ asset('js/jquery-migrate-3.0.1.min.js') }}"></script>
  <script src="{{ asset("js/popper.min.js") }}"></script>
  <script src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/jquery.easing.1.3.js') }}"></script>
  <script src="{{ asset('js/jquery.waypoints.min.js') }}"></script>
  <script src="{{ asset('js/jquery.stellar.min.js') }}"></script>
  <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
  <script src="{{asset('js/jquery.magnific-popup.min.js')}}"></script>
  <script src="{{ asset('js/aos.js') }}"></script>
  <script src="{{ asset('js/jquery.animateNumber.min.js') }}"></script>
  <script src="{{ asset('js/scrollax.min.js') }}"></script>
  <script src="{{ asset('https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false') }}"></script>
  <script src="{{ asset('js/google-map.js') }}"></script>
  <script src="{{ asset('js/main.js') }}"></script>

  <script>
    jQuery("#carousel").owlCarousel({
        autoplay: true,
        lazyLoad: true,
        loop: true,
        autoPlay : true,
        autoplaySpeed:100,
        nav: true,
        navText: ["<i class=\"fas fa-chevron-left\"></i>", "<i class=\"fas fa-chevron-right\"></i>"],
        navContainer: '.main-content .custom-nav',
        slideSpeed : 50,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1
            },

            767: {
                items:2
            },

            1024: {
                items: 5
            },

            1366: {
                items: 4
            }
        }
    });
</script>


    @yield('script_extra')

   