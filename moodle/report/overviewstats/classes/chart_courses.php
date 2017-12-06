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

require_once($CFG->libdir.'/coursecatlib.php');

/**
 * Reports various users related charts and figures
 */
class report_overviewstats_chart_courses extends report_overviewstats_chart {

    /**
     * @return array
     */
    public function get_content() {
        global $OUTPUT;

        $this->prepare_data();

        $title = get_string('chart-courses', 'report_overviewstats');
        $titlepercategory = get_string('chart-courses-percategory', 'report_overviewstats');

        $percategorydata = new html_table();
        $percategorydata->head = array(
            get_string('chart-courses-percategory-categoryname', 'report_overviewstats'),
            get_string('chart-courses-percategory-coursesrecursive', 'report_overviewstats'),
            get_string('chart-courses-percategory-coursesown', 'report_overviewstats'),
        );
        foreach ($this->data['percategory'] as $catdata) {
            $percategorydata->data[] = new html_table_row(array(
                $catdata['categoryname'],
                $catdata['coursesrecursive'],
                $catdata['coursesown'],
            ));
        }

        $titlesizes = sprintf('%s %s', get_string('chart-courses-sizes', 'report_overviewstats'),
            $OUTPUT->help_icon('chart-courses-sizes', 'report_overviewstats'));

        return array($title => array(
            $titlepercategory => html_writer::tag('div',
                html_writer::table($percategorydata),
                array(
                    'id' => 'chart_courses_percategory',
                    'class' => 'simple_data_table',
                )
            ),
            $titlesizes => html_writer::tag('div', '', array(
                'id' => 'chart_courses_sizes',
                'class' => 'chartplaceholder',
                'style' => 'min-height: 300px;',
            )),
        ));
    }

    /**
     * @see parent
     */
    public function inject_page_requirements(moodle_page $page) {

        $this->prepare_data();

        $page->requires->yui_module(
            'moodle-report_overviewstats-charts',
            'M.report_overviewstats.charts.courses.init',
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

        // Number of courses per category

        $cats = coursecat::make_categories_list();
        $this->data['percategory'] = array();
        $total = 0;

        foreach ($cats as $catid => $catname) {
            $cat = coursecat::get($catid);
            $coursesown = $cat->get_courses_count();
            $total += $coursesown;
            $this->data['percategory'][] = array(
                'categoryname' => $catname,
                'coursesrecursive' => $cat->get_courses_count(array('recursive' => true)),
                'coursesown' => $coursesown,
            );
        }

        $this->data['percategory'][] = array(
            'categoryname' => html_writer::tag('strong', get_string('total')),
            'coursesrecursive' => '',
            'coursesown' => html_writer::tag('strong', $total),
        );

        // Distribution graph of number of activities per course

        $sql = "SELECT course, COUNT(id) AS modules
                  FROM {course_modules}
              GROUP BY course";

        $rs = $DB->get_recordset_sql($sql);

        $max = 0;
        $data = array();
        $this->data['sizes'] = array();

        foreach ($rs as $record) {
            $distributiongroup = floor($record->modules / 5); // 0 for 0-4, 1 for 5-9, 2 for 10-14 etc.
            if (!isset($data[$distributiongroup])) {
                $data[$distributiongroup] = 1;
            } else {
                $data[$distributiongroup]++;
            }
            if ($distributiongroup > $max) {
                $max = $distributiongroup;
            }
        }

        $rs->close();

        for ($i = 0; $i <= $max; $i++) {
            if (!isset($data[$i])) {
                $data[$i] = 0;
            }
        }
        ksort($data);

        foreach ($data as $distributiongroup => $courses) {
            $distributiongroupname = sprintf("%d-%d", $distributiongroup * 5, $distributiongroup * 5 + 4);
            $this->data['sizes'][] = array(
                'course_size' => $distributiongroupname,
                'courses' => $courses,
            );
        }
    }
}
