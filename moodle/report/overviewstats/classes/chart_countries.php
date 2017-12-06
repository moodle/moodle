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
 * @package     report_overviewstats
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Reports the number of users from each country
 */
class report_overviewstats_chart_countries extends report_overviewstats_chart {

    /**
     * @return array
     */
    public function get_content() {

        $this->prepare_data();

        $title = get_string('chart-countries', 'report_overviewstats');
        $info = html_writer::div(get_string('chart-countries-info', 'report_overviewstats', count($this->data)), 'chartinfo');
        $chart = html_writer::tag('div', '', array(
            'id' => 'chart_countries',
            'class' => 'chartplaceholder',
            'style' => 'min-height: '.max(66, (count($this->data) * 20)).'px;'
        ));

        return array($title => $info . $chart);
    }

    /**
     * @see parent
     */
    public function inject_page_requirements(moodle_page $page) {

        $this->prepare_data();

        $page->requires->yui_module(
            'moodle-report_overviewstats-charts',
            'M.report_overviewstats.charts.countries.init',
            array($this->data)
        );
    }

    /**
     * Prepares data to report.
     */
    protected function prepare_data() {
        global $DB;

        if (!is_null($this->data)) {
            return;
        }

        $sql = "SELECT country, COUNT(*)
                  FROM {user}
                 WHERE country IS NOT NULL AND country <> '' AND deleted = 0 AND confirmed = 1
              GROUP BY country
              ORDER BY COUNT(*) DESC, country ASC";

        $data = array();
        foreach ($DB->get_records_sql_menu($sql) as $country => $count) {
            if (get_string_manager()->string_exists($country, 'core_countries')) {
                $countryname = get_string($country, 'core_countries');
            } else {
                $countryname = $country;
            }
            $data[] = array(
                'country' => $countryname,
                'count' => $count
            );
        }

        $this->data = $data;
    }
}
