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
define(['jquery', 'core_form/events'], function($, FormEvent) {
    return {
        /**
         * Enhance the supplied element to handle form field errors.
         *
         * @method
         * @param {String} elementid
         * @listens event:formFieldValidationFailed
         */
        enhance: function(elementid) {
            var element = document.getElementById(elementid);
            if (!element) {
                // Some elements (e.g. static) don't have a form field.
                // Hence there is no validation. So, no setup required here.
                return;
            }

            element.addEventListener(FormEvent.eventTypes.formFieldValidationFailed, e => {
                const msg = e.detail.message;
                e.preventDefault();

                var parent = $(element).closest('.form-group');
                var feedback = parent.find('.form-control-feedback');
                const feedbackId = feedback.attr('id');

                // Get current aria-describedby value.
                let describedBy = $(element).attr('aria-describedby');
                if (typeof describedBy === "undefined") {
                    describedBy = '';
                }
                // Split aria-describedby attribute into an array of IDs if necessary.
                let describedByIds = [];
                if (describedBy.length) {
                    describedByIds = describedBy.split(" ");
                }
                // Find the the feedback container in the aria-describedby attribute.
                const feedbackIndex = describedByIds.indexOf(feedbackId);

                // Sometimes (atto) we have a hidden textarea backed by a real contenteditable div.
                if (($(element).prop("tagName") == 'TEXTAREA') && parent.find('[contenteditable]')) {
                    element = parent.find('[contenteditable]');
                }
                if (msg !== '') {
                    parent.addClass('has-danger');
                    parent.data('client-validation-error', true);
                    $(element).addClass('is-invalid');
                    // Append the feedback ID to the aria-describedby attribute if it doesn't exist yet.
                    if (feedbackIndex === -1) {
                        describedByIds.push(feedbackId);
                        $(element).attr('aria-describedby', describedByIds.join(" "));
                    }
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
                        // If the aria-describedby attribute contains the error container's ID, remove it.
                        if (feedbackIndex > -1) {
                            describedByIds.splice(feedbackIndex, 1);
                        }
                        // Check the remaining element IDs in the aria-describedby attribute.
                        if (describedByIds.length) {
                            // If there's at least one, combine them with a blank space and update the aria-describedby attribute.
                            describedBy = describedByIds.join(" ");
                            // Put back the new describedby attribute.
                            $(element).attr('aria-describedby', describedBy);
                        } else {
                            // If there's none, remove the aria-describedby attribute.
                            $(element).removeAttr('aria-describedby');
                        }
                        $(element).attr('aria-invalid', false);
                        feedback.hide();
                    }
                }
            });

            var form = element.closest('form');
            if (form && !('boostFormErrorsEnhanced' in form.dataset)) {
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
