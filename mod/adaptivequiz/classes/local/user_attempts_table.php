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
 * A class to display a table with user's own attempts on the activity's view page.
 *
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local;

use coding_exception;
use help_icon;
use mod_adaptivequiz\local\attempt\attempt_state;
use mod_adaptivequiz_renderer;
use moodle_url;
use stdClass;
use table_sql;

final class user_attempts_table extends table_sql {
    /**
     * @var mod_adaptivequiz_renderer $renderer
     */
    private $renderer;

    public function __construct(mod_adaptivequiz_renderer $renderer) {
        parent::__construct('userattemptstable');

        $this->renderer = $renderer;
    }

    /**
     * A convenience function to call a bunch of init methods.
     *
     * @param moodle_url $baseurl
     * @param stdClass $adaptivequiz A record form {adaptivequiz}. id, lowestlevel, highestlevel, showabilitymeasure are
     * the expected fields.
     * @param int $userid
     * @throws coding_exception
     */
    public function init(moodle_url $baseurl, stdClass $adaptivequiz, int $userid): void {
        $columns = ['state', 'timefinished'];
        if ($adaptivequiz->showabilitymeasure) {
            $columns[] = 'measure';
        }
        $this->define_columns($columns);

        $headers = [
            get_string('attempt_state', 'adaptivequiz'),
            get_string('attemptfinishedtimestamp', 'adaptivequiz'),
        ];
        if ($adaptivequiz->showabilitymeasure) {
            $headers[] = get_string('abilityestimated', 'adaptivequiz') . ' / ' .
                $adaptivequiz->lowestlevel . ' - ' . $adaptivequiz->highestlevel;
        }
        $this->define_headers($headers);

        $this->set_attribute('class', 'generaltable userattemptstable');
        $this->is_downloadable(false);
        $this->collapsible(false);
        $this->sortable(false, 'timefinished', SORT_DESC);
        $this->define_help_for_headers(
            [2 => new help_icon('abilityestimated', 'adaptivequiz')]
        );
        $this->set_column_css_classes();
        $this->set_content_alignment_in_columns();
        $this->define_baseurl($baseurl);
        $this->set_sql('a.id, a.attemptstate AS state, a.timemodified AS timefinished, a.measure, q.highestlevel, ' .
            'q.lowestlevel', '{adaptivequiz_attempt} a, {adaptivequiz} q', 'a.instance = q.id AND q.id = ? ' .
            'AND userid = ?', [$adaptivequiz->id, $userid]);
    }

    /**
     * @throws coding_exception
     */
    protected function col_state(stdClass $row): string {
        return get_string('recent' . $row->state, 'adaptivequiz');
    }

    protected function col_timefinished(stdClass $row): string {
        if ($row->state != attempt_state::COMPLETED) {
            return '';
        }

        return userdate($row->timefinished);
    }

    protected function col_measure(stdClass $row): string {
        return $this->renderer->format_measure($row);
    }

    private function set_content_alignment_in_columns(): void {
        foreach (array_keys($this->columns) as $columnname) {
            $this->column_class[$columnname] .= ' text-center';
        }
    }

    private function set_column_css_classes(): void {
        $this->column_class['state'] .= ' statecol';

        if (array_key_exists('measure', $this->columns)) {
            $this->column_class['measure'] .= ' abilitymeasurecol';
        }
    }
}
