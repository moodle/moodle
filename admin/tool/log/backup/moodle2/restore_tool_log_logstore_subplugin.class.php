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
 * Restore support for tool_log logstore subplugins.
 *
 * @package    tool_log
 * @category   backup
 * @copyright  2015 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Parent class of all the logstore subplugin implementations.
 *
 * Note: While this intermediate class is not strictly required and all the
 * subplugin implementations can extend directly {@link restore_subplugin},
 * it is always recommended to have it, both for better testing and also
 * for sharing code between all subplugins.
 */
abstract class restore_tool_log_logstore_subplugin extends restore_subplugin {

    /**
     * Process log entries.
     *
     * This method proceeds to read, complete, remap and, finally,
     * discard or save every log entry.
     *
     * @param array $data log entry.
     * @param bool $jsonformat If true, uses JSON format for the 'other' field
     * @return object|null $dataobject A data object with values for one or more fields in the record,
     *  or null if we are not going to process the log.
     */
    protected function process_log($data, bool $jsonformat = false) {
        $data = (object) $data;

        // Complete the information that does not come from backup.
        $contextid = $data->contextid;
        if (!$data->contextid = $this->get_mappingid('context', $contextid)) {
            $message = "Context id \"$contextid\" could not be mapped. Skipping log record.";
            $this->log($message, backup::LOG_DEBUG);
            return;
        }
        $context = context::instance_by_id($data->contextid, MUST_EXIST);
        $data->contextlevel = $context->contextlevel;
        $data->contextinstanceid = $context->instanceid;
        $data->courseid = $this->task->get_courseid();

        // Remap users.
        $userid = $data->userid;
        if (!$data->userid = $this->get_mappingid('user', $userid)) {
            $message = "User id \"$userid\" could not be mapped. Skipping log record.";
            $this->log($message, backup::LOG_DEBUG);
            return;
        }
        if (!empty($data->relateduserid)) { // This is optional.
            $relateduserid = $data->relateduserid;
            if (!$data->relateduserid = $this->get_mappingid('user', $relateduserid)) {
                $message = "Related user id \"$relateduserid\" could not be mapped. Skipping log record.";
                $this->log($message, backup::LOG_DEBUG);
                return;
            }
        }
        if (!empty($data->realuserid)) { // This is optional.
            $realuserid = $data->realuserid;
            if (!$data->realuserid = $this->get_mappingid('user', $realuserid)) {
                $message = "Real user id \"$realuserid\" could not be mapped. Skipping log record.";
                $this->log($message, backup::LOG_DEBUG);
                return;
            }
        }

        // There is no need to roll dates. Logs are supposed to be immutable. See MDL-44961.

        // Revert other to its original php way.
        $data->other = \tool_log\helper\reader::decode_other(base64_decode($data->other));

        // Arrived here, we have both 'objectid' and 'other' to be converted. This is the tricky part.
        // Both are pointing to other records id, but the sources are not identified in the
        // same way restore mappings work. So we need to delegate them to some resolver that
        // will give us the correct restore mapping to be used.
        if (!empty($data->objectid)) {
            // Check if there is an available class for this event we can use to map this value.
            $eventclass = $data->eventname;
            if (class_exists($eventclass)) {
                $mapping = $eventclass::get_objectid_mapping();
                if ($mapping) {
                    // Check if it can not be mapped.
                    if ((is_int($mapping) && $mapping === \core\event\base::NOT_MAPPED) ||
                        ($mapping['restore'] === \core\event\base::NOT_MAPPED)) {
                        $data->objectid = \core\event\base::NOT_MAPPED;
                    } else {
                        $data->objectid = $this->get_mappingid($mapping['restore'], $data->objectid,
                            \core\event\base::NOT_FOUND);
                    }
                }
            } else {
                $message = "Event class not found: \"$eventclass\". Skipping log record.";
                $this->log($message, backup::LOG_DEBUG);
                return; // No such class, can not restore.
            }
        }
        if (!empty($data->other)) {
            // Check if there is an available class for this event we can use to map this value.
            $eventclass = $data->eventname;
            if (class_exists($eventclass)) {
                $othermapping = $eventclass::get_other_mapping();
                if ($othermapping) {
                    // Go through the data we have.
                    foreach ($data->other as $key => $value) {
                        // Check if there is a corresponding key we can use to map to.
                        if (isset($othermapping[$key]) && !empty($value)) {
                            // Ok, let's map this.
                            $mapping = $othermapping[$key];
                            // Check if it can not be mapped.
                            if ((is_int($mapping) && $mapping === \core\event\base::NOT_MAPPED) ||
                                ($mapping['restore'] === \core\event\base::NOT_MAPPED)) {
                                $data->other[$key] = \core\event\base::NOT_MAPPED;
                            } else {
                                $data->other[$key] = $this->get_mappingid($mapping['restore'], $value,
                                    \core\event\base::NOT_FOUND);
                            }
                        }
                    }
                }
            } else {
                $message = "Event class not found: \"$eventclass\". Skipping log record.";
                $this->log($message, backup::LOG_DEBUG);
                return; // No such class, can not restore.
            }
        }

        // Serialize 'other' field so we can store it in the DB.
        if ($jsonformat) {
            $data->other = json_encode($data->other);
        } else {
            $data->other = serialize($data->other);
        }

        return $data;
    }
}
