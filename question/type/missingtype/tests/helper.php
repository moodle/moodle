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
 * Test helpers for the missingtype question type.
 *
 * @package    qtype_missingtype
 * @copyright  2025 onwards Catalyst IT {@link http://www.catalyst-eu.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Conn Warwicker <conn.warwicker@catalyst-eu.net>
 */
class qtype_missingtype_test_helper extends question_test_helper {

    #[\Override]
    public function get_test_questions(): array {
        return ['invalid'];
    }

    /**
     * Gets the question form data for the invalid question.
     *
     * @return stdClass
     */
    public function get_missingtype_question_form_data_invalid(): stdClass {

        $form = new stdClass();
        $form->name = 'Invalid question';
        $form->questiontext = ['text' => 'You will never see this', 'format' => FORMAT_HTML];
        $form->defaultmark = 1.0;
        $form->generalfeedback = ['text' => 'How did you even submit this?', 'format' => FORMAT_HTML];
        $form->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        return $form;

    }

}
