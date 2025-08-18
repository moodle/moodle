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

import {addIconToContainer} from 'core/loadingicon';
import Carousel from 'theme_boost/bootstrap/carousel';
import {getFirst} from 'core/normalise';
import Notification from 'core/notification';
import Pending from 'core/pending';
import selectors from 'core_courseformat/local/activitychooser/selectors';
import Tab from 'theme_boost/bootstrap/tab';
import * as Templates from 'core/templates';


/**
 * The activity changer dialogue DOM manipulation module.
 *
 * @module     core_courseformat/local/activitychooser/dialoguedom
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export default class ChooserDialogueDOM {
    /**
     * ChooserDialogueDOM constructor.
     *
     * @param {Object} dialogue The dialogue object.
     * @param {Modal} modal The modal object.
     * @param {Object} exporter The exporter object to generate template data.
     */
    constructor(dialogue, modal, exporter) {
        this.modal = modal;
        this.modalBody = getFirst(this.modal.getBody());
        this.exporter = exporter;
        // Temporal variable while migrating methods.
        this.dialogue = dialogue;
    }

    /**
     * Get the search input element.
     *
     * @return {HTMLElement} The search input element.
     */
    getSearchInputElement() {
        return this.modalBody.querySelector(selectors.actions.search);
    }

    /**
     * Get the closest chooser option element.
     *
     * @param {HTMLElement} element
     * @return {HTMLElement|null} element
     */
    getClosestChooserOption(element) {
        return element.closest(selectors.regions.chooserOption.container);
    }

    /**
     * Check if the search tab is active.
     *
     * @return {Boolean} True if the search tab is active, false otherwise.
     */
    isFavoutiteTabActive() {
        const favouriteTab = this.modalBody.querySelector(selectors.regions.favouriteTabNav);
        return favouriteTab && favouriteTab.classList.contains('active');
    }

    /**
     * Get the URL of a chooser option.
     *
     * @param {HTMLElement} optionContainer The container of the chooser option.
     * @return {String} The URL of the chooser option.
     * @throws {Error} If the option container does not contain a link.
     */
    getChooserOptionUrl(optionContainer) {
        const optionLink = optionContainer.querySelector(selectors.actions.addChooser);
        if (!optionLink) {
            throw new Error('Invalid chooser option container: no link found');
        }
        return optionLink.getAttribute('href');
    }

    /**
     * Show the search results.
     *
     * @param {String} searchQuery The search query string.
     * @param {Object} searchResultsData Data containing the module items that satisfy the search criteria
     */
    async refreshSearchResults(searchQuery, searchResultsData) {
        const searchResultsContainer = this.modalBody.querySelector(selectors.regions.searchResults);
        const clearSearchButton = this.modalBody.querySelector(selectors.actions.clearSearch);

        await this.renderSearchResults(searchResultsContainer, searchQuery, searchResultsData);
        const chooserOptionsContainer = searchResultsContainer.querySelector(selectors.regions.chooserOptions);
        const firstSearchResultItem = chooserOptionsContainer.querySelector(selectors.regions.chooserOption.container);
        if (firstSearchResultItem) {
            // Set the first result item to be focusable.
            this.toggleFocusableChooserOption(firstSearchResultItem, true);
            // Register keyboard events on the created search result items.
        }
        clearSearchButton.classList.remove('d-none');

        // Results are rendered in the all activities tab, so we need to hide the category content.
        const tabContent = searchResultsContainer.closest(selectors.regions.tabContent);
        const categoryContent = tabContent.querySelector(selectors.regions.categoryContent);
        categoryContent.classList.add('d-none');
    }

    /**
     * Clear the search results.
     */
    cleanSearchResults() {
        const searchResultsContainer = this.modalBody.querySelector(selectors.regions.searchResults);
        const clearSearchButton = this.modalBody.querySelector(selectors.actions.clearSearch);
        searchResultsContainer.innerHTML = '';
        clearSearchButton.classList.add('d-none');

        // Results are rendered in the all activities tab, so we need to show the category content again.
        const tabContent = searchResultsContainer.closest(selectors.regions.tabContent);
        const categoryContent = tabContent.querySelector(selectors.regions.categoryContent);
        categoryContent.classList.remove('d-none');
    }

    /**
     * Render the search results in a defined container
     *
     * @private
     * @method renderSearchResults
     * @param {HTMLElement} searchResultsContainer The container where the data should be rendered
     * @param {String} searchQuery The search query string
     * @param {Object} searchResultsData Data containing the module items that satisfy the search criteria
     */
    async renderSearchResults(searchResultsContainer, searchQuery, searchResultsData) {
        const templateData = this.exporter.getSearchResultData(searchQuery, searchResultsData);
        // Build up the html & js ready to place into the help section.
        const {html, js} = await Templates.renderForPromise(
            'core_courseformat/local/activitychooser/search_results',
            templateData
        );
        await Templates.replaceNodeContents(searchResultsContainer, html, js);
    }

    /**
     * Show the "All activities" tab.
     *
     * @method showAllActivitiesTab
     * @return {HTMLElement} The "All activities" tab element.
     */
    showAllActivitiesTab() {
        return this.showCategoryTab('all');
    }

    /**
     * Show a specific category tab.
     * @method showCategoryTab
     * @param {String} category The category to show.
     * @return {HTMLElement} The category tab element.
     */
    showCategoryTab(category) {
        const navTab = this.modalBody.querySelector(selectors.regions.categoryTabNav(category));

        if (navTab.classList.contains('active')) {
            return navTab;
        }

        const pendingPromise = new Pending(`core_courseformat/activitychooser:${category}-tab`);

        navTab.addEventListener('shown.bs.tab', pendingPromise.resolve, {once: true});
        Tab.getOrCreateInstance(navTab).show();

        return navTab;
    }

    /**
     * Update the starred icons in the chooser modal.
     *
     * @method updateItemStarredIcons
     * @param {String} internal The internal name of the module.
     * @param {Boolean} favourite Whether the module is a favourite or not.
     */
    updateItemStarredIcons(internal, favourite) {
        const favouriteButtons = this.modalBody.querySelectorAll(
            `${selectors.elements.moduleItem(internal)} ${selectors.actions.optionActions.manageFavourite}`
        );
        Array.from(favouriteButtons).forEach((element) => {
            element.dataset.favourited = favourite;
            element.setAttribute('aria-pressed', favourite);
            element.querySelector(selectors.elements.favouriteIconActive)?.classList.toggle('d-none', !favourite);
            element.querySelector(selectors.elements.favouriteIconInactive)?.classList.toggle('d-none', favourite);

            const iconSelectsor = favourite ? selectors.elements.favouriteIconActive : selectors.elements.favouriteIconInactive;
            const favouriteIcon = element.querySelector(iconSelectsor);
            element.setAttribute('aria-label', favouriteIcon?.getAttribute('data-action-label') || '');
            element.setAttribute('title', favouriteIcon?.getAttribute('data-action-label') || '');
        });
    }

    /**
     * Refresh the favourite content.
     *
     * @param {Array} mappedModules The modules to be displayed in the favourite tab.
     */
    async refreshFavouritesTabContent(mappedModules) {
        const templateData = await this.exporter.getFavouriteTabData(mappedModules);
        const favouriteArea = this.modalBody.querySelector(selectors.regions.favouriteTab);
        const {html, js} = await Templates.renderForPromise(
            'core_courseformat/local/activitychooser/tabcontent',
            templateData,
        );
        await Templates.replaceNodeContents(favouriteArea, html, js);
    }

    /**
     * Toggle the display of the favourite tab.
     *
     * The favourite tab is only displayed when there are favourite modules
     * or when it is the active tab.
     *
     * @param {Boolean} displayed Whether we want to show or hide the favourite tab
     */
    toggleFavouriteTabDisplay(displayed) {
        const favouriteTabNav = this.modalBody.querySelector(selectors.regions.favouriteTabNav);

        let moveFocusTo;
        if (!displayed && favouriteTabNav.classList.contains('active')) {
            moveFocusTo = this.showAllActivitiesTab();
        }

        favouriteTabNav?.classList.toggle('d-none', !displayed);
        favouriteTabNav.tabIndex = displayed ? 0 : -1;
        // The disabled class is used by Boostrap Tab for keyboard navigation.
        if (displayed) {
            favouriteTabNav.classList.remove('disabled');
        } else {
            favouriteTabNav.classList.add('disabled');
        }

        if (moveFocusTo) {
            moveFocusTo.focus();
        }
        this.initActiveTabNavigation();
    }

    /**
     * Given an event from the main module 'page' navigate to it's help section via a carousel.
     *
     * @method showModuleHelp
     * @param {Object} moduleData Data of the module to carousel to
     */
    async showModuleHelp(moduleData) {
        const carousel = this.modalBody.querySelector(selectors.regions.carousel);

        const help = carousel.querySelector(selectors.regions.help);
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
        const contentPromise = Templates.renderForPromise(
            'core_courseformat/local/activitychooser/help',
            await this.exporter.getModuleHelpTemplateData(moduleData),
        );

        // Wait for the content to be ready, and for the transition to be complet.
        Promise.all([contentPromise, spinnerPromise, transitionPromise])
            .then(([{html, js}]) => Templates.replaceNodeContents(help, html, js))
            .then(() => {
                help.querySelector(selectors.regions.chooserSummary.header).focus();
                return help;
            })
            .catch(Notification.exception);

        // Move to the next slide, and resolve the transition promise when it's done.
        carousel.addEventListener(
            'slid.bs.carousel',
            () => {
                transitionPromiseResolver();
            },
            {once: true}
        );
        // Trigger the transition between 'pages'.
        Carousel.getInstance(carousel).next();
    }

    /**
     * Hide the help section of the chooser.
     *
     * @param {String|null} internal The internal name of the module to return to, if any.
     */
    hideModuleHelp(internal = null) {
        const carousel = this.modalBody.querySelector(selectors.regions.carousel);
        // Trigger the transition between 'pages'.
        Carousel.getInstance(carousel).to(0);
        // Some active footers may not provide a valid internal value.
        if (!internal) {
            return;
        }
        carousel.addEventListener(
            'slid.bs.carousel',
            () => {
                this.focusChooserOption(internal);
            },
            {once: true}
        );
    }

    /**
     * Set the module data for the back button.
     *
     * @param {Object} moduleData The module data to set for the back button.
     */
    setBackButtonModuleData(moduleData) {
        const footer = getFirst(this.modal.getFooter());
        const modnameValue = `${moduleData.componentname}_${moduleData.link}`;
        footer.querySelector(selectors.actions.closeOption).dataset.modname = modnameValue;
    }

    /**
     * Toggle the visibility of the back button.
     *
     * @param {Boolean} show Whether to show or hide the back button.
     */
    toggleBackButton(show) {
        const footer = getFirst(this.modal.getFooter());
        footer.querySelector(selectors.actions.closeOption).classList.toggle('d-none', !show);
    }

    /**
     * Toggle the visibility of the "Add selected" button.
     *
     * @param {Boolean} show Whether to show or hide the "Add selected" button.
     */
    toggleAddButton(show) {
        const footer = getFirst(this.modal.getFooter());
        footer.querySelector(selectors.actions.addSelectedChooserOption).classList.toggle('d-none', !show);
    }

    /**
     * Toggle the visibility of the active footer.
     *
     * @param {Boolean} show Whether to show or hide the active footer.
     */
    toggleActiveFooter(show) {
        const footer = getFirst(this.modal.getFooter());
        footer.querySelector(selectors.regions.activeFooter).classList.toggle('d-none', !show);
    }

    /**
     * Focus on a specific activity inside the active tab (if present).
     *
     * @private
     * @method focusChooserOption
     * @param {String} internal The internal name of the module.
     */
    focusChooserOption(internal) {
        const currentTabNav = this.modalBody.querySelector(selectors.elements.activetab);
        const activeSectionId = currentTabNav.getAttribute("href");
        const sectionChooserOptions = this.modalBody.querySelector(selectors.regions.getSectionChooserOptions(activeSectionId));
        const newCurrent = sectionChooserOptions.querySelector(selectors.regions.getModuleSelector(internal));

        if (!newCurrent) {
            throw new Error(`Invalid chooser option to focus on: ${internal}`);
        }

        // Chooser can only have one element focusable at a time, so we disable them all first.
        this.disableFocusAllChooserOptions(currentTabNav);
        this.toggleFocusableChooserOption(newCurrent, true);

        // Little hack: we want the element considered a focus-visible element.
        // But the focus method does not trigger the focus-visible class. There's an
        // experimental "{focusVisible: true}" option in the focus method, but it's not
        // supported in all browsers yet so we need to fake an editable element.
        newCurrent.contentEditable = true;
        newCurrent.focus();
        newCurrent.contentEditable = false;
    }

    /**
     * Initialise the active tab navigation.
     */
    initActiveTabNavigation() {
        const activeSectionId = this.modalBody.querySelector(selectors.elements.activetab).getAttribute("href");
        const sectionChooserOptions = this.modalBody.querySelector(selectors.regions.getSectionChooserOptions(activeSectionId));
        const firstChooserOption = sectionChooserOptions?.querySelector(selectors.regions.chooserOption.container);
        if (!firstChooserOption) {
            return;
        }
        this.toggleFocusableChooserOption(firstChooserOption, true);
    }

    /**
     * Initialise all Boostrap components.
     */
    initBootstrapComponents() {
        this.modalBody.querySelectorAll(selectors.elements.tab).forEach((navTab) => {
            // Init the Bootstrap Tab navigation.
            Tab.getOrCreateInstance(navTab);
        });

        // Set up the carousel.
        const carousel = this.modalBody.querySelector(selectors.regions.carousel);
        new Carousel(carousel, {
            interval: false,
            pause: true,
            keyboard: false
        });
    }

    /**
     * Disable the focus of all chooser options in a specific container (section).
     *
     * @method disableFocusAllChooserOptions
     * @param {HTMLElement} tabNav The tab navigation element (from the shown.bs.ta event).
     */
    disableFocusAllChooserOptions(tabNav) {
        const tabId = tabNav.getAttribute("href");
        const chooserOptions = this.modalBody.querySelector(
            selectors.regions.getSectionChooserOptions(tabId)
        );

        if (chooserOptions === null) {
            return;
        }

        const allChooserOptions = chooserOptions.querySelectorAll(selectors.regions.chooserOption.container);
        allChooserOptions.forEach((chooserOption) => {
            this.toggleFocusableChooserOption(chooserOption, false);
        });
    }

    /**
     * Add or remove a chooser option from the focus order.
     *
     * @private
     * @method toggleFocusableChooserOption
     * @param {HTMLElement} chooserOption The chooser option element which should be added or removed from the focus order
     * @param {Boolean} isFocusable Whether the chooser element is focusable or not
     */
    toggleFocusableChooserOption(chooserOption, isFocusable) {
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
    }

    /**
     * Move the focus to the previous chooser option element.
     *
     * @param {HTMLElement} current The current chooser option element
     */
    focusNextChooserOption(current) {
        this.moveChooserOptionFocus(
            current,
            (currentOption) => currentOption.nextElementSibling ?? currentOption,
        );
    }

    /**
     * Move the focus to the previous chooser option element.
     *
     * @param {HTMLElement} current The current chooser option element
     */
    focusPreviousChooserOption(current) {
        this.moveChooserOptionFocus(
            current,
            (currentOption) => currentOption.previousElementSibling ?? currentOption,
        );
    }

    /**
     * Move the focus to the first chooser option element.
     *
     * @param {HTMLElement} current The current chooser option element
     */
    focusFirstChooserOption(current) {
        this.moveChooserOptionFocus(
            current,
            (currentOption, container) => container.firstElementChild ?? currentOption,
        );
    }

    /**
     * Move the focus to the last chooser option element.
     *
     * @param {HTMLElement} current The current chooser option element
     */
    focusLastChooserOption(current) {
        this.moveChooserOptionFocus(
            current,
            (currentOption, container) => container.lastElementChild ?? currentOption,
        );
    }

    /**
     * Move the focus to the next chooser option element.
     *
     * @private
     * @param {HTMLElement} current The current chooser option element
     * @param {Function} getNextFocus Function to get the next focusable element
     */
    moveChooserOptionFocus(current, getNextFocus) {
        const currentOption = this.getClosestChooserOption(current);
        const container = current.closest(selectors.regions.chooserOptions);

        if (!container || !currentOption) {
            throw new Error('Invalid chooser options container or current option');
        }

        const newFocusOption = getNextFocus(currentOption, container);
        if (!newFocusOption) {
            return;
        }

        this.toggleFocusableChooserOption(currentOption, false);
        this.toggleFocusableChooserOption(newFocusOption, true);
        newFocusOption.focus();
    }

    /**
     * Mark a chooser option as selected.
     *
     * @param {HTMLElement} chooserOption The chooser option element to mark as selected.
     */
    markChooserOptionAsSelected(chooserOption) {
        this.unmarkAllChooserOptionAsSelected();
        chooserOption.classList.add('selected');
        chooserOption.setAttribute('aria-selected', 'true');

        const footer = getFirst(this.modal.getFooter());
        const addButton = footer.querySelector(selectors.actions.addSelectedChooserOption);
        if (addButton) {
            addButton.removeAttribute('disabled');
        }
    }

    /**
     * Unmark all chooser options as selected.
     */
    unmarkAllChooserOptionAsSelected() {
        const selectedOptions = this.modalBody.querySelectorAll(`${selectors.regions.chooserOption.container}.selected`);
        selectedOptions.forEach((option) => {
            option.classList.remove('selected');
            option.setAttribute('aria-selected', 'false');
        });

        const footer = getFirst(this.modal.getFooter());
        const addButton = footer.querySelector(selectors.actions.addSelectedChooserOption);
        if (addButton) {
            addButton.setAttribute('disabled', 'disabled');
        }
    }
}
