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
 * The activity header component.
 *
 * @module     core_courseformat/local/content/activity_header
 * @class      core_courseformat/local/content/activity_header
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {BaseComponent} from 'core/reactive';
import {getCurrentCourseEditor} from 'core_courseformat/courseeditor';
import * as CourseEvents from 'core_course/events';
import Templates from 'core/templates';

// Global page selectors.
const SELECTORS = {
    ACTIVITY_HEADER: `[data-for='page-activity-header']`,
    COMPLETION_STATUS: `[data-region='completion-status']`,
};

// Template used to render the activity completion status indicator in the header.
const COMPLETION_STATUS_TEMPLATE = 'core_course/completion_status';

export default class Component extends BaseComponent {

    /**
     * Constructor hook.
     */
    create() {
        // Optional component name for debugging.
        this.name = 'activity_header';
    }

    /**
     * Static method to create a component instance form the mustache template.
     *
     * @param {string} target optional altentative DOM main element CSS selector
     * @param {object} selectors optional css selector overrides
     * @return {Component}
     */
    static init(target, selectors) {
        const elementselector = (target) ? target : SELECTORS.ACTIVITY_HEADER;
        return new this({
            element: document.querySelector(elementselector),
            reactive: getCurrentCourseEditor(),
            selectors
        });
    }

    /**
     * Initial state ready method.
     */
    stateReady() {
        // Capture completion events from anywhere on the page. The manual completion button may be
        // relocated to the linear navigation sticky footer, which is outside the activity header,
        // so the toggle event must be captured at the document level.
        this.addEventListener(
            document,
            CourseEvents.manualCompletionToggled,
            this._completionHandler
        );
    }

    /**
     * Activity manual completion listener.
     *
     * @param {Event} event the custom event
     * @param {object} event.detail the event details
     */
    _completionHandler(event) {
        const {detail} = event;
        if (detail === undefined || event.activityHeaderCompletionHandled) {
            return;
        }
        // More than one activity header component may listen for this event (for example the
        // page heading and the activity header instances). Make sure the completion change is
        // only processed once per event.
        event.activityHeaderCompletionHandled = true;
        this.reactive.dispatch('cmCompletion', [detail.cmid], detail.completed);
        this._refreshCompletionStatus(detail);
    }

    /**
     * Refresh the read-only completion status indicator displayed in the activity header.
     *
     * This keeps the header indicator in sync when the manual completion button is toggled,
     * for instance from the linear navigation sticky footer.
     *
     * @param {object} detail the completion toggle event details
     * @param {number} detail.cmid the course module id
     * @param {boolean} detail.completed whether the activity is now complete
     */
    async _refreshCompletionStatus({cmid, completed}) {
        const statusElement = document.querySelector(
            `${SELECTORS.COMPLETION_STATUS}[data-cmid='${cmid}']`
        );
        if (!statusElement) {
            return;
        }
        const {html, js} = await Templates.renderForPromise(COMPLETION_STATUS_TEMPLATE, {
            cmid,
            istrackeduser: true, // We know completion is tracked for this user given the toggle event originated from their button.
            overallcomplete: completed,
            overallincomplete: !completed,
        });
        Templates.replaceNode(statusElement, html, js);
    }
}
