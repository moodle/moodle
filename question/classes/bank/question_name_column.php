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
 * A column type for the name of the question name.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_name_column extends column_base {
    protected $checkboxespresent = null;

    public function get_name() {
        return 'questionname';
    }

    protected function get_title() {
        return get_string('question');
    }

    protected function label_for($question) {
        if (is_null($this->checkboxespresent)) {
            $this->checkboxespresent = $this->qbank->has_column('core_question\bank\checkbox_column');
        }
        if ($this->checkboxespresent) {
            return 'checkq' . $question->id;
        } else {
            return '';
        }
    }

    protected function display_content($question, $rowclasses) {
        $labelfor = $this->label_for($question);
        if ($labelfor) {
            echo '<label for="' . $labelfor . '">';
        }
        echo format_string($question->name);
        if ($labelfor) {
            echo '</label>';
        }
    }

    public function get_required_fields() {
        return array('q.id', 'q.name');
    }

    public function is_sortable() {
        return 'q.name';
    }
}
