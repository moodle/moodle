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
 * Display AI policy acceptance report.
 *
 * @package    core_ai
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_reportbuilder\system_report_factory;
use core_ai\reportbuilder\local\systemreports\policy_acceptance;

require(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('aipolicyacceptancereport');

// Set up the page.
$systemcontext = context_system::instance();
$pageurl = new moodle_url($CFG->wwwroot . '/ai/policy_acceptance_report.php');
$PAGE->set_url($pageurl);
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('report');
$PAGE->set_primary_active_tab('siteadminnode');
echo $OUTPUT->header();

$report = system_report_factory::create(policy_acceptance::class, $systemcontext);
echo $OUTPUT->heading($report::get_name());
echo $report->output();
echo $OUTPUT->footer();
