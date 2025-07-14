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
 * @package     factor_cohort
 * @author      Chris Pratt <tonyyeb@gmail.com>
 * @copyright   Chris Pratt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Cohorts';
$string['privacy:metadata'] = 'The Cohorts factor plugin does not store any personal data.';
$string['settings:cohort'] = 'Non-passing cohorts';
$string['settings:cohort_help'] = 'Select the cohorts that will not pass this factor. This allows you to force these cohorts to use other factors to authenticate.';
$string['settings:description'] = '<p>Select the user cohorts that must use additional factors to authenticate. If this factor is not set up, all cohorts will be required to use additional factors by default.</p>
<p>This factor requires a cohort to be created.</p>';
$string['settings:shortdescription'] = 'Specify which cohorts of users must use other factors to authenticate. Must be combined with other factors.';
$string['summarycondition'] = 'does NOT have any of the following cohorts assigned in any context: {$a}';
