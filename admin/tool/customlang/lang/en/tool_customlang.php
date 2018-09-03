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
 * Strings for Language customisation admin tool
 *
 * @package    tool
 * @subpackage customlang
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['checkin'] = 'Save strings to language pack';
$string['checkout'] = 'Open language pack for editing';
$string['checkoutdone'] = 'Language pack loaded';
$string['checkoutinprogress'] = 'Loading language pack';
$string['confirmcheckin'] = 'You are about to save modifications to your local language pack. This will export the customised strings from the translator into you Moodle data directory and Moodle will start using the modified strings. Press \'Continue\' to proceed with saving.';
$string['customlang:edit'] = 'Edit local translation';
$string['customlang:view'] = 'View local translation';
$string['filter'] = 'Filter strings';
$string['filtercomponent'] = 'Show strings of these components';
$string['filtercustomized'] = 'Customised only';
$string['filtermodified'] = 'Modified only';
$string['filteronlyhelps'] = 'Help only';
$string['filtershowstrings'] = 'Show strings';
$string['filterstringid'] = 'String identifier';
$string['filtersubstring'] = 'Only strings containing';
$string['headingcomponent'] = 'Component';
$string['headinglocal'] = 'Local customisation';
$string['headingstandard'] = 'Standard text';
$string['headingstringid'] = 'String';
$string['markinguptodate'] = 'Marking the customisation as up-to-date';
$string['markinguptodate_help'] = 'The customised translation may get outdated if either the English original or the master translation has modified since the string was customised on your site. Review the customised translation. If you find it up-to-date, click the checkbox. Edit it otherwise.';
$string['markuptodate'] = 'mark as up-to-date';
$string['modifiedno'] = 'There are no modified strings to save.';
$string['modifiednum'] = 'There are {$a} modified strings. Do you wish to save these changes to your local language pack?';
$string['nostringsfound'] = 'No strings found, please modify the filter settings';
$string['placeholder'] = 'Placeholders';
$string['placeholder_help'] = 'Placeholders are special statements like `{$a}` or `{$a->something}` within the string. They are replaced with a value when the string is actually printed.

It is important to copy them exactly as they are in the original string. Do not translate them nor change their left-to-right orientation.';
$string['placeholderwarning'] = 'string contains a placeholder';
$string['pluginname'] = 'Language customisation';
$string['savecheckin'] = 'Save changes to the language pack';
$string['savecontinue'] = 'Apply changes and continue editing';
$string['privacy:metadata'] = 'The Language customisation plugin does not store any personal data.';
