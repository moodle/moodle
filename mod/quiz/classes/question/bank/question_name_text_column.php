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

namespace mod_quiz\question\bank;

/**
 * A column type for the name followed by the start of the question text.
 *
 * @package    mod_quiz
 * @category   question
 * @copyright  2009 Tim Hunt
 * @author     2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_name_text_column extends question_name_column {

    public function get_name(): string {
        return 'questionnametext';
    }

    protected function display_content($question, $rowclasses): void {
        echo \html_writer::start_tag('div');
        $labelfor = $this->label_for($question);
        if ($labelfor) {
            echo \html_writer::start_tag('label', ['for' => $labelfor]);
        }
        echo quiz_question_tostring($question, false, true, true, $question->tags);
        if ($labelfor) {
            echo \html_writer::end_tag('label');
        }
        echo \html_writer::end_tag('div');
    }

    public function get_required_fields(): array {
        $fields = parent::get_required_fields();
        $fields[] = 'q.questiontext';
        $fields[] = 'q.questiontextformat';
        $fields[] = 'qbe.idnumber';
        return $fields;
    }

    public function load_additional_data(array $questions) {
        parent::load_additional_data($questions);
        parent::load_question_tags($questions);
    }
}
