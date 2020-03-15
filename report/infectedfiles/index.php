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
 * Infected file report
 *
 * @package    report_infectedfiles
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('reportinfectedfiles', '', null, '', array('pagelayout' => 'report'));
$page = optional_param('page', 0, PARAM_INT);
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('infectedfiles', 'report_infectedfiles'));
$table = new \report_infectedfiles\output\infectedfiles_table('report-infectedfiles-report-table', $PAGE->url, $page);
$table->define_baseurl($PAGE->url);
echo $PAGE->get_renderer('report_infectedfiles')->render($table);
echo $OUTPUT->footer();
