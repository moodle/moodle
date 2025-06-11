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
 * Privacy class for requesting user data for the favourites subsystem.
 *
 * @package    core_favourites
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_favourites\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\context;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\transform;

/**
 * Privacy class for requesting user data.
 *
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\subsystem\plugin_provider,
        \core_privacy\local\request\shared_userlist_provider {

    /**
     * Returns metadata about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        return $collection->add_database_table('favourite', [
            'userid' => 'privacy:metadata:favourite:userid',
            'component' => 'privacy:metadata:favourite:component',
            'itemtype' => 'privacy:metadata:favourite:itemtype',
            'itemid' => 'privacy:metadata:favourite:itemid',
            'ordering' => 'privacy:metadata:favourite:ordering',
            'timecreated' => 'privacy:metadata:favourite:timecreated',
            'timemodified' => 'privacy:metadata:favourite:timemodified',
        ], 'privacy:metadata:favourite');
    }

    /**
     * Provide a list of contexts which have favourites for the user, in the respective area (component/itemtype combination).
     *
     * This method is to be called by consumers of the favourites subsystem (plugins), in their get_contexts_for_userid() method,
     * to add the contexts for items which may have been favourited, but would normally not be reported as having user data by the
     * plugin responsible for them.
     *
     * Consider an example: Favourite courses.
     * Favourite courses will be handled by the core_course subsystem and courses can be favourited at site context.
     *
     * Now normally, the course provider method get_contexts_for_userid() would report the context of any courses the user is in.
     * Then, we'd export data for those contexts. This won't include courses the user has favourited, but is not a member of.
     *
     * To report the full list, the course provider needs to be made aware of the contexts of any courses the user may have marked
     * as favourites. Course will need to ask th favourites subsystem for this - a call to add_contexts_for_userid($userid).
     *
     * Once called, if a course has been marked as a favourite, at site context, then we'd return the site context. During export,
     * the consumer (course), just looks at all contexts and decides whether to export favourite courses for each one.
     *
     * @param \core_privacy\local\request\contextlist $contextlist
     * @param int $userid The id of the user in scope.
     * @param string $component the frankenstyle component name.
     * @param string $itemtype the type of the favourited items.
     */
    public static function add_contexts_for_userid(\core_privacy\local\request\contextlist $contextlist, int $userid,
                                                   string $component, ?string $itemtype = null) {
        $sql = "SELECT contextid
                  FROM {favourite} f
                 WHERE userid = :userid
                   AND component = :component";

        $params = ['userid' => $userid, 'component' => $component];

        if (!is_null($itemtype)) {
            $sql .= " AND itemtype = :itemtype";
            $params['itemtype'] = $itemtype;
        }

        $contextlist->add_from_sql($sql, $params);
    }

    /**
     * Add users to a userlist who have favourites within the specified context.
     *
     * @param \core_privacy\local\request\userlist $userlist The userlist to add the users to.
     * @param string $itemtype the type of the favourited items.
     * @return void
     */
    public static function add_userids_for_context(\core_privacy\local\request\userlist $userlist,
                                                   ?string $itemtype = null) {
        if (empty($userlist)) {
            return;
        }

        $params = [
            'contextid' => $userlist->get_context()->id,
            'component' => $userlist->get_component()
        ];

        $sql = "SELECT userid
                  FROM {favourite}
                 WHERE contextid = :contextid
                       AND component = :component";

        if (!is_null($itemtype)) {
            $sql .= " AND itemtype = :itemtype";
            $params['itemtype'] = $itemtype;
        }

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Get favourites data for the specified user in the specified component, item type and item ID.
     *
     * @param int $userid The id of the user in scope.
     * @param \context $context The context to which data is scoped.
     * @param string $component The favourite's component name.
     * @param string $itemtype The favourite's item type.
     * @param int $itemid The favourite's item ID.
     * @return array|null
     */
    public static function get_favourites_info_for_user(int $userid, \context $context,
                                                        string $component, string $itemtype, int $itemid) {
        global $DB;

        $params = [
            'userid' => $userid,
            'component' => $component,
            'itemtype' => $itemtype,
            'itemid' => $itemid,
            'contextid' => $context->id
        ];

        if (!$favourited = $DB->get_record('favourite', $params)) {
            return;
        }

        return [
            'starred' => transform::yesno(true),
            'ordering' => $favourited->ordering,
            'timecreated' => transform::datetime($favourited->timecreated),
            'timemodified' => transform::datetime($favourited->timemodified)
        ];
    }

    /**
     * Delete all favourites for all users in the specified contexts, and component area.
     *
     * @param \context $context The context to which deletion is scoped.
     * @param string $component The favourite's component name.
     * @param string $itemtype The favourite's itemtype.
     * @param int $itemid Optional itemid associated with component.
     * @throws \dml_exception if any errors are encountered during deletion.
     */
    public static function delete_favourites_for_all_users(\context $context, string $component, string $itemtype,
                                                           int $itemid = 0) {
        global $DB;

        $params = [
            'component' => $component,
            'itemtype' => $itemtype,
            'contextid' => $context->id
        ];

        $select = "component = :component AND itemtype =:itemtype AND contextid = :contextid";

        if (!empty($itemid)) {
            $select .= " AND itemid = :itemid";
            $params['itemid'] = $itemid;
        }
        $DB->delete_records_select('favourite', $select, $params);
    }

    /**
     * Delete all favourites for the specified users in the specified context, component area and item type.
     *
     * @param \core_privacy\local\request\approved_userlist $userlist The approved contexts and user information
     * to delete information for.
     * @param string $itemtype The favourite's itemtype.
     * @param int $itemid Optional itemid associated with component.
     * @throws \dml_exception if any errors are encountered during deletion.
     */
    public static function delete_favourites_for_userlist(\core_privacy\local\request\approved_userlist $userlist,
                                                          string $itemtype, int $itemid = 0) {
        global $DB;

        $userids = $userlist->get_userids();

        if (empty($userids)) {
            return;
        }

        $context = $userlist->get_context();
        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        $params = [
            'component' => $userlist->get_component(),
            'itemtype' => $itemtype,
            'contextid' => $context->id
        ];

        $params += $userparams;
        $select = "component = :component AND itemtype = :itemtype AND contextid = :contextid AND userid $usersql";

        if (!empty($itemid)) {
            $select .= " AND itemid = :itemid";
            $params['itemid'] = $itemid;
        }

        $DB->delete_records_select('favourite', $select, $params);
    }

    /**
     * Delete all favourites for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     * @param string $component The favourite's component name.
     * @param string $itemtype The favourite's itemtype.
     * @param int $itemid Optional itemid associated with component.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function delete_favourites_for_user(approved_contextlist $contextlist, string $component, string $itemtype,
                                                      int $itemid = 0) {
        global $DB;

        $userid = $contextlist->get_user()->id;

        list($insql, $inparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $params = [
            'userid' => $userid,
            'component' => $component,
            'itemtype' => $itemtype,
        ];
        $params += $inparams;

        $select = "userid = :userid AND component = :component AND itemtype =:itemtype AND contextid $insql";

        if (!empty($itemid)) {
            $select .= " AND itemid = :itemid";
            $params['itemid'] = $itemid;
        }

        $DB->delete_records_select('favourite', $select, $params);
    }
}
