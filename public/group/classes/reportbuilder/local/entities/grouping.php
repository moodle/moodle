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

use core\{context, context_helper};
use lang_string;
use stdClass;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{date, text};
use core_reportbuilder\local\helpers\{custom_fields, format};
use core_reportbuilder\local\report\{column, filter};

/**
 * Grouping entity
 *
 * @package     core_group
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grouping extends base {

    /** @var custom_fields $customfields */
    private custom_fields $customfields;

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'context',
            'groupings',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('grouping', 'core_group');
    }

    /**
     * Initialise the entity
     *
     * @return base
     */
    public function initialise(): base {
        $groupingsalias = $this->get_table_alias('groupings');

        $this->customfields = (new custom_fields(
            "{$groupingsalias}.id",
            $this->get_entity_name(),
            'core_group',
            'grouping',
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
        $contextalias = $this->get_table_alias('context');
        $groupingsalias = $this->get_table_alias('groupings');

        // Name column.
        $columns[] = (new column(
            'name',
            new lang_string('name'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_field("{$groupingsalias}.name")
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            ->set_is_sortable(true)
            ->set_callback(static function(?string $name, stdClass $grouping): string {
                if ($name === null || $grouping->ctxid === null) {
                    return '';
                }

                context_helper::preload_from_record(clone $grouping);
                $context = context::instance_by_id($grouping->ctxid);

                return format_string($name, true, ['context' => $context]);
            });

        // ID number column.
        $columns[] = (new column(
            'idnumber',
            new lang_string('idnumber'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$groupingsalias}.idnumber")
            ->set_is_sortable(true);

        // Description column.
        $columns[] = (new column(
            'description',
            new lang_string('description'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_fields("{$groupingsalias}.description, {$groupingsalias}.descriptionformat, {$groupingsalias}.id")
            ->add_fields(context_helper::get_preload_record_columns_sql($contextalias))
            ->set_is_sortable(true)
            ->set_callback(static function(?string $description, stdClass $grouping): string {
                global $CFG;

                if ($description === null || $grouping->ctxid === null) {
                    return '';
                }

                require_once("{$CFG->libdir}/filelib.php");

                context_helper::preload_from_record(clone $grouping);
                $context = context::instance_by_id($grouping->ctxid);

                $description = file_rewrite_pluginfile_urls($description, 'pluginfile.php', $context->id, 'grouping',
                    'description', $grouping->id);

                return format_text($description, $grouping->descriptionformat, ['context' => $context]);
            });

        // Time created column.
        $columns[] = (new column(
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_fields("{$groupingsalias}.timecreated")
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
            ->add_fields("{$groupingsalias}.timemodified")
            ->set_is_sortable(true)
            ->set_callback([format::class, 'userdate']);

        // Merge with custom field columns.
        return array_merge($columns, $this->customfields->get_columns());
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_available_filters(): array {
        $groupingsalias = $this->get_table_alias('groupings');

        // Name filter.
        $filters[] = (new filter(
            text::class,
            'name',
            new lang_string('name'),
            $this->get_entity_name(),
            "{$groupingsalias}.name"
        ))
            ->add_joins($this->get_joins());

        // ID number filter.
        $filters[] = (new filter(
            text::class,
            'idnumber',
            new lang_string('idnumber'),
            $this->get_entity_name(),
            "{$groupingsalias}.idnumber"
        ))
            ->add_joins($this->get_joins());

        // Time created filter.
        $filters[] = (new filter(
            date::class,
            'timecreated',
            new lang_string('timecreated', 'core_reportbuilder'),
            $this->get_entity_name(),
            "{$groupingsalias}.timecreated"
        ))
            ->add_joins($this->get_joins());

        // Merge with custom field filters.
        return array_merge($filters, $this->customfields->get_filters());
    }
}
