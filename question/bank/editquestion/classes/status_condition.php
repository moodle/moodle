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

namespace qbank_editquestion;

use core_question\local\bank\condition;
use core_question\local\bank\question_version_status;

/**
 * Filter condition for the status column
 *
 * @package   qbank_editquestion
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class status_condition extends condition {
    #[\Override]
    public function get_title() {
        return get_string('filter:status', 'qbank_editquestion');
    }

    #[\Override]
    public static function get_condition_key() {
        return 'status';
    }

    #[\Override]
    public function get_filter_class() {
        return 'qbank_editquestion/datafilter/filtertypes/status';
    }

    /**
     * Return a single join type, we don't want a join type selector for this condition.
     *
     * @return array
     */
    public function get_join_list(): array {
        return [
            self::JOINTYPE_DEFAULT,
        ];
    }

    /**
     * Return an array mapping the values returned from the filter to the values required for the query.
     *
     * @return array
     */
    protected static function get_status_list() {
        return [
            0 => question_version_status::QUESTION_STATUS_READY,
            1 => question_version_status::QUESTION_STATUS_DRAFT,
        ];
    }

    /**
     * Return an SQL condition to filter qv.status on the selected status.
     *
     * @param array $filter
     * @return array
     */
    public static function build_query_from_filter(array $filter): array {
        if (!isset($filter['values'][0])) {
            return ['', []];
        }
        $statuses = self::get_status_list();
        if (!array_key_exists($filter['values'][0], $statuses)) {
            throw new \moodle_exception('filter:invalidstatus', 'qbank_editquestion', '', $filter['values'][0]);
        }
        return [
            'qv.status = :status',
            ['status' => $statuses[$filter['values'][0]]],
        ];
    }
}
