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
 * @module     core_course/local/activitychooser/selectors
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
        chooser: getDataSelector('region', 'chooser-container'),
        getSectionChooserOptions: containerid => `${containerid} ${getDataSelector('region', 'chooser-options-container')}`,
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
        modules: getDataSelector('region', 'modules'),
        favouriteTabNav: getDataSelector('region', 'favourite-tab-nav'),
        defaultTabNav: getDataSelector('region', 'default-tab-nav'),
        activityTabNav: getDataSelector('region', 'activity-tab-nav'),
        favouriteTab: getDataSelector('region', 'favourites'),
        recommendedTab: getDataSelector('region', 'recommended'),
        defaultTab: getDataSelector('region', 'default'),
        activityTab: getDataSelector('region', 'activity'),
        resourceTab: getDataSelector('region', 'resources'),
        getModuleSelector: modname => `[role="menuitem"][data-modname="${modname}"]`,
        searchResults: getDataSelector('region', 'search-results-container'),
        searchResultItems: getDataSelector('region', 'search-result-items-container'),
    },
    actions: {
        optionActions: {
            showSummary: getDataSelector('action', 'show-option-summary'),
            manageFavourite: getDataSelector('action', 'manage-module-favourite'),
        },
        addChooser: getDataSelector('action', 'add-chooser-option'),
        closeOption: getDataSelector('action', 'close-chooser-option-summary'),
        hide: getDataSelector('action', 'hide'),
        search: getDataSelector('action', 'search'),
        clearSearch: getDataSelector('action', 'clearsearch'),
    },
    render: {
        favourites: getDataSelector('render', 'favourites-area'),
    },
    elements: {
        section: '.section',
        sectionmodchooser: 'button.section-modchooser-link',
        sitemenu: '.block_site_main_menu',
        sitetopic: 'div.sitetopic',
        tab: 'a[data-toggle="tab"]',
        activetab: 'a[data-toggle="tab"][aria-selected="true"]',
        visibletabs: 'a[data-toggle="tab"]:not(.d-none)'
    },
};
