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

    log.debug('Essential Footer AMD.');

    return {
        init: function() {
            log.debug('Essential Footer AMD init.');
            $(document).ready(function($) {
                if ($("#page-footer").length) { // Might not have a footer.
                    var documentHeight = $(document).height();
                    if ($('html').height() < documentHeight) {
                        log.debug('Essential Footer AMD adjusting footer position.');
                        var pagefooter = $("#page-footer");
                        var footerOffset = pagefooter.offset().top;
                        var theOffset = pagefooter.outerHeight();
                        log.debug('Calculated page footer offset: ' + theOffset + '.');
                        theOffset = documentHeight - theOffset;
                        log.debug('Old footer offset: ' + footerOffset + '.');
                        log.debug('Calculated footer offset: ' + theOffset + '.');
                        log.debug('Old document height: ' + documentHeight + '.');
                        pagefooter.offset({top: theOffset, left: 0});
                        pagefooter.css('left', 0); // Negate the effect of the dock.
                        var newOffset = pagefooter.offset().top;
                        log.debug('New footer offset: ' + newOffset + '.');
                        log.debug('New document height: ' + $(document).height() + '.');
                    }
                }
            });
        }
    };
});
/* jshint ignore:end */
