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
 * @package   local_email
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once('lib/vars.php');
require_once('lib/api.php');

function email_cron() {
    global $DB;

    // Delete emails older than 6 months to prevent the email table from clogging up the database.
    $halfyearagoish = time() - 6 * 30 * 24 * 60 * 60;
    $now = time();
    $DB->delete_records_select('email', "modifiedtime < $halfyearagoish AND due < $now");

    // Send emails.
    mtrace("Processign email cron");
    if ($emails = $DB->get_records_sql("SELECT e.* from {email} e
                                        JOIN {user} u ON (e.userid = u.id)
                                        WHERE e.sent IS NULL
                                        AND e.due < :now
                                        AND u.deleted = 0
                                        AND u.suspended = 0", array('now' => $now))) {
        foreach ($emails as $email) {
            $company = new company($email->companyid);
            $managertype = 0;
            if (strpos($email->templatename, 'manager')) {
                $managertype = 1;
            }
            if (strpos($email->templatename, 'supervisor')) {
                $managertype = 2;
            }
            if (!$company->email_template_is_enabled($email->templatename, $managertype)) {
                $DB->delete_records('email', array('id' => $email->id));
                continue;
            } else {
                EmailTemplate::send_to_user($email);
                $email->modifiedtime = $email->sent = time();
                $email->id = $email->id;
                $DB->update_record('email', $email);
            }
        }
    }

    // Send company suspended emails. Users are suspended so not picked up above.
    if ($emails = $DB->get_records_sql("SELECT e.* from {email} e
                                        JOIN {user} u ON (e.userid = u.id)
                                        WHERE e.sent IS NULL
                                        AND e.due < :now
                                        AND e.templatename = :templatename
                                        AND u.deleted = 0",
                                        ['now' => $now,
                                         'templatename' => 'company_suspended'])) {
        foreach ($emails as $email) {
            $company = new company($email->companyid);
            $managertype = 0;
            if (strpos($email->templatename, 'manager')) {
                $manapegertype = 1;
            }
            if (strpos($email->templatename, 'supervisor')) {
                $managertype = 2;
            }
            if (!$company->email_template_is_enabled($email->templatename, $managertype)) {
                $DB->delete_records('email', array('id' => $email->id));
                continue;
            } else {
                EmailTemplate::send_to_user($email);
                $email->modifiedtime = $email->sent = time();
                $email->id = $email->id;
                $DB->update_record('email', $email);
            }
        }
    }
}

/**
 * Serves any files associated with the email settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function local_email_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $filename = $args[1];
    $itemid = $args[0];
    if ($filearea == 'companylogo') {
        $itemid = 0;
    }

    if (!$file = $fs->get_file($context->id, 'local_email', $filearea, $itemid, '/', $filename) or $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 0, 0, $forcedownload);
}

