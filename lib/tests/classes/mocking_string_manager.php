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

namespace core\tests;

/**
 * A string manager which supports mocking individual strings.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mocking_string_manager extends \core_string_manager_standard {
    /** @var array<string, string> The list of strings */
    private $strings = [];

    #[\Override]
    public function get_string($identifier, $component = '', $a = null, $lang = null) {
        if (isset($this->strings["{$component}/{$identifier}"])) {
            return $this->strings["{$component}/{$identifier}"];
        }

        return parent::get_string($identifier, $component, $a, $lang);
    }

     /**
      * Mock a string.
      *
      * @param string $identifier
      * @param string $component
      * @param string $value
      * @return void
      */
    public function mock_string(
        string $identifier,
        string $component,
        string $value,
    ): void {
        $this->strings["{$component}/{$identifier}"] = $value;
    }
}
