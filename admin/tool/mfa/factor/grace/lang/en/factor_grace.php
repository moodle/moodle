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
 * @package     factor_grace
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['info'] = 'Allows login without other factor for a specified period of time.';
$string['pluginname'] = 'Grace period';
$string['preferences'] = 'User preferences';
$string['privacy:metadata'] = 'The Grace period factor plugin does not store any personal data';
$string['redirectsetup'] = 'You must complete setup for Multi-factor authentication before you can proceed.';
$string['revokeexpiredfactors'] = 'Revoke expired gracemode factors';
$string['settings:customwarning'] = 'Warning banner content';
$string['settings:customwarning_help'] = 'Add content here to replace the grace warning notification with custom HTML contents. Adding {timeremaining} in text will replace it with the current grace duration for the user, and {setuplink} will replace with the URL of the setup page for the user.';
$string['settings:forcesetup'] = 'Force factor setup';
$string['settings:forcesetup_help'] = 'Forces a user to the preferences page to setup MFA when the gracemode period expires. If set to off, users will be unable to authenticate when the grace period expires.';
$string['settings:graceperiod'] = 'Grace period';
$string['settings:graceperiod_help'] = 'Period of time when users can access Moodle without configured and enabled factors';
$string['settings:ignorelist'] = 'Ignored factors';
$string['settings:ignorelist_help'] = 'Grace will not give points if there are other factors that users can use to authenticate with MFA. Any factors here will not be counted by grace when deciding whether to give points. This can allow Grace to allow authentication if another factor like email, is suffering configuration or system issues.';
$string['setupfactors'] = 'You are currently in grace mode, and may not have enough factors setup to login once the grace period is over.
    Visit {$a->url} to check your authentication status, and setup more authentication factors. Your grace period expires in {$a->time}.';
$string['summarycondition'] = 'is within grace period';
