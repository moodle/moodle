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
 * A question bank column showing the question name with idnumber and tags.
 *
 * @package   core_question
 * @copyright 2019 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\bank;
defined('MOODLE_INTERNAL') || die();


/**
 * A question bank column showing the question name with idnumber and tags.
 *
 * @copyright 2019 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_name_idnumber_tags_column extends question_name_column {
    public function get_name() {
        return 'qnameidnumbertags';
    }

    protected function display_content($question, $rowclasses) {
        global $OUTPUT;

        $layoutclasses = 'd-inline-flex flex-nowrap overflow-hidden w-100';
        $labelfor = $this->label_for($question);
        if ($labelfor) {
            echo '<label for="' . $labelfor . '" class="' . $layoutclasses . '">';
            $closetag = '</label>';
        } else {
            echo '<span class="' . $layoutclasses . '">';
            $closetag = '</span>';
        }

        // Question name.
        echo \html_writer::span(format_string($question->name), 'questionname flex-grow-1 flex-shrink-1 text-truncate');

        // Question idnumber.
        if ($question->idnumber !== null && $question->idnumber !== '') {
            echo ' ' . \html_writer::span(
                            \html_writer::span(get_string('idnumber', 'question'), 'accesshide') . ' ' .
                            \html_writer::span(s($question->idnumber), 'badge badge-primary'), 'ml-1');
        }

        // Question tags.
        if (!empty($question->tags)) {
            $tags = \core_tag_tag::get_item_tags('core_question', 'question', $question->id);
            echo $OUTPUT->tag_list($tags, null, 'd-inline flex-shrink-1 text-truncate ml-1', 0, null, true);
        }

        echo $closetag; // Computed above to ensure it matches.
    }

    public function get_required_fields() {
        $fields = parent::get_required_fields();
        $fields[] = 'q.idnumber';
        return $fields;
    }

    public function is_sortable() {
        return [
            'name' => ['field' => 'q.name', 'title' => get_string('questionname', 'question')],
            'idnumber' => ['field' => 'q.idnumber', 'title' => get_string('idnumber', 'question')],
        ];
    }

    public function load_additional_data(array $questions) {
        parent::load_additional_data($questions);
        parent::load_question_tags($questions);
    }
}
