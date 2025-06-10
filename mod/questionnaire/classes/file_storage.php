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

namespace mod_questionnaire;

/**
 * Defines the file stoeage class for questionnaire.
 * @package mod_questionnaire
 * @copyright  2020 onwards Mike Churchward (mike.churchward@poetopensource.org)
 * @author Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_storage extends \file_storage {

    /**
     * Copy all the files in a file area from one context to another.
     *
     * @param int $oldcontextid the context the files are being moved from.
     * @param int $newcontextid the context the files are being moved to.
     * @param string $component the plugin that these files belong to.
     * @param string $filearea the name of the file area.
     * @param int|boolean $olditemid The identifier for the old file area if required.
     * @param int|boolean $newitemid The identifier for the new file area if different than old.
     * @return int the number of files copied, for information.
     * @throws \coding_exception
     * @throws \file_exception
     * @throws \stored_file_creation_exception
     */
    public function copy_area_files_to_new_context($oldcontextid, $newcontextid, $component, $filearea, $olditemid = false,
                                                   $newitemid = false) {
        $count = 0;

        $oldfiles = $this->get_area_files($oldcontextid, $component, $filearea, $olditemid, 'id', false);
        foreach ($oldfiles as $oldfile) {
            $filerecord = new \stdClass();
            $filerecord->contextid = $newcontextid;
            if ($newitemid !== false) {
                $filerecord->itemid = $newitemid;
            } else {
                $filerecord->itemid = $olditemid;
            }
            $this->create_file_from_storedfile($filerecord, $oldfile);
            $count += 1;
        }
        return $count;
    }
}
