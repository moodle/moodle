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

$string['email:accident'] = 'If you did not request this email, click continue to attempt to invalidate the login attempt.
    If you clicked this link by accident, click cancel, and no action will be taken.';
$string['email:browseragent'] = 'The browser details for this request are: \'{$a}\'';
$string['email:geoinfo'] = 'This request appears to have originated from approximately {$a->city}, {$a->country}.';
$string['email:ipinfo'] = 'IP Information';
$string['email:link'] = 'this link';
$string['email:message'] = 'You are trying to log in to Moodle. Your confirmation code is \'{$a->secret}\'.
     Alternatively you can click {$a->link} from the same device to authorise this session.';
$string['email:originatingip'] = 'This login request was made from \'{$a}\'';
$string['email:revokelink'] = 'If this wasn\'t you, follow {$a} to stop this login attempt.';
$string['email:revokesuccess'] = 'This code has been successfully revoked. All sessions for {$a} have been ended.
    Email will not be usable as a factor until account security has been verified.';
$string['email:subject'] = 'Your confirmation code';
$string['email:uadescription'] = 'Browser identity for this request:';
$string['error:badcode'] = 'Code was not found. This may be an old link, a new code may have been emailed, or the login attempt with this code was successful.';
$string['error:parameters'] = 'Incorrect page parameters.';
$string['error:wrongverification'] = 'Incorrect verification code';
$string['event:unauthemail'] = 'Unauthorised email received';
$string['info'] = '<p>Built-in factor. Uses e-mail address mentioned in user profile for sending verification codes</p>';
$string['loginskip'] = "I didn't receive a code";
$string['loginsubmit'] = 'Verify code';
$string['pluginname'] = 'E-Mail Factor';
$string['privacy:metadata'] = 'The E-Mail Factor plugin does not store any personal data';
$string['settings:duration'] = 'Validity duration';
$string['settings:duration_help'] = 'The period of time that the code is valid.';
$string['settings:suspend'] = 'Suspend unauthorised accounts';
$string['settings:suspend_help'] = 'Check this to suspend user accounts if an unauthorised email verification is received.';
$string['setupfactor'] = 'E-Mail Factor setup';
$string['summarycondition'] = 'has valid email setup';
$string['unauthemail'] = 'Unauthorised Email';
$string['verificationcode'] = 'Enter verification code for confirmation';
$string['verificationcode_help'] = 'Verification code has been sent to your email address';
