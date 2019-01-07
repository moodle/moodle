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
 * Question bank columns for the preview action icon.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class preview_action_column extends action_column_base {
    public function get_name() {
        return 'previewaction';
    }

    protected function display_content($question, $rowclasses) {
        global $PAGE;
        if (question_has_capability_on($question, 'use')) {
            echo $PAGE->get_renderer('core_question')->question_preview_link(
                    $question->id, $this->qbank->get_most_specific_context(), false);
        }
    }
}
