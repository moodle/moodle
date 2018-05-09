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

unset($email);
global $email;
$email = array();

// Add emails with subject and body strings from lang/??/local_email.php.
$emailarray = array('approval',
                    'advertise_classroom_based_course',
                    'course_classroom_approval',
                    'course_classroom_approved',
                    'course_classroom_denied',
                    'course_classroom_manager_denied',
                    'course_classroom_approval_request',
                    'course_completed_manager',
                    'completion_course_supervisor',
                    'completion_expiry_warn_supervisor',
                    'completion_warn_supervisor',
                    'expire',
                    'expire_manager',
                    'user_added_to_course',
                    'invoice_ordercomplete',
                    'invoice_ordercomplete_admin',
                    'user_signed_up_for_event',
                    'user_removed_from_event',
                    'license_allocated',
                    'license_removed',
                    'password_update',
                    'completion_warn_user',
                    'completion_warn_manager',
                    'expiry_warn_user',
                    'expiry_warn_manager',
                    'user_create',
                    'user_reset');
foreach ($emailarray as $templatename) {
    $email[$templatename] = array(
        'subject' => get_string($templatename . '_subject', 'local_email' ),
        'body' => get_string($templatename . '_body', 'local_email')
    );
}
