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

define(['jquery', 'jqueryui', 'core/config', 'core/ajax', 'core/notification', 'core/templates'],
    function($, jqui, mdlcfg, ajax, notification, templates) {

    return {

        init: function(companyid, pathid) {

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

            function course_list() {

                var filter = $('#coursefilter').val();
                var category = $('#category').val();
                var program = $('#program').val();

                // Ajax stuff to get list
                // call the web service
                ajax.call([{
                    methodname: 'local_iomad_learningpath_getprospectivecourses',
                    args: {pathid: pathid, filter: filter, category: category, program: program},
                    done: function(courses) {
                        apply_filter(courses);
                    },
                    fail: notification.exception,
                }]);
            }


            $(window).on('load', course_list());
            $('#program').on('change', function() {
                course_list();
                pathcourse_list();
            });
            $('#coursefilter').on('input', function() {
                course_list();
            });
            $('#category').on('change', function() {
                course_list();
            });


            $('#prospectivelist, .pathcourselist').on('mouseenter', 'li', function() {
                $(this).addClass("text-primary");
            });
            $('#prospectivelist, .pathcourselist').on('mouseleave', 'li', function() {
                $(this).removeClass("text-primary");
            });

            function apply_pathcourses(pcl, courses) {

                // Show/hide 'no courses' message.
                $(pcl).find('li').remove();
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

            $(window).on('load', pathcourse_list());

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

            function fix_icons() {
                $("#prospectivelist .path-delete").removeClass('fa-trash path-delete').addClass('fa-plus path-add');
                $(".pathcourselist .path-add").removeClass('fa-plus path-add').addClass('fa-trash path-delete');
            }

            $(".pathcourselist").sortable({
                handle: '.lphandle',
                connectWith: '#prospectivelist, .pathcourselist',
                dropOnEmpty: true,
                update: function() {

                    // Get already selected courseids
                    var courses = [];
                    $(".pathcourselist .pathbox").each(function() {
                        var courseid = $(this).data('courseid');
                        var groupid = $(this).parent().parent().data('groupid');
                        courses.push({courseid: courseid, groupid: groupid});
                    });

                    // Reorder
                    ajax.call([{
                        methodname: 'local_iomad_learningpath_ordercourses',
                        args: {pathid: pathid, courses: courses},
                        done: function() {},
                        fail: notification.exception
                    }]);

                    fix_icons();
                }
            });

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
