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
 * Strings for component 'privacy', language 'en', branch 'master'
 *
 * @package   core_privacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['broughtbymoodle'] = 'This data export is provided by Moodle.';
$string['exportfrom'] = 'Exported from {$a}';
$string['exporttime'] = 'Exported on {$a}';
$string['exportuser'] = 'Data for {$a}';
$string['privacy:metadata'] = 'The privacy subsystem does not store any data of its own and is designed to act as a channel between components and the interface used to describe, export, and remove their data.';
$string['trace:done'] = 'Complete';
$string['trace:exportcomplete'] = 'Export complete';
$string['trace:exportingapproved'] = 'Performing primary export for {$a->total} components ({$a->datetime})';
$string['trace:exportingrelated'] = 'Performing related export for {$a->total} components ({$a->datetime})';
$string['trace:finalisingexport'] = 'Finalising export';
$string['trace:processingcomponent'] = 'Processing {$a->component} ({$a->progress}/{$a->total}) ({$a->datetime})';
$string['trace:preprocessingcomponent'] = 'Pre-processing {$a->component} ({$a->progress}/{$a->total}) ({$a->datetime})';
$string['trace:fetchcomponents'] = 'Fetching data from {$a->total} components ({$a->datetime})';
$string['trace:deletingapproved'] = 'Performing removal of {$a->total} approved contexts ({$a->datetime})';
$string['trace:deletingapprovedusers'] = 'Performing removal of users in {$a->total} approved components for context {$a->contextid} ({$a->datetime}).';
$string['trace:deletingcontext'] = 'Performing removal of context from {$a->total} components ({$a->datetime})';
$string['navigation'] = 'Navigation';
$string['trace:deletinguser'] = 'Performing removal of user from {$a->total} components ({$a->datetime})';
$string['privacy:subsystem:empty'] = 'This subsystem does not store any data.';
$string['viewdata'] = 'Click on a link in the navigation to view data.';
