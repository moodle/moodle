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
 * Prefetch module to help lazily load content for use on the current page.
 *
 * @module     core/prefetch
 * @class      prefetch
 * @package    core
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import Config from 'core/config';

// Keep track of whether the initial prefetch has occurred.
let initialPrefetchComplete = false;

// Prefetch templates.
let templateList = [];

// Prefetch strings.
let stringList = {};

let prefetchTimer;

/**
 * Fetch all queued items in the queue.
 *
 * Should only be called via processQueue.
 */
const fetchQueue = () => {
    // Prefetch templates.
    if (templateList) {
        const templatesToLoad = templateList.slice();
        templateList = [];
        import('core/templates')
        .then(Templates => Templates.prefetchTemplates(templatesToLoad))
        .catch();
    }

    // Prefetch strings.
    const mappedStringsToFetch = stringList;
    stringList = {};

    const stringsToFetch = [];
    Object.keys(mappedStringsToFetch).forEach(component => {
        stringsToFetch.push(...mappedStringsToFetch[component].map(key => {
            return {component, key};
        }));
    });

    if (stringsToFetch) {
        import('core/str')
        .then(Str => Str.get_strings(stringsToFetch))
        .catch();
    }
};

/**
 * Process the prefetch queues as required.
 *
 * The initial call will queue the first fetch after a delay.
 * Subsequent fetches are immediate.
 */
const processQueue = () => {
    if (prefetchTimer) {
        // There is a live prefetch timer. The initial prefetch has been scheduled but is not complete.
        return;
    }

    // The initial prefetch has compelted. Just queue as normal.
    if (initialPrefetchComplete) {
        fetchQueue();

        return;
    }

    // Queue the initial prefetch in a short while.
    prefetchTimer = setTimeout(() => {
        initialPrefetchComplete = true;
        prefetchTimer = null;

        // Ensure that the icon system is loaded.
        // This can be quite slow and delay UI interactions if it is loaded on demand.
        import(Config.iconsystemmodule)
        .then(IconSystem => {
            const iconSystem = new IconSystem();
            prefetchTemplate(iconSystem.getTemplateName());

            return iconSystem;
        })
        .then(iconSystem => {
            fetchQueue();
            iconSystem.init();

            return;
        })
        .catch();
    }, 500);
};

/**
 * Add a set of templates to the prefetch queue.
 *
 * @param {Array} templatesNames
 */
const prefetchTemplates = templatesNames => {
    templateList = templateList.concat(templatesNames);

    processQueue();
};

/**
 * Add a single template to the prefetch queue.
 *
 * @param {String} templateName
 * @returns {undefined}
 */
const prefetchTemplate = templateName => prefetchTemplates([templateName]);

/**
 * Add a set of strings from the same component to the prefetch queue.
 *
 * @param {String} component
 * @param {String[]} keys
 */
const prefetchStrings = (component, keys) => {
    if (!stringList[component]) {
        stringList[component] = [];
    }

    stringList[component] = stringList[component].concat(keys);

    processQueue();
};

/**
 * Add a single string to the prefetch queue.
 *
 * @param {String} component
 * @param {String} key
 */
const prefetchString = (component, key) => {
    if (!stringList[component]) {
        stringList[component] = [];
    }

    stringList[component].push(key);

    processQueue();
};

// Prefetch some commonly-used templates.
prefetchTemplates([].concat(
    ['core/loading'],
    ['core/modal'],
    ['core/modal_backdrop'],
));

// And some commonly used strings.
prefetchStrings('core', [
    'cancel',
    'closebuttontitle',
    'loading',
    'savechanges',
]);
prefetchStrings('core_form', [
    'showless',
    'showmore',
]);

export default {
    prefetchTemplate,
    prefetchTemplates,
    prefetchString,
    prefetchStrings,
};
