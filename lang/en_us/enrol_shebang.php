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
 * Strings for component 'enrol_shebang', language 'en_us', version '4.1'.
 *
 * @package     enrol_shebang
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['ERR_ENROL_INSERT'] = 'Could not create enroll instance in course';
$string['ERR_SUSPEND_FAIL'] = 'Failed to suspend user enrollment';
$string['LBL_COURSE_CATEGORY_ID'] = 'Category for \'Use Existing\'';
$string['LBL_CROSSLIST_help'] = '<p>These settings affect how cross-listed course sections are handled.</p><p><ul><li>Process course cross-listing - Enables processing of group &lt;membership&gt; messages, i.e. cross-listed child courses.</li><li>Implement cross-listing using - Determines whether to implement a cross-listed course as either a metacourse, or a regular course where enrollments are re-directed to the parent course. With metacourses, enrollments are made in the child course, as usual, and the Moodle syncronizes enrollments.</li><li>Group enrollees based on child-courses - Create groups in the parent course that correspond to the child courses, and populate them accordingly.</li><li>Cross-list Fullname Prefix - Prefix for the cross-list parent course full name.</li><li>Cross-list Shortname Prefix - Prefix for the cross-list parent course short name.</li><li>Hide child courses when cross-listed - Makes a course hidden after it has been designated as a child course.</li></ul></p>';
$string['LBL_DISCLAIMER_help'] = '<h4 style="width: 100%; text-align: center;">SHEBanG Enrollment Plugin</h4><p style="width: 100%; text-align: center;">Copyright &copy; 2010 Appalachian State University, Boone, NC</p><p style="text-align: justify">Distributed under the terms of the GNU General Public License version 3. This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.<br /><br />This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.<br /><br />You should have received a copy of the GNU General Public License along with this program. If not, see <a href="http://www.gnu.org/licenses/">www.gnu.org/licenses/</a>.</p>';
$string['LBL_ENROLL'] = 'Enrollment Role Mappings';
$string['LBL_ENROLL_help'] = '<p>Enrollment mappings determine the Moodle role used when a role assignment is made in a course. For a given LMB/IMS role used in &lt;membership&gt; messages, select which Moodle role to use.</p>';
$string['LBL_PERSON_DELETE_UNENROL'] = 'Unenroll';
$string['LBL_VERSION'] = 'SHEBanG Enrollment Plugin Version {$a}';
$string['description'] = '<p style="text-align: center">{$a->a}</p><p style="text-align: justify">{$a->c}<br /><br />This enrollment plugin provides a way to consume SunGard HE Banner&reg; messages generated from Luminis Message Broker. This module is not a SunGard product, and is neither endorsed nor supported by SunGard. This module is provided as is, with no warranties or guarantees, either express or implied. Use it at your own risk.<br /><br />Please review the README.txt file in the plugin directory for information concerning security and performance issues. Do not enable this plugin until you have secured access to the post.php script.<br /><br />To perform administrative tasks use the <a href="{$a->b}">Admin. Utilities</a>.</p>';
$string['shebang:unenrol'] = 'Unenroll users from course';
