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

                // Get already selected courseids
                var courses = [];
                $("#pathcourselist li").each(function() {
                    courses.push($(this).data('courseid'));
                });

                // Ajax stuff to get list
                // call the web service
                ajax.call([{
                    methodname: 'local_iomad_learningpath_getprospectivecourses',
                    args: {companyid: companyid, filter: filter, excludeids: courses },
                    done: function(courses) {
                        apply_filter(courses);
                    },
                    fail: notification.exception,
                }]);

            });


            // Add hover effect.
            // Bind on ul as li entries are dynamic!
            $('#prospectivelist, #pathcourselist').on('mouseenter', 'li', function() {
                $(this).addClass("text-primary");
            });
            $('#prospectivelist, #pathcourselist').on('mouseleave', 'li', function() {
                $(this).removeClass("text-primary");
            });


            // Add click handler for adding course
            $('#prospectivelist').on('click', 'li', function() {
                courseid = $(this).data('courseid');
                
                // Save the HTML for later
                html = $(this).html();

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

                // Add the new one
                $('#pathcourselist').append('<li class="text-truncate" data-courseid="' + courseid + '">' + html + '</li>');
            });


            // Add click handler for removing course
            $('#pathcourselist').on('click', 'li', function() {
                courseid = $(this).data('courseid');
                
                // Save the HTML for later
                html = $(this).html();

                // Remove the course from the path
                ajax.call([{
                    methodname: 'local_iomad_learningpath_removecourses',
                    args: {pathid: pathid, courseids: [courseid]},
                    done: function(result) {
                    },
                    fail: notification.exception
                }]);

                // Remove the clicked li
                $(this).remove();

                // Add the new one
                $('#prospectivelist').append('<li class="text-truncate" data-courseid="' + courseid + '">' + html + '</li>');
            });

             
            // Make list sortable somehow
            $("#pathcourselist").sortable({
                update: function(ev, ui) {

                    // Get already selected courseids
                    var courses = [];
                    $("#pathcourselist li").each(function() {
                        courses.push($(this).data('courseid'));
                    });

                    // Reorder
                    ajax.call([{
                        methodname: 'local_iomad_learningpath_ordercourses',
                        args: {pathid: pathid, courseids: courses},
                        done: function(result) {},
                        fail: notification.exception
                    }]);
                }
            });

        }
    };

});
