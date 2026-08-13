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
 * Module for the OAuth2 client creation form.
 *
 * @module     core_admin/oauth2/server/client/create_client_form
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const Selectors = {
    clientTypeRadioInputs: 'input[name="clienttype"]',
    authCodeFlowCheckbox: 'input[name="flow_auth_code"]',
    enablePkceCheckbox: 'input[name="enablepkce"]',
    primaryFlowsSeparator: '.primaryflowsgroup-separator',
};

/**
 * Determine if the selected client type is public.
 *
 * @returns {boolean} True if the selected client type is public, false otherwise.
 */
const isPublicClient = () => document.querySelector(
    Selectors.clientTypeRadioInputs + ':checked'
)?.value === '0';

/**
 * Synchronize defaults based on the selected client type.
 *
 * Public clients always use Authorization Code and PKCE.
 */
const syncPublicClientDefaults = () => {
    if (!isPublicClient()) {
        return;
    }

    const authCodeFlowCheckboxElement = document.querySelector(Selectors.authCodeFlowCheckbox);
    const enablePkceCheckboxElement = document.querySelector(Selectors.enablePkceCheckbox);

    if (authCodeFlowCheckboxElement) {
        authCodeFlowCheckboxElement.checked = true;
        authCodeFlowCheckboxElement.dispatchEvent(new Event('change', {bubbles: true}));
    }

    if (enablePkceCheckboxElement) {
        enablePkceCheckboxElement.checked = true;
        enablePkceCheckboxElement.dispatchEvent(new Event('change', {bubbles: true}));
    }
};

/**
 * Synchronize the display of the primary flows separator based on the selected client type.
 *
 * When the selected client type is public, only one Primary Flow option is available, so the divider is hidden.
 * For client types that support multiple Primary Flows, the divider is shown.
 */
const syncPrimaryFlowsSeparatorVisibility = () => {
    const divider = document.querySelector(Selectors.primaryFlowsSeparator);

    if (divider) {
        divider.classList.toggle('d-none', isPublicClient());
    }
};

/**
 * Initialization method.
 */
export const init = () => {
    const clientTypeRadioInputElements = document.querySelectorAll(Selectors.clientTypeRadioInputs);

    if (!clientTypeRadioInputElements.length) {
        return;
    }

    // When a client type radio input is changed, we need to sync the default values for public clients and also toggle
    // the visibility of the primary flows divider.
    clientTypeRadioInputElements.forEach(input => {
        input.addEventListener('change', () => {
            syncPublicClientDefaults();
            syncPrimaryFlowsSeparatorVisibility();
        });
    });
    // Synchronize the initial state.
    syncPublicClientDefaults();
    syncPrimaryFlowsSeparatorVisibility();
};
