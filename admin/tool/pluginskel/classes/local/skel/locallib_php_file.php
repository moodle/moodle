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
 * Provides tool_pluginskel\local\skel\locallib_php_file class.
 *
 * @package     tool_pluginskel
 * @subpackage  skel
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>, David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_pluginskel\local\skel;

defined('MOODLE_INTERNAL') || die();

/**
 * Class representing the locallib.php file.
 *
 * The plugin's internal functions, classes and constants should be defined in
 * locallib.php
 *
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class locallib_php_file extends php_internal_file {

    /**
     * Adds an event callback functionm to generate code for.
     *
     * @param string $callback The callback name
     * @param string $event The event name
     */
    public function add_event_callback($callback, $event) {

        if (empty($this->data)) {
            throw new exception('Skeleton data not set');
        }

        if (empty($this->data['self'])) {
            $this->data['self'] = array();
        }

        if (empty($this->data['self']['events'])) {
            $this->data['self']['events'] = array();
        }

        $this->data['self']['events'][] = array('event' => $event, 'callback' => $callback);
    }
}
