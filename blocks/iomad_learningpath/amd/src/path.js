// Javascript module for courselist page
// Copyright 2018 Howard Miller (howardsmiller@gmail.com)

define(['jquery', 'jqueryui', 'core/config', 'core/ajax', 'core/notification'], function($, jqui, mdlcfg, ajax, notification) {

    return {

        init: function() {

            // Speed for open/close transition
            var speed = 400;

            /**
             * Click learning path open button
             */
            $('.lpbutton').on('click', function() {
                var id = $(this).data('id');

                $('[data-pathid="' + id + '"]').show(speed);
                $('.pathdescription').hide(speed);
                $('.lpreturn').show(speed);
            });

            /**
             * Click return to learning paths button
             */
            $('.lpreturn').on('click', function() {
                $('.path_courses').hide(speed);
                $('.pathdescription').show(speed);
                $('.lpreturn').hide(speed);
            });
        }
    }
});
