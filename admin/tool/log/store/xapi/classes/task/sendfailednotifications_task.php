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

namespace logstore_xapi\task;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/admin/tool/log/store/xapi/lib.php');
require_once($CFG->libdir . '/weblib.php');
require_once($CFG->libdir . '/classes/user.php');
require_once($CFG->libdir . '/messagelib.php');

use tool_log\log\manager;
use logstore_xapi\log\store;

/**
 * Send failed notifications.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sendfailednotifications_task extends \core\task\scheduled_task {

    /**
     * Default mail sender.
     */
    const DEFAULT_SENDER = -99;

    /**
     * Default mail sender name.
     */
    const DEFAULT_SENDER_NAME = "";

    /**
     * Default mail sender email address.
     */
    const DEFAULT_SENDER_EMAIL = "";

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('tasksendfailednotifications', 'logstore_xapi');
    }

    /**
     * Get the last id of the last sent notification. This will be the largest id from the events from the last sent notification.
     *
     * @return int
     * @throws \dml_exception
     */
    private function get_last_sent_id() {
        global $DB;

        $lastnotification = $DB->get_record_sql('SELECT MAX(failedlogid) AS id FROM {logstore_xapi_notif_sent_log}');

        if ($lastnotification && isset($lastnotification->id)) {
            return $lastnotification->id;
        }

        // We return 0 in the event no notification emails have been sent before.
        // This will mean it will count all the rows in failed log.
        return 0;
    }

    /**
     * Get the last id of the new notifications to send, if there are no notifications to send then return false.
     *
     * @param  int $lastsentid id of the last failed event from the last sent notification.
     * @return int|bool
     * @throws \dml_exception
     */
    private function get_failed_last_id($lastsentid) {
        global $DB;

        $sql = 'SELECT MAX(id) AS id
                  FROM {logstore_xapi_failed_log}
                 WHERE id > :lastsentid
                HAVING COUNT(id) >= :threshold';

        $params = [
            'threshold' => get_config('logstore_xapi', 'errornotificationtrigger'),
            'lastsentid' => $lastsentid
        ];

        $lastnotification = $DB->get_record_sql($sql, $params);
        if ($lastnotification) {
            return $lastnotification->id;
        }
        return false;
    }

    /**
     * Get the counts of failures for each error type. Only count events since last notification.
     *
     * @param int $lastsentid id of the last failed event from the last sent notification.
     * @return array
     * @throws \dml_exception
     */
    private function get_failed_counts($lastsentid) {
        global $DB;

        $sql = 'SELECT eventname, COUNT(eventname) AS count
                  FROM {logstore_xapi_failed_log}
                 WHERE id > :lastsentid
              GROUP BY eventname';

        return $DB->get_records_sql($sql, ['lastsentid' => $lastsentid]);
    }

    /**
     * Send email using email_to_user.
     *
     * @param string $message email message
     * @param string $subject email subject
     * @param object $user user to receive email
     * @param int $lastfailedid
     * @return bool
     * @throws \dml_exception
     */
    private function send_notification_email($message, $subject, $user, $lastfailedid) {
        global $DB;

        // Check if we have an actual moodle user, if not we need to setup a temp user.
        if (empty($user->id)) {
            $email = $user->email;
            $user = \core_user::get_support_user();
            $user->email = $email;
            // Unset emailstop to ensure the message is sent. This may already be the case when getting the support user.
            $user->emailstop = 0;
        }

        $from = new \stdClass();
        $from->id = self::DEFAULT_SENDER;
        $from->username = self::DEFAULT_SENDER_NAME;
        $from->email = self::DEFAULT_SENDER_EMAIL;
        $from->deleted = 0;
        $from->mailformat = FORMAT_HTML;

        // Send the email.
        $messagesent = email_to_user($user, $from, $subject, html_to_text($message), $message);
        if ($messagesent) {
            // Log that these notifications have been sent.
            $now = time();
            $lastfailed = new \stdClass();
            $lastfailed->failedlogid = $lastfailedid;
            $lastfailed->email = $user->email;
            $lastfailed->timecreated = $now;

            $DB->insert_record('logstore_xapi_notif_sent_log', $lastfailed);
            return $messagesent;
        }

        return 0;
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $OUTPUT;

        echo get_string('insendfailednotificationstask', 'logstore_xapi') . PHP_EOL;

        $enablesendingnotifications = get_config('logstore_xapi', 'enablesendingnotifications');
        if (empty($enablesendingnotifications)) {
            echo get_string('notificationsnotenabled', 'logstore_xapi') . PHP_EOL;
            return;
        }

        $lastsentid = $this->get_last_sent_id();

        $lastfailedid = $this->get_failed_last_id($lastsentid);

        if (empty($lastfailedid)) {
            echo get_string('notificationtriggerlimitnotreached', 'logstore_xapi') . PHP_EOL;
            return;
        }

        // Set up email message.
        $subject = get_string('failedsubject', 'logstore_xapi');
        $messagedata = new \stdClass();
        $messagedata->lmsinstance = new \moodle_url('/');

        $messagedata->errorlogpageurl = new \moodle_url('/admin/tool/log/store/xapi/report.php');
        $errors = $this->get_failed_counts($lastsentid);
        $messagedata->errors = array_values($errors);
        $message = $OUTPUT->render_from_template('logstore_xapi/failed_notification_email', $messagedata);

        $users = logstore_xapi_get_users_for_notifications();
        foreach ($users as $user) {
            $this->send_notification_email($message, $subject, $user, $lastfailedid);
        }
    }
}
