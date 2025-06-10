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

 define(['jquery', 'block_lsuxe/xe_lib', 'block_lsuxe/form_events'],
    function($, XELib, XEEvents) {
    'use strict';
    return {
        /**
         * This is the starting function for the Cross Enrollment Tool
         * @param {object} extras is data coming from PHP
         */
        init: function() {
            // Clear the session storage so it won't last outside of the form page.
            sessionStorage.removeItem("currentToken");
            sessionStorage.removeItem("currentUrl");

            // Process any data being sent here.
            XELib.preLoadConfig();

            // Register any click events and other start up processes.
            XEEvents.init();
        }
    };
});