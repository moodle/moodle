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
 * @package     factor_admin
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['info'] = 'This factor allows for NOT being an administrator to count as a factor. Its intended use is to ensure administators require tighter security, so regular users get the weight for free, while admins must use other factors.';
$string['pluginname'] = 'Non-administrator';
$string['privacy:metadata'] = 'The Non-administrator factor plugin does not store any personal data.';
$string['settings:description'] = 'All users except site administrators receive the points for this factor. This allows you to require additional authentication factors for site administrators.';
$string['settings:shortdescription'] = 'Require additional authentication factors for administrators.';
$string['settings:weight_help'] = 'Weight is given to regular users for this factor, so admins must have more factors than a regular user to pass.';
$string['summarycondition'] = 'is not an admin';
