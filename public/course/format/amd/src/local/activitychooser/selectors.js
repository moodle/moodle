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
 * Define all of the selectors we will be using on the grading interface.
 *
 * @module     core_courseformat/local/activitychooser/selectors
 * @copyright  2019 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * A small helper function to build queryable data selectors.
 * @method getDataSelector
 * @param {String} name
 * @param {String} value
 * @return {string}
 */
const getDataSelector = (name, value) => {
    return `[data-${name}="${value}"]`;
};

export default {
    regions: {
        activeFooter: getDataSelector('region', 'active-footer-container'),
        categoryContent: getDataSelector('region', 'category-content'),
        getSectionChooserOptions: containerid => `${containerid} ${getDataSelector('region', 'chooser-options-container')}`,
        chooserOptions: getDataSelector('region', 'chooser-options-container'),
        chooserOption: {
            container: getDataSelector('region', 'chooser-option-container'),
            actions: getDataSelector('region', 'chooser-option-actions-container'),
            info: getDataSelector('region', 'chooser-option-info-container'),
        },
        chooserSummary: {
            container: getDataSelector('region', 'chooser-option-summary-container'),
            content: getDataSelector('region', 'chooser-option-summary-content-container'),
            header: getDataSelector('region', 'summary-header'),
            actions: getDataSelector('region', 'chooser-option-summary-actions-container'),
        },
        carousel: getDataSelector('region', 'carousel'),
        help: getDataSelector('region', 'help'),
        tabContent: getDataSelector('region', 'tabcontent'),
        favouriteTabNav: getDataSelector('region', 'favourites-tab-nav'),
        allTabNav: getDataSelector('region', 'all-tab-nav'),
        categoryTabNav: category => getDataSelector('region', `${category}-tab-nav`),
        favouriteTab: getDataSelector('region', 'favourites'),
        getModuleSelector: modname => `[role="menuitem"][data-modname="${modname}"]`,
        searchResults: getDataSelector('region', 'search-results-container'),
    },
    actions: {
        optionActions: {
            showSummary: getDataSelector('action', 'show-option-summary'),
            manageFavourite: getDataSelector('action', 'manage-module-favourite'),
        },
        addChooser: getDataSelector('action', 'add-chooser-option'),
        addSelectedChooserOption: getDataSelector('action', 'add-selected-chooser-option'),
        clearSearch: getDataSelector('action', 'clearsearch'),
        closeOption: getDataSelector('action', 'close-chooser-option-summary'),
        displayCategory: getDataSelector('action', 'chooser-display-category'),
        hide: getDataSelector('action', 'hide'),
        search: getDataSelector('action', 'search'),
    },
    elements: {
        section: '.section',
        sectionmodchooser: 'button.section-modchooser-link',
        tab: 'a[data-bs-toggle="tab"]',
        activetab: 'a[data-bs-toggle="tab"][aria-selected="true"]',
        moduleItem: item => `[data-internal="${item}"]`,
        favouriteIconActive: '[data-icon="favourite-active"]',
        favouriteIconInactive: '[data-icon="favourite-inactive"]',
    },
};
