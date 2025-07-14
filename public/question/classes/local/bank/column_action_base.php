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
 * Base class to implement actions that can be performed on any column.
 *
 * A plugin should define subclasses of this for each action it provides, and return an instance of each from
 * plugin_feature::get_column_actions(). The action returned from {@see get_action_menu_link()} will be displayed in each column
 * header.
 *
 * @package   core_question
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class column_action_base extends view_component {
    /**
     * A chance for subclasses to initialise themselves, for example to load lang strings,
     * without having to override the constructor.
     */
    protected function init(): void {
    }

    /**
     * Return the action menu link for this action on the supplied column.
     *
     * @param column_base $column The column we are providing the action for.
     * @return ?\action_menu_link The action to display in the column header.
     */
    abstract public function get_action_menu_link(column_base $column): ?\action_menu_link;
}
