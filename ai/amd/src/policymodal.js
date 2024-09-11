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

import Modal from 'core/modal';
import Policy from './policy';
import CustomEvents from 'core/custom_interaction_events';

/**
 * The Javascript module to handle the policy modal.
 *
 * @module     core_ai/policymodal
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export default class PolicyModal extends Modal {
    static TYPE = 'core_ai/policymodal';
    static TEMPLATE = 'core_ai/policymodal';

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

        this.context = modalConfig.context;
        this.setXlarge();
    }

    /**
     * Set the modal to be extra large.
     */
    setXlarge() {
        this.getModal().addClass('modal-xl');
    }

    /**
     * Handle click events within the policy modal.
     */
    registerEventListeners() {
        super.registerEventListeners();
        this.registerCloseOnSave();
        this.registerCloseOnCancel();

        this.getModal().on(CustomEvents.events.activate, this.getActionSelector('save'), (e) => {
            e.preventDefault();
            Policy.acceptPolicy();
        });
    }
}
