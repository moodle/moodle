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
 * @package     factor_email
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['email:accident'] = 'If you didn\'t request the email, click continue to invalidate the login attempt. If you clicked the link by accident, click cancel, and no action will be taken.';
$string['email:browseragent'] = 'The browser details for this request are: \'{$a}\'';
$string['email:geoinfo'] = 'This request appears to have originated from approximately:';
$string['email:greeting'] = 'Hello {$a} &#128075;';
$string['email:ipinfo'] = 'Login request details:';
$string['email:link'] = 'verification link';
$string['email:loginlink'] = 'Or, if you\'re on the same device, use this {$a}.';
$string['email:message'] = 'Here\'s your verification code for {$a->sitename} ({$a->siteurl}).';
$string['email:originatingip'] = 'This login request was made from \'{$a}\'';
$string['email:revokelink'] = 'If this wasn\'t you, you can {$a}.';
$string['email:revokesuccess'] = 'This code has been successfully revoked. All sessions for this user have been ended.
    Email will not be usable as a factor until account security has been verified.';
$string['email:subject'] = 'Here\'s your verification code';
$string['email:stoploginlink'] = 'stop this login attempt';
$string['email:uadescription'] = 'Browser identity for this request:';
$string['email:validity'] = 'The code can only be used once and is valid for {$a}.';
$string['error:badcode'] = 'Code was not found. This may be an old link, a new code may have been emailed, or the login attempt with this code was successful.';
$string['error:parameters'] = 'Incorrect page parameters.';
$string['error:wrongverification'] = 'Wrong code. Try again.';
$string['event:unauthemail'] = 'Unauthorised email received';
$string['info'] = 'You are using email {$a} to authenticate. This has been set up by your site administrator.';
$string['logindesc'] = 'We\'ve just sent a 6-digit code to your email: {$a}';
$string['loginoption'] = 'Have a code emailed to you';
$string['loginskip'] = "I didn't receive a code";
$string['loginsubmit'] = 'Continue';
$string['logintitle'] = "Verify it's you by email";
$string['managefactor'] = 'Manage email';
$string['manageinfo'] = '\'{$a}\' is being used to authenticate. This has been set up by your administrator.';
$string['pluginname'] = 'Email';
$string['privacy:metadata'] = 'The Email factor plugin does not store any personal data';
$string['settings:description'] = 'Users will receive a 6-digit verification code via email, which they must enter to complete the login process.';
$string['settings:duration'] = 'Validity duration';
$string['settings:duration_help'] = 'The period of time that the code is valid.';
$string['settings:shortdescription'] = 'Require users to enter a code received via email during login.';
$string['settings:suspend'] = 'Suspend unauthorised accounts';
$string['settings:suspend_help'] = 'Check this to suspend user accounts if an unauthorised email verification is received.';
$string['setupfactor'] = 'Set up email';
$string['summarycondition'] = 'has valid email setup';
$string['unauthloginattempt'] = 'The user with ID {$a->userid} made an unauthorised login attempt using email verification from
IP {$a->ip} with browser agent {$a->useragent}.';
$string['unauthemail'] = 'Unauthorised email';
$string['verificationcode'] = 'Enter verification code for confirmation';
$string['verificationcode_help'] = 'A verification code has been sent to your email.';
