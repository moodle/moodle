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

use context;
use lang_string;
use moodle_url;
use pix_icon;
use stdClass;
use core_reportbuilder\permission;
use core_reportbuilder\system_report;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\filters\date;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\models\schedule;
use core_reportbuilder\local\report\action;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;
use core_reportbuilder\output\schedule_name_editable;

/**
 * Report schedules list
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_schedules extends system_report {

    /**
     * The name of our internal report entity
     *
     * @return string
     */
    private function get_schedule_entity_name(): string {
        return 'schedule';
    }

    /**
     * Initialise the report
     */
    protected function initialise(): void {
        $this->set_main_table(schedule::TABLE, 'sc');
        $this->add_join('JOIN {' . report::TABLE . '} rb ON rb.id = sc.reportid');

        $this->add_base_condition_simple('sc.reportid', $this->get_parameter('reportid', 0, PARAM_INT));

        // Select fields required for actions, permission checks, and row class callbacks.
        $this->add_base_fields('sc.id, sc.name, sc.enabled, rb.contextid');

        // Join user entity for "User modified" column.
        $entityuser = new user();
        $entityuseralias = $entityuser->get_table_alias('user');

        $this->add_entity($entityuser
            ->add_join("JOIN {user} {$entityuseralias} ON {$entityuseralias}.id = sc.usermodified")
        );

        // Define our internal entity for schedule elements.
        $this->annotate_entity($this->get_schedule_entity_name(),
            new lang_string('schedules', 'core_reportbuilder'));

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
     * Dim the table row for disabled schedules
     *
     * @param stdClass $row
     * @return string
     */
    public function get_row_class(stdClass $row): string {
        return $row->enabled ? '' : 'text-muted';
    }

    /**
     * Add columns to report
     */
    protected function add_columns(): void {
        $tablealias = $this->get_main_table_alias();

        // Enable toggle column.
        $this->add_column((new column(
            'enabled',
            null,
            $this->get_schedule_entity_name()
        ))
            ->set_type(column::TYPE_BOOLEAN)
            ->add_fields("{$tablealias}.enabled, {$tablealias}.id")
            ->set_is_sortable(false)
            ->set_callback(static function(bool $enabled, stdClass $row): string {
                global $PAGE;

                $renderer = $PAGE->get_renderer('core_reportbuilder');
                $attributes = [
                    ['name' => 'id', 'value' => $row->id],
                    ['name' => 'action', 'value' => 'schedule-toggle'],
                    ['name' => 'state', 'value' => $row->enabled],
                ];
                $label = $row->enabled ? get_string('disableschedule', 'core_reportbuilder')
                    : get_string('enableschedule', 'core_reportbuilder');
                return $renderer->render_from_template('core/toggle', [
                    'id' => 'schedule-toggle-' . $row->id,
                    'checked' => $row->enabled,
                    'dataattributes' => $attributes,
                    'label' => $label,
                    'labelclasses' => 'sr-only'
                ]);
            })
        );

        // Report name column.
        $this->add_column((new column(
            'name',
            new lang_string('name'),
            $this->get_schedule_entity_name()
        ))
            ->set_type(column::TYPE_TEXT)
            // We need enough fields to re-create the persistent and pass to the editable component.
            ->add_fields("{$tablealias}.id, {$tablealias}.name, {$tablealias}.reportid")
            ->set_is_sortable(true, ["{$tablealias}.name"])
            ->add_callback(function(string $value, stdClass $schedule): string {
                global $PAGE;

                $editable = new schedule_name_editable(0, new schedule(0, $schedule));
                return $editable->render($PAGE->get_renderer('core'));
            })
        );

        // Time scheduled column.
        $this->add_column((new column(
            'timescheduled',
            new lang_string('startingfrom'),
            $this->get_schedule_entity_name()
        ))
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$tablealias}.timescheduled")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate'])
        );

        // Time last sent column.
        $this->add_column((new column(
            'timelastsent',
            new lang_string('timelastsent', 'core_reportbuilder'),
            $this->get_schedule_entity_name()
        ))
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$tablealias}.timelastsent")
            ->set_is_sortable(true)
            ->add_callback(static function(int $timelastsent, stdClass $row): string {
                if ($timelastsent === 0) {
                    return get_string('never');
                }

                return format::userdate($timelastsent, $row);
            })
        );

        // Format column.
        $this->add_column((new column(
            'format',
            new lang_string('format'),
            $this->get_schedule_entity_name()
        ))
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$tablealias}.format")
            ->set_is_sortable(true)
            ->add_callback(static function(string $format): string {
                if (get_string_manager()->string_exists('dataformat', 'dataformat_' . $format)) {
                    return get_string('dataformat', 'dataformat_' . $format);
                } else {
                    return $format;
                }
            })
        );

        // Time created column.
        $this->add_column((new column(
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_schedule_entity_name()
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
            $this->get_schedule_entity_name()
        ))
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$tablealias}.timemodified")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate'])
        );

        // The user who modified the schedule.
        $this->add_column_from_entity('user:fullname')
            ->set_title(new lang_string('usermodified', 'core_reportbuilder'));

        // Initial sorting.
        $this->set_initial_sort_column('schedule:timecreated', SORT_DESC);
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
            $this->get_schedule_entity_name(),
            "{$tablealias}.name"
        )));

        // Time created filter.
        $this->add_filter((new filter(
            date::class,
            'timelastsent',
            new lang_string('timelastsent', 'core_reportbuilder'),
            $this->get_schedule_entity_name(),
            "{$tablealias}.timelastsent"
        ))
            ->set_limited_operators([
                date::DATE_ANY,
                date::DATE_EMPTY,
                date::DATE_RANGE,
                date::DATE_PREVIOUS,
                date::DATE_CURRENT,
            ])
        );

    }

    /**
     * Add actions to report
     */
    protected function add_actions(): void {
        // Edit action.
        $this->add_action(new action(
            new moodle_url('#'),
            new pix_icon('t/edit', ''),
            ['data-action' => 'schedule-edit', 'data-schedule-id' => ':id'],
            false,
            new lang_string('editscheduledetails', 'core_reportbuilder')
        ));

        // Send now action.
        $this->add_action((new action(
            new moodle_url('#'),
            new pix_icon('t/email', ''),
            ['data-action' => 'schedule-send', 'data-schedule-id' => ':id', 'data-schedule-name' => ':name'],
            false,
            new lang_string('sendschedule', 'core_reportbuilder')
        ))
            ->add_callback(function(stdClass $row): bool {

                // Ensure data name attribute is properly formatted.
                $row->name = (new schedule(0, $row))->get_formatted_name(
                    context::instance_by_id($row->contextid));

                return true;
            })
        );

        // Delete action.
        $this->add_action((new action(
            new moodle_url('#'),
            new pix_icon('t/delete', ''),
            [
                'data-action' => 'schedule-delete',
                'data-schedule-id' => ':id',
                'data-schedule-name' => ':name',
                'class' => 'text-danger',
            ],
            false,
            new lang_string('deleteschedule', 'core_reportbuilder')
        ))
            ->add_callback(function(stdClass $row): bool {

                // Ensure data name attribute is properly formatted.
                $row->name = (new schedule(0, $row))->get_formatted_name(
                    context::instance_by_id($row->contextid));

                return true;
            })
        );
    }
}
