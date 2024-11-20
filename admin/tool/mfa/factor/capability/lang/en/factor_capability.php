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
 * @package     factor_capability
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['capability:cannotpassfactor'] = 'STOPS a role from passing the MFA user capability factor.';
$string['pluginname'] = 'User capability';
$string['privacy:metadata'] = 'The User capability factor plugin does not store any personal data.';
$string['settings:adminpasses'] = 'Site admins can pass this factor';
$string['settings:adminpasses_help'] = 'By default admins pass all capability checks, including this one which uses \'factor/capability:cannotpassfactor\', which means they will fail this factor.
    If checked then all site admins will pass this factor if they do not have this capability from another role.
    If unchecked site admins will fail this factor.';
$string['summarycondition'] = 'does NOT have the factor/capability:cannotpassfactor capability in any role including site administrator.';
