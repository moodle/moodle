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
 * LTI enrolment plugin version information
 *
 * @package enrol_lti
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['enrolenddate'] = 'End date';
$string['enrolenddate_help'] = 'If enabled, users can access until this date only.';
$string['enrolenddateerror'] = 'Enrolment end date cannot be earlier than start date';
$string['enrolisdisabled'] = 'The LTI enrolment plugin is disabled.';
$string['enrolperiod'] = 'Enrolment duration';
$string['enrolperiod_help'] = 'Length of time that the enrolment is valid, starting with the moment the user enrols themselves from the remote system. If disabled, the enrolment duration will be unlimited.';
$string['enrolmentfinished'] = 'Enrolment finished.';
$string['enrolmentnotstarted'] = 'Enrolment has not started.';
$string['enrolstartdate'] = 'Start date';
$string['enrolstartdate_help'] = 'If enabled, users can access from this date onward only.';
$string['globalsharedsecret'] = 'Global shared secret';
$string['gradesync'] = 'Grade synchronisation';
$string['gradesync_help'] = 'This determines if we want grade synchronisation to occur.';
$string['maxenrolled'] = 'Maximum enrolled';
$string['maxenrolled_help'] = 'Specifies the maximum number of users that can access from the remote system. The value \'0\' means there is no limit.';
$string['maxenrolledreached'] = 'Maximum number of users allowed to access was already reached.';
$string['membersync'] = 'Member synchronisation';
$string['membersync_help'] = 'This determines if we want member synchronisation to occur.';
$string['membersyncmode'] = 'Members synchronisation mode';
$string['membersyncmode_help'] = 'This setting determines what we should do when synchronising members.';
$string['membersyncmodeenrolandunenrol'] = 'Enrol new and unenrol missing members';
$string['membersyncmodeenrolnew'] = 'Enrol new members';
$string['membersyncmodeunenrolmissing'] = 'Unenrol missing members';
$string['notoolsprovided'] = 'No tools provided';
$string['lti:config'] = 'Configure LTI enrol instances';
$string['lti:unenrol'] = 'Unenrol users from the course';
$string['pluginname'] = 'Shared external tool';
$string['pluginname_desc'] = 'The shared external tool plugin allows externals users to access a course or an activity via a unique link - this requires the LTI authentication plugin to be enabled.';
$string['remotesystem'] = 'Remote system';
$string['requirecompletion'] = 'Require the course or activity to be completed before sending the grades';
$string['roleinstructor'] = 'Role for instructor';
$string['roleinstructor_help'] = 'This is the role that will be assigned at the context of the tool specificed to LTI consumer instructor.';
$string['rolelearner'] = 'Role for learner';
$string['rolelearner_help'] = 'This is the role that will be assigned at the context of the tool specificed to the LTI consumer student.';
$string['secret'] = 'Secret';
$string['secret_help'] = 'This is the secret that is shared with the LTI consumer in order for them to access this tool';
$string['sharedexternaltools'] = 'Shared external tools';
$string['syncsettings'] = 'Synchronisation settings';
$string['tooldoesnotexist'] = 'The requested tool does not exist.';
$string['toolsprovided'] = 'Tools provided';
$string['tooltobeprovided'] = 'Tool to be provided';
$string['userdefaultvalues'] = 'User default values';
