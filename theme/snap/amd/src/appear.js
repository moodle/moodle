// jshint ignore: start
define(['jquery'],
    function(jQuery) {

        /*
         * jQuery appear plugin
         *
         * Copyright (c) 2012 Andrey Sidorov
         * licensed under MIT license.
         *
         * https://github.com/morr/jquery.appear/
         *
         * Version: 0.3.6
         */
         (function($) {
            var selectors = [];

            var check_binded = false;
            var check_lock = false;
            var defaults = {
                interval: 250,
                force_process: false
            };
            var options = {}; // GT Mod, place options in scope for entire library.
            var $window = $(window);

            var $prior_appeared = [];

            function appeared(selector) {
                return $(selector).filter(function() {
                    return $(this).is(':appeared');
                });
            }

            function process() {
                check_lock = false;
                for (var index = 0, selectorsLength = selectors.length; index < selectorsLength; index++) {
                    var $appeared = appeared(selectors[index]);

                    $appeared.trigger('appear', [$appeared]);

                    if ($prior_appeared[index]) {
                        var $disappeared = $prior_appeared[index].not($appeared);
                        $disappeared.trigger('disappear', [$disappeared]);
                    }
                    $prior_appeared[index] = $appeared;
                }
            }

            function add_selector(selector) {
                selectors.push(selector);
                $prior_appeared.push();
            }

            // "appeared" custom filter
            $.expr[':'].appeared = function(element) {
                var $element = $(element);
                if (!$element.is(':visible')) {
                    return false;
                }

                var window_left = $window.scrollLeft();
                var window_top = $window.scrollTop();
                var offset = $element.offset();
                var left = offset.left;
                var top = offset.top;

                // GT Mod - use options variable for offsets if data attribute not set.
                var appeartopoffset = $element.data('appear-top-offset') || (options.appeartopoffset || 0);
                var appearleftoffset = $element.data('appear-left-offset') || (options.appearleftoffset || 0);

                if (top + $element.height() + appeartopoffset >= window_top &&
                    top - appeartopoffset <= window_top + $window.height() &&
                    left + $element.width() + appearleftoffset >= window_left &&
                    left - appearleftoffset <= window_left + $window.width()) {
                    return true;
                } else {
                    return false;
                }
            };

            $.fn.extend({
                // watching for element's appearance in browser viewport
                appear: function(opts) {
                    // GT Mod, set options variable which is declared within scope of entire module.
                    options = $.extend({}, defaults, opts || {});
                    var selector = this.selector || this;
                    if (!check_binded) {
                        var on_check = function() {
                            if (check_lock) {
                                return;
                            }
                            check_lock = true;

                            setTimeout(process, options.interval);
                        };

                        $(window).scroll(on_check).resize(on_check);
                        $('.appear_enabled').scroll(on_check);
                        check_binded = true;
                    }

                    if (options.force_process) {
                        setTimeout(process, options.interval);
                    }
                    add_selector(selector);
                    return $(selector);
                }
            });

            $.extend({
                // force elements's appearance check
                force_appear: function() {
                    if (check_binded) {
                        process();
                        return true;
                    }
                    return false;
                }
            });
        })(function() {
            if (typeof module !== 'undefined') {
                // Node
                return require('jquery');
            } else {
                return jQuery;
            }
        }());

        return jQuery;

    }
);
