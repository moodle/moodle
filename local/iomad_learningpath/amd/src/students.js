// Javascript module for courselist page
// Copyright 2018 Howard Miller (howardsmiller@gmail.com)

define(['jquery', 'jqueryui', 'core/config', 'core/ajax', 'core/notification'], function($, jqui, mdlcfg, ajax, notification) {

    return {

        init: function(companyid, pathid) {


            /**
             * Handle response from filter ajax
             * @param array users
             */
            function apply_filter(users) {

                // Check if there are any students
                $('#prospectivelist li').remove();
                if (users.length) {
                    $('#noprospective').hide();
                } else {
                    $('#noprospective').show();
                    return;
                }

                // Check if there are too many students
                if (users.length > 30) {
                    $('#toomanyprospective').show();
                    return;
                } else {
                    $('#toomanyprospective').hide();
                }

                // Display updated list.
                var items = [];
                $.each(users, function(id, user) {
                    items.push('<li class="text-truncate" data-userid="' + user.id + '"><i class="fa fa-user"></i> ' + user.fullname + '</li>');
                });
                $('#prospectivelist').append(items.join(''));
            }


            /*
             * Populate/filter user list
             * @param string filter
             */
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

            /**
             * Bind events for user_list function
             */
            $(window).on('load', user_list(''));
            $('#userfilter').on('input', function() {
                var filter = $(this).val();
                user_list(filter);
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
             * Handle response from add/display path users
             * @param array users
             */
            function apply_pathusers(users) {

                // Check if there are any users
                $('#pathuserlist li').remove();
                if (users.length) {
                    $('#nopathusers').hide();
                } else {
                    $('#nopathusers').show();
                    return;
                }

                // Display updated list.
                var items = [];
                $.each(users, function(id, user) {
                    items.push('<li class="text-truncate" data-userid="' + user.id + '"><i class="fa fa-user"></i> ' + user.fullname + '</li>');
                });
                $('#pathuserlist').append(items.join(''));
            }


            /*
             * Populate pathuser list
             * @param string filter
             */
            function pathuser_list() {

                // Ajax stuff to get list
                // call the web service
                ajax.call([{
                    methodname: 'local_iomad_learningpath_getusers',
                    args: {companyid: companyid, pathid: pathid},
                    done: function(users) {
                        apply_pathusers(users);
                    },
                    fail: notification.exception,
                }]);
            }


            /**
             * Bind events for user_list function
             */
            $(window).on('load', pathuser_list());


            /**
             * Add click handler for adding user
             * (bind on list as li's are dynamic)
             */
            $('#prospectivelist').on('click', 'li', function() {
                userid = $(this).data('userid');
                
                // Save the HTML for later
                html = $(this).html();

                // Add the course to the path
                ajax.call([{
                    methodname: 'local_iomad_learningpath_addusers',
                    args: {pathid: pathid, userids: [userid]},
                    done: function(result) {
                    },
                    fail: notification.exception,
                }]);

                pathuser_list();
            });


            /**
             * Add click handler for removing user
             */
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

             

        }
    };

});
