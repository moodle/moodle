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
     * Returns an array of objects representing favourited content items.
     *
     * Each object contains the following properties:
     * itemtype: a string containing the 'itemtype' key used by the favourites subsystem.
     * ids[]: an array of ids, representing the content items within a component.
     *
     * Since two components can return (via their hook implementation) the same id, the itemtype is used for uniqueness.
     *
     * @param \stdClass $user
     * @return array
     */
    private function get_favourite_content_items_for_user(\stdClass $user): array {
        $favcache = \cache::make('core', 'user_favourite_course_content_items');
        $key = $user->id;
        $favmods = $favcache->get($key);
        if ($favmods !== false) {
            return $favmods;
        }

        // Get all modules and any submodules which implement get_course_content_items() hook.
        // This gives us the set of all itemtypes which we'll use to register favourite content items.
        // The ids that each plugin returns will be used together with the itemtype to uniquely identify
        // each content item for favouriting.
        $pluginmanager = \core_plugin_manager::instance();
        $plugins = $pluginmanager->get_plugins_of_type('mod');
        $itemtypes = [];
        foreach ($plugins as $plugin) {
            // Add the mod itself.
            $itemtypes[] = 'contentitem_mod_' . $plugin->name;

            // Add any subplugins to the list of item types.
            $subplugins = $pluginmanager->get_subplugins_of_plugin('mod_' . $plugin->name);
            foreach ($subplugins as $subpluginname => $subplugininfo) {
                if (component_callback_exists($subpluginname, 'get_course_content_items')) {
                    $itemtypes[] = 'contentitem_' . $subpluginname;
                }
            }
        }

        $ufservice = \core_favourites\service_factory::get_service_for_user_context(\context_user::instance($user->id));
        $favourites = [];
        foreach ($itemtypes as $itemtype) {
            $favs = $ufservice->find_favourites_by_type('core_course', $itemtype);
            $favobj = (object) ['itemtype' => $itemtype, 'ids' => array_column($favs, 'itemid')];
            $favourites[] = $favobj;
        }
        $favcache->set($key, $favourites);
        return $favourites;
    }

    /**
     * Get all content items which may be added to courses, irrespective of course caps, for site admin views, etc.
     *
     * @param \stdClass $user the user object.
     * @return array the array of exported content items.
     */
    public function get_all_content_items(\stdClass $user): array {
        global $PAGE;
        $allcontentitems = $this->repository->find_all();

        // Export the objects to get the formatted objects for transfer/display.
        $favourites = $this->get_favourite_content_items_for_user($user);
        $ciexporter = new course_content_items_exporter(
            $allcontentitems,
            [
                'context' => \context_system::instance(),
                'favouriteitems' => $favourites
            ]
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
        $favourites = $this->get_favourite_content_items_for_user($user);
        $ciexporter = new course_content_items_exporter(
            $availablecontentitems,
            [
                'context' => \context_course::instance($course->id),
                'favouriteitems' => $favourites
            ]
        );
        $exported = $ciexporter->export($PAGE->get_renderer('course'));

        // Sort by title for return.
        usort($exported->content_items, function($a, $b) {
            return $a->title > $b->title;
        });

        return $exported->content_items;
    }

    /**
     * Add a content item to a user's favourites.
     *
     * @param \stdClass $user the user whose favourite this is.
     * @param string $componentname the name of the component from which the content item originates.
     * @param int $contentitemid the id of the content item.
     * @return \stdClass the exported content item.
     */
    public function add_to_user_favourites(\stdClass $user, string $componentname, int $contentitemid): \stdClass {
        $usercontext = \context_user::instance($user->id);
        $ufservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);

        // Because each plugin decides its own ids for content items, a combination of
        // itemtype and id is used to guarantee uniqueness across all content items.
        $itemtype = 'contentitem_' . $componentname;

        $ufservice->create_favourite('core_course', $itemtype, $contentitemid, $usercontext);

        $favcache = \cache::make('core', 'user_favourite_course_content_items');
        $favcache->delete($user->id);

        $items = $this->get_all_content_items($user);
        return $items[array_search($contentitemid, array_column($items, 'id'))];
    }

    /**
     * Remove the content item from a user's favourites.
     *
     * @param \stdClass $user the user whose favourite this is.
     * @param string $componentname the name of the component from which the content item originates.
     * @param int $contentitemid the id of the content item.
     * @return \stdClass the exported content item.
     */
    public function remove_from_user_favourites(\stdClass $user, string $componentname, int $contentitemid): \stdClass {
        $usercontext = \context_user::instance($user->id);
        $ufservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);

        // Because each plugin decides its own ids for content items, a combination of
        // itemtype and id is used to guarantee uniqueness across all content items.
        $itemtype = 'contentitem_' . $componentname;

        $ufservice->delete_favourite('core_course', $itemtype, $contentitemid, $usercontext);

        $favcache = \cache::make('core', 'user_favourite_course_content_items');
        $favcache->delete($user->id);

        $items = $this->get_all_content_items($user);
        return $items[array_search($contentitemid, array_column($items, 'id'))];
    }
}
