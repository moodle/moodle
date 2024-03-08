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
 * Contains the user_favourite_service class, part of the service layer for the favourites subsystem.
 *
 * @package   core_favourites
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_favourites\local\service;
use \core_favourites\local\entity\favourite;
use \core_favourites\local\repository\favourite_repository_interface;

defined('MOODLE_INTERNAL') || die();

/**
 * Class service, providing an single API for interacting with the favourites subsystem for a SINGLE USER.
 *
 * This class is responsible for exposing key operations (add, remove, find) and enforces any business logic necessary to validate
 * authorization/data integrity for these operations.
 *
 * All object persistence is delegated to the favourite_repository_interface object.
 *
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_favourite_service {

    /** @var favourite_repository_interface $repo the favourite repository object. */
    protected $repo;

    /** @var int $userid the id of the user to which this favourites service is scoped. */
    protected $userid;

    /**
     * The user_favourite_service constructor.
     *
     * @param \context_user $usercontext The context of the user to which this service operations are scoped.
     * @param \core_favourites\local\repository\favourite_repository_interface $repository a favourites repository.
     */
    public function __construct(\context_user $usercontext, favourite_repository_interface $repository) {
        $this->repo = $repository;
        $this->userid = $usercontext->instanceid;
    }

    /**
     * Favourite an item defined by itemid/context, in the area defined by component/itemtype.
     *
     * @param string $component the frankenstyle component name.
     * @param string $itemtype the type of the item being favourited.
     * @param int $itemid the id of the item which is to be favourited.
     * @param \context $context the context in which the item is to be favourited.
     * @param int|null $ordering optional ordering integer used for sorting the favourites in an area.
     * @return favourite the favourite, once created.
     * @throws \moodle_exception if the component name is invalid, or if the repository encounters any errors.
     */
    public function create_favourite(string $component, string $itemtype, int $itemid, \context $context,
            int $ordering = null): favourite {
        // Access: Any component can ask to favourite something, we can't verify access to that 'something' here though.

        // Validate the component name.
        if (!in_array($component, \core_component::get_component_names())) {
            throw new \moodle_exception("Invalid component name '$component'");
        }

        $favourite = new favourite($component, $itemtype, $itemid, $context->id, $this->userid);
        $favourite->ordering = $ordering > 0 ? $ordering : null;
        return $this->repo->add($favourite);
    }

    /**
     * Find a list of favourites, by type, where type is the component/itemtype pair.
     *
     * E.g. "Find all favourite courses" might result in:
     * $favcourses = find_favourites_by_type('core_course', 'course');
     *
     * @param string $component the frankenstyle component name.
     * @param string $itemtype the type of the favourited item.
     * @param int $limitfrom optional pagination control for returning a subset of records, starting at this point.
     * @param int $limitnum optional pagination control for returning a subset comprising this many records.
     * @return array the list of favourites found.
     * @throws \moodle_exception if the component name is invalid, or if the repository encounters any errors.
     */
    public function find_favourites_by_type(string $component, string $itemtype, int $limitfrom = 0, int $limitnum = 0): array {
        if (!in_array($component, \core_component::get_component_names())) {
            throw new \moodle_exception("Invalid component name '$component'");
        }
        return $this->repo->find_by(
            [
                'userid' => $this->userid,
                'component' => $component,
                'itemtype' => $itemtype
            ],
            $limitfrom,
            $limitnum
        );
    }

    /**
     * Find a list of favourites, by multiple types within a component.
     *
     * E.g. "Find all favourites in the activity chooser" might result in:
     * $favcourses = find_all_favourites('core_course', ['contentitem_mod_assign');
     *
     * @param string $component the frankenstyle component name.
     * @param array $itemtypes optional the type of the favourited item.
     * @param int $limitfrom optional pagination control for returning a subset of records, starting at this point.
     * @param int $limitnum optional pagination control for returning a subset comprising this many records.
     * @return array the list of favourites found.
     * @throws \moodle_exception if the component name is invalid, or if the repository encounters any errors.
     */
    public function find_all_favourites(string $component, array $itemtypes = [], int $limitfrom = 0, int $limitnum = 0): array {
        if (!in_array($component, \core_component::get_component_names())) {
            throw new \moodle_exception("Invalid component name '$component'");
        }
        $params = [
            'userid' => $this->userid,
            'component' => $component,
        ];
        if ($itemtypes) {
            $params['itemtype'] = $itemtypes;
        }

        return $this->repo->find_by(
            $params,
            $limitfrom,
            $limitnum
        );
    }

    /**
     * Returns the SQL required to include favourite information for a given component/itemtype combination.
     *
     * Generally, find_favourites_by_type() is the recommended way to fetch favourites.
     *
     * This method is used to include favourite information in external queries, for items identified by their
     * component and itemtype, matching itemid to the $joinitemid, and for the user to which this service is scoped.
     *
     * It uses a LEFT JOIN to preserve the original records. If you wish to restrict your records, please consider using a
     * "WHERE {$tablealias}.id IS NOT NULL" in your query.
     *
     * Example usage:
     *
     * list($sql, $params) = $service->get_join_sql_by_type('core_message', 'message_conversations', 'myfavouritetablealias',
     *                                                      'conv.id');
     * Results in $sql:
     *     "LEFT JOIN {favourite} fav
     *             ON fav.component = :favouritecomponent
     *            AND fav.itemtype = :favouriteitemtype
     *            AND fav.userid = 1234
     *            AND fav.itemid = conv.id"
     * and $params:
     *     ['favouritecomponent' => 'core_message', 'favouriteitemtype' => 'message_conversations']
     *
     * @param string $component the frankenstyle component name.
     * @param string $itemtype the type of the favourited item.
     * @param string $tablealias the desired alias for the favourites table.
     * @param string $joinitemid the table and column identifier which the itemid is joined to. E.g. conversation.id.
     * @return array the list of sql and params, in the format [$sql, $params].
     */
    public function get_join_sql_by_type(string $component, string $itemtype, string $tablealias, string $joinitemid): array {
        $sql = " LEFT JOIN {favourite} {$tablealias}
                        ON {$tablealias}.component = :favouritecomponent
                       AND {$tablealias}.itemtype = :favouriteitemtype
                       AND {$tablealias}.userid = {$this->userid}
                       AND {$tablealias}.itemid = {$joinitemid} ";

        $params = [
            'favouritecomponent' => $component,
            'favouriteitemtype' => $itemtype,
        ];

        return [$sql, $params];
    }

    /**
     * Delete a favourite item from an area and from within a context.
     *
     * E.g. delete a favourite course from the area 'core_course', 'course' with itemid 3 and from within the CONTEXT_USER context.
     *
     * @param string $component the frankenstyle component name.
     * @param string $itemtype the type of the favourited item.
     * @param int $itemid the id of the item which was favourited (not the favourite's id).
     * @param \context $context the context of the item which was favourited.
     * @throws \moodle_exception if the user does not control the favourite, or it doesn't exist.
     */
    public function delete_favourite(string $component, string $itemtype, int $itemid, \context $context) {
        if (!in_array($component, \core_component::get_component_names())) {
            throw new \moodle_exception("Invalid component name '$component'");
        }

        // Business logic: check the user owns the favourite.
        try {
            $favourite = $this->repo->find_favourite($this->userid, $component, $itemtype, $itemid, $context->id);
        } catch (\moodle_exception $e) {
            throw new \moodle_exception("Favourite does not exist for the user. Cannot delete.");
        }

        $this->repo->delete($favourite->id);
    }

    /**
     * Check whether an item has been marked as a favourite in the respective area.
     *
     * @param string $component the frankenstyle component name.
     * @param string $itemtype the type of the favourited item.
     * @param int $itemid the id of the item which was favourited (not the favourite's id).
     * @param \context $context the context of the item which was favourited.
     * @return bool true if the item is favourited, false otherwise.
     */
    public function favourite_exists(string $component, string $itemtype, int $itemid, \context $context): bool {
        return $this->repo->exists_by(
            [
                'userid' => $this->userid,
                'component' => $component,
                'itemtype' => $itemtype,
                'itemid' => $itemid,
                'contextid' => $context->id
            ]
        );
    }

    /**
     * Get the favourite.
     *
     * @param string $component the frankenstyle component name.
     * @param string $itemtype the type of the favourited item.
     * @param int $itemid the id of the item which was favourited (not the favourite's id).
     * @param \context $context the context of the item which was favourited.
     * @return favourite|null
     */
    public function get_favourite(string $component, string $itemtype, int $itemid, \context $context) {
        try {
            return $this->repo->find_favourite(
                $this->userid,
                $component,
                $itemtype,
                $itemid,
                $context->id
            );
        } catch (\dml_missing_record_exception $e) {
            return null;
        }
    }

    /**
     * Count the favourite by item type.
     *
     * @param string $component the frankenstyle component name.
     * @param string $itemtype the type of the favourited item.
     * @param \context|null $context the context of the item which was favourited.
     * @return int
     */
    public function count_favourites_by_type(string $component, string $itemtype, \context $context = null) {
        $criteria = [
            'userid' => $this->userid,
            'component' => $component,
            'itemtype' => $itemtype
        ];

        if ($context) {
            $criteria['contextid'] = $context->id;
        }

        return $this->repo->count_by($criteria);
    }
}
