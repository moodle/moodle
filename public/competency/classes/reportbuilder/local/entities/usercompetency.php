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

namespace core_competency\reportbuilder\local\entities;

use core\lang_string;
use core_competency\{competency, user_competency};
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{boolean_select, select};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};
use stdClass;

/**
 * User competency entity
 *
 * @package     core_competency
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class usercompetency extends base {

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'competency_usercomp',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('usercompetency', 'core_competency');
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
        $usercompetencyalias = $this->get_table_alias('competency_usercomp');

        // Status.
        $columns[] = (new column(
            'status',
            new lang_string('status'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_field("{$usercompetencyalias}.status")
            ->set_is_sortable(true)
            ->add_callback(static function(?string $status): string {
                if ($status === null) {
                    return '';
                }

                return (string) user_competency::get_status_name((int) $status);
            });

        // Proficient.
        $columns[] = (new column(
            'proficient',
            new lang_string('proficient', 'core_competency'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_BOOLEAN)
            ->add_field("{$usercompetencyalias}.proficiency")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'boolean_as_text']);

        // Rating.
        $columns[] = (new column(
            'rating',
            new lang_string('rating', 'core_competency'),
            $this->get_entity_name(),
        ))
            ->add_joins($this->get_joins())
            ->add_fields("{$usercompetencyalias}.grade, {$usercompetencyalias}.competencyid")
            ->add_callback(static function(?string $grade, stdClass $row): string {
                if ($grade === null) {
                    return '';
                }

                $competency = new competency($row->competencyid);
                $scale = $competency->get_scale()->scale_items;

                return (string) ($scale[(int) $grade - 1] ?? $grade);
            });

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $usercompetencyalias = $this->get_table_alias('competency_usercomp');

        // Status.
        $filters[] = (new filter(
            select::class,
            'status',
            new lang_string('status'),
            $this->get_entity_name(),
            "{$usercompetencyalias}.status",
        ))
            ->add_joins($this->get_joins())
            ->set_options_callback([user_competency::class, 'get_status_list']);

        // Proficient.
        $filters[] = (new filter(
            boolean_select::class,
            'proficient',
            new lang_string('proficient', 'core_competency'),
            $this->get_entity_name(),
            "{$usercompetencyalias}.proficiency",
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }
}
