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
 * Contain the logic for the quick add or update event modal.
 *
 * @module     core_calendar/modal_event_form
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import * as CustomEvents from 'core/custom_interaction_events';
import Modal from 'core/modal';
import * as FormEvents from 'core_form/events';
import CalendarEvents from './events';
import * as Str from 'core/str';
import * as Notification from 'core/notification';
import * as Fragment from 'core/fragment';
import * as Repository from 'core_calendar/repository';

const SELECTORS = {
    SAVE_BUTTON: '[data-action="save"]',
    LOADING_ICON_CONTAINER: '[data-region="loading-icon-container"]',
};

export default class ModalEventForm extends Modal {
    static TYPE = 'core_calendar-modal_event_form';
    static TEMPLATE = 'calendar/modal_event_form';

    /**
     * Constructor for the Modal.
     *
     * @param {object} root The root jQuery element for the modal
     */
    constructor(root) {
        super(root);

        this.eventId = null;
        this.startTime = null;
        this.courseId = null;
        this.categoryId = null;
        this.contextId = null;
        this.reloadingBody = false;
        this.reloadingTitle = false;
        this.saveButton = this.getFooter().find(SELECTORS.SAVE_BUTTON);
    }

    configure(modalConfig) {
        modalConfig.large = true;
        super.configure(modalConfig);
    }

    /**
     * Set the context id to the given value.
     *
     * @method setContextId
     * @param {Number} id The event id
     */
    setContextId(id) {
        this.contextId = id;
    }

    /**
     * Retrieve the current context id, if any.
     *
     * @method getContextId
     * @return {Number|null} The event id
     */
    getContextId() {
        return this.contextId;
    }

    /**
     * Set the course id to the given value.
     *
     * @method setCourseId
     * @param {Number} id The event id
     */
    setCourseId(id) {
        this.courseId = id;
    }

    /**
     * Retrieve the current course id, if any.
     *
     * @method getCourseId
     * @return {Number|null} The event id
     */
    getCourseId() {
        return this.courseId;
    }

    /**
     * Set the category id to the given value.
     *
     * @method setCategoryId
     * @param {Number} id The event id
     */
    setCategoryId(id) {
        this.categoryId = id;
    }

    /**
     * Retrieve the current category id, if any.
     *
     * @method getCategoryId
     * @return {Number|null} The event id
     */
    getCategoryId() {
        return this.categoryId;
    }

    /**
     * Check if the modal has an course id.
     *
     * @method hasCourseId
     * @return {bool}
     */
    hasCourseId() {
        return this.courseId !== null;
    }

    /**
     * Check if the modal has an category id.
     *
     * @method hasCategoryId
     * @return {bool}
     */
    hasCategoryId() {
        return this.categoryId !== null;
    }

    /**
     * Set the event id to the given value.
     *
     * @method setEventId
     * @param {Number} id The event id
     */
    setEventId(id) {
        this.eventId = id;
    }

    /**
     * Retrieve the current event id, if any.
     *
     * @method getEventId
     * @return {Number|null} The event id
     */
    getEventId() {
        return this.eventId;
    }

    /**
     * Check if the modal has an event id.
     *
     * @method hasEventId
     * @return {bool}
     */
    hasEventId() {
        return this.eventId !== null;
    }

    /**
     * Set the start time to the given value.
     *
     * @method setStartTime
     * @param {Number} time The start time
     */
    setStartTime(time) {
        this.startTime = time;
    }

    /**
     * Retrieve the current start time, if any.
     *
     * @method getStartTime
     * @return {Number|null} The start time
     */
    getStartTime() {
        return this.startTime;
    }

    /**
     * Check if the modal has start time.
     *
     * @method hasStartTime
     * @return {bool}
     */
    hasStartTime() {
        return this.startTime !== null;
    }

    /**
     * Get the form element from the modal.
     *
     * @method getForm
     * @return {object}
     */
    getForm() {
        return this.getBody().find('form');
    }

    /**
     * Disable the buttons in the footer.
     *
     * @method disableButtons
     */
    disableButtons() {
        this.saveButton.prop('disabled', true);
    }

    /**
     * Enable the buttons in the footer.
     *
     * @method enableButtons
     */
    enableButtons() {
        this.saveButton.prop('disabled', false);
    }

    /**
     * Reload the title for the modal to the appropriate value
     * depending on whether we are creating a new event or
     * editing an existing event.
     *
     * @method reloadTitleContent
     * @return {object} A promise resolved with the new title text
     */
    reloadTitleContent() {
        if (this.reloadingTitle) {
            return this.titlePromise;
        }

        this.reloadingTitle = true;

        if (this.hasEventId()) {
            this.titlePromise = Str.get_string('editevent', 'calendar');
        } else {
            this.titlePromise = Str.get_string('newevent', 'calendar');
        }

        this.titlePromise.then((string) => {
            this.setTitle(string);
            return string;
        })
        .catch(Notification.exception)
        .always(() => {
            this.reloadingTitle = false;
            return;
        });

        return this.titlePromise;
    }

    /**
     * Send a request to the server to get the event_form in a fragment
     * and render the result in the body of the modal.
     *
     * If serialised form data is provided then it will be sent in the
     * request to the server to have the form rendered with the data. This
     * is used when the form had a server side error and we need the server
     * to re-render it for us to display the error to the user.
     *
     * @method reloadBodyContent
     * @param {string} formData The serialised form data
     * @return {object} A promise resolved with the fragment html and js from
     */
    reloadBodyContent(formData) {
        if (this.reloadingBody) {
            return this.bodyPromise;
        }

        this.reloadingBody = true;
        this.disableButtons();

        const args = {};

        if (this.hasEventId()) {
            args.eventid = this.getEventId();
        }

        if (this.hasStartTime()) {
            args.starttime = this.getStartTime();
        }

        if (this.hasCourseId()) {
            args.courseid = this.getCourseId();
        }

        if (this.hasCategoryId()) {
            args.categoryid = this.getCategoryId();
        }

        if (typeof formData !== 'undefined') {
            args.formdata = formData;
        }

        this.bodyPromise = Fragment.loadFragment('calendar', 'event_form', this.getContextId(), args);

        this.setBody(this.bodyPromise);

        this.bodyPromise.then(() => {
            this.enableButtons();
            return;
        })
        .catch(Notification.exception)
        .always(() => {
            this.reloadingBody = false;
            return;
        });

        return this.bodyPromise;
    }

    /**
     * Reload both the title and body content.
     *
     * @method reloadAllContent
     * @return {object} promise
     */
    reloadAllContent() {
        return $.when(this.reloadTitleContent(), this.reloadBodyContent());
    }

    /**
     * Kick off a reload the modal content before showing it. This
     * is to allow us to re-use the same modal for creating and
     * editing different events within the page.
     *
     * We do the reload when showing the modal rather than hiding it
     * to save a request to the server if the user closes the modal
     * and never re-opens it.
     *
     * @method show
     */
    show() {
        this.reloadAllContent();
        super.show(this);
    }

    /**
     * Clear the event id from the modal when it's closed so
     * that it is loaded fresh next time it's displayed.
     *
     * The event id will be set by the calling code if it wants
     * to edit a specific event.
     *
     * @method hide
     */
    hide() {
        super.hide(this);
        this.setEventId(null);
        this.setStartTime(null);
        this.setCourseId(null);
        this.setCategoryId(null);
    }

    /**
     * Get the serialised form data.
     *
     * @method getFormData
     * @return {string} serialised form data
     */
    getFormData() {
        return this.getForm().serialize();
    }

    /**
     * Send the form data to the server to create or update
     * an event.
     *
     * If there is a server side validation error then we re-request the
     * rendered form (with the data) from the server in order to get the
     * server side errors to display.
     *
     * On success the modal is hidden and the page is reloaded so that the
     * new event will display.
     *
     * @method save
     * @return {object} A promise
     */
    save() {
        const loadingContainer = this.saveButton.find(SELECTORS.LOADING_ICON_CONTAINER);

        // Now the change events have run, see if there are any "invalid" form fields.
        const invalid = this.getForm().find('[aria-invalid="true"]');

        // If we found invalid fields, focus on the first one and do not submit via ajax.
        if (invalid.length) {
            invalid.first().focus();
            return Promise.resolve();
        }

        loadingContainer.removeClass('hidden');
        this.disableButtons();

        const formData = this.getFormData();
        // Send the form data to the server for processing.
        return Repository.submitCreateUpdateForm(formData)
            .then((response) => {
                if (response.validationerror) {
                    // If there was a server side validation error then
                    // we need to re-request the rendered form from the server
                    // in order to display the error for the user.
                    this.reloadBodyContent(formData);
                    return;
                } else {
                    // Check whether this was a new event or not.
                    // The hide function unsets the form data so grab this before the hide.
                    const isExisting = this.hasEventId();

                    // No problemo! Our work here is done.
                    this.hide();

                    // Trigger the appropriate calendar event so that the view can be updated.
                    if (isExisting) {
                        $('body').trigger(CalendarEvents.updated, [response.event]);
                    } else {
                        $('body').trigger(CalendarEvents.created, [response.event]);
                    }
                }

                return;
            })
            .catch(Notification.exception)
            .always(() => {
                // Regardless of success or error we should always stop
                // the loading icon and re-enable the buttons.
                loadingContainer.addClass('hidden');
                this.enableButtons();

                return;
            });
    }

    /**
     * Set up all of the event handling for the modal.
     *
     * @method registerEventListeners
     * @fires event:uploadStarted
     * @fires event:formSubmittedByJavascript
     */
    registerEventListeners() {
        // Apply parent event listeners.
        super.registerEventListeners(this);

        // When the user clicks the save button we trigger the form submission. We need to
        // trigger an actual submission because there is some JS code in the form that is
        // listening for this event and doing some stuff (e.g. saving draft areas etc).
        this.getModal().on(CustomEvents.events.activate, SELECTORS.SAVE_BUTTON, (e, data) => {
            this.getForm().submit();
            data.originalEvent.preventDefault();
            e.stopPropagation();
        });

        // Catch the submit event before it is actually processed by the browser and
        // prevent the submission. We'll take it from here.
        this.getModal().on('submit', (e) => {
            FormEvents.notifyFormSubmittedByJavascript(this.getForm()[0]);

            this.save();

            // Stop the form from actually submitting and prevent it's
            // propagation because we have already handled the event.
            e.preventDefault();
            e.stopPropagation();
        });
    }
}

ModalEventForm.registerModalType();
