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

namespace tool_admin_presets\reportbuilder\local\entities;

use lang_string;
use core\output\inplace_editable;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\text;
use core_reportbuilder\local\report\{column, filter};

/**
 * Admin preset entity
 *
 * @package     tool_admin_presets
 * @copyright   2024 David Carrillo <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_preset extends base {

    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'adminpresets',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('pluginname', 'tool_admin_presets');
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_available_columns(): array {
        $apalias = $this->get_table_alias('adminpresets');

        // Name.
        $columns[] = (new column(
            'name',
            new lang_string('name'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_TEXT)
            ->add_fields("{$apalias}.id, {$apalias}.name, {$apalias}.iscore")
            ->set_is_sortable(true)
            ->set_callback(static function(?string $value, \stdClass $row): string {
                global $OUTPUT;
                $edithint = get_string('editadminpresetname', 'tool_admin_presets');
                $displayvalue = format_string($row->name, true, ['context' => \context_system::instance(), 'escape' => false]);
                $editlabel = get_string('newvaluefor', 'form', $displayvalue);
                $canedit = (int)$row->iscore === \core_adminpresets\manager::NONCORE_PRESET;
                $inlineeditable = new inplace_editable('tool_admin_presets', 'presetname', $row->id, $canedit,
                    $displayvalue, $row->name, $edithint, $editlabel);
                return $OUTPUT->render($inlineeditable);
            });

        // Description.
        $columns[] = (new column(
            'description',
            new lang_string('description'),
            $this->get_entity_name()
        ))
            ->add_joins($this->get_joins())
            ->set_type(column::TYPE_LONGTEXT)
            ->add_field("{$apalias}.comments")
            ->set_is_sortable(true)
            ->set_callback(static function(?string $description): string {
                return format_text($description, FORMAT_HTML, ['context' => \context_system::instance()]);
            });

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_available_filters(): array {
        $apalias = $this->get_table_alias('adminpresets');

        // Name.
        $filters[] = (new filter(
            text::class,
            'name',
            new lang_string('name'),
            $this->get_entity_name(),
            "{$apalias}.name"
        ))
            ->add_joins($this->get_joins());

        return $filters;
    }
}
