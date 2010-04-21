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
 * @package    moodlecore
 * @subpackage backup-structure
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * TODO: Finish phpdocs
 */

/**
 * Instantiable class representing one final element atom (name/value/parent) piece of information on backup
 */
class backup_final_element extends base_final_element implements processable, annotable {

    protected $annotationitem; // To store the item this element will be responsible to annotate

    public function process($processor) {
        if (!$processor instanceof base_processor) { // No correct processor, throw exception
            throw new base_element_struct_exception('incorrect_processor');
        }
        $processor->process_final_element($this);
    }

    public function set_annotation_item($itemname) {
        if (!empty($this->annotationitem)) {
            $a = new stdclass();
            $a->attribute = $this->get_name();
            $a->annotating= $this->annotationitem;
            throw new base_element_struct_exception('element_already_used_for_annotation', $a);
        }
        $this->annotationitem = $itemname;
    }

    public function annotate($backupid) {
        if (empty($this->annotationitem)) { // We aren't annotating this item
            return;
        }
        if (!$this->is_set()) {
            throw new base_element_struct_exception('element_has_not_value', $this->get_name());
        }
        backup_structure_dbops::insert_backup_ids_record($backupid, $this->annotationitem, $this->get_value());
    }

// Protected API starts here

    /**
     * Returns one instace of the @base_attribute class to work with
     * when attributes are added simply by name
     */
    protected function get_new_attribute($name) {
        return new backup_attribute($name);
    }
}
