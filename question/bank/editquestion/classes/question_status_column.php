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
 * A column to show the status of the question.
 *
 * @package    qbank_editquestion
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qbank_editquestion;

defined('MOODLE_INTERNAL') || die();

use core_question\local\bank\column_base;

/**
 * Class question_status_column.
 *
 * @package    qbank_editquestion
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_status_column extends column_base {

    public function get_name(): string {
        return 'questionstatus';
    }

    protected function get_title(): string {
        return get_string('questionstatus', 'qbank_editquestion');
    }

    protected function display_content($question, $rowclasses): void {
        global $DB;
        $version = $DB->get_record('question_versions', ['questionid' => $question->id], 'status');
        echo \html_writer::tag('a', editquestion_helper::get_question_status_string($version->status));
    }

}
