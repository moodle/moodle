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
 * Strings for component 'factor_totp', language 'en'.
 *
 * @package     factor_totp
 * @subpackage  tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:manage'] = 'Manage time-based one-time password (TOTP) authenticator';
$string['action:revoke'] = 'Remove time-based one-time password (TOTP) authenticator';
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
$string['info'] = 'Generate a verification code using an authenticator app.';
$string['logindesc'] = 'Use the authenticator app in your mobile device to generate a code.';
$string['loginoption'] = 'Use Authenticator application';
$string['loginskip'] = 'I don\'t have my device';
$string['loginsubmit'] = 'Continue';
$string['logintitle'] = 'Verify it\'s you by mobile app';
$string['managefactor'] = 'Manage authenticator app';
$string['managefactorbutton'] = 'Manage';
$string['manageinfo'] = 'You are using \'{$a}\' to authenticate.';
$string['pluginname'] = 'Authenticator app';
$string['privacy:metadata'] = 'The Authenticator app factor plugin does not store any personal data.';
$string['replacefactor'] = 'Replace authenticator app';
$string['replacefactorconfirmation'] = 'Replace \'{$a}\' authenticator app?';
$string['revokefactorconfirmation'] = 'Remove \'{$a}\' authenticator app?';
$string['settings:description'] = 'Users will need an authenticator app installed on their mobile devices to generate a code, which they must enter during login.';
$string['settings:shortdescription'] = 'Require users to enter a code from an authenticator app on their devices during login.';
$string['settings:totplink'] = 'Show mobile app setup link';
$string['settings:totplink_help'] = 'If enabled the user will see a 3rd setup option with a direct otpauth:// link';
$string['settings:window'] = 'TOTP verification window';
$string['settings:window_help'] = 'The window of TOTP acts as time drift and specifies how long each code is valid for.
    The period, which is the time between newly generated codes, is 30 seconds.
    If the window is 15 (the default) and the current timestamp is 147682209, the OTP tested are within 147682194 (147682209 - 15), 147682209 and 147682224 (147682209 + 15).
    The window shall be lower than 30. Therefore, this test includes the previous OTP but not the next one.
    You can set this to a higher value (up to 29) as a workaround if your user\'s device clocks are often slightly wrong.';
$string['setupfactor'] = 'Set up authenticator app';
$string['setupfactorbutton'] = 'Set up';
$string['setupfactor:account'] = 'Account:';
$string['setupfactor:devicename'] = 'Device name';
$string['setupfactor:devicenameinfo'] = 'This helps you identify which device receives the verification code.';
$string['setupfactor:enter'] = 'Enter details manually';
$string['setupfactor:instructionsdevicename'] = '1. Give your device a name.';
$string['setupfactor:instructionsscan'] = '2. Scan the QR code with your authenticator app.';
$string['setupfactor:instructionsverification'] = '3. Enter the verification code.';
$string['setupfactor:intro'] = 'To set up this method, you need to have a device with an authenticator app. If you don\'t have an app, you can download one. For example, <a href="https://2fas.com/" target="_blank">2FAS Auth</a>, <a href="https://freeotp.github.io/" target="_blank">FreeOTP</a>, Google Authenticator, Microsoft Authenticator or Twilio Authy.';
$string['setupfactor:key'] = 'Secret key: ';
$string['setupfactor:link'] = 'Or enter details manually.';
$string['setupfactor:link_help'] = 'If you are on a mobile device and already have an authenticator app installed this link may work. Note that using TOTP on the same device as you log in on can weaken the benefits of MFA.';
$string['setupfactor:linklabel'] = 'Open app already installed on this device';
$string['setupfactor:mode'] = 'Mode:';
$string['setupfactor:mode:timebased'] = 'Time-based';
$string['setupfactor:scanwithapp'] = 'Scan QR code with your chosen authenticator application.';
$string['setupfactor:verificationcode'] = 'Verification code';
$string['summarycondition'] = 'using a TOTP app';
$string['systimeformat'] = '%l:%M:%S %P %Z';
$string['verificationcode'] = 'Enter your 6 digit verification code';
$string['verificationcode_help'] = 'Open your authenticator app such as Google Authenticator and look for the 6 digit code which matches this site and username';
