/* jshint ignore:start */
define(['jquery', 'theme_bootstrapbase/bootstrap', 'core/log'], function($, bootstrap, log) {

    "use strict"; // ...jshint ;_; !!!

    log.debug('Adaptable Bootstrap AMD opt in functions');

    return {
        init: function(hasaffix) {
            $(document).ready(function($) {
                if (hasaffix) {
                    // Check that #navwrap actually exists.
                    if($("#navwrap").length > 0) {
                        $('#navwrap').affix({
                            'offset': { top: $('#navwrap').offset().top}
                        });
                    }
                }
                $('#openoverlaymenu').click(function() {
                    $('#conditionalmenu').toggleClass('open');
                });
                $('#overlaymenuclose').click(function() {
                    $('#conditionalmenu').toggleClass('open');
                });
            });

            // Conditional javascript to resolve anchor link clicking issue with sticky navbar
            // in old bootstrap version. Re: issue #919.
            // Original issue / solution discussion here: https://github.com/twbs/bootstrap/issues/1768
            if (hasaffix) {
                var shiftWindow = function() { scrollBy(0, -50) };
	            if (location.hash) shiftWindow();
	            window.addEventListener("hashchange", shiftWindow);
            }
        }
    };
});
/* jshint ignore:end */
