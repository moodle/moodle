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
 * @module     core_courseformat/local/activitychooser/dialogue
 * @copyright  2019 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// import {addIconToContainer} from 'core/loadingicon';
import $ from 'jquery';
import {debounce} from 'core/utils';
import CustomEvents from 'core/custom_interaction_events';
import DialogueDom from 'core_courseformat/local/activitychooser/dialoguedom';
import {enter, space} from 'core/key_codes';
import Exporter from 'core_courseformat/local/activitychooser/exporter';
import {getFirst} from 'core/normalise';
import {getString} from 'core/str';
import Modal from 'core/modal';
import * as ModalEvents from 'core/modal_events';
import Notification from 'core/notification';
import * as Repository from 'core_courseformat/local/activitychooser/repository';
import selectors from 'core_courseformat/local/activitychooser/selectors';
import * as Templates from 'core/templates';
const getPlugin = pluginName => import(pluginName);

/**
 * Display the activity chooser modal.
 *
 * @method displayActivityChooser
 * @param {Promise} footerDataPromise Promise for the footer data.
 * @param {Promise} modulesDataPromise Promise for the modules data.
 */
export async function displayActivityChooserModal(
    footerDataPromise,
    modulesDataPromise,
) {
    // We want to show the modal instantly but loading whilst waiting for our data.
    let bodyPromiseResolver;
    const bodyPromise = new Promise(resolve => {
        bodyPromiseResolver = resolve;
    });

    const exporter = new Exporter();

    const footerData = await footerDataPromise;

    const footerPromise = Templates.render(
        'core_courseformat/local/activitychooser/footer',
        exporter.getFooterData(footerData),
    );

    const sectionModal = Modal.create({
        body: bodyPromise,
        title: getString('addresourceoractivity'),
        footer: footerPromise,
        large: true,
        scrollable: false,
        templateContext: {
            classes: 'modchooser'
        },
        show: true,
    });

    try {
        const modulesData = await modulesDataPromise;

        if (!modulesData) {
            return;
        }

        const modal = await sectionModal;
        new ActivityChooserDialogue(modal, modulesData, footerData);

        const templateData = await exporter.getModChooserTemplateData(modulesData);
        bodyPromiseResolver(await Templates.render('core_courseformat/activitychooser', templateData));
    } catch (error) {
        const errorTemplateData = {
            'errormessage': error.message
        };
        bodyPromiseResolver(
            await Templates.render('core_courseformat/local/activitychooser/error', errorTemplateData)
        );
        return;
    }
}

/**
 * Display the module chooser.
 *
 * @deprecated since Moodle 5.1
 * @todo Remove the method in Moodle 6.0 (MDL-85655).
 * @method displayChooser
 * @param {Promise} modalPromise Our created modal for the section
 * @param {Array} sectionModules An array of all of the built module information
 * @param {Function} partialFavourite Partially applied function we need to manage favourite status
 * @param {Object} footerData Our base footer object.
 */
export const displayChooser = (modalPromise, sectionModules, partialFavourite, footerData) => {
    window.console.warn(
        'The displayChooser function is deprecated. ' +
        'Please displayActivityChooserModal instead.'
    );

    // Register event listeners.
    modalPromise.then(modal => {
        new ActivityChooserDialogue(modal, sectionModules, footerData);
        return modal;
    }).catch(Notification.exception);
};

/**
 * Activity Chooser Dialogue class.
 *
 * @private
 */
class ActivityChooserDialogue {
    /**
     * Constructor for the ActivityChooserDialogue class.
     * @param {Modal} modal The modal object.
     * @param {Object} modulesData The data for the modules.
     * @param {Object} footerData The data for the footer.
     */
    constructor(modal, modulesData, footerData) {
        this.modal = modal;
        this.dialogueDom = null; // We cannot init until we have the modal body loaded.
        this.footerData = footerData;
        this.exporter = new Exporter();
        this.selectedModule = null;
        // This attribute marks when the tab content is dirty and needs to be refreshed when the user changes the tab.
        // We don't want the content to be updated while the user is managing their favourites.
        this.isFavouriteTabDirty = false;
        // Make a map so we can quickly fetch a specific module's object for either rendering or searching.
        this.mappedModules = new Map();
        modulesData.forEach((module) => {
            this.mappedModules.set(module.componentname + '_' + module.link, module);
        });
        this.init();
    }

    /**
     * Initialise the activity chooser dialogue.
     *
     * @return {Promise} A promise that resolves when the modal is ready.
     */
    async init() {
        await this.modal.getBodyPromise();
        await this.modal.getFooterPromise();
        this.dialogueDom = new DialogueDom(this, this.modal, this.exporter);
        this.registerModalListenerEvents();
        this.setupKeyboardAccessibility();
        // We want to focus on the action select when the dialog is closed.
        this.modal.getRoot().on(ModalEvents.hidden, () => {
            this.modal.destroy();
        });
        // When modal is open the add button should be disabled because there is no activity selected.
        this.dialogueDom.unmarkAllChooserOptionAsSelected();
    }

    /**
     * Register chooser related event listeners.
     *
     * @returns {Promise} A promise that resolves when events are registered
     */
    async registerModalListenerEvents() {
        const modalRoot = getFirst(this.modal.getRoot());

        // Changing the tab should cancel any active search.
        modalRoot.addEventListener(
            'shown.bs.tab',
            (event) => {
                this.dialogueDom.unmarkAllChooserOptionAsSelected();
                // The all tab has the search result, so we do not want to clear the search input.
                if (event.target.closest(selectors.regions.allTabNav)) {
                    return;
                }
                const searchInput = this.dialogueDom.getSearchInputElement();
                if (searchInput.value.length > 0) {
                    searchInput.value = "";
                    this.toggleSearchResultsView(searchInput.value);
                }
            },
        );

        // Add the listener for clicks on the full modal.
        modalRoot.addEventListener(
            'click',
            this.handleModalClick.bind(this),
        );
        modalRoot.addEventListener(
            'dblclick',
            this.handleModalDoubleClick.bind(this),
        );

        // Add a listener for an input change in the activity chooser's search bar.
        const searchInput = this.dialogueDom.getSearchInputElement();
        searchInput.addEventListener(
            'input',
            debounce(
                () => {
                    this.toggleSearchResultsView(searchInput.value);
                },
                300,
                {pending: true},
            ),
        );

        this.dialogueDom.initBootstrapComponents();

        // Handle focus when a new tab is shown.
        modalRoot.addEventListener('shown.bs.tab', (event) => {
            if (event.relatedTarget) {
                this.dialogueDom.disableFocusAllChooserOptions(event.relatedTarget);
            }
            this.dialogueDom.initActiveTabNavigation();
        });

        // Update the favourite tab content when the user changes the tab.
        modalRoot.addEventListener('shown.bs.tab', () => {
            if (this.isFavouriteTabDirty && !this.dialogueDom.isFavoutiteTabActive()) {
                this.refreshFavouritesTabContent();
            }
        });

        this.dialogueDom.initActiveTabNavigation();

        const modalFooter = getFirst(await this.modal.getFooterPromise());

        // Add the listener for clicks on the footer.
        modalFooter.addEventListener(
            'click',
            this.handleFooterClick.bind(this),
        );

        // Adapt modal footer depending on the displayed carousel page.
        modalRoot.addEventListener('slide.bs.carousel', (event) => {
            if (event.to === undefined) {
                return;
            }
            // The boostrap carousel event.to contains the index of the newly active item.
            // The zero index is the chooser options, the first index is the module help,
            // any other index are custom footer pages.
            this.dialogueDom.toggleActiveFooter(event.to === 0);
            this.dialogueDom.toggleBackButton(event.to !== 0);
            this.dialogueDom.toggleAddButton(event.to < 2);
        });
    }

    /**
     * Handle the click event on the footer of the modal.
     *
     * @param {Object} event The event object
     * @return {Promise} A promise that resolves when the event is handled
     */
    async handleFooterClick(event) {
        if (event.target.closest(selectors.actions.addSelectedChooserOption)) {
            this.submitAddSelectedModule();
        }

        if (
            event.target.closest(selectors.regions.activeFooter)
            && this.footerData.footer === true
        ) {
            const footerjs = await getPlugin(this.footerData.customfooterjs);
            await footerjs.footerClickListener(event, this.footerData, this.modal);
        }
    }

    /**
     * Modal click handler.
     *
     * @param {Object} event The event object
     * @return {Promise} A promise that resolves when the event is handled
     */
    async handleModalClick(event) {
        const target = event.target;

        if (target.closest(selectors.actions.optionActions.showSummary)) {
            event.preventDefault();
            this.handleShowSummary(target);
            return;
        }

        if (target.closest(selectors.actions.displayCategory)) {
            event.preventDefault();
            this.handleDisplayCategory(target);
            return;
        }

        if (target.closest(selectors.actions.optionActions.manageFavourite)) {
            event.preventDefault();
            await this.handleFavouriteClick(target);
            return;
        }

        // From the help screen go back to the module overview.
        if (target.matches(selectors.actions.closeOption)) {
            event.preventDefault();
            this.dialogueDom.hideModuleHelp(target.dataset.modname);
            return;
        }

        // The "clear search" button is triggered.
        if (target.closest(selectors.actions.clearSearch)) {
            this.handleClearSearch();
            return;
        }

        if (target.closest(selectors.regions.chooserOption.info)) {
            event.preventDefault();
            this.handleOptionSelection(target);
            return;
        }
    }

    /**
     * Handle the double click event on the modal.
     * @param {Object} event The event object
     */
    handleModalDoubleClick(event) {
        const option = this.dialogueDom.getClosestChooserOption(event.target);
        if (option !== null) {
            this.submitAddSelectedModule(this.selectedModule);
        }
    }

    /**
     * Show the summary of a module when the user clicks on the "show summary" button.
     *
     * @param {HTMLElement} target The target element that triggered the event
     */
    handleShowSummary(target) {
        const module = this.dialogueDom.getClosestChooserOption(target);
        const moduleName = module.dataset.modname;
        const moduleData = this.mappedModules.get(moduleName);
        // We select the module now. This way the back button will keep the module selected.
        this.handleOptionSelection(target);
        // We need to know if the overall modal has a footer so we know when to show a real / vs fake footer.
        moduleData.showFooter = this.modal.hasFooterContent();
        this.dialogueDom.setBackButtonModuleData(moduleData);
        this.dialogueDom.showModuleHelp(moduleData, this.modal);
    }

    /**
     * Handle the display of a category when the user clicks on the "display category" button.
     * @param {HTMLElement} target The target element that triggered the event
     */
    handleDisplayCategory(target) {
        const category = target.dataset.category;
        if (!category) {
            return;
        }
        this.dialogueDom.hideModuleHelp();
        const tabNav = this.dialogueDom.showCategoryTab(category);
        tabNav.focus();
        tabNav.scrollIntoView();
    }

    /**
     * Handle the favourite state of a module when the user clicks on the "starred" button.
     *
     * @param {HTMLElement} target The target element that triggered the event
     * @return {Promise} A promise that resolves when the event is handled
     */
    async handleFavouriteClick(target) {
        const caller = target.closest(selectors.actions.optionActions.manageFavourite);
        const id = caller.dataset.id;
        const name = caller.dataset.name;
        const internal = caller.dataset.internal;
        const isFavourite = caller.dataset.favourited;

        // Switch on fave or not.
        if (isFavourite === 'true') {
            await Repository.unfavouriteModule(name, id);
            this.updateFavouriteItemValue(internal, false);
        } else {
            await Repository.favouriteModule(name, id);
            this.updateFavouriteItemValue(internal, true);
        }
    }

    /**
     * Handle a clear search action.
     */
    handleClearSearch() {
        const searchInput = this.dialogueDom.getSearchInputElement();
        searchInput.value = "";
        searchInput.focus();
        this.toggleSearchResultsView(searchInput.value);
    }

    /**
     * Handle the click on a chooser option.
     *
     * @param {HTMLElement} target The target element that triggered the event
     */
    handleOptionSelection(target) {
        const option = this.dialogueDom.getClosestChooserOption(target);
        if (option === null) {
            return;
        }
        this.selectedModule = option;
        this.dialogueDom.markChooserOptionAsSelected(option, getFirst(this.modal.getFooter()));
    }

    /**
     * Submit the selected module to the chooser.
     *
     * This method will redirect the user to the URL of the selected module, if one is selected.
     *
     * @param {HTMLElement} newSelectedModule optional new selected module element.
     * @return {void}
     */
    submitAddSelectedModule(newSelectedModule = null) {
        if (newSelectedModule) {
            this.handleOptionSelection(newSelectedModule);
        }
        if (this.selectedModule === null) {
            return;
        }
        window.location.href = this.dialogueDom.getChooserOptionUrl(this.selectedModule);
    }

    /**
     * Set up our tabindex information across the chooser.
     *
     * @method setupKeyboardAccessibility
     */
    setupKeyboardAccessibility() {
        const mainElement = getFirst(this.modal.getModal());
        const $mainElement = $(mainElement);

        mainElement.tabIndex = -1;

        // Set up custom interaction events for RTL-aware keyboard navigation.
        CustomEvents.define($mainElement, [
            CustomEvents.events.next,
            CustomEvents.events.previous,
            CustomEvents.events.home,
            CustomEvents.events.end,
        ]);

        // Map of keyboard events to their corresponding focus methods.
        const bindings = [
            {event: CustomEvents.events.next, method: 'focusNextChooserOption'},
            {event: CustomEvents.events.previous, method: 'focusPreviousChooserOption'},
            {event: CustomEvents.events.home, method: 'focusFirstChooserOption'},
            {event: CustomEvents.events.end, method: 'focusLastChooserOption'},
        ];

        // Handle focus move (automatically handles RTL).
        const handleFocusMove = (method) => (e, data) => {
            const currentOption = this.dialogueDom.getClosestChooserOption(data.originalEvent.target);
            if (currentOption !== null) {
                data.originalEvent.preventDefault();
                this.dialogueDom[method](currentOption);
            }
        };

        bindings.forEach(({event, method}) => {
            $mainElement.on(event, handleFocusMove(method));
        });

        // Handle space and enter keys for selection.
        mainElement.addEventListener("keydown", (e) => {
            const currentOption = this.dialogueDom.getClosestChooserOption(
                e.target
            );
            if (currentOption === null) {
                return;
            }

            if (e.keyCode === enter || e.keyCode === space) {
                // Check first if the target is an internal control button (favourite or help).
                // If that is the case, the regular click will handle the event.
                if (e.target.closest(selectors.regions.chooserOption.actions)) {
                    return;
                }
            }

            if (e.keyCode === space) {
                e.preventDefault();
                this.handleOptionSelection(currentOption);
            }
            if (e.keyCode === enter) {
                e.preventDefault();
                this.submitAddSelectedModule(currentOption);
            }
        });
    }

    /**
     * Toggle (display/hide) the search results depending on the value of the search query
     *
     * @method toggleSearchResultsView
     * @param {String} searchQuery The search query
     */
    async toggleSearchResultsView(searchQuery) {
        const searchResultsData = this.searchModules(searchQuery);

        if (searchQuery.length > 0) {
            await this.dialogueDom.refreshSearchResults(searchQuery, searchResultsData);
            this.dialogueDom.showAllActivitiesTab(true);
        } else {
            this.dialogueDom.cleanSearchResults();
        }
    }

    /**
     * Return the list of modules which have a name or description that matches the given search term.
     *
     * @method searchModules
     * @param {String} searchTerm The search term to match
     * @return {Array}
     */
    searchModules(searchTerm) {
        if (searchTerm === '') {
            return this.mappedModules;
        }
        searchTerm = searchTerm.toLowerCase();
        const searchResults = [];
        this.mappedModules.forEach((activity) => {
            const activityName = activity.title.toLowerCase();
            const activitySummary = activity.summary.toLowerCase();
            const activityDesc = activity.help.toLowerCase();
            if (activityName.includes(searchTerm) || activitySummary.includes(searchTerm) || activityDesc.includes(searchTerm)) {
                searchResults.push(activity);
            }
        });

        return searchResults;
    }

    /**
     * Update the favourite item value in the mapped modules.
     *
     * @param {String} internal The internal name of the module.
     * @param {Boolean} favourite Whether the module is a favourite or not.
     * @return {Promise} A promise that resolves when the item is updated.
     */
    async updateFavouriteItemValue(internal, favourite) {
        const moduleItem = this.mappedModules.find(({name}) => name === internal);
        if (!moduleItem) {
            return;
        }
        moduleItem.favourite = favourite;

        this.dialogueDom.updateItemStarredIcons(internal, favourite);

        if (this.dialogueDom.isFavoutiteTabActive()) {
            this.isFavouriteTabDirty = true;
        } else {
            this.refreshFavouritesTabContent();
        }
    }

    /**
     * Refresh the favourites tab content.
     *
     * Note: this method will also hide the favourites tab if there are no favourite modules
     * to keep the modal consistent.
     *
     * @return {Promise} A promise that resolves when the content is refreshed.
     */
    async refreshFavouritesTabContent() {
        this.isFavouriteTabDirty = false;
        const favouriteCount = this.mappedModules.filter(mod => mod.favourite === true).size;
        this.dialogueDom.toggleFavouriteTabDisplay(favouriteCount > 0);
        await this.dialogueDom.refreshFavouritesTabContent(this.mappedModules);
    }
}
