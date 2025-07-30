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
use core\navigation\settings_navigation;

// phpcs:disable

/**
 * This is a dummy object that allows us to call protected methods within the
 * global navigation class by prefixing the methods with `exposed_`.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exposed_settings_navigation extends settings_navigation {
    protected string $exposedkey = 'exposed_';

    public function __construct() {
        global $PAGE;
        parent::__construct($PAGE);
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
}
