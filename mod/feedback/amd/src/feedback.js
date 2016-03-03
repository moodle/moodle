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
 * AJAX helper for the tag management page.
 *
 * @module     mod_feedback/feedback
 * @package    mod_feedback
 * @copyright  2016 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery'], function($) {
    return /** @alias module:mod_feedback/feedback */ {

        /**
         * Initialises course mapping page.
         *
         * @method initCourseMapping
         */
        initCourseMapping: function (elementid) {
            // Auto submit form on every change to element with id elementid.
            $('body').on('change', elementid, function (e) {
                var form = $(e.target).closest('form');
                $.ajax(form.attr('action'), {
                    type: 'POST',
                    data: form.serialize()
                });
                if (typeof M.core_formchangechecker != 'undefined') {
                    M.core_formchangechecker.set_form_submitted();
                }
            });
        }
    };
});
