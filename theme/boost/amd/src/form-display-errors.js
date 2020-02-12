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
            if (!element) {
                // Some elements (e.g. static) don't have a form field.
                // Hence there is no validation. So, no setup required here.
                return;
            }

            $(element).on(Event.Events.FORM_FIELD_VALIDATION, function(event, msg) {
                event.preventDefault();
                var parent = $(element).closest('.form-group');
                var feedback = parent.find('.form-control-feedback');

                // Sometimes (atto) we have a hidden textarea backed by a real contenteditable div.
                if (($(element).prop("tagName") == 'TEXTAREA') && parent.find('[contenteditable]')) {
                    element = parent.find('[contenteditable]');
                }
                if (msg !== '') {
                    parent.addClass('has-danger');
                    parent.data('client-validation-error', true);
                    $(element).addClass('is-invalid');
                    $(element).attr('aria-describedby', feedback.attr('id'));
                    $(element).attr('aria-invalid', true);
                    feedback.attr('tabindex', 0);
                    feedback.html(msg);

                    // Only display and focus when the error was not already visible.
                    // This is so that, when tabbing around the form, you don't get stuck.
                    if (!feedback.is(':visible')) {
                        feedback.show();
                        feedback.focus();
                    }

                } else {
                    if (parent.data('client-validation-error') === true) {
                        parent.removeClass('has-danger');
                        parent.data('client-validation-error', false);
                        $(element).removeClass('is-invalid');
                        $(element).removeAttr('aria-describedby');
                        $(element).attr('aria-invalid', false);
                        feedback.hide();
                    }
                }
            });

            var form = element.closest('form');
            if (!('boostFormErrorsEnhanced' in form.dataset)) {
                form.addEventListener('submit', function() {
                    var visibleError = $('.form-control-feedback:visible');
                    if (visibleError.length) {
                        visibleError[0].focus();
                    }
                });
                form.dataset.boostFormErrorsEnhanced = 1;
            }
        }
    };
});
