<?php

/**
 * Version details
 *
 * @package    local_message
 * @author  Albohtori
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// here basic methods to manage messages
namespace local_message;

use stdClass;
use dml_exception;

class manager
{
    /**
     * @param string $message_text
     * @param string $message_type
     * @return bool
     */
    public function create_message(string $message_text, string $message_type): bool
    {
        global $DB;
        $record_to_insert = new stdClass();
        $record_to_insert->messagetext = $message_text;
        $record_to_insert->messagetype = $message_type;

        try {
            return $DB->insert_record('local_message', $record_to_insert);
        } catch (dml_exception $e) {
            return false;
        }
    }

    /**
     * @param $userid
     * @return array
     */
    public function get_messages($userid): array
    {
        global $DB;

        $sql = "SELECT lm.id, lm.messagetext, lm.messagetype 
        FROM {local_message} lm 
        LEFT OUTER JOIN {local_message_read} lmr ON lm.id = lmr.messageid AND lmr.userid = :userid 
        WHERE lmr.userid IS NULL";

        $params = [
            'userid' => $userid,
        ];

        try {
            return $DB->get_records_sql($sql, $params);
        } catch (dml_exception $e) {
            // log error here
            return [];
        }
    }

    /**
     * @param $messageid
     * @param $userid
     * @return void
     */
    public function mark_message_read($messageid, $userid)
    {
        global $DB;
        $read_record = new stdClass();
        $read_record->messageid = $messageid;
        $read_record->userid = $userid;
        $read_record->timeread = time();
        try {
            return $DB->insert_record('local_message_read', $read_record, false);
        } catch (dml_exception $e) {
            return false;
        }
    }
}