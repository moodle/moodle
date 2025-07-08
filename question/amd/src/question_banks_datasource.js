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
 * Autocomplete data source for shared question banks.
 *
 * @module     core_question/question_banks_datasource
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call as fetchMany} from 'core/ajax';
import Notification from 'core/notification';

export default {

    transport: function(selector, query, callback) {
        const element = document.querySelector(selector);
        const contextId = element.dataset.contextid;
        let requiredcapabilities = ['use'];
        if (element.dataset.requiredcapabilities) {
            requiredcapabilities = JSON.parse(element.dataset.requiredcapabilities);
        }

        if (!contextId) {
            throw new Error('The attribute data-contextid is required on ' + selector);
        }

        fetchMany([{
            methodname: 'core_question_search_shared_banks',
            args: {
                contextid: contextId,
                search: query,
                requiredcapabilities: requiredcapabilities,
            },
        }])[0]
        .then(callback)
        .catch(Notification.exception);
    },

    processResults: (selector, results) => {
        return results.sharedbanks;
    },
};
