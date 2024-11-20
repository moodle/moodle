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
 * AI text modal for Tiny.
 *
 * @module      tiny_aiplacement/textmodal
 * @copyright   2024 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Modal from 'core/modal';
import ModalEvents from 'core/modal_events';

export default class TextModal extends Modal {
    static TYPE = 'tiny_aiplacement/textmodal';
    static TEMPLATE = 'tiny_aiplacement/textmodal';

    /**
     * Register event listeners.
     */
    registerEventListeners() {
        // Call the parent registration.
        super.registerEventListeners();

        // Register to close on save/cancel.
        this.registerCloseOnSave();
        this.registerCloseOnCancel();

        this.getRoot().on(ModalEvents.outsideClick, (e) => {
            // Prevent closing the modal when clicking outside of it.
            e.preventDefault();
        });
    }

    /**
     * Configure the modal.
     *
     * @param {object} modalConfig The modal configuration.
     */
    configure(modalConfig) {
        super.configure({
            ...modalConfig,
            show: true,
            removeOnClose: true,
        });

        // Add modal extra class.
        this.getModal().addClass('tiny_aiplacement_modal');
        this.setXlarge();
    }

    /**
     * Set the modal to be extra large.
     */
    setXlarge() {
        this.getModal().addClass('modal-xl');
    }
}
