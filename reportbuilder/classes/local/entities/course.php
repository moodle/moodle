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

namespace core_reportbuilder\local\entities;

use context_course;
use context_helper;
use core_reportbuilder\local\filters\boolean_select;
use core_reportbuilder\local\filters\course_selector;
use core_reportbuilder\local\filters\date;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\helpers\custom_fields;
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;
use html_writer;
use lang_string;
use stdClass;
use theme_config;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Course entity class implementation
 *
 * This entity defines all the course columns and filters to be used in any report.
 *
 * @package     core_reportbuilder
 * @copyright   2021 Sara Arjona <sara@moodle.com> based on Marina Glancy code.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course extends base {

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'course',
            'context',
            'tag_instance',
            'tag',
        ];
    }

    /**
     * The default title for this entity in the list of columns/filters in the report builder.
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('entitycourse', 'core_reportbuilder');
    }

    /**
     * Get custom fields helper
     *
     * @return custom_fields
     */
    protected function get_custom_fields(): custom_fields {
        $customfields = new custom_fields($this->get_table_alias('course') . '.id', $this->get_entity_name(),
            'core_course', 'course');
        $customfields->add_joins($this->get_joins());
        return $customfields;
    }

    /**
     * Initialise the entity, adding all course and custom course fields
     *
     * @return base
     */
    public function initialise(): base {
        $customfields = $this->get_custom_fields();

        $columns = array_merge($this->get_all_columns(), $customfields->get_columns());
        foreach ($columns as $column) {
            $this->add_column($column);
        }

        $filters = array_merge($this->get_all_filters(), $customfields->get_filters());
        foreach ($filters as $filter) {
            $this
                ->add_condition($filter)
                ->add_filter($filter);
        }

        return $this;
    }

    /**
     * Return syntax for joining on the context table
     *
     * @return string
     */
    public function get_context_join(): string {
        $coursealias = $this->get_table_alias('course');
        $contextalias = $this->get_table_alias('context');

        return "LEFT JOIN {context} {$contextalias}
            ON {$contextalias}.contextlevel = " . CONTEXT_COURSE . "
           AND {$contextalias}.instanceid = {$coursealias}.id";
    }

    /**
     * Course fields.
     *
     * @return array
     */
    protected function get_course_fields(): array {
        return [
            'fullname' => new lang_string('fullnamecourse'),
            'shortname' => new lang_string('shortnamecourse'),
            'idnumber' => new lang_string('idnumbercourse'),
            'summary' => new lang_string('coursesummary'),
            'format' => new lang_string('format'),
            'startdate' => new lang_string('startdate'),
            'enddate' => new lang_string('enddate'),
            'visible' => new lang_string('coursevisibility'),
            'groupmode' => new lang_string('groupmode', 'group'),
            'groupmodeforce' => new lang_string('groupmodeforce', 'group'),
            'lang' => new lang_string('forcelanguage'),
            'calendartype' => new lang_string('forcecalendartype', 'calendar'),
            'theme' => new lang_string('theme'),
            'enablecompletion' => new lang_string('enablecompletion', 'completion'),
            'downloadcontent' => new lang_string('downloadcoursecontent', 'course'),
            'timecreated' => new lang_string('timecreated', 'core_reportbuilder'),
            'timemodified' => new lang_string('timemodified', 'core_reportbuilder'),
        ];
    }

    /**
     * Check if this field is sortable
     *
     * @param string $fieldname
     * @return bool
     */
    protected function is_sortable(string $fieldname): bool {
        // Some columns can't be sorted, like longtext or images.
        $nonsortable = [
            'summary',
        ];

        return !in_array($fieldname, $nonsortable);
    }

    /**
     * Return appropriate column type for given user field
     *
     * @param string $coursefield
     * @return int
     */
    protected function get_course_field_type(string $coursefield): int {
        switch ($coursefield) {
            case 'downloadcontent':
            case 'enablecompletion':
            case 'groupmodeforce':
            case 'visible':
                $fieldtype = column::TYPE_BOOLEAN;
                break;
            case 'startdate':
            case 'enddate':
            case 'timecreated':
            case 'timemodified':
                $fieldtype = column::TYPE_TIMESTAMP;
                break;
            case 'summary':
                $fieldtype = column::TYPE_LONGTEXT;
                break;
            case 'groupmode':
                $fieldtype = column::TYPE_INTEGER;
                break;
            case 'calendartype':
            case 'idnumber':
            case 'format':
            case 'fullname':
            case 'lang':
            case 'shortname':
            case 'theme':
            default:
                $fieldtype = column::TYPE_TEXT;
                break;
        }

        return $fieldtype;
    }

    /**
     * Return joins necessary for retrieving tags
     *
     * @return string[]
     */
    public function get_tag_joins(): array {
        return $this->get_tag_joins_for_entity('core', 'course', $this->get_table_alias('course') . '.id');
    }

    /**
     * Returns list of all available columns.
     *
     * These are all the columns available to use in any report that uses this entity.
     *
     * @return column[]
     */
    protected function get_all_columns(): array {
        global $DB;

        $coursefields = $this->get_course_fields();
        $tablealias = $this->get_table_alias('course');
        $contexttablealias = $this->get_table_alias('context');

        // Columns course full name with link, course short name with link and course id with link.
        $fields = [
            'coursefullnamewithlink' => 'fullname',
            'courseshortnamewithlink' => 'shortname',
            'courseidnumberewithlink' => 'idnumber',
        ];
        foreach ($fields as $key => $field) {
            $column = (new column(
                $key,
                new lang_string($key, 'core_reportbuilder'),
                $this->get_entity_name()
            ))
                ->add_joins($this->get_joins())
                ->set_type(column::TYPE_TEXT)
                ->add_fields("{$tablealias}.{$field} as $key, {$tablealias}.id")
                ->set_is_sortable(true)
                ->add_callback(static function(?string $value, stdClass $row): string {
                    if ($value === null) {
                        return '';
                    }

                    context_helper::preload_from_record($row);

                    return html_writer::link(course_get_url($row->id),
                        format_string($value, true, ['context' => context_course::instance($row->id)]));
                });

            // Join on the context table so that we can use it for formatting these columns later.
            if ($key === 'coursefullnamewithlink') {
                $column->add_join($this->get_context_join())
                    ->add_fields(context_helper::get_preload_record_columns_sql($contexttablealias));
            }

            $columns[] = $column;
        }

        foreach ($coursefields as $coursefield => $coursefieldlang) {
            $columntype = $this->get_course_field_type($coursefield);

            $columnfieldsql = "{$tablealias}.{$coursefield}";
            if ($columntype === column::TYPE_LONGTEXT && $DB->get_dbfamily() === 'oracle') {
                $columnfieldsql = $DB->sql_order_by_text($columnfieldsql, 1024);
            }

            $column = (new column(
                $coursefield,
                $coursefieldlang,
                $this->get_entity_name()
            ))
                ->add_joins($this->get_joins())
                ->set_type($columntype)
                ->add_field($columnfieldsql, $coursefield)
                ->add_callback([$this, 'format'], $coursefield)
                ->set_is_sortable($this->is_sortable($coursefield));

            // Join on the context table so that we can use it for formatting these columns later.
            if ($coursefield === 'summary' || $coursefield === 'shortname' || $coursefield === 'fullname') {
                $column->add_join($this->get_context_join())
                    ->add_field("{$tablealias}.id", 'courseid')
                    ->add_fields(context_helper::get_preload_record_columns_sql($contexttablealias));
            }

            $columns[] = $column;
        }

        return $columns;
    }

    /**
     * Returns list of all available filters
     *
     * @return array
     */
    protected function get_all_filters(): array {
        global $DB;

        $filters = [];
        $tablealias = $this->get_table_alias('course');

        $fields = $this->get_course_fields();
        foreach ($fields as $field => $name) {
            $filterfieldsql = "{$tablealias}.{$field}";
            if ($this->get_course_field_type($field) === column::TYPE_LONGTEXT) {
                $filterfieldsql = $DB->sql_cast_to_char($filterfieldsql);
            }

            $optionscallback = [static::class, 'get_options_for_' . $field];
            if (is_callable($optionscallback)) {
                $filterclass = select::class;
            } else if ($this->get_course_field_type($field) === column::TYPE_BOOLEAN) {
                $filterclass = boolean_select::class;
            } else if ($this->get_course_field_type($field) === column::TYPE_TIMESTAMP) {
                $filterclass = date::class;
            } else {
                $filterclass = text::class;
            }

            $filter = (new filter(
                $filterclass,
                $field,
                $name,
                $this->get_entity_name(),
                $filterfieldsql
            ))
                ->add_joins($this->get_joins());

            // Populate filter options by callback, if available.
            if (is_callable($optionscallback)) {
                $filter->set_options_callback($optionscallback);
            }

            $filters[] = $filter;
        }

        // We add our own custom course selector filter.
        $filters[] = (new filter(
            course_selector::class,
            'courseselector',
            new lang_string('courseselect', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$tablealias}.id"
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }

    /**
     * Gets list of options if the filter supports it
     *
     * @param string $fieldname
     * @return null|array
     */
    protected function get_options_for(string $fieldname): ?array {
        static $cached = [];
        if (!array_key_exists($fieldname, $cached)) {
            $callable = [static::class, 'get_options_for_' . $fieldname];
            if (is_callable($callable)) {
                $cached[$fieldname] = $callable();
            } else {
                $cached[$fieldname] = null;
            }
        }
        return $cached[$fieldname];
    }

    /**
     * List of options for the field groupmode.
     *
     * @return array
     */
    public static function get_options_for_groupmode(): array {
        return [
            NOGROUPS => get_string('groupsnone', 'group'),
            SEPARATEGROUPS => get_string('groupsseparate', 'group'),
            VISIBLEGROUPS => get_string('groupsvisible', 'group'),
        ];
    }

    /**
     * List of options for the field format.
     *
     * @return array
     */
    public static function get_options_for_format(): array {
        global $CFG;
        require_once($CFG->dirroot.'/course/lib.php');

        $options = [];

        $courseformats = get_sorted_course_formats(true);
        foreach ($courseformats as $courseformat) {
            $options[$courseformat] = get_string('pluginname', "format_{$courseformat}");
        }

        return $options;
    }

    /**
     * List of options for the field theme.
     *
     * @return array
     */
    public static function get_options_for_theme(): array {
        return array_map(
            fn(theme_config $theme) => $theme->get_theme_name(),
            get_list_of_themes(),
        );
    }

    /**
     * List of options for the field lang.
     *
     * @return array
     */
    public static function get_options_for_lang(): array {
        return get_string_manager()->get_list_of_translations();
    }

    /**
     * List of options for the field.
     *
     * @return array
     */
    public static function get_options_for_calendartype(): array {
        return \core_calendar\type_factory::get_list_of_calendar_types();
    }

    /**
     * Formats the course field for display.
     *
     * @param mixed $value Current field value.
     * @param stdClass $row Complete row.
     * @param string $fieldname Name of the field to format.
     * @return string
     */
    public function format($value, stdClass $row, string $fieldname): string {
        if ($this->get_course_field_type($fieldname) === column::TYPE_TIMESTAMP) {
            return format::userdate($value, $row);
        }

        if ($this->get_course_field_type($fieldname) === column::TYPE_BOOLEAN) {
            return format::boolean_as_text($value);
        }

        // If the column has corresponding filter, determine the value from its options.
        $options = $this->get_options_for($fieldname);
        if ($options !== null && array_key_exists($value, $options)) {
            return $options[$value];
        }

        if (in_array($fieldname, ['fullname', 'shortname'])) {
            if (!$row->courseid) {
                return '';
            }
            context_helper::preload_from_record($row);
            $context = context_course::instance($row->courseid);
            return format_string($value, true, ['context' => $context->id, 'escape' => false]);
        }

        if (in_array($fieldname, ['summary'])) {
            if (!$row->courseid) {
                return '';
            }
            context_helper::preload_from_record($row);
            $context = context_course::instance($row->courseid);
            $summary = file_rewrite_pluginfile_urls($row->summary, 'pluginfile.php', $context->id, 'course', 'summary', null);
            return format_text($summary);
        }

        return s($value);
    }
}
