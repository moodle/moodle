// Javascript module for manage page
// Copyright 2018 Howard Miller (howardsmiller@gmail.com)

define(['jquery', 'core/config', 'core/ajax', 'core/notification', 'core/str'], function($, mdlcfg, ajax, notification, str) {

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

        
            // Handle delete button
            $('.lp_delete').click(function() {
                var id = $(this).data('id');
                str.get_strings([
                    {key: 'confirm', component: 'local_iomad_learningpath'},
                    {key: 'confirmdelete', component: 'local_iomad_learningpath'},
                    {key: 'yes'},
                    {key: 'no'}
                ]).done(function(s) {
                    notification.confirm(s[0], s[1], s[2], s[3], function() {
                        ajax.call([{
                            methodname: 'local_iomad_learningpath_deletepath',
                            args: { pathid: id },
                            done: function(result) {
                                location.reload();
                            },
                            fail: notification.exception,
                        }]);
                    });
                });

                // False stops normal link behaviour!!
                return false;
            });



            // Handle copy button
            $('.lp_copy').click(function() {
                var id = $(this).data('id');
                str.get_strings([
                    {key: 'confirm', component: 'local_iomad_learningpath'},
                    {key: 'confirmcopy', component: 'local_iomad_learningpath'},
                    {key: 'yes'},
                    {key: 'no'}
                ]).done(function(s) {
                    notification.confirm(s[0], s[1], s[2], s[3], function() {
                        ajax.call([{
                            methodname: 'local_iomad_learningpath_copypath',
                            args: { pathid: id },
                            done: function(result) {
                                location.reload();
                            },
                            fail: notification.exception,
                        }]);
                    });
                });

                // False stops normal link behaviour!!
                return false;
            });

        }
    };

});
