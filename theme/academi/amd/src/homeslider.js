define(['jquery', 'theme_academi/jquery.sudoSlider'], function($) {
    var defaults = {
        autoplay: false,
        interval: 500,
    };
    var Carousel = function(selector, options) {
        var results = $.extend(defaults, options);
        this.initializeslider(selector, results);
    };

    // Initialize the slider.
    Carousel.prototype.initializeslider = function(selector, data) {
        var autostopped = false;
        var sudoSlider = $(selector).sudoSlider({
            prevNext: true,
            prevHtml: '.homepage-carousel .prevBtn.carousel-control',
            nextHtml: '.homepage-carousel .nextBtn.carousel-control',
            speed: 1400,
            ease: 'swing',
            responsive: true,
            updateBefore: true,
            useCSS: true,
            interruptible: false,
            numeric: true,
            pause: (data.autoplay == 'false') ? false : data.interval,
            auto: (data.autoplay == 'true') ? true : false,
            customLink: ".homepage-carouselLink",
            afterAnimation: function(t) {
                $('.homecarousel-slide-item.carousel-item').not('[data-slide="' + t + '"]').removeClass('active');
                $('.homecarousel-slide-item.carousel-item[data-slide="' + t + '"]').addClass('active');
                $('.slide-text').show();
            },
            beforeAnimation: function() {
                animation();
            }
        });

        sudoSlider.mouseenter(function() {
            var auto = sudoSlider.getValue('autoAnimation');
            if (auto) {
                sudoSlider.stopAuto();
            } else {
                autostopped = true;
            }
        }).mouseleave(function() {
            if (!autostopped) {
                sudoSlider.startAuto();
            }
        });

        /**
         * Animation for slider.
         */
        function animation() {
            var $this = $('.slide-content .slide-text');
            var $content = $this.find('.heading-content [data-animation ^= "animated"]');
            var index = 0;
            if ($content != "undefined" && $content.length != "") {
                $content.css({'opacity': 0});
                var $time = setInterval(function() {
                    $this = $content;
                    var da = $content.eq(index);
                    var ani = da.attr('data-animation');
                    da.addClass(ani);
                    da.css({'opacity': 1});
                    index++;
                    if (index == $this.length) {
                        clearInterval($time);
                    }
                    doAnimations(da);
                }, 400);
            }
        }

        /**
         * Sider animation.
         * @param {string} elems Elements.
         */
        function doAnimations(elems) {
            var animEndEv = 'webkitAnimationEnd animationend';
            elems.each(function() {
              var $this = $(this),
                  $animationType = $this.data('animation');
              $this.addClass($animationType).one(animEndEv, function() {
                $this.removeClass($animationType);
              });
            });
          }
    };

    return {
        init: function(selector, options) {
            return new Carousel(selector, options);
        }
    };
});