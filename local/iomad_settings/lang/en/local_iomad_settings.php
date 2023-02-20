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
 * @package   local_iomad_settings
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'IOMAD settings';
$string['privacy:metadata'] = 'The Local IOMAD settings plugin only shows data stored in other locations.';
$string['customtext2'] = 'Custom Text 2';
$string['customtext3'] = 'Custom Text 3';
$string['dateformat'] = 'Date format';
$string['emaildelay'] = 'Email delay';
$string['emaildelay_help'] = 'Any IOMAD emails will have this value (in seconds) added to the send time by default.  This allows for a default delay in sending, much like for forum posts, of any IOMAD email.  Timings will still be impacted by the local_mail cron task but this delay will be a minimum value';
$string['iomad_autoenrol_managers'] = 'Enrol managers as non students';
$string['iomad_autoenrol_managers_help'] = 'If this is unticked then manager accounts will not be enrolled as the company teacher roles on manual enrol courses ';
$string['iomad_autoreallocate_licenses'] = 'Automatically re-allocate license';
$string['iomad_autoreallocate_licenses_help'] = 'If this is ticked then when a user\'s licensed course entry is deleted within the user report then the system will automatically try to re-allocate another from the company license pool';
$string['iomadcertificate_logo'] = 'Default logo for IOMAD Company certificate';
$string['iomadcertificate_signature'] = 'Default signature for IOMAD Company certificate';
$string['iomadcertificate_border'] = 'Default border for IOMAD Company certificate';
$string['iomadcertificate_watermark'] = 'Default watermark for IOMAD Company certificate';
$string['iomadcertificate_logodesc'] = 'This is the default logo image used for the IOMAD Company certificate type. You can override it in the company edit pages. The uploaded image should be 80 pixels high and have a transparent background.';
$string['iomadcertificate_signaturedesc'] = 'This is the default signature image used for the IOMAD Company certificate type. You can override it in the company edit pages. The uploaded image should be 31 pixels x 150 pixels and have a transparent background';
$string['iomadcertificate_borderdesc'] = 'This is the default border image used for the IOMAD Company certificate type. You can override it in the company edit pages. The uploaded image should be 800 pixels x 604 pixels.';
$string['iomadcertificate_watermarkdesc'] = 'This is the default watermark image used for the IOMAD Company certificate type. You can override it in the company edit pages. The uploaded image should be no more than 800 pixels x 604 pixels.';
$string['iomad_allow_username'] = 'Can specify username';
$string['iomad_allow_username_help'] = 'Selecting this will allow the username field to be presented when creating accounts.  This will supercede the use email address as username setting.';
$string['iomad_hidevalidcourses'] = 'Show only current course results in reports as default';
$string['iomad_hidevalidcourses_help'] = 'This changes the display of the completion reports so that it only shows current course results (ones which have not yet expired or have no expiry) by default.';
$string['iomad_max_list_classrooms'] = 'Maximum listed classrooms';
$string['iomad_max_list_classrooms_help'] = 'This defines the maximum number of classrooms displayed on a page';
$string['iomad_max_list_companies'] = 'Maximum listed companies';
$string['iomad_max_list_companies_help'] = 'This defines the maximum number of companies displayed on a page';
$string['iomad_max_list_competencies'] = 'Maximum listed competencies';
$string['iomad_max_list_competencies_help'] = 'This defines the maximum number of competencies displayed on a page';
$string['iomad_max_list_courses'] = 'Maximum listed courses';
$string['iomad_max_list_courses_help'] = 'This defines the maximum number of courses displayed on a page';
$string['iomad_max_list_email_templates'] = 'Maximum listed email templates';
$string['iomad_max_list_email_templates_help'] = 'This defines the maximum number of email templates displayed on a page';
$string['iomad_max_list_frameworks'] = 'Maximum listed frameworks';
$string['iomad_max_list_frameworks_help'] = 'This defines the maximum number of frameworks displayed on a page';
$string['iomad_max_list_licenses'] = 'Maximum listed licenses';
$string['iomad_max_list_licenses_help'] = 'This defines the maximum number of licenses displayed on a page';
$string['iomad_max_list_templates'] = 'Maximum listed learning plan templates';
$string['iomad_max_list_templates_help'] = 'This defines the maximum number of learning plan templates displayed on a page';
$string['iomad_max_list_users'] = 'Maximum listed users';
$string['iomad_max_list_users_help'] = 'This defines the maximum number of users displayed on a page';
$string['iomad_max_select_courses'] = 'Maximum listed courses in selector';
$string['iomad_max_select_courses_help'] = 'This defines the maximum number of courses displayed in a form search selector before too many courses is shown';
$string['iomad_max_select_frameworks'] = 'Maximum listed frameworks in selector';
$string['iomad_max_select_frameworks_help'] = 'This defines the maximum number of frameworks displayed in a form search selector before too many frameworks is shown';
$string['iomad_max_select_templates'] = 'Maximum listed learning plan templates in selector';
$string['iomad_max_select_templates_help'] = 'This defines the maximum number of learning plan templates displayed in a form search selector before too many templates is shown';
$string['iomad_max_select_users'] = 'Maximum listed users in selector';
$string['iomad_max_select_users_help'] = 'This defines the maximum number of users displayed in a form search selector before too many users is shown';
$string['iomad_report_fields'] = 'Additional report profile fields';
$string['iomad_report_fields_help'] = 'This is a list of profile fields separated by a comma.  If you want to use an optional profile field you need to use profile_field_<shortname> where <shortname> is the shortname defined for the profile field. The order given is the order they are displayed in.';
$string['iomad_report_grade_places'] = 'Number of decimal places for grades in reports';
$string['iomad_report_grade_places_help'] = 'This defines the number of decimal places which will be displayed in IOMAD reports whenever a users grade is listed';
$string['iomad_settings:addinstance'] = 'Add a new IOMAD Settings';
$string['iomad_showcharts'] = 'Show course completion charts as default';
$string['iomad_showcharts_help'] = 'If checked the charts will be shown fist with an option to show as text instead';
$string['iomad_sync_department'] = 'Sync company department with profile';
$string['iomad_sync_department_help'] = 'Selecting this will keep the user profile field for department in sync with the name of the company department that the user is in.';
$string['iomad_sync_institution'] = 'Sync company name with profile';
$string['iomad_sync_institution_help'] = 'Selecting this will keep the user profile field for institution in sync with the shortname of the company that the user is in.';
$string['iomad_use_email_as_username'] = 'Use email address as user name';
$string['iomad_use_email_as_username_help'] = 'Selecting this will change the way a user name is automatically created for a new user account in IOMAD so that it simply uses the email address.';
$string['reset_annually'] = 'Annually';
$string['reset_daily'] = 'Daily';
$string['reset_never'] = 'Never';
$string['reset_sequence'] = 'Reset sequence number';
$string['serialnumberformat'] = 'Serial Number Format';
$string['serialnumberformat_help'] = '<p>The Custom Text fields and the Serial Number Format can have the following variables:</p><ul>
                                        <li>{EC} = Establishment Code</li>
                                        <li>{CC} = Course ID Number</li>
                                        <li>{CD:DDMMYY} = Date (with format)</li>
                                        <li>{SEQNO:n} = Sequence Number (with padding n)</li>
                                        <li>{SN} = Certificate Serial Number (blank if used in Serial Number Format field))</li>
                                        </ul>';

// SAMPLE Certificate.
$string['sampletitle'] = 'Certificate of Training';
$string['samplecertify'] = 'This is to certify that';
$string['samplestatement'] = 'has completed an e-learning course on';
$string['sampledate'] = 'on';
$string['samplecoursegrade'] = 'with the result of';
$string['typesample'] = 'Sample';
$string['samplecode'] = 'Certificate Number :';
$string['samplesigned'] = 'Signed: ';
$string['sampleonbehalfof'] = 'On behalf of Company';
