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
 * For collecting WebAuthn authenticator details on factor setup
 *
 * @module     factor_webauthn/register
 * @copyright  Catalyst IT
 * @author     Alex Morris <alex.morris@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'factor_webauthn/utils',
    'core/prefetch',
    'core/str',
    'core/toast',
], function(
    utils,
    Prefetch,
    Str,
    Toast,
) {

    /**
     * Register the security key.
     *
     * @param {*} createArgs
     */
    async function registerSecurityKey(createArgs) {
        try {
            if (!navigator.credentials || !navigator.credentials.create) {
                throw new Error('Browser not supported.');
            }

            if (createArgs.success === false) {
                throw new Error(createArgs.msg || 'unknown error occurred');
            }

            utils.recursiveBase64StrToArrayBuffer(createArgs);
            const cred = await navigator.credentials.create(createArgs);
            const authenticatorResponse = {
                transports: cred.response.getTransports ? cred.response.getTransports() : null,
                clientDataJSON: cred.response.clientDataJSON ?
                    utils.arrayBufferToBase64(cred.response.clientDataJSON) : null,
                attestationObject: cred.response.attestationObject ?
                    utils.arrayBufferToBase64(cred.response.attestationObject) : null,
            };

            const registerSuccess = await Str.getString('registersuccess', 'factor_webauthn');
            await Toast.add(registerSuccess, {type: 'success'});

            document.getElementById('id_response_input').value = JSON.stringify(authenticatorResponse);
            // Enable the submit button so that we can proceed.
            document.getElementById('id_submitbutton').disabled = false;
        } catch (e) {
            const registerError = await Str.getString('registererror', 'factor_webauthn', e.message);
            await Toast.add(registerError, {type: 'warning'});
        }
    }

    return {
        init: function(createArgs) {
            // Disable the submit button until we have registered a security key.
            document.getElementById('id_submitbutton').disabled = true;

            Prefetch.prefetchStrings('factor_webauthn', [
                'registersuccess',
                'registererror',
            ]);

            // Register event listeners.
            createArgs = JSON.parse(createArgs);
            document.getElementById('factor_webauthn-register').addEventListener('click', function() {
                registerSecurityKey(createArgs);
            });
            document.getElementById('factor_webauthn-register').addEventListener('keypress', function() {
                registerSecurityKey(createArgs);
            });
        }
    };
});
