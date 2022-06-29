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

$string['accountexists'] = 'A user already exists on this site with this username. If this is your account, log in by entering your username and password and add it as a linked login via your preferences page.';
$string['auth_oauth2description'] = 'OAuth 2 standards based authentication';
$string['auth_oauth2settings'] = 'OAuth 2 authentication settings.';
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
{$a->admin}

If you did not do this, someone else could be trying to compromise your account.
Please contact the site administrator immediately.';
$string['confirmaccountemailsubject'] = '{$a}: account confirmation';
$string['confirmationinvalid'] = 'The confirmation link is either invalid, or has expired. Please start the login process again to generate a new confirmation email.';
$string['confirmationpending'] = 'This account is pending email confirmation.';
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
{$a->admin}

If you did not do this, someone else could be trying to compromise your account.
Please contact the site administrator immediately.';
$string['confirmlinkedloginemailsubject'] = '{$a}: linked login confirmation';
$string['createaccountswarning'] = 'This authentication plugin allows users to create accounts on your site. You may want to enable the setting "authpreventaccountcreation" if you use this plugin.';
$string['createnewlinkedlogin'] = 'Link a new account ({$a})';
$string['emailconfirmlink'] = 'Link your accounts';
$string['emailconfirmlinksent'] = '<p>An existing account was found with this email address but it is not linked yet.</p>
   <p>The accounts must be linked before you can log in.</p>
   <p>An email should have been sent to your address at <b>{$a}</b>.</p>
   <p>It contains easy instructions to link your accounts.</p>
   <p>If you have any difficulty, contact the site administrator.</p>';
$string['emailpasswordchangeinfo'] = 'Hi {$a->firstname},

Someone (probably you) has requested a new password for your account on \'{$a->sitename}\'.

However your password cannot be reset because you are using your account on another site to log in.

Please log in as before, using the link on the login page.
{$a->admin}';
$string['emailpasswordchangeinfosubject'] = '{$a}: Change password information';
$string['info'] = 'External account';
$string['issuer'] = 'OAuth 2 Service';
$string['issuernologin'] = 'This issuer can not be used to login';
$string['linkedlogins'] = 'Linked logins';
$string['linkedloginshelp'] = 'Help with linked logins';
$string['loginerror_userincomplete'] = 'The user information returned did not contain a username and email address. The OAuth 2 service may be configured incorrectly.';
$string['loginerror_nouserinfo'] = 'No user information was returned. The OAuth 2 service may be configured incorrectly.';
$string['loginerror_invaliddomain'] = 'The email address is not allowed at this site.';
$string['loginerror_authenticationfailed'] = 'The authentication process failed.';
$string['loginerror_cannotcreateaccounts'] = 'An account with your email address could not be found.';
$string['noissuersavailable'] = 'None of the configured OAuth2 services allow you to link login accounts';
$string['notloggedindebug'] = 'The login attempt failed. Reason: {$a}';
$string['notwhileloggedinas'] = 'Linked logins cannot be managed while logged in as another user.';
$string['oauth2:managelinkedlogins'] = 'Manage own linked login accounts';
$string['notenabled'] = 'Sorry, OAuth 2 authentication plugin is not enabled';
$string['plugindescription'] = 'This authentication plugin displays a list of the configured identity providers on the login page. Selecting an identity provider allows users to login with their credentials from an OAuth 2 provider.';
$string['pluginname'] = 'OAuth 2';
$string['alreadylinked'] = 'This external account is already linked to an account on this site';
$string['privacy:metadata:auth_oauth2'] = 'OAuth 2 authentication';
$string['privacy:metadata:auth_oauth2:authsubsystem'] = 'This plugin is connected to the authentication subsystem.';
$string['privacy:metadata:auth_oauth2:confirmtoken'] = 'The confirmation token.';
$string['privacy:metadata:auth_oauth2:confirmtokenexpires'] = 'The timestamp when the confirmation token expires.';
$string['privacy:metadata:auth_oauth2:email'] = 'The external email that maps to this account.';
$string['privacy:metadata:auth_oauth2:issuerid'] = 'The ID of the OAuth 2 issuer for this OAuth 2 login';
$string['privacy:metadata:auth_oauth2:tableexplanation'] = 'OAuth 2 accounts linked to a user\'s Moodle account.';
$string['privacy:metadata:auth_oauth2:timecreated'] = 'The timestamp when the user account was linked to the OAuth 2 login.';
$string['privacy:metadata:auth_oauth2:timemodified'] = 'The timestamp when this record was modified.';
$string['privacy:metadata:auth_oauth2:userid'] = 'The ID of the user account which the OAuth 2 login is linked to.';
$string['privacy:metadata:auth_oauth2:usermodified'] = 'The ID of the user who modified this account.';
$string['privacy:metadata:auth_oauth2:username'] = 'The external username that maps to this account.';
