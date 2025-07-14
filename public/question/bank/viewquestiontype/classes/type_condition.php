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

use question_bank;
use core\output\datafilter;
use core_question\local\bank\condition;

/**
 * Filter condition for question type
 *
 * @package   qbank_viewquestiontype
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class type_condition extends condition {
    #[\Override]
    public static function get_condition_key() {
        return 'qtype';
    }

    #[\Override]
    public function get_title() {
        return get_string('typefilter', 'qbank_viewquestiontype');
    }

    #[\Override]
    public function get_filter_class() {
        return 'qbank_viewquestiontype/datafilter/filtertype/type';
    }

    #[\Override]
    public function allow_custom() {
        return false;
    }

    /**
     * Get the list of available joins for the filter.
     *
     * Questions can be ANY or NONE of the selected types. Since questions cannot have multiple types,
     * allowing "ALL" does not make sense here.
     *
     * @return array
     */
    public function get_join_list(): array {
        return [
            datafilter::JOINTYPE_NONE,
            datafilter::JOINTYPE_ANY,
        ];
    }

    /**
     * Build a list of the available question types.
     *
     * @return array
     */
    public function get_initial_values(): array {
        $types = question_bank::get_all_qtypes();
        $values = [];
        foreach ($types as $plugin => $type) {
            $values[] = [
                'value' => $plugin,
                'title' => get_string('pluginname', 'qtype_' . $plugin),
            ];
        }
        usort($values, fn($a, $b) => $a['title'] <=> $b['title']);
        return $values;
    }

    /**
     * Build a WHERE condition to filter the q.qtype column by the selected question types.
     *
     * @param array $filter
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function build_query_from_filter(array $filter): array {
        global $DB;

        // Remove empty string.
        $filter['values'] = array_filter($filter['values']);

        $selectedtypes = $filter['values'] ?? [];

        $params = [];
        $where = '';
        $jointype = $filter['jointype'] ?? self::JOINTYPE_DEFAULT;
        if ($selectedtypes) {
            // If we are matching NONE rather than ANY, exclude the selected types instead.
            $equal = !($jointype === datafilter::JOINTYPE_NONE);
            [$typesql, $params] = $DB->get_in_or_equal($selectedtypes, SQL_PARAMS_NAMED, 'param', $equal);
            $where = "q.qtype $typesql";
        }
        return [$where, $params];
    }
}
