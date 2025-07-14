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

namespace qbank_deletequestion;

use core\output\datafilter;
use core_question\local\bank\condition;

use core_question\local\bank\question_version_status;

/**
 * This class controls whether hidden / deleted questions are hidden in the list.
 *
 * @package    qbank_deletequestion
 * @copyright  2013 Ray Morris
 * @author     2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hidden_condition extends condition {

    public static function get_condition_key() {
        return 'hidden';
    }

    /**
     * Build query from filter value
     *
     * @param array $filter filter properties
     * @return array where sql and params
     */
    public static function build_query_from_filter(array $filter): array {
        $showhidden = (bool)$filter['values'][0];
        $where = "";
        $params = [];
        if (!$showhidden) {
            $where = "qv.status <> :hidden_condition";
            $params = ['hidden_condition' => question_version_status::QUESTION_STATUS_HIDDEN];
        }
        return [$where, $params];
    }

    public function get_title() {
        return get_string('showhidden', 'core_question');
    }

    public function get_join_list(): array {
        return [
            datafilter::JOINTYPE_ANY,
        ];
    }

    public function get_filter_class() {
        return 'qbank_deletequestion/datafilter/filtertypes/hidden';
    }

    public function is_required(): bool {
        return true;
    }
}
