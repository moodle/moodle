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

namespace core_group\reportbuilder\local\entities;

use context_course;
use context_helper;
use html_writer;
use lang_string;
use moodle_url;
use stdClass;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{boolean_select, date, select, text};
use core_reportbuilder\local\helpers\{custom_fields, format};
use core_reportbuilder\local\report\{column, filter};

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->libdir}/grouplib.php");

/**
 * Group entity
 *
 * @package     core_group
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class group extends base {

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'context',
            'groups',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('group', 'core_group');
    }

    /**
     * Initialise the entity
     *
     * @return base
     */
    public function initialise(): base {
        $groupsalias = $this->get_table_alias('groups');

        $customfields = (new custom_fields(
            "{$groupsalias}.id",
            $this->get_entity_name(),
            'core_group',
            'group',
        ))
            ->add_joins($this->get_joins());

        $columns = array_merge($this->get_all_columns(), $customfields->get_columns());
        foreach ($columns as $column) {
            $this->add_column($column);
        }

        // All the filters defined by the entity can also be used as conditions.
        $filters = array_merge($this->get_all_filters(), $customfields->get_filters());
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

        $contextalias = $this->get_table_alias('context');
        $groupsalias = $this->get_table_alias('groups');

        // Name column.
        $columns[] = (new column(
            'name',
            new lang_string('name'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$groupsalias}.name, {$groupsalias}.courseid")
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            ->set_is_sortable(true)
            ->set_callback(static function($name, stdClass $group): string {
                if ($name === null) {
                    return '';
                }

                context_helper::preload_from_record($group);
                $context = context_course::instance($group->courseid);

                return format_string($group->name, true, ['context' => $context]);
            });

        // ID number column.
        $columns[] = (new column(
            'idnumber',
            new lang_string('idnumber'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$groupsalias}.idnumber")
            ->set_is_sortable(true);

        // Description column.
        $descriptionfieldsql = "{$groupsalias}.description";
        if ($DB->get_dbfamily() === 'oracle') {
            $descriptionfieldsql = $DB->sql_order_by_text($descriptionfieldsql, 1024);
        }
        $columns[] = (new column(
            'description',
            new lang_string('description'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_field($descriptionfieldsql, 'description')
            ->add_fields("{$groupsalias}.descriptionformat, {$groupsalias}.id, {$groupsalias}.courseid")
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            ->set_is_sortable(false)
            ->set_callback(static function(?string $description, stdClass $group): string {
                global $CFG;

                if ($description === null) {
                    return '';
                }

                require_once("{$CFG->libdir}/filelib.php");

                context_helper::preload_from_record($group);
                $context = context_course::instance($group->courseid);

                $description = file_rewrite_pluginfile_urls($description, 'pluginfile.php', $context->id, 'group',
                    'description', $group->id);

                return format_text($description, $group->descriptionformat, ['context' => $context]);
            });

        // Enrolment key column.
        $columns[] = (new column(
            'enrolmentkey',
            new lang_string('enrolmentkey', 'core_group'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$groupsalias}.enrolmentkey")
            ->set_is_sortable(true);

        // Visibility column.
        $columns[] = (new column(
            'visibility',
            new lang_string('visibilityshort', 'core_group'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$groupsalias}.visibility")
            ->set_is_sortable(true)
            ->set_callback(static function(?string $visibility): string {
                if ($visibility === null) {
                    return '';
                }

                $options = [
                    GROUPS_VISIBILITY_ALL => new lang_string('visibilityall', 'core_group'),
                    GROUPS_VISIBILITY_MEMBERS => new lang_string('visibilitymembers', 'core_group'),
                    GROUPS_VISIBILITY_OWN => new lang_string('visibilityown', 'core_group'),
                    GROUPS_VISIBILITY_NONE => new lang_string('visibilitynone', 'core_group'),
                ];

                return (string) ($options[(int) $visibility] ?? $visibility);
            });

        // Participation column.
        $columns[] = (new column(
            'participation',
            new lang_string('participationshort', 'core_group'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_BOOLEAN)
            ->add_fields("{$groupsalias}.participation")
            ->set_is_sortable(true)
            ->set_callback([format::class, 'boolean_as_text']);

        // Picture column.
        $columns[] = (new column(
            'picture',
            new lang_string('picture'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$groupsalias}.picture, {$groupsalias}.id, {$contextalias}.id AS contextid")
            ->set_is_sortable(false)
            ->set_callback(static function($value, stdClass $group): string {
                if (empty($group->picture)) {
                    return '';
                }

                $pictureurl = moodle_url::make_pluginfile_url($group->contextid, 'group', 'icon', $group->id, '/', 'f2');
                $pictureurl->param('rev', $group->picture);

                return html_writer::img($pictureurl, '');
            });

        // Time created column.
        $columns[] = (new column(
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$groupsalias}.timecreated")
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
            ->add_fields("{$groupsalias}.timemodified")
            ->set_is_sortable(true)
            ->set_callback([format::class, 'userdate']);

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $groupsalias = $this->get_table_alias('groups');

        // Name filter.
        $filters[] = (new filter(
            text::class,
            'name',
            new lang_string('name'),
            $this->get_entity_name(),
            "{$groupsalias}.name"
        ))
            ->add_joins($this->get_joins());

        // ID number filter.
        $filters[] = (new filter(
            text::class,
            'idnumber',
            new lang_string('idnumber'),
            $this->get_entity_name(),
            "{$groupsalias}.idnumber"
        ))
            ->add_joins($this->get_joins());

        // Visibility filter.
        $filters[] = (new filter(
            select::class,
            'visibility',
            new lang_string('visibilityshort', 'core_group'),
            $this->get_entity_name(),
            "{$groupsalias}.visibility"
        ))
            ->add_joins($this->get_joins())
            ->set_options([
                GROUPS_VISIBILITY_ALL => new lang_string('visibilityall', 'core_group'),
                GROUPS_VISIBILITY_MEMBERS => new lang_string('visibilitymembers', 'core_group'),
                GROUPS_VISIBILITY_OWN => new lang_string('visibilityown', 'core_group'),
                GROUPS_VISIBILITY_NONE => new lang_string('visibilitynone', 'core_group'),
            ]);

        // Participation filter.
        $filters[] = (new filter(
            boolean_select::class,
            'participation',
            new lang_string('participationshort', 'core_group'),
            $this->get_entity_name(),
            "{$groupsalias}.participation"
        ))
            ->add_joins($this->get_joins());

        // Time created filter.
        $filters[] = (new filter(
            date::class,
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$groupsalias}.timecreated"
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }
}
