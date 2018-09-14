/**
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* jshint ignore:start */
define(['jquery', 'core/log'], function($, log) {

    "use strict"; // jshint ;_;

    log.debug('Essential Course Navigation AMD.');

    return {
        init: function() {
            log.debug('Essential Course Navigation AMD init.');
            $(document).ready(function($) {
                var navbarHeight = 0;
                var duration = 500;
                var navbar = 0;
                if ($('.navbar').length) {
                    navbar = $('.navbar');
                    navbarHeight = navbar.height();
                    log.debug('Essential Course Navigation AMD navbar height: ' + navbarHeight);
                }

                var pageHrefBase = location.href;
                var hrefIndex = location.href.indexOf("#");
                log.debug('Essential Course Navigation AMD navigation pageHrefBase 1: ' + pageHrefBase);
                log.debug('Essential Course Navigation AMD navigation hrefIndex: ' + hrefIndex);
                if (hrefIndex != -1) {
                    pageHrefBase = location.href.substring(0, hrefIndex);
                    log.debug('Essential Course Navigation AMD navigation pageHrefBase 2: ' + pageHrefBase);
                    // We are an anchor on the same site - otherwise why would this run?  Therefore still need to scroll.
                    var url = location.href;
                    var hash = url.substring(url.indexOf("#") + 1);
                    log.debug('Essential Course Navigation AMD navigation page hash: ' + hash);
                    var target = $('[id="' + hash + '"]');
                    var targetOffset = target.offset().top;
                    var scrollTo = targetOffset;
                    if (navbar) {
                        if (navbar.css('position') == 'fixed') {
                            scrollTo = scrollTo - navbarHeight;
                        } else {
                            // Strange but true.
                            scrollTo = scrollTo - (navbarHeight * 2);
                        }
                    }
                    $('html, body').animate({scrollTop: scrollTo}, duration);
                    log.debug('Essential Course Navigation AMD navigation page scrollTop: ' + scrollTo);
                    log.debug('Essential Course Navigation AMD navigation page target offset: ' + targetOffset);
                }
                $('a[href*="\\#section-"]').each(function() {
                    var linkHrefBase = this.href.substring(0, this.href.indexOf('#'));
                    log.debug('Essential Course Navigation AMD navigation element: ' + $(this).attr('href'));
                    log.debug('Essential Course Navigation AMD navigation linkHrefBase: ' + linkHrefBase);
                    if (pageHrefBase == linkHrefBase) {
                        $(this).click(function(e) {
                            e.preventDefault();
                            var url = this.href;
                            log.debug('Essential Course Navigation AMD navigation element url: ' + url);
                            var hash = url.substring(url.indexOf('#') + 1);
                            log.debug('Essential Course Navigation AMD navigation element hash: ' + hash);
                            var target = $('[id="' + hash + '"]');
                            var targetOffset = target.offset().top;
                            var scrollTo = targetOffset;
                            if (navbar) {
                                if (navbar.css('position') == 'fixed') {
                                    scrollTo = scrollTo - navbarHeight;
                                } else {
                                    // Strange but true.
                                    scrollTo = scrollTo - (navbarHeight * 2);
                                }
                            }
                            $('html, body').animate({scrollTop: scrollTo}, duration);
                            log.debug('Essential Course Navigation AMD navigation element scrollTop: ' + scrollTo);
                            log.debug('Essential Course Navigation AMD navigation element target offset: ' + targetOffset);
                        });
                    }
                });
            });
        }
    };
});
/* jshint ignore:end */
