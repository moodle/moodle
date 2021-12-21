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
 * List of custom reports
 *
 * @package   core_reportbuilder
 * @copyright 2021 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

use core_reportbuilder\permission;
use core_reportbuilder\system_report_factory;
use core_reportbuilder\local\systemreports\reports_list;

require_once(__DIR__ . '/../config.php');
require_once("{$CFG->libdir}/adminlib.php");

admin_externalpage_setup('customreports');

$PAGE->requires->js_call_amd('core_reportbuilder/reports_list', 'init');

echo $OUTPUT->header();
echo html_writer::start_div('d-flex justify-content-between mb-2');
echo $OUTPUT->heading(get_string('customreports', 'core_reportbuilder'));

if (permission::can_create_report()) {
    /** @var \core_reportbuilder\output\renderer $renderer */
    $renderer = $PAGE->get_renderer('core_reportbuilder');
    echo $renderer->render_new_report_button();
}

echo html_writer::end_div();

$report = system_report_factory::create(reports_list::class, context_system::instance());
echo $report->output();

echo $OUTPUT->footer();
