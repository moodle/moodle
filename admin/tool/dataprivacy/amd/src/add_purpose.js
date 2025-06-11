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
 * Module to add purposes.
 *
 * @module     tool_dataprivacy/add_purpose
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/str',
    'core/ajax',
    'core/notification',
    'core/modal_save_cancel',
    'core/modal_events',
    'core/fragment',
    'core_form/changechecker',
], function(
    $,
    Str,
    Ajax,
    Notification,
    ModalSaveCancel,
    ModalEvents,
    Fragment,
    FormChangeChecker
) {

        var SELECTORS = {
            PURPOSE_LINK: '[data-add-element="purpose"]',
        };

        var AddPurpose = function(contextId) {
            this.contextId = contextId;

            var stringKeys = [
                {
                    key: 'addpurpose',
                    component: 'tool_dataprivacy'
                },
                {
                    key: 'save',
                    component: 'admin'
                }
            ];
            this.strings = Str.get_strings(stringKeys);

            this.registerEventListeners();
        };

        /**
         * @var {int} contextId
         * @private
         */
        AddPurpose.prototype.contextId = 0;

        /**
         * @var {Promise}
         * @private
         */
        AddPurpose.prototype.strings = 0;

        AddPurpose.prototype.registerEventListeners = function() {

            var trigger = $(SELECTORS.PURPOSE_LINK);
            trigger.on('click', function() {
                this.strings.then(function(strings) {
                    return Promise.all([
                        ModalSaveCancel.create({
                            title: strings[0],
                            body: '',
                        }),
                        strings[1],
                    ]).then(function([modal, string]) {
                        this.setupFormModal(modal, string);
                    }.bind(this));
                }.bind(this))
                .catch(Notification.exception);
            }.bind(this));

        };

        /**
         * @method getBody
         * @param {Object} formdata
         * @private
         * @return {Promise}
         */
        AddPurpose.prototype.getBody = function(formdata) {

            var params = null;
            if (typeof formdata !== "undefined") {
                params = {jsonformdata: JSON.stringify(formdata)};
            }
            // Get the content of the modal.
            return Fragment.loadFragment('tool_dataprivacy', 'addpurpose_form', this.contextId, params);
        };

        AddPurpose.prototype.setupFormModal = function(modal, saveText) {
            modal.setLarge();

            modal.setSaveButtonText(saveText);

            // We want to reset the form every time it is opened.
            modal.getRoot().on(ModalEvents.hidden, this.destroy.bind(this));

            modal.setBody(this.getBody());

            // We catch the modal save event, and use it to submit the form inside the modal.
            // Triggering a form submission will give JS validation scripts a chance to check for errors.
            modal.getRoot().on(ModalEvents.save, this.submitForm.bind(this));
            // We also catch the form submit event and use it to submit the form with ajax.
            modal.getRoot().on('submit', 'form', this.submitFormAjax.bind(this));

            this.modal = modal;

            modal.show();
        };

        /**
         * This triggers a form submission, so that any mform elements can do final tricks before the form submission is processed.
         *
         * @method submitForm
         * @param {Event} e Form submission event.
         * @private
         */
        AddPurpose.prototype.submitForm = function(e) {
            e.preventDefault();
            this.modal.getRoot().find('form').submit();
        };

        AddPurpose.prototype.submitFormAjax = function(e) {
            // We don't want to do a real form submission.
            e.preventDefault();

            // Convert all the form elements values to a serialised string.
            var formData = this.modal.getRoot().find('form').serialize();

            Ajax.call([{
                methodname: 'tool_dataprivacy_create_purpose_form',
                args: {jsonformdata: JSON.stringify(formData)},
                done: function(data) {
                    if (data.validationerrors) {
                        this.modal.setBody(this.getBody(formData));
                    } else {
                        this.close();
                    }
                }.bind(this),

                fail: Notification.exception
            }]);
        };

        AddPurpose.prototype.close = function() {
            this.destroy();
            document.location.reload();
        };

        AddPurpose.prototype.destroy = function() {
            FormChangeChecker.resetAllFormDirtyStates();
            this.modal.destroy();
        };

        AddPurpose.prototype.removeListeners = function() {
            $(SELECTORS.PURPOSE_LINK).off('click');
        };

        return /** @alias module:tool_dataprivacy/add_purpose */ {
            getInstance: function(contextId) {
                return new AddPurpose(contextId);
            }
        };
    }
);
