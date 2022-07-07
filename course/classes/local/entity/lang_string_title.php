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
 * Contains the lang_string_title class of value object, providing access to the value of a lang string.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_course\local\entity;

defined('MOODLE_INTERNAL') || die();

/**
 * The lang_string_title class of value object, providing access to the value of a lang string.
 *
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lang_string_title implements title {

    /** @var string $component the component name. */
    private $component;

    /** @var string $identifier the string identifier. */
    private $identifier;

    /**
     * The lang_string_title constructor.
     *
     * @param string $identifier the component name.
     * @param string $component the string identifier.
     */
    public function __construct(string $identifier, string $component) {
        $this->identifier = $identifier;
        $this->component = $component;
    }

    /**
     * Returns the value of the wrapped string.
     *
     * @return string the value of the string.
     */
    public function get_value(): string {
        return get_string($this->identifier, $this->component);
    }
}
