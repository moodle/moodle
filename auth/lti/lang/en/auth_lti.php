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
 * Strings for component 'auth_lti', language 'en'.
 *
 * @package auth_lti
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['accountcreatedsuccess'] = 'Your account has been created and is now ready to use.';
$string['accountlinkedsuccess'] = 'Your existing account has been successfully linked.';
$string['auth_ltidescription'] = 'The LTI authentication plugin, together with the \'Publish as LTI tool\' enrolment plugin, allows remote users to access selected courses and activities. In other words, Moodle functions as an LTI tool provider.';
$string['cannotcreateaccounts'] = 'Account creation is currently prohibited on this site.';
$string['createaccount'] = 'Create account';
$string['createaccountforme'] = 'Create an account for me';
$string['createnewaccount'] = 'I\'d like to create a new account';
$string['currentlyloggedinas'] = 'You are currently logged in as:';
$string['firstlaunchnotice'] = 'It looks like this is your first time here. Please select from one of the following account options.';
$string['getstartedwithnewaccount'] = 'Get started with a new account';
$string['haveexistingaccount'] = 'I have an existing account';
$string['linkthisaccount'] = 'Link this account';
$string['mustbeloggedin'] = 'Sign in to link your existing account';
$string['pluginname'] = 'LTI';
$string['privacy:metadata:auth_lti'] = 'LTI authentication';
$string['privacy:metadata:auth_lti:authsubsystem'] = 'This plugin is connected to the authentication subsystem.';
$string['privacy:metadata:auth_lti:issuer'] = 'The issuer URL identifying the platform to which the linked user belongs.';
$string['privacy:metadata:auth_lti:issuer256'] = 'The SHA256 hash of the issuer URL.';
$string['privacy:metadata:auth_lti:sub'] = 'The subject string identifying the user on the issuer.';
$string['privacy:metadata:auth_lti:sub256'] = 'The SHA256 hash of the subject string identifying the user on the issuer.';
$string['privacy:metadata:auth_lti:tableexplanation'] = 'LTI accounts linked to a user\'s Moodle account.';
$string['privacy:metadata:auth_lti:timecreated'] = 'The timestamp when the user account was linked to the LTI login.';
$string['privacy:metadata:auth_lti:timemodified'] = 'The timestamp when this record was modified.';
$string['privacy:metadata:auth_lti:userid'] = 'The ID of the user account which the LTI login is linked to';
$string['provisioningmodeauto'] = 'New accounts only (automatic)';
$string['provisioningmodenewexisting'] = 'Existing and new accounts (prompt)';
$string['provisioningmodeexistingonly'] = 'Existing accounts only (prompt)';
$string['useexistingaccount'] = 'Use existing account';
$string['welcome'] = 'Welcome!';

// Deprecated since Moodle 4.4.
$string['firstlaunchnoauthnotice'] = 'To link your existing account you must be logged in to the site. Please log in to the site in a new tab/window and then relaunch the tool here. For further information, see the documentation <a href="{$a}" target="_blank">Publish as LTI tool</a>.';
