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
$string['addnumber'] = 'Mobile number';
$string['clientnotfound'] = 'AWS service client not found. Client must be fully qualified classname e.g. \Aws\S3\S3Client.';
$string['editphonenumber'] = 'Edit phone number';
$string['editphonenumberinfo'] = 'If you didn\'t receive the code or entered the wrong number, please edit the number and try again.';
$string['errorawsconection'] = 'Error connecting to AWS server: {$a}';
$string['errorsmssent'] = 'Error sending a SMS message containing your verification code.';
$string['error:emptyverification'] = 'Empty code. Try again.';
$string['error:wrongphonenumber'] = 'The phone number you provided is not in a valid format.';
$string['error:wrongverification'] = 'Wrong code. Try again.';
$string['event:smssent'] = 'SMS message sent.';
$string['event:smssentdescription'] = 'The user with ID {$a->userid} was sent a verification code via SMS. Information: {$a->debuginfo}';
$string['info'] = '<p>Set up mobile phone to receive authentication code.</p>';
$string['logindesc'] = 'SMS message containing a 6-digit code sent to mobile number {$a}';
$string['loginoption'] = 'Have a code sent to your mobile phone';
$string['loginskip'] = "I didn't receive a code";
$string['loginsubmit'] = 'Continue';
$string['logintitle'] = 'Enter the verification code sent to your mobile';
$string['phonehelp'] = 'Enter your mobile number (including country code) to receive a verification code.';
$string['pluginname'] = 'SMS mobile phone';
$string['privacy:metadata'] = 'The SMS mobile phone factor plugin does not store any personal data.';
$string['settings:aws'] = 'AWS SNS';
$string['settings:aws:key'] = 'Key';
$string['settings:aws:key_help'] = 'Amazon API key credential.';
$string['settings:aws:region'] = 'Region';
$string['settings:aws:region_help'] = 'Amazon API gateway region.';
$string['settings:aws:secret'] = 'Secret';
$string['settings:aws:secret_help'] = 'Amazon API secret credential.';
$string['settings:aws:usecredchain'] = 'Find AWS credentials using the default credential provider chain';
$string['settings:countrycode'] = 'Country number code';
$string['settings:countrycode_help'] = 'The calling code without the leading + as a default if users do not enter an international number with a + prefix.

See this link for a list of calling codes: {$a}';
$string['settings:duration'] = 'Validity duration';
$string['settings:duration_help'] = 'The period of time that the code is valid.';
$string['settings:gateway'] = 'SMS gateway';
$string['settings:gateway_help'] = 'The SMS provider for sending messages via.';
$string['setupfactor'] = 'SMS setup';
$string['setupfactorbutton'] = 'Set up SMS';
$string['setupsubmitcode'] = 'Save';
$string['setupsubmitphone'] = 'Send code';
$string['smsstring'] = '{$a->code} is your {$a->fullname} one-time security code.

@{$a->url} #{$a->code}';
$string['summarycondition'] = 'Using an SMS one-time security code';
