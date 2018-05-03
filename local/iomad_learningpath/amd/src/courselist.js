// Javascript module for courselist page
// Copyright 2018 Howard Miller (howardsmiller@gmail.com)

define(['jquery', 'jqueryui', 'core/config', 'core/ajax', 'core/notification', 'core/templates'],
    function($, jqui, mdlcfg, ajax, notification, templates) {

    return {

        init: function(companyid, pathid) {


            /**
             * Enable Bootstrap tooltips
             */
            //require(['theme_boost/loader']);
            //require(['theme_boost/tooltip'], function() {
                $('[data-toggle="tooltip"]').tooltip();
            //});


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
                var context = {
                    courses: courses,
                    wwwroot: mdlcfg.wwwroot,
                    prospective: true
                };
                templates.render('local_iomad_learningpath/prospectivelist', context)
                    .done(function(html) {
                        $('#prospectivelist').append(html);
                    })
                    .fail(notification.exception);
            }


            /**
             * Setup filter on prospective courses box.
             * @param string filter
             */
            function course_list() {

                var filter = $('#coursefilter').val();
                var category = $('#category').val();

                // Ajax stuff to get list
                // call the web service
                ajax.call([{
                    methodname: 'local_iomad_learningpath_getprospectivecourses',
                    args: {pathid: pathid, filter: filter, category: category},
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
            $('#category').on('change', function() {
                course_list();
            });



            /**
             * Add hover effect.
             * Bind on ul as li entries are dynamic!
             */
            $('#prospectivelist, .pathcourselist').on('mouseenter', 'li', function() {
                $(this).addClass("text-primary");
            });
            $('#prospectivelist, .pathcourselist').on('mouseleave', 'li', function() {
                $(this).removeClass("text-primary");
            });


            /**
             * Handle response from add/display selected courses
             * @param object pcl - the selected list (= group)
             * @param array courses - full course objects
             */
            function apply_pathcourses(pcl, courses) {

                // Show/hide 'no courses' message.
                $(pcl).find('li').remove();
                //$('.pathcourselist li').remove();
                //var nogroupcourses = $(pcl).parent().find('.nogroupcourses');
                var groupid = $(pcl).data('groupid');
                var nogroupcourses = $(".nogroupcourses[data-groupid='" + groupid + "']");
                if (courses.length) {
                    nogroupcourses.hide();
                } else {
                    nogroupcourses.show();
                    return;
                }

                // Template for course list
                var context = {
                    courses: courses,
                    wwwroot: mdlcfg.wwwroot,
                    prospective: false
                };
                templates.render('local_iomad_learningpath/pathcourselist', context)
                    .done(function(html) {
                        $(pcl).append(html);
                    })
                    .fail(notification.exception);
            }


            /*
             * Populate pathcourse list
             * @param string filter
             */
            function pathcourse_list() {

                // Ajax stuff to get list
                // call the web service
                $(".pathcourselist").each(function() {
                    var pcl = this;
                    var groupid = $(pcl).data('groupid');
                    ajax.call([{
                        methodname: 'local_iomad_learningpath_getcourses',
                        args: {pathid: pathid, groupid: groupid},
                        done: function(courses) {
                            apply_pathcourses(pcl, courses);
                        },
                        fail: notification.exception,
                    }]);
                });
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
            $('.pathcourselist').on('click', '.path-delete', function() {
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


            /**
             * Fix plus/trash icons so they are correct for the list they are in
             */
            function fix_icons() {
                $("#prospectivelist .path-delete").removeClass('fa-trash path-delete').addClass('fa-plus path-add');
                $(".pathcourselist .path-add").removeClass('fa-plus path-add').addClass('fa-trash path-delete');
            }


            /**
             * Sort added courses list into order
             */
            $(".pathcourselist").sortable({
                handle: '.lphandle',
                connectWith: '#prospectivelist',
                dropOnEmpty: true,
                update: function() {

                    // Get already selected courseids
                    var courses = [];
                    $(".pathcourselist .pathbox").each(function() {
                        courses.push($(this).data('courseid'));
                    });

                    // Reorder
                    ajax.call([{
                        methodname: 'local_iomad_learningpath_ordercourses',
                        args: {pathid: pathid, courseids: courses},
                        done: function() {},
                        fail: notification.exception
                    }]);

                    fix_icons();
                }
            });

            /**
             * Permit drag to add
             */
            $('#prospectivelist').sortable({
                handle: '.lphandle',
                connectWith: '.pathcourselist',
                dropOnEmpty: true,
                update: function() {
                    fix_icons();
                }
            });

        }
    };

});
