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

use core_question\local\bank\column_base;


/**
 * A column type for the name of the question name.
 *
 * @package   qbank_viewquestionname
 * @copyright 2009 Tim Hunt
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class viewquestionname_column_helper extends column_base {

    /**
     * @var null $checkboxespresent
     */
    protected $checkboxespresent = null;

    public function get_name(): string {
        return 'questionname';
    }

    public function get_title(): string {
        return get_string('question');
    }

    protected function label_for($question): string {
        if (is_null($this->checkboxespresent)) {
            $this->checkboxespresent = $this->qbank->has_column('checkbox_column');
        }
        if ($this->checkboxespresent) {
            return 'checkq' . $question->id;
        }

        return '';
    }

    protected function display_content($question, $rowclasses): void {
        $labelfor = $this->label_for($question);
        if ($labelfor) {
            echo \html_writer::start_tag('label', array('for' => $labelfor));
        }
        echo format_string($question->name);
        if ($labelfor) {
            echo \html_writer::end_tag('label');
        }
    }

    public function get_required_fields(): array {
        return ['q.id', 'q.name'];
    }

    public function is_sortable() {
        return 'q.name';
    }

    public function get_default_width(): int {
        return 250;
    }
}
