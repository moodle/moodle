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

namespace core_reportbuilder\local\systemreports;

use html_writer;
use lang_string;
use moodle_url;
use pix_icon;
use stdClass;
use core_reportbuilder\datasource;
use core_reportbuilder\manager;
use core_reportbuilder\system_report;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\filters\date;
use core_reportbuilder\local\filters\tags;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\helpers\audience;
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\action;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;
use core_reportbuilder\output\report_name_editable;
use core_reportbuilder\local\models\report;
use core_reportbuilder\permission;
use core_tag_tag;

/**
 * Reports list
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reports_list extends system_report {

    /**
     * The name of our internal report entity
     *
     * @return string
     */
    private function get_report_entity_name(): string {
        return 'report';
    }

    /**
     * Initialise the report
     */
    protected function initialise(): void {
        $this->set_main_table('reportbuilder_report', 'rb');
        $this->add_base_condition_simple('rb.type', self::TYPE_CUSTOM_REPORT);

        // Select fields required for actions, permission checks, and row class callbacks.
        $this->add_base_fields('rb.id, rb.name, rb.source, rb.type, rb.usercreated, rb.contextid');

        // Limit the returned list to those reports the current user can access.
        [$where, $params] = audience::user_reports_list_access_sql('rb');
        $this->add_base_condition_sql($where, $params);

        // Join user entity for "User modified" column.
        $entityuser = new user();
        $entityuseralias = $entityuser->get_table_alias('user');

        $this->add_entity($entityuser
            ->add_join("JOIN {user} {$entityuseralias} ON {$entityuseralias}.id = rb.usermodified")
        );

        // Define our internal entity for report elements.
        $this->annotate_entity($this->get_report_entity_name(),
            new lang_string('customreports', 'core_reportbuilder'));

        $this->add_columns();
        $this->add_filters();
        $this->add_actions();

        $this->set_downloadable(false);
    }

    /**
     * Ensure we can view the report
     *
     * @return bool
     */
    protected function can_view(): bool {
        return permission::can_view_reports_list();
    }

    /**
     * Dim the table row for invalid datasource
     *
     * @param stdClass $row
     * @return string
     */
    public function get_row_class(stdClass $row): string {
        return $this->report_source_valid($row->source) ? '' : 'text-muted';
    }

    /**
     * Add columns to report
     */
    protected function add_columns(): void {
        global $DB;

        $tablealias = $this->get_main_table_alias();

        // Report name column.
        $this->add_column((new column(
            'name',
            new lang_string('name'),
            $this->get_report_entity_name()
        ))
            ->set_type(column::TYPE_TEXT)
            // We need enough fields to re-create the persistent and pass to the editable component.
            ->add_fields(implode(', ', [
                "{$tablealias}.id",
                "{$tablealias}.name",
                "{$tablealias}.contextid",
                "{$tablealias}.type",
                "{$tablealias}.usercreated",
            ]))
            ->set_is_sortable(true, ["{$tablealias}.name"])
            ->add_callback(static function(string $value, stdClass $report): string {
                global $PAGE;

                $editable = new report_name_editable(0, new report(0, $report));
                return $editable->render($PAGE->get_renderer('core'));
            })
        );

        // Report source column.
        $this->add_column((new column(
            'source',
            new lang_string('reportsource', 'core_reportbuilder'),
            $this->get_report_entity_name()
        ))
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$tablealias}.source")
            ->set_is_sortable(true)
            ->add_callback(function(string $value, stdClass $row) {
                if (!$this->report_source_valid($value)) {
                    // Add danger badge if report source is not valid (either it's missing, or has errors).
                    return html_writer::span(get_string('errorsourceinvalid', 'core_reportbuilder'), 'badge bg-danger text-white');
                }

                return call_user_func([$value, 'get_name']);
            })
        );

        // Tags column. TODO: Reuse tag entity column when MDL-76392 is integrated.
        $tagfieldconcatsql = $DB->sql_group_concat(
            field: $DB->sql_concat_join("'|'", ['t.name', 't.rawname']),
            sort: 't.name',
        );
        $this->add_column((new column(
            'tags',
            new lang_string('tags'),
            $this->get_report_entity_name(),
        ))
            ->set_type(column::TYPE_TEXT)
            ->add_field("(
                SELECT {$tagfieldconcatsql}
                  FROM {tag_instance} ti
                  JOIN {tag} t ON t.id = ti.tagid
                 WHERE ti.component = 'core_reportbuilder' AND ti.itemtype = 'reportbuilder_report'
                   AND ti.itemid = {$tablealias}.id
            )", 'tags')
            ->set_is_sortable(true)
            ->set_is_available(core_tag_tag::is_enabled('core_reportbuilder', 'reportbuilder_report') === true)
            ->add_callback(static function(?string $tags): string {
                return implode(', ', array_map(static function(string $tag): string {
                    [$name, $rawname] = explode('|', $tag);
                    return core_tag_tag::make_display_name((object) [
                        'name' => $name,
                        'rawname' => $rawname,
                    ]);
                }, preg_split('/, /', (string) $tags, -1, PREG_SPLIT_NO_EMPTY)));
            })
        );

        // Time created column.
        $this->add_column((new column(
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_report_entity_name()
        ))
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$tablealias}.timecreated")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate'])
        );

        // Time modified column.
        $this->add_column((new column(
            'timemodified',
            new lang_string('timemodified', 'core_reportbuilder'),
            $this->get_report_entity_name()
        ))
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$tablealias}.timemodified")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate'])
        );

        // The user who modified the report.
        $this->add_column_from_entity('user:fullname')
            ->set_title(new lang_string('usermodified', 'reportbuilder'));

        // Initial sorting.
        $this->set_initial_sort_column('report:timecreated', SORT_DESC);
    }

    /**
     * Add filters to report
     */
    protected function add_filters(): void {
        $tablealias = $this->get_main_table_alias();

        // Name filter.
        $this->add_filter((new filter(
            text::class,
            'name',
            new lang_string('name'),
            $this->get_report_entity_name(),
            "{$tablealias}.name"
        )));

        // Source filter.
        $this->add_filter((new filter(
            select::class,
            'source',
            new lang_string('reportsource', 'core_reportbuilder'),
            $this->get_report_entity_name(),
            "{$tablealias}.source"
        ))
            ->set_options_callback(static function(): array {
                return manager::get_report_datasources();
            })
        );

        // Tags filter.
        $this->add_filter((new filter(
            tags::class,
            'tags',
            new lang_string('tags'),
            $this->get_report_entity_name(),
            "{$tablealias}.id",
        ))
            ->set_options([
                'component' => 'core_reportbuilder',
                'itemtype' => 'reportbuilder_report',
            ])
            ->set_is_available(core_tag_tag::is_enabled('core_reportbuilder', 'reportbuilder_report') === true)
        );

        // Time created filter.
        $this->add_filter((new filter(
            date::class,
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_report_entity_name(),
            "{$tablealias}.timecreated"
        ))
            ->set_limited_operators([
                date::DATE_ANY,
                date::DATE_RANGE,
            ])
        );
    }

    /**
     * Add actions to report
     */
    protected function add_actions(): void {
        // Edit content action.
        $this->add_action((new action(
            new moodle_url('/reportbuilder/edit.php', ['id' => ':id']),
            new pix_icon('t/right', ''),
            [],
            false,
            new lang_string('editreportcontent', 'core_reportbuilder')
        ))
            ->add_callback(function(stdClass $row): bool {
                return $this->report_source_valid($row->source) && permission::can_edit_report(new report(0, $row));
            })
        );

        // Edit details action.
        $this->add_action((new action(
            new moodle_url('#'),
            new pix_icon('t/edit', ''),
            ['data-action' => 'report-edit', 'data-report-id' => ':id'],
            false,
            new lang_string('editreportdetails', 'core_reportbuilder')
        ))
            ->add_callback(function(stdClass $row): bool {
                return $this->report_source_valid($row->source) && permission::can_edit_report(new report(0, $row));
            })
        );

        // Preview action.
        $this->add_action((new action(
            new moodle_url('/reportbuilder/view.php', ['id' => ':id']),
            new pix_icon('i/search', ''),
            [],
            false,
            new lang_string('viewreport', 'core_reportbuilder')
        ))
            ->add_callback(function(stdClass $row): bool {
                // We check this only to give the action to editors, because normal users can just click on the report name.
                return $this->report_source_valid($row->source) && permission::can_edit_report(new report(0, $row));
            })
        );

        // Delete action.
        $this->add_action((new action(
            new moodle_url('#'),
            new pix_icon('t/delete', ''),
            [
                'data-action' => 'report-delete',
                'data-report-id' => ':id',
                'data-report-name' => ':name',
                'class' => 'text-danger',
            ],
            false,
            new lang_string('deletereport', 'core_reportbuilder')
        ))
            ->add_callback(function(stdClass $row): bool {

                // Ensure data name attribute is properly formatted.
                $report = new report(0, $row);
                $row->name = $report->get_formatted_name();

                // We don't check whether report is valid to ensure editor can always delete them.
                return permission::can_edit_report($report);
            })
        );
    }

    /**
     * Helper to determine whether given report source is valid (it both exists, and is available)
     *
     * @param string $source
     * @return bool
     */
    private function report_source_valid(string $source): bool {
        return manager::report_source_exists($source, datasource::class) && manager::report_source_available($source);
    }
}
