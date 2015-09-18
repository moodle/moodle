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
     * @return object|null $dataobject A data object with values for one or more fields in the record,
     *  or null if we are not going to process the log.
     */
    protected function process_log($data) {
        $data = (object) $data;

        // Complete the information that does not come from backup.
        if (!$data->contextid = $this->get_mappingid('context', $data->contextid)) {
            // Something went really wrong, cannot find the context this log belongs to.
            return;
        }
        $context = context::instance_by_id($data->contextid, MUST_EXIST);
        $data->contextlevel = $context->contextlevel;
        $data->contextinstanceid = $context->instanceid;
        $data->courseid = $this->task->get_courseid();

        // Remap users.
        if (!$data->userid = $this->get_mappingid('user', $data->userid)) {
            // Something went really wrong, cannot find the user this log belongs to.
            return;
        }
        if (!empty($data->relateduserid)) { // This is optional.
            if (!$data->relateduserid = $this->get_mappingid('user', $data->relateduserid)) {
                // Something went really wrong, cannot find the relateduserid this log is about.
                return;
            }
        }
        if (!empty($data->realuserid)) { // This is optional.
            if (!$data->realuserid = $this->get_mappingid('user', $data->realuserid)) {
                // Something went really wrong, cannot find the realuserid this log is logged in as.
                return;
            }
        }

        // Roll dates.
        $data->timecreated = $this->apply_date_offset($data->timecreated);

        // Revert other to its original php way.
        $data->other = unserialize(base64_decode($data->other));

        // Arrived here, we have both 'objectid' and 'other' to be converted. This is the tricky part.
        // Both are pointing to other records id, but the sources are not identified in the
        // same way restore mappings work. So we need to delegate them to some resolver that
        // will give us the correct restore mapping to be used.
        if (!empty($data->objectid)) {
            // TODO: Call to the resolver.
            return;
        }
        if (!empty($data->other)) {
            // TODO: Call to the resolver.
            return;
        }

        return $data;
    }
}
