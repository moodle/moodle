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
 * Enhancements to all components for easy course accessibility.
 *
 * @module     format/remuiformat
 * @copyright  WisdmLabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define(['jquery'], function($) {
    /**
     * Init method
     *
     */
    function init() {
        // eslint-disable-next-line no-console
        $(document).ready(function() {
            var headercontent = $(".rmuiformate-header-wrapper").html();
            $('#page #page-header').replaceWith(headercontent);
            // $('.rmuiformate-header-wrapper').removeClass("d-none");
        });
    }
    // Must return the init function.
    return {
        init: init
    };
});
