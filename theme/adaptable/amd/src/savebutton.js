/* jshint ignore:start */
define(['jquery', 'theme_bootstrapbase/bootstrap', 'core/log'], function($, bootstrap, log) {

    "use strict"; // ... jshint ;_;.

    log.debug('Adaptable savebutton.js function called');

    return {
        init: function() {
            $(document).ready(function($) {

                $("#savediscardsection").hide();

                $('#adminsettings :input').on('change input', function() {
                    $("#savediscardsection").fadeIn('slow');
                });

                $("#adminsubmitbutton").click(function() {
                    $("#adminsettings").submit();
                });
                $("#adminresetbutton").click(function() {
                    var result = confirm("This resets any changes made since loading this page. Are you sure?")
                    if (result == true) {
                        $('#adminsettings')[0].reset();
                        $("#savediscardsection").hide();
                    }
                });

                $(".colourdialogue").click(function() {
                    $("#savediscardsection").fadeIn('slow');
                });
            });
        }
    };
});
/* jshint ignore:end */
