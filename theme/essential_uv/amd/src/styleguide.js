/**
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* jshint ignore:start */
define(['jquery', 'theme_bootstrapbase/bootstrap', 'theme_essential/holder', 'core/log'], function($, bootstrap, holder, log) {

    "use strict"; // jshint ;_;

    log.debug('Essential Style Guide AMD');

    return {
        init: function() {
            $(document).ready(function($) {
                $("[data-toggle=tooltip]").tooltip();
                $("[data-toggle=popover]").popover().click(function(e) {
                    e.preventDefault();
                });
            });
            log.debug('Essential Style Guide AMD init');
        }
    };
});
/* jshint ignore:end */
