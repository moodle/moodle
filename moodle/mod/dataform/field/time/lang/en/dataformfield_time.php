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
 * @package dataformfield_time
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Time';
$string['time:addinstance'] = 'Add a new Time dataformfield';
$string['dateonly'] = 'Date only';
$string['dateonly_help'] = 'Select this option to display only the date portion of the field value and a date only selector when the field is edited.';
$string['displayformat'] = 'Display format';
$string['displayformat_help'] = 'You can set a custom display format for the field value. Format options can be found at <a href="http://php.net/manual/en/function.strftime.php">PHP strftime format</a>.';
$string['stopyear'] = 'Stop year';
$string['stopyear_help'] = 'Year value (YYYY). This value determines the max year value in the date/time selector in editing mode. Leave 0 or empty to use Moodle default.';
$string['fromtimestamp'] = 'From timestamp: ';
$string['startyear'] = 'Start year';
$string['startyear_help'] = 'Year value (YYYY). This value determines the min year value in the date/time selector in editing mode. Leave 0 or empty to use Moodle default.';
$string['day'] = 'Day';
$string['month'] = 'Month';
$string['year'] = 'Year';
$string['hour'] = 'Hour';
$string['minute'] = 'Minute';
$string['masked'] = 'Masked';
$string['masked_help'] = 'Select this option to render Time/Date selector dropdowns with labels (e.g. Year, Month, Day) for empty values. The labels are defined in the language pack.';
