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

namespace core_question\local\bank;

/**
 * Default column manager class
 *
 * This class defines stub methods that can be overridden by a plugin defining its own column manager.
 *
 * @package   core_question
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class column_manager_base {
    /**
     * Sort the list of columns
     *
     * Sort the provided list of columns into the order implemented in this column manager.
     *
     * @param array $unsortedcolumns Unordered array of columns
     * @return array Columns in the desired order.
     */
    public function get_sorted_columns(array $unsortedcolumns): array {
        return $unsortedcolumns;
    }

    /**
     * Given an array of columns, set the isvisible attribute.
     *
     * This base class leave all columns visible.
     *
     * @param column_base[] $columns
     * @return array
     */
    public function set_columns_visibility(array $columns): array {
        return $columns;
    }

    /**
     * Return a list of actions to display in an action menu for each column.
     *
     * @param view $qbank Question bank view.
     * @return column_action_base[] A list of column actions.
     */
    public function get_column_actions(view $qbank): array {
        return [];
    }

    /**
     * Given a column, return a value for its width CSS property.
     *
     * @param column_base $column
     * @return string CSS width property value.
     */
    public function get_column_width(column_base $column): string {
        return $column->get_default_width() . 'px';
    }
}
