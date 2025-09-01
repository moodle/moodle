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

namespace core\tests\navigation;

use core\exception\coding_exception;
use core\navigation\global_navigation;

// phpcs:disable
/**
 * This is a dummy object that allows us to call protected methods within the
 * global navigation class by prefixing the methods with `exposed_`
 */
class exposed_global_navigation extends global_navigation {
    protected $exposedkey = 'exposed_';
    public function __construct(?\moodle_page $page = null) {
        global $PAGE;
        if ($page === null) {
            $page = $PAGE;
        }
        parent::__construct($page);
    }
    public function __call($method, $arguments) {
        if (strpos($method, $this->exposedkey) !== false) {
            $method = substr($method, strlen($this->exposedkey));
        }
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $arguments);
        }
        throw new coding_exception(
            'You have attempted to access a method that does not exist for the given object ' . $method,
            DEBUG_DEVELOPER,
        );
    }
    public function set_initialised() {
        $this->initialised = true;
    }
}
