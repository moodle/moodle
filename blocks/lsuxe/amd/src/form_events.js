// This file is part of Moodle - http://moodle.org/
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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 // define(['jquery', 'block_lsuxe/xe_lib', 'block_lsuxe/notifications', 'block_lsuxe/verify'],
 define(['jquery', 'block_lsuxe/xe_lib', 'block_lsuxe/notifications'],
    // function($, XELib, Noti, Veri) {
    function($, XELib, Noti) {
    'use strict';
    return {
        /**
         * Fetch the token for the current selected URL. Store in temp sessionStorage
         *
         * @param null
         * @return void
         */
        getTokenReady: function () {
            // Check to see if this is the first time landing or not.
            var url = $('#id_available_moodle_instances option:selected').text();
            XELib.getTokenForURL(url).then(function (response) {
                if (response.success == true) {
                    sessionStorage.setItem("currentToken", response.data);
                    sessionStorage.setItem("currentUrl", url);
                } else {
                    console.log("ERROR: Failed to get the token :-( ");
                    // TODO: Send Notification to user that token is crap
                }
            });
        },

        /**
         * Get group data from a course.
         * @param {object} the json object sent to the server
         * @return resolved data
         */
        getGroupData: function (params) {
            return XELib.jaxyPromise({
                'call': 'get_group_data',
                'params': params,
                'class': 'router'
            });
        },

        /**
         * Moodle removes any changed option/select elements. In order to maintain
         * the data push data into hidden some that are in the form's page.
         *
         * @param {string} tag name of the tag to be changed
         * @param {string} value the value to insert
         * @return {void}
         */
        setHiddenValue: function (tag, value) {
            $('input[name='+tag+']').val(value);
        },

        /**
         * Verify the source course and group.
         *
         * @param {object} params the json object sent to the server
         * @return {Object} resolved data
         */
        verifySourceCourse: function (params) {
            return XELib.jaxyPromise({
                'call': 'verify_course',
                'params': params,
                'class': 'router'
            });
        },

        /**
         * Verify the destination course.
         *
         * @param {object} params the json object sent to the server
         * @return {Object} resolved data
         */
        verifyDestCourse: function (params) {
            var new_params = {
                'type': 'GET',
                'url': sessionStorage.getItem("currentUrl") + '/webservice/rest/server.php',
                'data': {
                    'wstoken': sessionStorage.getItem("currentToken"),
                    'wsfunction': 'core_course_get_courses_by_field',
                    'moodlewsrestformat': 'json',
                    'field': 'shortname',
                    'value': params.coursename
                }
            };
            return XELib.jaxyRemotePromise(new_params);
        },

        /**
         * Reset the dropdown for the source group.
         *
         * @return null
         */
        resetGroupList: function () {
            $('#id_srccoursegroupnameselect').empty();
            $('#id_srccoursegroupnametext').text();
            $('#id_srccoursegroupnameselect')
                .append($("<option></option>")
                .attr("value", 0)
                .text("Please search for a course first"));
        },

        /**
         * Register all the events for the Mappings Form Page.
         * @return {void}
         */
        registerMoodleEvents: function() {
            // This is for the Moodles Form
            $('#id_verifysource').on('click', function() {
                var test_url = $("#id_instanceurl").val(),
                    test_token = $("#id_instancetoken").val();

                var params = {
                    'type': 'GET',
                    // 'type': 'POST',
                    // 'url': test_url + '/admin/webservice/testclient.php',
                    'url': test_url + '/webservice/rest/server.php',
                    'data': {
                        'wstoken': test_token,
                        'wsfunction': 'block_lsuxe_xeajax',
                        'moodlewsrestformat': 'json',
                        // 'data': {
                        'datachunk': JSON.stringify({
                            'call': 'test_service',
                            'params': {
                                'test': 'test',
                            },
                            'class': 'router'
                        })
                    }
                };

                XELib.testWebServices(params).then(function (response) {
                    if (response.success == false) {
                        Noti.callNoti({
                            message: response.msg,
                            type: 'error'
                        });
                    } else {
                        Noti.callNoti({
                            message: "Successfully said hello to the remote Moodle instance .",
                            type: 'success'
                        });
                    }

                });
            });
        },

        /**
         * Register all the events for the Mappings Form Page.
         * @return void
         */
        registerMappingEvents: function() {
            var that = this,
                src_form_select = $("#id_srccourseshortname");

            src_form_select.change(function() {
                if (src_form_select.val()) {
                    // change invokes any change so only make an ajax call if there is value
                    that.getGroupData({
                        'courseid': src_form_select.val(),
                        'coursename': $( "#id_srccourseshortname option:selected" ).text()
                    },).then(function (response) {
                        // if the text is disabled then use select
                        if (response.count == 0) {
                            // There are no groups, reset.
                            that.resetGroupList();

                        } else {
                            // Multiple groups, so let's unhide the select
                            $('#id_srccoursegroupnameselect').empty();
                            var first_choice = "";
                            for (let i in response.data) {
                                // This is to store the first select and to be used.
                                if (first_choice == "") {
                                    first_choice = {
                                        groupid: response.data[i].groupid,
                                        groupname: response.data[i].groupname
                                    };
                                }
                                $('#id_srccoursegroupnameselect')
                                    .append($("<option></option>")
                                    .attr("value", response.data[i].groupid)
                                    .text(response.data[i].groupname));
                            }

                            // Now that it's been populated, set the hidden elements to match the first
                            // select option.
                            that.setHiddenValue('srccoursegroupname', first_choice.groupname);
                            that.setHiddenValue('srccoursegroupid', first_choice.groupid);
                        }

                    });
                } else {
                    // if there is no value in the course name then clear out the group name.
                    that.resetGroupList();
                }
            });

            // Any changes to the group element, update the hidden.
            $("#id_srccoursegroupnameselect").change(function() {
                var new_value = $(this).find("option:selected").attr('value'),
                    new_text = $(this).find("option:selected").text();
                that.setHiddenValue('srccoursegroupname', new_text);
                that.setHiddenValue('srccoursegroupid', new_value);
            });

            // Verify the Course and Group Names.
            $('#id_verifysource').on('click', function() {
                var coursename = "",
                    groupname = "";

                // If we are using the autocomplete feature then the jquery
                // selector is different.
                if (sessionStorage.getItem("xes_autocomplete") == "1") {
                    coursename = $("#id_srccourseshortname").find("option:selected").text();
                    groupname = $("#id_srccoursegroupnameselect").find("option:selected").text();
                } else {
                    coursename = $("#id_srccourseshortname").val();
                    groupname = $("#id_srccoursegroupname").val();
                }

                if (coursename.length < 1) {
                    // User forgot to enter a course name.
                    Noti.callNoti({
                        message: "Ooops, you forgot to enter a course short name",
                        type: 'error'
                    });
                    return;
                }

                if (groupname.length < 1) {
                    // User forgot to enter a course name.
                    Noti.callNoti({
                        message: "Ooops, you forgot to enter a group name",
                        type: 'error'
                    });
                    return;
                }
                that.verifySourceCourse({
                    'coursename': coursename,
                    'groupname': groupname
                }).then( function (response) {
                    if (response.success == false) {
                        Noti.callNoti({
                            message: response.msg,
                            type: 'error'
                        });
                    } else {
                        // Populate the hidden fields since we are here.
                        that.setHiddenValue('srccourseid', response.data.id);
                        that.setHiddenValue('srccoursegroupid', response.data.groupid);
                        Noti.callNoti({
                            message: "Everything checks out for the source course and group.",
                            type: 'success'
                        });
                    }
                });
            });

            $('#id_verifydest').on('click', function() {
                var destname = $("#id_destcourseshortname").val();

                that.verifyDestCourse({
                    'coursename': destname
                }).then( function (response){
                    if (("courses" in response)) {
                        // how many courses were retrieved
                        if (response.courses.length == 1) {
                            that.setHiddenValue('destcourseid', response.courses[0].id);

                            Noti.callNoti({
                                message: "Destination course is there and waiting for you.",
                                type: 'success'
                            });
                        } else {
                            Noti.callNoti({
                                message: "There seems to be more than one course with that shortname.",
                                type: 'warn'
                            });
                        }
                    } else {
                        // FALSE
                        Noti.callNoti({
                            message: "The course: " + destname + " was not found on the destination server.",
                            type: 'error'
                        });
                    }
                });
            });

            // Register events on the moodles form,
            // onChange event for the URL selector
            $('select#id_available_moodle_instances').on('change', function() {
                that.getTokenReady();

                // Clear out the Destination Course Shorname
                $('div.xe_dest_course_auto ul.form-autocomplete-suggestions').empty();
                $('div.xe_dest_course_auto input').val('');
                $('span.notifications').empty();
            });
        },

        /**
         * These are registered events being loaded that are NOT being called from mustache templates
         * @param null
         * @return resolved data
         */
        registerEvents: function () {

            // Let's not load all events, just what we need.
            if (sessionStorage.getItem('xe_form') == "mappings" && sessionStorage.getItem('xe_viewform') == "true") {
                // Register events on the mappings form.
                this.registerMappingEvents();

            } else if (sessionStorage.getItem('xe_form') == "moodles" && sessionStorage.getItem('xe_viewform') == "true") {
                // Register events on the mappings form.
                this.registerMoodleEvents();
                // Veri.registerCheckMarkTags()
                // TODO: move from template to here.
            }
        },

        /**
         * The entry point for the mappings and moodles form.
         * Register the events and load the token.
         *
         * @return null
         */
        init: function () {
            this.registerEvents();
            this.getTokenReady();
        }
    };
});