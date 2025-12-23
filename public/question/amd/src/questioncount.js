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
 * Question count badge with asynchronous loading.
 *
 * @module     core_question/questioncount
 * @copyright  2024 Catalyst IT Europe Ltd.
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getString} from 'core/str';
import Fetch from 'core/fetch';
import Notification from 'core/notification';
import LoadingIcon from 'core/loadingicon';

const SELECTORS = {
    COUNT_CONTAINER: '.questioncount',
    COUNT_BADGE: (cmid) => `.questioncount[data-cmid='${cmid}'] .badge`,
};

export const fetchCounts = async(courseId) => {
    const countContainer = document.querySelector(SELECTORS.COUNT_CONTAINER);
    const endpoint = ['bank', courseId, 'question_counts'];
    const loadingPromise = LoadingIcon.addIconToContainerWithPromise(countContainer);
    try {
        const response = await Fetch.performGet('core_question', endpoint.join('/'));
        const questionCounts = await response.json();
        for (const [cmid, count] of Object.entries(questionCounts.counts)) {
            const countBadge = document.querySelector(SELECTORS.COUNT_BADGE(cmid));
            if (countBadge) {
                countBadge.innerText = await getString('questioncount', 'question', count);
                countBadge.classList.remove('d-none');
            }
        }
    } catch (ex) {
        if (typeof ex === 'string') {
            Notification.alert(getString('error', 'error'), ex);
        } else {
            Notification.exception(ex);
        }
    } finally {
        loadingPromise.resolve();
    }
};
