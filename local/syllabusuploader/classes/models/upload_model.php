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
 * @package    local_syllabusuploader
 * @copyright  2023 onwards LSU Online & Continuing Education
 * @copyright  2023 onwards Tim Hunt, Robert Russo, David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class upload_model {

    /** Simple CRUD operations to handle the files.
     *
     * @param type form object
     * @return boolean
     */
    public function save($object) {
        global $DB;

        // Transform the object.
        $trob = $this->transform($object);

        // Try to insert the record.
        try {
            foreach ($trob as $disfile) {
                // Insert the record and return true/false.
                if (!$response = $DB->insert_record('local_syllabusuploader_file', $disfile, $returnid = true)) {
                    return false;
                }
            }
            return $response;
        } catch (Exception $exc) {
            debugging("Uh Oh....NOTICE - something did done broken. ". $ex);
        }
    }

    public function update($object) {
        global $DB;

        // Try to update the record.
        try {
            // Set the id.
            $object->id = $object->idfile;
            // Update the record and return true/false.
            $response = $DB->update_record('local_syllabusuploader_file', $object, false);
            return $response;
        } catch (Exception $ex) {
            debugging("Uh Oh....NOTICE - something did done broken. ". $ex);
        }
    }

    public function get($instance) {
        global $DB;

        // Try to get the records.
        try {
            // Return the data.
            return $DB->get_records(
                'local_syllabusuploader_file',
                array('instance' => $instance),
                null,
                'instance, local_syllabusuploader_files'
            )[$instance];
        } catch (Exception $ex) {
            debugging("Uh Oh....NOTICE - something did done broken. ". $ex);
        }
    }

    public function delete($pfileid, $mfileid) {
        global $DB;
        // Try to delete the records.
        try {
            // Delete the record.
            $DB->delete_records('local_syllabusuploader_file', array("id" => $pfileid));
            // Get the file manager.
            $fs = get_file_storage();
            // Get the file.
            $file = $fs->get_file_by_id($mfileid);
            // Delete it.
            $file->delete();

        } catch (Exception $ex) {
            debugging("Uh Oh....NOTICE - something didn't delete properly. ". $ex);
        }
    }

    public function transform($object) {
        global $DB;

        // Build the SQL.
        $sql = "SELECT * FROM mdl_files
            WHERE itemid = ". $object->syllabusuploader_file."
            AND filename <> '.'
            AND filearea <> 'draft'";

        // Get the files.
        $files = $DB->get_records_sql($sql);

        $listoffiles = array();
        foreach ($files as $file) {
            $temp = array(
                "fileid" => $file->id,
                "filename" => $file->filename,
                "itemid" => $file->itemid,
                "timecreated" => $file->timecreated,
                "timemodified" => $file->timemodified
            );
            $listoffiles[] = $temp;
        }
        return $listoffiles;
    }
}
