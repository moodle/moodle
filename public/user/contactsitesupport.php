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
 * Contact site support.
 *
 * @copyright 2022 Simey Lameze <simey@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */
require_once('../config.php');
require_once($CFG->dirroot . '/user/lib.php');

$user = isloggedin() && !isguestuser() ? $USER : null;

// If not allowed to view this page, redirect to the homepage. This would be where the site has
// disabled support, or limited it to authenticated users and the current user is a guest or not logged in.
if (!isset($CFG->supportavailability) ||
        $CFG->supportavailability == CONTACT_SUPPORT_DISABLED ||
        ($CFG->supportavailability == CONTACT_SUPPORT_AUTHENTICATED && is_null($user))) {
    redirect($CFG->wwwroot);
}

if (!empty($CFG->supportpage)) {
    redirect($CFG->supportpage);
}

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/user/contactsitesupport.php');
$PAGE->set_title(get_string('contactsitesupport', 'admin'));
$PAGE->set_heading(get_string('contactsitesupport', 'admin'));
$PAGE->set_pagelayout('standard');

$renderer = $PAGE->get_renderer('user');

$form = new \core_user\form\contactsitesupport_form(null, $user);
if ($form->is_cancelled()) {
    redirect($CFG->wwwroot);
} else if ($form->is_submitted() && $form->is_validated() && confirm_sesskey()) {
    $data = $form->get_data();

    $from = $user ?? core_user::get_noreply_user();
    $subject = get_string('supportemailsubject', 'admin', format_string($SITE->fullname));
    $data->notloggedinuser = (!$user);
    $message = $renderer->render_from_template('user/contact_site_support_email_body', $data);

    $sendmail = email_to_user(
        user: core_user::get_support_user(),
        from: $from,
        subject: $subject,
        messagetext: $message,
        usetrueaddress: true,
        replyto: $data->email,
        replytoname: $data->name
    );
    if (!$sendmail) {
        $supportemail = $CFG->supportemail;
        $form->set_data($data);
        $templatectx = [
            'supportemail' => $user ? html_writer::link("mailto:{$supportemail}", $supportemail) : false,
            'supportform' => $form->render(),
        ];

        $output = $renderer->render_from_template('user/contact_site_support_not_available', $templatectx);
    } else {
        $level = \core\output\notification::NOTIFY_SUCCESS;
        redirect($CFG->wwwroot, get_string('supportmessagesent', 'user'), 3, $level);
    }
} else {
    $output = $form->render();
}

echo $OUTPUT->header();

echo $output;

echo $OUTPUT->footer();
