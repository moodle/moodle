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
 * Plugin language strings
 *
 * @package    customfield_number
 * @copyright  2024 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['activitytypes'] = 'Activity types';
$string['automaticallypopulated'] = 'Automatically populated';
$string['crontaskname'] = 'Populating automatic number custom fields';
$string['decimalplaces'] = 'Decimal places';
$string['defaultvalueconfigerror'] = 'Default value must be between minimum and maximum';
$string['display'] = 'Display template';
$string['display_help'] = 'How to display the value of the field. Use the following placeholders:

* **{value}** - display value in a general format (float with decimals configured in the field)
* **$ {value}** - price in dollars
* **{value} hrs** - duration in hours';
$string['displayvalueconfigerror'] = 'Invalid placeholder';
$string['displaywhenzero'] = 'Display when zero';
$string['displaywhenzero_help'] = 'How to display the field value when the value is "0". For example, in case of a price you can display the word "Free" but in case of the duration you may want to leave it empty since it means that the duration was not estimated.

Leave empty if you do not want to display anything at all when the value is set to "0".';
$string['fieldtype'] = 'Field type';
$string['fieldtype_help'] = 'Select the type of numeric field to be set. Some options require manual input in the course settings page, while others will automatically populate values, such as with the number of specified activities in a course.';
$string['genericfield'] = 'Generic field for any numeric data';
$string['headerdisplaysettings'] = 'Display format';
$string['invalidprovider'] = 'Incorrect provider';
$string['manualinput'] = 'Manual input';
$string['maximumvalue'] = 'Maximum value';
$string['maximumvalueerror'] = 'Value must be less than or equal to {$a}';
$string['minimumvalue'] = 'Minimum value';
$string['minimumvalueconfigerror'] = 'Minimum value must be less than maximum';
$string['minimumvalueerror'] = 'Value must be greater than or equal to {$a}';
$string['missingrequired'] = 'Missing instance ID or field ID';
$string['nofactivities'] = 'Number of activities in the course';
$string['pluginname'] = 'Number';
$string['privacy:metadata'] = 'The number custom field plugin does not store any personal data';
$string['specificsettings'] = 'Number field settings';
