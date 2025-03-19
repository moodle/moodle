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
 * Strings for component 'gradepenalty_duedate', language 'en'.
 *
 * @package   gradepenalty_duedate
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addrule'] = 'Add rule';
$string['deleteallrules'] = 'Delete all rules';
$string['duedate:manage'] = 'Permission to manage penalty rules';
$string['duedaterule'] = 'Penalty rules';
$string['editduedaterule'] = 'Edit penalty rules';
$string['error_overdueby_abovevalue'] = 'The overdue must be greater than the value of above rule: {$a}.';
$string['error_overdueby_maxvalue'] = 'The overdue cannot be greater than {$a}.';
$string['error_overdueby_minvalue'] = 'The overdue must be greater than or equal to {$a}.';
$string['error_penalty_abovevalue'] = 'The penalty must be greater than the value of above rule: {$a}%.';
$string['error_penalty_maxvalue'] = 'The penalty cannot be greater than {$a}%.';
$string['error_penalty_minvalue'] = 'The penalty must be greater than or equal to {$a}%.';
$string['existingrule'] = 'Existing rules';
$string['finalpenaltyrule'] = 'Final penalty rule';
$string['finalpenaltyrule_help'] = 'The penalty system uses the first matching rule found. If no rules match, the final rule is applied.';
$string['insertrule'] = 'Insert below';
$string['overdueby'] = 'Overdue';
$string['overdueby_help'] = 'Set the time in seconds after the due date that the penalty will be applied.';
$string['overdueby_label'] = 'Overdue:';
$string['overdueby_lastrow'] = '&gt; {$a}';
$string['overdueby_onerow'] = 'All late submissions';
$string['overdueby_row'] = '&le; {$a}';
$string['penalty'] = 'Penalty';
$string['penalty_help'] = 'Set the penalty in percent that will be applied for late submissions.';
$string['penalty_label'] = 'Penalty:';
$string['penaltyrule'] = 'Penalty rules';
$string['penaltyrule_group'] = 'Penalty rule {no}';
$string['penaltyrule_inherited'] = 'The penalty rules in this context are inherited from a parent context. You can click on "Edit" button to override the values.';
$string['penaltyrule_not_inherited'] = 'Please click on "Edit" button to change or create new penalty rules.';
$string['penaltyrule_overridden'] = 'The penalty rules have been overridden. You can click on "Reset" button to remove overridden rules. Note: this will remove all if there is no rule in parent contexts.';
$string['pluginname'] = 'Late submission penalties';
$string['privacy:metadata:gradepenalty_duedate_rule'] = 'Grade penalty due date table';
$string['privacy:metadata:gradepenalty_duedate_rule:usermodified'] = 'User who modified the rule';
$string['resetconfirm'] = 'This will remove all rules set up for this context. Are you sure you want to continue?';
