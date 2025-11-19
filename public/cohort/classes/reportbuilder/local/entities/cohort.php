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

use stdClass;
use theme_config;
use core\{context, context_helper};
use core\lang_string;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{boolean_select, cohort as cohort_filter, date, select, text};
use core_reportbuilder\local\helpers\{custom_fields, format};
use core_reportbuilder\local\report\{column, filter};

/**
 * Cohort entity
 *
 * @package     core_cohort
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohort extends base {

    /** @var custom_fields $customfields */
    private custom_fields $customfields;

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'cohort',
            'context',
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
        $tablealias = $this->get_table_alias('cohort');

        $this->customfields = (new custom_fields(
            "{$tablealias}.id",
            $this->get_entity_name(),
            'core_cohort',
            'cohort',
        ))
            ->add_joins($this->get_joins());

        return parent::initialise();
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_available_columns(): array {
        $tablealias = $this->get_table_alias('cohort');
        $contextalias = $this->get_table_alias('context');

        // Category/context column.
        $columns[] = (new column(
            'context',
            new lang_string('category'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_join($this->get_context_join())
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            ->set_is_sortable(true)
            ->add_callback(static function($contextid, stdClass $cohort): string {
                if ($cohort->ctxid === null) {
                    return '';
                }

                context_helper::preload_from_record(clone $cohort);
                $context = context::instance_by_id($cohort->ctxid);

                return $context->get_context_name(false);
            });

        // Name column.
        $columns[] = (new column(
            'name',
            new lang_string('name', 'core_cohort'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_join($this->get_context_join())
            ->add_field("{$tablealias}.name")
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            ->set_is_sortable(true)
            ->set_callback(static function (?string $name, stdClass $cohort): string {
                if ($name === null || $cohort->ctxid === null) {
                    return '';
                }

                context_helper::preload_from_record(clone $cohort);
                $context = context::instance_by_id($cohort->ctxid);

                return format_string($name, options: ['context' => $context]);
            });

        // ID number column.
        $columns[] = (new column(
            'idnumber',
            new lang_string('idnumber', 'core_cohort'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$tablealias}.idnumber")
            ->set_is_sortable(true);

        // Description column.
        $columns[] = (new column(
            'description',
            new lang_string('description'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_join($this->get_context_join())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_fields("{$tablealias}.description, {$tablealias}.descriptionformat, {$tablealias}.id")
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            ->set_is_sortable(true)
            ->add_callback(static function(?string $description, stdClass $cohort): string {
                global $CFG;
                require_once("{$CFG->libdir}/filelib.php");

                if ($description === null || $cohort->ctxid === null) {
                    return '';
                }

                context_helper::preload_from_record(clone $cohort);
                $context = context::instance_by_id($cohort->ctxid);

                $description = file_rewrite_pluginfile_urls($description, 'pluginfile.php', $context->id, 'cohort',
                    'description', $cohort->id);

                return format_text($description, $cohort->descriptionformat, ['context' => $context]);
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
            ->add_fields("{$tablealias}.component")
            ->set_is_sortable(true)
            ->add_callback(static function(?string $component): string {
                if ($component === null) {
                    return '';
                }

                return $component === ''
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
            ->add_fields("{$tablealias}.theme")
            ->set_is_sortable(true)
            ->add_callback(static function (?string $theme): string {
                return match ($theme) {
                    null => '',
                    '' => get_string('forceno'),
                    default => get_string('pluginname', "theme_{$theme}"),
                };
            });

        // Merge with custom field columns.
        return array_merge($columns, $this->customfields->get_columns());
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_available_filters(): array {
        $tablealias = $this->get_table_alias('cohort');

        // Cohort select filter.
        $filters[] = (new filter(
            cohort_filter::class,
            'cohortselect',
            new lang_string('selectcohort', 'core_cohort'),
            $this->get_entity_name(),
            "{$tablealias}.id"
        ))
            ->add_joins($this->get_joins());

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
                    SELECT DISTINCT {$ctxfields}
                      FROM {context} ctx
                      JOIN {cohort} c ON c.contextid = ctx.id");

                // Transform context record into it's name (used as the filter options).
                return array_map(static function(stdClass $context): string {
                    context_helper::preload_from_record(clone $context);
                    $context = context::instance_by_id($context->ctxid);

                    return $context->get_context_name(false);
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
            "{$tablealias}.description"
        ))
            ->add_joins($this->get_joins());

        // Theme filter.
        $filters[] = (new filter(
            select::class,
            'theme',
            new lang_string('theme'),
            $this->get_entity_name(),
            "{$tablealias}.theme",
        ))
            ->set_options_callback(static function(): array {
                return ['' => get_string('forceno')] + array_map(
                    fn(theme_config $theme) => $theme->get_theme_name(),
                    get_list_of_themes(),
                );
            })
            ->add_joins($this->get_joins());

        // Visible filter.
        $filters[] = (new filter(
            boolean_select::class,
            'visible',
            new lang_string('visible', 'core_cohort'),
            $this->get_entity_name(),
            "{$tablealias}.visible"
        ))
            ->add_joins($this->get_joins());

        // Merge with custom field filters.
        return array_merge($filters, $this->customfields->get_filters());
    }

    /**
     * Return context join used by columns
     *
     * @return string
     */
    private function get_context_join(): string {

        // If the context table is already joined, we don't need to do that again.
        if ($this->has_table_join_alias('context')) {
            return '';
        }

        $tablealias = $this->get_table_alias('cohort');
        $contextalias = $this->get_table_alias('context');

        return "LEFT JOIN {context} {$contextalias} ON {$contextalias}.id = {$tablealias}.contextid";
    }
}
