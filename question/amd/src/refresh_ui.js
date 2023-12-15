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
 * Question bank UI refresh utility
 *
 * @module    core_question/refresh_ui
 * @copyright 2023 Catalyst IT Europe Ltd.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Fragment from 'core/fragment';
import Templates from 'core/templates';

export default {
    /**
     * Reload the question bank UI, retaining the current filters and sort data.
     *
     * @param {Element} uiRoot The root element of the UI to be refreshed. Must contain "component", "callback" and "contextid" in
     *     its data attributes, to be passed to the Fragment API.
     * @param {URL} returnUrl The url of the current page, containing filter and sort parameters.
     * @return {Promise} Resolved when the refresh is complete.
     */
    refresh: (uiRoot, returnUrl) => {
        return new Promise((resolve, reject) => {
            const fragmentData = uiRoot.dataset;
            const viewData = {};
            const sortData = {};
            if (returnUrl) {
                returnUrl.searchParams.forEach((value, key) => {
                    // Match keys like 'sortdata[fieldname]' and convert them to an array,
                    // because the fragment API doesn't like non-alphanum argument keys.
                    const sortItem = key.match(/sortdata\[([^\]]+)\]/);
                    if (sortItem) {
                        // The item returned by sortItem.pop() is the contents of the matching group, the field name.
                        sortData[sortItem.pop()] = value;
                    } else {
                        viewData[key] = value;
                    }
                });
            }
            viewData.sortdata = JSON.stringify(sortData);
            // We have to use then() there, as loadFragment doesn't appear to work with await.
            Fragment.loadFragment(fragmentData.component, fragmentData.callback, fragmentData.contextid, viewData)
                .then((html, js) => {
                    Templates.replaceNode(uiRoot, html, js);
                    resolve();
                    return html;
                })
                .catch(reject);
        });
    }
};
