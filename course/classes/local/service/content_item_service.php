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
     * Get all content items which may be added to courses, irrespective of course caps, for site admin views, etc.
     *
     * @return array the array of exported content items.
     */
    public function get_all_content_items(): array {
        global $PAGE;
        $allcontentitems = $this->repository->find_all();

        // Export the objects to get the formatted objects for transfer/display.
        $ciexporter = new course_content_items_exporter(
            $allcontentitems,
            ['context' => \context_system::instance()]
        );
        $exported = $ciexporter->export($PAGE->get_renderer('core'));

        // Sort by title for return.
        usort($exported->content_items, function($a, $b) {
            return $a->title > $b->title;
        });

        return $exported->content_items;
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

        // Content items can only originate from modules or submodules.
        $pluginmanager = \core_plugin_manager::instance();
        $components = \core_component::get_component_list();
        $parents = [];
        foreach ($allcontentitems as $contentitem) {
            if (!in_array($contentitem->get_component_name(), array_keys($components['mod']))) {
                // It could be a subplugin.
                $info = $pluginmanager->get_plugin_info($contentitem->get_component_name());
                if (!is_null($info)) {
                    $parent = $info->get_parent_plugin();
                    if ($parent != false) {
                        if (in_array($parent, array_keys($components['mod']))) {
                            $parents[$contentitem->get_component_name()] = $parent;
                            continue;
                        }
                    }
                }
                throw new \moodle_exception('Only modules and submodules can generate content items. \''
                    . $contentitem->get_component_name() . '\' is neither.');
            }
            $parents[$contentitem->get_component_name()] = $contentitem->get_component_name();
        }

        // Now, check access to these items for the user.
        $availablecontentitems = array_filter($allcontentitems, function($contentitem) use ($course, $user, $parents) {
            // Check the parent module access for the user.
            return course_allowed_module($course, explode('_', $parents[$contentitem->get_component_name()])[1], $user);
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
