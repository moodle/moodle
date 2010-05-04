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
 * Strings for component 'condition', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   condition
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addcompletions'] = 'Add {no} activity conditions to form';
$string['addgrades'] = 'Add {no} grade conditions to form';
$string['availabilityconditions'] = 'Restrict availability';
$string['availablefrom'] = 'Only available from';
$string['availablefrom_help'] = 'The activity is only shown to students from this date. If you choose to show information about an activity that is unavailable, then before this date, students will see the activity greyed-out, with informational text about the date that it appears.';
$string['availableuntil'] = 'Only available until end';
$string['availableuntil_help'] = 'The activity disappears after this date. The activity completely vanishes at midnight on the given date, even if you\'ve chosen to show the restriction information.';
$string['badavailabledates'] = 'Invalid dates. If you set both dates, the \'available from\' date should be before the \'until\' date.';
$string['completion_complete'] = 'must be marked complete';
$string['completioncondition'] = 'Activity completion condition';
$string['completioncondition_help'] = 'This setting determines any activity completion conditions which must be met in order for the activity to be available. Note that completion tracking must first be set before an activity completion condition can be set.  Multiple activity completion conditions may be set if desired.  The activity will only become available when ALL activity completion conditions are met.';
$string['completion_fail'] = 'must be complete with fail grade';
$string['completion_incomplete'] = 'must not be marked complete';
$string['completion_pass'] = 'must be complete with pass grade';
$string['configenableavailability'] = 'When enabled, this lets you set conditions (based on date, grade, or completion) that control whether an activity is available.';
$string['enableavailability'] = 'Enable conditional availability';
$string['grade_atleast'] = 'must be at least';
$string['gradecondition'] = 'Grade condition';
$string['gradecondition_help'] = 'This setting determines any grade conditions which must be met in order for the activity to be available. Multiple grade conditions may be set if desired. The activity will only become available when ALL grade conditions are met.';
$string['grade_upto'] = 'and less than';
$string['none'] = '(none)';
$string['notavailableyet'] = 'Not available yet';
$string['requires_completion_0'] = 'Not available unless the activity <strong>{$a}</strong> is incomplete.';
$string['requires_completion_1'] = 'Not available until the activity <strong>{$a}</strong> is marked complete.';
$string['requires_completion_2'] = 'Not available until the activity <strong>{$a}</strong> is complete and passed.';
$string['requires_completion_3'] = 'Not available unless the activity <strong>{$a}</strong> is complete and failed.';
$string['requires_date'] = 'Available from {$a}.';
$string['requires_date_before'] = 'Available until {$a}.';
$string['requires_date_both'] = 'Available from {$a->from} to {$a->until}.';
$string['requires_grade_any'] = 'Not available until you have a grade in <strong>{$a}</strong>.';
$string['requires_grade_max'] = 'Not available unless you get an appropriate score in <strong>{$a}</strong>.';
$string['requires_grade_min'] = 'Not available until you achieve a required score in <strong>{$a}</strong>.';
$string['requires_grade_range'] = 'Not available unless you get a particular score in <strong>{$a}</strong>.';
$string['showavailability'] = 'Before activity is available';
$string['showavailability_help'] = 'When an activity is unavailable due to the restrictions in this box, there are two possibilities:

* The activity displays to users, but as greyed-out text instead of a link. Informational text below the activity indicates when, or under what conditions, it will become available.
* The activity does not display to users at all.

In both cases, once the activity becomes available, it displays as normal. Users with the capability to view hidden activities can still see unavailable activities, regardless of this setting. The informational text always appears to them. This option does not affect the standard visibility option. You can still use the eye icon to quickly and completely hide any activity from course participants.';
$string['showavailability_hide'] = 'Hide activity entirely';
$string['showavailability_show'] = 'Show activity greyed-out, with restriction information';
$string['userrestriction_hidden'] = 'Restricted (completely hidden, no message): &lsquo;{$a}&rsquo;';
$string['userrestriction_visible'] = 'Restricted: &lsquo;{$a}&rsquo;';
