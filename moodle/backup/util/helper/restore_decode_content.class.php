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
 * @package moodlecore
 * @subpackage backup-helper
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class in charge of providing the contents to be processed by restore_decode_rules
 *
 * This class is in charge of looking (in DB) for the contents needing to be
 * processed by the declared restore_decode_rules. Basically it iterates over
 * one recordset (optimised by joining them with backup_ids records), retrieving
 * them from DB, delegating process to the restore_plan and storing results back
 * to DB.
 *
 * Implements one visitor-like pattern so the decode_processor will visit it
 * to get all the contents processed by its defined rules
 *
 * TODO: Complete phpdocs
 */
class restore_decode_content implements processable {

    protected $tablename; // Name, without prefix, of the table we are going to retrieve contents
    protected $fields;    // Array of fields we are going to decode in that table (usually 1)
    protected $mapping;   // Mapping (itemname) in backup_ids used to determine target ids (defaults to $tablename)

    protected $restoreid; // Unique id of the restore operation we are running
    protected $iterator;  // The iterator for this content (usually one recordset)

    public function __construct($tablename, $fields, $mapping = null) {
        // TODO: check table exists
        // TODO: check fields exist
        $this->tablename = $tablename;
        $this->fields    = !is_array($fields) ? array($fields) : $fields; // Accept string/array
        $this->mapping   = is_null($mapping) ? $tablename : $mapping; // Default to tableanme
        $this->restoreid = 0;
    }

    public function set_restoreid($restoreid) {
        $this->restoreid = $restoreid;
    }

    public function process($processor) {
        if (!$processor instanceof restore_decode_processor) { // No correct processor, throw exception
            throw new restore_decode_content_exception('incorrect_restore_decode_processor', get_class($processor));
        }
        if (!$this->restoreid) { // Check restoreid is set
            throw new restore_decode_rule_exception('decode_content_restoreid_not_set');
        }

        // Get the iterator of contents
        $it = $this->get_iterator();
        foreach ($it as $itrow) {               // Iterate over rows
            $itrowarr   = (array)$itrow;        // Array-ize for clean access
            $rowchanged = false;                // To track changes in the row
            foreach ($this->fields as $field) { // Iterate for each field
                $content = $this->preprocess_field($itrowarr[$field]);     // Apply potential pre-transformations
                if ($result = $processor->decode_content($content)) {
                    $itrowarr[$field] = $this->postprocess_field($result); // Apply potential post-transformations
                    $rowchanged = true;
                }
            }
            if ($rowchanged) { // Change detected, perform update in the row
                $this->update_iterator_row($itrowarr);
            }
        }
        $it->close(); // Always close the iterator at the end
    }

// Protected API starts here

    protected function get_iterator() {
        global $DB;

        // Build the SQL dynamically here
        $fieldslist = 't.' . implode(', t.', $this->fields);
        $sql = "SELECT t.id, $fieldslist
                  FROM {" . $this->tablename . "} t
                  JOIN {backup_ids_temp} b ON b.newitemid = t.id
                 WHERE b.backupid = ?
                   AND b.itemname = ?";
        $params = array($this->restoreid, $this->mapping);
        return ($DB->get_recordset_sql($sql, $params));
    }

    protected function update_iterator_row($row) {
        global $DB;
        $DB->update_record($this->tablename, $row);
    }

    protected function preprocess_field($field) {
        return $field;
    }

    protected function postprocess_field($field) {
        return $field;
    }
}

/*
 * Exception class used by all the @restore_decode_content stuff
 */
class restore_decode_content_exception extends backup_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        return parent::__construct($errorcode, $a, $debuginfo);
    }
}
