// Javascript module for courselist page
// Copyright 2018 Howard Miller (howardsmiller@gmail.com)

define(['jquery', 'jqueryui', 'core/config', 'core/ajax', 'core/notification'], function($, jqui, mdlcfg, ajax, notification) {

    return {

        init: function(companyid, pathid) {


            // Handle response from filter ajax
            function apply_filter(users) {
                $('#prospectivelist li').remove();
                if (users.length) {
                    $('#noprospective').hide();
                } else {
                    $('#noprospective').show();
                    return;
                }
                var items = [];
                $.each(users, function(id, user) {
                    items.push('<li class="text-truncate" data-userid="' + user.id + '"><i class="fa fa-user"></i> ' + user.fullname + '</li>');
                });
                $('#prospectivelist').append(items.join(''));
            }


            // Function to populate user list
            function user_list(filter) {

                // Ajax stuff to get list
                // call the web service
                ajax.call([{
                    methodname: 'local_iomad_learningpath_getprospectiveusers',
                    args: {companyid: companyid, pathid: pathid, filter: filter},
                    done: function(users) {
                        apply_filter(users);
                    },
                    fail: notification.exception,
                }]);
            }

            // Filter events
            $(window).on('load', user_list(''));
            $('#userfilter').on('input', function() {
                var filter = $(this).val();
                user_list(filter);
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
