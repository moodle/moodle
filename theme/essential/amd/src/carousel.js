/* jshint ignore:start */
define(['jquery', 'theme_bootstrapbase/bootstrap', 'core/log'], function($, bootstrap, log) {

    "use strict"; // jshint ;_;

    log.debug('Essential carousel AMD');

    return {
        init: function(data) {
            log.debug('Essential carousel AMD init, slide interval: ' + data.slideinterval + ', slideright: ' + data.slideright);
            $(document).ready(function($) {
                $("#essentialCarousel").carousel({
                    interval: data.slideinterval,
                    dirright: data.slideright
                });
            });
        }
    };
});
/* jshint ignore:end */
