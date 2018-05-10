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
 * Strings for component 'enrol_imsenterprise', language 'en'.
 *
 * @package    enrol_imsenterprise
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['aftersaving...'] = 'Once you have saved your settings, you may wish to';
$string['allowunenrol'] = 'Allow the IMS data to <strong>unenrol</strong> students/teachers';
$string['allowunenrol_desc'] = 'If enabled, course enrolments will be removed when specified in the Enterprise data.';
$string['basicsettings'] = 'Basic settings';
$string['categoryidnumber'] = 'Allow category idnumber';
$string['categoryidnumber_desc'] = 'If enabled IMS Enterprise will create category with idnumber';
$string['categoryseparator'] = 'Category separator character';
$string['categoryseparator_desc'] = 'Required when "Category idnumber" is enabled. Character to separate the category name and idnumber.';
$string['coursesettings'] = 'Course data options';
$string['createnewcategories'] = 'Create new (hidden) course categories if not found in Moodle';
$string['createnewcategories_desc'] = 'If the <org><orgunit> element is present in a course\'s incoming data, its content will be used to specify a category if the course is to be created from scratch. The plugin will NOT re-categorise existing courses.

If no category exists with the desired name, then a hidden category will be created.';
$string['createnewcourses'] = 'Create new (hidden) courses if not found in Moodle';
$string['createnewcourses_desc'] = 'If enabled, the IMS Enterprise enrolment plugin can create new courses for any it finds in the IMS data but not in Moodle\'s database. Any newly-created courses are initially hidden.';
$string['createnewusers'] = 'Create user accounts for users not yet registered in Moodle';
$string['createnewusers_desc'] = 'IMS Enterprise enrolment data typically describes a set of users. If enabled, accounts can be created for any users not found in Moodle\'s database.

Users are searched for first by their "idnumber", and second by their Moodle username. Passwords are not imported by the IMS Enterprise plugin. The use of an authentication plugin is recommended for authenticating users.';
$string['cronfrequency'] = 'Frequency of processing';
$string['deleteusers'] = 'Delete user accounts when specified in IMS data';
$string['deleteusers_desc'] = 'If enabled, IMS Enterprise enrolment data can specify the deletion of user accounts (if the "recstatus" flag is set to 3, which represents deletion of an account). As is standard in Moodle, the user record isn\'t actually deleted from Moodle\'s database, but a flag is set to mark the account as deleted.';
$string['doitnow'] = 'perform an IMS Enterprise import right now';
$string['emptyattribute'] = 'Leave it empty';
$string['filelockedmail'] = 'The text file you are using for IMS-file-based enrolments ({$a}) can not be deleted by the cron process.  This usually means the permissions are wrong on it.  Please fix the permissions so that Moodle can delete the file, otherwise it might be processed repeatedly.';
$string['filelockedmailsubject'] = 'Important error: Enrolment file';
$string['fixcasepersonalnames'] = 'Change personal names to Title Case';
$string['fixcaseusernames'] = 'Change usernames to lower case';
$string['imsenterprisecrontask'] = 'Enrolment file processing';
$string['imsenterprise:config'] = 'Configure IMS Enterprise enrol instances';
$string['imsrolesdescription'] = 'The IMS Enterprise specification includes 8 distinct role types. Please choose how you want them to be assigned in Moodle, including whether any of them should be ignored.';
$string['location'] = 'File location';
$string['logtolocation'] = 'Log file output location (blank for no logging)';
$string['mailadmins'] = 'Notify admin by email';
$string['mailusers'] = 'Notify users by email';
$string['messageprovider:imsenterprise_enrolment'] = 'IMS Enterprise enrolment messages';
$string['miscsettings'] = 'Miscellaneous';
$string['nestedcategories'] = 'Allow nested categories';
$string['nestedcategories_desc'] = 'If enabled IMS Enterprise will create nested categories';
$string['pluginname'] = 'IMS Enterprise file';
$string['pluginname_desc'] = 'This method will repeatedly check for and process a specially-formatted text file in the location that you specify.  The file must follow the IMS Enterprise specifications containing person, group, and membership XML elements.';
$string['processphoto'] = 'Add user photo data to profile';
$string['processphotowarning'] = 'Warning: Image processing is likely to add a significant burden to the server. You are recommended not to activate this option if large numbers of students are expected to be processed.';
$string['restricttarget'] = 'Only process data if the following target is specified';
$string['restricttarget_desc'] = 'An IMS Enterprise data file could be intended for multiple "targets" - different LMSes, or different systems within a school/university. It\'s possible to specify in the Enterprise file that the data is intended for one or more named target systems, by naming them in <target> tags contained within the <properties> tag.

In general you don\'t need to worry about this. Leave the setting blank and Moodle will always process the data file, no matter whether a target is specified or not. Otherwise, fill in the exact name that will be output inside the <target> tag.';
$string['settingfullname'] = 'IMS description tag for the course full name';
$string['settingfullnamedescription'] = 'The full name is a required course field so you have to define the selected description tag in your IMS Enterprise file';
$string['settingshortname'] = 'IMS description tag for the course short name';
$string['settingshortnamedescription'] = 'The short name is a required course field so you have to define the selected description tag in your IMS Enterprise file';
$string['settingsummary'] = 'IMS description tag for the course summary';
$string['settingsummarydescription'] = 'Is an optional field, select \'Leave it empty\' if you dont\'t want to specify a course summary';
$string['sourcedidfallback'] = 'Use the &quot;sourcedid&quot; for a person\'s userid if the &quot;userid&quot; field is not found';
$string['sourcedidfallback_desc'] = 'In IMS data, the <sourcedid> field represents the persistent ID code for a person as used in the source system. The <userid> field is a separate field which should contain the ID code used by the user when logging in. In many cases these two codes may be the same - but not always.

Some student information systems fail to output the <userid> field. If this is the case, you should enable this setting to allow for using the <sourcedid> as the Moodle user ID. Otherwise, leave this setting disabled.';
$string['truncatecoursecodes'] = 'Truncate course codes to this length';
$string['truncatecoursecodes_desc'] = 'In some situations you may have course codes which you wish to truncate to a specified length before processing. If so, enter the number of characters in this box. Otherwise, leave the box blank and no truncation will occur.';
$string['updatecourses'] = 'Update course';
$string['updatecourses_desc'] = 'If enabled, the IMS Enterprise enrolment plugin can update course full and short names (if the "recstatus" flag is set to 2, which represents an update).';
$string['updateusers'] = 'Update user accounts when specified in IMS data';
$string['updateusers_desc'] = 'If enabled, IMS Enterprise enrolment data can specify changes to user accounts (if the "recstatus" flag is set to 2, which represents an update).';
$string['usecapitafix'] = 'Tick this box if using &quot;Capita&quot; (their XML format is slightly wrong)';
$string['usecapitafix_desc'] = 'The student data system produced by Capita has been found to have one slight error in its XML output. If you are using Capita you should enable this setting - otherwise leave it un-ticked.';
$string['usersettings'] = 'User data options';
$string['zeroisnotruncation'] = '0 indicates no truncation';
$string['roles'] = 'Roles';
$string['ignore'] = 'Ignore';
$string['importimsfile'] = 'Import IMS Enterprise file';
$string['privacy:metadata'] = 'The IMS Enterprise file enrolment plugin does not store any personal data.';
