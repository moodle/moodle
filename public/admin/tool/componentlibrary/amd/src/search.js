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
 * Interface to the Lunr search engines.
 *
 * @module     tool_componentlibrary/search
 * @copyright  2021 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import lunrJs from 'tool_componentlibrary/lunr';
import selectors from 'tool_componentlibrary/selectors';
import Log from 'core/log';
import Notification from 'core/notification';
import {enter, escape} from 'core/key_codes';

let lunrIndex = null;
let pagesIndex = null;

/**
 * Get the jsonFile that is generated when the component library is build.
 *
 * @method
 * @private
 * @param {String} jsonFile the URL to the json file.
 * @return {Object}
 */
const fetchJson = async(jsonFile) => {
    const response = await fetch(jsonFile);

    if (!response.ok) {
        Log.debug(`Error getting Hugo index file: ${response.status}`);
    }

    return await response.json();
};

/**
 * Initiate lunr on the data in the jsonFile and add the jsondata to the pagesIndex
 *
 * @method
 * @private
 * @param {String} jsonFile the URL to the json file.
 */
const initLunr = jsonFile => {
    fetchJson(jsonFile).then(jsondata => {
        pagesIndex = jsondata;
        // Using an arrow function here will break lunr on compile.
        lunrIndex = lunrJs(function() {
            this.ref('uri');
            this.field('title', {boost: 10});
            this.field('content');
            this.field('tags', {boost: 5});
            jsondata.forEach(p => {
                this.add(p);
            });
        });
        return null;
    }).catch(Notification.exception);
};

/**
 * Setup the eventlistener to listen on user input on the search field.
 *
 * @method
 * @private
 */
const initUI = () => {
    const searchInput = document.querySelector(selectors.searchinput);
    searchInput.addEventListener('keyup', e => {
        const query = e.currentTarget.value;
        if (query.length < 2) {
            document.querySelector(selectors.dropdownmenu).classList.remove('show');
            return;
        }
        renderResults(searchIndex(query));
    });
    searchInput.addEventListener('keydown', e => {
        if (e.keyCode === enter) {
            e.preventDefault();
        }
        if (e.keyCode === escape) {
            searchInput.value = '';
        }
    });
};

/**
 * Trigger a search in lunr and transform the result.
 *
 * @method
 * @private
 * @param  {String} query
 * @return {Array} results
 */
const searchIndex = query => {
    // Find the item in our index corresponding to the lunr one to have more info
    // Lunr result:
    //  {ref: "/section/page1", score: 0.2725657778206127}
    // Our result:
    //  {title:"Page1", href:"/section/page1", ...}

    return lunrIndex.search(query + ' ' + query + '*').map(result => {
        return pagesIndex.filter(page => {
            return page.uri === result.ref;
        })[0];
    });
};

/**
 * Display the 10 first results
 *
 * @method
 * @private
 * @param {Array} results to display
 */
const renderResults = results => {
    const dropdownMenu = document.querySelector(selectors.dropdownmenu);
    if (!results.length) {
        dropdownMenu.classList.remove('show');
        return;
    }

    // Clear out the results.
    dropdownMenu.innerHTML = '';

    const baseUrl = M.cfg.wwwroot + '/admin/tool/componentlibrary/docspage.php';

    // Only show the ten first results
    results.slice(0, 10).forEach(function(result) {
        const link = document.createElement("a");
        const chapter = result.uri.split('/')[1];
        link.appendChild(document.createTextNode(`${chapter} > ${result.title}`));
        link.classList.add('dropdown-item');
        link.href = baseUrl + result.uri;

        dropdownMenu.appendChild(link);
    });

    dropdownMenu.classList.add('show');
};

/**
 * Initialize module.
 *
 * @method
 * @param {String} jsonFile Full path to the search DB json file.
 */
export const search = jsonFile => {
    initLunr(jsonFile);
    initUI();
};
