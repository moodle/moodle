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
 * Action factory interface.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\factories;

defined('MOODLE_INTERNAL') || die();

interface action_factory_interface {
    /**
     * Creates an instance of an action.
     *
     * @param string      $name       The action's name.
     * @param \moodle_url $url        The action's URL.
     * @param int         $itemcount  The number of items needing action.
     * @param bool        $actionable The action's actionability.
     * @return \core_calendar\local\event\entities\action_interface The action.
     */
    public function create_instance($name, \moodle_url $url, $itemcount, $actionable);
}
