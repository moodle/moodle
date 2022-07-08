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
 * @package   local_report_companies
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');

use local_report_companies\companyrep;

// Check permissions.
require_login();
$context = context_system::instance();
iomad::require_capability('local/report_companies:view', $context);
$companyid = optional_param('companyid', 0, PARAM_INT);

// Url stuff.
$url = new moodle_url('/local/report_companies/index.php', ['companyid' => $companyid]);

// Page stuff:.
$strcompletion = get_string('pluginname', 'local_report_companies');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->requires->css("/local/report_companies/styles.css");

// Set the page heading.
$PAGE->set_heading($strcompletion);

// Renderer
$output = $PAGE->get_renderer('local_report_companies');

// Navigation and header.
echo $OUTPUT->header();

// Ajax odds and sods.
$PAGE->requires->js_init_call('M.local_report_companies.init');

// Get the company list.
$companies = companyrep::companylist($USER, $companyid);
companyrep::addmanagers($companies) ;
companyrep::addusers($companies);
companyrep::addcourses($companies);

// Render report
$main = new local_report_companies\output\main($companies);
echo $output->render_main($main);

echo $OUTPUT->footer();
