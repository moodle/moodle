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
 * Modal for deleting an override with the option to recalculate penalties.
 *
 * @module     `mod_assign/override_delete_modal
 * @copyright  2025 Catalyst IT Australia Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as CustomEvents from 'core/custom_interaction_events';
import Config from 'core/config';
import Modal from 'core/modal';

const SELECTORS = {
    DELETE_BUTTONS: '.delete-override',
    RECACULATION_CHECKBOX: '#recalculatepenalties',
};

/**
 * Custom Modal
 */
export default class OverrideDeleteModal extends Modal {
    static TYPE = "mod_assign/override_delete_modal";
    static TEMPLATE = "mod_assign/override_delete_modal";

    /**
     * Configure the modal.
     *
     * @param {Object} modalConfig
     */
    configure(modalConfig) {
        // Add question modals are always large.
        modalConfig.large = true;

        // Always show on creation.
        modalConfig.show = true;
        modalConfig.removeOnClose = true;

        // Apply standard configuration.
        super.configure(modalConfig);

        this.setOverrideId(modalConfig.overrideId);
        this.setSessionKey(modalConfig.sessionKey);
    }

    /**
     * Constructor.
     * Set required data to null.
     *
     * @param {HTMLElement} root
     */
    constructor(root) {
        super(root);

        // Recalculate penalties checkbox.
        this.recalculationCheckbox = this.getModal().find(SELECTORS.RECACULATION_CHECKBOX);

        // Data.
        this.setOverrideId(null);
        this.setSessionKey(null);
    }

    /**
     * Set the override id.
     *
     * @param {number} id The override id.
     */
    setOverrideId(id) {
        this.overrideId = id;
    }

    /**
     * Get the override id.
     *
     * @returns {*}
     */
    getOverrideId() {
        return this.overrideId;
    }

    /**
     * Set the session key.
     *
     * @param {string} key
     */
    setSessionKey(key) {
        this.sessionKey = key;
    }

    /**
     * Get the session key.
     *
     * @returns {*}
     */
    getSessionKey() {
        return this.sessionKey;
    }

    /**
     * Register events.
     *
     */
    registerEventListeners() {
        // Apply parent event listeners.
        super.registerEventListeners(this);

        // Register to close on cancel.
        this.registerCloseOnCancel();

        // Register the delete action.
        this.getModal().on(CustomEvents.events.activate, this.getActionSelector('delete'), () => {
            this.deleteOverride();
        });
    }

    /**
     * Delete a override.
     *
     */
    deleteOverride() {
        // Check if the recalculation checkbox is checked.
        const recalculate = this.recalculationCheckbox.prop('checked');

        // Redirect to the delete URL.
        const targetUrl = new URL(`${Config.wwwroot}/mod/assign/overridedelete.php`);
        targetUrl.searchParams.append('id', this.getOverrideId());
        targetUrl.searchParams.append('sesskey', this.getSessionKey());
        targetUrl.searchParams.append('confirm', 1);

        if (recalculate) {
            targetUrl.searchParams.append('recalculate', 1);
        }

        window.location.href = targetUrl.href;
    }
}
