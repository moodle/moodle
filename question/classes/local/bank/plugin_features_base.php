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

/**
 * Base class class for qbank plugins.
 *
 * Every qbank plugin must extent this class.
 *
 * @package    core_question
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\local\bank;

/**
 * Class plugin_features_base is the base class for qbank plugins.
 *
 * @package    core_question
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin_features_base {

    /**
     * This method will return the array of objects to be rendered as a part of question bank columns/actions.
     *
     * @param view $qbank
     * @return array
     */
    public function get_question_columns(view $qbank): ?array {
        return [];
    }

    /**
     * This method will return the object for the navigation node.
     *
     * @return null|navigation_node_base
     */
    public function get_navigation_node(): ?navigation_node_base {
        return null;
    }

    /**
     * This method will return the array objects for the bulk actions ui.
     *
     * @return bulk_action_base[]
     */
    public function get_bulk_actions() {
        return [];
    }

}
