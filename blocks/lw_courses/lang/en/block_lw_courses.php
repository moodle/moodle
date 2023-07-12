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
 * Lang strings for lw_courses block
 *
 * @package    block_lw_courses
 * @copyright  2012 Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['activityoverview'] = 'You have {$a}s that need attention';
$string['alwaysshowall'] = 'Always show all';
$string['collapseall'] = 'Collapse all course lists';
$string['configotherexpanded'] = 'If enabled, other courses will be expanded by default unless overridden by user preferences.';
$string['configpreservestates'] = 'If enabled, the collapsed/expanded states set by the user are stored and used on each load.';
$string['lw_courses:addinstance'] = 'Add a new my courses block';
$string['lw_courses:myaddinstance'] = 'Add a new my courses block to Dashboard';
$string['defaultmaxcourses'] = 'Default maximum courses';
$string['defaultmaxcoursesdesc'] = 'Maximum courses which should be displayed on my courses block, 0 will show all courses';
$string['expandall'] = 'Expand all course lists';
$string['forcedefaultmaxcourses'] = 'Force maximum courses';
$string['forcedefaultmaxcoursesdesc'] = 'If set then user will not be able to change his/her personal setting';
$string['fullpath'] = 'All categories and subcategories';
$string['hiddencoursecount'] = 'You have {$a} hidden course';
$string['hiddencoursecountplural'] = 'You have {$a} hidden courses';
$string['hiddencoursecountwithshowall'] = 'You have {$a->coursecount} hidden course ({$a->showalllink})';
$string['hiddencoursecountwithshowallplural'] = 'You have {$a->coursecount} hidden courses ({$a->showalllink})';
$string['message'] = 'message';
$string['messages'] = 'messages';
$string['movecourse'] = 'Move course: {$a}';
$string['movecoursehere'] = 'Move course here';
$string['movetofirst'] = 'Move {$a} course to top';
$string['moveafterhere'] = 'Move {$a->movingcoursename} course after {$a->currentcoursename}';
$string['movingcourse'] = 'You are moving: {$a->fullname} ({$a->cancellink})';
$string['none'] = 'None';
$string['numtodisplay'] = 'Number of courses to display: ';
$string['onlyparentname'] = 'Parent category only';
$string['otherexpanded'] = 'Other courses expanded';
$string['pluginname'] = 'My Courses (lw_courses)';
$string['displayname'] = 'My Courses';
$string['preservestates'] = 'Preserve expanded states';
$string['shortnameprefix'] = 'Includes {$a}';
$string['shortnamesufixsingular'] = ' (and {$a} other)';
$string['shortnamesufixprural'] = ' (and {$a} others)';
$string['showcategories'] = 'Categories to show';
$string['showcategoriesdesc'] = 'Should course categories be displayed below each course?';
$string['showchildren'] = 'Show children';
$string['showchildrendesc'] = 'Should child courses be listed underneath the main course title?';
$string['showwelcomearea'] = 'Show welcome area';
$string['showwelcomeareadesc'] = 'Show the welcome area above the course list?';
$string['view_edit_profile'] = '(View and edit your profile.)';
$string['welcome'] = 'Welcome {$a}';
$string['youhavemessages'] = 'You have {$a} unread ';
$string['youhavenomessages'] = 'You have no unread ';

$string['unset'] = "Unset";
$string['coursegridwidth'] = "Grid size";
$string['coursegridwidthdesc'] = "Select how wide will the course grid will be";

$string['fullwidth'] = "Full Width";
$string['splitwidth'] = "2 per row";
$string['thirdwidth'] = "3 per row";
$string['quarterwidth'] = "4 per row";

// Custom LearningWorks Lang strings.

$string['customsettings'] = 'Custom settings';
$string['customsettings_desc'] = 'The following are settings for additions to the course overview block';

$string['courseimagedefault'] = 'Default course image';
$string['courseimagedefault_desc'] = 'This image will be shown if a course lacks a course summary image file.';

$string['lw_courses_bgimage']   = "Embed images into background";
$string['lw_courses_bgimage_desc']   = "This will embed images into background as a CSS property";

$string['summary_limit'] = 'Summary character limit';
$string['summary_limit_desc'] = 'Limit the output of text to display as course summaries on the users home page';

$string['showteachers'] = 'Show teachers';
$string['showteachers_desc'] = 'This will allow teachers images and names to be rendered against a course';

$string['progress'] = 'Progress type';
$string['progress_desc'] = 'This will determine what type of course grades the block will try to render';

$string['progressenabled'] = 'Course progress bar';
$string['progressenabled_desc'] = 'This will determine if the course progress bar will be shown to learners';

$string['startgrid'] = "Start as grid";
$string['startgrid_desc'] = "This will render the courses in a grid straight away";

$string['noprogress'] = ": enable course completion tracking!";
$string['progressunavail'] = "Progress unavailable";
$string['nocompletion'] = "Completion not enabled";

$string['privacy:metadata'] = 'The My Courses (lw_courses) block only shows information about courses and does not store data itself.';