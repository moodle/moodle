// Javascript module for courselist page
// Copyright 2018 Howard Miller (howardsmiller@gmail.com)

define(['jquery', 'jqueryui', 'core/config', 'core/ajax', 'core/notification'], function($, jqui, mdlcfg, ajax, notification) {

    return {

        init: function() {

            /**
             * Click learning path open button
             */
            $('.lpbutton').on('click', function() {
                var id = $(this).data('id');

                $('[data-pathid="' + id + '"]').show(400);
                $('.pathdescription').hide(400);
            })
        }
    }
});
