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
 * @module   local_syllabusuploader
 * @copyright 2023 onwards Louisiana State University
 * @copyright 2023 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 define([
    'jquery',
    'local_syllabusuploader/notifications',
    'local_syllabusuploader/su_lib',
    'local_syllabusuploader/file_buttons'
], function($, noti, SULib, FB) {
    'use strict';
    return {
        /**
         * Register click events for the system files page. If the template is reloaded
         * for the system files then the events need to called again. This will be called
         * from the template file.
         *
         * @return void
         */
        registerSysFileEvents: function () {
            // --------------------------------
            // Delete NON Moodle File.
            // --------------------------------
            SULib.preLoadConfig();
            if (sessionStorage.getItem('debugging') === "true") {
                console.log(" ********** registerSysFileEvents START ***********");
            }
            $('.local_syllabusuploader_container .nonmood_file_delete').on('click', function(ev) {
                ev.preventDefault();

                var row_data = {
                    "record": $(this).closest("tr").data("rowid"),
                    "title": 'Delete File',
                    "body": 'Are you sure you want to delete this file?',
                    "save_button": "Yes"
                };
                noti.callYesNoModi(row_data).then(function (response) {
                    if (response.status == true) {
                        var this_form = $('#nonmood_file_form');
                        $('.nonmood_file_form_'+response.data.record+' > input').each(function(idx, item) {
                            this_form.append('<input type="hidden" ' +
                                'name="' + $(item).attr("name") + '" ' +
                                'value = "'+ $(item).attr("value") + '" />');
                        });
                        this_form.append('<input type="hidden" name="action" value="delete" />');
                        this_form.submit();
                        // SULib.get_file_list(response).then(function (response) {
                        //     Refresh.refreshGeneralFiles(response);
                        // });
                    }
                });
            });
        },
        /**
         * Register click events for the page.
         *
         * @return void
         */
        registerMooFileEvents: function () {
            // --------------------------------
            // Copy File.
            // --------------------------------
            if (sessionStorage.getItem('debugging') === "true") {
                console.log(" ********** registerMooFileEvents START ***********");
            }

            $('.local_syllabusuploader_container .syllabusuploader_file_copy').on('click', function(ev) {
                ev.preventDefault();

                var row_data = {
                    "record": $(this).closest("tr").data("rowid"),
                    "this_form": $(this).closest("form"),
                    "title": 'Copy File',
                    "body": 'Copy file to location defined in settings?',
                    "save_button": "Copy",
                    "mfileid": $(this).closest("tr").find('input[name=mdl_file_id]').val()
                };
                if (sessionStorage.getItem('debugging') === "true") {
                    console.log("Debugging: Data being sent to confirm check is: ", row_data);
                }
                FB.confirmCheckExecute(row_data);
            });

            // --------------------------------
            // Delete Moodle File.
            // --------------------------------
            $('body .local_syllabusuploader_container .syllabusuploader_file_delete').on('click', function(ev) {
                ev.preventDefault();

                var row_data = {
                    "record": $(this).closest("tr").data("rowid"),
                    "title": 'Delete File',
                    "body": 'Are you sure you want to delete this file?',
                    "save_button": "Yes"
                };
                noti.callYesNoModi(row_data).then(function (response) {
                    if (response.status == true) {
                        var this_form = $('#syllabusuploader_file_form_' + response.data.record);
                        // Convert all the form elements values to a serialised string.
                        this_form.append('<input type="hidden" name="action" value="delete" />');
                        this_form.submit();
                    }
                });
            });

            //Bind keypress event to document
            $(document).keypress(function(event){
                var keycode = (event.keyCode ? event.keyCode : event.which);
                if(keycode == '13'){
                    if ($('.justdoitnow').length) {
                        $('.justdoitnow').parent().next().find('.btn.btn-primary').click();
                    }
                }
            });
        },

        /**
         * Currently this is being called from the mustache templates when viewing lists.
         * @param null
         * @return void
         */
        // init: function() {
        //     var that = this;
        //     // Register events.
        //     that.registerMooFileEvents();
        // },
    };
});
