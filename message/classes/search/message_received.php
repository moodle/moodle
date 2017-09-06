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
     * Returns recordset containing message records.
     *
     * @param int $modifiedfrom timestamp
     * @return \moodle_recordset
     */
    public function get_recordset_by_timestamp($modifiedfrom = 0) {
        global $DB;

        // We don't want to index messages received from noreply and support users.
        $params = array('modifiedfrom' => $modifiedfrom, 'noreplyuser' => \core_user::NOREPLY_USER,
            'supportuser' => \core_user::SUPPORT_USER);
        return $DB->get_recordset_select('message_read', 'timeread >= :modifiedfrom AND
            useridto != :noreplyuser AND useridto != :supportuser', $params, 'timeread ASC');
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

        $message = $DB->get_record('message_read', array('id' => $id));
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

        if ($message->timeusertodeleted != 0) {
            return \core_search\manager::ACCESS_DELETED;
        }

        return \core_search\manager::ACCESS_GRANTED;
    }

}
