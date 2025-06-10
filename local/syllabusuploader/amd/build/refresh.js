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
 * @package   local_syllabusuploader
 * @copyright 2023 onwards Louisiana State University
 * @copyright 2023 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 define(['core/notification', 'core/templates'],
    function(DE, Templates) {
    'use strict';
    return {
        /**
         * From the file_button file get the list of files from the folder that's indicated
         * in the settings page. Once the list is obtained reload the template.
         * @param {object} the json object sent to the server
         * @return resolved data
         */
        refreshGeneralFiles: function (params) {
            Templates.renderForPromise('local_syllabusuploader/non_mood_files', params)
                // It returns a promise that needs to be resoved.
            .then(({html, js}) => {
                Templates.replaceNodeContents('.non_mood_files_refresher', html, js);
            })
            .catch(ex => DE(ex));
        },
    };
});
