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
use core_reportbuilder\output\report_action;

require_once(__DIR__ . '/../config.php');
require_once("{$CFG->libdir}/adminlib.php");

admin_externalpage_setup('customreports');

$PAGE->requires->js_call_amd('core_reportbuilder/reports_list', 'init');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('customreports', 'core_reportbuilder'));

$report = system_report_factory::create(reports_list::class, context_system::instance());
if (permission::can_create_report()) {
    $report->set_report_action(new report_action(
        get_string('newreport', 'core_reportbuilder'),
        ['class' => 'btn btn-primary my-auto', 'data-action' => 'report-create'],
    ));
}

echo $report->output();

echo $OUTPUT->footer();
