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
 * @package    block_pu
 * @copyright  2021 onwards LSU Online & Continuing Education
 * @copyright  2021 onwards Tim Hunt, Robert Russo, David Lowe
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
        $trob = $this->transform($object);
        try {

            $response = $DB->insert_record('block_pu_file', $trob, $returnid = true);
            return $response;

        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    public function update($object) {
        global $DB;

        try {
            $object->id = $object->idfile;
            $response = $DB->update_record('block_pu_file', $object, false);
            return $response;
        } catch (Exception $ex) {

        }
    }

    public function get($instance) {
        global $DB;

        try {
            return $DB->get_records('block_pu_file', array('instance' => $instance), null, 'instance, block_pu_files')[$instance];
        } catch (Exception $ex) {

        }
    }

    public function delete($pfileid, $mfileid) {
        global $DB;
        try {
            $DB->delete_records('block_pu_file', array("id" => $pfileid));
            $fs = get_file_storage();
            $file = $fs->get_file_by_id($mfileid);
            $file->delete();

        } catch (Exception $ex) {
            error_log("Uh Oh....NOTICE - something didn't delete properly");
        }
    }

    public function transform($object) {
        global $DB;

        $sql = "SELECT * FROM {files}
            WHERE itemid = ". $object->pu_file."
            AND filename <> '.'
            AND filearea <> 'draft'";

        $files = $DB->get_records_sql($sql);
        $count = count($files);

        if ($count == 1) {
            $files = array_values($files);

            return array(
                "fileid" => $files[0]->id,
                "filename" => $files[0]->filename,
                "itemid" => $files[0]->itemid,
                "timecreated" => $files[0]->timecreated,
                "timemodified" => $files[0]->timemodified,
            );
        } else {
            error_log("\n\n\e[0;31m****************************************************");
            error_log("\e[0;31mupload_model -> ERROR: returned multiple objects");
            error_log("\e[0;31m****************************************************\n\n");
        }
    }
}
