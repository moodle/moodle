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
 * Language strings.
 *
 * @package     factor_sms
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:revoke'] = 'Revoke mobile phone number';
$string['addnumber'] = 'Enter mobile phone number';
$string['awssdkrequired'] = 'The local_aws plugin leveraging the AWS SDK is required to use this factor. Please install local_aws.';
$string['event:smssent'] = 'SMS Message sent';
$string['info'] = '<p>Setup a mobile phone number to receive SMS one-time security codes on.</p>';
$string['loginskip'] = "I didn't receive a code";
$string['loginsubmit'] = 'Verify code';
$string['phonehelp'] = 'Enter your local mobile phone number, or an international phone number starting with \'+\'.';
$string['phoneplaceholder'] = '04xx xxx xxx or +61 4xx xxx xxx';
$string['pluginname'] = 'Mobile phone SMS';
$string['privacy:metadata'] = 'The mobile phone SMS	factor plugin does not store any personal data';
$string['settings:aws'] = 'AWS SNS';
$string['settings:aws:key'] = 'Key';
$string['settings:aws:key_help'] = 'Amazon API key credential.';
$string['settings:aws:region'] = 'Region';
$string['settings:aws:region_help'] = 'Amazon API gateway region.';
$string['settings:aws:secret'] = 'Secret';
$string['settings:aws:secret_help'] = 'Amazon API secret credential.';
$string['settings:aws:usecredchain'] = 'Use the default credential provider chain to find AWS credentials';
$string['settings:countrycode'] = 'Country number code';
$string['settings:countrycode_help'] = 'The calling code without the leading + as a default if users do not enter an international number with a + prefix.

See this link for a list of calling codes: {$a}';
$string['settings:duration'] = 'Validity duration';
$string['settings:duration_help'] = 'The period of time that the code is valid.';
$string['settings:gateway'] = 'SMS Gateway';
$string['settings:gateway_help'] = 'The SMS provider you wish to send messages via';
$string['settings:modica'] = 'Modica Mobile Gateway';
$string['settings:modica:application'] = 'Rest Application Name';
$string['settings:modica:application_help'] = 'Mobile Gateway (API) Application name';
$string['settings:modica:password'] = 'Password';
$string['settings:modica:password_help'] = 'Mobile Gateway (API) Password';
$string['settings:modica:url'] = 'API URL';
$string['settings:modica:url_help'] = 'Can be left blank in most cases';
$string['setupfactor'] = 'Setup mobile number';
$string['smssent'] = 'An SMS message containing your verification code was sent to {$a}.';
$string['smsstring'] = '{$a->code} is your {$a->fullname} one-time security code.

@{$a->url} #{$a->code}';
$string['summarycondition'] = 'Using an SMS one-time security code';
$string['wrongcode'] = 'Invalid security code.';
