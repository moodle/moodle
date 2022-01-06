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
 * Contains the string_title class of value object, which provides access to a simple string.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_course\local\entity;

defined('MOODLE_INTERNAL') || die();

/**
 * The string_title class of value object, which provides access to a simple string.
 *
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class string_title implements title {

    /** @var string $title the title string. */
    private $title;

    /**
     * The string_title constructor.
     *
     * @param string $title a string.
     */
    public function __construct(string $title) {
        $this->title = $title;
    }

    /**
     * Return the value of the wrapped string.
     *
     * @return string
     */
    public function get_value(): string {
        return $this->title;
    }
}
