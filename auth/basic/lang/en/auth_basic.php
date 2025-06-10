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
 * Anobody can login using basic auth http headers
 *
 * @package   auth_basic
 * @copyright Brendan Heywood <brendan@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['auth_basicdescription'] = 'Users can sign in via HTTP Basic authentication';
$string['pluginname'] = 'Basic authentication';
$string['send401'] = 'Force Basic for everyone';
$string['send401_help'] = 'If Yes then all users will be prompted with the basic auth dialog and the normal login page will be disabled. In most cases you won\'t want this.';
$string['onlybasic'] = 'Only basic';
$string['onlybasic_help'] = 'If Yes then only users whose auth type has been explicitly set to \'basic\' will work. For additional security.';
$string['debug'] = 'Debug mode';
$string['debug_help'] = 'Dump details of auth process to error log and http headers';
$string['send401_cancel'] = 'You need to enter a valid username and password';

/*
 * Privacy provider (GDPR)
 */
$string["privacy:metadata:auth_basic_master_password"] = "Master password.";
$string["privacy:metadata:auth_basic_master_password:userid"] = "User who created master password.";
$string["privacy:metadata:masterpassword"] = "masterpassword";

$string["masterpassword"] = "Master Password";
$string["password"] = "Password";

$string["auth_basic_not_enabled"] = 'Auth Basic is not enabled. The plugin won\'t work until you enable it in \'Manage Authenticaton\'';
$string["masterpassword_not_enabled"] = 'Please add <code>$CFG->auth_basic_enabled_master_password = true;</code> in config.php to enable master password.<br/>
You are able to generate new master passwords, but they won\'t work until the config is enabled.';
$string["whitelist_not_set"] = '<code>$CFG->auth_basic_whitelist_ips</code> is not set up in config.php, there will be no IP restriction.';
$string["whitelistonly"] = 'Only allow access to the following ips: <strong>{$a}</strong>';

$string["masterpassword_desc"] = "Master Password";
$string["menusettings"] = "Settings";
$string["generated_masterpassword"] = "Generated Password";

$string["savepassword"] = "Save Password";
$string["regeneratepassword"] = "Regenerate Passwords";

$string["username"] = "Name";
$string["uses"] = "Usage";
$string["timecreated"] = "Time Created";
$string["timeexpired"] = "Time Expired";
