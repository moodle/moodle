<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'enrol_lmb', language 'en_us', version '4.1'.
 *
 * @package     enrol_lmb
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['bannerxmlfoldercomphelp'] = 'If this option is selected, then enrollments missing from the extract files will be dropped.';
$string['bannerxmllocationcomphelp'] = 'If this option is selected, then enrollments missing from the extract file will be dropped.';
$string['categorytypehelp'] = 'This allows you select what categories you would like courses to be created in. Options:
<ul>
<li>Terms: This setting will cause courses to be placed in categories with the name of their term/semester.</li>
<li>Departments: This setting will cause courses to be placed in categories with the name of their host department.</li>
<li>Department Codes: Uses the department short code, instead of full name.</li>
<li>Terms then Departments: This setting will cause courses to be placed in categories with the name of their host department, which is contained in a parent term named for the term/semester.</li>
<li>Terms then Department Codes: Same as Terms then Departments, but uses the department short code instead of its full name.</li>
<li>Selected: With this setting, select the existing category you would like courses to be placed in from the second drop down menu.</li>
</ul>';
$string['disableenrol'] = 'Disable Enrollments on Drop';
$string['disableenrolhelp'] = 'Disable enrollments instead of unenrolling them. Prevents possible data loss in some versions and configurations of Moodle when users are dropped and re-added to a course.';
$string['dropprecentlimit'] = 'Do not drop if more than this percent of enrollments';
$string['dropprecentlimithelp'] = 'When doing comprehensive XML processing, missing enrollments are treated as drops. This setting will cause the module to skip the drop process if more than this percent of total enrollments in a term are set to be dropped';
$string['header'] = 'You are using Banner/Luminis Message Broker Module version {$a->version}.<br>
LMB Tools have moved to the setting block, under Site Administration>Plugins>Enrollments>Banner/Luminis Message Broker>Tools</a>.';
$string['lmb:enrol'] = 'Enroll users';
$string['lmb:manage'] = 'Manage user enrollments';
$string['lmb:unenrol'] = 'Unenroll users from the course';
$string['lmb:unenrolself'] = 'Unenroll self from the course';
$string['locality'] = 'User XML \'locality\'';
$string['page_reprocessenrolments'] = 'Reprocess Enrollments';
$string['parseenrol'] = 'XML Parse - Enrollment';
$string['parseenrolxml'] = 'Parse Enrollment XML';
$string['parseenrolxmlhelp'] = 'Process enrollment records. Parse Course and Parse Person must be on. When unchecked, records will be completely skipped.';
$string['recovergrades'] = 'Recover olds grades for re-enrolled users';
$string['recovergradeshelp'] = 'If users are being re-enrolled in a course, try and recover old grades. This was the standard behavior in Moodle 1.9.x and below.';
$string['removelangs'] = '<b><font color=red>Notice:</font> It appears that old Banner/Luminis Message Broker language files are still installed. Please remove the file \'$a/enrol_lmb.php\' and the folder \'$a/help/enrol/lmb\'.</b>';
$string['storexmlhelp'] = 'This dictates when XML messages from Luminis Message Broker are stored in the enrol_lmb_raw_xml table. This allows for greater troubleshooting, but the XML main contain sensitive data that should not be stored. Options:
<ul>
<li>Never: XML will never be stored.
<li>On Error: XML will only be stored if there is an error processing it.
<li>Always: XML will always be stored.
</ul>';
$string['unenrolmember'] = 'Unenroll members from course when directed';
$string['unenrolmemberhelp'] = 'Unenroll (or \'drop\') members from a course when an appropriate XML message is received.';
$string['userestrictdateshelp'] = 'If specified in the enrollment, set enrollment begin and end dates in Moodle.';
$string['xlstitlehelp'] = 'This contains the template for creating the full course name for crosslisted courses.
<p>The crosslisted name template works in the same way, as the \'Course full name\' setting, with a few differences, as outlined here.</p>
<p>The crosslisted name template can contain the same flags as \'Course full name<?php helpbutton(\'coursetitle\', \'More detail about this option\', \'enrol-lmb\'); ?>\'. If any of these flags are found, they will be replaced with the corresponding data from the first course to join the crosslist</p>
<p>In addition to the standard flags, 2 new flags are added:
<ul>
<li>[XLSID] - The Banner identifier for the crosslist<br />
<li>[REPEAT] - The flag will be replaced by the string generated with the \'name repeater\' and \'name divider\' settings.<br />
</ul></p>
<p>Example: Say you have two courses, 12345.200710 and 54321.200710, and they are crosslisted with the crosslist code XLSAA200710. If you set \'Crosslisted course full name\' to \'[XLSID] - [REPEAT]\', \'Full name repeater\' to \'[CRN]\', and \'Full name divider\' to \' / \', the resulting full title of the crosslisted course would be \'XLSAA200710 - 12345 / 54321.</p>';
$string['xlstitlerepeathelp'] = 'This contains the template for [REPEAT] section of the course full name, and will be repeated for each member course in the crosslist.
<p>The \'name repeater\' value works the same way as the \'Course full name\' setting, except that it will be repeated for each member course in the crosslist, and the \'name divider\' will be placed in between subsequent repetitions.</p>
<p>The name repeaters can contain the same flags as \'Course full name<?php helpbutton(\'coursetitle\', \'More detail about this option\', \'enrol/lmb\'); ?>\'.</p>
<p>In addition to the standard flags, 1 new flags is added:
<ul>
<li>[XLSID] - The Banner identifier for the crosslist<br />
</ul></p>
<p>Example: Say you have two courses, 12345.200710 and 54321.200710, and they are crosslisted with the crosslist code XLSAA200710. If you set \'Crosslisted course full name\' to \'[XLSID] - [REPEAT]\', \'Full name repeater\' to \'[CRN]\', and \'Full name divider\' to \' / \', the resulting full title of the crosslisted course would be \'XLSAA200710 - 12345 / 54321\'.</p>';
$string['xlstypehelp'] = 'This determines how crosslisted courses will be handled in Moodle. Options:
<ul>
<li>Merged course: This setting will cause the separate courses of the crosslist to be left empty, with no enrollments. All members will be enrolled directly into the crosslisted course.
<li>Meta course: This setting will cause members to be enrolled in the individual courses, while the crosslsted course is formed by making a meta-course containing all the individual courses.
</ul>';
