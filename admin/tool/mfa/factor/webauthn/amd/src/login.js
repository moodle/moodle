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
 * For collecting WebAuthn authenticator details on login
 *
 * @module     factor_webauthn/login
 * @copyright  Catalyst IT
 * @author     Alex Morris <alex.morris@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['factor_webauthn/utils'], function(utils) {
    return {
        init: function(getArgs) {
            const idSubmitButton = document.getElementById('id_submitbutton');
            if (idSubmitButton) {
                idSubmitButton.addEventListener('click', async function(e) {
                    e.preventDefault();
                    if (!navigator.credentials || !navigator.credentials.create) {
                        throw new Error('Browser not supported.');
                    }

                    getArgs = JSON.parse(getArgs);

                    if (getArgs.success === false) {
                        throw new Error(getArgs.msg || 'unknown error occured');
                    }

                    utils.recursiveBase64StrToArrayBuffer(getArgs);

                    const cred = await navigator.credentials.get(getArgs);

                    const authenticatorAttestationResponse = {
                        id: cred.rawId ? utils.arrayBufferToBase64(cred.rawId) : null,
                        clientDataJSON:
                            cred.response.clientDataJSON ? utils.arrayBufferToBase64(cred.response.clientDataJSON) : null,
                        authenticatorData:
                            cred.response.authenticatorData ? utils.arrayBufferToBase64(cred.response.authenticatorData) : null,
                        signature: cred.response.signature ? utils.arrayBufferToBase64(cred.response.signature) : null,
                        userHandle: cred.response.userHandle ? utils.arrayBufferToBase64(cred.response.userHandle) : null
                    };

                    const responseInput = document.getElementById('id_response_input');
                    responseInput.value = JSON.stringify(authenticatorAttestationResponse);
                    responseInput.form.submit();
                });
            }
        }
    };
});
