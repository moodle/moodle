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
 * Strings for component 'enrol_mnet', language 'en_us', version '4.1'.
 *
 * @package     enrol_mnet
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['error_multiplehost'] = 'Some instance of MNet enrollment plugin already exists for this host. Only one instance per host and/or one instance for \'All hosts\' is allowed.';
$string['instancename'] = 'Enrollment method name';
$string['instancename_help'] = 'You can optionally rename this instance of the MNet enrollment method. If you leave this field empty, the default instance name will be used, containing the name of the remote host and the assigned role for their users.';
$string['mnet:config'] = 'Configure MNet enroll instances';
$string['mnet_enrol_description'] = 'Publish this service to allow administrators at {$a} to enroll their students in courses you have created on your server.<br/><ul><li><em>Dependency</em>: You must also <strong>publish</strong> the SSO (Service Provider) service to {$a}.</li><li><em>Dependency</em>: You must also <strong>subscribe</strong> to the SSO (Identity Provider) service on {$a}.</li></ul><br/>Subscribe to this service to be able to enroll your students in courses on {$a}.<br/><ul><li><em>Dependency</em>: You must also <strong>subscribe</strong> to the SSO (Service Provider) service on {$a}.</li><li><em>Dependency</em>: You must also <strong>publish</strong> the SSO (Identity Provider) service to {$a}.</li></ul><br/>';
$string['mnet_enrol_name'] = 'Remote enrollment service';
$string['pluginname'] = 'MNet remote enrollments';
$string['pluginname_desc'] = 'Allows remote MNet host to enroll their users into our courses.';
$string['privacy:metadata'] = 'The MNet remote enrollments plugin does not store any personal data.';
$string['remotesubscriber_help'] = 'Select \'All hosts\' to open this course for all MNet peers we are offering the remote enrollment service to. Or choose a single host to make this course available for their users only.';
