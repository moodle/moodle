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
 * @package    block_pu
 * @copyright  2021 onwards LSU Online & Continuing Education
 * @copyright  2021 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Block.
$string['pluginname'] = 'ProctorU Access Codes';
$string['foldername'] = 'Manage ProctorU';
$string['adminname'] = 'Manage ProctorU Codes';

// Tasks.

// Capabilities.
$string['pu:admin'] = 'Administer the ProctorU Access Code system.';
$string['pu:addinstance'] = 'Add a new ProctorU Access Code block to a course page';
$string['pu:myaddinstance'] = 'Add a new ProctorU Access Code block to the /my page';

// General terms.
$string['backtocourse'] = 'Back to course';
$string['backtohome'] = 'Back to home';

// Settings management.
$string['default_numcodes'] = '# of exams';
$string['default_numcodes_help'] = 'The default number of proctored exams (ProctorU access codes) issued to a user in a course';
$string['pu_ccfile'] = 'Access codes file';
$string['pu_ccfile_help'] = 'The location for the ProctorU access codes file.<br>File has 5 fields with the access code in the 2nd field and can contain a header.';

$string['pu_guildfile'] = 'Guild Mapping File';
$string['pu_guildfile_help'] = 'The location for the GUILD section / student mapping file.<br>One comma seperated sectionid, LSUID pair per line with no header.<br>2021ENGL1001, 891234567';

$string['pu_sectionmap'] = 'Section Mapping';
$string['pu_sectionmap_help'] = 'Use LSU style Universal Enrollment System course-section mapping method.';
// Configuration.
$string['manage_invalids'] = 'Invalid codes';
$string['manage_invalids_help'] = 'Manage whether invalid access codes are confirmed by ProctorU to either be "unused and valid" or "used or invalid" access codes.';
$string['manage_overrides'] = 'Manage overrides';
$string['manage_overrides_help'] = 'Manage the number of proctored exams and replacement ProctorU access codes at the course shell level.
                                    <strong>This setting, if overridden, will be the final determining factor for how many exams (access codes) 
                                    and how many replacement access codes are allowed per person in the specified course.</strong>';
$string['manage_overrides_help2'] = 'Please note the sitewide default for ProctorU access codes per course is {$a->percourse}.
                                     The number of replacement codes is tied to the sitewide default of {$a->percourse} as well. Feel free to override these values below.';
$string['override_numcodes'] = 'Number of exams';
$string['override_numcodes_help'] = 'Override the default number of access codes for this course.';
$string['override_numinvalid'] = 'Number of replacement codes';
$string['override_numinvalid_help'] = 'Override the default number of replacement codes for this course. By default this is capped at the number of exams in this course.';
$string['defaultsnull_codes'] = 'The defualt for this course is {$a->numcodes}.';

$string['pu_profilefield'] = 'Profile Field for import';
$string['pu_profilefield_help'] = 'This is either the standard user IDNumber or whichever other additional profile field you choose.<br>This field is what we key on when importing data.';

// File Uploader
$string['manage_uploader'] = 'File Uploader';
$string['manage_viewer'] = 'File Viewer';
$string['pu_uploadstring'] = 'Upload a File';
$string['no_upload_permissions'] = 'You do not have permission to upload codes.';
$string['pu_file_link'] = 'File Link';
$string['pu_filename'] = 'File Name';
$string['pu_filecreated'] = 'Created';
$string['pu_filemodified'] = 'Last Modified';
$string['pu_copy'] = 'Copy File';
$string['pu_delete'] = 'Delete File';
$string['pu_nofiles'] = 'No Files To Display';
$string['dashboard'] = 'Dashboard';
$string['pu_settings'] = 'PU Settings';
$string['pu_copy_file'] = 'Copy File Location';
$string['pu_copy_file_help'] = 'Files can be uploaded and copied to the location specified here. (include forward slash at the end/)';
$string['no_upload_permissions'] = 'You do not have permission to upload and view files.';

// Block strings.
$string['pu_block_intro_one'] = 'Your first ProctorU access code for <strong>{$a->coursename}</strong> is:';
$string['pu_block_intro_multi'] = 'Your {$a->numassigned} ProctorU access codes for <strong>{$a->coursename}</strong> are:';
$string['pu_docs_intro'] = 'What you need to know about access codes:';
$string['pu_docs_intronone'] = 'You have not claimed any ProctorU access codes for this course yet.<br>If you are ready to schedule your exam, select the "<strong>Claim your access code</strong>" button above.';
$string['pu_docs_allocatednum'] = 'You have <strong>requested</strong> {$a->numallocated} of the {$a->numtotal} access codes allowed in this course.';
$string['pu_docs_usednum'] = 'You have <strong>used</strong> {$a->numused} of the {$a->numtotal} access codes for this course.';
$string['pu_docs_noneleft'] = 'You have used all of the access codes allowed for this course.<br>If you need another code, please contact <a href="mailto:answers@outreach.lsu.edu">answers@outreach.lsu.edu</a>.';
$string['pu_docs_touse'] = 'To use your ProctorU access code, please go to your exam module in your course and use the "<strong>Schedule Exam</strong>" link to route to ProctorU.<br>You will use the top (latest) ProctorU access code in place of payment.';
$string['pu_docs_used'] = 'If you have used the top (latest) access code and need another for your next exam, please click the "<strong>Mark used</strong>" button below:';
$string['pu_docs_requestedall'] = 'If you have used the top (latest) access code, please click the "<strong>Mark used</strong>" button below:';
$string['pu_docs_invalid'] = 'If the top (latest) access code does not work, request a replacement code by clicking on the "<strong>This access code did not work</strong>" button below:';
$string['pu_docs_invalidsused'] = 'You have received {$a->numused} replacement access codes from the pool of {$a->numtotal} available for this course.';
$string['pu_docs_invalidsfull'] = 'You have requested all of the available replacement access codes available for this course.<br>If you need another access code, please contact <a href="mailto:answers@outreach.lsu.edu">answers@outreach.lsu.edu</a>.';
$string['pu_docs_invalidsnone'] = 'If there is a problem with your access code and need another, please contact <a href="mailto:answers@outreach.lsu.edu">answers@outreach.lsu.edu</a>.';
$string['pu_used'] = 'Mark used';
$string['pu_new'] = 'Claim your access code';
$string['pu_past'] = 'Used code ';
$string['import_codes'] = 'Import ProctorU access codes';
$string['import_guild'] = 'Import GUILD data';
$string['import_unmap'] = 'Unmap orphaned ProctorU access codes';
$string['pu_codeslow'] = 'Emails when ProctorU access codes are low';
$string['pu_mincodes'] = 'Minimum number of access codes';
$string['pu_mincodes_help'] = 'The minimum number of valid ProctorU access codes left in the system before an email is triggered.';
$string['pu_minlines'] = 'Minimum lines in GUILD file';
$string['pu_minlines_help'] = 'The minimum number of lines in the provided GUILD file to process mapping.';
$string['pu_code_admin'] = 'ProctorU administrators';
$string['pu_code_admin_help'] = 'The Moodle usernames designated to be the ProctorU access code administrators.';
$string['invalid_code'] = '<strong>{$a->accesscode}</strong> - {$a->course} - {$a->user} - {$a->idnumber} - {$a->email}';
$string['opts0'] = 'Do nothing'; 
$string['opts1'] = 'Mark unused and valid'; 
$string['opts2'] = 'Permanently mark used or invalid'; 

// Interstitial strings.
$string['pu_yousure'] = 'Are you sure you need a replacement ProctorU access code?<ul>
                         <li>Have you already used the access code in a previous testing session? If so, did you mark the previous access code used?</li>
                         <li>Have you copied and pasted the access code without spaces before and after when paying for your testing session?</li></ul>';
$string['pu_replace'] = 'This access code did not work';
$string['pu_try_again'] = 'Let me try this access code again';

// Error strings.
$string['nopermissions'] = 'You do not have permission to modify ProctorU access codes.';
$string['no_override_permissions'] = 'You do not have permission to modify exam overrides.';
$string['nopermission'] = 'You do not have permission to modify the requested ProctorU access code.<br><br>Please contact an administrator if you believe you should be able to modify that specific code.';
$string['markused'] = 'Successfully marked your latest ProctorU access code as used and requested a new code.';
$string['lastused'] = 'Successfully marked your final ProctorU access code as used.';
$string['markinvalid'] = 'Successfully marked your latest ProctorU access code as invalid and requested a replacement.';
$string['assigned'] = 'You have been assigned a new ProctorU access code.';
$string['nothingtodo'] = 'There was nothing to do.';
$string['override_complete'] = 'Successfully saved override values.';
$string['validate_complete'] = 'Successfully saved code validation values.';
$string['nomorecodes'] = 'The ProctorU access code system is out of codes, please contact <a href="mailto:answers@outreach.lsu.edu">answers@outreach.lsu.edu</a>.';
