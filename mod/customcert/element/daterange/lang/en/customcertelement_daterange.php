<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * Strings for component 'customcertelement_daterange', language 'en'.
 *
 * @package    customcertelement_daterange
 * @copyright  2018 Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

$string['addrange'] = 'Add another range';
$string['completiondate'] = 'Completion date';
$string['courseenddate'] = 'Course end date';
$string['coursegradedate'] = 'Course grade date';
$string['coursestartdate'] = 'Course start date';
$string['currentdate'] = 'Current date';
$string['dateitem'] = 'Date item';
$string['dateitem_help'] = 'This will be the date that is printed on the certificate';
$string['dateranges'] = 'Date ranges';
$string['datestring'] = 'String';
$string['end'] = 'End';
$string['error:atleastone'] = 'You must have at least one date range configured';
$string['error:datestring'] = 'You must provide string representation for the date range';
$string['error:enddate'] = 'End date must occur after the start date';
$string['error:recurring'] = 'Recurring range must not be longer than 12 months';
$string['fallbackstring'] = 'Fallback string';
$string['fallbackstring_help'] = 'This string will be displayed if no date range applies to a date. If the fallback string is not set, then there will be no output at all.';
$string['help'] = 'Configure a string representation for your date ranges.<br /><br />If your ranges overlap the first matched date range will be applied.';
$string['issueddate'] = 'Issued date';
$string['placeholders'] = 'The following placeholders can be used in the string representation or fallback string. <br/><br /> {{range_first_year}} - first year of the matched range,<br/> {{range_last_year}} - last year of the matched range,<br/> {{recurring_range_first_year}} - first year of the matched recurring period,<br/> {{recurring_range_last_year}} - last year of the matched recurring period,<br/> {{current_year}} - the current year,<br/>  {{date_year}} - a year of the users\'s date.';
$string['pluginname'] = 'Date range';
$string['preview'] = 'Preview {$a}';
$string['privacy:metadata'] = 'The Date range plugin does not store any personal data.';
$string['recurring'] = 'Recurring';
$string['recurring_help'] = 'If you mark a date range as recurring then the configured year will not be considered.';
$string['setdeleted'] = 'Delete';
$string['start'] = 'Start';
