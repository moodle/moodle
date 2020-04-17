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
 * AMD module for the user enrolment status field in the course participants page.
 *
 * @module     core_user/status_field
 * @copyright  2017 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import Fragment from 'core/fragment';
import ModalEvents from 'core/modal_events';
import ModalFactory from 'core/modal_factory';
import Notification from 'core/notification';
import * as Str from 'core/str';
import Templates from 'core/templates';
import jQuery from 'jquery';

const Selectors = {
    editEnrolment: '[data-action="editenrolment"]',
    showDetails: '[data-action="showdetails"]',
    unenrol: '[data-action="unenrol"]'
};

class StatusFieldActions {
    /**
     * Constructor
     *
     * @param {Object} options Object containing options. The only valid option at this time is contextid.
     * Each call to templates.render gets it's own instance of this class.
     */
    constructor(options) {
        this.contextid = options.contextid;
        this.courseid = options.courseid;

        // Bind click event to editenrol buttons.
        this.bindEditEnrol();

        // Bind click event to unenrol buttons.
        this.bindUnenrol();

        // Bind click event to status details buttons.
        this.bindStatusDetails();
    }

    bindEditEnrol() {
        var statusFieldInstsance = this;

        jQuery(Selectors.editEnrolment).click(function(e) {
            e.preventDefault();

            // The particular edit button that was clicked.
            var clickedEditTrigger = jQuery(this);
            // Get the parent container (it contains the data attributes associated with the status field).
            var parentContainer = clickedEditTrigger.parent();
            // Get the name of the user whose enrolment status is being edited.
            var fullname = parentContainer.data('fullname');
            // Get the user enrolment ID.
            var ueid = clickedEditTrigger.attr('rel');

            jQuery.when(Str.get_string('edituserenrolment', 'enrol', fullname)).then(function(modalTitle) {
                return ModalFactory.create({
                    large: true,
                    title: modalTitle,
                    type: ModalFactory.types.SAVE_CANCEL
                });
            }).done(function(modal) {
                // Handle save event.
                modal.getRoot().on(ModalEvents.save, function(e) {
                    // Don't close the modal yet.
                    e.preventDefault();
                    // Submit form data.
                    statusFieldInstsance.submitEditFormAjax(modal);
                });

                // Handle hidden event.
                modal.getRoot().on(ModalEvents.hidden, function() {
                    // Destroy when hidden.
                    modal.destroy();
                });

                // Set the modal body.
                modal.setBody(statusFieldInstsance.getBody(ueid));

                // Show the modal!
                modal.show();
            }).fail(Notification.exception);
        });
    }

    bindUnenrol() {
        var statusFieldInstsance = this;

        jQuery(Selectors.unenrol).click(function(e) {
            e.preventDefault();
            var unenrolLink = jQuery(this);
            var parentContainer = unenrolLink.parent();
            var strings = [
                {
                    key: 'unenrol',
                    component: 'enrol'
                },
                {
                    key: 'unenrolconfirm',
                    component: 'enrol',
                    param: {
                        user: parentContainer.data('fullname'),
                        course: parentContainer.data('coursename'),
                        enrolinstancename: parentContainer.data('enrolinstancename')
                    }
                }
            ];

            var deleteModalPromise = ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL
            });

            jQuery.when(Str.get_strings(strings), deleteModalPromise).done(function(results, modal) {
                var title = results[0];
                var confirmMessage = results[1];
                modal.setTitle(title);
                modal.setBody(confirmMessage);
                modal.setSaveButtonText(title);

                // Handle confirm event.
                modal.getRoot().on(ModalEvents.save, function() {
                    // Build params.
                    var unenrolParams = {
                        'ueid': jQuery(unenrolLink).attr('rel')
                    };
                    // Don't close the modal yet.
                    e.preventDefault();
                    // Submit data.
                    statusFieldInstsance.submitUnenrolFormAjax(modal, unenrolParams);
                });

                // Handle hidden event.
                modal.getRoot().on(ModalEvents.hidden, function() {
                    // Destroy when hidden.
                    modal.destroy();
                });

                // Display the delete confirmation modal.
                modal.show();
            }).fail(Notification.exception);
        });
    }

    bindStatusDetails() {
        jQuery(Selectors.showDetails).click(function(e) {
            e.preventDefault();

            var detailsButton = jQuery(this);
            var parentContainer = detailsButton.parent();
            var context = {
                "fullname": parentContainer.data('fullname'),
                "coursename": parentContainer.data('coursename'),
                "enrolinstancename": parentContainer.data('enrolinstancename'),
                "status": parentContainer.data('status'),
                "statusclass": parentContainer.find('span').attr('class'),
                "timestart": parentContainer.data('timestart'),
                "timeend": parentContainer.data('timeend'),
                "timeenrolled": parentContainer.data('timeenrolled')
            };

            // Get default string for the modal and modal type.
            var strings = [
                {
                    key: 'enroldetails',
                    component: 'enrol'
                }
            ];

            // Find the edit enrolment link.
            var editEnrolLink = detailsButton.next(Selectors.editEnrolment);
            if (editEnrolLink.length) {
                // If there's an edit enrolment link for this user, clone it into the context for the modal.
                context.editenrollink = jQuery('<div>').append(editEnrolLink.clone()).html();
            }

            var modalStringsPromise = Str.get_strings(strings);
            var modalPromise = ModalFactory.create({large: true, type: ModalFactory.types.CANCEL});
            jQuery.when(modalStringsPromise, modalPromise).done(function(strings, modal) {
                var modalBodyPromise = Templates.render('core_user/status_details', context);
                modal.setTitle(strings[0]);
                modal.setBody(modalBodyPromise);

                if (editEnrolLink.length) {
                    modal.getRoot().on('click', Selectors.editEnrolment, function(e) {
                        e.preventDefault();
                        modal.hide();
                        // Trigger click event for the edit enrolment link to show the edit enrolment modal.
                        jQuery(editEnrolLink).trigger('click');
                    });
                }

                modal.show();

                // Handle hidden event.
                modal.getRoot().on(ModalEvents.hidden, function() {
                    // Destroy when hidden.
                    modal.destroy();
                });
            }).fail(Notification.exception);
        });
    }

    submitEditFormAjax(modal) {
        var statusFieldInstsance = this;
        var form = modal.getRoot().find('form');

        // User enrolment ID.
        var ueid = jQuery(form).find('[name="ue"]').val();

        var request = {
            methodname: 'core_enrol_submit_user_enrolment_form',
            args: {
                formdata: form.serialize()
            }
        };

        Ajax.call([request])[0].done(function(data) {
            if (data.result) {
                // Dismiss the modal.
                modal.hide();

                // Reload the page, don't show changed data warnings.
                if (typeof window.M.core_formchangechecker !== "undefined") {
                    window.M.core_formchangechecker.reset_form_dirty_state();
                }
                window.location.reload();
            } else {
                // Serialise the form data and reload the form fragment to show validation errors.
                var formData = JSON.stringify(form.serialize());
                modal.setBody(statusFieldInstsance.getBody(ueid, formData));
            }
        }).fail(Notification.exception);
    }

    submitUnenrolFormAjax(modal, unenrolParams) {
        var request = {
            methodname: 'core_enrol_unenrol_user_enrolment',
            args: unenrolParams
        };

        Ajax.call([request])[0].done(function(data) {
            if (data.result) {
                // Dismiss the modal.
                modal.hide();

                // Reload the page, don't show changed data warnings.
                if (typeof window.M.core_formchangechecker !== "undefined") {
                    window.M.core_formchangechecker.reset_form_dirty_state();
                }
                window.location.reload();
            } else {
                // Display an alert containing the error message
                Notification.alert(data.errors[0].key, data.errors[0].message);
            }
        }).fail(Notification.exception);
    }

    getBody(ueid, formData) {
        var params = {
            'ueid': ueid
        };
        if (typeof formData !== 'undefined') {
            params.formdata = formData;
        }
        return Fragment.loadFragment('enrol', 'user_enrolment_form', this.contextid, params).fail(Notification.exception);
    }
}

export const init = config => {
    new StatusFieldActions(config);
};
