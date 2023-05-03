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
 * Contain the logic for the delete/cancel modal.
 *
 * @module     core/modal_delete_cancel
 * @copyright  2022 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import Modal from 'core/modal';
import Notification from 'core/notification';

/**
 * The Delete/Cancel Modal.
 *
 * @class
 * @extends module:core/modal
 */
export default class extends Modal {
    constructor(root) {
        super(root);

        if (!this.getFooter().find(this.getActionSelector('delete')).length) {
            Notification.exception({message: 'No delete button found'});
        }

        if (!this.getFooter().find(this.getActionSelector('cancel')).length) {
            Notification.exception({message: 'No cancel button found'});
        }
    }

    /**
     * Register all event listeners.
     */
    registerEventListeners() {
        // Call the parent registration.
        super.registerEventListeners();

        // Register to close on delete/cancel.
        this.registerCloseOnDelete();
        this.registerCloseOnCancel();
    }

    /**
     * Override parent implementation to prevent changing the footer content.
     */
    setFooter() {
        Notification.exception({message: 'Can not change the footer of a delete cancel modal'});
    }

    /**
     * Set the title of the delete button.
     *
     * @param {String|Promise} value The button text, or a Promise which will resolve it
     * @returns{Promise}
     */
    setDeleteButtonText(value) {
        return this.setButtonText('delete', value);
    }
}
