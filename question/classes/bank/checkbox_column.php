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
        global $PAGE;

        $PAGE->requires->js_call_amd('core/checkbox-toggleall', 'init');
    }

    public function get_name() {
        return 'checkbox';
    }

    protected function get_title() {
        $input = \html_writer::empty_tag('input', [
            'id' => 'qbheadercheckbox',
            'name' => 'qbheadercheckbox',
            'type' => 'checkbox',
            'value' => '1',
            'data-action' => 'toggle',
            'data-toggle' => 'master',
            'data-togglegroup' => 'qbank',
            'data-toggle-selectall' => get_string('selectall', 'moodle'),
            'data-toggle-deselectall' => get_string('deselectall', 'moodle'),
        ]);

        $label = \html_writer::tag('label', get_string('selectall', 'moodle'), [
            'class' => 'accesshide',
            'for' => 'qbheadercheckbox',
        ]);

        return $input . $label;
    }

    protected function get_title_tip() {
        return get_string('selectquestionsforbulk', 'question');
    }

    protected function display_content($question, $rowclasses) {
        echo \html_writer::empty_tag('input', [
            'title' => get_string('select'),
            'type' => 'checkbox',
            'name' => "q{$question->id}",
            'id' => "checkq{$question->id}",
            'value' => '1',
            'data-action' => 'toggle',
            'data-toggle' => 'slave',
            'data-togglegroup' => 'qbank',
        ]);
    }

    public function get_required_fields() {
        return array('q.id');
    }
}
