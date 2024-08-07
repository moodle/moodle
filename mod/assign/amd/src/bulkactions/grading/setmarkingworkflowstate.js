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
 * Class that defines the bulk action for setting marking workflow state in the assignment grading page.
 *
 * @module     mod_assign/bulkactions/grading/setmarkingworkflowstate
 * @copyright  2024 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import BulkAction from 'core/bulkactions/bulk_action';
import Notification from 'core/notification';
import Templates from 'core/templates';
import {getString} from 'core/str';

const Selectors = {
    selectBulkItemCheckbox: 'input[type="checkbox"][name="selectedusers"]:checked',
};

export default class extends BulkAction {
    /** @type {number} The course module ID. */
    #cmid;

    /**
     * The class constructor.
     *
     * @param {number} cmid The course module ID.
     */
    constructor(cmid) {
        super();
        this.#cmid = cmid;
    }

    getBulkActionTriggerSelector() {
        return '[data-type="bulkactions"] [data-action="setmarkingworkflowstate"]';
    }

    async triggerBulkAction() {
        Notification.saveCancelPromise(
            getString('setmarkingworkflowstate', 'mod_assign'),
            getString('batchoperationconfirmsetmarkingworkflowstate', 'mod_assign'),
            getString('batchoperationsetmarkingworkflowstate', 'mod_assign'),
        ).then(() => {
            const selectedUsers = [...document.querySelectorAll(Selectors.selectBulkItemCheckbox)].map(checkbox => checkbox.value);
            const url = new URL(window.location.href);
            url.searchParams.set('id', this.#cmid);
            url.searchParams.set('action', 'viewbatchsetmarkingworkflowstate');
            url.searchParams.set('selectedusers', selectedUsers.join(','));
            window.location = url;

            return;
        }).catch(() => {
            return;
        });
    }

    async renderBulkActionTrigger(showInDropdown) {
        return Templates.render('mod_assign/bulkactions/grading/bulk_setmarkingworkflowstate_trigger', {
            showindropdown: showInDropdown,
        });
    }
}
