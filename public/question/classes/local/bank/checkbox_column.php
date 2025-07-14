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
 * A column with a checkbox for each question with name q{questionid}.
 *
 * @package   core_question
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\local\bank;

use core\output\checkbox_toggleall;

/**
 * A column with a checkbox for each question with name q{questionid}.
 *
 * @copyright 2009 Tim Hunt
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class checkbox_column extends column_base {

    public function get_name(): string {
        return 'checkbox';
    }

    public function get_title() {
        global $OUTPUT;

        $togglercheckbox = new checkbox_toggleall('qbank', true, [
            'id' => 'qbheadercheckbox',
            'name' => 'qbheadercheckbox',
            'value' => '1',
            'label' => get_string('selectall'),
            'labelclasses' => 'accesshide',
        ]);

        return $OUTPUT->render($togglercheckbox);
    }

    public function get_title_tip() {
        return get_string('selectquestionsforbulk', 'question');
    }

    public function display_header(array $columnactions = [], string $width = ''): void {
        global $PAGE;
        $renderer = $PAGE->get_renderer('core_question', 'bank');

        $data = [];
        $data['sortable'] = false;
        $data['extraclasses'] = $this->get_classes();
        $name = get_class($this);
        $data['sorttip'] = true;
        $data['tiptitle'] = $this->get_title();
        $data['tip'] = $this->get_title_tip();

        $data['colname'] = $this->get_column_name();
        $data['columnid'] = $this->get_column_id();
        $data['name'] = get_string('selectall');
        $data['class'] = $name;
        $data['width'] = $width;

        echo $renderer->render_column_header($data);
    }

    protected function display_content($question, $rowclasses): void {
        global $OUTPUT;

        $checkbox = new checkbox_toggleall('qbank', false, [
            'id' => "checkq{$question->id}",
            'name' => "q{$question->id}",
            'value' => '1',
            'label' => get_string('select'),
            'labelclasses' => 'accesshide',
        ]);

        echo $OUTPUT->render($checkbox);
    }

    public function get_required_fields(): array {
        return ['q.id'];
    }

    public function get_default_width(): int {
        return 30;
    }
}
