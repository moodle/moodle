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

namespace core_reportbuilder\output;

use html_writer;
use moodle_url;
use plugin_renderer_base;
use core_reportbuilder\table\custom_report_table;
use core_reportbuilder\table\custom_report_table_view;
use core_reportbuilder\table\system_report_table;
use core_reportbuilder\local\models\report;

/**
 * Report renderer class
 *
 * @package     core_reportbuilder
 * @copyright   2020 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Render a system report
     *
     * @param system_report $report
     * @return string
     */
    protected function render_system_report(system_report $report): string {
        $context = $report->export_for_template($this);

        return $this->render_from_template('core_reportbuilder/report', $context);
    }

    /**
     * Render a system report table
     *
     * @param system_report_table $table
     * @return string
     */
    protected function render_system_report_table(system_report_table $table): string {
        ob_start();
        $table->out($table->get_default_per_page(), false);
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /**
     * Render a custom report
     *
     * @param custom_report $report
     * @return string
     */
    protected function render_custom_report(custom_report $report): string {
        $context = $report->export_for_template($this);

        return $this->render_from_template('core_reportbuilder/local/dynamictabs/editor', $context);
    }

    /**
     * Render a custom report table
     *
     * @param custom_report_table $table
     * @return string
     */
    protected function render_custom_report_table(custom_report_table $table): string {
        ob_start();
        $table->out($table->get_default_per_page(), false);
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /**
     * Render a custom report table (view only mode)
     *
     * @param custom_report_table_view $table
     * @return string
     */
    protected function render_custom_report_table_view(custom_report_table_view $table): string {
        ob_start();
        $table->out($table->get_default_per_page(), false);
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /**
     * Renders the New report button
     *
     * @return string
     */
    public function render_new_report_button(): string {
        return html_writer::tag('button', get_string('newreport', 'core_reportbuilder'), [
            'class' => 'btn btn-primary my-auto',
            'data-action' => 'report-create',
        ]);
    }

    /**
     * Renders full page editor header
     *
     * @param report $report
     * @return string
     */
    public function render_fullpage_editor_header(report $report): string {
        $reportname = $report->get_formatted_name();
        $editdetailsbutton = html_writer::tag('button', get_string('editdetails', 'core_reportbuilder'), [
            'class' => 'btn btn-outline-secondary me-2',
            'data-action' => 'report-edit',
            'data-report-id' => $report->get('id')
        ]);
        $closebutton = html_writer::link(new moodle_url('/reportbuilder/index.php'), get_string('close', 'core_reportbuilder'), [
            'class' => 'btn btn-secondary',
            'title' => get_string('closeeditor', 'core_reportbuilder', $reportname),
            'role' => 'button'
        ]);
        $context = [
            'title' => $reportname,
            'buttons' => $editdetailsbutton . $closebutton,
        ];

        return $this->render_from_template('core_reportbuilder/editor_navbar', $context);
    }
}
