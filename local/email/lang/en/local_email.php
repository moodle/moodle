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

$string['add_template_button'] = 'Override';
$string['addnewtemplate'] = 'Override a default template';
$string['blocktitle'] = 'Email Templates';
$string['body'] = 'Body';
$string['crontask'] 'Iomad email processing';
$string['default'] = 'default';
$string['delete_template'] = 'Delete template';
$string['delete_template_button'] = 'Revert to default';
$string['delete_template_checkfull'] = 'Are you absolutely sure you want to revert {$a} to the default template?';
$string['edit_template'] = 'Edit email template';
$string['editatemplate'] = 'Edit an override template';
$string['email_data'] = 'Data for substitutions';
$string['email_templates_for'] = 'Email templates for \'{$a}\'';
$string['email_template'] = 'Email template \'{$a->name}\' for \'{$a->companyname}\'';
$string['email_template_send'] = 'Send message to all applicable users of \'{$a->companyname}\' using \'{$a->name}\'';
$string['email:add'] = 'Override Default Email Templates';
$string['email:delete'] = 'Revert to Default Email Templates';
$string['email:edit'] = 'Edit Email Templates';
$string['email:list'] = 'List Email Templates';
$string['email:send'] = 'Send emails using templates';
$string['override'] = 'override';
$string['pluginname'] = 'Local: Email';
$string['save_to_override_default_template'] = 'Save to override default template';
$string['select_email_var'] = 'Select email variable';
$string['select_course'] = 'Select course';
$string['send_button'] = 'Send';
$string['send_emails'] = 'Send e-mails';
$string['subject'] = 'Subject';
$string['template_list_title'] = 'Email Templates';

/* Email templates */
$string['approval_subject'] = 'New course approval';
$string['approval_body'] = 'You have been asked to approve access to course {Course_FullName} for {User_FirstName} {User_LastName}.
please log onto {Site_FullName} ({LinkURL}) to approve or deny this request.';

$string['approved_subject'] = 'You have been approved access to {Course_FullName}';
$string['approved_body'] = 'You have been granted access to course {Course_FullName}.  To access this, please click on {CourseURL}.';

$string['course_classroom_approval_subject'] = 'New face to face training event approval';
$string['course_classroom_approval_body'] = 'You have been asked to approve access to the face to face training course {Event_Name} for {Approveuser_FirstName} {Approveuser_LastName} at the following event -

Time : {Classroom_Time}
Location : {Classroom_Name}
Address : {Classroom_Address}
          {Classroom_City} {Classroom_Postcode}

please log onto {Site_FullName} ('.$CFG->wwwroot.') to approve or deny this request.';

$string['course_classroom_approved_subject'] = 'Face to face training event approved';
$string['course_classroom_approved_body'] = 'You have been approved access to the face to face training course {Event_Name} at the following event -

Time : {Classroom_Time}
Location : {Classroom_Name}
Address : {Classroom_Address}
          {Classroom_City} {Classroom_Postcode}
';

$string['course_classroom_denied_subject'] = 'Face to face training event approval denied';
$string['course_classroom_denied_body'] = 'Your approval request has been rejected for {Event_Name} at the following event -

Time : {Classroom_Time}
Location : {Classroom_Name}
Address : {Classroom_Address}
          {Classroom_City} {Classroom_Postcode}
';

$string['course_classroom_manager_denied_subject'] = 'Face to face training event approval denied by company manager';
$string['course_classroom_manager_denied_body'] = 'The approval request for {Approveuser_FirstName} {Approveuser_LastName} has been rejected by {User_FirstName} {User_LastName} ({User_Email}) for {Event_Name} at the following event -

Time : {Classroom_Time}
Location : {Classroom_Name}
Address : {Classroom_Address}
          {Classroom_City} {Classroom_Postcode}
';

$string['course_classroom_approval_request_subject'] = 'New face to face training event approval request sent';
$string['course_classroom_approval_request_body'] = 'You have asked for access to the face to face training course {Event_Name} at the following event -

Time : {Classroom_Time}
Location : {Classroom_Name}
Address : {Classroom_Address}
          {Classroom_City} {Classroom_Postcode}

You will be notified once your manager has approved or denied access.';

$string['courseclassroom_approved_subject'] = 'You have been approved access to {Event_Name}';
$string['courseclassroom_approved_body'] = 'You have been granted access to course {Event_Name}.  To access this, please click on {CourseURL}.';

$string['user_added_to_course_subject'] = 'Added to {Course_FullName}';
$string['user_added_to_course_body'] = 'Dear {User_FirstName}

You have been granted access to the online training for {Course_FullName}.  Please visit {CourseURL} to partake in this training.';
$string['invoice_ordercomplete_subject'] = 'Thank you for your order at {Site_ShortName}';
$string['invoice_ordercomplete_body'] = 'Dear {User_FirstName} {User_LastName}
               Your order reference is {Invoice_Reference}
               Thank you for your order of the following:
               {Invoice_Itemized}
               Once this invoice has been paid licenses will be created
               or enrolments will be done by the administrator.';

$string['invoice_ordercomplete_admin_subject'] = 'E-commerce order (invoice {Invoice_Reference})';
$string['invoice_ordercomplete_admin_body'] = 'Dear e-commerce admin
               The following order has just been submitted by {Invoice_FirstName} {Invoice_LastName} of {Invoice_Company}.
               An invoice has been sent to them via email.

               {Invoice_Itemized}';

$string['advertise_classroom_based_course_subject'] = 'Course {Course_FullName}';
$string['advertise_classroom_based_course_body'] = 'This to let you know about the following classroom based course:
    {Course_FullName}

    It will be in {Classroom_Name}, which is at
    {Classroom_Address}
    {Classroom_City} {Classroom_Postcode}
    {Classroom_Country}

    and has a capacity of {Classroom_Capacity}.

    Please click on {CourseURL} to find out more about this course and book on this event
';
$string['user_signed_up_for_event_subject'] = 'Attendance Notice {Course_FullName}';
$string['user_signed_up_for_event_body'] = 'Dear {User_FirstName},

you have signed up for the face to face training on {Course_FullName} at the following event -

Time : {Classroom_Time}
Location : {Classroom_Name}
Address : {Classroom_Address}
          {Classroom_City} {Classroom_Postcode}

Please ensure you have completed an pre-course tasks required before attendance';
$string['user_removed_from_event_subject'] = 'Cancellation Notice {Course_FullName}';
$string['user_removed_from_event_body'] = 'Dear {User_FirstName},

you have been marked as no longer attending the face to face training on {Course_FullName} at the following event -

Time : {Classroom_Time}
Location : {Classroom_Name}
Address : {Classroom_Address}
          {Classroom_City} {Classroom_Postcode}';
$string['license_allocated_subject'] = 'Access to course {Course_FullName} granted';
$string['license_allocated_body'] = 'Dear {User_FirstName},

You have been granted access to the online training for {Course_FullName}.  Please visit {CourseURL} to partake in this training.
Once you have entered the course you will have access to it for {License_Length} days.  Unused access will expire after {License_Valid}';
$string['license_removed_subject'] = 'Access to course {Course_FullName} removed';
$string['license_removed_body'] = 'Your access to course {Course_FullName} has been revoked.  If you feel this is in error, please contact your training manager';
$string['password_update_subject'] = 'Password change notification for {User_FirstName}';
$string['password_update_body'] = 'Your password has been updated by the administrative staff.  Your new password is

{User_Newpassword}

Please visit {LinkURL}  to change this';
$string['completion_warn_user_subject'] = 'Notice: Course {Course_FullName} has not been completed';
$string['completion_warn_user_body'] = 'Dear {User_FirstName},
You have still not completed your training on {Course_FullName}.  Please visit {CourseURL} to rectify this.';
$string['completion_warn_manager_subject'] = 'Completion failure report for {Course_FullName}';
$string['completion_warn_manager_body'] = 'Dear {User_FirstName},
the following users have not completed the training on the course {Course_FullName} within the normal timeframe :

{Course_ReportText}';
$string['expiry_warn_user_subject'] = 'Notice: Accreditation in {Course_FullName} will expire soon.';
$string['expiry_warn_user_body'] = 'Dear {User_FirstName},
your accredited training on {Course_FullName} is expiring soon.  Please arrange for re-accreditation if appropriate';
$string['expiry_warn_manager_subject'] = 'Accreditation expiry report for {Course_FullName}';
$string['expiry_warn_manager_body'] = 'Dear {User_FullName},
the following users accreditation in {Course_FullName} is due to expire soon :

{Course_ReportText}';
$string['expire_subject'] = 'Course expires';
$string['expire_body'] = 'This is to let you know that your training in {Course_FullName} expires soon.';
$string['expire_manager_subject'] = 'Accreditation expired report for {Course_FullName}';
$string['expire_manager_body'] = 'Dear {User_FullName},
the following users accreditation in {Course_FullName} has expired :

{Course_ReportText}';
$string['user_create_subject'] = 'A new on-line learning account has been created for you';
$string['user_create_body'] = 'Dear {User_FirstName},

A new user account has been created for you on the \'Training Management System\'
and you have been issued with a new temporary password.

Your current login information is now:
username: {User_Username}
password: {User_Newpassword}
(you will have to change your password
when you login for the first time)

To start using \'Training Management System\', login at
{LinkURL}

In most mail programs, this should appear as a blue link
which you can just click on. If that doesn\'t work,
then cut and paste the address into the address
line at the top of your web browser window.

Once logged in, should you require any help or assistance, there is an FAQs section available at {CourseURL}

For technical queries, please contact your IT Support team/Helpdesk

Best Regards,

{Sender_FirstName} {Sender_LastName}';
