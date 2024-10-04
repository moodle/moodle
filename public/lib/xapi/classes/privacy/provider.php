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

namespace core_xapi\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;

/**
 * Privacy implementation for core xAPI Library.
 *
 * @package    core_xapi
 * @category   privacy
 * @copyright  2020 Ferran Recio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\subsystem\plugin_provider,
    \core_privacy\local\request\shared_userlist_provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('xapi_states', [
                'component' => 'privacy:metadata:component',
                'userid' => 'privacy:metadata:userid',
                'itemid' => 'privacy:metadata:itemid',
                'stateid' => 'privacy:metadata:stateid',
                'statedata' => 'privacy:metadata:statedata',
                'registration' => 'privacy:metadata:registration',
                'timecreated' => 'privacy:metadata:timecreated',
                'timemodified' => 'privacy:metadata:timemodified',
            ], 'privacy:metadata:xapi_states');

        return $collection;
    }

    /**
     * Provide a list of contexts which have xAPI for the user, in the respective area (component/itemtype combination).
     *
     * This method is to be called by consumers of the xAPI subsystem (plugins), in their get_contexts_for_userid() method,
     * to add the contexts for items which may have xAPI data, but would normally not be reported as having user data by the
     * plugin responsible for them.
     *
     * @param \core_privacy\local\request\contextlist $contextlist
     * @param int $userid The id of the user in scope.
     * @param string $component the frankenstyle component name.
     */
    public static function add_contexts_for_userid(
            \core_privacy\local\request\contextlist $contextlist,
            int $userid,
            string $component) {
        $sql = "SELECT ctx.id
                  FROM {xapi_states} xs
                  JOIN {context} ctx
                    ON ctx.id = xs.itemid
                 WHERE xs.userid = :userid
                   AND xs.component = :component";

        $params = ['userid' => $userid, 'component' => $component];

        $contextlist->add_from_sql($sql, $params);
    }

    /**
     * Add users to a userlist who have xAPI within the specified context.
     *
     * @param \core_privacy\local\request\userlist $userlist The userlist to add the users to.
     * @return void
     */
    public static function add_userids_for_context(\core_privacy\local\request\userlist $userlist) {
        if (empty($userlist)) {
            return;
        }

        $params = [
            'contextid' => $userlist->get_context()->id,
            'component' => $userlist->get_component()
        ];

        $sql = "SELECT xs.userid
                  FROM {xapi_states} xs
                  JOIN {context} ctx
                    ON ctx.id = xs.itemid
                 WHERE ctx.id = :contextid
                   AND xs.component = :component";

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Get xAPI states data for the specified user in the specified component and item ID.
     *
     * @param int $userid The id of the user in scope.
     * @param string $component The component name.
     * @param int $itemid The item ID.
     * @return array|null
     */
    public static function get_xapi_states_for_user(int $userid, string $component, int $itemid) {
        global $DB;

        $params = [
            'userid' => $userid,
            'component' => $component,
            'itemid' => $itemid,
        ];

        if (!$states = $DB->get_records('xapi_states', $params)) {
            return;
        }

        $result = [];
        foreach ($states as $state) {
            $result[] = [
                'statedata' => $state->statedata,
                'timecreated' => transform::datetime($state->timecreated),
                'timemodified' => transform::datetime($state->timemodified)
            ];
        }

        return $result;
    }

    /**
     * Delete all xAPI states for all users in the specified contexts, and component area.
     *
     * @param \context $context The context to which deletion is scoped.
     * @param string $component The component name.
     * @throws \dml_exception if any errors are encountered during deletion.
     */
    public static function delete_states_for_all_users(\context $context, string $component) {
        global $DB;

        $params = [
            'component' => $component,
        ];

        $select = "component = :component";

        if (!empty($context)) {
            $select .= " AND itemid = :itemid";
            $params['itemid'] = $context->id;
        }
        $DB->delete_records_select('xapi_states', $select, $params);
    }

    /**
     * Delete all xAPI states for the specified users in the specified context, component area and item type.
     *
     * @param \core_privacy\local\request\approved_userlist $userlist The approved contexts and user information
     * to delete information for.
     * @param int $itemid Optional itemid associated with component.
     * @throws \dml_exception if any errors are encountered during deletion.
     */
    public static function delete_states_for_userlist(\core_privacy\local\request\approved_userlist $userlist, int $itemid = 0) {
        global $DB;

        $userids = $userlist->get_userids();

        if (empty($userids)) {
            return;
        }

        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        $params = [
            'component' => $userlist->get_component(),
        ];

        $params += $userparams;
        $select = "component = :component AND userid $usersql";

        if (!empty($itemid)) {
            $select .= " AND itemid = :itemid";
            $params['itemid'] = $itemid;
        }

        $DB->delete_records_select('xapi_states', $select, $params);
    }

    /**
     * Delete all xAPI states for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     * @param string $component The component name.
     * @param int $itemid Optional itemid associated with component.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function delete_states_for_user(approved_contextlist $contextlist, string $component, int $itemid = 0) {
        global $DB;

        $userid = $contextlist->get_user()->id;

        $params = [
            'userid' => $userid,
            'component' => $component,
        ];

        $select = "userid = :userid AND component = :component";

        if (!empty($itemid)) {
            $select .= " AND itemid = :itemid";
            $params['itemid'] = $itemid;
        }

        $DB->delete_records_select('xapi_states', $select, $params);
    }
}
