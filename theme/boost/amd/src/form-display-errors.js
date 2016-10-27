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
 * Custom form error event handler to manipulate the bootstrap markup and show
 * nicely styled errors in an mform.
 *
 * @module     theme_boost/form-display-errors
 * @copyright  2016 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/event'], function($, Event) {
    return {
        enhance: function(elementid) {
            var element = document.getElementById(elementid);
            $(element).on(Event.Events.FORM_FIELD_VALIDATION, function(event, msg) {
                event.preventDefault();
                var parent = $(element).closest('.form-group');
                var feedback = parent.find('.form-control-feedback');
                if (msg !== '') {
                    parent.addClass('has-danger');
                    parent.data('client-validation-error', true);
                    $(element).addClass('form-control-danger');
                    $(element).attr('aria-describedby', $(element).attr('id') + '-feedback');
                    feedback.html(msg);
                    feedback.show();
                } else {
                    if (parent.data('client-validation-error') === true) {
                        parent.removeClass('has-danger');
                        parent.data('client-validation-error', false);
                        $(element).removeClass('form-control-danger');
                        $(element).attr('aria-describedby', '');
                        feedback.hide();
                    }
                }
            });
        }
    };
});
