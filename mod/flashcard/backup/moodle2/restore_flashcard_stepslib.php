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

class restore_flashcard_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('flashcard', '/activity/flashcard');
        $paths[] = new restore_path_element('flashcard_deck', '/activity/flashcard/group_decks/deck');

        if ($this->get_setting_value('userinfo')) {
            $paths[] = new restore_path_element('flashcard_card', '/activity/flashcard/group_cards/card');
            $paths[] = new restore_path_element('flashcard_deckstate', '/activity/flashcard/deckstates/deckstate');
        }

        return $this->prepare_activity_structure($paths);
    }

    protected function process_flashcard($data) {
        global $DB;

        $data = (object)$data;

        $oldid = $data->id;
        unset($data->id);

        $data->course = $this->get_courseid();
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->starttime = $this->apply_date_offset($data->starttime);
        $data->endtime = $this->apply_date_offset($data->endtime);

        if (!isset($data->audiostart)) {
            $data->audiostart = 0;
        }

        $newid = $DB->insert_record('flashcard', $data);
        $this->apply_activity_instance($newid);
        $this->set_mapping('flashcard', $oldid, $newid, true);
    }

    protected function process_flashcard_deck($data) {
        global $DB;

        $data = (object)$data;

        $oldid = $data->id;
        unset($data->id);

        $data->flashcardid = $this->get_new_parentid('flashcard');

        $newid = $DB->insert_record('flashcard_deckdata', $data);
        $this->set_mapping('flashcard_deckdata', $oldid, $newid, true);

        $oldcontextid = $this->task->get_old_contextid();
        $this->add_related_files('mod_flashcard', 'questionsoundfile', 'flashcard_deckdata', $oldcontextid, $oldid);
        $this->add_related_files('mod_flashcard', 'questionimagefile', 'flashcard_deckdata', $oldcontextid, $oldid);
        $this->add_related_files('mod_flashcard', 'questionvideofile', 'flashcard_deckdata', $oldcontextid, $oldid);
        $this->add_related_files('mod_flashcard', 'answersoundfile', 'flashcard_deckdata', $oldcontextid, $oldid);
        $this->add_related_files('mod_flashcard', 'answerimagefile', 'flashcard_deckdata', $oldcontextid, $oldid);
        $this->add_related_files('mod_flashcard', 'answervideofile', 'flashcard_deckdata', $oldcontextid, $oldid);
    }

    protected function process_flashcard_card($data) {
        global $DB;

        $data = (object)$data;

        $oldid = $data->id;
        unset($data->id);

        $data->flashcardid = $this->get_new_parentid('flashcard');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->entryid = $this->get_mappingid('flashcard_deckdata', $data->entryid);

        $newid = $DB->insert_record('flashcard_card', $data);
    }

    protected function process_flashcard_deckstate($data) {
        global $DB;

        $data = (object)$data;

        $oldid = $data->id;
        unset($data->id);

        $data->flashcardid = $this->get_new_parentid('flashcard');
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newid = $DB->insert_record('flashcard_userdeck_state', $data);
    }

    protected function after_execute() {
        $this->add_related_files('mod_flashcard', 'intro', null);
        $this->add_related_files('mod_flashcard', 'customfront', null);
        $this->add_related_files('mod_flashcard', 'customempty', null);
        $this->add_related_files('mod_flashcard', 'customback', null);
        $this->add_related_files('mod_flashcard', 'customreview', null);
        $this->add_related_files('mod_flashcard', 'customreviewed', null);
        $this->add_related_files('mod_flashcard', 'customreviewempty', null);
    }
}
