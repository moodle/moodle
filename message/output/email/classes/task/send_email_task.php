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
 * Contains the class responsible for sending emails as a digest.
 *
 * @package    message_email
 * @copyright  2019 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace message_email\task;

use core\task\scheduled_task;
use moodle_recordset;

defined('MOODLE_INTERNAL') || die();

/**
 * Class responsible for sending emails as a digest.
 *
 * @package    message_email
 * @copyright  2019 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_email_task extends scheduled_task {

    /**
     * @var int $maxid This is the maximum id of the message in 'message_email_messages'.
     *                 We use this so we know what records to process, as more records may be added
     *                 while this task runs.
     */
    private $maxid;

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('tasksendemail', 'message_email');
    }

    /**
     * Send out emails.
     */
    public function execute() {
        global $DB, $PAGE, $SITE;

        // Get the maximum id we are going to use.
        // We use this as records may be added to the table while this task runs.
        $this->maxid = $DB->get_field_sql("SELECT MAX(id) FROM {message_email_messages}");

        // We are going to send these emails from 'noreplyaddress'.
        $noreplyuser = \core_user::get_noreply_user();

        // The renderers used for sending emails.
        $htmlrenderer = $PAGE->get_renderer('message_email', 'email', 'htmlemail');
        $textrenderer = $PAGE->get_renderer('message_email', 'email', 'textemail');

        // Keep track of which emails failed to send.
        $users = $this->get_unique_users();
        foreach ($users as $user) {
            cron_setup_user($user);

            $hascontent = false;
            $renderable = new \message_email\output\email_digest($user);
            $conversations = $this->get_conversations_for_user($user->id);
            foreach ($conversations as $conversation) {
                $renderable->add_conversation($conversation);
                $messages = $this->get_users_messages_for_conversation($conversation->id, $user->id);
                if ($messages->valid()) {
                    $hascontent = true;
                    foreach ($messages as $message) {
                        $renderable->add_message($message);
                    }
                }
                $messages->close();
            }
            $conversations->close();
            if ($hascontent) {
                $subject = get_string('messagedigestemailsubject', 'message_email', format_string($SITE->fullname));
                $message = $textrenderer->render($renderable);
                $messagehtml = $htmlrenderer->render($renderable);
                if (email_to_user($user, $noreplyuser, $subject, $message, $messagehtml)) {
                    $DB->delete_records_select('message_email_messages', 'useridto = ? AND id <= ?', [$user->id, $this->maxid]);
                }
            }
        }
        cron_setup_user();
        $users->close();
    }

    /**
     * Returns an array of users in the given conversation.
     *
     * @return moodle_recordset A moodle_recordset instance.
     */
    private function get_unique_users() : moodle_recordset {
        global $DB;

        $subsql = 'SELECT DISTINCT(useridto) as id
                     FROM {message_email_messages}
                    WHERE id <= ?';

        $sql = "SELECT *
                  FROM {user} u
                 WHERE id IN ($subsql)";

        return $DB->get_recordset_sql($sql, [$this->maxid]);
    }

    /**
     * Returns an array of unique conversations that require processing.
     *
     * @param int $userid The ID of the user we are sending a digest to.
     * @return moodle_recordset A moodle_recordset instance.
     */
    private function get_conversations_for_user(int $userid) : moodle_recordset {
        global $DB;

        // We shouldn't be joining directly on the group table as group
        // conversations may (in the future) be something created that
        // isn't related to an actual group in a course. However, for
        // now this will have to do before 3.7 code freeze.
        // See related MDL-63814.
        $sql = "SELECT DISTINCT mc.id, mc.name, c.id as courseid, c.fullname as coursename, g.id as groupid,
                                g.picture
                  FROM {message_conversations} mc
                  JOIN {groups} g
                    ON mc.itemid = g.id
                  JOIN {course} c
                    ON g.courseid = c.id
                  JOIN {message_email_messages} mem
                    ON mem.conversationid = mc.id
                 WHERE mem.useridto = ?
                   AND mem.id <= ?";

        return $DB->get_recordset_sql($sql, [$userid, $this->maxid]);
    }

    /**
     * Returns the messages to send to a user for a given conversation
     *
     * @param int $conversationid
     * @param int $userid
     * @return moodle_recordset A moodle_recordset instance.
     */
    protected function get_users_messages_for_conversation(int $conversationid, int $userid) : moodle_recordset {
        global $DB;

        $userfieldsapi = \core_user\fields::for_userpic();
        $usernamefields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
        $sql = "SELECT $usernamefields, m.*
                  FROM {messages} m
                  JOIN {user} u
                    ON u.id = m.useridfrom
                  JOIN {message_email_messages} mem
                    ON mem.messageid = m.id
                 WHERE mem.useridto = ?
                   AND mem.conversationid = ?
                   AND mem.id <= ?";

        return $DB->get_recordset_sql($sql, [$userid, $conversationid, $this->maxid]);
    }
}
