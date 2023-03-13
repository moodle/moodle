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

namespace mod_data\local;

use coding_exception;
use context;
use context_system;
use dml_exception;
use moodle_exception;

/**
 * Utility class for exporting data from a mod_data instance.
 *
 * @package    mod_data
 * @copyright  2023 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exporter_utils {

    /**
     * Exports the data of the mod_data instance to an exporter object which then can export it to a file format.
     *
     * @param int $dataid
     * @param array $fields
     * @param array $selectedfields
     * @param exporter $exporter the exporter object used
     * @param int $currentgroup group ID of the current group. This is used for
     *  exporting data while maintaining group divisions.
     * @param context|null $context the context in which the operation is performed (for capability checks)
     * @param bool $userdetails whether to include the details of the record author
     * @param bool $time whether to include time created/modified
     * @param bool $approval whether to include approval status
     * @param bool $tags whether to include tags
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function data_exportdata(int $dataid, array $fields, array $selectedfields, exporter $exporter,
        int $currentgroup = 0, context $context = null, bool $userdetails = false, bool $time = false, bool $approval = false,
        bool $tags = false): void {
        global $DB;

        if (is_null($context)) {
            $context = context_system::instance();
        }
        // Exporting user data needs special permission.
        $userdetails = $userdetails && has_capability('mod/data:exportuserinfo', $context);

        // Populate the header in first row of export.
        $header = [];
        foreach ($fields as $key => $field) {
            if (!in_array($field->field->id, $selectedfields)) {
                // Ignore values we aren't exporting.
                unset($fields[$key]);
            } else {
                $header[] = $field->field->name;
            }
        }
        if ($tags) {
            $header[] = get_string('tags', 'data');
        }
        if ($userdetails) {
            $header[] = get_string('user');
            $header[] = get_string('username');
            $header[] = get_string('email');
        }
        if ($time) {
            $header[] = get_string('timeadded', 'data');
            $header[] = get_string('timemodified', 'data');
        }
        if ($approval) {
            $header[] = get_string('approved', 'data');
        }
        $exporter->add_row($header);

        $datarecords = $DB->get_records('data_records', array('dataid' => $dataid));
        ksort($datarecords);
        $line = 1;
        foreach ($datarecords as $record) {
            // Get content indexed by fieldid.
            if ($currentgroup) {
                $select = 'SELECT c.fieldid, c.content, c.content1, c.content2, c.content3, c.content4 FROM {data_content} c, '
                    . '{data_records} r WHERE c.recordid = ? AND r.id = c.recordid AND r.groupid = ?';
                $where = array($record->id, $currentgroup);
            } else {
                $select = 'SELECT fieldid, content, content1, content2, content3, content4 FROM {data_content} WHERE recordid = ?';
                $where = array($record->id);
            }

            if ($content = $DB->get_records_sql($select, $where)) {
                foreach ($fields as $field) {
                    $contents = '';
                    if (isset($content[$field->field->id])) {
                        $contents = $field->export_text_value($content[$field->field->id]);
                    }
                    // Just be double sure.
                    $contents = !empty($contents) ? $contents : '';
                    $exporter->add_to_current_row($contents);
                }
                if ($tags) {
                    $itemtags = \core_tag_tag::get_item_tags_array('mod_data', 'data_records', $record->id);
                    $exporter->add_to_current_row(implode(', ', $itemtags));
                }
                if ($userdetails) { // Add user details to the export data.
                    $userdata = get_complete_user_data('id', $record->userid);
                    $exporter->add_to_current_row(fullname($userdata));
                    $exporter->add_to_current_row($userdata->username);
                    $exporter->add_to_current_row($userdata->email);
                }
                if ($time) { // Add time added / modified.
                    $exporter->add_to_current_row(userdate($record->timecreated));
                    $exporter->add_to_current_row(userdate($record->timemodified));
                }
                if ($approval) { // Add approval status.
                    $exporter->add_to_current_row((int) $record->approved);
                }
            }
            $exporter->next_row();
        }
    }
}
