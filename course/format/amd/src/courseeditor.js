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
 * @module     core_courseformat/courseeditor
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import DefaultMutations from 'core_courseformat/local/courseeditor/mutations';
import CourseEditor from 'core_courseformat/local/courseeditor/courseeditor';
import events from 'core_course/events';

// A map with all the course editor instances.
const courseEditorMap = new Map();

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
 * Setup the current view settings
 *
 * @param {number} courseId the course id
 * @param {setup} setup format, page and course settings
 * @param {boolean} setup.editing if the page is in edit mode
 * @param {boolean} setup.supportscomponents if the format supports components for content
 */
export const setViewFormat = (courseId, setup) => {
    const editor = getCourseEditor(courseId);
    editor.setViewFormat(setup);
};

/**
 * Get a specific course editor reactive instance.
 *
 * @param {number} courseId the course id
 * @returns {CourseEditor}
 */
export const getCourseEditor = (courseId) => {
    courseId = parseInt(courseId);

    if (!courseEditorMap.has(courseId)) {
        courseEditorMap.set(
            courseId,
            new CourseEditor({
                name: `CourseEditor${courseId}`,
                eventName: events.stateChanged,
                eventDispatch: dispatchStateChangedEvent,
                // Mutations can be overridden by the format plugin using setMutations
                // but we need the default one at least.
                mutations: new DefaultMutations(),
            })
        );
        courseEditorMap.get(courseId).loadCourse(courseId);
    }
    return courseEditorMap.get(courseId);
};

/**
 * Get the current course reactive instance.
 *
 * @returns {CourseEditor}
 */
export const getCurrentCourseEditor = () => getCourseEditor(M.cfg.courseId);
