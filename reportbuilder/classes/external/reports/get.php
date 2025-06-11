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

declare(strict_types=1);

namespace core_reportbuilder\external\reports;

use core_external\external_api;
use core_external\external_value;
use core_external\external_single_structure;
use core_external\external_function_parameters;
use core_reportbuilder\manager;
use core_reportbuilder\permission;
use core_reportbuilder\output\custom_report;
use core_reportbuilder\external\custom_report_exporter;
use moodle_url;

/**
 * External method for getting a custom report
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get extends external_api {

    /**
     * External method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'reportid' => new external_value(PARAM_INT, 'Report ID'),
            'editmode' => new external_value(PARAM_BOOL, 'Whether editing mode is enabled', VALUE_DEFAULT, 0),
            'pagesize' => new external_value(PARAM_INT, 'Page size', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * External method execution
     *
     * @param int $reportid
     * @param bool $editmode
     * @param int $pagesize
     * @return array
     */
    public static function execute(int $reportid, bool $editmode, int $pagesize = 0): array {
        global $PAGE, $OUTPUT;

        [
            'reportid' => $reportid,
            'editmode' => $editmode,
            'pagesize' => $pagesize,
        ] = self::validate_parameters(self::execute_parameters(), [
            'reportid' => $reportid,
            'editmode' => $editmode,
            'pagesize' => $pagesize,
        ]);

        $report = manager::get_report_from_id($reportid);
        if ($pagesize > 0) {
            $report->set_default_per_page($pagesize);
        }
        self::validate_context($report->get_context());

        if ($editmode) {
            permission::require_can_edit_report($report->get_report_persistent());
        } else {
            permission::require_can_view_report($report->get_report_persistent());
        }

        // Set current URL and force bootstrap_renderer to initiate moodle page.
        $PAGE->set_url(new moodle_url('/'));
        $OUTPUT->header();
        $PAGE->start_collecting_javascript_requirements();

        $renderer = $PAGE->get_renderer('core_reportbuilder');
        $context = (new custom_report($report->get_report_persistent(), $editmode))->export_for_template($renderer);
        $context->javascript = $PAGE->requires->get_end_code();

        return (array)$context;
    }

    /**
     * External method return value
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return custom_report_exporter::get_read_structure();
    }
}
