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
 * Course module stdClass proxy.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\proxies;

defined('MOODLE_INTERNAL') || die();

/**
 * Course module stdClass proxy.
 *
 * This implementation differs from the regular std_proxy in that it takes
 * a module name and instance instead of an id to construct the proxied class.
 *
 * This is needed as the event table does not store the id of course modules
 * instead it stores the module name and instance.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module_std_proxy extends std_proxy implements proxy_interface {

    /**
     * module_std_proxy constructor.
     *
     * @param int $modulename The module name.
     * @param callable $instance The module instance.
     * @param callable $callback Callback to load the class.
     * @param \stdClass $base Class containing base values.
     */
    public function __construct($modulename, $instance, callable $callback, \stdClass $base = null) {
        $this->modulename = $modulename;
        $this->instance = $instance;
        $this->callbackargs = [$modulename, $instance];
        $this->callback = $callback;
        $this->base = $base = is_null($base) ? new \stdClass() : $base;
        $this->base->modulename = $modulename;
        $this->base->instance = $instance;
    }

    public function get_id() {
        return $this->get_proxied_instance()->id;
    }
}
