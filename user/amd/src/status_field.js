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
define(['core/templates',
        'jquery',
        'core/str',
        'core/config',
        'core/notification',
        'core/modal_factory',
        'core/modal_events',
        'core/fragment',
        'core/ajax'
    ],
    function(Template, $, Str, Config, Notification, ModalFactory, ModalEvents, Fragment, Ajax) {

        /**
         * Action selectors.
         *
         * @access private
         * @type {{EDIT_ENROLMENT: string, SHOW_DETAILS: string, UNENROL: string}}
         */
        var SELECTORS = {
            EDIT_ENROLMENT: '[data-action="editenrolment"]',
            SHOW_DETAILS: '[data-action="showdetails"]',
            UNENROL: '[data-action="unenrol"]'
        };

        /**
         * Constructor
         *
         * @param {Object} options Object containing options. The only valid option at this time is contextid.
         * Each call to templates.render gets it's own instance of this class.
         */
        var StatusFieldActions = function(options) {
            this.contextid = options.contextid;
            this.courseid = options.courseid;

            // Bind click event to editenrol buttons.
            this.bindEditEnrol();

            // Bind click event to unenrol buttons.
            this.bindUnenrol();

            // Bind click event to status details buttons.
            this.bindStatusDetails();
        };
        // Class variables and functions.

        /** @var {number} courseid The course ID. */
        StatusFieldActions.prototype.courseid = 0;

        /**
         * Private method
         *
         * @method initModal
         * @private
         */
        StatusFieldActions.prototype.bindEditEnrol = function() {
            var statusFieldInstsance = this;

            $(SELECTORS.EDIT_ENROLMENT).click(function(e) {
                e.preventDefault();

                // The particular edit button that was clicked.
                var clickedEditTrigger = $(this);
                // Get the parent container (it contains the data attributes associated with the status field).
                var parentContainer = clickedEditTrigger.parent();
                // Get the name of the user whose enrolment status is being edited.
                var fullname = parentContainer.data('fullname');
                // Get the user enrolment ID.
                var ueid = clickedEditTrigger.attr('rel');

                $.when(Str.get_string('edituserenrolment', 'enrol', fullname)).then(function(modalTitle) {
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
        };

        /**
         * Private method
         *
         * @method bindUnenrol
         * @private
         */
        StatusFieldActions.prototype.bindUnenrol = function() {
            var statusFieldInstsance = this;

            $(SELECTORS.UNENROL).click(function(e) {
                e.preventDefault();
                var unenrolLink = $(this);
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

                $.when(Str.get_strings(strings), deleteModalPromise).done(function(results, modal) {
                    var title = results[0];
                    var confirmMessage = results[1];
                    modal.setTitle(title);
                    modal.setBody(confirmMessage);
                    modal.setSaveButtonText(title);

                    // Handle confirm event.
                    modal.getRoot().on(ModalEvents.save, function() {
                        // Build params.
                        var unenrolParams = {
                            'ueid': $(unenrolLink).attr('rel')
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
        };

        /**
         * Private method
         *
         * @method bindStatusDetails
         * @private
         */
        StatusFieldActions.prototype.bindStatusDetails = function() {
            $(SELECTORS.SHOW_DETAILS).click(function(e) {
                e.preventDefault();

                var detailsButton = $(this);
                var parentContainer = detailsButton.parent();
                var context = {
                    "fullname": parentContainer.data('fullname'),
                    "coursename": parentContainer.data('coursename'),
                    "enrolinstancename": parentContainer.data('enrolinstancename'),
                    "status": parentContainer.data('status'),
                    "statusclass": parentContainer.find('span').attr('class'),
                    "timestart": parentContainer.data('timestart'),
                    "timeend": parentContainer.data('timeend')
                };

                // Get default string for the modal and modal type.
                var strings = [
                    {
                        key: 'enroldetails',
                        component: 'enrol'
                    }
                ];

                // Find the edit enrolment link.
                var editEnrolLink = detailsButton.next(SELECTORS.EDIT_ENROLMENT);
                if (editEnrolLink.length) {
                    // If there's an edit enrolment link for this user, clone it into the context for the modal.
                    context.editenrollink = $('<div>').append(editEnrolLink.clone()).html();
                }

                var modalStringsPromise = Str.get_strings(strings);
                var modalPromise = ModalFactory.create({large: true, type: ModalFactory.types.CANCEL});
                $.when(modalStringsPromise, modalPromise).done(function(strings, modal) {
                    var modalBodyPromise = Template.render('core_user/status_details', context);
                    modal.setTitle(strings[0]);
                    modal.setBody(modalBodyPromise);

                    if (editEnrolLink.length) {
                        modal.getRoot().on('click', SELECTORS.EDIT_ENROLMENT, function(e) {
                            e.preventDefault();
                            modal.hide();
                            // Trigger click event for the edit enrolment link to show the edit enrolment modal.
                            $(editEnrolLink).trigger('click');
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
        };

        /**
         * Private method
         *
         * @method submitEditFormAjax
         * @param {Object} modal The the AMD modal object containing the form.
         * @private
         */
        StatusFieldActions.prototype.submitEditFormAjax = function(modal) {
            var statusFieldInstsance = this;
            var form = modal.getRoot().find('form');

            // User enrolment ID.
            var ueid = $(form).find('[name="ue"]').val();
            // Status.
            var status = $(form).find('[name="status"]').val();

            var params = {
                'courseid': this.courseid,
                'ueid': ueid,
                'status': status
            };

            // Enrol time start.
            var timeStartEnabled = $(form).find('[name="timestart[enabled]"]');
            if (timeStartEnabled.is(':checked')) {
                var timeStartYear = $(form).find('[name="timestart[year]"]').val();
                var timeStartMonth = $(form).find('[name="timestart[month]"]').val() - 1;
                var timeStartDay = $(form).find('[name="timestart[day]"]').val();
                var timeStartHour = $(form).find('[name="timestart[hour]"]').val();
                var timeStartMinute = $(form).find('[name="timestart[minute]"]').val();
                var timeStart = new Date(timeStartYear, timeStartMonth, timeStartDay, timeStartHour, timeStartMinute);
                params.timestart = timeStart.getTime() / 1000;
            }

            // Enrol time end.
            var timeEndEnabled = $(form).find('[name="timeend[enabled]"]');
            if (timeEndEnabled.is(':checked')) {
                var timeEndYear = $(form).find('[name="timeend[year]"]').val();
                var timeEndMonth = $(form).find('[name="timeend[month]"]').val() - 1;
                var timeEndDay = $(form).find('[name="timeend[day]"]').val();
                var timeEndHour = $(form).find('[name="timeend[hour]"]').val();
                var timeEndMinute = $(form).find('[name="timeend[minute]"]').val();
                var timeEnd = new Date(timeEndYear, timeEndMonth, timeEndDay, timeEndHour, timeEndMinute);
                params.timeend = timeEnd.getTime() / 1000;
            }

            var request = {
                methodname: 'core_enrol_edit_user_enrolment',
                args: params
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
        };

         /**
         * Private method
         *
         * @method submitUnenrolFormAjax
         * @param {Object} modal The the AMD modal object containing the form.
         * @param {Object} unenrolParams The unenrol parameters.
         * @private
         */
        StatusFieldActions.prototype.submitUnenrolFormAjax = function(modal, unenrolParams) {
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
        };

        /**
         * Private method
         *
         * @method getBody
         * @private
         * @param {Number} ueid The user enrolment ID associated with the user.
         * @param {string} formData Serialized string of the edit enrolment form data.
         * @return {Promise}
         */
        StatusFieldActions.prototype.getBody = function(ueid, formData) {
            var params = {
                'ueid': ueid
            };
            if (typeof formData !== 'undefined') {
                params.formdata = formData;
            }
            return Fragment.loadFragment('enrol', 'user_enrolment_form', this.contextid, params).fail(Notification.exception);
        };

        return /** @alias module:core_user/editenrolment */ {
            // Public variables and functions.
            /**
             * Every call to init creates a new instance of the class with it's own event listeners etc.
             *
             * @method init
             * @public
             * @param {object} config - config variables for the module.
             */
            init: function(config) {
                (new StatusFieldActions(config));
            }
        };
    });
