// Javascript module for manage page
// Copyright 2018 Howard Miller (howardsmiller@gmail.com)

define(['jquery', 'core/config', 'core/ajax', 'core/notification'], function($, mdlcfg, ajax, notification) {

    return {
        init: function() {

            // Handle active/hidden
            $('.lp_active').click(function() {
                var id = $(this).data('id');
                var state = $(this).data('state');

                // flip current state
                if (state == 0) {
                    state = 1;
                } else {
                    state = 0;
                }

                // call the web service
                ajax.call([{
                    methodname: 'local_iomad_learningpath_activate',
                    args: { pathid: id, state: state },
                    done: function() { console.log('it worked') },
                    fail: notification.exception,
                }]);

                // false stops the normal link behaviour!
                return false;
            });

        }
    };

});
