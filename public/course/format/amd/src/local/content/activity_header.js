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

// Global page selectors.
const SELECTORS = {
    ACTIVITY_HEADER: `[data-for='page-activity-header']`,
};

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
        return new Component({
            element: document.querySelector(elementselector),
            reactive: getCurrentCourseEditor(),
            selectors
        });
    }

    /**
     * Initial state ready method.
     */
    stateReady() {
        // Capture completion events.
        this.addEventListener(
            this.element,
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
    _completionHandler({detail}) {
        if (detail === undefined) {
            return;
        }
        this.reactive.dispatch('cmCompletion', [detail.cmid], detail.completed);
    }
}
