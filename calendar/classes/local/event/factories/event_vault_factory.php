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
 * Event vault factory class
 *
 * @package    core_calendar
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\factories;

use core_calendar\local\event\data_access\event_vault;
use core_calendar\local\interfaces\event_factory_interface;
use core_calendar\local\interfaces\event_vault_factory_interface;

/**
 * Event vault factory class
 *
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_vault_factory implements event_vault_factory_interface {
    /**
     * Creates an instance of an event vault
     *
     * @param event_factory_interface $eventfactory The event factory
     * @return event_vault_interface
     */
    public function create_instance(event_factory_interface $eventfactory) {
        return new event_vault($eventfactory);
    }
}
