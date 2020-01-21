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
 * Contains the content_item_service class.
 *
 * @package    core
 * @subpackage course
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_course\local\service;

defined('MOODLE_INTERNAL') || die();

use core_course\local\exporters\course_content_items_exporter;
use core_course\local\repository\content_item_readonly_repository_interface;

/**
 * The content_item_service class, providing the api for interacting with content items.
 *
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_item_service {

    /** @var content_item_readonly_repository_interface $repository a repository for content items. */
    private $repository;

    /**
     * The content_item_service constructor.
     *
     * @param content_item_readonly_repository_interface $repository a content item repository.
     */
    public function __construct(content_item_readonly_repository_interface $repository) {
        $this->repository = $repository;
    }

    /**
     * Return a representation of the available content items, for a user in a course.
     *
     * @param \stdClass $user the user to check access for.
     * @param \stdClass $course the course to scope the content items to.
     * @param array $linkparams the desired section to return to.
     * @return \stdClass[] the content items, scoped to a course.
     */
    public function get_content_items_for_user_in_course(\stdClass $user, \stdClass $course, array $linkparams = []): array {
        global $PAGE;

        if (!has_capability('moodle/course:manageactivities', \context_course::instance($course->id), $user)) {
            return [];
        }

        // Get all the visible content items.
        $allcontentitems = $this->repository->find_all_for_course($course, $user);

        // Now, check access to these items for the user.
        $availablecontentitems = array_filter($allcontentitems, function($contentitem) use ($course, $user) {
            // Check the parent module access for the user.
            return course_allowed_module($course, explode('_', $contentitem->get_component_name())[1], $user);
        });

        // Add the link params to the link, if any have been provided.
        if (!empty($linkparams)) {
            $availablecontentitems = array_map(function ($item) use ($linkparams) {
                $item->get_link()->params($linkparams);
                return $item;
            }, $availablecontentitems);
        }

        // Export the objects to get the formatted objects for transfer/display.
        $ciexporter = new course_content_items_exporter(
            $availablecontentitems,
            ['context' => \context_course::instance($course->id)]
        );
        $exported = $ciexporter->export($PAGE->get_renderer('course'));

        // Sort by title for return.
        usort($exported->content_items, function($a, $b) {
            return $a->title > $b->title;
        });

        return $exported->content_items;
    }
}
