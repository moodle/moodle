<?php
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
 * Language strings.
 *
 * @package     factor_webauthn
 * @author      Alex Morris <alex.morris@catalyst.net.nz>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:manage'] = 'Manage security key';
$string['action:revoke'] = 'Remove security key';
$string['authenticator:ble'] = 'BLE';
$string['authenticator:hybrid'] = 'Hybrid';
$string['authenticator:internal'] = 'Internal';
$string['authenticator:nfc'] = 'NFC';
$string['authenticator:usb'] = 'USB';
$string['authenticatorname'] = 'Security key name';
$string['error'] = 'Failed to authenticate';
$string['error:alreadyregistered'] = 'This security key secret has already been registered.';
$string['info'] = 'Use a physical security key or fingerprint scanner.';
$string['logindesc'] = 'Click continue to use your security key.';
$string['loginoption'] = 'Use security key';
$string['loginskip'] = 'I don\'t have my security key';
$string['loginsubmit'] = 'Continue';
$string['logintitle'] = 'Verify it\'s you by security key';
$string['pluginname'] = 'Security key';
$string['privacy:metadata'] = 'The Security key factor plugin does not store any personal data.';
$string['register'] = 'Register security key';
$string['registererror'] = 'Couldn\'t register security key: {$a}';
$string['registersuccess'] = 'Security key registered.';
$string['replacefactor'] = 'Replace security key';
$string['replacefactorconfirmation'] = 'Replace \'{$a}\' security key?';
$string['revokefactorconfirmation'] = 'Remove \'{$a}\' security key?';
$string['settings:authenticatortypes'] = 'Types of authenticator';
$string['settings:authenticatortypes_help'] = 'Toggle certain types of authenticators';
$string['settings:description'] = '<p>Users authenticate using a physical security key, such as a USB or NFC token, or a biometric method like a fingerprint. During login, they must physically use their security key to verify their identity.</p>
<p>Users will need to set up their own security keys first.</p>';
$string['settings:shortdescription'] = 'Require users to use a security key, like a USB or NFC token, or a biometric method, during login.';
$string['settings:userverification'] = 'User verification';
$string['settings:userverification_help'] = 'Serves to ensure the person authenticating is in fact who they say they are. User verification can take various forms, such as password, PIN, fingerprint, etc.';
$string['setupfactor'] = 'Set up security key';
$string['setupfactorbutton'] = 'Set up';
$string['setupfactorbuttonadditional'] = 'Add security key';
$string['setupfactor:instructionsregistersecuritykey'] = '2. Register a security key.';
$string['setupfactor:instructionssecuritykeyname'] = '1. Give your key a name.';
$string['setupfactor:intro'] = 'A security key is a physical device that you can use to authenticate yourself. Security keys can be USB tokens, Bluetooth devices, or event built-in fingerprint scanners on your phone or computer.';
$string['setupfactor:securitykeyinfo'] = 'This helps you identify which security key you are using.';
$string['summarycondition'] = 'using a WebAuthn supported authenticator';
$string['managefactor'] = 'Manage security key';
$string['managefactorbutton'] = 'Manage';
$string['manageinfo'] = 'You are using \'{$a}\' to authenticate.';
$string['userverification:discouraged'] = 'User verification should not be employed, for example to minimize user interaction';
$string['userverification:preferred'] = 'User verification is preferred, authentication will not fail if user verification is missing';
$string['userverification:required'] = 'User verification is required (e.g. by pin). Authentication fails if key does not have user verification';
