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
 * @package   block_pu
 * @copyright 2021 onwards Louisiana State University
 * @copyright 2021 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 // define(['jquery', 'block_pu/notifications', 'block_pu/pu_lib', 'block_pu/file_buttons', 'block_pu/refresh'],
 define(['jquery', 'block_pu/notifications', 'block_pu/pu_lib', 'block_pu/file_buttons'],
    function($, noti, PULib, FB) {
    // function($, noti, PULib, FB, Refresh) {
    'use strict';
    return {
        /**
         * Register click events for the system files page. If the template is reloaded
         * for the system files then the events need to called again. This will be called
         * from the template file.
         *
         * @param null
         * @return void
         */
        registerSysFileEvents: function () {
            // --------------------------------
            // Delete NON Moodle File.
            // --------------------------------
            $('.block_pu_container .nonmood_file_delete').on('click', function(ev) {
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
                        // PULib.get_file_list(response).then(function (response) {
                        //     Refresh.refreshGeneralFiles(response);
                        // });
                    }
                });
            });
        },
        /**
         * Register click events for the page.
         *
         * @param null
         * @return void
         */
        registerMooFileEvents: function () {
            // --------------------------------
            // Copy File.
            // --------------------------------
            $('.block_pu_container .pu_file_copy').on('click', function(ev) {
                ev.preventDefault();

                var row_data = {
                    "record": $(this).closest("tr").data("rowid"),
                    "this_form": $(this).closest("form"),
                    "title": 'Copy File',
                    "body": 'Copy and move file to location in settings?',
                    "save_button": "Copy",
                    "mfileid": $(this).closest("tr").find('input[name=mdl_file_id]').val()
                };

                FB.confirmCheckExecute(row_data);
            });

            // --------------------------------
            // Delete Moodle File.
            // --------------------------------
            $('body .block_pu_container .pu_file_delete').on('click', function(ev) {
                ev.preventDefault();

                var row_data = {
                    "record": $(this).closest("tr").data("rowid"),
                    "title": 'Delete File',
                    "body": 'Are you sure you want to delete this file?',
                    "save_button": "Yes"
                };
                noti.callYesNoModi(row_data).then(function (response) {
                    if (response.status == true) {
                        var this_form = $('#pu_file_form_' + response.data.record);
                        // Convert all the form elements values to a serialised string.
                        this_form.append('<input type="hidden" name="action" value="delete" />');
                        this_form.submit();
                    }
                });
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