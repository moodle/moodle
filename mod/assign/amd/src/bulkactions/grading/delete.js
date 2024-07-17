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
 * Class that defines the bulk action for removing submissions in the assignment grading page.
 *
 * @module     mod_assign/bulkactions/grading/delete
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

    /** @type {string} The session key. */
    #sesskey;

    /**
     * The class constructor.
     *
     * @param {number} cmid The course module ID.
     * @param {string} sesskey The session key.
     */
    constructor(cmid, sesskey) {
        super();
        this.#cmid = cmid;
        this.#sesskey = sesskey;
    }

    getBulkActionTriggerSelector() {
        return 'button[data-action="removesubmission"]';
    }

    async triggerBulkAction() {
        Notification.deleteCancelPromise(
            getString('removesubmission', 'mod_assign'),
            getString('batchoperationconfirmremovesubmission', 'mod_assign'),
            getString('batchoperationremovesubmission', 'mod_assign'),
        ).then(() => {
            const selectedUsers = [...document.querySelectorAll(Selectors.selectBulkItemCheckbox)].map(checkbox => checkbox.value);
            const url = new URL(window.location.href);
            url.searchParams.set('id', this.#cmid);

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url.toString();

            // Create hidden inputs for the form.
            ((form, hiddenInputs) => {
                for (const [name, value] of Object.entries(hiddenInputs)) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = value;
                    form.appendChild(input);
                }
            })(form, {
                action: 'gradingbatchoperation',
                operation: 'removesubmission',
                selectedusers: selectedUsers.join(','),
                sesskey: this.#sesskey,
            });

            // Append the form to the body, submit it, and then remove it from the DOM.
            document.body.appendChild(form);
            form.submit();
            form.remove();

            return;
        }).catch(() => {
            return;
        });
    }

    async renderBulkActionTrigger() {
        return Templates.render('mod_assign/bulkactions/grading/bulk_delete_trigger', {});
    }
}
