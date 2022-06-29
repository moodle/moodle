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
 * Search area for received messages.
 *
 * @package    core_message
 * @copyright  2016 Devang Gaur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_message\search;


defined('MOODLE_INTERNAL') || die();

/**
 * Search area for received messages.
 *
 * @package    core_message
 * @copyright  2016 Devang Gaur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_received extends base_message {

    /**
     * Returns a recordset with the messages for indexing.
     *
     * @param int $modifiedfrom
     * @param \context|null $context Optional context to restrict scope of returned results
     * @return moodle_recordset|null Recordset (or null if no results)
     */
    public function get_document_recordset($modifiedfrom = 0, \context $context = null) {
        return $this->get_document_recordset_helper($modifiedfrom, $context, 'useridto');
    }

    /**
     * Returns the document associated with this message record.
     *
     * @param stdClass $record
     * @param array    $options
     * @return \core_search\document
     */
    public function get_document($record, $options = array()) {
        return parent::get_document($record, array('user1id' => $record->useridto, 'user2id' => $record->useridfrom));
    }

    /**
     * Whether the user can access the document or not.
     *
     * @param int $id The message instance id.
     * @return int
     */
    public function check_access($id) {
        global $CFG, $DB, $USER;

        if (!$CFG->messaging) {
            return \core_search\manager::ACCESS_DENIED;
        }

        $sql = "SELECT m.*, mcm.userid as useridto
                  FROM {messages} m
            INNER JOIN {message_conversations} mc
                    ON m.conversationid = mc.id
            INNER JOIN {message_conversation_members} mcm
                    ON mcm.conversationid = mc.id
                 WHERE mcm.userid != m.useridfrom
                   AND m.id = :id";
        $message = $DB->get_record_sql($sql, array('id' => $id));
        if (!$message) {
            return \core_search\manager::ACCESS_DELETED;
        }

        $userfrom = \core_user::get_user($message->useridfrom, 'id, deleted');
        $userto = \core_user::get_user($message->useridto, 'id, deleted');

        if (!$userfrom || !$userto || $userfrom->deleted || $userto->deleted) {
            return \core_search\manager::ACCESS_DELETED;
        }

        if ($USER->id != $userto->id) {
            return \core_search\manager::ACCESS_DENIED;
        }

        $usertodeleted = $DB->record_exists('message_user_actions', ['messageid' => $id, 'userid' => $message->useridto,
            'action' => \core_message\api::MESSAGE_ACTION_DELETED]);
        if ($usertodeleted) {
            return \core_search\manager::ACCESS_DELETED;
        }

        return \core_search\manager::ACCESS_GRANTED;
    }

}
