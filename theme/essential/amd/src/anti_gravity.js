/**
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* jshint ignore:start */
define(['jquery', 'core/log'], function(jQuery, log) {

    "use strict"; // jshint ;_;

    log.debug('Essential anti gravity AMD initialised');

    jQuery(document).ready(function() {
        var offset = 220;
        var duration = 500;
        jQuery(window).scroll(function() {
            if (jQuery(window).scrollTop() > offset) {
                jQuery('.back-to-top').fadeIn(duration);
            } else {
                jQuery('.back-to-top').fadeOut(duration);
            }
        });

        jQuery('.back-to-top').click(function(event) {
            event.preventDefault();
            jQuery('html, body').animate({scrollTop: 0}, duration);
            return false;
        });

        jQuery('a[href="\\#region-main"]').click(function(e) {
            e.preventDefault();
            var target = jQuery("#region-main");
            jQuery('html, body').animate({scrollTop: target.height()}, duration);
            return false;
        });
    });
});
/* jshint ignore:end */
