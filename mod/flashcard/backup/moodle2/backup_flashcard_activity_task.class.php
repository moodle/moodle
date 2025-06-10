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

require_once($CFG->dirroot.'/mod/flashcard/backup/moodle2/backup_flashcard_stepslib.php');

class backup_flashcard_activity_task extends backup_activity_task {

    protected function define_my_settings() {
        assert(1);
    }

    protected function define_my_steps() {
        $this->add_step(new backup_flashcard_activity_structure_step('flashcard_structure', 'flashcard.xml'));
    }

    static public function encode_content_links($content) {
        global $CFG;

        return $content;

        $base = preg_quote($CFG->wwwroot . '/mod/flashcard', '#');

        $pattern = "#(" . $base . "\/index.php\?id\=)([0-9]+)#";
        $content = preg_replace($pattern, '$@FLASHCARDINDEX*$2@$', $content);

        $pattern = "#(" . $base . "\/view.php\?id\=)([0-9]+)#";
        $content = preg_replace($pattern, '$@FLASHCARDVIEWBYID*$2@$', $content);

        $pattern = "#(" . $base . "\/report.php\?id\=)([0-9]+)#";
        $content = preg_replace($pattern, '$@FLASHCARDREPORT*$2@$', $content);

        $pattern = "#(" . $base . "\/edit.php\?id\=)([0-9]+)#";
        $content = preg_replace($pattern, '$@FLASHCARDEDIT*$2@$', $content);

        return $content;
    }
}
