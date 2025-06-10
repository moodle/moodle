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
 * Manage tags for a learning plan popup.
 *
 * @module     report_lpmonitoring/tags_popup
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréal
 */
define(['jquery', 'core/str', 'core/modal_factory', 'core/modal_events', 'core/fragment', 'core/ajax', 'core/notification'],
    function($, Str, ModalFactory, ModalEvents, Fragment, Ajax, Notification) {

        /**
         * Constructor.
         *
         * @param {String} selector_button The CSS selector used to find triggers for the new group modal.
         * @param {string} selector_nbtags The CSS selector used to display the new number of tags for the plan.
         * @param {int} contextid
         * @param {int} planid The learning plan id.
         *
         * Each call to init gets it's own instance of this class.
         */
        var TagsPopup = function(selector_button, selector_nbtags, contextid, planid) {
            this.contextid = contextid;
            this.planid = planid;
            this.selector_nbtags = selector_nbtags;
            $(selector_button).on('click', this.init.bind(this));
        };

        /**
         * @var {Modal} modal
         * @private
         */
        TagsPopup.prototype.modal = null;

        /**
         * @var {int} contextid
         * @private
         */
        TagsPopup.prototype.contextid = -1;

        /**
         * @var {int} planid
         * @private
         */
        TagsPopup.prototype.planid = -1;

        /**
         * @var {string} selector_nbtags  The CSS selector used to display the new number of tags for the plan.
         * @private
         */
        TagsPopup.prototype.selector_nbtags = '';

        /**
         * Initialise the class.
         *
         * @param {Event} e click event.
         * @private
         * @return {Promise}
         */
        TagsPopup.prototype.init = function(e) {
            e.preventDefault();
            var trigger = $(e.target);
            // Fetch the title string.
            return Str.get_string('tagsedit', 'report_lpmonitoring').then(function(title) {
                // Create the modal.
                return ModalFactory.create({
                    type: ModalFactory.types.SAVE_CANCEL,
                    title: title
                }).then(function(modal) {
                    // Keep a reference to the modal.
                    this.modal = modal;

                    // Forms are big, we want a big modal.
                    this.modal.setLarge();
                    // We want to reset the form every time it is opened.
                    this.modal.getRoot().on(ModalEvents.shown, function() {
                        this.modal.setBody(this.getBody());
                    }.bind(this));
                    // We want to hide the submit buttons of the form every time it is opened.
                    this.modal.getRoot().on(ModalEvents.bodyRendered, function() {
                        this.modal.getRoot().find('[data-groupname=buttonar]').addClass('hidden');
                    }.bind(this));

                    modal.getRoot().on(ModalEvents.hidden, function() {
                        this.close();
                        this.focusContentItem(trigger);
                    }.bind(this));

                    // We catch the modal save event, and use it to submit the form inside the modal.
                    // Triggering a form submission will give JS validation scripts a chance to check for errors.
                    this.modal.getRoot().on(ModalEvents.save, this.submitForm.bind(this));
                    // We also catch the form submit event and use it to submit the form with ajax.
                    this.modal.getRoot().on('submit', 'form', this.submitFormAjax.bind(this));

                    this.modal.show();
                }.bind(this));
            }.bind(this));
        };

        /**
         * Focus the given content item or the first focusable element within
         * the content item.
         *
         * @method focusContentItem
         * @param {object} item The content item jQuery element
         */
        TagsPopup.prototype.focusContentItem = function(item) {
            var focusable = 'input:not([type="hidden"]), a[href], button, textarea, select, [tabindex]';
            if (item.is(focusable)) {
                item.focus();
            } else {
                item.find(focusable).first().focus();
            }
        };

        /**
         * Close the popup.
         *
         * @method close
         */
        TagsPopup.prototype.close = function() {
            this.modal.destroy();
            this.modal = null;
        };

        /**
         * @method getBody
         * @param {Object} formdata
         * @private
         * @return {Promise}
         */
        TagsPopup.prototype.getBody = function(formdata) {
            if (typeof formdata === "undefined") {
                formdata = {};
            }
            // Get the content of the modal.
            var params = {jsonformdata: JSON.stringify(formdata), planid: this.planid, contextid: this.contextid};
            var frag = Fragment.loadFragment('report_lpmonitoring', 'tags', this.contextid, params);
            return frag;
        };

        /**
         * Submit the form via ajax.
         *
         * @method submitFormAjax
         * @private
         * @param {Event} e Form submission event.
         */
        TagsPopup.prototype.submitFormAjax = function(e) {
            // We don't want to do a real form submission.
            e.preventDefault();

            // Convert all the form elements values to a serialised string.
            var formData = this.modal.getRoot().find('form').serialize();

            var tagspopup = this;
            var promises = Ajax.call([{
                methodname: 'report_lpmonitoring_submit_manage_tags_form',
                args: {
                    contextid: this.contextid,
                    jsonformdata: JSON.stringify(formData)
                }
            }]);

            promises[0].done(function(response) {
                $(tagspopup.selector_nbtags).text(response);
                tagspopup.modal.hide();
            }).fail(function(exp) {
                Notification.exception(exp);
                // We should re-display the form with errors but since there is no real validation, it is not necessary.
            });
        };

        /**
         * This triggers a form submission, so that any mform elements can do final tricks before the form submission is processed.
         *
         * @method submitForm
         * @param {Event} e Form submission event.
         * @private
         */
        TagsPopup.prototype.submitForm = function(e) {
            e.preventDefault();
            this.modal.getRoot().find('form').submit();
        };

        return {
            /**
             * Attach event listeners to initialise this module.
             *
             * @method init
             * @param {string} selector_button The CSS selector used to find nodes that will trigger this module.
             * @param {string} selector_nbtags The CSS selector used to display the new number of tags for the plan.
             * @param {int} contextid The contextid.
             * @param {int} planid The learning plan id.
             * @return {TagsPopup} A new instance of TagsPopup.
             */
            init: function(selector_button, selector_nbtags, contextid, planid) {
                return new TagsPopup(selector_button, selector_nbtags, contextid, planid);
            }
        };
    });