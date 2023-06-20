<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     local_clickview
 * @category    string
 * @copyright   2021 ClickView Pty. Limited <info@clickview.com.au>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'ClickView';
$string['settings'] = 'ClickView settings';
$string['hostlocation'] = 'Host location';
$string['hostlocation_desc'] = 'You should select a host location appropriate to your geographic region otherwise your users will not be able to sign into our platform.';
$string['schoolid'] = 'Single Sign On (SSO)';
$string['schoolid_desc'] = 'If a school ID (GUID) is provided the plugin will redirect to their SSO login page when authentication with our plugin. The plugin will still function normally if the ID is incorrect, taking the user to the default sign in page rather than the expected SSO page.';
$string['privacy:metadata'] = 'The ClickView plugin does not store any personal data.';
