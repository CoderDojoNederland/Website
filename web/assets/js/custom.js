$(document).on('ready', function () {
    // initialization of go to
    $.HSCore.components.HSGoTo.init('.js-go-to');

    // initialization of carousel
    $.HSCore.components.HSCarousel.init('.js-carousel');

    // initialization of masonry
    $('.masonry-grid').imagesLoaded().then(function () {
        $('.masonry-grid').masonry({
            columnWidth: '.masonry-grid-sizer',
            itemSelector: '.masonry-grid-item',
            percentPosition: true
        });
    });

    // initialization of popups
    $.HSCore.components.HSPopup.init('.js-fancybox');

    $('#city-search').on('keyup paste', function(){
        $('[data-dojo-city]').each(function(key, el){
            var city = $(el).data('dojo-city');
            var matches = new RegExp($('#city-search').val(), 'i').exec(city);

            if(matches === null) {
                $(el).addClass('g-hidden-xs-up');
            } else {
                $(el).removeClass('g-hidden-xs-up');
            }
        });
    });
});

$(window).on('load', function () {
    // initialization of header
    $.HSCore.components.HSHeader.init($('#js-header'));
    $.HSCore.helpers.HSHamburgers.init('.hamburger');

    // initialization of HSMegaMenu component
    $('.js-mega-menu').HSMegaMenu({
        event: 'hover',
        pageContainer: $('.container'),
        breakpoint: 991
    });

    window.cookieconsent.initialise({
        "palette": {
            "popup": {
                "background": "#ffffff"
            },
            "button": {
                "background": "#e67e22",
                "text": "#ffffff"
            }
        },
        "theme": "classic",
        "position": "bottom",
        "content": {
            "message": "Deze website gebruikt cookies om te zorgen voor de best mogelijke ervaring. Door gebruik te maken van de website ga je akkoord met het plaatsen van deze cookies.",
            "dismiss": "Akkoord!",
            "link": "Meer informatie",
            "href": "/privacy"
        }
    })
});
