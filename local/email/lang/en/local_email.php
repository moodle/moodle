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
$string['backtocompanytemplates'] = 'Finish editing template set';
$string['blocktitle'] = 'Email Templates';
$string['body'] = 'Body';
$string['cc'] = 'CC address';
$string['ccother'] = 'Manual CC address';
$string['controls'] = 'Controls';
$string['crontask'] = 'Iomad email processing';
$string['custom'] = 'custom';
$string['daily'] = 'Daily';
$string['default'] = 'default';
$string['delete_template'] = 'Delete template';
$string['deletetemplateset'] = 'Delete template set';
$string['deletetemplatesetfull'] = 'Are you absolutely sure you want to delete template set {$a}?';
$string['delete_template_button'] = 'Revert to default';
$string['delete_template_checkfull'] = 'Are you absolutely sure you want to revert {$a} to the default template?';
$string['edit_template'] = 'Edit email template';
$string['editatemplate'] = 'Edit an override template';
$string['edittemplateset'] = 'Edit template set';
$string['emailtemplatename'] = 'Email template name';
$string['email_data'] = 'Data for substitutions';
$string['email_templates_for'] = 'Email templates for \'{$a}\'';
$string['email_template'] = 'Email template \'{$a->name}\' for \'{$a->companyname}\'';
$string['emailtemplatesets'] = 'Email template sets';
$string['emailtemplatesetsaved'] = 'Template set saved successfully';
$string['email_template_send'] = 'Send message to all applicable users of \'{$a->companyname}\' using \'{$a->name}\'';
$string['email:add'] = 'Override Default Email Templates';
$string['email:delete'] = 'Revert to Default Email Templates';
$string['email:edit'] = 'Edit Email Templates';
$string['email:list'] = 'List Email Templates';
$string['email:send'] = 'Send emails using templates';
$string['enable_manager'] = 'Enable for managers';
$string['enable_supervisor'] = 'Enable for supervisors';
$string['emailrepeatday'] = 'Email re-send day**';
$string['emailrepeatday_help'] = 'This is the specific day that an email is re-sent out.';
$string['emailrepeatinfo'] = '<p>**Only warning emails will repeat.</p>';
$string['emailrepeatperiod'] = 'Email re-send every**';
$string['emailrepeatperiod_help'] = 'This is how often an email is re-sent to the user.';
$string['emailrepeatvalue'] = 'Email re-send amount**';
$string['emailrepeatvalue_help'] = 'This is the maximum number of times that the email is re-sent.';
$string['fortnightly'] = 'Fortnightly';
$string['fromother'] = 'Manual From address';
$string['fromothername'] = 'Manual From name';
$string['managetemplatesets'] = 'Manage template sets';
$string['missingtemplatesetname'] = 'Please enter a template set name';
$string['monthly'] = 'Monthly';
$string['override'] = 'override';
$string['pluginname'] = 'Local: Email';
$string['replyto'] = 'Reply to';
$string['replytoother'] = 'Manual Reply to';
$string['save'] = 'Save';
$string['savetemplateset'] = 'Save as a template set';
$string['save_to_override_default_template'] = 'Save to override default template';
$string['select_email_var'] = 'Select email variable';
$string['select_course'] = 'Select course';
$string['send_button'] = 'Send';
$string['send_emails'] = 'Send e-mails';
$string['signature'] = 'Signature';
$string['signatureseparator'] = "<p>--</p>";
$string['subject'] = 'Subject';
$string['template_list_title'] = 'Email Templates';
$string['templatesetdeleted'] = 'Template set deleted successfully';
$string['templatesetname'] = 'Template set name';
$string['templatesetname_help'] = 'This is name which the template set will be referenced by.';
$string['templatetype'] = 'Template type';
$string['toother'] = 'Manual To address';
$string['weekly'] = 'Weekly';

/* Email templates */
$string['approval_subject'] = 'New course approval';
$string['approval_body'] = '<p>You have been asked to approve access to course {Course_FullName} for {User_FirstName} {User_LastName}.</p>
<p>please log onto {Site_FullName} (<a href="{LinkURL}">{LinkURL}</a>) to approve or deny this request.</p>';

$string['approved_subject'] = 'You have been approved access to {Course_FullName}';
$string['approved_body'] = '<p>You have been granted access to course {Course_FullName}.  To access this, please click on <a href="{CourseURL}">{CourseURL}</a>.</p>';

$string['course_classroom_approval_subject'] = 'New face to face training event approval';
$string['course_classroom_approval_body'] = '<p>You have been asked to approve access to the face to face training course {Event_Name} for {Approveuser_FirstName} {Approveuser_LastName} at the following event -</p>
<br>
Time : {Classroom_Time}</br>
Location : {Classroom_Name}</br>
Address : {Classroom_Address}</br>
          {Classroom_City} {Classroom_Postcode}</br>
</br>
<p>please log onto {Site_FullName} ('.$CFG->wwwroot.') to approve or deny this request.</p>';

$string['course_classroom_approved_subject'] = 'Face to face training event approved';
$string['course_classroom_approved_body'] = '<p>You have been approved access to the face to face training course {Event_Name} at the following event -</p>
</br>
Time : {Classroom_Time}</br>
Location : {Classroom_Name}</br>
Address : {Classroom_Address}</br>
          {Classroom_City} {Classroom_Postcode}';

$string['course_classroom_denied_subject'] = 'Face to face training event approval denied';
$string['course_classroom_denied_body'] = '<p>Your approval request has been rejected for {Event_Name} at the following event -</p>
</br>
Time : {Classroom_Time}</br>
Location : {Classroom_Name}</br>
Address : {Classroom_Address}</br>
          {Classroom_City} {Classroom_Postcode}';

$string['course_classroom_manager_denied_subject'] = 'Face to face training event approval denied by company manager';
$string['course_classroom_manager_denied_body'] = '<p>The approval request for {Approveuser_FirstName} {Approveuser_LastName} has been rejected by {User_FirstName} {User_LastName} ({User_Email}) for {Event_Name} at the following event -</p>
</br>
Time : {Classroom_Time}</br>
Location : {Classroom_Name}</br>
Address : {Classroom_Address}</br>
          {Classroom_City} {Classroom_Postcode}';

$string['course_classroom_approval_request_subject'] = 'New face to face training event approval request sent';
$string['course_classroom_approval_request_body'] = '<p>You have asked for access to the face to face training course {Event_Name} at the following event -</p>
</br>
Time : {Classroom_Time}</br>
Location : {Classroom_Name}</br>
Address : {Classroom_Address}</br>
          {Classroom_City} {Classroom_Postcode}</br>
<p>You will be notified once your manager has approved or denied access.</p>';

$string['courseclassroom_approved_subject'] = 'You have been approved access to {Event_Name}';
$string['courseclassroom_approved_body'] = '<p>You have been granted access to course {Event_Name}.  To access this, please click on <a href="{CourseURL}">{CourseURL}</a>.<p>';
$string['course_completed_manager_subject'] = 'Student course completion report';
$string['course_completed_manager_body'] = '<p>Dear {User_FirstName}</p>
<p>{Course_ReportText}</p>';

$string['user_added_to_course_subject'] = 'Added to {Course_FullName}';
$string['user_added_to_course_body'] = '<p>Dear {User_FirstName}</p>
<br>
<p>You have been granted access to the online training for {Course_FullName}.  Please visit <a href="{CourseURL}">{CourseURL}</a> to partake in this training.</p>';
$string['invoice_ordercomplete_subject'] = 'Thank you for your order at {Site_ShortName}';
$string['invoice_ordercomplete_body'] = '<p>Dear {User_FirstName} {User_LastName}</p>
<p>Your order reference is {Invoice_Reference}</p>
<p>Thank you for your order of the following:</p>
<p>{Invoice_Itemized}</p>
<p>Once this invoice has been paid licenses will be created or enrolments will be done by the administrator.</p>';

$string['invoice_ordercomplete_admin_subject'] = 'E-commerce order (invoice {Invoice_Reference})';
$string['invoice_ordercomplete_admin_body'] = '<p>Dear e-commerce admin</p>
<p>The following order has just been submitted by {Invoice_FirstName} {Invoice_LastName} of {Invoice_Company}.</br>
An invoice has been sent to them via email.</p>

<p>{Invoice_Itemized}</p>';

$string['advertise_classroom_based_course_subject'] = 'Course {Course_FullName}';
$string['advertise_classroom_based_course_body'] = '<o>This to let you know about the following classroom based course:</p>
<p>{Course_FullName}</p>

<p>It will be in {Classroom_Name}, which is at</p>
<p>{Classroom_Address}</br>
{Classroom_City} {Classroom_Postcode}</br>
{Classroom_Country}</br>

<p>and has a capacity of {Classroom_Capacity}.</p>

<p>Please click on <a href="{CourseURL}">{CourseURL}</a> to find out more about this course and book on this event</p>';
$string['user_signed_up_for_event_subject'] = 'Attendance Notice {Course_FullName}';
$string['user_signed_up_for_event_body'] = '<p>Dear {User_FirstName},</p>

<p>you have signed up for the face to face training on {Course_FullName} at the following event -</p>

<p>Time : {Classroom_Time}</br>
Location : {Classroom_Name}</br>
Address : {Classroom_Address}</br>
          {Classroom_City} {Classroom_Postcode}</br>

<p>Please ensure you have completed an pre-course tasks required before attendance</p>';
$string['user_removed_from_event_subject'] = 'Cancellation Notice {Course_FullName}';
$string['user_removed_from_event_body'] = '<p>Dear {User_FirstName},</p>

<p>you have been marked as no longer attending the face to face training on {Course_FullName} at the following event -</p>

<p>Time : {Classroom_Time}</br>
Location : {Classroom_Name}</br>
Address : {Classroom_Address}</br>
          {Classroom_City} {Classroom_Postcode}';
$string['license_allocated_subject'] = 'Access to course {Course_FullName} granted';
$string['license_allocated_body'] = '<p>Dear {User_FirstName},</p>

<p>You have been granted access to the online training for {Course_FullName}.  Please visit <a href="{CourseURL}">{CourseURL}</a> to partake in this training.</br>
Once you have entered the course you will have access to it for {License_Length} days.  Unused access will expire after {License_Valid}</p>';
$string['license_reminder_subject'] = 'Reminder: you have been allocated the course {Course_FullName}';
$string['license_reminder_body'] = '<p>Dear {User_FirstName},</p>

<p>You have been granted access to the online training for {Course_FullName}.  Please visit <a href="{CourseURL}">{CourseURL}</a> to partake in this training.</br>
Once you have entered the course you will have access to it for {License_Length} days.  Unused access will expire after {License_Valid}</p>';
$string['license_removed_subject'] = 'Access to course {Course_FullName} removed';
$string['license_removed_body'] = '<p>Your access to course {Course_FullName} has been revoked.  If you feel this is in error, please contact your training manager</p>';
$string['password_update_subject'] = 'Password change notification for {User_FirstName}';
$string['password_update_body'] = '<p>Your password has been updated by the administrative staff.  Your new password is</p>

<p>{User_Newpassword}</p>

<p>Please visit <a href="{LinkURL}">{LinkURL}</a> to change this</p>';
$string['completion_warn_user_subject'] = 'Notice: Course {Course_FullName} has not been completed';
$string['completion_warn_user_body'] = '<p>Dear {User_FirstName},</p>
<p>You have still not completed your training on {Course_FullName}.  Please visit <a href="{CourseURL}">{CourseURL}</a> to rectify this.</p>';
$string['completion_warn_manager_subject'] = 'User completion failure report';
$string['completion_warn_manager_body'] = '<p>Dear {User_FirstName},</p>
<p>the following users have not completed their training within the normal timeframe :</p>

<p>{Course_ReportText}</p>';
$string['completion_digest_manager_subject'] = 'User completion report';
$string['completion_digest_manager_body'] = '<p>Dear {User_FirstName},</p>
<p>the following users have completed their training within the last week :</p>

<p>{Course_ReportText}</p>';
$string['expiry_warn_user_subject'] = 'Notice: Accreditation in {Course_FullName} will expire soon.';
$string['expiry_warn_user_body'] = '<p>Dear {User_FirstName},</p>
<p>your accredited training on {Course_FullName} is expiring soon.  Please arrange for re-accreditation if appropriate</p>';
$string['expiry_warn_manager_subject'] = 'Accreditation expiry report';
$string['expiry_warn_manager_body'] = '<p>Dear {User_FirstName},</p>
<p>the following users accreditation is due to expire soon :</p>

<p>{Course_ReportText}</p>';
$string['expire_subject'] = 'Course expires';
$string['expire_body'] = '<p>This is to let you know that your training in {Course_FullName} expires soon.</p>';
$string['expire_manager_subject'] = 'Accreditation expired report for {Course_FullName}';
$string['expire_manager_body'] = '<p>Dear {User_FullName},</p>
<p>the following users accreditation in {Course_FullName} has expired :</p>

<p>{User_ReportText}</p>';
$string['user_reset_subject'] = 'The login details for your account have been reset';
$string['user_reset_body'] = '<p>Dear {User_FirstName},</p>

<p>Your user account details are as follows.</p>

<p>username: {User_Username}</br>
password: {User_Newpassword}</br>
(you will have to change your password when you log in)</p>

<p>Best Regards,</p>

<p>{Sender_FirstName} {Sender_LastName}</p>';
$string['user_create_subject'] = 'A new on-line learning account has been created for you';
$string['user_create_body'] = '<p>Dear {User_FirstName},</p>

<p>A new user account has been created for you on the \'Training Management System\'
and you have been issued with a new temporary password.</p>

<p>Your current login information is now:<p>
<p>username: {User_Username}</br>
password: {User_Newpassword}</br>
(you will have to change your password
when you login for the first time)</p>

<p>To start using \'Training Management System\', login at</p>
<p><a href="{LinkURL}">{LinkURL}</a></p>

<p>In most mail programs, this should appear as a blue link
which you can just click on. If that doesn\'t work,
then cut and paste the address into the address
line at the top of your web browser window.</p>

<p>For technical queries, please contact your IT Support team/Helpdesk</p>

<p>Best Regards,</p>

<p>{Sender_FirstName} {Sender_LastName}</p>';
$string['completion_course_supervisor_subject'] = 'Notice: Course {Course_FullName} has been completed';
$string['completion_course_supervisor_body'] = '<p>{User_FirstName} {User_LastName} has completed the training course {Course_FullName}. Please find attached a copy of their certificate for your records.</p>

<p>The certificate is also available from the User Report section on our system should you need a copy in the future.</p>';
$string['completion_course_user_subject'] = 'Course {Course_FullName} has been completed';
$string['completion_course_user_body'] = '<p>Dear {User_FirstName},</p>
<p>Congratulations on completing the training course {Course_FullName}. Please find attached a copy of their certificate for your records.</p>

<p>The certificate is also available from your dashboard should you need a copy in the future.</p>';
$string['user_programcompleted_subject'] = 'Program of courses is complete';
$string['user_programcompleted_body'] = "<p>Dear {User_FirstName} {User_LastName},</p>
<p>Congratulations! You have completed all of the courses within your training program</p>";
$string['completion_warn_supervisor_subject'] = 'Notice: Course {Course_FullName} has not been completed';
$string['completion_warn_supervisor_body'] = '<p>{User_FirstName} {User_LastName} has not completed their training in course {Course_FullName} within the normal timeframe</p>';
$string['completion_expiry_warn_supervisor_subject'] = 'Notice: Course {Course_FullName} training expiry';
$string['completion_expiry_warn_supervisor_body'] = '<p>The training for {User_FirstName} {User_LastName} in course {Course_FullName} will expiry shortly.  Please arrange for them to retake this training if appropriate.</p>';
$string['licensepoolwarning_subject'] = "90% License pool used {License_Name}, {License_ID}";
$string['licensepoolwarning_body'] = "<p>Hi {User_Firstname} {User_Lastname}</p>
<p>This message is to notify your company account {Company_Name} has used 90% of the license pool {License_Name}, {License_ID} . You will not be able to allocate further licenses once you reach 100% usage.</p>
<p>Please contact your Fletdefense Program Manager for details</p>";
$string['licensepoolexpiring_subject'] = "Alert: License pool expiration {License_Name}  {License_Expirydate}, {License_ID}";
$string['licensepoolexpiring_body'] = "<p>Hi {User_Firstname} {User_Lastname}</p>
<p>This message is to notify your company account {Company_Name} that that your program license {License_Name}, {License_ID} is expiring on  {License_Expirydate}.
You will not be able to allocate further licenses to users once you reach expiration date. Please contact your Program Manager if you have further questions</p>";
$string['user_promoted_subject'] = 'New role granted';
$string['user_promoted_body'] = "<p>Hello {User_Firstname} {User_Lastname},</p>
<p>You have been granted admin privilidges. To access the administrative suite, tracking and reporting tools please click the following link or copy and paste the link into your browser to set your password and login
 {SiteURL}</p>
<p>On the login page, Click Activate button to activate your account for first time login.</p>
<p>Your email: {User_Email}</p>";
$string['user_deleted_subject'] = "Account has been deleted";
$string['user_deleted_body'] = "<p>Hello {User_Firstname} {User_Lastname},</p>
<p>Your account has been deleted on {SiteURL}. You no longer have access to any of your training courses</p>
<p>If you feel that this is in error, please contact your manager</p>
<p>Your email: {User_Email}</p>";
$string['admin_deleted_subject'] = "Account has been deleted";
$string['admin_deleted_body'] = "<p>Hello {User_Firstname} {User_Lastname},</p>
<p>Your account has been deleted on {SiteURL}. You no longer have access to manage your company</p>
<p>Your email: {User_Email}</p><p>If you feel that this is in error, please contact your manager.</p>";
$string['user_suspended_subject'] = "Account has been suspended";
$string['user_suspended_body'] = "<p>Hello {User_Firstname} {User_Lastname},</p>
<p>Your account has been suspended on {SiteURL}. You no longer have access to any of your training courses</p>
<p>If you feel that this is in error, please contact your manager</p>
<p>Your email: {User_Email}</p>";
$string['user_unsuspended_subject'] = "Account has been unsuspended";
$string['user_unsuspended_body'] = "<p>Hello {User_Firstname} {User_Lastname},</p>
<p>Your account has been unsuspended on {SiteURL}. You now have access to any of your training courses</p>
<p>If you have any further questions, please contact your manager</p>
<p>Your email: {User_Email}</p>";
$string['company_suspended_subject'] = "Company account has been suspended";
$string['company_suspended_body'] = "<p>Hello {User_Firstname} {User_Lastname},</p>
<p>Your company account has been suspended on {SiteURL}. You no longer have access to manage your company</p>
<p>Your email: {User_Email}</p><p>If you feel that this is in error, please contact support</p>";
$string['company_unsuspended_subject'] = "Company account has been unsuspended";
$string['company_unsuspended_body'] = "<p>Hello {User_Firstname} {User_Lastname},</p>
<p>Your company account has been unsuspended on {SiteURL}. You now have access to manage your company</p>
<p>Your email: {User_Email}</p><p>If you have any further questions, please contact support</p>";
$string['company_licenseassigned_subject'] = "New Training assigned to {Company_Name}";
$string['company_licenseassigned_body'] = "<p>Dear {User_Firstname} {User_Lastname},</p>
<p>New courses have been allocated to your company on {SiteURL}.</p>
<p>Please log in using your username {User_Username} to manage this.</p>";

