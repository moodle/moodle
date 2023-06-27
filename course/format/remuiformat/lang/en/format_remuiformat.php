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
 * Strings for component 'format_remuiformat'
 *
 * @package    format_remuiformat
 * @copyright  2019 Wisdmlabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Plugin Name.
$string['pluginname'] = 'Edwiser course formats';

// Settings.
$string['defaultcoursedisplay'] = 'Course display default';
$string['defaultcoursedisplay_desc'] = "Either show all the sections on a single page or section zero and the chosen section on page.";

$string['defaultbuttoncolour'] = 'Default View topic button colour';
$string['defaultbuttoncolour_desc'] = 'The View topic button colour.';

$string['defaultoverlaycolour'] = 'Default overlay colour when user hover on activities';
$string['defaultoverlaycolour_desc'] = 'The overlay colour when user hover on activities';

$string['enablepagination'] = 'Enable pagination';
$string['enablepagination_desc'] = 'This will enable multiple pages view when the number of section/activities are very large.';

$string['defaultnumberoftopics'] = 'Default number of topics per page';
$string['defaultnumberoftopics_desc'] = 'The number of topics to be displayed in one page';

$string['defaultnumberofactivities'] = 'Default number of activities per page';
$string['defaultnumberofactivities_desc'] = 'The number of activities to be displayed in one page';

$string['off'] = 'Off';
$string['on'] = 'On';

$string['defaultshowsectiontitlesummary'] = 'Show the section title summary on hover option';
$string['defaultshowsectiontitlesummary_desc'] = 'Show the section title summary when hovering over the grid box.';
$string['sectiontitlesummarymaxlength'] = 'Set the section/activities summary maximum length.';
$string['sectiontitlesummarymaxlength_help'] = 'Set the the section/activities title summary maxium length displayed on the card.';
$string['defaultsectionsummarymaxlength'] = 'Set the section/activities summary maximum length.';
$string['defaultsectionsummarymaxlength_desc'] = 'Set the the section/activities summary maxium length displayed on the card.';
$string['hidegeneralsectionwhenempty'] = 'Hide general section when empty';
$string['hidegeneralsectionwhenempty_help'] = 'When general section does not have any activity and summary then you can hide it.';

// Section.
$string['sectionname'] = 'Section';
$string['sectionnamecaps'] = 'SECTION';
$string['section0name'] = 'General';
$string['hidefromothers'] = 'Hide section';
$string['showfromothers'] = 'Show section';
$string['viewtopic'] = 'View';
$string['editsection'] = 'Edit section';
$string['editsectionname'] = 'Edit section name';
$string['newsectionname'] = 'New name for section {$a}';
$string['currentsection'] = 'This section';
$string['addnewsection'] = 'Add Section';
$string['moveresource'] = 'Move resource';

// Activity.
$string['viewactivity'] = 'View Activity';
$string['markcomplete'] = 'Mark Complete';
$string['grade'] = 'Grade';
$string['notattempted'] = 'Not Attempted';
$string['subscribed'] = "Subscribed";
$string['notsubscribed'] = "Not Subscribed";
$string['completed'] = "Completed";
$string['notcompleted'] = 'Not Completed';
$string['progress'] = 'Progress';
$string['showinrow'] = 'Make row';
$string['showincard'] = 'Make card';
$string['moveto'] = 'Move to';
$string['changelayoutnotify'] = 'Refresh page to see changes.';
$string['generalactivities'] = 'Activities';
$string['coursecompletionprogress'] = 'Course Progress';
$string['resumetoactivity'] = 'Resume';

// For list format.
$string['remuicourseformat'] = 'Choose layout';
$string['remuicourseformat_card'] = 'Card Layout';
$string['remuicourseformat_list'] = 'List Layout';
$string['remuicourseformat_help'] = 'Choose a course layout';
$string['remuicourseimage_filemanager'] = 'Course format image';
$string['remuicourseimage_filemanager_help'] = 'This image will be displayed in General section card in card layout and as a background of General section in list layout. <strong>Recommended image size 16:9.<strong>';
$string['addsections'] = 'Add sections';
$string['teacher'] = 'Teacher';
$string['teachers'] = 'Teachers';
$string['remuiteacherdisplay'] = 'Show Teacher image';
$string['remuiteacherdisplay_help'] = 'Show Teacher image in the Course header.';
$string['defaultremuiteacherdisplay'] = 'Show Teacher image';
$string['defaultremuiteacherdisplay_desc'] = 'Show Teacher image in the Course header.';

$string['remuidefaultsectionview'] = 'Choose default sections view';
$string['remuidefaultsectionview_help'] = 'Choose a default view for the sections of the course.';
$string['expanded'] = 'Expand All';
$string['collapsed'] = 'Collapse All';

$string['remuienablecardbackgroundimg'] = 'Section background image';
$string['remuienablecardbackgroundimg_help'] = 'Enable section background image. By default it is disable. It fetches the image from section summary.';
$string['enablecardbackgroundimg'] = 'Show background image to section in card.';
$string['disablecardbackgroundimg'] = 'Hide background image to section in card.';
$string['next'] = 'Next';
$string['previous'] = 'Previous';

$string['remuidefaultsectiontheme'] = 'Choose default sections theme';
$string['remuidefaultsectiontheme_help'] = 'Choose a default theme for the sections of the course.';

$string['dark'] = 'Dark';
$string['light'] = 'Light';

$string['defaultcardbackgroundcolor'] = 'Set the section background color in card format.';
$string['cardbackgroundcolor_help'] = 'Card background color Help.';
$string['cardbackgroundcolor'] = 'Set the section background color in card format.';
$string['defaultcardbackgroundcolordesc'] = 'Card background color Description';

// GDPR.
$string['privacy:metadata'] = 'The Edwiser Course Formats plugin does not store any personal data.';

// Validation.
$string['coursedisplay_error'] = 'Please choose correct combination of layout.';

// Activities completed text.
$string['activitystart'] = "Let's Start";
$string['outof'] = 'out of';
$string['activitiescompleted'] = 'activities completed';
$string['activitycompleted'] = 'activity completed';
$string['activitiesremaining'] = 'activities remaining';
$string['activityremaining'] = 'activity remaining';
$string['allactivitiescompleted'] = "All activities completed";

// Used in format.js on change course layout.
$string['showallsectionperpage'] = 'Show all sections per page';

// Card format general section.
$string['showfullsummary'] = '+ Show full summary';
$string['showless'] = 'Show less';

// Usage tracking.
$string['enableusagetracking'] = "Enable Usage Tracking";
$string['enableusagetrackingdesc'] = "<strong>USAGE TRACKING NOTICE</strong>

<hr class='text-muted' />

<p>Edwiser from now on will collect anonymous data to generate product usage statistics.</p>

<p>This information will help us guide the development in right direction and the Edwiser community prosper.</p>

<p>Having said that we don't gather your personal data or of your students during this process. You can disable this from the plugin whenever you wish to opt out of this service.</p>

<p>An overview of the data collected is available <strong><a href='https://forums.edwiser.org/topic/67/anonymously-tracking-the-usage-of-edwiser-products' target='_blank'>here</a></strong>.</p>";
