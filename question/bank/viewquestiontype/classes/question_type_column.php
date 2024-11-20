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

namespace qbank_viewquestiontype;

use core_question\local\bank\column_base;

/**
 * A column with a type of question for each question with name q{questionid}.
 *
 * @package   qbank_viewquestiontype
 * @copyright 2009 Tim Hunt
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_type_column extends column_base {

    public function get_name(): string {
        return 'qtype';
    }

    public function get_title(): string {
        return get_string('qtypeveryshort', 'question');
    }

    public function get_title_tip(): string {
        return get_string('questiontype', 'question');
    }

    protected function display_content($question, $rowclasses): void {
        echo print_question_icon($question);
    }

    public function get_required_fields(): array {
        return ['q.qtype'];
    }

    public function is_sortable() {
        return 'q.qtype';
    }

    public function get_default_width(): int {
        return 45;
    }
}
