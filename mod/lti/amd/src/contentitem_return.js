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
 * Processes the result of LTI tool creation from a Content-Item message type.
 *
 * @module     mod_lti/contentitem_return
 * @class      contentitem_return
 * @package    mod_lti
 * @copyright  2016 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */
define(['jquery'], function($) {
    return {
        /**
         * Init function.
         *
         * @param {string} returnData The returned data.
         */
        init: function(returnData) {
            // Make sure the window has loaded before we perform processing.
            $(window).ready(function() {
                if (window != top) {
                    // Send return data to be processed by the parent window.
                    parent.processContentItemReturnData(returnData);
                }
            });
        }
    };
});
