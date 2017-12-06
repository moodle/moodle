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
 * Displays some overview statistics for the site
 *
 * @package     report_overviewstats
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/report/overviewstats/locallib.php');

$courseid = optional_param('course', null, PARAM_INT);

if (is_null($courseid)) {
    // Site level reports
    admin_externalpage_setup('overviewstats', '', null, '', array('pagelayout' => 'report'));
    $charts = report_overviewstats_manager::get_site_charts();

} else {
    // Course level report
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $context = context_course::instance($course->id);

    require_login($course, false);
    require_capability('report/overviewstats:view', $context);

    $PAGE->set_url(new moodle_url('/report/overviewstats/index.php', array('course' => $course->id)));
    $PAGE->set_pagelayout('report');
    $PAGE->set_title($course->shortname.' - '.get_string('pluginname', 'report_overviewstats'));
    $PAGE->set_heading($course->fullname.' - '.get_string('pluginname', 'report_overviewstats'));

    $charts = report_overviewstats_manager::get_course_charts($course);
}

foreach ($charts as $chart) {
    $chart->inject_page_requirements($PAGE);
}

$output = $PAGE->get_renderer('report_overviewstats');

echo $output->charts($charts);
