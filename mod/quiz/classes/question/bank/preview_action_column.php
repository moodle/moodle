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
 * A column type for the preview question action.
 *
 * @package    mod_quiz
 * @category   question
 * @copyright  2023 Catalyst IT Europe Ltd.
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class preview_action_column extends \core_question\local\bank\column_base {

    public function get_extra_classes(): array {
        return ['iconcol'];
    }

    public function get_title(): string {
        return '&#160;';
    }

    public function get_name() {
        return 'previewquestionaction';
    }

    protected function display_content($question, $rowclasses) {
        global $PAGE;
        if (!question_has_capability_on($question, 'use')) {
            return;
        }
        $editrenderer = $PAGE->get_renderer('quiz', 'edit');
        echo $editrenderer->question_preview_icon($this->qbank->get_quiz(), $question);
    }
}
