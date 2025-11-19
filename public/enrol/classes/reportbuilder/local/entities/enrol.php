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

namespace core_enrol\reportbuilder\local\entities;

use enrol_plugin;
use lang_string;
use stdClass;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{boolean_select, date, duration, select, text};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};

/**
 * Enrolment method entity
 *
 * @package     core_enrol
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol extends base {

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'enrol',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('enrolmentmethod', 'core_enrol');
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_available_columns(): array {
        global $DB;

        $enrolalias = $this->get_table_alias('enrol');

        // Plugin column.
        $columns[] = (new column(
            'plugin',
            new lang_string('plugin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$enrolalias}.enrol")
            ->set_is_sortable(true)
            ->set_callback(static function(?string $enrol): string {
                if ($enrol === null || !$plugin = enrol_get_plugin($enrol)) {
                    return '';
                }

                return $plugin->get_instance_name(null);
            });

        // Name column.
        $columns[] = (new column(
            'name',
            new lang_string('name'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$enrolalias}.enrol, {$enrolalias}.name, {$enrolalias}.courseid, " .
                "{$enrolalias}.roleid, {$enrolalias}.customint1")
            ->set_is_sortable(true)
            ->set_callback(static function(?string $enrol, stdClass $instance): string {
                if ($enrol === null || !$plugin = enrol_get_plugin($enrol)) {
                    return '';
                }

                return $plugin->get_instance_name($instance);
            });

        // Enabled column.
        $columns[] = (new column(
            'enabled',
            new lang_string('enabled', 'core_admin'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_BOOLEAN)
            // For accurate aggregation, we need to return boolean enabled = true by xor'ing the field value.
            ->add_field($DB->sql_bitxor("{$enrolalias}.status", 1), 'status')
            ->set_is_sortable(true)
            ->set_callback([format::class, 'boolean_as_text']);

        // Period column.
        $columns[] = (new column(
            'period',
            new lang_string('enrolperiod', 'core_enrol'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$enrolalias}.enrolperiod")
            ->set_is_sortable(true)
            ->set_callback(static function(?int $enrolperiod, stdClass $row): string {
                if ($enrolperiod === 0) {
                    return '';
                }
                return format::format_time($enrolperiod, $row);
            });

        // Start date column.
        $columns[] = (new column(
            'startdate',
            new lang_string('enroltimestart', 'core_enrol'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$enrolalias}.enrolstartdate")
            ->set_is_sortable(true)
            ->set_callback([format::class, 'userdate']);

        // End date column.
        $columns[] = (new column(
            'enddate',
            new lang_string('enroltimeend', 'core_enrol'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$enrolalias}.enrolenddate")
            ->set_is_sortable(true)
            ->set_callback([format::class, 'userdate']);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_available_filters(): array {
        global $DB;

        $enrolalias = $this->get_table_alias('enrol');

        // Plugin filter.
        $filters[] = (new filter(
            select::class,
            'plugin',
            new lang_string('plugin'),
            $this->get_entity_name(),
            "{$enrolalias}.enrol"
        ))
            ->add_joins($this->get_joins())
            ->set_options_callback(static function(): array {
                return array_map(static function(enrol_plugin $plugin): string {
                    return $plugin->get_instance_name(null);
                }, enrol_get_plugins(true));
            });

        // Custom name filter.
        $filters[] = (new filter(
            text::class,
            'customname',
            new lang_string('custominstancename', 'core_enrol'),
            $this->get_entity_name(),
            "{$enrolalias}.name"
        ))
            ->add_joins($this->get_joins());

        // Enabled filter.
        $filters[] = (new filter(
            boolean_select::class,
            'enabled',
            new lang_string('enabled', 'core_admin'),
            $this->get_entity_name(),
            $DB->sql_bitxor("{$enrolalias}.status", 1)
        ))
            ->add_joins($this->get_joins());

        // Period filter.
        $filters[] = (new filter(
            duration::class,
            'period',
            new lang_string('enrolperiod', 'core_enrol'),
            $this->get_entity_name(),
            "{$enrolalias}.enrolperiod"
        ))
            ->add_joins($this->get_joins());

        // Start date filter.
        $filters[] = (new filter(
            date::class,
            'startdate',
            new lang_string('enroltimestart', 'core_enrol'),
            $this->get_entity_name(),
            "{$enrolalias}.enrolstartdate"
        ))
            ->add_joins($this->get_joins());

        // End date filter.
        $filters[] = (new filter(
            date::class,
            'enddate',
            new lang_string('enroltimeend', 'core_enrol'),
            $this->get_entity_name(),
            "{$enrolalias}.enrolenddate"
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }
}
