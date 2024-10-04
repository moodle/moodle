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
 * This is an empty module, that is required before all other modules.
 * Because every module is returned from a request for any other module, this
 * forces the loading of all modules with a single request.
 *
 * This function also sets up the listeners for ajax requests so we can tell
 * if any requests are still in progress.
 *
 * @module     core/first
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */

import $ from 'jquery';

$(document)
.bind("ajaxStart", function() {
    M.util.js_pending('jq');
})
.bind("ajaxStop", function() {
    M.util.js_complete('jq');
});

// TODO: MDL-84465 Final deprecation in 6.0.
// Attach jQuery to the window object for Bootstrap backwards compatibility.
// Bootstrap 5 is designed to be used without jQuery, but it’s still possible to use our components with jQuery.
// If Bootstrap detects jQuery in the window object it’ll add all of our components in jQuery’s plugin system.
window.jQuery = $;
