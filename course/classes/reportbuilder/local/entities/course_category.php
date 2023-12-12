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

namespace core_course\reportbuilder\local\entities;

use context_coursecat;
use context_helper;
use html_writer;
use lang_string;
use moodle_url;
use stdClass;
use core_course_category;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{category, text};
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;

/**
 * Course category entity
 *
 * @package     core_course
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_category extends base {

    /**
     * Database tables that this entity uses and their default aliases
     *
     * @return array
     */
    protected function get_default_table_aliases(): array {
        return [
            'context' => 'ccctx',
            'course_categories' => 'cc',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('coursecategory');
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

        $tablealias = $this->get_table_alias('course_categories');
        $tablealiascontext = $this->get_table_alias('context');

        // Name column.
        $columns[] = (new column(
            'name',
            new lang_string('categoryname'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_join($this->get_context_join())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$tablealias}.name, {$tablealias}.id")
            ->add_fields(context_helper::get_preload_record_columns_sql($tablealiascontext))
            ->add_callback(static function(?string $name, stdClass $category): string {
                if (empty($category->id)) {
                    return '';
                }

                context_helper::preload_from_record($category);
                $context = context_coursecat::instance($category->id);

                return format_string($category->name, true, ['context' => $context]);
            })
            ->set_is_sortable(true);

        // Category name with link column.
        $columns[] = (new column(
            'namewithlink',
            new lang_string('namewithlink', 'core_course'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_join($this->get_context_join())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$tablealias}.name, {$tablealias}.id")
            ->add_fields(context_helper::get_preload_record_columns_sql($tablealiascontext))
            ->add_callback(static function(?string $name, stdClass $category): string {
                if (empty($category->id)) {
                    return '';
                }
                context_helper::preload_from_record($category);
                $context = context_coursecat::instance($category->id);
                $url = new moodle_url('/course/management.php', ['categoryid' => $category->id]);
                return html_writer::link($url,
                    format_string($category->name, true, ['context' => $context]));
            })
            ->set_is_sortable(true);

        // Path column.
        $columns[] = (new column(
            'path',
            new lang_string('categorypath'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$tablealias}.name, {$tablealias}.id")
            ->add_callback(static function(?string $name, stdClass $category): string {
                return empty($category->id) ? '' :
                    core_course_category::get($category->id, MUST_EXIST, true)->get_nested_name(false);
            })
            ->set_disabled_aggregation(['groupconcat', 'groupconcatdistinct'])
            ->set_is_sortable(true);

        // ID number column.
        $columns[] = (new column(
            'idnumber',
            new lang_string('idnumbercoursecategory'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$tablealias}.idnumber")
            ->set_is_sortable(true);

        // Description column (note we need to join/select from the context table in order to format the column).
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
            ->add_join($this->get_context_join())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_field($descriptionfieldsql, 'description')
            ->add_fields("{$tablealias}.descriptionformat, {$tablealias}.id")
            ->add_fields(context_helper::get_preload_record_columns_sql($tablealiascontext))
            ->add_callback(static function(?string $description, stdClass $category): string {
                global $CFG;
                require_once("{$CFG->libdir}/filelib.php");

                if ($description === null) {
                    return '';
                }

                context_helper::preload_from_record($category);
                $context = context_coursecat::instance($category->id);

                $description = file_rewrite_pluginfile_urls($description, 'pluginfile.php', $context->id, 'coursecat',
                    'description', null);

                return format_text($description, $category->descriptionformat, ['context' => $context->id]);
            });

        // Course count column.
        $columns[] = (new column(
            'coursecount',
            new lang_string('coursecount', 'core_course'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_INTEGER)
            ->add_fields("{$tablealias}.coursecount")
            ->set_is_sortable(true);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $tablealias = $this->get_table_alias('course_categories');

        // Select category filter.
        $filters[] = (new filter(
            category::class,
            'name',
            new lang_string('categoryselect', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$tablealias}.id"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
                'requiredcapabilities' => 'moodle/category:viewcourselist',
            ]);

        // Name filter.
        $filters[] = (new filter(
            text::class,
            'text',
            new lang_string('categoryname'),
            $this->get_entity_name(),
            "{$tablealias}.name"
        ))
            ->add_joins($this->get_joins());

        // ID number filter.
        $filters[] = (new filter(
            text::class,
            'idnumber',
            new lang_string('idnumbercoursecategory'),
            $this->get_entity_name(),
            "{$tablealias}.idnumber"
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }

    /**
     * Return context join used by columns
     *
     * @return string
     */
    public function get_context_join(): string {
        $coursecategories = $this->get_table_alias('course_categories');
        $context = $this->get_table_alias('context');
        return "LEFT JOIN {context} {$context} ON {$context}.instanceid = {$coursecategories}.id
            AND {$context}.contextlevel = " . CONTEXT_COURSECAT;
    }
}
