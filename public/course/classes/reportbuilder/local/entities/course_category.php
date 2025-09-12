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

use core\{context, context_helper};
use core\url;
use html_writer;
use lang_string;
use stdClass;
use theme_config;
use core_course_category;
use core_reportbuilder\local\aggregation\{groupconcat, groupconcatdistinct};
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{category, number, select, text};
use core_reportbuilder\local\report\{column, filter};

/**
 * Course category entity
 *
 * @package     core_course
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_category extends base {

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'context',
            'course_categories',
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
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_available_columns(): array {
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
            ->add_field("{$tablealias}.name")
            ->add_fields(context_helper::get_preload_record_columns_sql($tablealiascontext))
            ->add_callback(static function(?string $name, stdClass $category): string {
                if ($name === null || $category->ctxid === null) {
                    return '';
                }

                context_helper::preload_from_record(clone $category);
                $context = context::instance_by_id($category->ctxid);

                return format_string($name, true, ['context' => $context]);
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
            ->add_field("{$tablealias}.name")
            ->add_fields(context_helper::get_preload_record_columns_sql($tablealiascontext))
            ->add_callback(static function(?string $name, stdClass $category): string {
                if ($name === null || $category->ctxid === null) {
                    return '';
                }

                context_helper::preload_from_record(clone $category);
                $context = context::instance_by_id($category->ctxid);

                return html_writer::link(
                    new url('/course/management.php', ['categoryid' => $context->instanceid]),
                    format_string($name, true, ['context' => $context]),
                );
            })
            ->set_is_sortable(true);

        // Path column.
        $columns[] = (new column(
            'path',
            new lang_string('categorypath'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$tablealias}.name, {$tablealias}.id")
            ->add_callback(static function(?string $name, stdClass $category): string {
                return empty($category->id) ? '' :
                    core_course_category::get($category->id, MUST_EXIST, true)->get_nested_name(false);
            })
            ->set_disabled_aggregation([
                groupconcat::get_class_name(),
                groupconcatdistinct::get_class_name(),
            ])
            ->set_is_sortable(true);

        // ID number column.
        $columns[] = (new column(
            'idnumber',
            new lang_string('idnumbercoursecategory'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$tablealias}.idnumber")
            ->set_is_sortable(true);

        // Description column (note we need to join/select from the context table in order to format the column).
        $columns[] = (new column(
            'description',
            new lang_string('description'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_join($this->get_context_join())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_fields("{$tablealias}.description, {$tablealias}.descriptionformat")
            ->add_fields(context_helper::get_preload_record_columns_sql($tablealiascontext))
            ->set_is_sortable(true)
            ->add_callback(static function(?string $description, stdClass $category): string {
                global $CFG;
                require_once("{$CFG->libdir}/filelib.php");

                if ($description === null || $category->ctxid === null) {
                    return '';
                }

                context_helper::preload_from_record(clone $category);
                $context = context::instance_by_id($category->ctxid);

                $description = file_rewrite_pluginfile_urls($description, 'pluginfile.php', $context->id, 'coursecat',
                    'description', null);

                return format_text($description, $category->descriptionformat, ['context' => $context]);
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
    protected function get_available_filters(): array {
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

        // Course count filter.
        $filters[] = (new filter(
            number::class,
            'coursecount',
            new lang_string('coursecount', 'core_course'),
            $this->get_entity_name(),
            "{$tablealias}.coursecount",
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
