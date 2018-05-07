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

$string['pluginname'] = 'Iomad settings';
$string['privacy:metadata'] = 'The Local Iomad settings plugin only shows data stored in other locations.';
$string['establishment_code'] = 'Establishment Code';
$string['establishment_code_help'] = 'What should the admin see in the course list block?';
$string['customtext2'] = 'Custom Text 2';
$string['customtext3'] = 'Custom Text 3';
$string['dateformat'] = 'Date format';
$string['iomad_autoenrol_managers'] = 'Enrol managers as non students'; 
$string['iomad_autoenrol_managers_help'] = 'If this is unticked then manager accounts will not be enrolled as the company teacher roles on manual enrol courses '; 
$string['iomadcertificate_logo'] = 'Default logo for Iomad Company certificate';
$string['iomadcertificate_signature'] = 'Default signature for Iomad Company certificate';
$string['iomadcertificate_border'] = 'Default border for Iomad Company certificate';
$string['iomadcertificate_watermark'] = 'Default watermark for Iomad Company certificate';
$string['iomadcertificate_logodesc'] = 'This is the default logo image used for the Iomad Company certificate type. You can override it in the company edit pages. The uploaded image should be 80 pixels high and have a transparent background.';
$string['iomadcertificate_signaturedesc'] = 'This is the default signature image used for the Iomad Company certificate type. You can override it in the company edit pages. The uploaded image should be 31 pixels x 150 pixels and have a transparent background';
$string['iomadcertificate_borderdesc'] = 'This is the default border image used for the Iomad Company certificate type. You can override it in the company edit pages. The uploaded image should be 800 pixels x 604 pixels.';
$string['iomadcertificate_watermarkdesc'] = 'This is the default watermark image used for the Iomad Company certificate type. You can override it in the company edit pages. The uploaded image should be no more than 800 pixels x 604 pixels.';
$string['iomad_allow_username'] = 'Can specify username';
$string['iomad_allow_username_help'] = 'Selecting this will allow the username field to be presented when creating accounts.  This will supercede the use email address as username setting.';
$string['iomad_report_fields'] = 'Additional report profile fields';
$string['iomad_report_fields_help'] = 'This is a list of profile fields separated by a comma.  If you want to use an optional profile field you need to use profile_field_<shortname> where <shortname> is the shortname defined for the profile field. The order given is the order they are displayed in.';
$string['iomad_settings:addinstance'] = 'Add a new Iomad Settings';
$string['iomad_sync_department'] = 'Sync company department with profile';
$string['iomad_sync_department_help'] = 'Selecting this will keep the user profile field for department in sync with the name of the company department that the user is in.';
$string['iomad_sync_institution'] = 'Sync company name with profile';
$string['iomad_sync_institution_help'] = 'Selecting this will keep the user profile field for institution in sync with the shortname of the company that the user is in.';
$string['iomad_use_email_as_username'] = 'Use email address as user name';
$string['iomad_use_email_as_username_help'] = 'Selecting this will change the way a user name is automatically created for a new user account in Iomad so that it simply uses the email address.';
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
