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
 * A class to display a table with users either with attempts or without them.
 *
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local\report\users_attempts;

use coding_exception;
use context;
use dml_exception;
use html_writer;
use mod_adaptivequiz\local\report\questions_difficulty_range;
use mod_adaptivequiz\local\report\users_attempts\filter\filter;
use mod_adaptivequiz\local\report\users_attempts\sql\sql_resolver;
use mod_adaptivequiz_renderer;
use moodle_exception;
use moodle_url;
use stdClass;
use table_sql;

final class users_attempts_table extends table_sql {

    private const UNIQUE_ID = 'usersattemptstable';

    /**
     * @var mod_adaptivequiz_renderer $renderer
     */
    private $renderer;

    /**
     * @var int $cmid
     */
    private $cmid;

    /**
     * @var questions_difficulty_range $questionsdifficultyrange
     */
    private $questionsdifficultyrange;

    /**
     * @throws coding_exception
     */
    public function __construct(
        mod_adaptivequiz_renderer $renderer,
        int $cmid,
        questions_difficulty_range $questionsdifficultyrange,
        moodle_url $baseurl,
        context $context,
        filter $filter
    ) {
        parent::__construct(self::UNIQUE_ID);

        $this->renderer = $renderer;
        $this->cmid = $cmid;
        $this->questionsdifficultyrange = $questionsdifficultyrange;

        $this->init($baseurl, $context, $filter);
    }

    /**
     * {@inheritdoc}
     * @throws dml_exception
     */
    public function query_db($pagesize, $useinitialsbar = true): void {
        global $DB;

        if (!$this->is_downloading()) {
            if ($this->countsql === null) {
                $this->countsql = 'SELECT COUNT(1) FROM '.$this->sql->from.' WHERE '.$this->sql->where;
                $this->countparams = $this->sql->params;
            }
            $grandtotal = $DB->count_records_sql($this->countsql, $this->countparams);
            if ($useinitialsbar && !$this->is_downloading()) {
                $this->initialbars(true);
            }

            list($wsql, $wparams) = $this->get_sql_where();
            if ($wsql) {
                $this->countsql .= ' AND ' . $wsql;
                $this->countparams = array_merge($this->countparams, $wparams);

                $this->sql->where .= ' AND ' . $wsql;
                $this->sql->params = array_merge($this->sql->params, $wparams);

                $total  = $DB->count_records_sql($this->countsql, $this->countparams);
            } else {
                $total = $grandtotal;
            }

            $this->pagesize($pagesize, $total);
        }

        $sort = $this->get_sql_sort();
        if ($sort) {
            $sort = "ORDER BY $sort";
        }

        $groupby = $this->sql->groupby ?? '';
        if ($groupby) {
            $groupby = "GROUP BY $groupby";
        }

        $sql = "SELECT {$this->sql->fields} FROM {$this->sql->from} WHERE {$this->sql->where} {$groupby} {$sort}";

        if (!$this->is_downloading()) {
            $this->rawdata = $DB->get_records_sql($sql, $this->sql->params, $this->get_page_start(),
                $this->get_page_size());
        } else {
            $this->rawdata = $DB->get_records_sql($sql, $this->sql->params);
        }
    }

    protected function col_attemptsnum(stdClass $row): string {
        if (!$row->attemptsnum) {
            return '-';
        }

        if (!$this->is_downloading()) {
            return html_writer::link(
                new moodle_url(
                    '/mod/adaptivequiz/viewattemptreport.php',
                    ['userid' => $row->id, 'cmid' => $this->cmid]
                ),
                $row->attemptsnum
            );
        }

        return $row->attemptsnum;
    }

    /**
     * @throws moodle_exception
     */
    protected function col_measure(stdClass $row): string {
        $formatmeasureparams = new stdClass();
        $formatmeasureparams->measure = $row->measure;
        $formatmeasureparams->highestlevel = $this->questionsdifficultyrange->highest_level();
        $formatmeasureparams->lowestlevel = $this->questionsdifficultyrange->lowest_level();

        $measure = $this->renderer->format_measure($formatmeasureparams);
        if (!$row->attemptid) {
            return $measure;
        }

        if (!$this->is_downloading()) {
            return html_writer::link(
                new moodle_url('/mod/adaptivequiz/reviewattempt.php', ['attempt' => $row->attemptid]),
                $measure
            );
        }

        return $measure;
    }

    protected function col_stderror(stdClass $row): string {
        $rendered = $this->renderer->format_standard_error($row);
        if (!$this->is_downloading()) {
            return $rendered;
        }

        return html_entity_decode($rendered, ENT_QUOTES, 'UTF-8');
    }

    /**
     * @throws coding_exception
     */
    protected function col_attempttimefinished(stdClass $row): string {
        return intval($row->attempttimefinished)
            ? userdate($row->attempttimefinished)
            : get_string('na', 'adaptivequiz');
    }

    /**
     * A convenience method to call a bunch of init methods.
     *
     * @param moodle_url $baseurl
     * @throws coding_exception
     */
    private function init(moodle_url $baseurl, context $context, filter $filter): void {
        $this->define_columns([
            'fullname', 'email', 'attemptsnum', 'measure', 'stderror', 'attempttimefinished',
        ]);
        $this->define_headers([
            get_string('fullname'),
            get_string('email'),
            get_string('numofattemptshdr', 'adaptivequiz'),
            get_string('bestscore', 'adaptivequiz'),
            get_string('bestscorestderror', 'adaptivequiz'),
            get_string('attemptfinishedtimestamp', 'adaptivequiz'),
        ]);
        $this->define_baseurl($baseurl);
        $this->set_attribute('class', $this->attributes['class'] . ' usersattemptstable');
        $this->set_content_alignment_in_columns();
        $this->collapsible(false);
        $this->sortable(true, 'lastname');
        $this->is_downloadable(true);

        $sqlandparams = sql_resolver::sql_and_params($filter, $context);
        $this->set_sql($sqlandparams->fields(), $sqlandparams->from(), $sqlandparams->where(), $sqlandparams->params());
        $this->set_group_by_sql($sqlandparams->group_by());
        $this->set_count_sql($sqlandparams->count_sql(), $sqlandparams->count_sql_params());
    }

    private function set_group_by_sql(?string $clause): void {
        $this->sql->groupby = $clause;
    }

    private function set_content_alignment_in_columns(): void {
        $this->column_class['attemptsnum'] .= ' text-center';
        $this->column_class['measure'] .= ' text-center';
        $this->column_class['stderror'] .= ' text-center';
    }
}
