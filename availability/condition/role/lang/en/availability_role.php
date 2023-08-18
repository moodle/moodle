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
 * Availability role - Language pack
 *
 * @package    availability_role
 * @copyright  2015 Bence Laky, Synergy Learning UK <b.laky@intrallect.com>
 *             on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['description'] = 'Allow only users with a specified course role.';
$string['error_selectrole'] = 'You must select a role';
$string['missing'] = '[Missing role]';
$string['title'] = 'Role';
$string['pluginname'] = 'Restriction by course role';
$string['privacy:metadata'] = 'The Restriction by course role plugin does not store any personal data.';
$string['requires_role'] = 'You are a(n) <em>{$a}</em>';
$string['requires_notrole'] = 'You are not a(n) <em>{$a}</em>';
$string['setting_supportedrolesheading'] = 'Supported roles';
$string['setting_supportguestrole'] = 'Guest role';
$string['setting_supportguestrole_desc'] = 'If activated, the availability of activities can be restricted to or forbidden for users that are viewing a course as guest.';
$string['setting_supportnotloggedinrole'] = 'Not-logged-in role';
$string['setting_supportnotloggedinrole_desc'] = 'If activated, the availability of activities can be restricted to or forbidden for users that are not logged in.';
