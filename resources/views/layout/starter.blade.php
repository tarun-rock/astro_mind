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

