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
    public const SORTORDER_NEWEST_FIRST = 1;
    /** Sort by oldest first */
    public const SORTORDER_OLDEST_FIRST = 2;
    /** Sort by created desc */
    public const SORTORDER_CREATED_DESC = 3;

    /**
     * Get the table alias.
     *
     * @return string
     */
    protected function get_table_alias() : string {
        return 'd';
    }

    /**
     * Build the SQL to be used in get_records_sql.
     *
     * @param string|null $wheresql Where conditions for the SQL
     * @param string|null $sortsql Order by conditions for the SQL
     * @return string
     */
    protected function generate_get_records_sql(string $wheresql = null, ?string $sortsql = null) : string {
        $alias = $this->get_table_alias();
        $db = $this->get_db();

        // Fetch:
        // - Discussion
        // - First post
        // - Author
        // - Most recent editor.
        $thistable = new dml_table(self::TABLE, $alias, $alias);
        $posttable = new dml_table('forum_posts', 'fp', 'p_');
        $firstauthorfields = \user_picture::fields('fa', null, self::FIRST_AUTHOR_ID_ALIAS, self::FIRST_AUTHOR_ALIAS);
        $latestuserfields = \user_picture::fields('la', null, self::LATEST_AUTHOR_ID_ALIAS, self::LATEST_AUTHOR_ALIAS);

        $fields = implode(', ', [
            $thistable->get_field_select(),
            $posttable->get_field_select(),
            $firstauthorfields,
            $latestuserfields,
        ]);

        $tables = $thistable->get_from_sql();
        $tables .= ' JOIN {user} fa ON fa.id = ' . $alias . '.userid';
        $tables .= ' JOIN {user} la ON la.id = ' . $alias . '.usermodified';
        $tables .= ' JOIN ' . $posttable->get_from_sql() . ' ON fp.id = ' . $alias . '.firstpost';

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
     * Get the sort order SQL for a sort method.
     *
     * @param int|null $sortmethod
     */
    public function get_sort_order(?int $sortmethod) : string {
        global $CFG;

        $alias = $this->get_table_alias();

        if ($sortmethod == self::SORTORDER_CREATED_DESC) {
            $keyfield = "fp.created";
            $direction = "DESC";
        } else {
            // TODO consider user favourites...
            $keyfield = "{$alias}.timemodified";
            $direction = "DESC";

            if ($sortmethod == self::SORTORDER_OLDEST_FIRST) {
                $direction = "ASC";
            }

            if (!empty($CFG->forum_enabletimedposts)) {
                $keyfield = "CASE WHEN {$keyfield} < {$alias}.timestart THEN {$alias}.timestart ELSE {$keyfield} END";
            }
        }

        return "{$alias}.pinned DESC, {$keyfield} {$direction}, {$alias}.id DESC";
    }

    /**
     * Fetch any required SQL to respect timed posts.
     *
     * @param   bool        $includehiddendiscussions Whether to include hidden discussions or not
     * @param   int         $includepostsforuser Which user to include posts for, if any
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

        $sql = $this->generate_get_records_sql($wheresql, $this->get_sort_order($sortorder));
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

        $sql = $this->generate_get_records_sql($wheresql, $this->get_sort_order($sortorder));
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
}
