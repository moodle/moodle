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
 * Display usage information about themes.
 *
 * @package    report_themeusage
 * @copyright  2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_reportbuilder\system_report_factory;
use report_themeusage\form\theme_usage_form;
use report_themeusage\reportbuilder\local\systemreports\theme_usage_report;

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');

require_login();
admin_externalpage_setup('reportthemeusage');

// Get URL parameters.
$themechoice = optional_param('themechoice', '', PARAM_TEXT);

// Check the requested theme is a valid one.
if (!theme_usage_form::validate_theme_choice_param($themechoice)) {
    throw new \moodle_exception(get_string('invalidparametertheme', 'report_themeusage'));
}

// Set up the page.
$pageurl = new moodle_url($CFG->wwwroot . '/report/themeusage/index.php');
$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('report');
$PAGE->set_primary_active_tab('siteadminnode');
echo $OUTPUT->header();

// Show heading.
$heading = get_string('themeusage', 'report_themeusage');
echo $OUTPUT->heading($heading);

// Build form with prepared data.
$cutomdata['themechoice'] = $themechoice;
$form = new theme_usage_form($pageurl, $cutomdata);
$form->display();

if ($data = $form->get_data()) {
    // Build report using submitted form data.
    $themechoice = $data->themechoice;
    $typechoice = $data->typechoice;

} else if (!empty($themechoice)) {
    // Build report with incoming theme choice and set the type to 'all'.
    $typechoice  = 'all';
}

if (!empty($themechoice) && !empty($typechoice)) {
    // Show a heading that explains what the report is showing.
    $themename = get_string('pluginname', 'theme_' . $themechoice);
    $reportheading = get_string('themeusagereport' . $typechoice, 'report_themeusage', $themename);
    echo $OUTPUT->heading($reportheading, 3, 'mt-4');

    // Build the report.
    $reportparams = ['themechoice' => $themechoice, 'typechoice' => $typechoice];
    $report = system_report_factory::create(theme_usage_report::class, context_system::instance(), '', '', 0, $reportparams);
    echo $report->output();
}

// Show footer.
echo $OUTPUT->footer();
