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
 * A type of dialogue used as for choosing options.
 *
 * @module     core_course/local/chooser/dialogue
 * @copyright  2019 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import * as ModalEvents from 'core/modal_events';
import selectors from 'core_course/local/activitychooser/selectors';
import * as Templates from 'core/templates';
import {end, arrowLeft, arrowRight, home, enter, space} from 'core/key_codes';
import {addIconToContainer} from 'core/loadingicon';
import * as Repository from 'core_course/local/activitychooser/repository';
import Notification from 'core/notification';
import {debounce} from 'core/utils';
const getPlugin = pluginName => import(pluginName);

/**
 * Given an event from the main module 'page' navigate to it's help section via a carousel.
 *
 * @method showModuleHelp
 * @param {jQuery} carousel Our initialized carousel to manipulate
 * @param {Object} moduleData Data of the module to carousel to
 * @param {jQuery} modal We need to figure out if the current modal has a footer.
 */
const showModuleHelp = (carousel, moduleData, modal = null) => {
    // If we have a real footer then we need to change temporarily.
    if (modal !== null && moduleData.showFooter === true) {
        modal.setFooter(Templates.render('core_course/local/activitychooser/footer_partial', moduleData));
    }
    const help = carousel.find(selectors.regions.help)[0];
    help.innerHTML = '';
    help.classList.add('m-auto');

    // Add a spinner.
    const spinnerPromise = addIconToContainer(help);

    // Used later...
    let transitionPromiseResolver = null;
    const transitionPromise = new Promise(resolve => {
        transitionPromiseResolver = resolve;
    });

    // Build up the html & js ready to place into the help section.
    const contentPromise = Templates.renderForPromise('core_course/local/activitychooser/help', moduleData);

    // Wait for the content to be ready, and for the transition to be complet.
    Promise.all([contentPromise, spinnerPromise, transitionPromise])
        .then(([{html, js}]) => Templates.replaceNodeContents(help, html, js))
        .then(() => {
            help.querySelector(selectors.regions.chooserSummary.header).focus();
            return help;
        })
        .catch(Notification.exception);

    // Move to the next slide, and resolve the transition promise when it's done.
    carousel.one('slid.bs.carousel', () => {
        transitionPromiseResolver();
    });
    // Trigger the transition between 'pages'.
    carousel.carousel('next');
};

/**
 * Given a user wants to change the favourite state of a module we either add or remove the status.
 * We also propergate this change across our map of modals.
 *
 * @method manageFavouriteState
 * @param {HTMLElement} modalBody The DOM node of the modal to manipulate
 * @param {HTMLElement} caller
 * @param {Function} partialFavourite Partially applied function we need to manage favourite status
 */
const manageFavouriteState = async(modalBody, caller, partialFavourite) => {
    const isFavourite = caller.dataset.favourited;
    const id = caller.dataset.id;
    const name = caller.dataset.name;
    const internal = caller.dataset.internal;
    // Switch on fave or not.
    if (isFavourite === 'true') {
        await Repository.unfavouriteModule(name, id);

        partialFavourite(internal, false, modalBody);
    } else {
        await Repository.favouriteModule(name, id);

        partialFavourite(internal, true, modalBody);
    }

};

/**
 * Register chooser related event listeners.
 *
 * @method registerListenerEvents
 * @param {Promise} modal Our modal that we are working with
 * @param {Map} mappedModules A map of all of the modules we are working with with K: mod_name V: {Object}
 * @param {Function} partialFavourite Partially applied function we need to manage favourite status
 * @param {Object} footerData Our base footer object.
 */
const registerListenerEvents = (modal, mappedModules, partialFavourite, footerData) => {
    const bodyClickListener = async(e) => {
        if (e.target.closest(selectors.actions.optionActions.showSummary)) {
            const carousel = $(modal.getBody()[0].querySelector(selectors.regions.carousel));

            const module = e.target.closest(selectors.regions.chooserOption.container);
            const moduleName = module.dataset.modname;
            const moduleData = mappedModules.get(moduleName);
            // We need to know if the overall modal has a footer so we know when to show a real / vs fake footer.
            moduleData.showFooter = modal.hasFooterContent();
            showModuleHelp(carousel, moduleData, modal);
        }

        if (e.target.closest(selectors.actions.optionActions.manageFavourite)) {
            const caller = e.target.closest(selectors.actions.optionActions.manageFavourite);
            await manageFavouriteState(modal.getBody()[0], caller, partialFavourite);
            const activeSectionId = modal.getBody()[0].querySelector(selectors.elements.activetab).getAttribute("href");
            const sectionChooserOptions = modal.getBody()[0]
                .querySelector(selectors.regions.getSectionChooserOptions(activeSectionId));
            const firstChooserOption = sectionChooserOptions
                .querySelector(selectors.regions.chooserOption.container);
            toggleFocusableChooserOption(firstChooserOption, true);
            initChooserOptionsKeyboardNavigation(modal.getBody()[0], mappedModules, sectionChooserOptions, modal);
        }

        // From the help screen go back to the module overview.
        if (e.target.matches(selectors.actions.closeOption)) {
            const carousel = $(modal.getBody()[0].querySelector(selectors.regions.carousel));

            // Trigger the transition between 'pages'.
            carousel.carousel('prev');
            carousel.on('slid.bs.carousel', () => {
                const allModules = modal.getBody()[0].querySelector(selectors.regions.modules);
                const caller = allModules.querySelector(selectors.regions.getModuleSelector(e.target.dataset.modname));
                caller.focus();
            });
        }

        // The "clear search" button is triggered.
        if (e.target.closest(selectors.actions.clearSearch)) {
            // Clear the entered search query in the search bar and hide the search results container.
            const searchInput = modal.getBody()[0].querySelector(selectors.actions.search);
            searchInput.value = "";
            searchInput.focus();
            toggleSearchResultsView(modal, mappedModules, searchInput.value);
        }
    };

    // We essentially have two types of footer.
    // A fake one that is handled within the template for chooser_help and then all of the stuff for
    // modal.footer. We need to ensure we know exactly what type of footer we are using so we know what we
    // need to manage. The below code handles a real footer going to a mnet carousel item.
    const footerClickListener = async(e) => {
        if (footerData.footer === true) {
            const footerjs = await getPlugin(footerData.customfooterjs);
            await footerjs.footerClickListener(e, footerData, modal);
        }
    };

    modal.getBodyPromise()

    // The return value of getBodyPromise is a jquery object containing the body NodeElement.
    .then(body => body[0])

    // Set up the carousel.
    .then(body => {
        $(body.querySelector(selectors.regions.carousel))
            .carousel({
                interval: false,
                pause: true,
                keyboard: false
            });

        return body;
    })

    // Add the listener for clicks on the body.
    .then(body => {
        body.addEventListener('click', bodyClickListener);
        return body;
    })

    // Add a listener for an input change in the activity chooser's search bar.
    .then(body => {
        const searchInput = body.querySelector(selectors.actions.search);
        // The search input is triggered.
        searchInput.addEventListener('input', debounce(() => {
            // Display the search results.
            toggleSearchResultsView(modal, mappedModules, searchInput.value);
        }, 300));
        return body;
    })

    // Register event listeners related to the keyboard navigation controls.
    .then(body => {
        // Get the active chooser options section.
        const activeSectionId = body.querySelector(selectors.elements.activetab).getAttribute("href");
        const sectionChooserOptions = body.querySelector(selectors.regions.getSectionChooserOptions(activeSectionId));
        const firstChooserOption = sectionChooserOptions.querySelector(selectors.regions.chooserOption.container);

        toggleFocusableChooserOption(firstChooserOption, true);
        initChooserOptionsKeyboardNavigation(body, mappedModules, sectionChooserOptions, modal);

        return body;
    })
    .catch();

    modal.getFooterPromise()

    // The return value of getBodyPromise is a jquery object containing the body NodeElement.
    .then(footer => footer[0])
    // Add the listener for clicks on the footer.
    .then(footer => {
        footer.addEventListener('click', footerClickListener);
        return footer;
    })
    .catch();
};

/**
 * Initialise the keyboard navigation controls for the chooser options.
 *
 * @method initChooserOptionsKeyboardNavigation
 * @param {HTMLElement} body Our modal that we are working with
 * @param {Map} mappedModules A map of all of the modules we are working with with K: mod_name V: {Object}
 * @param {HTMLElement} chooserOptionsContainer The section that contains the chooser items
 * @param {Object} modal Our created modal for the section
 */
const initChooserOptionsKeyboardNavigation = (body, mappedModules, chooserOptionsContainer, modal = null) => {
    const chooserOptions = chooserOptionsContainer.querySelectorAll(selectors.regions.chooserOption.container);

    Array.from(chooserOptions).forEach((element) => {
        return element.addEventListener('keydown', (e) => {

            // Check for enter/ space triggers for showing the help.
            if (e.keyCode === enter || e.keyCode === space) {
                if (e.target.matches(selectors.actions.optionActions.showSummary)) {
                    e.preventDefault();
                    const module = e.target.closest(selectors.regions.chooserOption.container);
                    const moduleName = module.dataset.modname;
                    const moduleData = mappedModules.get(moduleName);
                    const carousel = $(body.querySelector(selectors.regions.carousel));
                    carousel.carousel({
                        interval: false,
                        pause: true,
                        keyboard: false
                    });

                    // We need to know if the overall modal has a footer so we know when to show a real / vs fake footer.
                    moduleData.showFooter = modal.hasFooterContent();
                    showModuleHelp(carousel, moduleData, modal);
                }
            }

            // Next.
            if (e.keyCode === arrowRight) {
                e.preventDefault();
                const currentOption = e.target.closest(selectors.regions.chooserOption.container);
                const nextOption = currentOption.nextElementSibling;
                const firstOption = chooserOptionsContainer.firstElementChild;
                const toFocusOption = clickErrorHandler(nextOption, firstOption);
                focusChooserOption(toFocusOption, currentOption);
            }

            // Previous.
            if (e.keyCode === arrowLeft) {
                e.preventDefault();
                const currentOption = e.target.closest(selectors.regions.chooserOption.container);
                const previousOption = currentOption.previousElementSibling;
                const lastOption = chooserOptionsContainer.lastElementChild;
                const toFocusOption = clickErrorHandler(previousOption, lastOption);
                focusChooserOption(toFocusOption, currentOption);
            }

            if (e.keyCode === home) {
                e.preventDefault();
                const currentOption = e.target.closest(selectors.regions.chooserOption.container);
                const firstOption = chooserOptionsContainer.firstElementChild;
                focusChooserOption(firstOption, currentOption);
            }

            if (e.keyCode === end) {
                e.preventDefault();
                const currentOption = e.target.closest(selectors.regions.chooserOption.container);
                const lastOption = chooserOptionsContainer.lastElementChild;
                focusChooserOption(lastOption, currentOption);
            }
        });
    });
};

/**
 * Focus on a chooser option element and remove the previous chooser element from the focus order
 *
 * @method focusChooserOption
 * @param {HTMLElement} currentChooserOption The current chooser option element that we want to focus
 * @param {HTMLElement|null} previousChooserOption The previous focused option element
 */
const focusChooserOption = (currentChooserOption, previousChooserOption = null) => {
    if (previousChooserOption !== null) {
        toggleFocusableChooserOption(previousChooserOption, false);
    }

    toggleFocusableChooserOption(currentChooserOption, true);
    currentChooserOption.focus();
};

/**
 * Add or remove a chooser option from the focus order.
 *
 * @method toggleFocusableChooserOption
 * @param {HTMLElement} chooserOption The chooser option element which should be added or removed from the focus order
 * @param {Boolean} isFocusable Whether the chooser element is focusable or not
 */
const toggleFocusableChooserOption = (chooserOption, isFocusable) => {
    const chooserOptionLink = chooserOption.querySelector(selectors.actions.addChooser);
    const chooserOptionHelp = chooserOption.querySelector(selectors.actions.optionActions.showSummary);
    const chooserOptionFavourite = chooserOption.querySelector(selectors.actions.optionActions.manageFavourite);

    if (isFocusable) {
        // Set tabindex to 0 to add current chooser option element to the focus order.
        chooserOption.tabIndex = 0;
        chooserOptionLink.tabIndex = 0;
        chooserOptionHelp.tabIndex = 0;
        chooserOptionFavourite.tabIndex = 0;
    } else {
        // Set tabindex to -1 to remove the previous chooser option element from the focus order.
        chooserOption.tabIndex = -1;
        chooserOptionLink.tabIndex = -1;
        chooserOptionHelp.tabIndex = -1;
        chooserOptionFavourite.tabIndex = -1;
    }
};

/**
 * Small error handling function to make sure the navigated to object exists
 *
 * @method clickErrorHandler
 * @param {HTMLElement} item What we want to check exists
 * @param {HTMLElement} fallback If we dont match anything fallback the focus
 * @return {HTMLElement}
 */
const clickErrorHandler = (item, fallback) => {
    if (item !== null) {
        return item;
    } else {
        return fallback;
    }
};

/**
 * Render the search results in a defined container
 *
 * @method renderSearchResults
 * @param {HTMLElement} searchResultsContainer The container where the data should be rendered
 * @param {Object} searchResultsData Data containing the module items that satisfy the search criteria
 */
const renderSearchResults = async(searchResultsContainer, searchResultsData) => {
    const templateData = {
        'searchresultsnumber': searchResultsData.length,
        'searchresults': searchResultsData
    };
    // Build up the html & js ready to place into the help section.
    const {html, js} = await Templates.renderForPromise('core_course/local/activitychooser/search_results', templateData);
    await Templates.replaceNodeContents(searchResultsContainer, html, js);
};

/**
 * Toggle (display/hide) the search results depending on the value of the search query
 *
 * @method toggleSearchResultsView
 * @param {Object} modal Our created modal for the section
 * @param {Map} mappedModules A map of all of the modules we are working with with K: mod_name V: {Object}
 * @param {String} searchQuery The search query
 */
const toggleSearchResultsView = async(modal, mappedModules, searchQuery) => {
    const modalBody = modal.getBody()[0];
    const searchResultsContainer = modalBody.querySelector(selectors.regions.searchResults);
    const chooserContainer = modalBody.querySelector(selectors.regions.chooser);
    const clearSearchButton = modalBody.querySelector(selectors.actions.clearSearch);

    if (searchQuery.length > 0) { // Search query is present.
        const searchResultsData = searchModules(mappedModules, searchQuery);
        await renderSearchResults(searchResultsContainer, searchResultsData);
        const searchResultItemsContainer = searchResultsContainer.querySelector(selectors.regions.searchResultItems);
        const firstSearchResultItem = searchResultItemsContainer.querySelector(selectors.regions.chooserOption.container);
        if (firstSearchResultItem) {
            // Set the first result item to be focusable.
            toggleFocusableChooserOption(firstSearchResultItem, true);
            // Register keyboard events on the created search result items.
            initChooserOptionsKeyboardNavigation(modalBody, mappedModules, searchResultItemsContainer, modal);
        }
        // Display the "clear" search button in the activity chooser search bar.
        clearSearchButton.classList.remove('d-none');
        // Hide the default chooser options container.
        chooserContainer.setAttribute('hidden', 'hidden');
        // Display the search results container.
        searchResultsContainer.removeAttribute('hidden');
    } else { // Search query is not present.
        // Hide the "clear" search button in the activity chooser search bar.
        clearSearchButton.classList.add('d-none');
        // Hide the search results container.
        searchResultsContainer.setAttribute('hidden', 'hidden');
        // Display the default chooser options container.
        chooserContainer.removeAttribute('hidden');
    }
};

/**
 * Return the list of modules which have a name or description that matches the given search term.
 *
 * @method searchModules
 * @param {Array} modules List of available modules
 * @param {String} searchTerm The search term to match
 * @return {Array}
 */
const searchModules = (modules, searchTerm) => {
    if (searchTerm === '') {
        return modules;
    }
    searchTerm = searchTerm.toLowerCase();
    const searchResults = [];
    modules.forEach((activity) => {
        const activityName = activity.title.toLowerCase();
        const activityDesc = activity.help.toLowerCase();
        if (activityName.includes(searchTerm) || activityDesc.includes(searchTerm)) {
            searchResults.push(activity);
        }
    });

    return searchResults;
};

/**
 * Set up our tabindex information across the chooser.
 *
 * @method setupKeyboardAccessibility
 * @param {Promise} modal Our created modal for the section
 * @param {Map} mappedModules A map of all of the built module information
 */
const setupKeyboardAccessibility = (modal, mappedModules) => {
    modal.getModal()[0].tabIndex = -1;

    modal.getBodyPromise().then(body => {
        $(selectors.elements.tab).on('shown.bs.tab', (e) => {
            const activeSectionId = e.target.getAttribute("href");
            const activeSectionChooserOptions = body[0]
                .querySelector(selectors.regions.getSectionChooserOptions(activeSectionId));
            const firstChooserOption = activeSectionChooserOptions
                .querySelector(selectors.regions.chooserOption.container);
            const prevActiveSectionId = e.relatedTarget.getAttribute("href");
            const prevActiveSectionChooserOptions = body[0]
                .querySelector(selectors.regions.getSectionChooserOptions(prevActiveSectionId));

            // Disable the focus of every chooser option in the previous active section.
            disableFocusAllChooserOptions(prevActiveSectionChooserOptions);
            // Enable the focus of the first chooser option in the current active section.
            toggleFocusableChooserOption(firstChooserOption, true);
            initChooserOptionsKeyboardNavigation(body[0], mappedModules, activeSectionChooserOptions, modal);
        });
        return;
    }).catch(Notification.exception);
};

/**
 * Disable the focus of all chooser options in a specific container (section).
 *
 * @method disableFocusAllChooserOptions
 * @param {HTMLElement} sectionChooserOptions The section that contains the chooser items
 */
const disableFocusAllChooserOptions = (sectionChooserOptions) => {
    const allChooserOptions = sectionChooserOptions.querySelectorAll(selectors.regions.chooserOption.container);
    allChooserOptions.forEach((chooserOption) => {
        toggleFocusableChooserOption(chooserOption, false);
    });
};

/**
 * Display the module chooser.
 *
 * @method displayChooser
 * @param {Promise} modalPromise Our created modal for the section
 * @param {Array} sectionModules An array of all of the built module information
 * @param {Function} partialFavourite Partially applied function we need to manage favourite status
 * @param {Object} footerData Our base footer object.
 */
export const displayChooser = (modalPromise, sectionModules, partialFavourite, footerData) => {
    // Make a map so we can quickly fetch a specific module's object for either rendering or searching.
    const mappedModules = new Map();
    sectionModules.forEach((module) => {
        mappedModules.set(module.componentname + '_' + module.link, module);
    });

    // Register event listeners.
    modalPromise.then(modal => {
        registerListenerEvents(modal, mappedModules, partialFavourite, footerData);

        // We want to focus on the first chooser option element as soon as the modal is opened.
        setupKeyboardAccessibility(modal, mappedModules);

        // We want to focus on the action select when the dialog is closed.
        modal.getRoot().on(ModalEvents.hidden, () => {
            modal.destroy();
        });

        return modal;
    }).catch();
};
