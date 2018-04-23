// Javascript module for courselist page
// Copyright 2018 Howard Miller (howardsmiller@gmail.com)

define(['jquery', 'jqueryui', 'core/config', 'core/ajax', 'core/notification', 'core/templates'], function($, jqui, mdlcfg, ajax, notification, templates) {

    return {

        init: function(companyid, pathid) {


            /**
             * Enable Bootstrap tooltips
             */
            require(['theme_boost/loader']);
            require(['theme_boost/tooltip'], function() {
                $('[data-toggle="tooltip"]').tooltip();
            });


            /**
             * Handle response from filter ajax
             * @param array courses - full course objects
             */
            function apply_filter(courses) {

                // Show/hide 'no courses' message.
                $('#prospectivelist li').remove();
                if (courses.length) {
                    $('#noprospective').hide();
                } else {
                    $('#noprospective').show();
                    return;
                }

                // Template for course list
                var context = {courses: courses};
                templates.render('local_iomad_learningpath/prospectivelist', context)
                    .done(function(html) {
                        $('#prospectivelist').append(html);
                    })
                    .fail(notification.exception);

                var items = [];
                $.each(courses, function(id, course) {
                    items.push('<li class="text-truncate"><i class="fa fa-globe"></i> ' + course.fullname +
                        ' <i data-courseid="' + course.id + '" class="path-add fa fa-plus text-success"></i></li>');
                });
                $('#prospectivelist').append(items.join(''));
            }


            /**
             * Setup filter on prospective courses box.
             * @param string filter
             */
            function course_list() {

                var filter = $('#coursefilter').val();

                // Ajax stuff to get list
                // call the web service
                ajax.call([{
                    methodname: 'local_iomad_learningpath_getprospectivecourses',
                    args: {pathid: pathid, filter: filter},
                    done: function(courses) {
                        apply_filter(courses);
                    },
                    fail: notification.exception,
                }]);

            }


            /**
             * Bind events for course_list function
             */
            $(window).on('load', course_list());
            $('#coursefilter').on('input', function() {
                course_list();
            });



            /**
             * Add hover effect.
             * Bind on ul as li entries are dynamic!
             */
            $('#prospectivelist, #pathcourselist').on('mouseenter', 'li', function() {
                $(this).addClass("text-primary");
            });
            $('#prospectivelist, #pathcourselist').on('mouseleave', 'li', function() {
                $(this).removeClass("text-primary");
            });


            /**
             * Handle response from add/display selected courses
             * @param array courses - full course objects
             */
            function apply_pathcourses(courses) {

                // Show/hide 'no courses' message.
                $('#pathcourselist li').remove();
                if (courses.length) {
                    $('#nopathcourses').hide();
                } else {
                    $('#nopathcourses').show();
                    return;
                }

                var items = [];
                $.each(courses, function(id, course) {
                    items.push('<li class="text-truncate"><i class="fa fa-globe"></i> ' + course.fullname +
                        ' <i data-courseid="' + course.id + '" class="path-delete fa fa-trash text-danger"></i></li>');
                });
                $('#pathcourselist').append(items.join(''));
            }


            /*
             * Populate pathcourse list
             * @param string filter
             */
            function pathcourse_list() {

                // Ajax stuff to get list
                // call the web service
                ajax.call([{
                    methodname: 'local_iomad_learningpath_getcourses',
                    args: {pathid: pathid},
                    done: function(courses) {
                        apply_pathcourses(courses);
                    },
                    fail: notification.exception,
                }]);
            }


            /**
             * Bind events for user_list function
             */
            $(window).on('load', pathcourse_list());


            /**
             * Add click handler for adding course
             */
            $('#prospectivelist').on('click', '.path-add', function() {
                var courseid = $(this).data('courseid');

                // Add the course to the path
                ajax.call([{
                    methodname: 'local_iomad_learningpath_addcourses',
                    args: {pathid: pathid, courseids: [courseid]},
                    done: function() {
                        pathcourse_list();
                        course_list();
                    },
                    fail: notification.exception,
                }]);
            });


            /**
             * Add click handler for removing course
             */
            $('#pathcourselist').on('click', '.path-delete', function() {
                var courseid = $(this).data('courseid');

                // Remove the course from the path
                ajax.call([{
                    methodname: 'local_iomad_learningpath_removecourses',
                    args: {pathid: pathid, courseids: [courseid]},
                    done: function() {
                        pathcourse_list();
                        course_list();
                    },
                    fail: notification.exception
                }]);
            });


            // Make list sortable somehow
            $("#pathcourselist").sortable({
                update: function() {

                    // Get already selected courseids
                    var courses = [];
                    $("#pathcourselist .path-delete").each(function() {
                        courses.push($(this).data('courseid'));
                    });

                    // Reorder
                    ajax.call([{
                        methodname: 'local_iomad_learningpath_ordercourses',
                        args: {pathid: pathid, courseids: courses},
                        done: function() {},
                        fail: notification.exception
                    }]);
                }
            });

        }
    };

});
