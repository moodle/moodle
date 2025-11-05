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

namespace mod_board\reportbuilder\local\entities;

use lang_string;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\report\{column, filter};
use core_reportbuilder\local\filters\select;

/**
 * Template entity class.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class template extends base {
    #[\Override]
    protected function get_default_tables(): array {
        return [
            'board_templates',
            'context',
        ];
    }

    #[\Override]
    protected function get_default_entity_title(): lang_string {
        return new lang_string('template', 'mod_board');
    }

    /**
     * Return syntax for joining on the context table
     *
     * @return string
     */
    public function get_context_join(): string {
        $templatealias = $this->get_table_alias('board_templates');
        $contextalias = $this->get_table_alias('context');

        return "LEFT JOIN {context} {$contextalias} ON {$contextalias}.id = {$templatealias}.contextid";
    }

    #[\Override]
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
     * Returns list of all available columns.
     *
     * @return column[]
     */
    protected function get_all_columns(): array {
        $templatealias = $this->get_table_alias('board_templates');

        $columns[] = (new column(
            'name',
            new lang_string('name', 'core'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$templatealias}.name")
            ->set_is_sortable(true)
            ->set_callback(static function (?string $value, \stdClass $row): string {
                return s($row->name);
            });

        $columns[] = (new column(
            'description',
            new lang_string('template_description', 'mod_board'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_fields("{$templatealias}.description")
            ->set_is_sortable(false)
            ->set_callback(static function (?string $value, \stdClass $row): string {
                return format_text($row->description, FORMAT_HTML);
            });

        $columns[] = (new column(
            'context',
            new lang_string('category'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->add_join($this->get_context_join())
            ->set_type(column::TYPE_INTEGER)
            ->add_fields("{$templatealias}.contextid")
            ->set_is_sortable(false)
            ->set_callback(static function (?int $value, \stdClass $row): string {
                if (!$row->contextid) {
                    return get_string('error');
                }
                $context = \context::instance_by_id($row->contextid, IGNORE_MISSING);
                if (!$context) {
                    return get_string('error');
                }
                return $context->get_context_name(false);
            });

        $columns[] = (new column(
            'columns',
            new lang_string('template_columns', 'mod_board'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_fields("{$templatealias}.columns")
            ->set_is_sortable(false)
            ->set_callback(static function (string $value, \stdClass $row): string {
                return \mod_board\local\template::format_columns($value);
            });

        $columns[] = (new column(
            'settings',
            new lang_string('settings', 'core'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_fields("{$templatealias}.jsonsettings")
            ->set_is_sortable(false)
            ->set_callback(static function (?string $value, \stdClass $row): string {
                if (!$value) {
                    return '';
                }
                return \mod_board\local\template::format_settings($value);
            });

        return $columns;
    }

    /**
     * Return list of all available filters.
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        $templatealias = $this->get_table_alias('board_templates');

        $filters[] = (new filter(
            text::class,
            'name',
            new lang_string('name', 'core'),
            $this->get_entity_name(),
            "{$templatealias}.name"
        ))
            ->add_joins($this->get_joins());

        $filters[] = (new filter(
            select::class,
            'context',
            new lang_string('category'),
            $this->get_entity_name(),
            "{$templatealias}.contextid"
        ))
            ->add_joins($this->get_joins())
            ->set_options_callback(static function (): array {
                global $DB;

                $sql = "SELECT DISTINCT bt.contextid
                          FROM {board_templates} bt";
                $contextids = $DB->get_fieldset_sql($sql);
                $result = [];
                foreach ($contextids as $contextid) {
                    $context = \context::instance_by_id($contextid, IGNORE_MISSING);
                    if (!$context) {
                        $result[$contextid] = get_string('error');
                        continue;
                    }
                    $result[$contextid] = $context->get_context_name(false);
                }

                \core_collator::asort($result);
                return $result;
            });

        return $filters;
    }
}
