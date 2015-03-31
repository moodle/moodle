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

namespace core_question\bank;

/**
 * A column with a checkbox for each question with name q{questionid}.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class checkbox_column extends column_base {
    protected $strselect;

    public function init() {
        $this->strselect = get_string('select');
    }

    public function get_name() {
        return 'checkbox';
    }

    protected function get_title() {
        return '<input type="checkbox" disabled="disabled" id="qbheadercheckbox" />';
    }

    protected function get_title_tip() {
        global $PAGE;
        $PAGE->requires->strings_for_js(array('selectall', 'deselectall'), 'moodle');
        $PAGE->requires->yui_module('moodle-question-qbankmanager', 'M.question.qbankmanager.init');
        return get_string('selectquestionsforbulk', 'question');

    }

    protected function display_content($question, $rowclasses) {
        global $PAGE;
        echo '<input title="' . $this->strselect . '" type="checkbox" name="q' .
                $question->id . '" id="checkq' . $question->id . '" value="1"/>';
    }

    public function get_required_fields() {
        return array('q.id');
    }
}
