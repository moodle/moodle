<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace qbank_usage\tables;

global $CFG;
require_once($CFG->libdir.'/tablelib.php');

use context_course;
use html_writer;
use moodle_url;
use qbank_usage\helper;
use table_sql;

/**
 * Class question_usage_table.
 * An extension of regular Moodle table.
 *
 * @package    qbank_usage
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_usage_table extends table_sql {

    /**
     * Search string.
     *
     * @var string $search
     */
    public $search = '';

    /**
     * Question id.
     *
     * @var \question_definition $question
     */
    public $question;

    /**
     * constructor.
     * Sets the SQL for the table and the pagination.
     *
     * @param string $uniqueid
     * @param \question_definition $question
     */
    public function __construct(string $uniqueid, \question_definition $question) {
        global $PAGE;
        parent::__construct($uniqueid);
        $this->question = $question;
        $columns = ['modulename', 'coursename', 'attempts'];
        $headers = [
            get_string('modulename', 'qbank_usage'),
            get_string('coursename', 'qbank_usage'),
            get_string('attempts', 'qbank_usage')
        ];
        $this->is_collapsible = false;
        $this->no_sorting('modulename');
        $this->no_sorting('coursename');
        $this->no_sorting('attempts');
        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->define_baseurl($PAGE->url);
    }

    public function query_db($pagesize, $useinitialsbar = true) {
        global $DB;
        if (!$this->is_downloading()) {
            $total = helper::get_question_entry_usage_count($this->question);
            $this->pagesize($pagesize, $total);
        }

        $sql = helper::question_usage_sql();
        $params = [$this->question->id, $this->question->questionbankentryid, 'mod_quiz', 'slot'];

        if (!$this->is_downloading()) {
            $this->rawdata = $DB->get_records_sql($sql, $params, $this->get_page_start(), $this->get_page_size());
        } else {
            $this->rawdata = $DB->get_records_sql($sql, $params);
        }
    }

    public function col_modulename(\stdClass $values): string {
        $cm = get_fast_modinfo($values->courseid)->instances['quiz'][$values->quizid];

        return html_writer::link(new moodle_url('/mod/quiz/view.php', ['q' => $values->quizid]), $cm->get_formatted_name());
    }

    public function col_coursename(\stdClass $values): string {
        $course = get_course($values->courseid);
        $context = context_course::instance($course->id);

        return html_writer::link(course_get_url($course), format_string($course->fullname, true, [
            'context' => $context,
        ]));
    }

    public function col_attempts(\stdClass $values): string {
        return helper::get_question_attempts_count_in_quiz($this->question->id, $values->quizid);
    }

    /**
     * Export this data so it can be used as the context for a mustache template/fragment.
     *
     * @return string
     */
    public function export_for_fragment(): string {
        ob_start();
        $this->out(10, true);
        return ob_get_clean();
    }

}
