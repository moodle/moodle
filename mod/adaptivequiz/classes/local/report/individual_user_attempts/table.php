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
 * Contains definition of the table class for the user attempts report.
 *
 * @package   mod_adaptivequiz
 * @copyright 2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local\report\individual_user_attempts;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use mod_adaptivequiz\local\attempt\attempt_state;
use mod_adaptivequiz\local\report\questions_difficulty_range;
use mod_adaptivequiz_renderer;
use moodle_url;
use stdClass;
use table_sql;

/**
 * Definition of the table class for the user attempts report.
 *
 * @package mod_adaptivequiz
 */
final class table extends table_sql {

    /**
     * @var mod_adaptivequiz_renderer $renderer
     */
    private $renderer;

    /**
     * @var filter $filter
     */
    private $filter;

    /**
     * @var questions_difficulty_range $questionsdifficultyrange
     */
    private $questionsdifficultyrange;

    /**
     * @var int $cmid
     */
    private $cmid;

    public function __construct(
        mod_adaptivequiz_renderer $renderer,
        filter $filter,
        moodle_url $baseurl,
        questions_difficulty_range $questionsdifficultyrange,
        int $cmid
    ) {
        parent::__construct('individualuserattemptstable');

        $this->renderer = $renderer;
        $this->filter = $filter;
        $this->questionsdifficultyrange = $questionsdifficultyrange;
        $this->cmid = $cmid;

        $this->init($baseurl);
    }

    public function get_sql_sort() {
        return 'timemodified DESC';
    }

    protected function col_attemptstate(stdClass $row): string {
        if (0 == strcmp(attempt_state::IN_PROGRESS, $row->attemptstate)) {
            return get_string('recentinprogress', 'adaptivequiz');
        }

        return get_string('recentcomplete', 'adaptivequiz');
    }

    protected function col_score(stdClass $row): string {
        if ($row->measure === null || $row->stderror === null || $row->stderror == 0.0) {
            return 'n/a';
        }

        $formatmeasureparams = new stdClass();
        $formatmeasureparams->measure = $row->measure;
        $formatmeasureparams->highestlevel = $this->questionsdifficultyrange->highest_level();
        $formatmeasureparams->lowestlevel = $this->questionsdifficultyrange->lowest_level();

        return $this->renderer->format_measure($formatmeasureparams) .
            ' ' . $this->renderer->format_standard_error($row);
    }

    protected function col_timecreated(stdClass $row): string {
        return userdate($row->timecreated);
    }

    protected function col_timemodified(stdClass $row): string {
        return userdate($row->timemodified);
    }

    /**
     * A hook to format the output of actions column for an attempt row.
     *
     * @param stdClass $row A row from the {adaptivequiz_attempt}.
     */
    protected function col_actions(stdClass $row): string {
        return $this->renderer->individual_user_attempt_actions($row);
    }

    private function init(moodle_url $baseurl): void {
        $this->define_columns(['attemptstate', 'attemptstopcriteria', 'questionsattempted', 'score',
            'timecreated', 'timemodified', 'actions']);
        $this->define_headers([
            get_string('attemptstate', 'adaptivequiz'),
            get_string('attemptstopcriteria', 'adaptivequiz'),
            get_string('questionsattempted', 'adaptivequiz'),
            get_string('score', 'adaptivequiz'),
            get_string('attemptstarttime', 'adaptivequiz'),
            get_string('attemptfinishedtimestamp', 'adaptivequiz'),
            '',
        ]);
        $this->set_content_alignment_in_columns();
        $this->define_baseurl($baseurl);
        $this->set_attribute('class', $this->attributes['class'] . ' ' . $this->uniqueid);
        $this->is_downloadable(false);
        $this->collapsible(false);
        $this->sortable(false);

        $this->set_sql(
            'id, userid, uniqueid, attemptstopcriteria, measure, attemptstate, questionsattempted,timemodified,
            standarderror AS stderror, timecreated',
            '{adaptivequiz_attempt}',
            'instance = :adaptivequiz AND userid = :userid',
            ['adaptivequiz' => $this->filter->adaptivequizid, 'userid' => $this->filter->userid]
        );
    }

    private function set_content_alignment_in_columns(): void {
        foreach (array_keys($this->columns) as $column) {
            $this->column_class[$column] .= ' text-center';
        }
    }
}
