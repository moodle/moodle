/**
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* jshint ignore:start */
define(['jquery', 'core/log'], function($, log) {

    "use strict"; // jshint ;_;

    log.debug('Essential navbar AMD');

    return {
        init: function(data) {
            $(document).ready(function($) {
                if ($("#essentialnavbar").length) {
                    if (data.oldnavbar) {
                        $("#page-header").css('margin-bottom', $("#essentialnavbar .navbar-inner").height() + 'px');
                    } else {
                        $("#page-header").css('margin-top', $("#essentialnavbar .navbar-inner").height() + 'px');
                    }
                }
            });
            log.debug('Essential navbar AMD init: ' + data.oldnavbar);
        }
    };
});
/* jshint ignore:end */
