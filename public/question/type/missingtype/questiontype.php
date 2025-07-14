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
 * Question type class for the 'missingtype' type.
 *
 * @package    qtype
 * @subpackage missingtype
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/questiontypebase.php');

/**
 * Missing question type class
 *
 * When we encounter a question of a type that is not currently installed, then
 * we use this question type class instead so that some of the information about
 * this question can be seen, and the rest of the system keeps working.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_missingtype extends question_type {
    public function menu_name() {
        return false;
    }

    public function is_usable_by_random() {
        return false;
    }

    public function can_analyse_responses() {
        return false;
    }

    public function make_question($questiondata) {
        $question = parent::make_question($questiondata);
        $question->questiontext = html_writer::tag('div',
                get_string('missingqtypewarning', 'qtype_missingtype'),
                array('class' => 'warning missingqtypewarning')) .
                $question->questiontext;
        return $question;
    }

    public function make_deleted_instance($questionid, $maxmark) {
        question_bank::load_question_definition_classes('missingtype');
        $question = new qtype_missingtype_question();
        $question->id = $questionid;
        $question->category = null;
        $question->parent = 0;
        $question->qtype = question_bank::get_qtype('missingtype');
        $question->name = get_string('deletedquestion', 'qtype_missingtype');
        $question->questiontext = get_string('deletedquestiontext', 'qtype_missingtype');
        $question->questiontextformat = FORMAT_HTML;
        $question->generalfeedback = '';
        $question->defaultmark = $maxmark;
        $question->length = 1;
        $question->penalty = 0;
        $question->stamp = '';
        $question->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $question->timecreated = null;
        $question->timemodified = null;
        $question->createdby = null;
        $question->modifiedby = null;
        return $question;
    }

    public function get_random_guess_score($questiondata) {
        return null;
    }

    public function display_question_editing_page($mform, $question, $wizardnow) {
        global $OUTPUT;
        echo $OUTPUT->heading(get_string('warningmissingtype', 'qtype_missingtype'));

        $mform->display();
    }

    #[\Override]
    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_answers($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_combined_feedback($questionid, $oldcontextid, $newcontextid);
    }

}
