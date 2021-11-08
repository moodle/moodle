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

namespace core_course\local\entities;

use lang_string;
use stdClass;
use core_course_category;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\filters\text;
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
        $tablealias = $this->get_table_alias('course_categories');
        $tablealiascontext = $this->get_table_alias('context');

        // Name column.
        $columns[] = (new column(
            'name',
            new lang_string('categoryname'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$tablealias}.name, {$tablealias}.id")
            ->add_callback(static function(string $name, stdClass $category): string {
                return core_course_category::get($category->id, MUST_EXIST, true)->get_formatted_name();
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
            ->add_callback(static function(string $name, stdClass $category): string {
                return core_course_category::get($category->id, MUST_EXIST, true)->get_nested_name(false);
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
        $columns[] = (new column(
            'description',
            new lang_string('description'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_join("
                JOIN {context} {$tablealiascontext}
                  ON {$tablealiascontext}.instanceid = {$tablealias}.id
                 AND {$tablealiascontext}.contextlevel = " . CONTEXT_COURSECAT)
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$tablealias}.description, {$tablealias}.descriptionformat, {$tablealiascontext}.id AS contextid")
            ->add_callback(static function(string $description, stdClass $category): string {
                global $CFG;
                require_once("{$CFG->libdir}/filelib.php");

                $description = file_rewrite_pluginfile_urls($description, 'pluginfile.php', $category->contextid, 'coursecat',
                    'description', null);

                return format_text($description, $category->descriptionformat, ['context' => $category->contextid]);
            })
            ->set_is_sortable(false);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $tablealias = $this->get_table_alias('course_categories');

        // Name filter.
        $filters[] = (new filter(
            select::class,
            'name',
            new lang_string('categoryname'),
            $this->get_entity_name(),
            "{$tablealias}.id"
        ))
            ->add_joins($this->get_joins())
            ->set_options_callback(static function(): array {
                return core_course_category::make_categories_list('moodle/category:viewcourselist');
            });

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
}
