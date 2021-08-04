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
 * Add policy consent modal to the page
 *
 * @module     tool_policy/acceptmodal
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str', 'core/modal_factory', 'core/modal_events', 'core/notification', 'core/fragment',
        'core/ajax', 'core/yui'],
    function($, Str, ModalFactory, ModalEvents, Notification, Fragment, Ajax, Y) {

        "use strict";

        /**
         * Constructor
         *
         * @param {int} contextid
         *
         * Each call to init gets it's own instance of this class.
         */
        var AcceptOnBehalf = function(contextid) {
            this.contextid = contextid;
            this.init();
        };

        /**
         * @var {Modal} modal
         * @private
         */
        AcceptOnBehalf.prototype.modal = null;

        /**
         * @var {int} contextid
         * @private
         */
        AcceptOnBehalf.prototype.contextid = -1;

        /**
         * @var {object} currentTrigger The triggered HTML jQuery object
         * @private
         */
        AcceptOnBehalf.prototype.currentTrigger = null;

        /**
         * @var {object} triggers The trigger selectors
         * @private
         */
        AcceptOnBehalf.prototype.triggers = {
            SINGLE: 'a[data-action=acceptmodal]',
            BULK: 'input[data-action=acceptmodal]'
        };

        /**
         * Initialise the class.
         *
         * @private
         */
        AcceptOnBehalf.prototype.init = function() {
            // Initialise for links accepting policies for individual users.
            $(this.triggers.SINGLE).on('click', function(e) {
                e.preventDefault();
                this.currentTrigger = $(e.currentTarget);
                var href = $(e.currentTarget).attr('href'),
                    formData = href.slice(href.indexOf('?') + 1);
                this.showFormModal(formData);
            }.bind(this));

            // Initialise for multiple users acceptance form.
            $(this.triggers.BULK).on('click', function(e) {
                e.preventDefault();
                this.currentTrigger = $(e.currentTarget);
                var form = $(e.currentTarget).closest('form');
                if (form.find('input[type=checkbox][name="userids[]"]:checked').length) {
                    var formData = form.serialize();
                    this.showFormModal(formData);
                } else {
                    Str.get_strings([
                        {key: 'notice'},
                        {key: 'selectusersforconsent', component: 'tool_policy'},
                        {key: 'ok'}
                    ]).then(function(strings) {
                        Notification.alert(strings[0], strings[1], strings[2]);
                        return;
                    }).fail(Notification.exception);
                }
            }.bind(this));
        };

        /**
         * Show modal with a form
         *
         * @param {String} formData
         */
        AcceptOnBehalf.prototype.showFormModal = function(formData) {
            var action;
            var params = formData.split('&');
            for (var i = 0; i < params.length; i++) {
                var pair = params[i].split('=');
                if (pair[0] == 'action') {
                    action = pair[1];
                }
            }
            // Fetch the title string.
            Str.get_strings([
                {key: 'statusformtitleaccept', component: 'tool_policy'},
                {key: 'iagreetothepolicy', component: 'tool_policy'},
                {key: 'statusformtitlerevoke', component: 'tool_policy'},
                {key: 'irevokethepolicy', component: 'tool_policy'},
                {key: 'statusformtitledecline', component: 'tool_policy'},
                {key: 'declinethepolicy', component: 'tool_policy'}
            ]).then(function(strings) {
                var title;
                var saveText;
                if (action == 'accept') {
                    title = strings[0];
                    saveText = strings[1];
                } else if (action == 'revoke') {
                    title = strings[2];
                    saveText = strings[3];
                } else if (action == 'decline') {
                    title = strings[4];
                    saveText = strings[5];
                }
                // Create the modal.
                return ModalFactory.create({
                    type: ModalFactory.types.SAVE_CANCEL,
                    title: title,
                    body: ''
                }).done(function(modal) {
                    this.modal = modal;
                    this.setupFormModal(formData, saveText);
                }.bind(this));
            }.bind(this))
                .catch(Notification.exception);
        };

        /**
         * Setup form inside a modal
         *
         * @param {String} formData
         * @param {String} saveText
         */
        AcceptOnBehalf.prototype.setupFormModal = function(formData, saveText) {
            var modal = this.modal;

            modal.setLarge();

            modal.setSaveButtonText(saveText);

            // We want to reset the form every time it is opened.
            modal.getRoot().on(ModalEvents.hidden, this.destroy.bind(this));

            modal.setBody(this.getBody(formData));

            // We catch the modal save event, and use it to submit the form inside the modal.
            // Triggering a form submission will give JS validation scripts a chance to check for errors.
            modal.getRoot().on(ModalEvents.save, this.submitForm.bind(this));
            // We also catch the form submit event and use it to submit the form with ajax.
            modal.getRoot().on('submit', 'form', this.submitFormAjax.bind(this));

            modal.show();
        };

        /**
         * Load the body of the modal (contains the form)
         *
         * @method getBody
         * @private
         * @param {String} formData
         * @return {Promise}
         */
        AcceptOnBehalf.prototype.getBody = function(formData) {
            if (typeof formData === "undefined") {
                formData = {};
            }
            // Get the content of the modal.
            var params = {jsonformdata: JSON.stringify(formData)};
            return Fragment.loadFragment('tool_policy', 'accept_on_behalf', this.contextid, params);
        };

        /**
         * Submit the form inside the modal via AJAX request
         *
         * @method submitFormAjax
         * @private
         * @param {Event} e Form submission event.
         */
        AcceptOnBehalf.prototype.submitFormAjax = function(e) {
            // We don't want to do a real form submission.
            e.preventDefault();

            // Convert all the form elements values to a serialised string.
            var formData = this.modal.getRoot().find('form').serialize();

            var requests = Ajax.call([{
                methodname: 'tool_policy_submit_accept_on_behalf',
                args: {jsonformdata: JSON.stringify(formData)}
            }]);
            requests[0].done(function(data) {
                if (data.validationerrors) {
                    this.modal.setBody(this.getBody(formData));
                } else {
                    this.close();
                }
            }.bind(this)).fail(Notification.exception);
        };

        /**
         * This triggers a form submission, so that any mform elements can do final tricks before the form submission is processed.
         *
         * @method submitForm
         * @param {Event} e Form submission event.
         * @private
         */
        AcceptOnBehalf.prototype.submitForm = function(e) {
            e.preventDefault();
            this.modal.getRoot().find('form').submit();
        };

        /**
         * Close the modal
         */
        AcceptOnBehalf.prototype.close = function() {
            this.destroy();
            document.location.reload();
        };

        /**
         * Destroy the modal
         */
        AcceptOnBehalf.prototype.destroy = function() {
            Y.use('moodle-core-formchangechecker', function() {
                M.core_formchangechecker.reset_form_dirty_state();
            });
            this.modal.destroy();
            this.currentTrigger.focus();
        };

        return /** @alias module:tool_policy/acceptmodal */ {
            // Public variables and functions.
            /**
             * Attach event listeners to initialise this module.
             *
             * @method init
             * @param {int} contextid The contextid for the course.
             * @return {AcceptOnBehalf}
             */
            getInstance: function(contextid) {
                return new AcceptOnBehalf(contextid);
            }
        };
    });
