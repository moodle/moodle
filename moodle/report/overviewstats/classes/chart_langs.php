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
 * Report of the users' preferred languages
 *
 * @package     report_overviewstats
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Reports the number of users preferring a language for Moodle UI
 */
class report_overviewstats_chart_langs extends report_overviewstats_chart {

    /**
     * @return string
     */
    public function get_content() {

        $this->prepare_data();

        $title = get_string('chart-langs', 'report_overviewstats');
        $info = html_writer::div(get_string('chart-langs-info', 'report_overviewstats', count($this->data)), 'chartinfo');
        $chart = html_writer::tag('div', '', array(
            'id' => 'chart_langs',
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
            'M.report_overviewstats.charts.langs.init',
            array($this->data)
        );
    }

    /**
     * Prepares data to report
     */
    protected function prepare_data() {
        global $DB;

        if (!is_null($this->data)) {
            return;
        }

        $sql = "SELECT lang, COUNT(*)
                  FROM {user}
                 WHERE deleted = 0 AND confirmed = 1
              GROUP BY lang
              ORDER BY COUNT(*) DESC";

        $data = array();
        foreach ($DB->get_records_sql_menu($sql) as $lang => $count) {
            if (get_string_manager()->translation_exists($lang)) {
                $langname = get_string_manager()->get_string('thislanguageint', 'core_langconfig', null, $lang);
            } else {
                $langname = $lang;
            }
            $data[] = array(
                'language' => $langname,
                'count' => $count
            );
        }

        $this->data = $data;
    }
}
