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
 * @package     factor_totp
 * @subpackage  tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:revoke'] = 'Revoke time-based one-time password (TOTP) authenticator';
$string['devicename'] = 'Device label';
$string['devicename_help'] = 'This is the device you have an authenticator app installed on. You can set up multiple devices so this label helps track which ones are being used. You should set up each device with their own unique code so they can be revoked separately.';
$string['devicenameexample'] = 'eg "Work iPhone 11"';
$string['error:alreadyregistered'] = 'This time-based one-time password (TOTP) secret has already been registered.';
$string['error:codealreadyused'] = 'This code has already been used to authenticate. Please wait for a new code to be generated, and try again.';
$string['error:futurecode'] = 'This code is invalid. Please verify the time on your authenticator device is correct and try again.
    Current system time is {$a}.';
$string['error:oldcode'] = 'This code is too old. Please verify the time on your authenticator device is correct and try again.
    Current system time is {$a}.';
$string['error:wrongverification'] = 'Incorrect verification code.';
$string['factorsetup'] = 'App setup';
$string['info'] = '<p>Use any time-based one-time password (TOTP) authenticator app on your device to generate a verification code, even when it is offline.</p>

<p>For example <a href="https://2fas.com/">2FAS Auth</a>, <a href="https://freeotp.github.io/">FreeOTP</a>, Google Authenticator, Microsoft Authenticator or Twilio Authy.</p>

<p>Note: Please ensure your device time and date has been set to "Auto" or "Network provided".</p>';
$string['logindesc'] = 'Use the authenticator app in your mobile device to generate a code.';
$string['loginoption'] = 'Use Authenticator application';
$string['loginskip'] = 'I don\'t have my device';
$string['loginsubmit'] = 'Continue';
$string['logintitle'] = 'Verify it\'s you by mobile app';
$string['pluginname'] = 'Authenticator app';
$string['privacy:metadata'] = 'The Authenticator app factor plugin does not store any personal data.';
$string['settings:totplink'] = 'Show mobile app setup link';
$string['settings:totplink_help'] = 'If enabled the user will see a 3rd setup option with a direct otpauth:// link';
$string['settings:window'] = 'TOTP verification window';
$string['settings:window_help'] = 'How long each code is valid for. You can set this to a higher value as a workaround if your users device clocks are often slightly wrong.
    Rounded down to the nearest 30 seconds, which is the time between new generated codes.';
$string['setupfactor'] = 'TOTP authenticator setup';
$string['setupfactor:account'] = 'Account:';
$string['setupfactor:enter'] = 'Enter details manually:';
$string['setupfactor:key'] = 'Secret key: ';
$string['setupfactor:link'] = '<b> OR </b> open mobile app:';
$string['setupfactor:link_help'] = 'If you are on a mobile device and already have an authenticator app installed this link may work. Note that using TOTP on the same device as you login on can weaken the benefits of MFA.';
$string['setupfactor:linklabel'] = 'Open app already installed on this device';
$string['setupfactor:mode'] = 'Mode:';
$string['setupfactor:mode:timebased'] = 'Time-based';
$string['setupfactor:scan'] = 'Scan QR code:';
$string['setupfactor:scanfail'] = 'Can\'t scan?';
$string['setupfactor:scanwithapp'] = 'Scan QR code with your chosen authenticator application.';
$string['summarycondition'] = 'using a TOTP app';
$string['systimeformat'] = '%l:%M:%S %P %Z';
$string['verificationcode'] = 'Enter your 6 digit verification code';
$string['verificationcode_help'] = 'Open your authenticator app such as Google Authenticator and look for the 6 digit code which matches this site and username';
