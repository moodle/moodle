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
 * Provides rendering functionality for the forum summary report subplugin.
 *
 * @package   forumreport_summary
 * @copyright 2019 Michael Hawkins <michaelh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use forumreport_summary\summary_table;

/**
 * Renderer for the forum summary report.
 *
 * @copyright  2019 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class forumreport_summary_renderer extends plugin_renderer_base {

    /**
     * Render the filters available for the forum summary report.
     *
     * @param stdClass $cm The course module object.
     * @param moodle_url $actionurl The form action URL.
     * @param array $filters Optional array of currently applied filter values.
     * @return string The filter form HTML.
     */
    public function render_filters_form(stdClass $cm, moodle_url $actionurl, array $filters = []): string {
        $renderable = new \forumreport_summary\output\filters($cm, $actionurl, $filters);
        $templatecontext = $renderable->export_for_template($this);

        return $this->render_from_template('forumreport_summary/filters', $templatecontext);
    }

    /**
     * Render the summary report table.
     *
     * @param summary_table $table The summary table to be rendered.
     * @param int $perpage Number of results to render per page.
     * @return string The report table HTML.
     */
    public function render_summary_table(summary_table $table, int $perpage): string {
        // Buffer so calling script can output the report as required.
        ob_start();

        // Render table.
        $table->out($perpage, false);

        $tablehtml = ob_get_contents();

        ob_end_clean();

        return $this->render_from_template('forumreport_summary/report', ['tablehtml' => $tablehtml, 'placeholdertext' => false]);
    }

    /**
     * Render the bulk action menu for the forum summary report.
     * @return string
     */
    public function render_bulk_action_menu(): string {
        $data = new stdClass();
        $data->id = 'formactionid';
        $data->attributes = [
            [
                'name' => 'data-action',
                'value' => 'toggle'
            ],
            [
                'name' => 'data-togglegroup',
                'value' => 'summaryreport-table'
            ],
            [
                'name' => 'data-toggle',
                'value' => 'action'
            ],
            [
                'name' => 'disabled',
                'value' => true
            ]
        ];
        $data->actions = [
            [
                'value' => '#messageselect',
                'name' => get_string('messageselectadd')
            ]
        ];

        return $this->render_from_template('forumreport_summary/bulk_action_menu', $data);
    }
}
