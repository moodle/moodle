// Javascript module for manage page
// Copyright 2018 Howard Miller (howardsmiller@gmail.com)

define(['jquery', 'core/config', 'core/ajax', 'core/notification'], function($, mdlcfg, ajax, notification) {

    return {

        init: function() {

            // Enable Bootstrap tooltips
            require(['theme_boost/loader']);
            require(['theme_boost/tooltip'], function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

            // Update the eye icon
            function _redraw(icon, state) {
                icon.removeClass('fa-eye fa-eye-slash');
                if (state == 1) {
                    icon.addClass('fa-eye');
                } else {
                    icon.addClass('fa-eye-slash');
                }
            }

            // Handle active/hidden
            $('.lp_active').click(function() {
                var icon = $(this).find('i');
                var id = $(this).data('id');
                var state = $(this).data('state');

                // flip current state
                if (state == 0) {
                    state = 1;
                } else {
                    state = 0;
                }
                $(this).data('state', state);

                // call the web service
                ajax.call([{
                    methodname: 'local_iomad_learningpath_activate',
                    args: { pathid: id, state: state },
                    done: _redraw(icon, state),
                    fail: notification.exception,
                }]);

                // false stops the normal link behaviour!
                return false;
            });

        }
    };

});
