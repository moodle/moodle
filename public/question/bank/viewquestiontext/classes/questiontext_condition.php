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

namespace qbank_viewquestiontext;

use core\output\datafilter;
use core_question\local\bank\condition;

/**
 * Filter for question text and question feedback text.
 *
 * @package    qbank_viewquestiontext
 * @copyright  2025 onwards Catalyst IT {@link http://www.catalyst-eu.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Conn Warwicker <conn.warwicker@catalyst-eu.net>
 */
class questiontext_condition extends condition {

    #[\Override]
    public function get_title(): string {
        return get_string('questiontext_condition', 'qbank_viewquestiontext');
    }

    #[\Override]
    public static function get_condition_key(): string {
        return 'questiontext';
    }

    #[\Override]
    public function get_filter_class() {
        return 'core/datafilter/filtertypes/keyword';
    }

    #[\Override]
    public static function build_query_from_filter(array $filter): array {

        global $DB;

        // Are we inverting the search to look for where NOT like?
        $notlike = ($filter['jointype'] === datafilter::JOINTYPE_NONE);

        [$params, $conditions] = [[], []];

        // Loop through the values we've sent from the filter.
        foreach ($filter['values'] as $key => $value) {

            $params['questiontext_' . $key] = '%' . $value . '%';
            $params['questiontext_f_' . $key] = '%' . $value . '%';

            $condition = $DB->sql_like(
                'q.questiontext', ':questiontext_' . $key, false, true, $notlike
            );
            $condition .= ($notlike) ? ' AND ' : ' OR ';
            $condition .= $DB->sql_like(
                'q.generalfeedback', ':questiontext_f_' . $key, false, true, $notlike
            );
            $conditions[] = '(' . $condition . ')';

        }

        $delimiter = ($filter['jointype'] === datafilter::JOINTYPE_ANY) ? ' OR ' : ' AND ';
        return [
            implode($delimiter, $conditions),
            $params,
        ];

    }
}
