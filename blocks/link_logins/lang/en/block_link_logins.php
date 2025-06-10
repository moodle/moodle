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
 * @package    block_link_logins
 * @copyright  2023 onwards Louisiana State University
 * @copyright  2023 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['allowed_users'] = 'Allowed Users';
$string['allowed_users_desc'] = 'A comma seperated list of all allowed users. Only use usernames.';
$string['homedomain'] = 'Home Domain';
$string['homedomain_desc'] = 'Your OAuth2 home domain. These emails will not be converted to #ext# usernames.';
$string['extdomain'] = 'External Domain';
$string['extdomain_desc'] = 'Your OAuth2 external domain used by your authentication source.';
$string['issuerid'] = 'Issuer ID';
$string['issuerid_desc'] = 'Pick the Oauth 2 issuer you are using for default logins.';
$string['link'] = 'Link';
$string['link'] = 'Link';
$string['link_logins'] = 'Link Logins';
$string['link_logins:addinstance'] = 'Add a new Link Logins block';
$string['link_logins:link_login'] = 'Link Login';
$string['link_logins:myaddinstance'] = 'Add a new Link Logins block to the My Moodle page';
$string['pluginname'] = 'Link Logins block';
$string['securityviolation'] = 'You do not have access to this plugin.';
$string['existingusername'] = 'Existing username:&nbsp;&nbsp;';
$string['prospectiveemail'] = 'Prospective email:&nbsp;&nbsp;';

// Return strings
$string['found'] = ' was found, they have been linked.';
$string['nonefound'] = 'Something went wrong finding ';
$string['mistake'] = 'The existing username you are trying to map to is missing!<br>The prospective user you are wanting to map already exists!<br><br>Rethink things.';
$string['multimistake'] = 'You have more than one existing Moodle user matching your supplied prospective email address.<br>Please deal with them.';
$string['exception'] = 'We have experienced a failure: ';
$string['success'] = 'You have successfully linked logins.';
$string['successfullink'] = 'You have successfully linked the following user to an external login:<br>Internal Moodle userid: {$a->userid}<br>External username: {$a->username}<br>External email: {$a->email}.';
$string['dupemistake'] = 'The requested external username: {$a->username} already exists with email: {$a->email} in the auth_oauth2_linked_login table and is linked to Moodle user ID {$a->userid} and was created by {$a->creatorfirstname} {$a->creatorlastname}.';
$string['continue'] = 'Are you sure you want to continue with the process?';
$string['confirm1'] = 'Are you sure you want to allow ';
$string['confirm2'] = ' to login as ';
$string['missingusername'] = 'Your existing user is not valid or their username is incorrect, please try again.';
$string['existingprospective'] = 'Your prospective user already exists in Moodle, please remove them before you continue.';
