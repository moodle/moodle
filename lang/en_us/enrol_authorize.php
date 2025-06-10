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
 * Strings for component 'enrol_authorize', language 'en_us', version '4.1'.
 *
 * @package     enrol_authorize
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['adminneworder'] = 'Dear Admin,

  You have received a new pending order:

   Order ID: {$a->orderid}
   Transaction ID: {$a->transid}
   User: {$a->user}
   Course: {$a->course}
   Amount: {$a->amount}

   SCHEDULED-CAPTURE ENABLED?: {$a->acstatus}

  If scheduled-capture enabled the credit card will be captured on {$a->captureon}
  and then student will be enrolled to course, otherwise it will be expired
  on {$a->expireon} and cannot be captured after this day.

  Also you can accept/deny the payment to enroll the student immediately following this link:
  {$a->url}';
$string['authorize:config'] = 'Configure Authorize.Net enroll instances';
$string['authorize:manage'] = 'Manage enrolled users
';
$string['authorize:unenrol'] = 'Unenroll users from course';
$string['authorize:unenrolself'] = 'Unenroll self from the course';
$string['cancelled'] = 'Canceled';
$string['captureyes'] = 'The credit card will be captured and the student will be enrolled to the course. Are you sure?
';
$string['choosemethod'] = 'If you know the enrollment key of the course, please enter it below;  <br/>Otherwise you need to pay for this course.';
$string['description'] = 'The Authorize.net module allows you to set up paid courses via CC providers.  If the cost for any course is zero, then students are not asked to pay for entry.  Two ways to set the course cost (1) a site-wide cost as a default for the whole site or (2) a course setting that you can set for each course individually. The course cost overrides the site cost.<br /><br /><b>Note:</b> If you enter an enrollment key in the course settings, then students will also have the option to enroll using a key. This is useful if you have a mixture of paying and non-paying students.';
$string['enrolenddaterror'] = 'Enrollment end date cannot be earlier than start date';
$string['enrolperiod'] = 'Enrollment duration';
$string['messageprovider:authorize_enrolment'] = 'Authorize.Net enrollment messages';
$string['nocost'] = 'There is no cost associated with enrolling in this course via Authorize.Net!
';
$string['pendingecheckemail'] = 'Dear manager,

There are {$a->count} pending echecks now and you have to upload a csv file to get the users enrolled.

Click the link and read the help file on the page seen:
{$a->url}';
$string['pendingordersemail'] = 'Dear admin,

{$a->pending} transactions for course "{$a->course}" will expire unless you accept payment within {$a->days} days.

This is a warning message, because you didn\'t enable scheduled-capture.
It means you have to accept or deny payments manually.

To accept/deny pending payments go to:
{$a->url}

To enable scheduled-capture, it means you will not receive any warning emails anymore, go to:

{$a->enrolurl}';
$string['status'] = 'Allow Authorize.Net enrollments';
$string['subvoidyes'] = 'The transaction refunded ({$a->transid}) is going to be canceled and this will cause crediting {$a->amount} to your account. Are you sure?';
$string['unenrolselfconfirm'] = 'Do you really want to unenroll yourself from course "{$a}"?';
$string['unenrolstudent'] = 'Unenroll student?';
$string['usingccmethod'] = 'Enroll using <a href="{$a->url}"><strong>Credit Card</strong></a>';
$string['usingecheckmethod'] = 'Enroll using <a href="{$a->url}"><strong>eCheck</strong></a>';
$string['voidyes'] = 'The transaction will be canceled. Are you sure?';
$string['welcometocoursesemail'] = 'Dear {$a->name},

Thanks for your payments. You have enrolled in these courses:
{$a->courses}

You may view your payment details or edit your profile:
 {$a->paymenturl}
 {$a->profileurl}';
