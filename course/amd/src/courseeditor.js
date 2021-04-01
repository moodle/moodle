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
 * Generic reactive module used in the course editor.
 *
 * @module     core_course/courseeditor
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import DefaultMutations from 'core_course/local/courseeditor/mutations';
import CourseEditor from 'core_course/local/courseeditor/courseeditor';
import events from 'core_course/events';

/**
 * Trigger a state changed event.
 *
 * This function will be moved to core_course/events module
 * when the file is migrated to the new JS events structure proposed in MDL-70990.
 *
 * @method dispatchStateChangedEvent
 * @param {object} detail the full state
 * @param {object} target the custom event target (document if none provided)
 */
function dispatchStateChangedEvent(detail, target) {
    if (target === undefined) {
        target = document;
    }
    target.dispatchEvent(new CustomEvent(events.stateChanged, {
        bubbles: true,
        detail: detail,
    }));
}

/**
 * This is the courseditor instance all components will register in.
 */
export const courseEditor = new CourseEditor({
    name: 'CourseEditor',
    eventName: events.stateChanged,
    eventDispatch: dispatchStateChangedEvent,
    // Mutations can be overridden by the format plugin using setMutations
    // but we need the default one at least.
    mutations: new DefaultMutations(),
});

/**
 * This method is called only once to load the initial state when the page is ready.
 *
 * @param {int} courseId the current course id
 */
export const init = (courseId) => {
    courseEditor.loadCourse(courseId);
};
