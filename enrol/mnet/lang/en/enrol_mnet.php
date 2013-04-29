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
 * English strings for MNet enrolment plugin.
 *
 * @package    enrol_mnet
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['error_multiplehost'] = 'Some instance of MNet enrolment plugin already exists for this host. Only one instance per host and/or one instance for \'All hosts\' is allowed.';
$string['instancename'] = 'Enrolment method name';
$string['instancename_help'] = 'You can optionally rename this instance of the MNet enrolment method. If you leave this field empty, the default instance name will be used, containing the name of the remote host and the assigned role for their users.';
$string['mnet_enrol_description'] = 'Publish this service to allow administrators at {$a} to enrol their students in courses you have created on your server.<br/><ul><li><em>Dependency</em>: You must also <strong>subscribe</strong> to the SSO (Identity Provider) service on {$a}.</li><li><em>Dependency</em>: You must also <strong>publish</strong> the SSO (Service Provider) service to {$a}.</li></ul><br/>Subscribe to this service to be able to enrol your students in courses  on {$a}.<br/><ul><li><em>Dependency</em>: You must also <strong>publish</strong> the SSO (Identity Provider) service to {$a}.</li><li><em>Dependency</em>: You must also <strong>subscribe</strong> to the SSO (Service Provider) service on {$a}.</li></ul><br/>';
$string['mnet_enrol_name'] = 'Remote enrolment service';
$string['pluginname'] = 'MNet remote enrolments';
$string['pluginname_desc'] = 'Allows remote MNet host to enrol their users into our courses.';
$string['remotesubscriber'] = 'Remote host';
$string['remotesubscriber_help'] = 'Select \'All hosts\' to open this course for all MNet peers we are offering the remote enrolment service to. Or choose a single host to make this course available for their users only.';
$string['remotesubscribersall'] = 'All hosts';
$string['roleforremoteusers'] = 'Role for their users';
$string['roleforremoteusers_help'] = 'What role will the remote users from the selected host get.';
