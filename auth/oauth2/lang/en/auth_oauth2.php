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
 * Strings for component 'auth_oauth2', language 'en'.
 *
 * @package   auth_oauth2
 * @copyright 2017 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['auth_oauth2description'] = 'OAuth 2 standards based authentication';
$string['auth_oauth2settings'] = 'OAuth 2 authentication settings.';
$string['notloggedin'] = 'The login attempt failed.';
$string['plugindescription'] = 'This authentication plugin displays a list of the configured identity providers on the moodle login page. Selecting an identity provider allows users to login with their credentials from an OAuth 2 provider.';
$string['pluginname'] = 'OAuth 2';
$string['emailconfirmlink'] = 'Link your accounts';
$string['emailconfirmlinksent'] = '<p>An existing account was found with this email address but it is not linked yet.</p>
   <p>The accounts must be linked before you can login.</p>
   <p>An email should have been sent to your address at <b>{$a}</b></p>
   <p>It contains easy instructions to link your accounts.</p>
   <p>If you continue to have difficulty, contact the site administrator.</p>';
$string['oauth2:managelinkedlogins'] = 'Manage own linked login accounts';
$string['linkedlogins'] = 'Linked logins';
$string['accountexists'] = 'A user already exists on this site with this username. If this is your account, login manually and link this link from your preferences page.';
$string['linkedloginshelp'] = 'Help with linked logins.';
$string['notwhileloggedinas'] = 'Linked logins cannot be managed while logged in as another user.';
$string['issuer'] = 'OAuth 2 Service';
$string['info'] = 'External account';
$string['createnewlinkedlogin'] = 'Link a new account ({$a})';
$string['confirmationpending'] = 'This account is pending email confirmation.';
$string['emailnotallowed'] = 'The email address is not permitted at this site.';
$string['createaccountswarning'] = 'This authentication plugin allows users to create accounts on your site. You may want to enable the setting "authpreventaccountcreation" if you use this plugin.';
$string['selfregistrationdisabled'] = 'No matching account could be found on this site, and this site does not allow self registration.';
$string['confirmlinkedloginemail'] = 'Hi {$a->fullname},

A request has been made to link the {$a->issuername} login
{$a->linkedemail} to your account at \'{$a->sitename}\'
using your email address.

To confirm this request and link these logins, please go to this web address:

{$a->link}

In most mail programs, this should appear as a blue link
which you can just click on.  If that doesn\'t work,
then cut and paste the address into the address
line at the top of your web browser window.

If you need help, please contact the site administrator,
{$a->admin}';
$string['confirmlinkedloginemailsubject'] = '{$a}: linked login confirmation';
$string['confirmaccountemail'] = 'Hi {$a->fullname},

A new account has been requested at \'{$a->sitename}\'
using your email address.

To confirm your new account, please go to this web address:

{$a->link}

In most mail programs, this should appear as a blue link
which you can just click on.  If that doesn\'t work,
then cut and paste the address into the address
line at the top of your web browser window.

If you need help, please contact the site administrator,
{$a->admin}';
$string['confirmaccountemailsubject'] = '{$a}: account confirmation';
