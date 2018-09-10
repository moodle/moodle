/**
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* jshint ignore:start */
define(['jquery', 'theme_bootstrapbase/bootstrap', 'core/log'], function($, boot, log) {

    "use strict"; // jshint ;_;

    log.debug('Essential affix AMD');

    return {
        init: function() {
            $(document).ready(function($) {
                if ($("#essentialnavbar").length) {
                    var $essentialnavbar = $("#essentialnavbar");
                    var pageheaderHeight = $("#page-header").height();
                    log.debug('Essential affix AMD pageheaderHeight: ' + pageheaderHeight);
                    $essentialnavbar.affix({
                        offset: {
                            top: function() {
                                pageheaderHeight = $("#page-header").height();
                                log.debug('Essential affix offset AMD phh: ' + pageheaderHeight);
                                var wst = $(window).scrollTop();
                                log.debug('Essential affix offset AMD wst: ' + wst);
                                var diff = pageheaderHeight - wst;
                                log.debug('Essential affix offset AMD diff: ' + diff);
                                if (diff < 0) {
                                    diff = 0;
                                }
                                $essentialnavbar.css('top', diff + 'px');
                                return pageheaderHeight;
                            }
                        }
                    });
                    $(window).resize(function() {
                        pageheaderHeight = $("#page-header").height();
                        log.debug('Essential affix resize AMD phh: ' + pageheaderHeight);
                        var wst = $(window).scrollTop();
                        log.debug('Essential affix resize AMD wst: ' + wst);
                        var diff = pageheaderHeight - wst;
                        log.debug('Essential affix resize AMD diff: ' + diff);
                        if (diff < 0) {
                            diff = 0;
                        }
                        $essentialnavbar.css('top', diff + 'px');
                    });
                }
            });
            log.debug('Essential affix AMD init');
        }
    };
});
/* jshint ignore:end */
