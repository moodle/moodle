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
 * Class that defines the bulk action for general actions in the assignment grading page.
 *
 * @module     mod_assign/bulkactions/grading/general_action
 * @copyright  2024 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import BulkAction from 'core/bulkactions/bulk_action';
import Templates from 'core/templates';
import SaveCancelModal from 'core/modal_save_cancel';
import ModalEvents from 'core/modal_events';

const Selectors = {
    selectBulkItemCheckbox: 'input[type="checkbox"][name="selectedusers"]:checked',
};

export default class extends BulkAction {

    /** @type {string} The action key. */
    actionKey;

    /** @type {number} The course module ID. */
    #cmid;

    /** @type {Promise<string>} The action button's icon. */
    #buttonIcon;

    /** @type {Promise<string>} The action button's label. */
    #buttonLabel;

    /** @type {Promise<string>} Title of the confirmation dialog. */
    #confirmationTitle;

    /** @type {Promise<string>} Question of the confirmation dialog. */
    #confirmationQuestion;

    /** @type {Promise<string>} Text for the confirmation yes button. */
    #confirmationYes;

    /** @type {string} The session key. */
    #sesskey;

    /**
     * The class constructor.
     *
     * @param {int} cmid The course module ID.
     * @param {string} sesskey The session key.
     * @param {string} actionKey The action key.
     * @param {Promise<string>} buttonLabel The action button's label.
     * @param {Promise<string>} buttonIcon The action button's icon.
     * @param {Promise<string>} confirmationTitle Title of the confirmation dialog.
     * @param {Promise<string>} confirmationQuestion Question of the confirmation dialog.
     * @param {Promise<string>} confirmationYes Text for the confirmation yes button.
     */
    constructor(cmid, sesskey, actionKey, buttonLabel, buttonIcon, confirmationTitle, confirmationQuestion, confirmationYes) {
        super();
        this.#cmid = cmid;
        this.#sesskey = sesskey;
        this.actionKey = actionKey;
        this.#buttonLabel = buttonLabel;
        this.#buttonIcon = buttonIcon;
        this.#confirmationTitle = confirmationTitle;
        this.#confirmationQuestion = confirmationQuestion;
        this.#confirmationYes = confirmationYes;
    }

    getBulkActionTriggerSelector() {
        return `[data-type="bulkactions"] [data-action="${this.actionKey}"]`;
    }

    async triggerBulkAction() {
        const selectedUsers = [...document.querySelectorAll(Selectors.selectBulkItemCheckbox)].map(checkbox => checkbox.value);

        const modal = await SaveCancelModal.create({
            title: await this.#confirmationTitle,
            buttons: {
                save: await this.#confirmationYes,
            },
            body: Templates.render('mod_assign/bulkactions/grading/bulk_action_modal_body', {
                text: await this.#confirmationQuestion,
                operation: this.actionKey,
                cmid: this.#cmid,
                selectedusers: selectedUsers.join(','),
                sesskey: this.#sesskey
            }),
            show: true,
            removeOnClose: true,
        });

        // Handle save event.
        modal.getRoot().on(ModalEvents.save, () => {
            modal.getRoot().find('form').submit();
        });
    }

    async renderBulkActionTrigger(showInDropdown, index) {
        return Templates.render('mod_assign/bulkactions/grading/bulk_general_action_trigger', {
            action: this.actionKey,
            title: await this.#buttonLabel,
            icon: await this.#buttonIcon,
            showindropdown: showInDropdown,
            isfirst: index === 0,
        });
    }
}
