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
 * A type of dialogue used as for choosing modules in a course.
 *
 * @module     core_courseformat/activitychooser
 * @copyright  2020 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as ChooserDialogue from 'core_courseformat/local/activitychooser/dialogue';
import CustomEvents from 'core/custom_interaction_events';
import Log from 'core/log';
import Pending from 'core/pending';
import * as Repository from 'core_courseformat/local/activitychooser/repository';
import selectors from 'core_courseformat/local/activitychooser/selectors';

let initialized = false;

/**
 * Set up the activity chooser.
 *
 * @method init
 * @param {Number} courseId Course ID to use later on in fetchModules()
 * @param {Object|null} chooserConfig Any PHP config settings that we may need to reference
 */
export const init = (courseId, chooserConfig = null) => {
    const pendingPromise = new Pending();

    // TODO: Remove the chooserConfig in Moodle 6.0 (MDL-85655)
    if (chooserConfig?.tabmode !== undefined) {
        window.console.warn('The tabmode config option has been deprecated and will be ignored.');
    }

    registerListenerEvents(courseId);

    pendingPromise.resolve();
};

/**
 * Once a selection has been made make the modal & module information and pass it along
 *
 * @method registerListenerEvents
 * @param {Number} courseId
 */
const registerListenerEvents = (courseId) => {
    if (initialized) {
        return;
    }
    initialized = true;

    const eventsToHandle = [
        'click',
        CustomEvents.events.activate,
        CustomEvents.events.keyboardActivate
    ];

    CustomEvents.define(document, eventsToHandle);

    // Display module chooser event listeners.
    eventsToHandle.forEach((eventToHandle) => {
        document.addEventListener(eventToHandle, async(event) => {
            if (!event.target.closest(selectors.elements.sectionmodchooser)) {
                return;
            }
            const position = getCoursePositionFromTarget(event.target);

            const footerDataPromise = Repository.getModalFooterData(courseId, position.sectionNum);

            let modulesDataPromise;
            if (position.sectionId && position.sectionId !== '') {
                modulesDataPromise = Repository.getSectionModulesData(
                    courseId,
                    position.sectionId,
                    position.sectionReturnNum,
                    position.beforeMod,
                );
            } else {
                // Todo remove this else in Moodle 6.0 (MDL-86310)
                Log.debug(
                    'Having only the section number attribute in the activity chooser is deprecated. ' +
                    'Please add the data-section-id attribute.'
                );
                modulesDataPromise = Repository.getModulesData(
                    courseId,
                    position.sectionNum,
                    position.sectionReturnNum,
                    position.beforeMod,
                );
            }

            ChooserDialogue.displayActivityChooserModal(footerDataPromise, modulesDataPromise);
        });
    });
};

/**
 * Return the course position of a target add activity element.
 *
 * @param {HTMLElement} target The target element.
 * @return {Object} The course position of the target.
 * @property {Number} sectionNum The section number.
 * @property {Number|null} sectionId The section id.
 * @property {Number|null} sectionReturnNum The section return number.
 * @property {Number|null} sectionReturnId The section return id.
 * @property {Number|null} beforeMod The ID of the cm to add the modules before.
 */
function getCoursePositionFromTarget(target) {
    let caller;
    let sectionNum = null;
    let sectionId = null;
    // We need to know who called this.
    // Standard courses use the ID in the main section info.
    const sectionDiv = target.closest(selectors.elements.section);
    // Front page courses need some special handling.
    const button = target.closest(selectors.elements.sectionmodchooser);

    // If we don't have a section number use the fallback ID.
    // We always want the sectionDiv caller first as it keeps track of section number's after DnD changes.
    // The button attribute is always just a fallback for us as the section div is not always available.
    // A YUI change could be done maybe to only update the button attribute but we are going for minimal change here.
    if (
        sectionDiv !== null
        && (sectionDiv.hasAttribute('data-number') || sectionDiv.hasAttribute('data-id'))
    ) {
        // We check for attributes just in case of outdated contrib course formats.
        caller = sectionDiv;
        sectionNum = sectionDiv.getAttribute('data-number');
        sectionId = sectionDiv.getAttribute('data-id');
    } else {
        caller = button;
        if (caller.hasAttribute('data-sectionid')) {
            window.console.warn(
                'The data-sectionid attribute has been deprecated. ' +
                'Please update your code to use data-section-id passing the real section ID instead.'
            );
            caller.setAttribute('data-sectionnum', caller.dataset.sectionid);
        }
        sectionNum = caller.dataset.sectionnum;
        sectionId = caller.getAttribute('data-section-id');
    }
    return {
        sectionNum,
        sectionId,
        // The old data attribute for the section return number was data-sectionreturn.
        sectionReturnNum: caller.dataset?.sectionreturnnum ?? caller.dataset?.sectionreturn ?? null,
        sectionReturnId: caller.dataset?.sectionreturnid ?? null,
        beforeMod: caller.dataset?.beforemod ?? null,
    };
}
