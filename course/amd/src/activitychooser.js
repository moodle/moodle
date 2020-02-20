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
 * @module     core_course/activitychooser
 * @package    core_course
 * @copyright  2020 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as ChooserDialogue from 'core_course/local/activitychooser/dialogue';
import * as Repository from 'core_course/local/activitychooser/repository';
import selectors from 'core_course/local/activitychooser/selectors';
import CustomEvents from 'core/custom_interaction_events';
import * as Templates from 'core/templates';
import * as ModalFactory from 'core/modal_factory';
import {get_string as getString} from 'core/str';
import Pending from 'core/pending';

/**
 * Set up the activity chooser.
 *
 * @method init
 * @param {Number} courseId Course ID to use later on in fetchModules()
 */
export const init = courseId => {
    const pendingPromise = new Pending();

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
    const events = [
        'click',
        CustomEvents.events.activate,
        CustomEvents.events.keyboardActivate
    ];

    const fetchModuleData = (() => {
        let innerPromise = null;

        return () => {
            if (!innerPromise) {
                innerPromise = new Promise((resolve) => {
                    resolve(Repository.activityModules(courseId));
                });
            }

            return innerPromise;
        };
    })();

    CustomEvents.define(document, events);

    // Display module chooser event listeners.
    events.forEach((event) => {
        document.addEventListener(event, async(e) => {
            if (e.target.closest(selectors.elements.sectionmodchooser)) {
                const caller = e.target.closest(selectors.elements.sectionmodchooser);
                const builtModuleData = sectionIdMapper(await fetchModuleData(), caller.dataset.sectionid);
                const sectionModal = await modalBuilder(builtModuleData);

                ChooserDialogue.displayChooser(caller, sectionModal, builtModuleData);
            }
        });
    });
};

/**
 * Given the web service data and an ID we want to make a deep copy
 * of the WS data then add on the section ID to the addoption URL
 *
 * @method sectionIdMapper
 * @param {Object} webServiceData Our original data from the Web service call
 * @param {Array} id The ID of the section we need to append to the links
 * @return {Array} [modules] with URL's built
 */
const sectionIdMapper = (webServiceData, id) => {
    // We need to take a fresh deep copy of the original data as an object is a reference type.
    const newData = JSON.parse(JSON.stringify(webServiceData));
    newData.content_items.forEach((module) => {
        module.link += '&section=' + id;
    });
    return newData.content_items;
};

/**
 * Build a modal for each section ID and store it into a map for quick access
 *
 * @method modalBuilder
 * @param {Map} data our map of section ID's & modules to generate modals for
 * @return {Object} TODO
 */
const modalBuilder = data => buildModal(templateDataBuilder(data));

/**
 * Given an array of modules we want to figure out where & how to place them into our template object
 *
 * @method templateDataBuilder
 * @param {Array} data our modules to manipulate into a Templatable object
 * @return {Object} Our built object ready to render out
 */
const templateDataBuilder = (data) => {
    // Filter the incoming data to find favourite & recommended modules.
    const favourites = [];
    const recommended = [];

    // Given the results of the above filters lets figure out what tab to set active.

    // We have some favourites.
    const favouritesFirst = !!favourites.length;
    // Check if we have no favourites but have some recommended.
    const recommendedFirst = !!(recommended.length && favouritesFirst === false);
    // We have nothing fallback to show all modules.
    const fallback = favouritesFirst === false && recommendedFirst === false;

    return {
        'default': data,
        favourites: favourites,
        recommended: recommended,
        favouritesFirst: favouritesFirst,
        recommendedFirst: recommendedFirst,
        fallback: fallback,
    };
};

/**
 * Given an object we want to prebuild a modal ready to store into a map
 *
 * @method buildModal
 * @param {Object} data The template data which contains arrays of modules
 * @return {Object} The modal for the calling section with everything already set up
 */
const buildModal = data => {
    return ModalFactory.create({
        type: ModalFactory.types.DEFAULT,
        title: getString('addresourceoractivity'),
        body: Templates.render('core_course/chooser', data),
        large: true,
        templateContext: {
            classes: 'modchooser'
        }
    });
};
