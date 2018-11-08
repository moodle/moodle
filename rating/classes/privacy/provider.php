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
 * Privacy Subsystem implementation for core_ratings.
 *
 * @package    core_rating
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_rating\privacy;

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\userlist;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/rating/lib.php');

/**
 * Privacy Subsystem implementation for core_ratings.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // The ratings subsystem contains data.
        \core_privacy\local\metadata\provider,

        // The ratings subsystem is only ever used to store data for other components.
        // It does not store any data of its own and does not need to implement the \core_privacy\local\request\subsystem\provider
        // as a result.

        // The ratings subsystem provides a data service to other components.
        \core_privacy\local\request\subsystem\plugin_provider,
        \core_privacy\local\request\shared_userlist_provider
    {

    /**
     * Returns metadata about the ratings subsystem.
     *
     * @param   collection     $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through the subsystem.
     */
    public static function get_metadata(collection $collection) : collection {
        // The table 'rating' cotains data that a user has entered.
        // It stores the user-entered rating alongside a mapping to describe what was mapped.
        $collection->add_database_table('rating', [
                'rating' => 'privacy:metadata:rating:rating',
                'userid' => 'privacy:metadata:rating:userid',
                'timecreated' => 'privacy:metadata:rating:timecreated',
                'timemodified' => 'privacy:metadata:rating:timemodified',
            ], 'privacy:metadata:rating');

        return $collection;
    }

    /**
     * Export all ratings which match the specified component, areaid, and itemid.
     *
     * If requesting ratings for a users own content, and you wish to include all ratings of that content, specify
     * $onlyuser as false.
     *
     * When requesting ratings for another users content, you should only export the ratings that the specified user
     * made themselves.
     *
     * @param   int         $userid The user whose information is to be exported
     * @param   \context    $context The context being stored.
     * @param   array       $subcontext The subcontext within the context to export this information
     * @param   string      $component The component to fetch data from
     * @param   string      $ratingarea The ratingarea that the data was stored in within the component
     * @param   int         $itemid The itemid within that ratingarea
     * @param   bool        $onlyuser Whether to only export ratings that the current user has made, or all ratings
     */
    public static function export_area_ratings(
        int $userid,
        \context $context,
        array $subcontext,
        string $component,
        string $ratingarea,
        int $itemid,
        bool $onlyuser = true
    ) {
        global $DB;

        $rm = new \rating_manager();
        $ratings = $rm->get_all_ratings_for_item((object) [
            'context' => $context,
            'component' => $component,
            'ratingarea' => $ratingarea,
            'itemid' => $itemid,
        ]);

        if ($onlyuser) {
            $ratings = array_filter($ratings, function($rating) use ($userid){
                return ($rating->userid == $userid);
            });
        }

        if (empty($ratings)) {
            return;
        }

        $toexport = array_map(function($rating) {
            return (object) [
                'rating' => $rating->rating,
                'author' => $rating->userid,
            ];
        }, $ratings);

        $writer = \core_privacy\local\request\writer::with_context($context)
            ->export_related_data($subcontext, 'rating', $toexport);
    }

    /**
     * Get the SQL required to find all submission items where this user has had any involvements.
     *
     * @param   string          $alias      The name of the table alias to use.
     * @param   string          $component  The na eof the component to fetch ratings for.
     * @param   string          $ratingarea The rating area to fetch results for.
     * @param   string          $itemidjoin The right-hand-side of the JOIN ON clause.
     * @param   int             $userid     The ID of the user being stored.
     * @return  \stdClass
     */
    public static function get_sql_join($alias, $component, $ratingarea, $itemidjoin, $userid) {
        static $count = 0;
        $count++;

        // Join the rating table with the specified alias and the relevant join params.
        $join = "LEFT JOIN {rating} {$alias} ON ";
        $join .= "{$alias}.userid = :ratinguserid{$count} AND ";
        $join .= "{$alias}.component = :ratingcomponent{$count} AND ";
        $join .= "{$alias}.ratingarea = :ratingarea{$count} AND ";
        $join .= "{$alias}.itemid = {$itemidjoin}";

        // Match against the specified user.
        $userwhere = "{$alias}.id IS NOT NULL";

        $params = [
            'ratingcomponent' . $count  => $component,
            'ratingarea' . $count       => $ratingarea,
            'ratinguserid' . $count     => $userid,
        ];

        $return = (object) [
            'join' => $join,
            'params' => $params,
            'userwhere' => $userwhere,
        ];
        return $return;
    }

    /**
     * Deletes all ratings for a specified context, component, ratingarea and itemid.
     *
     * Only delete ratings when the item itself was deleted.
     *
     * We never delete ratings for one user but not others - this may affect grades, therefore ratings
     * made by particular user are not considered personal information.
     *
     * @param  \context $context Details about which context to delete ratings for.
     * @param  string $component Component to delete.
     * @param  string $ratingarea Rating area to delete.
     * @param  int $itemid The item ID for use with deletion.
     */
    public static function delete_ratings(\context $context, string $component = null,
            string $ratingarea = null, int $itemid = null) {
        global $DB;

        $options = ['contextid' => $context->id];
        if ($component) {
            $options['component'] = $component;
        }
        if ($ratingarea) {
            $options['ratingarea'] = $ratingarea;
        }
        if ($itemid) {
            $options['itemid'] = $itemid;
        }

        $DB->delete_records('rating', $options);
    }

    /**
     * Deletes all tag instances for given context, component, itemtype using subquery for itemids
     *
     * In most situations you will want to specify $userid as null. Per-user tag instances
     * are possible in Tags API, however there are no components or standard plugins that actually use them.
     *
     * @param  \context $context Details about which context to delete ratings for.
     * @param  string $component Component to delete.
     * @param  string $ratingarea Rating area to delete.
     * @param  string $itemidstest an SQL fragment that the itemid must match. Used
     *      in the query like WHERE itemid $itemidstest. Must use named parameters,
     *      and may not use named parameters called contextid, component or ratingarea.
     * @param array $params any query params used by $itemidstest.
     */
    public static function delete_ratings_select(\context $context, string $component,
             string $ratingarea, $itemidstest, $params = []) {
        global $DB;
        $params += ['contextid' => $context->id, 'component' => $component, 'ratingarea' => $ratingarea];
        $DB->delete_records_select('rating',
            'contextid = :contextid AND component = :component AND ratingarea = :ratingarea AND itemid ' . $itemidstest,
            $params);
    }

    /**
     * Add the list of users who have rated in the specified constraints.
     *
     * @param   userlist    $userlist The userlist to add the users to.
     * @param   string      $alias An alias prefix to use for rating selects to avoid interference with your own sql.
     * @param   string      $component The component to check.
     * @param   string      $area The rating area to check.
     * @param   string      $insql The SQL to use in a sub-select for the itemid query.
     * @param   array       $params The params required for the insql.
     */
    public static function get_users_in_context_from_sql(
            userlist $userlist, string $alias, string $component, string $area, string $insql, $params) {
        // Discussion authors.
        $sql = "SELECT {$alias}.userid
                  FROM {rating} {$alias}
                 WHERE {$alias}.component = :{$alias}component
                   AND {$alias}.ratingarea = :{$alias}ratingarea
                   AND {$alias}.itemid IN ({$insql})";

        $params["{$alias}component"] = $component;
        $params["{$alias}ratingarea"] = $area;

        $userlist->add_from_sql('userid', $sql, $params);
    }
}
