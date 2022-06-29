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
 * Contains the interface content_item_readonly_repository_interface, defining operations for readonly content item repositories.
 *
 * This interface is not considered a published interface and serves to govern internal, local repository objects only.
 * All calling code should use instances of the service classes, and should not interact with repositories directly.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_course\local\repository;

defined('MOODLE_INTERNAL') || die();

interface content_item_readonly_repository_interface {
    /**
     * Find all content items for a given course and user.
     *
     * @param \stdClass $course the course object.
     * @param \stdClass $user the user object.
     * @return array the array of content items.
     */
    public function find_all_for_course(\stdClass $course, \stdClass $user): array;

    /**
     * Find all content items that can be presented, irrespective of course.
     *
     * @return array the array of content items.
     */
    public function find_all(): array;
}
