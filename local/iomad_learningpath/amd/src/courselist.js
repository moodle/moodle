// Javascript module for courselist page
// Copyright 2018 Howard Miller (howardsmiller@gmail.com)

define(['jquery', 'jqueryui', 'core/config', 'core/ajax', 'core/notification'], function($, jqui, mdlcfg, ajax, notification) {

    return {

        init: function(companyid, pathid) {


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
                    args: {companyid: companyid, filter: filter, excludeids: [] },
                    done: function(courses) {
                        apply_filter(courses);
                    },
                    fail: notification.exception,
                }]);

            });


            // Add hover effect.
            // Bind on ul as li entries are dynamic!
            $('#prospectivelist').on('mouseenter', 'li', function() {
                $(this).addClass("text-primary");
            });
            $('#prospectivelist').on('mouseleave', 'li', function() {
                $(this).removeClass("text-primary");
            });


            // Add click handler for adding course
            $('#prospectivelist').on('click', 'li', function() {
                courseid = $(this).data('courseid');

                // Add the course to the path
                ajax.call([{
                    methodname: 'local_iomad_learningpath_addcourses',
                    args: {pathid: pathid, courseids: [courseid]},
                    done: function(result) {
                    },
                    fail: notification.exception,
                }]);

                // Remove the clicked li
                $(this).remove();
            });

        }
    };

});
