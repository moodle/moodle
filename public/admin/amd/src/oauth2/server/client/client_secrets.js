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
 * Module for the OAuth2 client secrets page.
 *
 * @module     core_admin/oauth2/server/client/client_secrets
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import Modal from 'core/modal';
import {getString} from 'core/str';
import Fetch from 'core/fetch';
import Notification from 'core/notification';
import * as reportSelectors from 'core_reportbuilder/local/selectors';
import {dispatchEvent} from 'core/event_dispatcher';
import * as reportEvents from 'core_reportbuilder/local/events';
import ModalEvents from 'core/modal_events';

/**
 * Handles secret generation button click.
 *
 * @param {HTMLButtonElement} button The trigger button.
 * @param {number} maxActiveSecrets The maximum number of active secrets allowed.
 */
const handleGenerateClick = async(button, maxActiveSecrets) => {
    const clientId = button.dataset.clientid;
    button.disabled = true;

    try {
        const createSecretResponse = await Fetch.performPost(
            'core_admin',
            `oauth2/server/clients/${clientId}/secrets/create`,
            {}
        );

        const createSecretData = await createSecretResponse.json();
        await handlePostSecretCreation(button, createSecretData.secret, maxActiveSecrets);
    } catch (error) {
        button.disabled = false;
        Notification.exception(error);
    }
};

/**
 * Whether a new client secret can be created.
 *
 * @param {string} clientId The client ID.
 * @param {number} maxActiveSecrets The maximum number of active secrets allowed.
 */
const canCreateClientSecret = async(clientId, maxActiveSecrets) => {
    const getSecretsResponse = await Fetch.performGet(
        'core_admin',
        `oauth2/server/clients/${clientId}/secrets`,
        {}
    );

    const getSecretsData = await getSecretsResponse.json();
    const secretsCount = Object.keys(getSecretsData?.secrets ?? {}).length;

    return secretsCount < maxActiveSecrets;
};

/**
 * Handles post secret creation logic.
 *
 * @param {HTMLButtonElement} generateSecretButton The trigger button.
 * @param {string} secret The generated secret.
 * @param {number} maxActiveSecrets The maximum number of active secrets allowed.
 * */
const handlePostSecretCreation = async(generateSecretButton, secret, maxActiveSecrets) => {
    const clientId = generateSecretButton.dataset.clientid;
    const clientIdentifier = generateSecretButton.dataset.clientidentifier;

    // Reload the report table to reflect the new secret.
    const reportElement = document.querySelector(reportSelectors.regions.report);
    dispatchEvent(reportEvents.tableReload, {}, reportElement);

    // Generate and show the modal with the generated secret.
    const modalBody = await Templates.render(
        'core_admin/oauth2/server/generated_client_secret',
        {secret: secret, clientidentifier: clientIdentifier}
    );
    const modal = await Modal.create({
        title: await getString('oauth2server_clientsecretgenerated', 'admin'),
        body: modalBody,
    });

    // Listen for when the modal becomes visible.
    modal.getRoot().on(ModalEvents.shown, () => {
        // TODO: MDL-89622 Remove this temporary workaround once the core toast layering is fixed.
        const zIndexModal = window.getComputedStyle(modal.getRoot()[0]).zIndex;
        const toastElement = document.querySelector('.toast-wrapper');
        if (toastElement) {
            toastElement.style.zIndex = parseInt(zIndexModal, 10) + 1;
        }
    });

    modal.show();

    // Handle hidden event.
    modal.getRoot().on(ModalEvents.hidden, function() {
        // Destroy when hidden.
        modal.destroy();
    });

    // Handle the state of other UI components based on whether the maximum number of secrets has been reached.
    const canCreateMoreSecrets = await canCreateClientSecret(clientId, maxActiveSecrets);
    if (!canCreateMoreSecrets) {
        // Hide the 'generate secret' button.
        generateSecretButton.classList.add('d-none');
        // Fetch and display an alert that the maximum number of secrets has been reached.
        const {html, js} = await Templates.renderForPromise(
            'core_admin/oauth2/server/client_secrets_limit_reached_alert',
            {maxsecretsnumber: maxActiveSecrets}
        );
        const secretsAlertContainer = document.getElementById('client-secrets-alert-container');
        if (secretsAlertContainer) {
            Templates.replaceNodeContents(secretsAlertContainer, html, js);
        }
    } else {
        generateSecretButton.disabled = false;
    }
};

/**
 * Initialize event listeners.
 *
 * @param {number} maxActiveSecrets The maximum number of active secrets allowed.
 */
export const init = (maxActiveSecrets) => {
    document.addEventListener('click', async(e) => {
        const btn = e.target.closest('[data-action="generate-secret"]');
        if (btn) {
            e.preventDefault();
            await handleGenerateClick(btn, maxActiveSecrets);
        }
    });
};
