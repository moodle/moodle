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
 * Progress Bar block English language translation
 *
 * @package    contrib
 * @subpackage block_iomad_progress
 * @copyright  2010 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Module names.
$string['aspirelist'] = 'Aspire resource list';
$string['assign'] = 'Assignment';
$string['assignment'] = 'Assignment';
$string['book'] = 'Book';
$string['bigbluebuttonbn'] = 'Big Blue Button';
$string['certificate'] = 'Certificate';
$string['chat'] = 'Chat';
$string['choice'] = 'Choice';
$string['data'] = 'Database';
$string['feedback'] = 'Feedback';
$string['flashcardtrainer'] = 'Flashcard trainer';
$string['folder'] = 'Folder';
$string['forum'] = 'Forum';
$string['glossary'] = 'Glossary';
$string['hotpot'] = 'Hot Potatoes';
$string['hsuforum'] = 'Advanced Forum';
$string['imscp'] = 'IMS Content Package';
$string['journal'] = 'Journal';
$string['lesson'] = 'Lesson';
$string['lti'] = 'External tool';
$string['page'] = 'Page';
$string['panopto'] = 'Panopto video';
$string['questionnaire'] = 'Questionnaire';
$string['quiz'] = 'Quiz';
$string['resource'] = 'File';
$string['recordingsbn'] = 'BBB Recordings';
$string['scorm'] = 'SCORM';
$string['turnitintool'] = 'Turnitin Tool';
$string['url'] = 'URL';
$string['wiki'] = 'Wiki';
$string['workshop'] = 'Workshop';

// Actions.
$string['activity_completion'] = 'activity completion';
$string['answered'] = 'answered';
$string['assessed'] = 'assessed';
$string['attempted'] = 'attempted';
$string['awarded'] = 'awarded';
$string['completed'] = 'completed';
$string['finished'] = 'finished';
$string['graded'] = 'graded';
$string['marked'] = 'marked';
$string['passed'] = 'passed';
$string['passedby'] = 'passed by deadline';
$string['passedscorm'] = 'passed';
$string['posted_to'] = 'posted to';
$string['responded_to'] = 'responded to';
$string['submitted'] = 'submitted';
$string['viewed'] = 'viewed';

// Stings for the Config page.
$string['config_default_title'] = 'Progress Bar';
$string['config_group'] = 'Visible only to group';
$string['config_header_action'] = 'Action';
$string['config_header_expected'] = 'Expected by';
$string['config_header_icon'] = 'Icon';
$string['config_header_locked'] = 'Locked to deadline';
$string['config_header_monitored'] = 'Monitored';
$string['config_icons'] = 'Use icons in bar';
$string['config_now'] = 'Use';
$string['config_percentage'] = 'Show percentage to students';
$string['config_title'] = 'Alternate title';
$string['config_orderby'] = 'Order bar items by';
$string['config_orderby_due_time'] = '"Expected by" date-time';
$string['config_orderby_course_order'] = 'Ordering in course';

// Help strings.
$string['why_set_the_title'] = 'Why you might want to set the block instance title?';
$string['why_set_the_title_help'] = '
<p>There can be multiple instances of the Progress Bar block. You may use different Progress Bar blocks to monitor different sets of activities or resources. For instance you could track progress in assignments in one block and quizzes in another. For this reason you can override the default title and set a more appropriate block title for each instance.</p>
';
$string['why_use_icons'] = 'Why you might want to use icons?';
$string['why_use_icons_help'] = '
<p>You may wish to add tick and cross icons in the Progress Bar to make this block more visually accessible for students with colour-blindness.</p>
<p>It may also make the meaning of the block clearer if you believe colours are not intuitive, either for cultural or personal reasons.</p>
';
$string['why_display_now'] = 'Why you might want to hide/show the NOW indicator?';
$string['why_display_now_help'] = '
<p>Not all course are focussed on completion of tasks by specific times. Some courses may have an open-enrolment, allowing students to enrol and complete when they can.</p>
<p>To use the Progress Bar as a tool in such courses, create "Expected by" dates in the far-future and set the "Use NOW" setting to No.</p>
';
$string['what_does_monitored_mean'] = 'What monitored means?';
$string['what_does_monitored_mean_help'] = '
<p>The purpose of this block is to encourage students to manage their time effectively. Each student can monitor their progress in completing the activities and resources you have created.</p>
<p>On the configuration page you will see a list of all the modules that you have created which can be monitored by the Progress Bar block. Modules will only be monitored and appear as a small square in the progress bar if you select Yes to monitor the module.</p>
';
$string['what_locked_means'] = 'What locked to deadline means?';
$string['what_locked_means_help'] = '
<p>Where an activity can, in its own settings, have a deadline, and a deadline is set, it is optional to use the deadline of the activity, or to set another separate time used for the activity in the Progress Bar.</p>
<p>To lock the Progress Bar to an activity\'s deadline it must have a deadline enabled and set. If the deadline is locked, changing the deadline in the activity\'s settings will automatically change the time associated with the activity in the Progress Bar.</p>
<p>When an activity is not locked to a deadline of the activity, changing the date and time in the Progress Bar settings will not affect the deadline of the activity.</p>
';
$string['what_expected_by_means'] = 'What expected by means?';
$string['what_expected_by_means_help'] = '
<p>The <em>Expected by</em> date-time is when the related activity/resource is expected to be completed (viewed, submitted, posted-to, etc...).</p>
<p>If there is already a deadline associated with an activity, like an assignment deadline, this deadline can be used as the expected time for the event as long as the "Locked to Deadline" checkbox is checked. By deselecting locking an independent expected time can be created, and altering this will not affect the actual deadline of the activity.</p>
<p>When you first visit the configuration page for the Progress Bar, or if you create a new activity/resource and return to the configuration page, a guess will be made about the expected date-time for the activity/resource.
<ul>
    <li>For an activity with an existing deadline, this deadline will used.</li>
    <li>When there is no activity deadline, but the course format used is a weekly format, the end of the week (just before midnight Sunday) is assumed.</li>
    <li>For an activity/resource not used in a weekly course format, the end of the current week (just before midnight next Sunday) is used.</li>
</ul>
</p>
<p>Once an expected date-time is set, it is independent of any deadline or other information for that activity/resource.</p>
';
$string['what_actions_can_be_monitored'] = 'What actions can be monitored?';
$string['what_actions_can_be_monitored_help'] = '
<p>Different activities and resources can be monitored.</p>
<p>Because different activities and resources are used differently, what is monitored for each module varies. For example, for assignments, submission can be monitored; quizzes can be monitored on attempt; forums can be monitored for student postings; choice activities can monitored for answering and viewing resources is monitored.</p>
<h3>Passing</h3>
<p>For the assignment, lesson and quiz modules, the notion of passing relies on a "Grade to pass" being set for the grade item in the Gradebook. <a href="http://docs.moodle.org/en/Grade_items#Activity-based_grade_items" target="_blank">More help...</a></p>
';
$string['why_show_precentage'] = 'Why show a progress percentage to students?';
$string['why_show_precentage_help'] = '
<p>It is possible to show an overall percentage of progress to students.</p>
<p>This is calculated as the number of items complete divided by the total number of items in the bar.</p>
<p>The progress percentage appears until the student mouses over an item in the bar.</p>
';
$string['how_ordering_works'] = 'How ordering works';
$string['how_ordering_works_help'] = '
<p>There are two ways items in the Progress Bar can be ordered.</p>
<ul>
    <li><em>"Expected by" date-time</em> (default)<br />
    The due dates or manually set dates of activities/resources are used to order items shown in the Progress Bar.
    </li>
    <li><em>Ordering in course</em><br />
    Activities/resources are presented in the same order as they are on the course page. When this option is used, time-related aspects are disabled.
    </li>
</ul>
';
$string['how_group_works'] = 'How visible group works';
$string['how_group_works_help'] = '
<p>Selecting a group will limit the display of the this block to that group only.</p>
';

// Other terms.
$string['addallcurrentitems'] = 'Add all activities/resources';
$string['mouse_over_prompt'] = 'Mouse over block for info.';
$string['no_events_config_message'] = 'There are no activities or resources to monitor the progress of. Create some activities and/or resources then configure this block.';
$string['no_events_message'] = 'No activities or resources are being monitored. Use config to set up monitoring.';
$string['no_visible_events_message'] = 'None of the monitored events are currently visible.';
$string['now_indicator'] = 'NOW';
$string['pluginname'] = 'Progress Bar';
$string['privacy:metadata'] = 'The IOMAD progress bar block only shows data stored in other locations.';
$string['selectitemstobeadded'] = 'Select activities/resources';
$string['time_expected'] = 'Expected';

// Global setting strings
// Default colours that may have different cultural meanings.
// Note that these defaults can be overridden by the block's global settings.
$string['attempted_colour'] = '#5CD85C';
$string['notAttempted_colour'] = '#FF5C5C';
$string['futureNotAttempted_colour'] = '#5C5CFF';
$string['attempted_colour_title'] = 'Attempted Colour';
$string['attempted_colour_descr'] = 'HTML Colour code for elements that have been attempted';
$string['notattempted_colour_title'] = 'Not-attempted Colour';
$string['notattempted_colour_descr'] = 'HTML Colour code for current elements that have not yet been attempted';
$string['futurenotattempted_colour_title'] = 'Future Not-attempted Colour';
$string['futurenotattempted_colour_descr'] = 'HTML Colour code for future elements that have not yet been attemted';
$string['coursenametoshow'] = 'Course name to show on Dashboard';
$string['shortname'] = 'Short course name';
$string['fullname'] = 'Full course name';

// Overview page strings.
$string['lastonline'] = 'Last in course';
$string['overview'] = 'Overview of students';
$string['iomad_progress'] = 'Progress';
$string['iomad_progressbar'] = 'Progress Bar';

// For cabailities.
$string['iomad_progress:overview'] = 'View course overview of Progress bars for all students';
$string['iomad_progress:addinstance'] = 'Add a new Progress Bar block';
$string['iomad_progress:myaddinstance'] = 'Add a Progress Bar block to My home page';

// For Cache.
$string['cachedef_cachedlogs'] = 'Progress Bar log query caching';

// For My home page.
$string['no_blocks'] = "No Progress Bar blocks are set up for your courses.";
