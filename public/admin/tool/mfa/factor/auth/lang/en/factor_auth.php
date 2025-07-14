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
 * @package     factor_auth
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['info'] = 'Check the type of authentication used to log in as an MFA factor.';
$string['pluginname'] = 'Authentication type';
$string['privacy:metadata'] = 'The Authentication type factor plugin does not store any personal data.';
$string['settings:description'] = 'Automatically verify users based on their authentication type.';
$string['settings:goodauth'] = 'Factor authentication types';
$string['settings:goodauth_help'] = 'Select all authentication types to use as a factor for MFA. Any types not selected will not be treated as a FAIL in MFA.';
$string['settings:shortdescription'] = 'Allow users to bypass extra authentication steps based on their authentication type.';
$string['summarycondition'] = 'has an authentication type of {$a}';
