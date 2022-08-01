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

namespace qbank_viewquestionname;

/**
 * A question bank column showing the question name with idnumber and tags.
 *
 * @package   qbank_viewquestionname
 * @copyright 2019 The Open University
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_name_idnumber_tags_column extends viewquestionname_column_helper {

    public function get_name(): string {
        return 'qnameidnumbertags';
    }

    protected function display_content($question, $rowclasses): void {
        global $OUTPUT;

        echo \html_writer::start_tag('div', ['class' => 'd-inline-flex flex-nowrap overflow-hidden w-100']);

        $questionname = format_string($question->name);
        $labelfor = $this->label_for($question);
        if ($labelfor) {
            echo \html_writer::label($questionname, $labelfor);
        } else {
            // Question name.
            echo \html_writer::span($questionname, 'questionname flex-grow-1 flex-shrink-1 text-truncate');
        }

        // Question idnumber.
        if ($question->idnumber !== null && $question->idnumber !== '') {
            echo ' ' . \html_writer::span(
                            \html_writer::span(get_string('idnumber', 'question'), 'accesshide')
                            . ' ' . \html_writer::span(s($question->idnumber), 'badge badge-primary'), 'ml-1');
        }

        // Question tags.
        if (!empty($question->tags)) {
            $tags = \core_tag_tag::get_item_tags('core_question', 'question', $question->id);
            echo $OUTPUT->tag_list($tags, null, 'd-inline flex-shrink-1 text-truncate ml-1', 0, null, true);
        }

        echo \html_writer::end_tag('div');
    }

    public function get_required_fields(): array {
        $fields = parent::get_required_fields();
        $fields[] = 'qbe.idnumber';
        return $fields;
    }

    public function is_sortable(): array {
        return [
                'name' => ['field' => 'q.name', 'title' => get_string('questionname', 'question')],
                'idnumber' => ['field' => 'qbe.idnumber', 'title' => get_string('idnumber', 'question')],
        ];
    }

    public function load_additional_data(array $questions): void {
        parent::load_additional_data($questions);
        parent::load_question_tags($questions);
    }

    public function get_extra_classes(): array {
        return ['pr-3'];
    }

}
