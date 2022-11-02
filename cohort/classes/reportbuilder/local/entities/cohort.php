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

namespace core_cohort\reportbuilder\local\entities;

use context;
use context_helper;
use lang_string;
use stdClass;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\date;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;

/**
 * Cohort entity
 *
 * @package     core_cohort
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohort extends base {

    /**
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_table_aliases(): array {
        return [
            'cohort' => 'c',
            'context' => 'chctx',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('cohort', 'core_cohort');
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

        $tablealias = $this->get_table_alias('cohort');
        $contextalias = $this->get_table_alias('context');

        // Category/context column.
        $columns[] = (new column(
            'context',
            new lang_string('category'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_join("JOIN {context} {$contextalias} ON {$contextalias}.id = {$tablealias}.contextid")
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$tablealias}.contextid, " . context_helper::get_preload_record_columns_sql($contextalias))
            ->set_is_sortable(true)
            ->add_callback(static function($contextid, stdClass $cohort): string {
                context_helper::preload_from_record($cohort);
                return context::instance_by_id($cohort->contextid)->get_context_name(false);
            });

        // Name column.
        $columns[] = (new column(
            'name',
            new lang_string('name', 'core_cohort'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$tablealias}.name")
            ->set_is_sortable(true);

        // ID number column.
        $columns[] = (new column(
            'idnumber',
            new lang_string('idnumber', 'core_cohort'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$tablealias}.idnumber")
            ->set_is_sortable(true);

        // Description column.
        $descriptionfieldsql = "{$tablealias}.description";
        if ($DB->get_dbfamily() === 'oracle') {
            $descriptionfieldsql = $DB->sql_order_by_text($descriptionfieldsql, 1024);
        }
        $columns[] = (new column(
            'description',
            new lang_string('description'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_join("JOIN {context} {$contextalias} ON {$contextalias}.id = {$tablealias}.contextid")
            ->set_type(column::TYPE_LONGTEXT)
            ->add_field($descriptionfieldsql, 'description')
            ->add_fields("{$tablealias}.descriptionformat, {$tablealias}.id, {$tablealias}.contextid")
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            ->add_callback(static function(?string $description, stdClass $cohort): string {
                global $CFG;
                require_once("{$CFG->libdir}/filelib.php");

                if ($description === null) {
                    return '';
                }

                context_helper::preload_from_record($cohort);
                $context = context::instance_by_id($cohort->contextid);

                $description = file_rewrite_pluginfile_urls($description, 'pluginfile.php', $context->id, 'cohort',
                    'description', $cohort->id);

                return format_text($description, $cohort->descriptionformat, ['context' => $context->id]);
            });

        // Visible column.
        $columns[] = (new column(
            'visible',
            new lang_string('visible', 'core_cohort'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_BOOLEAN)
            ->add_fields("{$tablealias}.visible")
            ->set_is_sortable(true)
            ->set_callback([format::class, 'boolean_as_text']);

        // Time created column.
        $columns[] = (new column(
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$tablealias}.timecreated")
            ->set_is_sortable(true)
            ->set_callback([format::class, 'userdate']);

        // Time modified column.
        $columns[] = (new column(
            'timemodified',
            new lang_string('timemodified', 'core_reportbuilder'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$tablealias}.timemodified")
            ->set_is_sortable(true)
            ->set_callback([format::class, 'userdate']);

        // Component column.
        $columns[] = (new column(
            'component',
            new lang_string('component', 'core_cohort'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$tablealias}.component")
            ->set_is_sortable(true)
            ->add_callback(static function(string $component): string {
                return empty($component)
                    ? get_string('nocomponent', 'cohort')
                    : get_string('pluginname', $component);
            });

        // Theme column.
        $columns[] = (new column(
            'theme',
            new lang_string('theme'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$tablealias}.theme")
            ->set_is_sortable(true);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        global $DB;

        $tablealias = $this->get_table_alias('cohort');

        // Context filter.
        $filters[] = (new filter(
            select::class,
            'context',
            new lang_string('category'),
            $this->get_entity_name(),
            "{$tablealias}.contextid"
        ))
            ->add_joins($this->get_joins())
            ->set_options_callback(static function(): array {
                global $DB;

                // Load all contexts in which there are cohorts.
                $ctxfields = context_helper::get_preload_record_columns_sql('ctx');
                $contexts = $DB->get_records_sql("
                    SELECT DISTINCT {$ctxfields}, c.contextid
                      FROM {context} ctx
                      JOIN {cohort} c ON c.contextid = ctx.id");

                // Transform context record into it's name (used as the filter options).
                return array_map(static function(stdClass $contextrecord): string {
                    context_helper::preload_from_record($contextrecord);

                    return context::instance_by_id($contextrecord->contextid)
                        ->get_context_name(false);
                }, $contexts);
            });

        // Name filter.
        $filters[] = (new filter(
            text::class,
            'name',
            new lang_string('name', 'core_cohort'),
            $this->get_entity_name(),
            "{$tablealias}.name"
        ))
            ->add_joins($this->get_joins());

        // ID number filter.
        $filters[] = (new filter(
            text::class,
            'idnumber',
            new lang_string('idnumber', 'core_cohort'),
            $this->get_entity_name(),
            "{$tablealias}.idnumber"
        ))
            ->add_joins($this->get_joins());

        // Time created filter.
        $filters[] = (new filter(
            date::class,
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$tablealias}.timecreated"
        ))
            ->add_joins($this->get_joins());

        // Description filter.
        $filters[] = (new filter(
            text::class,
            'description',
            new lang_string('description'),
            $this->get_entity_name(),
            $DB->sql_cast_to_char("{$tablealias}.description")
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }
}
