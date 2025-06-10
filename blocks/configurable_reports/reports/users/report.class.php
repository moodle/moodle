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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class report_users
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class report_users extends report_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->components = [
            'columns',
            'conditions',
            'ordering',
            'filters',
            'template',
            'permissions',
            'calcs',
            'plot',
        ];
    }

    /**
     * get_all_elements
     *
     * @return array
     */
    public function get_all_elements(): array {
        global $DB;

        $elements = [];
        $rs = $DB->get_recordset('user', null, '', 'id');
        foreach ($rs as $result) {
            $elements[] = $result->id;
        }
        $rs->close();

        return $elements;
    }

    /**
     * Get rows
     *
     * @param array $elements
     * @param string $sqlorder
     * @return array
     */
    public function get_rows(array $elements, string $sqlorder = ''): array {
        global $DB;

        if (!empty($elements)) {
            [$usql, $params] = $DB->get_in_or_equal($elements);

            return $DB->get_records_select('user', "id $usql", $params, $sqlorder);
        }

        return [];
    }

}
