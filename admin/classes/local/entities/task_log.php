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

namespace core_admin\local\entities;

use core_reportbuilder\local\filters\date;
use core_reportbuilder\local\filters\duration;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\helpers\format;
use lang_string;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;
use stdClass;

/**
 * Task log entity class implementation
 *
 * @package    core_admin
 * @copyright  2021 David Matamoros <davidmc@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class task_log extends base {

    /** @var int Result success */
    protected const SUCCESS = 0;

    /** @var int Result failed */
    protected const FAILED = 1;

    /**
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_table_aliases(): array {
        return ['task_log' => 'tl'];
    }

    /**
     * The default title for this entity in the list of columns/conditions/filters in the report builder
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('entitytasklog', 'admin');
    }

    /**
     * The default machine-readable name for this entity that will be used in the internal names of the columns/filters
     *
     * @return string
     */
    protected function get_default_entity_name(): string {
        return 'task_log';
    }

    /**
     * Initialise the entity
     *
     * @return base
     */
    public function initialise(): base {
        $columns = $this->get_all_columns();
        foreach ($columns as $column) {
            $this->add_column($column);
        }

        $filters = $this->get_all_filters();
        foreach ($filters as $filter) {
            $this->add_filter($filter);
        }

        return $this;
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_all_columns(): array {

        $tablealias = $this->get_table_alias('task_log');

        // Name column.
        $columns[] = (new column(
            'name',
            new lang_string('name'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("$tablealias.classname")
            ->set_is_sortable(true)
            ->add_callback(static function(string $value): string {
                $output = '';
                if (class_exists($value)) {
                    $task = new $value;
                    if ($task instanceof \core\task\scheduled_task) {
                        $output = $task->get_name();
                    }
                }
                $output .= \html_writer::tag('div', "\\{$value}", [
                    'class' => 'task-class',
                ]);
                return $output;
            });

        // Type column.
        $columns[] = (new column(
            'type',
            new lang_string('tasktype', 'admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$tablealias}.type")
            ->set_is_sortable(true)
            ->add_callback(static function(int $value): string {
                if (\core\task\database_logger::TYPE_SCHEDULED === $value) {
                    return get_string('task_type:scheduled', 'admin');
                }
                return get_string('task_type:adhoc', 'admin');
            });

        // Start time column.
        $columns[] = (new column(
            'starttime',
            new lang_string('task_starttime', 'admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$tablealias}.timestart")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate'], get_string('strftimedatetimeshortaccurate', 'core_langconfig'));

        // Duration column.
        $columns[] = (new column(
            'duration',
            new lang_string('task_duration', 'admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_FLOAT)
            ->add_field("{$tablealias}.timeend - {$tablealias}.timestart", 'duration')
            ->set_is_sortable(true)
            ->add_callback(static function(float $value): string {
                $duration = round($value, 2);
                if (empty($duration)) {
                    // The format_time function returns 'now' when the difference is exactly 0.
                    // Note: format_time performs concatenation in exactly this fashion so we should do this for consistency.
                    return '0 ' . get_string('secs', 'moodle');
                }
                return format_time($duration);
            });

        // Hostname column.
        $columns[] = (new column(
            'hostname',
            new lang_string('hostname', 'admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("$tablealias.hostname")
            ->set_is_sortable(true);

        // PID column.
        $columns[] = (new column(
            'pid',
            new lang_string('pid', 'admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("{$tablealias}.pid")
            ->set_is_sortable(true);

        // Database column.
        $columns[] = (new column(
            'database',
            new lang_string('task_dbstats', 'admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_fields("{$tablealias}.dbreads, {$tablealias}.dbwrites")
            ->set_is_sortable(true)
            ->add_callback(static function(int $value, stdClass $row): string {
                $output = '';
                $output .= \html_writer::div(get_string('task_stats:dbreads', 'admin', $row->dbreads));
                $output .= \html_writer::div(get_string('task_stats:dbwrites', 'admin', $row->dbwrites));
                return $output;
            });

        // Result column.
        $columns[] = (new column(
            'result',
            new lang_string('task_result', 'admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_field("$tablealias.result")
            ->set_is_sortable(true)
            ->add_callback(static function(int $value): string {
                if ($value) {
                    return get_string('task_result:failed', 'admin');
                }
                return get_string('success');
            });

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $filters = [];

        $tablealias = $this->get_table_alias('task_log');

        // Name filter (Filter by classname).
        $filters[] = (new filter(
            text::class,
            'name',
            new lang_string('classname', 'tool_task'),
            $this->get_entity_name(),
            "{$tablealias}.classname"
        ))
            ->add_joins($this->get_joins());

        // Output filter (Filter by task output).
        $filters[] = (new filter(
            text::class,
            'output',
            new lang_string('task_logoutput', 'admin'),
            $this->get_entity_name(),
            "{$tablealias}.output"
        ))
            ->add_joins($this->get_joins());

        // Result filter.
        $filters[] = (new filter(
            select::class,
            'result',
            new lang_string('task_result', 'admin'),
            $this->get_entity_name(),
            "{$tablealias}.result"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
                self::SUCCESS => get_string('success'),
                self::FAILED => get_string('task_result:failed', 'admin'),
            ]);

        // Start time filter.
        $filters[] = (new filter(
            date::class,
            'timestart',
            new lang_string('task_starttime', 'admin'),
            $this->get_entity_name(),
            "{$tablealias}.timestart"
        ))
            ->add_joins($this->get_joins())
            ->set_limited_operators([
                date::DATE_ANY,
                date::DATE_RANGE,
                date::DATE_PREVIOUS,
                date::DATE_CURRENT,
            ]);

        // Duration filter.
        $filters[] = (new filter(
            duration::class,
            'duration',
            new lang_string('task_duration', 'admin'),
            $this->get_entity_name(),
            "${tablealias}.timeend - {$tablealias}.timestart"
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }
}
