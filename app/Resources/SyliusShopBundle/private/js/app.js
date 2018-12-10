jQuery(document).ready(function() {
    // Initialize Isotope
    function initIsotope() {
        var $megaMenu = $('.megamenus'),
            $grid = $('.menuGrid', $megaMenu);
        if ($grid.data('isotope')) $grid.isotope('destroy');
        if ($(window).width() >= 1200) {
            $megaMenu.css({opacity: 0, 'z-index': -1, display: 'block'});
            $grid.isotope({
                itemSelector: '.menuGrid__cell',
            });
            $megaMenu.removeAttr('style');
        }
    }
    $(window).on('resize', function() {
        initIsotope();
    });
    initIsotope();



    // Main Menu
    $(".dropdown").hover(function() {
        $('.dropdown-menu', this).stop().fadeIn("fast");
    }, function() {
        $('.dropdown-menu', this).stop().fadeOut("fast");
    });


    // $(window).on('resize load',function(){
    //     if ($(window).width() > 767){
    //         calcWidth();
    //         $('.nav-link--more').show();
    //     } else {
    //         $('.nav-link--more').hide();
    //     }
    // });

    function calcWidth() {
        var navWidthDefault = $('.nav').outerWidth(true);       // defaultni sirka <ul>
        var navWidth = 0;                                       // = soucet sirky vsech itemu v menu

        $('.nav .nav-item:not(.nav-link--more)').each(function(i, el) {
            navWidth += $(this).outerWidth( true );

            if (navWidth >= ( navWidthDefault - 150 ) ) {
                if(!$(this).find('a').hasClass('nav-link--more')) {
                    $(this).addClass('menu-extra menu-extra-hidden');
                }
            }
        });

        $('.nav').css('opacity', 1);
    };

    $(".header__menu__toggler").click(function(){
        $(".menu__nav--mobile").toggleClass("menu__nav--mobile--open");
        //$(".header__top").toggleClass("header__top--open");
        $(".header__menu__toggler").toggleClass("header__menu__toggler--open");
        $(".body").toggleClass("body--open");
    });

    // Slick Carousel
    var $carousel = $(".carousel");
    var handle = $('.ui-slider-handle__sum');
    var slider;
  
    $carousel.slick({
        speed : 300,
        height: 200,
        arrows: false,
        slidesToShow: 3,
        slidesToScroll: 3,
        infinite: false,
        responsive: [
            {
                breakpoint: 991,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                }
            }
        ]
    });

    $carousel.on('swipe', function(event, slick, direction) {
        console.log(direction);
        console.log(slick.currentSlide);

        if($(window).width() > 992) {
            slider.slider('value', slick.currentSlide / 3);
            handle.find('strong').text( slick.currentSlide + 3 );
        } else {
            slider.slider('value', slick.currentSlide);
            handle.find('strong').text( slick.currentSlide + 1);
        }
    });

    var slick = $carousel.slick( "getSlick" );
  
    if($(window).width() > 992) {
        slider = $( ".slider" ).slider({
            min : 0,
            max : 4,
            create: function() {
                handle.find('strong').text( "3" );
                handle.find('span').text("/" + slick.slideCount);
            },
            slide: function(event, ui) {
                goTo = Math.round( ui.value * (slick.slideCount) / 5 );
                // console.log( goTo );
                handle.find('strong').text( (ui.value * 3) + 3 );
                $carousel.slick( "goTo", goTo );
            }
        });
    } else {
        slider = $( ".slider" ).slider({
            min : 0,
            max : 14,
            create: function() {
                handle.find('strong').text( "1" );
                handle.find('span').text("/" + slick.slideCount);
            },
            slide: function(event, ui) {      
                handle.find('strong').text( ui.value + 1 );
            },
            stop: function( event, ui ) {
                goTo = ui.value;
                // console.log( goTo );
                handle.find('strong').text( ui.value + 1 );
                $carousel.slick( "goTo", goTo );
            }
        });
    }

    // Product detail images
    if ($('.product-detail__img-featured').length) {
        $('.product-detail__img-other a').click(function (e) {
            e.preventDefault();
            var dataImg = $(this).attr('data-img');
            $('.product-detail__img-featured img').attr('src', dataImg);
        });
    }

    if($('.gallery-items').length) {
        var images = []; // Pole pro obrazky

        $('.gallery-items a').each(function(){
            var fullImg = $(this).attr('data-full');

            images.push({
                imageUrl_img: fullImg,
            })
        });

        $('.product-detail__img-featured').magnificPopup({
            key: 'my-popup',
            items: images,
            type: 'inline',
            inline: {
            // Define markup. Class names should match key names.
            markup: '<div class="popup"><div class="mfp-close"></div>'+
                '<div class="popup__img">'+
                '<div class="mfp-imageUrl"></div>'+
                '</a>'+
                '</div>',
            },
            gallery: {
                enabled: true,
            },
        });

        // Pred otevrenim zjistim index oteviraneho obrazku a ten otevru v popupu
        $('.product-detail__img-featured').on('mfpBeforeOpen', function() {
            var imgUrl = $(this).find('img').attr('src');
            var magnificPopup = $.magnificPopup.instance;

            $.each(images, function(key, value){
                if( value.imageUrl_img == imgUrl) {
                    magnificPopup.goTo(key);
                }
            });
        });
    }
  
    // Cart popup widget
    $("#sylius-cart-button")
        .on('click', function(e) {
            e.preventDefault();
        })
        .popover({
            html : true,
            content: function() {
              var content = $(this).attr("data-popover-content");
              return $(content).children(".popover-body").html();
            },
            title: function() {
              var title = $(this).attr("data-popover-content");
              return $(title).children(".popover-heading").html();
            }
        });

    // Hide BS flash alert
    $(".alert-flash").delay(4000).slideUp(300, function() {
        $(this).alert('close');
    });

    // Wtf bug in safari with hidden main menu
    // $(document).calcWidth();
    
    // Category simple filter redirect
    $(".select__filter").on('change', function(e) {
        var optionSelected = $("option:selected", this);
        window.location.href = optionSelected.data('href');
    });

    // $('.menuGrid').isotope({
    //     itemSelector: '.menuGrid__cell',
    //     masonry: {
    //         columnWidth: '25%';
    //     }
    // });
    
    // Phone number mask
    $('input[data-mask-phone]').unmask().mask('+000 000 000 000', {
        placeholder: '+420 123 456 789',
        selectOnFocus: true
    });

    // Email mask
    $('input[data-mask-email]').unmask().mask("A", {
        translation: {
            "A": { pattern: /[\w@\-.+]/, recursive: true }
        },
        placeholder: 'jan.novak@seznam.cz'
    });
});

// Menu init
;(function(window) {
    // Mobile menu
    var _MLMenuIsInited = false,
        MLMenuInit = function() {
            if(window.innerWidth < 1200 && !_MLMenuIsInited){
                var menuEl = document.getElementById('ml-menu'),
                    mlmenu = new MLMenu(menuEl, {
                        // breadcrumbsCtrl : true, // show breadcrumbs
                        // initialBreadcrumb : 'all', // initial breadcrumb text
                        backCtrl : false, // show back button
                        // itemsDelayInterval : 60, // delay between each menu item sliding animation
                        //->onItemClick: loadDummyData // callback: item that doesn´t have a submenu gets clicked - onItemClick([event], [inner HTML of the clicked item])
                    });
                _MLMenuIsInited = true;
            }
        };

    if (!document.body.classList.contains('checkout')) {
        window.addEventListener("resize", MLMenuInit);
        MLMenuInit();
    }
})(window);