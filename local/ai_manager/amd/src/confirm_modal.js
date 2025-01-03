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
 * Confirmation modal for confirming the usage of the AI tools.
 *
 * @module     local_ai_manager/confirm_modal
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Modal from 'core/modal';

export default class ModalConfirm extends Modal {
    static TYPE = 'local_ai_manager/confirm_modal';
    static TEMPLATE = 'local_ai_manager/confirm_modal';

    configure(modalConfig) {
        modalConfig.show = true;
        modalConfig.removeOnClose = true;
        // Button in our template has data-action="cancel", so we use the modal function
        // to register the event for properly closing the modal on click.
        this.registerCloseOnCancel();
        super.configure(modalConfig);
    }
}
