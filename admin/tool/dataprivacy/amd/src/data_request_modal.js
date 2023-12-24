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
 * Request actions.
 *
 * @module     tool_dataprivacy/data_request_modal
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import * as CustomEvents from 'core/custom_interaction_events';
import Modal from 'core/modal';
import DataPrivacyEvents from './events';

const SELECTORS = {
    APPROVE_BUTTON: '[data-action="approve"]',
    DENY_BUTTON: '[data-action="deny"]',
    COMPLETE_BUTTON: '[data-action="complete"]',
    APPROVE_REQUEST_SELECT_COURSE: '[data-action="approve-selected-courses"]',
};

export default class ModalDataRequest extends Modal {
    static TYPE = 'tool_dataprivacy-data_request';
    static TEMPLATE = 'tool_dataprivacy/data_request_modal';

    /**
     * Set up all of the event handling for the modal.
     */
    registerEventListeners() {
        // Apply parent event listeners.
        super.registerEventListeners(this);

        this.getModal().on(CustomEvents.events.activate, SELECTORS.APPROVE_BUTTON, (e, data) => {
            const approveEvent = $.Event(DataPrivacyEvents.approve);
            this.getRoot().trigger(approveEvent, this);

            if (!approveEvent.isDefaultPrevented()) {
                this.hide();
                data.originalEvent.preventDefault();
            }
        });

        this.getModal().on(CustomEvents.events.activate, SELECTORS.DENY_BUTTON, (e, data) => {
            const denyEvent = $.Event(DataPrivacyEvents.deny);
            this.getRoot().trigger(denyEvent, this);

            if (!denyEvent.isDefaultPrevented()) {
                this.hide();
                data.originalEvent.preventDefault();
            }
        });

        this.getModal().on(CustomEvents.events.activate, SELECTORS.COMPLETE_BUTTON, (e, data) => {
            const completeEvent = $.Event(DataPrivacyEvents.complete);
            this.getRoot().trigger(completeEvent, this);

            if (!completeEvent.isDefaultPrevented()) {
                this.hide();
                data.originalEvent.preventDefault();
            }
        });

        this.getModal().on(CustomEvents.events.activate, SELECTORS.APPROVE_REQUEST_SELECT_COURSE, (e, data) => {
            let approveSelectCoursesEvent = $.Event(DataPrivacyEvents.approveSelectCourses);
            this.getRoot().trigger(approveSelectCoursesEvent, this);

            if (!approveSelectCoursesEvent.isDefaultPrevented()) {
                this.hide();
                data.originalEvent.preventDefault();
            }
        });

    }
}

ModalDataRequest.registerModalType();
