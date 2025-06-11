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

namespace theme_snap\controller;

/**
 * Controller Abstract.
 * Controllers handle requests.  Any methods that end with "_action"
 * can be routed to via the URL.  See the router for how this happens.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class controller_abstract {
    /**
     * Generate a new URL to this page
     *
     * @param array $params
     * @return \moodle_url
     */
    public function new_url($params = array()) {
        global $PAGE;

        $url = $PAGE->url;
        $url->params($params);
        return $url;
    }

    /**
     * Initialize the controller before the given
     * action is invoked.
     *
     * @param string $action This is the action that is about to be invoked
     * @throws \moodle_exception
     */
    public function init($action) {
        $this->require_capability($action);
    }

    /**
    * Used for test_action, modify if needed.
    */
    public function test_action() {}

    /**
     * Do any security checks needed for the passed action
     *
     * @abstract
     * @param string $action
     */
    abstract public function require_capability($action);
}
