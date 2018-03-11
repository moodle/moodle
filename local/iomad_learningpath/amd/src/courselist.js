// Javascript module for courselist page
// Copyright 2018 Howard Miller (howardsmiller@gmail.com)

define(['jquery', 'core/config', 'core/ajax', 'core/notification'], function($, mdlcfg, ajax, notification) {

    return {

        init: function(companyid) {

            // Enable Bootstrap tooltips
            require(['theme_boost/loader']);
            require(['theme_boost/tooltip'], function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

            // Handle response from filter ajax
            function apply_filter(courses) {
                $('#prospectivelist li').remove();
                var items = [];
                $.each(courses, function(id, course) {
                    items.push('<li class="text-truncate" data-courseid="' + course.id + '"><i class="fa fa-globe"></i> ' + course.fullname + '</li>');
                });
                $('#prospectivelist').append(items.join(''));
            }

            // Setup filter on prospective courses box.
            $('#coursefilter').on('input', function() {
                var filter = $(this).val();
                console.log('Filter value ' + filter );

                // Ajax stuff to get list
                // call the web service
                ajax.call([{
                    methodname: 'local_iomad_learningpath_getprospectivecourses',
                    args: {companyid: companyid, filter: filter, excludeids: [1, 2, 3] },
                    done: function(courses) {
                        apply_filter(courses);
                    },
                    fail: notification.exception,
                }]);

            });

            // Add hover effect
            $('#prospectivelist li').hover(
                function() {
                    $(this).addClass("text-primary");
                },
                function() {
                    $(this).removeClass("text-primary");
                }
            );

        }
    };

});
