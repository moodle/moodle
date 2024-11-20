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
use core_courseformat\sectiondelegate;

/**
 * The content_item_service class, providing the api for interacting with content items.
 *
 * @copyright  2020 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_item_service {

    /** @var content_item_readonly_repository_interface $repository a repository for content items. */
    private $repository;

    /** string the component for this favourite. */
    public const COMPONENT = 'core_course';
    /** string the favourite prefix itemtype in the favourites table. */
    public const FAVOURITE_PREFIX = 'contentitem_';
    /** string the recommendation prefix itemtype in the favourites table. */
    public const RECOMMENDATION_PREFIX = 'recommend_';
    /** string the cache name for recommendations. */
    public const RECOMMENDATION_CACHE = 'recommendation_favourite_course_content_items';

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

        $favourites = $this->get_content_favourites(self::FAVOURITE_PREFIX, \context_user::instance($user->id));

        $favcache->set($key, $favourites);
        return $favourites;
    }

    /**
     * Returns an array of objects representing recommended content items.
     *
     * Each object contains the following properties:
     * itemtype: a string containing the 'itemtype' key used by the favourites subsystem.
     * ids[]: an array of ids, representing the content items within a component.
     *
     * Since two components can return (via their hook implementation) the same id, the itemtype is used for uniqueness.
     *
     * @return array
     */
    private function get_recommendations(): array {
        global $CFG;

        $recommendationcache = \cache::make('core', self::RECOMMENDATION_CACHE);
        $key = $CFG->siteguest;
        $favmods = $recommendationcache->get($key);
        if ($favmods !== false) {
            return $favmods;
        }

        // Make sure the guest user exists in the database.
        if (!\core_user::get_user($CFG->siteguest)) {
            throw new \coding_exception('The guest user does not exist in the database.');
        }

        // Make sure the guest user context exists.
        if (!$guestusercontext = \context_user::instance($CFG->siteguest, false)) {
            throw new \coding_exception('The guest user context does not exist.');
        }

        $favourites = $this->get_content_favourites(self::RECOMMENDATION_PREFIX, $guestusercontext);

        $recommendationcache->set($CFG->siteguest, $favourites);
        return $favourites;
    }

    /**
     * Gets content favourites from the favourites system depending on the area.
     *
     * @param  string        $prefix      Prefix for the item type.
     * @param  \context_user $usercontext User context for the favourite
     * @return array An array of favourite objects.
     */
    private function get_content_favourites(string $prefix, \context_user $usercontext): array {
        // Get all modules and any submodules which implement get_course_content_items() hook.
        // This gives us the set of all itemtypes which we'll use to register favourite content items.
        // The ids that each plugin returns will be used together with the itemtype to uniquely identify
        // each content item for favouriting.
        $pluginmanager = \core_plugin_manager::instance();
        $plugins = $pluginmanager->get_plugins_of_type('mod');
        $itemtypes = [];
        foreach ($plugins as $plugin) {
            // Add the mod itself.
            $itemtypes[] = $prefix . 'mod_' . $plugin->name;

            // Add any subplugins to the list of item types.
            $subplugins = $pluginmanager->get_subplugins_of_plugin('mod_' . $plugin->name);
            foreach ($subplugins as $subpluginname => $subplugininfo) {
                try {
                    if (component_callback_exists($subpluginname, 'get_course_content_items')) {
                        $itemtypes[] = $prefix . $subpluginname;
                    }
                } catch (\moodle_exception $e) {
                    debugging('Cannot get_course_content_items: ' . $e->getMessage(), DEBUG_DEVELOPER);
                }
            }
        }

        $ufservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);
        $favourites = [];
        $favs = $ufservice->find_all_favourites(self::COMPONENT, $itemtypes);
        $favsreduced = array_reduce($favs, function($carry, $item) {
            $carry[$item->itemtype][$item->itemid] = 0;
            return $carry;
        }, []);

        foreach ($itemtypes as $type) {
            $favourites[] = (object) [
                'itemtype' => $type,
                'ids' => isset($favsreduced[$type]) ? array_keys($favsreduced[$type]) : []
            ];
        }
        return $favourites;
    }

    /**
     * Get all content items which may be added to courses, irrespective of course caps, for site admin views, etc.
     *
     * @param \stdClass $user the user object.
     * @return array the array of exported content items.
     */
    public function get_all_content_items(\stdClass $user): array {
        $allcontentitems = $this->repository->find_all();

        return $this->export_content_items($user, $allcontentitems);
    }

    /**
     * Get content items which name matches a certain pattern and may be added to courses,
     * irrespective of course caps, for site admin views, etc.
     *
     * @param \stdClass $user The user object.
     * @param string $pattern The search pattern.
     * @return array The array of exported content items.
     */
    public function get_content_items_by_name_pattern(\stdClass $user, string $pattern): array {
        $allcontentitems = $this->repository->find_all();

        $filteredcontentitems = array_filter($allcontentitems, function($contentitem) use ($pattern) {
            return preg_match("/$pattern/i", $contentitem->get_title()->get_value());
        });

        return $this->export_content_items($user, $filteredcontentitems);
    }

    /**
     * Export content items.
     *
     * @param \stdClass $user The user object.
     * @param array $contentitems The content items array.
     * @return array The array of exported content items.
     */
    private function export_content_items(\stdClass $user, $contentitems) {
        global $PAGE;

        // Export the objects to get the formatted objects for transfer/display.
        $favourites = $this->get_favourite_content_items_for_user($user);
        $recommendations = $this->get_recommendations();
        $ciexporter = new course_content_items_exporter(
            $contentitems,
            [
                'context' => \context_system::instance(),
                'favouriteitems' => $favourites,
                'recommended' => $recommendations
            ]
        );
        $exported = $ciexporter->export($PAGE->get_renderer('core'));

        // Sort by title for return.
        \core_collator::asort_objects_by_property($exported->content_items, 'title');
        return array_values($exported->content_items);
    }

    /**
     * Return a representation of the available content items, for a user in a course.
     *
     * @param \stdClass $user the user to check access for.
     * @param \stdClass $course the course to scope the content items to.
     * @param array $linkparams the desired section to return to.
     * @param \section_info|null $section_info the section we want to fetch the modules for.
     * @return \stdClass[] the content items, scoped to a course.
     */
    public function get_content_items_for_user_in_course(\stdClass $user, \stdClass $course, array $linkparams = [], ?\section_info $sectioninfo = null): array {
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

        $format = course_get_format($course);
        $maxsectionsreached = ($format->get_last_section_number() >= $format->get_max_sections());

        // Now, check there is no delegated section into a delegated section.
        if (is_null($sectioninfo) || $sectioninfo->is_delegated() || $maxsectionsreached) {
            $availablecontentitems = array_filter($availablecontentitems, function($contentitem){
                return !sectiondelegate::has_delegate_class($contentitem->get_component_name());
            });
        }

        // Add the link params to the link, if any have been provided.
        if (!empty($linkparams)) {
            $availablecontentitems = array_map(function ($item) use ($linkparams) {
                $item->get_link()->params($linkparams);
                return $item;
            }, $availablecontentitems);
        }

        // Export the objects to get the formatted objects for transfer/display.
        $favourites = $this->get_favourite_content_items_for_user($user);
        $recommended = $this->get_recommendations();
        $ciexporter = new course_content_items_exporter(
            $availablecontentitems,
            [
                'context' => \context_course::instance($course->id),
                'favouriteitems' => $favourites,
                'recommended' => $recommended
            ]
        );
        $exported = $ciexporter->export($PAGE->get_renderer('course'));

        // Sort by title for return.
        \core_collator::asort_objects_by_property($exported->content_items, 'title');

        return array_values($exported->content_items);
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
        $itemtype = self::FAVOURITE_PREFIX . $componentname;

        $ufservice->create_favourite(self::COMPONENT, $itemtype, $contentitemid, $usercontext);

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
        $itemtype = self::FAVOURITE_PREFIX . $componentname;

        $ufservice->delete_favourite(self::COMPONENT, $itemtype, $contentitemid, $usercontext);

        $favcache = \cache::make('core', 'user_favourite_course_content_items');
        $favcache->delete($user->id);

        $items = $this->get_all_content_items($user);
        return $items[array_search($contentitemid, array_column($items, 'id'))];
    }

    /**
     * Toggle an activity to being recommended or not.
     *
     * @param  string $itemtype The component such as mod_assign, or assignsubmission_file
     * @param  int    $itemid   The id related to this component item.
     * @return bool True on creating a favourite, false on deleting it.
     */
    public function toggle_recommendation(string $itemtype, int $itemid): bool {
        global $CFG;

        $context = \context_system::instance();

        $itemtype = self::RECOMMENDATION_PREFIX . $itemtype;

        // Favourites are created using a user context. We'll use the site guest user ID as that should not change and there
        // can be only one.
        $usercontext = \context_user::instance($CFG->siteguest);

        $recommendationcache = \cache::make('core', self::RECOMMENDATION_CACHE);

        $favouritefactory = \core_favourites\service_factory::get_service_for_user_context($usercontext);
        if ($favouritefactory->favourite_exists(self::COMPONENT, $itemtype, $itemid, $context)) {
            $favouritefactory->delete_favourite(self::COMPONENT, $itemtype, $itemid, $context);
            $result = $recommendationcache->delete($CFG->siteguest);
            return false;
        } else {
            $favouritefactory->create_favourite(self::COMPONENT, $itemtype, $itemid, $context);
            $result = $recommendationcache->delete($CFG->siteguest);
            return true;
        }
    }
}
