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

namespace core_course;

/**
 * Constants related to courses.
 *
 * @package    core_course
 * @copyright The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class constants {
    /** @var int the length of the course.shortname field. */
    public const FULLNAME_MAXIMUM_LENGTH = 1333;

    /** @var int the length of the course.shortname field. */
    public const SHORTNAME_MAXIMUM_LENGTH = 255;

}
