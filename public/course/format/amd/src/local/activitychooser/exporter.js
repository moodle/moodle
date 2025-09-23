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
 * Module to generate template data for the activity chooser.
 *
 * @module     core_courseformat/local/activitychooser/exporter
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getStrings} from 'core/str';

const activityCategories = [
    'administration',
    'assessment',
    'collaboration',
    'communication',
    'content',
    'interactivecontent',
];

let allStrings = null;

loadNecessaryStrings();

export default class {
    /**
     * A tab data structure.
     *
     * @typedef {object} TabData
     * @property {String} tabId the tab ID
     * @property {Boolean} active whether the tab is active or not
     * @property {Array} items the filtered modules to be displayed in the tab
     * @property {Boolean} displayed whether the tab is displayed or not
     * @property {String} tabLabel the tab label
     * @property {String|null} tabHelp the help text for the tab (optional)
     */

    /**
     * @typedef {Object} ModuleHelpData
     * @property {String} name The name of the module.
     * @property {String} description The description of the module.
     * @property {Array<ModulePurposeData>} purposes The purposes of the module.
     * @property {Array} details Additional details about the module.
     */

    /**
     * @typedef {Object} ModulePurposeData
     * @property {String} purposename The name of the purpose.
     * @property {String} purposelabel The label of the purpose.
     */

    /**
     * Generate a tab data object for the activity chooser.
     *
     * @private
     * @param {String} tabId Tab ID.
     * @param {Array} filteredModules Filtered modules to be displayed in the tab.
     * @param {String} tabLabel Tab label.
     * @param {String|null} tabHelp Help text for the tab (optional).
     * @param {Boolean} active Whether the tab is active or not.
     * @return {TabData} Tab data object.
     */
    getTabData(tabId, filteredModules, tabLabel, tabHelp = null, active = false) {
        const result = {
            tabId: tabId,
            active: active,
            items: filteredModules,
            displayed: filteredModules.length > 0,
            tabLabel,
        };
        if (tabHelp) {
            result.tabHelp = tabHelp;
        }
        return result;
    }

    /**
     * Normalise the modules data to be used in the chooser.
     *
     * The modulesData can be a plain array or a Map. This method will convert it to a
     * plain array of objects.
     *
     * @param {Array|Map} modulesData Modules data to be used in the chooser.
     * @return {Array} Normalised modules data.
     */
    normaliseModulesData(modulesData) {
        if (modulesData instanceof Map) {
            modulesData = Array.from(modulesData.values());
        } else if (!Array.isArray(modulesData)) {
            throw new Error('Invalid modules data format. Expected an array or a Map.');
        }
        return modulesData;
    }

    /**
     * Fetch the chooser template data for a specific section.
     *
     * @param {Array|Map} modulesData Modules data to be used in the chooser.
     * @return {Promise<Object>} Promise resolved with the template data.
     */
    async getModChooserTemplateData(modulesData) {
        modulesData = this.normaliseModulesData(modulesData);
        const allStrings = await loadNecessaryStrings();
        const favouriteTab = await this.getFavouriteTabData(modulesData);

        const tabs = [
            {
                ...this.getTabData(
                    'all',
                    modulesData,
                    allStrings.all,
                    null,
                    !favouriteTab.displayed,
                ),
                hasSearchResults: true, // The all tab will also show search results.
            },
            favouriteTab,
            {
                ...this.getTabData(
                    'recommended',
                    modulesData.filter(mod => mod.recommended === true),
                    allStrings.recommended,
                    allStrings.recommended_help
                ),
                separator: true, // Add a separator before the purpose categories.
            },
        ];

        activityCategories.forEach((category) => {
            const categoryModules = modulesData.filter(mod => mod.purpose == category || mod.otherpurpose == category);
            if (categoryModules.length === 0) {
                return;
            }
            tabs.push(
                this.getTabData(
                    category,
                    categoryModules,
                    allStrings['mod_purpose_' + category],
                    allStrings['mod_purpose_' + category + '_help']
                )
            );
        });

        return {
            modules: modulesData,
            tabs,
        };
    }

    /**
     * Get the module help template data.
     *
     * @param {Object} moduleData Data of the module to get help for.
     * @return {Promise<ModuleHelpData>} Promise resolved with the module help data.
     */
    async getModuleHelpTemplateData(moduleData) {
        const allStrings = await loadNecessaryStrings();
        const data = {
            ...moduleData,
            purposes: [],
        };
        // Add purpose information from all related fields.
        for (const purposeField of ['purpose', 'otherpurpose']) {
            if (
                !moduleData[purposeField]
                || !activityCategories.includes(moduleData[purposeField])
            ) {
                continue;
            }
            data.purposes.push({
                purposename: moduleData[purposeField],
                purposelabel: allStrings[`mod_purpose_${moduleData[purposeField]}`],
            });
        }
        data.haspurposes = data.purposes.length > 0;
        // The rest of details are displayed as a simpler list.
        data.details = [
            {
                label: allStrings['gradable'],
                value: moduleData.gradable ? allStrings['yes'] : allStrings['no'],
            },
        ];
        return data;
    }

    /**
     * Get the favourite tab data.
     *
     * @param {Array|Map} modulesData Modules data to be used in the chooser.
     * @return {Promise<TabData>} Promise resolved with the template data.
     */
    async getFavouriteTabData(modulesData) {
        modulesData = this.normaliseModulesData(modulesData);
        const allStrings = await loadNecessaryStrings();

        // We need to deconstruct the modules data to ensure it is an array.
        const favouriteModules = modulesData.filter(
            mod => {
                return mod.favourite === true;
            }
        );

        return this.getTabData(
            'favourites',
            favouriteModules,
            allStrings.favourites,
            null,
            favouriteModules.length > 0,
        );
    }

    /**
     * Get the search result template data.
     *
     * @param {String} searchQuery The search query string.
     * @param {Array|Map} resultsModulesData Modules data to be used in the chooser.
     * @return {Object} The template data.
     */
    getSearchResultData(searchQuery, resultsModulesData) {
        resultsModulesData = this.normaliseModulesData(resultsModulesData);
        return {
            'searchresultsnumber': resultsModulesData.length,
            'searchresults': resultsModulesData,
            'hasresults': resultsModulesData.length > 0,
            'searchquery': searchQuery,
        };
    }

    /**
     * Get the number of items in a tab.
     *
     * @param {TabData} tabData The tab data.
     * @return {Number} The number of items in the tab.
     */
    countTabItems(tabData) {
        return tabData.items?.length ?? 0;
    }

    /**
     * Get the activity chooser footer template data.
     * @param {Object} footerData The active footer data object.
     * @return {Object} The template data.
     */
    getFooterData(footerData) {
        return {
            'activeFooter': footerData.customfootertemplate,
        };
    }
}

/**
 * Load the necessary strings for the activity chooser.
 *
 * @return {Promise<Object>} Promise resolved with the loaded strings.
 */
async function loadNecessaryStrings() {
    if (allStrings !== null) {
        return allStrings;
    }
    allStrings = {};

    const stringToLoad = [
        {key: 'all', component: 'core'},
        {key: 'yes', component: 'core'},
        {key: 'no', component: 'core'},
        {key: 'favourites', component: 'core'},
        {key: 'recommended', component: 'core'},
        {key: 'gradable', component: 'core'},
        {key: 'recommended_help', component: 'core_course'},
        ...activityCategories.map(
            (key) => ({
                key: 'mod_purpose_' + key,
                component: 'core_course',
            })
        ),
        ...activityCategories.map(
            (key) => ({
                key: 'mod_purpose_' + key + '_help',
                component: 'core_course',
            })
        ),
    ];

    const loadedStrings = await getStrings(stringToLoad);
    stringToLoad.forEach(({key}, index) => {
        allStrings[key] = loadedStrings[index];
    });
    return allStrings;
}
