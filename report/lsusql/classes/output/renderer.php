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

namespace report_lsusql\output;

use context;
use html_writer;
use moodle_url;
use plugin_renderer_base;
use stdClass;

/**
 * LSU Report API renderer class.
 *
 * @package   report_lsusql
 * @copyright 2021 The Open University
 * @copyright 2022 Louisiana State University
 * @copyright 2022 Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Output the standard action icons (edit, delete and back to list) for a report.
     *
     * @param stdClass $report the report.
     * @param context $context context to use for permission checks.
     * @param stdClass $category Category object.
     * @return string HTML for report actions.
     */
    public function render_report_actions(stdClass $report, stdClass $category, context $context):string {
        if (has_capability('report/lsusql:definequeries', $context)) {
            $reporturl = report_lsusql_url('view.php', ['id' => $report->id]);
            $editaction = $this->action_link(
                report_lsusql_url('edit.php', ['id' => $report->id, 'returnurl' => $reporturl->out_as_local_url(false)]),
                $this->pix_icon('t/edit', '') . ' ' .
                get_string('editreportx', 'report_lsusql', format_string($report->displayname)));
            $deleteaction = $this->action_link(
                report_lsusql_url('delete.php', ['id' => $report->id, 'returnurl' => $reporturl->out_as_local_url(false)]),
                $this->pix_icon('t/delete', '') . ' ' .
                get_string('deletereportx', 'report_lsusql', format_string($report->displayname)));
        }

        $backtocategoryaction = $this->action_link(
            report_lsusql_url('category.php', ['id' => $category->id]),
            $this->pix_icon('t/left', '') .
            get_string('backtocategory', 'report_lsusql', $category->name));

        $context = [
            'editaction' => isset($editaction) ? $editaction : '',
            'deleteaction' => isset($deleteaction) ? $deleteaction : '',
            'backtocategoryaction' => isset($backtocategoryaction) ? $backtocategoryaction : ''
        ];

        return $this->render_from_template('report_lsusql/query_actions', $context);
    }
}
