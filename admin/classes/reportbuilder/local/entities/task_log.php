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

namespace core_admin\reportbuilder\local\entities;

use core_reportbuilder\local\filters\date;
use core_reportbuilder\local\filters\duration;
use core_reportbuilder\local\filters\number;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\filters\autocomplete;
use core_reportbuilder\local\helpers\format;
use lang_string;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;
use stdClass;
use core_collator;

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
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'task_log',
        ];
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
     * Initialise the entity
     *
     * @return base
     */
    public function initialise(): base {
        $columns = $this->get_all_columns();
        foreach ($columns as $column) {
            $this->add_column($column);
        }

        // All the filters defined by the entity can also be used as conditions.
        $filters = $this->get_all_filters();
        foreach ($filters as $filter) {
            $this
                ->add_filter($filter)
                ->add_condition($filter);
        }

        return $this;
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_all_columns(): array {
        global $DB;

        $tablealias = $this->get_table_alias('task_log');

        // Name column.
        $columns[] = (new column(
            'name',
            new lang_string('name'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$tablealias}.classname")
            ->set_is_sortable(true)
            ->add_callback(static function(string $classname): string {
                $output = '';
                if (class_exists($classname)) {
                    $task = new $classname;
                    if ($task instanceof \core\task\task_base) {
                        $output = $task->get_name();
                    }
                }
                $output .= \html_writer::tag('div', "\\{$classname}", [
                    'class' => 'small text-muted',
                ]);
                return $output;
            });

        // Component column.
        $columns[] = (new column(
            'component',
            new lang_string('plugin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$tablealias}.component")
            ->set_is_sortable(true);

        // Type column.
        $columns[] = (new column(
            'type',
            new lang_string('tasktype', 'admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$tablealias}.type")
            ->set_is_sortable(true)
            ->add_callback(static function($value): string {
                if (\core\task\database_logger::TYPE_SCHEDULED === (int) $value) {
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

        // End time column.
        $columns[] = (new column(
            'endtime',
            new lang_string('task_endtime', 'admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$tablealias}.timeend")
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
            ->add_callback([format::class, 'format_time'], 2);

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
            ->add_field("{$tablealias}.pid")
            ->set_is_sortable(true);

        // Database column.
        $columns[] = (new column(
            'database',
            new lang_string('task_dbstats', 'admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$tablealias}.dbreads, {$tablealias}.dbwrites")
            ->set_is_sortable(true, ["{$tablealias}.dbreads", "{$tablealias}.dbwrites"])
            ->add_callback(static function($value, stdClass $row): string {
                $output = '';
                $output .= \html_writer::div(get_string('task_stats:dbreads', 'admin', $row->dbreads));
                $output .= \html_writer::div(get_string('task_stats:dbwrites', 'admin', $row->dbwrites));
                return $output;
            });

        // Database reads column.
        $columns[] = (new column(
            'dbreads',
            new lang_string('task_dbreads', 'admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_fields("{$tablealias}.dbreads")
            ->set_is_sortable(true);

        // Database writes column.
        $columns[] = (new column(
            'dbwrites',
            new lang_string('task_dbwrites', 'admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_fields("{$tablealias}.dbwrites")
            ->set_is_sortable(true);

        // Result column.
        $columns[] = (new column(
            'result',
            new lang_string('task_result', 'admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_BOOLEAN)
            // For accurate aggregation, we need to return boolean success = true by xor'ing the field value.
            ->add_field($DB->sql_bitxor("{$tablealias}.result", 1), 'success')
            ->set_is_sortable(true)
            ->add_callback(static function(bool $success): string {
                if (!$success) {
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
        global $DB;

        $tablealias = $this->get_table_alias('task_log');

        // Name filter (Filter by classname).
        $filters[] = (new filter(
            autocomplete::class,
            'name',
            new lang_string('classname', 'tool_task'),
            $this->get_entity_name(),
            "{$tablealias}.classname"
        ))
            ->add_joins($this->get_joins())
            ->set_options_callback(static function(): array {
                global $DB;
                $classnames = $DB->get_fieldset_sql('SELECT DISTINCT classname FROM {task_log} ORDER BY classname ASC');

                $options = [];
                foreach ($classnames as $classname) {
                    if (class_exists($classname)) {
                        $task = new $classname;
                        $options[$classname] = $task->get_name();
                    }
                }

                core_collator::asort($options);
                return $options;
            });

        // Component filter.
        $filters[] = (new filter(
            text::class,
            'component',
            new lang_string('plugin'),
            $this->get_entity_name(),
            "{$tablealias}.component"
        ))
            ->add_joins($this->get_joins());

        // Type filter.
        $filters[] = (new filter(
            select::class,
            'type',
            new lang_string('tasktype', 'admin'),
            $this->get_entity_name(),
            "{$tablealias}.type"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
                \core\task\database_logger::TYPE_ADHOC => new lang_string('task_type:adhoc', 'admin'),
                \core\task\database_logger::TYPE_SCHEDULED => new lang_string('task_type:scheduled', 'admin'),
            ]);

        // Output filter (Filter by task output).
        $filters[] = (new filter(
            text::class,
            'output',
            new lang_string('task_logoutput', 'admin'),
            $this->get_entity_name(),
            $DB->sql_cast_to_char("{$tablealias}.output")
        ))
            ->add_joins($this->get_joins());

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

        // End time.
        $filters[] = (new filter(
            date::class,
            'timeend',
            new lang_string('task_endtime', 'admin'),
            $this->get_entity_name(),
            "{$tablealias}.timeend"
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
            "{$tablealias}.timeend - {$tablealias}.timestart"
        ))
            ->add_joins($this->get_joins());

        // Database reads.
        $filters[] = (new filter(
            number::class,
            'dbreads',
            new lang_string('task_dbreads', 'admin'),
            $this->get_entity_name(),
            "{$tablealias}.dbreads"
        ))
            ->add_joins($this->get_joins());

        // Database writes.
        $filters[] = (new filter(
            number::class,
            'dbwrites',
            new lang_string('task_dbwrites', 'admin'),
            $this->get_entity_name(),
            "{$tablealias}.dbwrites"
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

        return $filters;
    }
}
