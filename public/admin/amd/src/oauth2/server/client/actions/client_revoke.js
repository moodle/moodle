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
 * Revoke OAuth2 client confirmation action.
 *
 * @module     core_admin/oauth2/server/client/actions/client_revoke
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import baseClientAction from 'core_admin/oauth2/server/client/actions/base_client_action';
import {getString} from 'core/str';
import Fetch from 'core/fetch';
import * as reportSelectors from 'core_reportbuilder/local/selectors';
import {dispatchEvent} from 'core/event_dispatcher';
import * as reportEvents from 'core_reportbuilder/local/events';

class RevokeClientAction extends baseClientAction {
    /**
     * Return the CSS selector used for triggering the action modal.
     *
     * @returns {string} The CSS selector used for triggering the action modal.
     */
    getActionSelector() {
        return '[data-action="client-revoke"]';
    }

    /**
     * Set the execution of the confirmation action.
     */
    async executeConfirmAction() {
        const clientId = this.target.dataset.id;

        // Revoke the client.
        await Fetch.performPost(
            'core_admin',
            `oauth2/server/clients/${clientId}/revoke`,
            {}
        );

        // Reload the report table to reflect the new state.
        const reportElement = document.querySelector(reportSelectors.regions.report);
        dispatchEvent(reportEvents.tableReload, {}, reportElement);
    }

    /**
     * Get the title for the confirmation modal.
     *
     * @returns {Promise<string>} Resolved title string or HTML text.
     */
    async getTitleText() {
        return await getString('oauth2server_clientrevokeactiontitle', 'admin', this.target.dataset.name);
    }

    /**
     * Get the body for the confirmation modal.
     *
     * @returns {Promise<string>} Resolved body string or HTML text.
     */
    async getBody() {
        return await getString('oauth2server_clientrevokeactiondesc', 'admin');
    }

    /**
     * Get the confirmation button text for the confirmation modal.
     *
     * @returns {Promise<string>} Resolved button string text.
     */
    async getConfirmationButtonText() {
        return await getString('oauth2server_clientrevoke', 'admin');
    }

    /**
     * Determine if the confirmation modal should have destructive styling.
     *
     * @returns {boolean} True if confirmation should trigger a destructive styling modal.
     */
    isDestructive() {
        return false;
    }
}

/**
 * Initialize the client revoke action.
 */
export const init = () => {
    (new RevokeClientAction()).init();
};
