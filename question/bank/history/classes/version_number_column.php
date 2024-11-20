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

namespace qbank_history;

use core_question\local\bank\column_base;

/**
 * Question bank column for the question version number.
 *
 * @package    qbank_history
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class version_number_column extends column_base {

    public function get_name(): string {
        return 'questionversionnumber';
    }

    public function get_title(): string {
        return get_string('questionversionnumber', 'qbank_history');
    }

    protected function display_content($question, $rowclasses): void {
        print_string('questionversiondata', 'qbank_history', $question->version);
    }

    public function get_extra_classes(): array {
        return ['pe-3'];
    }

}
