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
 * An adhoc task for emailing certificates.
 *
 * @package    mod_customcert
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_customcert\task;

use mod_customcert\helper;

/**
 * An adhoc task for emailing certificates per issue.
 *
 * @package    mod_customcert
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class email_certificate_task extends \core\task\adhoc_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskemailcertificate', 'customcert');
    }

    /**
     * Execute.
     */
    public function execute() {
        global $DB;

        $customdata = $this->get_custom_data();

        $issueid = $customdata->issueid;
        $customcertid = $customdata->customcertid;
        $sql = "SELECT c.*, ct.id as templateid, ct.name as templatename, ct.contextid, co.id as courseid,
                       co.fullname as coursefullname, co.shortname as courseshortname
                  FROM {customcert} c
                  JOIN {customcert_templates} ct ON c.templateid = ct.id
                  JOIN {course} co ON c.course = co.id
                 WHERE c.id = :id";

        $customcert = $DB->get_record_sql($sql, ['id' => $customcertid]);

        // The renderers used for sending emails.
        $page = new \moodle_page();
        $htmlrenderer = $page->get_renderer('mod_customcert', 'email', 'htmlemail');
        $textrenderer = $page->get_renderer('mod_customcert', 'email', 'textemail');

        // Get the context.
        $context = \context::instance_by_id($customcert->contextid);

        // Get the person we are going to send this email on behalf of.
        $userfrom = \core_user::get_noreply_user();

        $courseshortname = format_string($customcert->courseshortname, true, ['context' => $context]);
        $coursefullname = format_string($customcert->coursefullname, true, ['context' => $context]);
        $certificatename = format_string($customcert->name, true, ['context' => $context]);

        // Used to create the email subject.
        $info = new \stdClass();
        $info->coursename = $courseshortname; // Added for BC, so users who have edited the string don't lose this value.
        $info->courseshortname = $courseshortname;
        $info->coursefullname = $coursefullname;
        $info->certificatename = $certificatename;

        // Get the information about the user and the certificate issue.
        $userfields = helper::get_all_user_name_fields('u');
        $sql = "SELECT u.id, u.username, $userfields, u.email, ci.id as issueid, ci.emailed
                  FROM {customcert_issues} ci
                  JOIN {user} u
                    ON ci.userid = u.id
                 WHERE ci.customcertid = :customcertid
                   AND ci.id = :issueid";
        $user = $DB->get_record_sql($sql, ['customcertid' => $customcertid, 'issueid' => $issueid]);

        // Create a directory to store the PDF we will be sending.
        $tempdir = make_temp_directory('certificate/attachment');
        if (!$tempdir) {
            return;
        }

        // Setup the user for the cron.
        \core\cron::setup_user($user);

        $userfullname = fullname($user);
        $info->userfullname = $userfullname;

        // Now, get the PDF.
        $template = new \stdClass();
        $template->id = $customcert->templateid;
        $template->name = $customcert->templatename;
        $template->contextid = $customcert->contextid;
        $template = new \mod_customcert\template($template);
        $filecontents = $template->generate_pdf(false, $user->id, true);

        // Set the name of the file we are going to send.
        $filename = $courseshortname . '_' . $certificatename;
        $filename = \core_text::entities_to_utf8($filename);
        $filename = strip_tags($filename);
        $filename = rtrim($filename, '.');
        $filename = str_replace('&', '_', $filename) . '.pdf';

        // Create the file we will be sending.
        $tempfile = $tempdir . '/' . md5(microtime() . $user->id) . '.pdf';
        file_put_contents($tempfile, $filecontents);

        if ($customcert->emailstudents) {
            $renderable = new \mod_customcert\output\email_certificate(true, $userfullname, $courseshortname,
                $coursefullname, $certificatename, $context->instanceid);

            $subject = get_string('emailstudentsubject', 'customcert', $info);
            $message = $textrenderer->render($renderable);
            $messagehtml = $htmlrenderer->render($renderable);
            email_to_user($user, $userfrom, html_entity_decode($subject, ENT_COMPAT), $message,
                $messagehtml, $tempfile, $filename);
        }

        if ($customcert->emailteachers) {
            $teachers = get_enrolled_users($context, 'moodle/course:update');

            $renderable = new \mod_customcert\output\email_certificate(false, $userfullname, $courseshortname,
                $coursefullname, $certificatename, $context->instanceid);

            $subject = get_string('emailnonstudentsubject', 'customcert', $info);
            $message = $textrenderer->render($renderable);
            $messagehtml = $htmlrenderer->render($renderable);
            foreach ($teachers as $teacher) {
                email_to_user($teacher, $userfrom, html_entity_decode($subject, ENT_COMPAT),
                    $message, $messagehtml, $tempfile, $filename);
            }
        }

        if (!empty($customcert->emailothers)) {
            $others = explode(',', $customcert->emailothers);
            foreach ($others as $email) {
                $email = trim($email);
                if (validate_email($email)) {
                    $renderable = new \mod_customcert\output\email_certificate(false, $userfullname,
                        $courseshortname, $coursefullname, $certificatename, $context->instanceid);

                    $subject = get_string('emailnonstudentsubject', 'customcert', $info);
                    $message = $textrenderer->render($renderable);
                    $messagehtml = $htmlrenderer->render($renderable);

                    $emailuser = new \stdClass();
                    $emailuser->id = -1;
                    $emailuser->email = $email;
                    email_to_user($emailuser, $userfrom, html_entity_decode($subject, ENT_COMPAT), $message,
                        $messagehtml, $tempfile, $filename);
                }
            }
        }

        // Set the field so that it is emailed.
        $DB->set_field('customcert_issues', 'emailed', 1, ['id' => $issueid]);
    }
}
