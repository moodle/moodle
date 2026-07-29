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
    let focusedAlready = false;
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

                var parent = $(element).closest('.fitem');
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

                if (element.tagName === 'TEXTAREA') {
                    // Check if the textarea is backed by a contenteditable div.
                    const contentEditable = parent.find('[contenteditable]');
                    if (contentEditable.length > 0) {
                        // Use the contenteditable div as the target element.
                        element = contentEditable[0];
                    } else {
                        // Use the TinyMCE iframe as the target element if it exists.
                        element = document.getElementById(`${element.id}_ifr`) || element;
                    }
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
                    feedback.html(msg);
                    feedback.show();

                    // If we haven't focused anything yet, focus this one.
                    if (!focusedAlready) {
                        element.scrollIntoView({behavior: "smooth", block: "center"});
                        focusedAlready = true;
                        setTimeout(()=> {
                            // Actual focus happens later in case we need to do this in response to
                            // a change event which happens in the middle of changing focus.
                            element.focus({preventScroll: true});
                            // Let it focus again next time they submit the form.
                            focusedAlready = false;
                        }, 0);
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
