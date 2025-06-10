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
 * @package     mod_flashcard
 * @category    mod
 * @author      Tomasz Muras <nexor1984@gmail.com>
 * @author      Valery Fremaux <valery.fremaux@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/flashcard/backup/moodle2/restore_flashcard_stepslib.php');

class restore_flashcard_activity_task extends restore_activity_task {

    protected function define_my_settings() {
        assert(1);
    }

    protected function define_my_steps() {
        $this->add_step(new restore_flashcard_activity_structure_step('flashcard_structure', 'flashcard.xml'));
    }

    static public function define_decode_contents() {

        $contents = array();
        $contents[] = new restore_decode_content('flashcard', array('intro'), 'flashcard');

        return $contents;
    }

    static public function define_decode_rules() {

        $rules = array();
        $rules[] = new restore_decode_rule('FLASHCARDINDEX', '/mod/flashcard/index.php?id=$1', 'course');
        $rules[] = new restore_decode_rule('FLASHCARDVIEWBYID', '/mod/flashcard/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('FLASHCARDREPORT', '/mod/flashcard/report.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('FLASHCARDEDIT', '/mod/flashcard/edit.php?id=$1', 'course_module');

        return $rules;
    }

    static public function define_restore_log_rules() {
        $rules = array();
        return $rules;
    }
}
