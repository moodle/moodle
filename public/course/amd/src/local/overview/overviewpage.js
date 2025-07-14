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
 * Controls the fragment overview loadings.
 *
 * @module     core_course/local/overview/overviewpage
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import CourseContent from 'core_courseformat/local/content';
import {eventTypes as collapsableSectionEventTypes} from 'core/local/collapsable_section/events';
import Fragment from 'core/fragment';
import {getCurrentCourseEditor} from 'core_courseformat/courseeditor';
import Pending from 'core/pending';
import Templates from 'core/templates';

/**
 * Initialize the overview page.
 *
 * @param {String} selector The selector where the overview page is located.
 */
export const init = async(selector) => {
    const pageElement = document.querySelector(selector);
    if (!pageElement) {
        throw new Error('No elements found with the selector: ' + selector);
    }

    pageElement.addEventListener(
        collapsableSectionEventTypes.shown,
        event => {
            const fragmentElement = getFragmentContainer(event.target);
            if (!fragmentElement) {
                return;
            }
            loadFragmentContent(fragmentElement);
        }
    );

    // The overview page is considered an alternative course view page so it must
    // include the course content component to capture any possible action. For example,
    // capturing manual completion toggles.
    return new CourseContent({
        element: pageElement,
        reactive: getCurrentCourseEditor(),
    });
};

/**
 * Load the fragment content.
 *
 * @private
 * @param {HTMLElement} element The element where the fragment content will be loaded.
 */
const loadFragmentContent = (element) => {
    if (element.dataset.loaded) {
        return;
    }

    const pendingReload = new Pending(`course_overviewtable_${element.dataset.modname}`);

    const promise = Fragment.loadFragment(
        'core_course',
        'course_overview',
        element.dataset.contextid,
        {
            courseid: element.dataset.courseid,
            modname: element.dataset.modname,
        }
    );

    promise.then(async(html, js) => {
        Templates.runTemplateJS(js);
        element.innerHTML = html;
        // Templates.replaceNode(element, html, js);
        element.dataset.loaded = true;
        pendingReload.resolve();
        return true;
    }).catch(() => {
        pendingReload.resolve();
    });
};

/**
 * Get the fragment container.
 *
 * @private
 * @param {HTMLElement} element The element where the fragment container is located.
 * @return {HTMLElement|null} The fragment container.
 */
const getFragmentContainer = (element) => {
    const result = element.querySelector('[data-region="loading-icon-container"]');
    if (!result) {
        return null;
    }
    if (!result.dataset.contextid || !result.dataset.courseid || !result.dataset.modname) {
        throw new Error('The element is missing required data attributes.');
    }
    return result;
};
