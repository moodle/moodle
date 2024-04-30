//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @module    local_learningpath
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'jqueryui', 'core/config', 'core/ajax', 'core/notification'], function($, jqui, mdlcfg, ajax, notification) {

    return {

        init: function(companyid, pathid) {
            function apply_filter(users) {

                // Check if there are any students
                $('#prospectivelist li').remove();
                if (users.length) {
                    $('#noprospective').hide();
                } else {
                    $('#noprospective').show();
                    $('#toomanyprospective').hide();
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
                    items.push('<li class="text-truncate" data-userid="' + user.id +
                        '"><i class="fa fa-user"></i> ' + user.fullname + '</li>');
                });
                $('#prospectivelist').append(items.join(''));
            }

            function user_list() {

                var filter = $('#userfilter').val();
                var profilefieldid = $('#profilefieldid').val();

                // Ajax stuff to get list
                // call the web service
                ajax.call([{
                    methodname: 'local_iomad_learningpath_getprospectiveusers',
                    args: {companyid: companyid, pathid: pathid, filter: filter, profilefieldid: profilefieldid},
                    done: function(users) {
                        apply_filter(users);
                    },
                    fail: notification.exception,
                }]);
            }

            $(window).on('load', user_list(''));
            $('#userfilter').on('input', function() {
                user_list();
            });
            $('#profilefieldid').on('change', function() {
                user_list();
            });

            $('#prospectivelist, #pathuserlist').on('mouseenter', 'li', function() {
                $(this).addClass("text-primary");
            });
            $('#prospectivelist, #pathuserlist').on('mouseleave', 'li', function() {
                $(this).removeClass("text-primary");
            });

            function apply_pathusers(users) {

                // Check if there are any users
                $('#pathuserlist li').remove();
                if (users.length) {
                    $('#nopathusers').hide();
                } else {
                    $('#nopathusers').show();
                    return;
                }

                var items = [];
                $.each(users, function(id, user) {
                    items.push('<li class="text-truncate" data-userid="' + user.id +
                        '"><i class="fa fa-user"></i> ' + user.fullname + '</li>');
                });
                $('#pathuserlist').append(items.join(''));
            }

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

            $(window).on('load', pathuser_list());

            $('#prospectivelist').on('click', 'li', function() {
                var userid = $(this).data('userid');

                // Add the user to the path
                ajax.call([{
                    methodname: 'local_iomad_learningpath_addusers',
                    args: {pathid: pathid, userids: [userid]},
                    done: function() {
                        pathuser_list();
                        user_list();
                    },
                    fail: notification.exception,
                }]);
            });

            $('#pathuserlist').on('click', 'li', function() {
                var userid = $(this).data('userid');

                // Remove the course from the path
                ajax.call([{
                    methodname: 'local_iomad_learningpath_removeusers',
                    args: {pathid: pathid, userids: [userid]},
                    done: function() {

                        // Update list
                        pathuser_list();
                        user_list();
                    },
                    fail: notification.exception
                }]);
            });
        }
    };
});
