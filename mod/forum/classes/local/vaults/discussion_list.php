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
 * Vault class for a discussion list.
 *
 * @package    mod_forum
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\vaults;

defined('MOODLE_INTERNAL') || die();

use core_group\output\group_details;
use mod_forum\local\vaults\preprocessors\extract_record as extract_record_preprocessor;
use mod_forum\local\vaults\preprocessors\extract_user as extract_user_preprocessor;
use mod_forum\local\renderers\discussion_list as discussion_list_renderer;
use core\dml\table as dml_table;
use stdClass;

/**
 * Discussion list vault.
 *
 * This should be the only place that accessed the database.
 *
 * This uses the repository pattern. See:
 * https://designpatternsphp.readthedocs.io/en/latest/More/Repository/README.html
 *
 * @package    mod_forum
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class discussion_list extends db_table_vault {
    /** The table for this vault */
    private const TABLE = 'forum_discussions';
    /** Alias for first author id */
    private const FIRST_AUTHOR_ID_ALIAS = 'fauserpictureid';
    /** Alias for author fields */
    private const FIRST_AUTHOR_ALIAS = 'fauserrecord';
    /** Alias for last author id */
    private const LATEST_AUTHOR_ID_ALIAS = 'lauserpictureid';
    /** Alias for last author fields */
    private const LATEST_AUTHOR_ALIAS = 'lauserrecord';
    /** Default limit */
    public const PAGESIZE_DEFAULT = 100;

    /** Sort by newest first */
    public const SORTORDER_LASTPOST_DESC = 1;
    /** Sort by oldest first */
    public const SORTORDER_LASTPOST_ASC = 2;
    /** Sort by created desc */
    public const SORTORDER_CREATED_DESC = 3;
    /** Sort by created asc */
    public const SORTORDER_CREATED_ASC = 4;
    /** Sort by number of replies desc */
    public const SORTORDER_REPLIES_DESC = 5;
    /** Sort by number of replies desc */
    public const SORTORDER_REPLIES_ASC = 6;
    /** Sort by discussion name desc */
    public const SORTORDER_DISCUSSION_DESC = 7;
    /** Sort by discussion name asc */
    public const SORTORDER_DISCUSSION_ASC = 8;
    /** Sort by discussion starter's name desc */
    public const SORTORDER_STARTER_DESC = 9;
    /** Sort by discussion starter's name asc */
    public const SORTORDER_STARTER_ASC = 10;
    /** Sort by group name desc */
    public const SORTORDER_GROUP_DESC = 11;
    /** Sort by group name asc */
    public const SORTORDER_GROUP_ASC = 12;

    /**
     * Get the table alias.
     *
     * @return string
     */
    protected function get_table_alias() : string {
        return 'd';
    }

    /**
     * Get the favourite table alias
     *
     * @return string
     */
    protected function get_favourite_alias() : string {
        return 'favalias';
    }

    /**
     * Build the SQL to be used in get_records_sql.
     *
     * @param string|null $wheresql Where conditions for the SQL
     * @param string|null $sortsql Order by conditions for the SQL
     * @param int|null $userid The ID of the user we are performing this query for
     *
     * @return string
     */
    protected function generate_get_records_sql(string $wheresql = null, ?string $sortsql = null, ?int $userid = null) : string {
        $alias = $this->get_table_alias();

        $includefavourites = $userid ? true : false;

        $favsql = '';
        if ($includefavourites) {
            list($favsql, $favparams) = $this->get_favourite_sql($userid);
            foreach ($favparams as $key => $param) {
                $favsql = str_replace(":$key", "'$param'", $favsql);
            }
        }

        // Fetch:
        // - Discussion
        // - First post
        // - Author
        // - Most recent editor.
        $thistable = new dml_table(self::TABLE, $alias, $alias);
        $posttable = new dml_table('forum_posts', 'fp', 'p_');
        $userfieldsapi = \core_user\fields::for_userpic()->including('deleted');
        $firstauthorfields = $userfieldsapi->get_sql('fa', false,
                self::FIRST_AUTHOR_ALIAS, self::FIRST_AUTHOR_ID_ALIAS, false)->selects;
        $latestuserfields = $userfieldsapi->get_sql('la', false,
                self::LATEST_AUTHOR_ALIAS, self::LATEST_AUTHOR_ID_ALIAS, false)->selects;

        $fields = implode(', ', [
            $thistable->get_field_select(),
            $posttable->get_field_select(),
            $firstauthorfields,
            $latestuserfields,
        ]);

        $sortkeys = [
            $this->get_sort_order(self::SORTORDER_REPLIES_DESC, $includefavourites),
            $this->get_sort_order(self::SORTORDER_REPLIES_ASC, $includefavourites)
        ];
        $issortbyreplies = in_array($sortsql, $sortkeys);

        $tables = $thistable->get_from_sql();
        $tables .= ' JOIN ' . $posttable->get_from_sql() . ' ON fp.id = ' . $alias . '.firstpost';
        $tables .= ' JOIN {user} fa ON fa.id = fp.userid';
        $tables .= ' JOIN {user} la ON la.id = ' . $alias . '.usermodified';
        $tables .= $favsql;

        if ($issortbyreplies) {
            // Join the discussion replies.
            $tables .= ' JOIN (
                            SELECT rd.id, COUNT(rp.id) as replycount
                            FROM {forum_discussions} rd
                            LEFT JOIN {forum_posts} rp
                                ON rp.discussion = rd.id AND rp.id != rd.firstpost
                            GROUP BY rd.id
                         ) r ON d.id = r.id';
        }

        $groupsortorders = [
            $this->get_sort_order(self::SORTORDER_GROUP_DESC, $includefavourites),
            $this->get_sort_order(self::SORTORDER_GROUP_ASC, $includefavourites)
        ];
        $sortbygroup = in_array($sortsql, $groupsortorders);
        if ($sortbygroup) {
            $groupstable = new dml_table('groups', 'g', 'g');
            $fields .= ', ' . $groupstable->get_field_select();
            // Join groups.
            $tables .= 'LEFT JOIN {groups} g ON g.id = d.groupid';
        }

        $selectsql = 'SELECT ' . $fields . ' FROM ' . $tables;
        $selectsql .= $wheresql ? ' WHERE ' . $wheresql : '';
        $selectsql .= $sortsql ? ' ORDER BY ' . $sortsql : '';

        return $selectsql;
    }

    /**
     * Build the SQL to be used in count_records_sql.
     *
     * @param string|null $wheresql Where conditions for the SQL
     * @return string
     */
    protected function generate_count_records_sql(string $wheresql = null) : string {
        $alias = $this->get_table_alias();
        $db = $this->get_db();

        $selectsql = "SELECT COUNT(1) FROM {" . self::TABLE . "} {$alias}";
        $selectsql .= $wheresql ? ' WHERE ' . $wheresql : '';

        return $selectsql;
    }

    /**
     * Get a list of preprocessors to execute on the DB results before being converted
     * into entities.
     *
     * @return array
     */
    protected function get_preprocessors() : array {
        return array_merge(
            parent::get_preprocessors(),
            [
                'discussion' => new extract_record_preprocessor(self::TABLE, $this->get_table_alias()),
                'firstpost' => new extract_record_preprocessor('forum_posts', 'p_'),
                'firstpostauthor' => new extract_user_preprocessor(self::FIRST_AUTHOR_ID_ALIAS, self::FIRST_AUTHOR_ALIAS),
                'latestpostauthor' => new extract_user_preprocessor(self::LATEST_AUTHOR_ID_ALIAS, self::LATEST_AUTHOR_ALIAS),
            ]
        );
    }

    /**
     * Convert the DB records into discussion list entities.
     *
     * @param array $results The DB records
     * @return discussion_list[]
     */
    protected function from_db_records(array $results) {
        $entityfactory = $this->get_entity_factory();

        return array_map(function(array $result) use ($entityfactory) {
            [
                'discussion' => $discussion,
                'firstpost' => $firstpost,
                'firstpostauthor' => $firstpostauthor,
                'latestpostauthor' => $latestpostauthor,
            ] = $result;
            return $entityfactory->get_discussion_summary_from_stdclass(
                $discussion,
                $firstpost,
                $firstpostauthor,
                $latestpostauthor
            );
        }, $results);
    }

    /**
     * Get the field to sort by.
     *
     * @param int|null $sortmethod
     * @return string
     */
    protected function get_keyfield(?int $sortmethod) : string {
        global $CFG;

        switch ($sortmethod) {
            case self::SORTORDER_CREATED_DESC:
            case self::SORTORDER_CREATED_ASC:
                return 'fp.created';
            case self::SORTORDER_REPLIES_DESC:
            case self::SORTORDER_REPLIES_ASC:
                return 'replycount';
            case self::SORTORDER_DISCUSSION_DESC:
            case self::SORTORDER_DISCUSSION_ASC:
                return 'dname';
            case self::SORTORDER_STARTER_DESC:
            case self::SORTORDER_STARTER_ASC:
                // We'll sort by the first name field of the discussion starter's name.

                // Let's get the full name display config first.
                $nameformat = $CFG->fullnamedisplay;
                if ($CFG->fullnamedisplay === 'language') {
                    $nameformat = get_string('fullnamedisplay', '', (object)['firstname' => 'firstname', 'lastname' => 'lastname']);
                }
                // Fetch all the available user name fields.
                $availablefields = order_in_string(\core_user\fields::get_name_fields(), $nameformat);
                // We'll default to the first name if there's no available name field.
                $returnfield = 'firstname';
                if (!empty($availablefields)) {
                    // Use the first name field.
                    $returnfield = reset($availablefields);
                }
                return 'fauserrecord' . $returnfield;
            case self::SORTORDER_GROUP_DESC:
            case self::SORTORDER_GROUP_ASC:
                return 'gname';
            default:
                global $CFG;
                $alias = $this->get_table_alias();
                $field = "{$alias}.timemodified";
                if (!empty($CFG->forum_enabletimedposts)) {
                    return "CASE WHEN {$field} < {$alias}.timestart THEN {$alias}.timestart ELSE {$field} END";
                }
                return $field;
        }
    }

    /**
     * Get the sort direction.
     *
     * @param int|null $sortmethod
     * @return string
     */
    protected function get_sort_direction(?int $sortmethod) : string {
        switch ($sortmethod) {
            case self::SORTORDER_LASTPOST_ASC:
            case self::SORTORDER_CREATED_ASC:
            case self::SORTORDER_REPLIES_ASC:
            case self::SORTORDER_DISCUSSION_ASC:
            case self::SORTORDER_STARTER_ASC:
            case self::SORTORDER_GROUP_ASC:
                return "ASC";
            case self::SORTORDER_LASTPOST_DESC:
            case self::SORTORDER_CREATED_DESC:
            case self::SORTORDER_REPLIES_DESC:
            case self::SORTORDER_DISCUSSION_DESC:
            case self::SORTORDER_STARTER_DESC:
            case self::SORTORDER_GROUP_DESC:
            default:
                return "DESC";
        }
    }

    /**
     * Get the sort order SQL for a sort method.
     *
     * @param int|null  $sortmethod
     * @param bool|null $includefavourites
     * @return string
     */
    private function get_sort_order(?int $sortmethod, bool $includefavourites = true) : string {

        $alias = $this->get_table_alias();
        // TODO consider user favourites...
        $keyfield = $this->get_keyfield($sortmethod);
        $direction = $this->get_sort_direction($sortmethod);

        $favouritesort = '';
        if ($includefavourites) {
            $favalias = $this->get_favourite_alias();
            // Since we're joining on the favourite table any discussion that isn't favourited will have
            // null in the favourite columns. Nulls behave differently in the sorting for different databases.
            // We can ensure consistency between databases by explicitly deprioritising any null favourite field
            // using a case statement.
            $favouritesort = ", CASE WHEN {$favalias}.id IS NULL THEN 0 ELSE 1 END DESC";
            // After the null favourite fields are deprioritised and appear below the favourited discussions we
            // need to order the favourited discussions by id so that the most recently favourited discussions
            // appear at the top of the list.
            $favouritesort .= ", {$favalias}.itemtype DESC";
        }

        return "{$alias}.pinned DESC $favouritesort , {$keyfield} {$direction}, {$alias}.id {$direction}";
    }

    /**
     * Fetch any required SQL to respect timed posts.
     *
     * @param   bool        $includehiddendiscussions Whether to include hidden discussions or not
     * @param   int|null    $includepostsforuser Which user to include posts for, if any
     * @return  array       The SQL and parameters to include
     */
    protected function get_hidden_post_sql(bool $includehiddendiscussions, ?int $includepostsforuser) {
        $wheresql = '';
        $params = [];
        if (!$includehiddendiscussions) {
            $now = time();
            $wheresql = " AND ((d.timestart <= :timestart AND (d.timeend = 0 OR d.timeend > :timeend))";
            $params['timestart'] = $now;
            $params['timeend'] = $now;
            if (null !== $includepostsforuser) {
                $wheresql .= " OR d.userid = :byuser";
                $params['byuser'] = $includepostsforuser;
            }
            $wheresql .= ")";
        }

        return [
            'wheresql' => $wheresql,
            'params' => $params,
        ];
    }

    /**
     * Get each discussion, first post, first and last post author for the given forum, considering timed posts, and
     * pagination.
     *
     * @param   int         $forumid The forum to fetch the discussion set for
     * @param   bool        $includehiddendiscussions Whether to include hidden discussions or not
     * @param   int|null    $includepostsforuser Which user to include posts for, if any
     * @param   int         $sortorder The sort order to use
     * @param   int         $limit The number of discussions to fetch
     * @param   int         $offset The record offset
     * @return  array       The set of data fetched
     */
    public function get_from_forum_id(
        int $forumid,
        bool $includehiddendiscussions,
        ?int $includepostsforuser,
        ?int $sortorder,
        int $limit,
        int $offset
    ) {
        $alias = $this->get_table_alias();
        $wheresql = "{$alias}.forum = :forumid";
        [
            'wheresql' => $hiddensql,
            'params' => $hiddenparams
        ] = $this->get_hidden_post_sql($includehiddendiscussions, $includepostsforuser);
        $wheresql .= $hiddensql;

        $params = array_merge($hiddenparams, [
            'forumid' => $forumid,
        ]);

        $includefavourites = $includepostsforuser ? true : false;
        $sql = $this->generate_get_records_sql($wheresql, $this->get_sort_order($sortorder, $includefavourites),
            $includepostsforuser);
        $records = $this->get_db()->get_records_sql($sql, $params, $offset, $limit);

        return $this->transform_db_records_to_entities($records);
    }

    /**
     * Get each discussion, first post, first and last post author for the given forum, and the set of groups to display
     * considering timed posts, and pagination.
     *
     * @param   int         $forumid The forum to fetch the discussion set for
     * @param   int[]       $groupids The list of real groups to filter on
     * @param   bool        $includehiddendiscussions Whether to include hidden discussions or not
     * @param   int|null    $includepostsforuser Which user to include posts for, if any
     * @param   int         $sortorder The sort order to use
     * @param   int         $limit The number of discussions to fetch
     * @param   int         $offset The record offset
     * @return  array       The set of data fetched
     */
    public function get_from_forum_id_and_group_id(
        int $forumid,
        array $groupids,
        bool $includehiddendiscussions,
        ?int $includepostsforuser,
        ?int $sortorder,
        int $limit,
        int $offset
    ) {
        $alias = $this->get_table_alias();

        $wheresql = "{$alias}.forum = :forumid AND ";
        $groupparams = [];
        if (empty($groupids)) {
            $wheresql .= "{$alias}.groupid = :allgroupsid";
        } else {
            list($insql, $groupparams) = $this->get_db()->get_in_or_equal($groupids, SQL_PARAMS_NAMED, 'gid');
            $wheresql .= "({$alias}.groupid = :allgroupsid OR {$alias}.groupid {$insql})";
        }

        [
            'wheresql' => $hiddensql,
            'params' => $hiddenparams
        ] = $this->get_hidden_post_sql($includehiddendiscussions, $includepostsforuser);
        $wheresql .= $hiddensql;

        $params = array_merge($hiddenparams, $groupparams, [
            'forumid' => $forumid,
            'allgroupsid' => -1,
        ]);

        $includefavourites = $includepostsforuser ? true : false;
        $sql = $this->generate_get_records_sql($wheresql, $this->get_sort_order($sortorder, $includefavourites),
            $includepostsforuser);
        $records = $this->get_db()->get_records_sql($sql, $params, $offset, $limit);

        return $this->transform_db_records_to_entities($records);
    }

    /**
     * Count the number of discussions in the forum.
     *
     * @param int $forumid Id of the forum to count discussions in
     * @param bool $includehiddendiscussions Include hidden dicussions in the count?
     * @param int|null $includepostsforuser Include discussions created by this user in the count
     *                                      (only works if not including hidden discussions).
     * @return int
     */
    public function get_total_discussion_count_from_forum_id(
        int $forumid,
        bool $includehiddendiscussions,
        ?int $includepostsforuser
    ) {
        $alias = $this->get_table_alias();

        $wheresql = "{$alias}.forum = :forumid";

        [
            'wheresql' => $hiddensql,
            'params' => $hiddenparams
        ] = $this->get_hidden_post_sql($includehiddendiscussions, $includepostsforuser);
        $wheresql .= $hiddensql;

        $params = array_merge($hiddenparams, [
            'forumid' => $forumid,
        ]);

        return $this->get_db()->count_records_sql($this->generate_count_records_sql($wheresql), $params);
    }

    /**
     * Count the number of discussions in all groups and the list of groups provided.
     *
     * @param int $forumid Id of the forum to count discussions in
     * @param int[] $groupids List of group ids to include in the count (discussions in all groups will always be counted)
     * @param bool $includehiddendiscussions Include hidden dicussions in the count?
     * @param int|null $includepostsforuser Include discussions created by this user in the count
     *                                      (only works if not including hidden discussions).
     * @return int
     */
    public function get_total_discussion_count_from_forum_id_and_group_id(
        int $forumid,
        array $groupids,
        bool $includehiddendiscussions,
        ?int $includepostsforuser
    ) {
        $alias = $this->get_table_alias();

        $wheresql = "{$alias}.forum = :forumid AND ";
        $groupparams = [];
        if (empty($groupids)) {
            $wheresql .= "{$alias}.groupid = :allgroupsid";
        } else {
            list($insql, $groupparams) = $this->get_db()->get_in_or_equal($groupids, SQL_PARAMS_NAMED, 'gid');
            $wheresql .= "({$alias}.groupid = :allgroupsid OR {$alias}.groupid {$insql})";
        }

        [
            'wheresql' => $hiddensql,
            'params' => $hiddenparams
        ] = $this->get_hidden_post_sql($includehiddendiscussions, $includepostsforuser);
        $wheresql .= $hiddensql;

        $params = array_merge($hiddenparams, $groupparams, [
            'forumid' => $forumid,
            'allgroupsid' => -1,
        ]);

        return $this->get_db()->count_records_sql($this->generate_count_records_sql($wheresql), $params);
    }

    /**
     * Get the standard favouriting sql.
     *
     * @param int $userid The ID of the user we are getting the sql for
     * @return [$sql, $params] An array comprising of the sql and any associated params
     */
    private function get_favourite_sql(int $userid): array {

        $usercontext = \context_user::instance($userid);
        $alias = $this->get_table_alias();
        $ufservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);
        list($favsql, $favparams) = $ufservice->get_join_sql_by_type('mod_forum', 'discussions',
            $this->get_favourite_alias(), "$alias.id");

        return [$favsql, $favparams];
    }
}
