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
 * Language strings for the WDS Post Grades block.
 *
 * @package    block_wds_postgrades
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'WDS Post Grades';
$string['wds_postgrades:addinstance'] = 'Add a new WDS Post Grades block.';
$string['wds_postgrades:view'] = 'View WDS Post Grades block.';
$string['firstname'] = 'First Name';
$string['lastname'] = 'Last Name';
$string['fullname'] = 'Student';
$string['universalid'] = 'Universal ID';
$string['gradingscheme'] = 'Grading Scheme';
$string['gradingbasis'] = 'Grading Basis';
$string['grade'] = 'Workday Grade';
$string['finalgrade'] = 'Workday {$a->typeword} Grade';
$string['gradetype'] = '{$a} Grades';
$string['gradecode'] = 'Workday Grade Code';
$string['nograde'] = 'No grade';
$string['letter'] = 'Letter grade';
$string['nopermission'] = 'You do not have permission to view this information.';
$string['nostudents'] = '<strong>No graded students found in this course. Please add grades to the course.</strong>';
$string['nocoursegrade'] = 'No course grade item found.';
$string['viewgrades'] = 'View {$a->typeword} Grades';
$string['gradesfor'] = '{$a->typeword} Grades for {$a->sectiontitle}';
$string['viewgradesfor'] = 'View {$a->typeword} Grades for {$a->sectiontitle}';
$string['backtocourse'] = 'Back to course';

// Form stuffs.
$string['wds_postgrades:post'] = 'Post grades to Workday Student';
$string['postgrades'] = 'Post Grades to Workday';
$string['postgradessuccess'] = 'Grades successfully posted to Workday';
$string['postgradefailed'] = 'No grades were posted to Workday due to the following errors.';
$string['postgradeservererror'] = 'Server error occurred while posting grades: {$a}';
$string['postgradepartial'] = 'Some grades were successfully posted, but others failed';

// New strings for posting method
$string['postingmethod'] = 'Grade Posting Method';
$string['postingmethoddesc'] = 'Choose whether to post all student grades at once (batch) or one student at a time (individual)';
$string['postingmethodbatch'] = 'Post all student grades together';
$string['postingmethodindividual'] = 'Post each student grade individually';
$string['connectionerror'] = 'Connection error during grade posting';
$string['servererror'] = 'Server error: {$a}';

// Results page stuffs.
$string['postgraderesults'] = 'Grade Posting Results';
$string['errordetails'] = 'Error Details';
$string['successdetails'] = 'Successfully Posted Grades';
$string['errormessage'] = 'Error Message';
$string['status'] = 'Status';
$string['gradeposted'] = 'Grade posted successfully';
$string['unknownerror'] = 'Unknown error occurred';
$string['sectionlisting'] = 'Section Listing: {$a->sectiontitle}';
$string['sectiongraded'] = 'Grades for {$a->sectiontitle} have been sent to Workday Student.';

// Multiple section postings.
$string['postallgrades'] = 'Post all course grades';
$string['individualsections'] = 'Post grades by section';
$string['postgradesfor'] = 'Post Grades for {$a->sectiontitle}';
$string['viewgradesfor'] = 'View Grades for {$a->sectiontitle}';
$string['section'] = 'Section';

// Settings strings.
$string['settings'] = 'WDS Post Grades Settings';
$string['periodheading'] = 'Academic Period: {$a}';
$string['perioddescription'] = 'Configure the start and end dates for posting interim grades during this academic period.';
$string['periodstartdate'] = 'Start date';
$string['periodstartdatedesc'] = 'The date and time when posting interim grades becomes available.';
$string['periodenddate'] = 'End date';
$string['periodenddatedesc'] = 'The date and time when posting interim grades is no longer available.';
$string['noperiods'] = 'No Active Academic Periods';
$string['nogradepostingperiod'] = 'No active grade posting periods are available at this time.';
$string['noperiodsdesc'] = 'No active academic periods were found. Settings will appear when active periods are available.';
$string['gradesnotconfigured'] = '{$a->typeword} grades posting dates have not been configured for this period.';
$string['gradesnotavailable'] = '{$a} grades posting is currently not available for this section.';
$string['gradesfuture'] = '{$a->typeword} grades posting will be available in {$a->time}.';
$string['gradespast'] = '{$a} grades posting is no longer available for this period.';
$string['gradesopen'] = '{$a->typeword} grades posting is available. Time remaining: {$a->time}.';

// Period configuration page strings
$string['periodconfig'] = 'Grading Period Configuration';
$string['endbeforestart'] = 'End date must be after start date';
$string['changessaved'] = 'Changes saved successfully';
$string['periodconfiglinktext'] = 'Configure Grading Periods';
$string['managegradeperiods'] = 'Manage Grade Posting Periods';

// Final grade tracking strings
$string['alreadyposted'] = 'Already posted';
$string['dateposted'] = 'Posted on {$a}';
$string['postedby'] = 'Posted by {$a}';
$string['allgradesposted'] = 'All final grades for this section have been posted to Workday Student.';
$string['nopostablegrades'] = 'There are no grades to post for this section at this time.';
$string['remaininggrades'] = 'Posting {$a} remaining final grades.';

// Last attendance date strings
$string['lastattendancedate'] = 'Last Attendance Date';
$string['lastattendancedaterequired'] = 'Required for failing grade';
$string['lastattendancedatenotapplicable'] = 'Not Required';
$string['lastattendancedatemissing'] = 'A last attendance date is required for failing grades';
$string['lastattendancedateinvalid'] = 'The last attendance date format is invalid';
$string['lastattendancedateexpired'] = 'The last attendance date cannot be in the future';
$string['lastattendancedateexample'] = 'Format: YYYY-MM-DD';

// Workday API settings.
$string['workdayapiurl'] = 'Workday API URL';
$string['workdayapiurldesc'] = 'Enter the base URL for the Workday Student Records API.';
$string['workdayapiversion'] = 'Workday API Version';
$string['workdayapiversiondesc'] = 'Enter the Workday API version. Do not include the "v".';
$string['usernamesuffix'] = 'Username suffix';
$string['usernamesuffixdesc'] = 'Workday webservice requires a username suffix. We use @lsu.';
