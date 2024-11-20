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
 * @copyright  2020 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as ChooserDialogue from 'core_course/local/activitychooser/dialogue';
import * as Repository from 'core_course/local/activitychooser/repository';
import selectors from 'core_course/local/activitychooser/selectors';
import CustomEvents from 'core/custom_interaction_events';
import * as Templates from 'core/templates';
import {getString} from 'core/str';
import Modal from 'core/modal';
import Pending from 'core/pending';

// Set up some JS module wide constants that can be added to in the future.

// Tab config options.
const ALLACTIVITIESRESOURCES = 0;
const ACTIVITIESRESOURCES = 2;
const ALLACTIVITIESRESOURCESREC = 3;
const ONLYALLREC = 4;
const ACTIVITIESRESOURCESREC = 5;


// Module types.
const ACTIVITY = 0;
const RESOURCE = 1;

let initialized = false;

/**
 * Set up the activity chooser.
 *
 * @method init
 * @param {Number} courseId Course ID to use later on in fetchModules()
 * @param {Object} chooserConfig Any PHP config settings that we may need to reference
 */
export const init = (courseId, chooserConfig) => {
    const pendingPromise = new Pending();

    registerListenerEvents(courseId, chooserConfig);

    pendingPromise.resolve();
};

/**
 * Once a selection has been made make the modal & module information and pass it along
 *
 * @method registerListenerEvents
 * @param {Number} courseId
 * @param {Object} chooserConfig Any PHP config settings that we may need to reference
 */
const registerListenerEvents = (courseId, chooserConfig) => {

    // Ensure we only add our listeners once.
    if (initialized) {
        return;
    }

    const events = [
        'click',
        CustomEvents.events.activate,
        CustomEvents.events.keyboardActivate
    ];

    const fetchModuleData = (() => {
        let innerPromises = new Map();

        return (sectionNum) => {
            if (innerPromises.has(sectionNum)) {
                return innerPromises.get(sectionNum);
            }

            innerPromises.set(
                sectionNum,
                new Promise((resolve) => {
                    resolve(Repository.activityModules(courseId, sectionNum));
                })
            );
            return innerPromises.get(sectionNum);
        };
    })();

    const fetchFooterData = (() => {
        let footerInnerPromise = null;

        return (sectionNum) => {
            if (!footerInnerPromise) {
                footerInnerPromise = new Promise((resolve) => {
                    resolve(Repository.fetchFooterData(courseId, sectionNum));
                });
            }

            return footerInnerPromise;
        };
    })();

    CustomEvents.define(document, events);

    // Display module chooser event listeners.
    events.forEach((event) => {
        document.addEventListener(event, async(e) => {
            if (e.target.closest(selectors.elements.sectionmodchooser)) {
                let caller;
                let sectionnum;
                // We need to know who called this.
                // Standard courses use the ID in the main section info.
                const sectionDiv = e.target.closest(selectors.elements.section);
                // Front page courses need some special handling.
                const button = e.target.closest(selectors.elements.sectionmodchooser);

                // If we don't have a section number use the fallback ID.
                // We always want the sectionDiv caller first as it keeps track of section number's after DnD changes.
                // The button attribute is always just a fallback for us as the section div is not always available.
                // A YUI change could be done maybe to only update the button attribute but we are going for minimal change here.
                if (sectionDiv !== null && sectionDiv.hasAttribute('data-number')) {
                    // We check for attributes just in case of outdated contrib course formats.
                    caller = sectionDiv;
                    sectionnum = sectionDiv.getAttribute('data-number');
                } else {
                    caller = button;

                    if (caller.hasAttribute('data-sectionid')) {
                        window.console.warn(
                            'The data-sectionid attribute has been deprecated. ' +
                            'Please update your code to use data-sectionnum instead.'
                        );
                        caller.setAttribute('data-sectionnum', caller.dataset.sectionid);
                    }
                    sectionnum = caller.dataset.sectionnum;
                }

                // We want to show the modal instantly but loading whilst waiting for our data.
                let bodyPromiseResolver;
                const bodyPromise = new Promise(resolve => {
                    bodyPromiseResolver = resolve;
                });

                const footerData = await fetchFooterData(sectionnum);
                const sectionModal = buildModal(bodyPromise, footerData);

                // Now we have a modal we should start fetching data.
                // If an error occurs while fetching the data, display the error within the modal.
                const data = await fetchModuleData(sectionnum).catch(async(e) => {
                    const errorTemplateData = {
                        'errormessage': e.message
                    };
                    bodyPromiseResolver(await Templates.render('core_course/local/activitychooser/error', errorTemplateData));
                });

                // Early return if there is no module data.
                if (!data) {
                    return;
                }

                // Apply the section num to all the module instance links.
                const builtModuleData = sectionMapper(
                    data,
                    sectionnum,
                    caller.dataset.sectionreturnnum,
                    caller.dataset.beforemod
                );

                ChooserDialogue.displayChooser(
                    sectionModal,
                    builtModuleData,
                    partiallyAppliedFavouriteManager(data, sectionnum),
                    footerData,
                );

                bodyPromiseResolver(await Templates.render(
                    'core_course/activitychooser',
                    templateDataBuilder(builtModuleData, chooserConfig)
                ));
            }
        });
    });

    initialized = true;
};

/**
 * Given the web service data and an ID we want to make a deep copy
 * of the WS data then add on the section num to the addoption URL
 *
 * @method sectionMapper
 * @param {Object} webServiceData Our original data from the Web service call
 * @param {Number} num The number of the section we need to append to the links
 * @param {Number|null} sectionreturnnum The number of the section return we need to append to the links
 * @param {Number|null} beforemod The ID of the cm we need to append to the links
 * @return {Array} [modules] with URL's built
 */
const sectionMapper = (webServiceData, num, sectionreturnnum, beforemod) => {
    // We need to take a fresh deep copy of the original data as an object is a reference type.
    const newData = JSON.parse(JSON.stringify(webServiceData));
    newData.content_items.forEach((module) => {
        module.link += '&section=' + num + '&beforemod=' + (beforemod ?? 0);
        if (sectionreturnnum) {
            module.link += '&sr=' + sectionreturnnum;
        }
    });
    return newData.content_items;
};

/**
 * Given an array of modules we want to figure out where & how to place them into our template object
 *
 * @method templateDataBuilder
 * @param {Array} data our modules to manipulate into a Templatable object
 * @param {Object} chooserConfig Any PHP config settings that we may need to reference
 * @return {Object} Our built object ready to render out
 */
const templateDataBuilder = (data, chooserConfig) => {
    // Setup of various bits and pieces we need to mutate before throwing it to the wolves.
    let activities = [];
    let resources = [];
    let showAll = true;
    let showActivities = false;
    let showResources = false;

    // Tab mode can be the following [All, Resources & Activities, All & Activities & Resources].
    const tabMode = parseInt(chooserConfig.tabmode);

    // Filter the incoming data to find favourite & recommended modules.
    const favourites = data.filter(mod => mod.favourite === true);
    const recommended = data.filter(mod => mod.recommended === true);

    // Whether the activities and resources tabs should be displayed or not.
    const showActivitiesAndResources = (tabMode) => {
        const acceptableModes = [
            ALLACTIVITIESRESOURCES,
            ALLACTIVITIESRESOURCESREC,
            ACTIVITIESRESOURCES,
            ACTIVITIESRESOURCESREC,
        ];

        return acceptableModes.indexOf(tabMode) !== -1;
    };

    // These modes need Activity & Resource tabs.
    if (showActivitiesAndResources(tabMode)) {
        // Filter the incoming data to find activities then resources.
        activities = data.filter(mod => mod.archetype === ACTIVITY);
        resources = data.filter(mod => mod.archetype === RESOURCE);
        showActivities = true;
        showResources = true;

        // We want all of the previous information but no 'All' tab.
        if (tabMode === ACTIVITIESRESOURCES || tabMode === ACTIVITIESRESOURCESREC) {
            showAll = false;
        }
    }

    const recommendedBeforeTabs = [
        ALLACTIVITIESRESOURCESREC,
        ONLYALLREC,
        ACTIVITIESRESOURCESREC,
    ];
    // Whether the recommended tab should be displayed before the All/Activities/Resources tabs.
    const recommendedBeginning = recommendedBeforeTabs.indexOf(tabMode) !== -1;

    // Given the results of the above filters lets figure out what tab to set active.
    // We have some favourites.
    const favouritesFirst = !!favourites.length;
    const recommendedFirst = favouritesFirst === false && recommendedBeginning === true && !!recommended.length;
    // We are in tabMode 2 without any favourites.
    const activitiesFirst = showAll === false && favouritesFirst === false && recommendedFirst === false;
    // We have nothing fallback to show all modules.
    const fallback = showAll === true && favouritesFirst === false && recommendedFirst === false;

    return {
        'default': data,
        showAll: showAll,
        activities: activities,
        showActivities: showActivities,
        activitiesFirst: activitiesFirst,
        resources: resources,
        showResources: showResources,
        favourites: favourites,
        recommended: recommended,
        recommendedFirst: recommendedFirst,
        recommendedBeginning: recommendedBeginning,
        favouritesFirst: favouritesFirst,
        fallback: fallback,
    };
};

/**
 * Given an object we want to build a modal ready to show
 *
 * @method buildModal
 * @param {Promise} body
 * @param {String|Boolean} footer Either a footer to add or nothing
 * @return {Object} The modal ready to display immediately and render body in later.
 */
const buildModal = (body, footer) => Modal.create({
    body,
    title: getString('addresourceoractivity'),
    footer: footer.customfootertemplate,
    large: true,
    scrollable: false,
    templateContext: {
        classes: 'modchooser'
    },
    show: true,
});

/**
 * A small helper function to handle the case where there are no more favourites
 * and we need to mess a bit with the available tabs in the chooser
 *
 * @method nullFavouriteDomManager
 * @param {HTMLElement} favouriteTabNav Dom node of the favourite tab nav
 * @param {HTMLElement} modalBody Our current modals' body
 */
const nullFavouriteDomManager = (favouriteTabNav, modalBody) => {
    favouriteTabNav.tabIndex = -1;
    favouriteTabNav.classList.add('d-none');
    // Need to set active to an available tab.
    if (favouriteTabNav.classList.contains('active')) {
        favouriteTabNav.classList.remove('active');
        favouriteTabNav.setAttribute('aria-selected', 'false');
        const favouriteTab = modalBody.querySelector(selectors.regions.favouriteTab);
        favouriteTab.classList.remove('active');
        const defaultTabNav = modalBody.querySelector(selectors.regions.defaultTabNav);
        const activitiesTabNav = modalBody.querySelector(selectors.regions.activityTabNav);
        if (defaultTabNav.classList.contains('d-none') === false) {
            defaultTabNav.classList.add('active');
            defaultTabNav.setAttribute('aria-selected', 'true');
            defaultTabNav.tabIndex = 0;
            defaultTabNav.focus();
            const defaultTab = modalBody.querySelector(selectors.regions.defaultTab);
            defaultTab.classList.add('active');
        } else {
            activitiesTabNav.classList.add('active');
            activitiesTabNav.setAttribute('aria-selected', 'true');
            activitiesTabNav.tabIndex = 0;
            activitiesTabNav.focus();
            const activitiesTab = modalBody.querySelector(selectors.regions.activityTab);
            activitiesTab.classList.add('active');
        }

    }
};

/**
 * Export a curried function where the builtModules has been applied.
 * We have our array of modules so we can rerender the favourites area and have all of the items sorted.
 *
 * @method partiallyAppliedFavouriteManager
 * @param {Array} moduleData This is our raw WS data that we need to manipulate
 * @param {Number} sectionnum We need this to add the sectionnum to the URL's in the faves area after rerender
 * @return {Function} partially applied function so we can manipulate DOM nodes easily & update our internal array
 */
const partiallyAppliedFavouriteManager = (moduleData, sectionnum) => {
    /**
     * Curried function that is being returned.
     *
     * @param {String} internal Internal name of the module to manage
     * @param {Boolean} favourite Is the caller adding a favourite or removing one?
     * @param {HTMLElement} modalBody What we need to update whilst we are here
     */
    return async(internal, favourite, modalBody) => {
        const favouriteArea = modalBody.querySelector(selectors.render.favourites);

        // eslint-disable-next-line max-len
        const favouriteButtons = modalBody.querySelectorAll(`[data-internal="${internal}"] ${selectors.actions.optionActions.manageFavourite}`);
        const favouriteTabNav = modalBody.querySelector(selectors.regions.favouriteTabNav);
        const result = moduleData.content_items.find(({name}) => name === internal);
        const newFaves = {};
        if (result) {
            if (favourite) {
                result.favourite = true;

                // eslint-disable-next-line camelcase
                newFaves.content_items = moduleData.content_items.filter(mod => mod.favourite === true);

                const builtFaves = sectionMapper(newFaves, sectionnum);

                const {html, js} = await Templates.renderForPromise('core_course/local/activitychooser/favourites',
                    {favourites: builtFaves});

                await Templates.replaceNodeContents(favouriteArea, html, js);

                Array.from(favouriteButtons).forEach((element) => {
                    element.classList.remove('text-muted');
                    element.classList.add('text-primary');
                    element.dataset.favourited = 'true';
                    element.setAttribute('aria-pressed', true);
                    element.firstElementChild.classList.remove('fa-star-o');
                    element.firstElementChild.classList.add('fa-star');
                });

                favouriteTabNav.classList.remove('d-none');
            } else {
                result.favourite = false;

                const nodeToRemove = favouriteArea.querySelector(`[data-internal="${internal}"]`);

                nodeToRemove.parentNode.removeChild(nodeToRemove);

                Array.from(favouriteButtons).forEach((element) => {
                    element.classList.add('text-muted');
                    element.classList.remove('text-primary');
                    element.dataset.favourited = 'false';
                    element.setAttribute('aria-pressed', false);
                    element.firstElementChild.classList.remove('fa-star');
                    element.firstElementChild.classList.add('fa-star-o');
                });
                const newFaves = moduleData.content_items.filter(mod => mod.favourite === true);

                if (newFaves.length === 0) {
                    nullFavouriteDomManager(favouriteTabNav, modalBody);
                }
            }
        }
    };
};
