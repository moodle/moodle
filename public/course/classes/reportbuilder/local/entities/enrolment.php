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

use core\lang_string;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{date, select};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};
use core_user\output\status_field;

/**
 * Course enrolment entity implementation
 *
 * @package     core_course
 * @copyright   2022 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrolment extends base {
    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'user_enrolments',
            'enrol',
        ];
    }

    /**
     * The default title for this entity in the list of columns/conditions/filters in the report builder
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('enrolment', 'enrol');
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_available_columns(): array {
        $userenrolments = $this->get_table_alias('user_enrolments');

        // Enrolment time created.
        $columns[] = (new column(
            'timecreated',
            new lang_string('timecreated', 'moodle'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$userenrolments}.timecreated")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // Enrolment time started.
        $columns[] = (new column(
            'timestarted',
            new lang_string('timestarted', 'enrol'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("
                CASE WHEN {$userenrolments}.timestart = 0
                     THEN {$userenrolments}.timecreated
                     ELSE {$userenrolments}.timestart
                 END", 'timestarted')
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // Enrolment time ended.
        $columns[] = (new column(
            'timeended',
            new lang_string('timeended', 'enrol'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TIMESTAMP)
            ->add_field("{$userenrolments}.timeend")
            ->set_is_sortable(true)
            ->add_callback([format::class, 'userdate']);

        // Enrolment status.
        $columns[] = (new column(
            'status',
            new lang_string('status', 'moodle'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_field($this->get_status_field_sql(), 'status')
            ->set_is_sortable(true)
            ->add_callback(static function (?string $status): string {
                if ($status === null) {
                    return '';
                }

                $statuses = [
                    status_field::STATUS_ACTIVE => new lang_string('participationactive', 'core_enrol'),
                    status_field::STATUS_SUSPENDED => new lang_string('participationsuspended', 'core_enrol'),
                    status_field::STATUS_NOT_CURRENT => new lang_string('participationnotcurrent', 'core_enrol'),
                ];

                return (string) ($statuses[(int) $status] ?? $status);
            });

        return $columns;
    }

    /**
     * Generate SQL snippet suitable for returning enrolment status field
     *
     * @return string
     */
    private function get_status_field_sql(): string {
        $time = time();
        $userenrolments = $this->get_table_alias('user_enrolments');
        $enrol = $this->get_table_alias('enrol');

        return "
            CASE WHEN {$userenrolments}.status = " . ENROL_USER_ACTIVE . "
                 THEN CASE WHEN ({$userenrolments}.timestart > {$time})
                             OR ({$userenrolments}.timeend > 0 AND {$userenrolments}.timeend < {$time})
                             OR ({$enrol}.status = " . ENROL_INSTANCE_DISABLED . ")
                           THEN " . status_field::STATUS_NOT_CURRENT . "
                           ELSE " . status_field::STATUS_ACTIVE . "
                      END
                 ELSE {$userenrolments}.status
            END";
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_available_filters(): array {
        $userenrolments = $this->get_table_alias('user_enrolments');

        // Enrolment time created.
        $filters[] = (new filter(
            date::class,
            'timecreated',
            new lang_string('timecreated', 'moodle'),
            $this->get_entity_name(),
            "{$userenrolments}.timecreated"
        ))
            ->add_joins($this->get_joins())
            ->set_limited_operators([
                date::DATE_ANY,
                date::DATE_NOT_EMPTY,
                date::DATE_EMPTY,
                date::DATE_RANGE,
                date::DATE_LAST,
                date::DATE_CURRENT,
            ]);

        // Enrolment time started.
        $filters[] = (new filter(
            date::class,
            'timestarted',
            new lang_string('timestarted', 'enrol'),
            $this->get_entity_name(),
            "CASE WHEN {$userenrolments}.timestart = 0
                          THEN {$userenrolments}.timecreated
                          ELSE {$userenrolments}.timestart
                      END"
        ))
            ->add_joins($this->get_joins())
            ->set_limited_operators([
                date::DATE_ANY,
                date::DATE_NOT_EMPTY,
                date::DATE_EMPTY,
                date::DATE_RANGE,
                date::DATE_LAST,
                date::DATE_CURRENT,
            ]);

        // Enrolment time ended.
        $filters[] = (new filter(
            date::class,
            'timeended',
            new lang_string('timeended', 'enrol'),
            $this->get_entity_name(),
            "{$userenrolments}.timeend"
        ))
            ->add_joins($this->get_joins())
            ->set_limited_operators([
                date::DATE_ANY,
                date::DATE_NOT_EMPTY,
                date::DATE_EMPTY,
                date::DATE_RANGE,
                date::DATE_LAST,
                date::DATE_CURRENT,
            ]);

        // Enrolment status.
        $filters[] = (new filter(
            select::class,
            'status',
            new lang_string('status', 'moodle'),
            $this->get_entity_name(),
            $this->get_status_field_sql()
        ))
            ->add_joins($this->get_joins())
            ->set_options([
                status_field::STATUS_ACTIVE => new lang_string('participationactive', 'core_enrol'),
                status_field::STATUS_SUSPENDED => new lang_string('participationsuspended', 'core_enrol'),
                status_field::STATUS_NOT_CURRENT => new lang_string('participationnotcurrent', 'core_enrol'),
            ]);

        return $filters;
    }
}
