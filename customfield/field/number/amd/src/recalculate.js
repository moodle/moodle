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
 * Allows to recalculate a single value on demand
 *
 * @module     customfield_number/recalculate
 * @author     2024 Marina Glancy
 * @copyright  2024 Moodle Pty Ltd <support@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import Notification from 'core/notification';
import {addIconToContainer} from 'core/loadingicon';
import Pending from 'core/pending';

const SELECTORS = {
    wrapper: '[data-fieldtype="wrapper"]',
    value: '[data-fieldtype="value"]',
    link: '[data-fieldtype="link"]',
};

let initialised = false;

/**
 * Init
 */
export function init() {
    if (initialised) {
        return;
    }

    document.addEventListener('click', (e) => {
        const target = e.target.closest(SELECTORS.wrapper + " " + SELECTORS.link);
        if (!target) {
            return;
        }
        const el = target.closest(SELECTORS.wrapper).querySelector(SELECTORS.value);
        if (!el) {
            return;
        }
        e.preventDefault();

        const {fieldid, instanceid} = target.dataset;

        const pendingPromise = new Pending('recalculate_customfield_number');
        addIconToContainer(el).then(() => {
            return Ajax.call([{
                methodname: 'customfield_number_recalculate_value',
                args: {fieldid, instanceid}
            }])[0];
        }).then((data) => {
            el.innerHTML = data.value;
            return pendingPromise.resolve();
        }).catch(Notification.exception);
    });

    initialised = true;
}
