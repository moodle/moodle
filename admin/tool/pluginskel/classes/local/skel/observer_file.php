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
 * Provides tool_pluginskel\local\skel\observer_file class.
 *
 * @package     tool_pluginskel
 * @subpackage  skel
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>, David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_pluginskel\local\skel;

defined('MOODLE_INTERNAL') || die();

/**
 * Class representing a Moodle observer class file.
 *
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer_file extends php_single_file {

    /**
     * Sets the name of the observer.
     *
     * @param string $name The observer's name.
     */
    public function set_observer_name($name) {

        if (empty($this->data)) {
            throw new exception('Skeleton data not set');
        }

        if (!empty($this->data['observer']['observer_name'])) {
            throw new exception("Observer: '$name' already set");
        }

        $this->data['observer']['observer_name'] = $name;
    }

    /**
     * Adds a callback function for the observer.
     *
     * @param string $callback The function name.
     * @param string $event The event that triggers the callback.
     */
    public function add_event_callback($callback, $event) {

        if (empty($this->data)) {
            throw new exception('Skeleton data not set');
        }

        if (empty($this->data['observer']['callbacks'])) {
            $this->data['observer']['callbacks'] = array();
        }

        $this->data['observer']['callbacks'][] = array('callback' => $callback, 'event' => $event);
    }

    /**
     * Sets the namespace for the observer class file.
     *
     * @param string $namespace
     */
    public function set_file_namespace($namespace) {

        if (empty($this->data)) {
            throw new exception('Skeleton data not set');
        }

        $this->data['observer']['namespace'] = 'namespace '.$namespace.';';
    }

}
