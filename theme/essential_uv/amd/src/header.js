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

    log.debug('Essential header AMD');

    return {
        init: function() {
            $(document).ready(function($) {
                if (($("#page-header .titlearea").length) && ($("#essentialicons").length)) {
                    var titlearea = $("#page-header .titlearea");
                    $("#essentialicons").on('hide', function() {
                        titlearea.fadeIn();
                    });
                    $("#essentialicons").on('show', function() {
                        titlearea.fadeOut();
                    });
                }
                if (($("#essentialicons").length) && ($("#essentialnavbar").length)) {
                    var $essentialnavbar = $("#essentialnavbar");
                    $("#essentialicons").on('hidden', function() {
                        var pageheaderHeight = $("#page-header").height();
                        log.debug('Essential header hidden AMD phh: ' + pageheaderHeight);
                        var wst = $(window).scrollTop();
                        log.debug('Essential header hidden AMD wst: ' + wst);
                        var diff = pageheaderHeight - wst;
                        log.debug('Essential header hidden AMD diff: ' + diff);
                        if (diff < 0) {
                            diff = 0;
                        }
                        $essentialnavbar.css('top', diff + 'px');
                    });
                    $("#essentialicons").on('shown', function() {
                        var pageheaderHeight = $("#page-header").height();
                        log.debug('Essential header shown AMD phh: ' + pageheaderHeight);
                        var wst = $(window).scrollTop();
                        log.debug('Essential header shown AMD wst: ' + wst);
                        var diff = pageheaderHeight - wst;
                        log.debug('Essential header shown AMD diff: ' + diff);
                        if (diff < 0) {
                            diff = 0;
                        }
                        $essentialnavbar.css('top', diff + 'px');
                    });
                }
            });
            log.debug('Essential header AMD init');
        }
    };
});
/* jshint ignore:end */
