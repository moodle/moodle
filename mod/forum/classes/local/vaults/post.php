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
 * Post vault class.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\vaults;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\post as post_entity;
use mod_forum\local\factories\entity as entity_factory;
use stdClass;

/**
 * Post vault class.
 *
 * This should be the only place that accessed the database.
 *
 * This class should not return any objects other than post_entity objects. The class
 * may contain some utility count methods which return integers.
 *
 * This uses the repository pattern. See:
 * https://designpatternsphp.readthedocs.io/en/latest/More/Repository/README.html
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post extends db_table_vault {
    /** The table for this vault */
    private const TABLE = 'forum_posts';
    /** Alias for user id */
    private const USER_ID_ALIAS = 'userpictureid';
    /** Alias for user fields */
    private const USER_ALIAS = 'userrecord';

    /**
     * Get the table alias.
     *
     * @return string
     */
    protected function get_table_alias() : string {
        return 'p';
    }

    /**
     * Build the SQL to be used in get_records_sql.
     *
     * @param string|null $wheresql Where conditions for the SQL
     * @param string|null $sortsql Order by conditions for the SQL
     * @param int|null $userid The user ID
     * @return string
     */
    protected function generate_get_records_sql(string $wheresql = null, string $sortsql = null, ?int $userid = null) : string {
        $table = self::TABLE;
        $alias = $this->get_table_alias();
        $fields = $alias . '.*';
        $tables = "{{$table}} {$alias}";

        $selectsql = "SELECT {$fields} FROM {$tables}";
        $selectsql .= $wheresql ? ' WHERE ' . $wheresql : '';
        $selectsql .= $sortsql ? ' ORDER BY ' . $sortsql : '';

        return $selectsql;
    }

    /**
     * Convert the DB records into post entities.
     *
     * @param array $results The DB records
     * @return post_entity[]
     */
    protected function from_db_records(array $results) {
        $entityfactory = $this->get_entity_factory();

        return array_map(function(array $result) use ($entityfactory) {
            ['record' => $record] = $result;
            return $entityfactory->get_post_from_stdclass($record);
        }, $results);
    }

    /**
     * Get the post ids for the given discussion.
     *
     * @param stdClass $user The user to check the unread count for
     * @param int $discussionid The discussion to load posts for
     * @param bool $canseeprivatereplies Whether this user can see all private replies or not
     * @param string $orderby Order the results
     * @return post_entity[]
     */
    public function get_from_discussion_id(
        stdClass $user,
        int $discussionid,
        bool $canseeprivatereplies,
        string $orderby = 'created ASC'
    ) : array {
        $alias = $this->get_table_alias();

        [
            'where' => $privatewhere,
            'params' => $privateparams,
        ] = $this->get_private_reply_sql($user, $canseeprivatereplies);

        $wheresql = "{$alias}.discussion = :discussionid {$privatewhere}";
        $orderbysql = $alias . '.' . $orderby;

        $sql = $this->generate_get_records_sql($wheresql, $orderbysql);
        $records = $this->get_db()->get_records_sql($sql, array_merge([
            'discussionid' => $discussionid,
        ], $privateparams));

        return $this->transform_db_records_to_entities($records);
    }

    /**
     * Get the list of posts for the given discussions.
     *
     * @param stdClass $user The user to check the unread count for
     * @param int[] $discussionids The list of discussion ids to load posts for
     * @param bool $canseeprivatereplies Whether this user can see all private replies or not
     * @return post_entity[]
     */
    public function get_from_discussion_ids(stdClass $user, array $discussionids, bool $canseeprivatereplies) : array {
        if (empty($discussionids)) {
            return [];
        }

        $alias = $this->get_table_alias();

        list($insql, $params) = $this->get_db()->get_in_or_equal($discussionids, SQL_PARAMS_NAMED);
        [
            'where' => $privatewhere,
            'params' => $privateparams,
        ] = $this->get_private_reply_sql($user, $canseeprivatereplies);

        $wheresql = "{$alias}.discussion {$insql} {$privatewhere}";

        $sql = $this->generate_get_records_sql($wheresql, '');
        $records = $this->get_db()->get_records_sql($sql, array_merge($params, $privateparams));

        return $this->transform_db_records_to_entities($records);
    }

    /**
     * Load a list of replies to the given post. This will load all descendants of the post.
     * That is, all direct replies and replies to those replies etc.
     *
     * The return value will be a flat array of posts in the requested order.
     *
     * @param stdClass    $user The user to check the unread count for
     * @param post_entity $post The post to load replies for
     * @param bool        $canseeprivatereplies Whether this user can see all private replies or not
     * @param string $orderby How to order the replies
     * @return post_entity[]
     */
    public function get_replies_to_post(
        stdClass $user,
        post_entity $post,
        bool $canseeprivatereplies,
        string $orderby = 'created ASC'
    ) : array {
        $alias = $this->get_table_alias();

        [
            'where' => $privatewhere,
            'params' => $privateparams,
        ] = $this->get_private_reply_sql($user, $canseeprivatereplies);

        $params = array_merge([
            'discussionid' => $post->get_discussion_id(),
            'created' => $post->get_time_created(),
            'excludepostid' => $post->get_id(),
        ], $privateparams);

        // Unfortunately the best we can do to filter down the query is ignore all posts
        // that were created before the given post (since they can't be replies).
        // We also filter to remove private replies if the user cannot vie them.
        $wheresql = "{$alias}.discussion = :discussionid
                 AND {$alias}.created >= :created {$privatewhere}
                 AND {$alias}.id != :excludepostid";
        $orderbysql = $alias . '.' . $orderby;
        $sql = $this->generate_get_records_sql($wheresql, $orderbysql);
        $records = $this->get_db()->get_records_sql($sql, $params);
        $posts = $this->transform_db_records_to_entities($records);
        $sorter = $this->get_entity_factory()->get_posts_sorter();

        // We need to sort all of the values into the replies tree in order to capture
        // the full list of descendants.
        $sortedposts = $sorter->sort_into_children($posts);
        $replies = [];

        // From the sorted list we can grab the first elements and check if they are replies
        // to the post we care about. If so we keep them.
        foreach ($sortedposts as $candidate) {
            [$candidatepost, $candidatereplies] = $candidate;
            if ($candidatepost->has_parent() && $candidatepost->get_parent_id() == $post->get_id()) {
                $replies[] = $candidate;
            }
        }

        if (empty($replies)) {
            return $replies;
        }

        $getreplypostids = function($candidates) use (&$getreplypostids) {
            $ids = [];

            foreach ($candidates as $candidate) {
                [$reply, $replies] = $candidate;
                $ids = array_merge($ids, [$reply->get_id()], $getreplypostids($replies));
            }

            return $ids;
        };
        // Recursively build a list of the ids of all posts in the full reply tree.
        $replypostids = $getreplypostids($replies);

        // Now go back and filter the original result set down to just the posts that
        // we've flagged as in the reply tree. We need to filter the original set of values
        // so that we can maintain the requested sort order.
        return array_values(array_filter($posts, function($post) use ($replypostids) {
            return in_array($post->get_id(), $replypostids);
        }));
    }

    /**
     * Get a mapping of replies to the specified discussions.
     *
     * @param   stdClass    $user The user to check the unread count for
     * @param   int[]       $discussionids The list of discussions to fetch counts for
     * @param   bool        $canseeprivatereplies Whether this user can see all private replies or not
     * @return  int[]       The number of replies for each discussion returned in an associative array
     */
    public function get_reply_count_for_discussion_ids(stdClass $user, array $discussionids, bool $canseeprivatereplies) : array {
        if (empty($discussionids)) {
            return [];
        }

        list($insql, $params) = $this->get_db()->get_in_or_equal($discussionids, SQL_PARAMS_NAMED);

        [
            'where' => $privatewhere,
            'params' => $privateparams,
        ] = $this->get_private_reply_sql($user, $canseeprivatereplies);

        $sql = "SELECT discussion, COUNT(1)
                  FROM {" . self::TABLE . "} p
                 WHERE p.discussion {$insql} AND p.parent > 0 {$privatewhere}
                 GROUP BY discussion";

        return $this->get_db()->get_records_sql_menu($sql, array_merge($params, $privateparams));
    }

    /**
     * Get a mapping of replies to the specified discussions.
     *
     * @param   stdClass    $user The user to check the unread count for
     * @param   int         $postid The post to collect replies to
     * @param   int         $discussionid The list of discussions to fetch counts for
     * @param   bool        $canseeprivatereplies Whether this user can see all private replies or not
     * @return  int         The number of replies for each discussion returned in an associative array
     */
    public function get_reply_count_for_post_id_in_discussion_id(
            stdClass $user, int $postid, int $discussionid, bool $canseeprivatereplies) : int {
        [
            'where' => $privatewhere,
            'params' => $privateparams,
        ] = $this->get_private_reply_sql($user, $canseeprivatereplies);

        $alias = $this->get_table_alias();
        $table = self::TABLE;

        $sql = "SELECT {$alias}.id, {$alias}.parent
                  FROM {{$table}} {$alias}
                 WHERE p.discussion = :discussionid {$privatewhere}";

        $postparents = $this->get_db()->get_records_sql_menu($sql, array_merge([
                'discussionid' => $discussionid,
            ], $privateparams));

        return $this->count_children_from_parent_recursively($postparents, $postid);
    }

    /**
     * Count the children whose parent matches the current record recursively.
     *
     * @param   array   $postparents The full mapping of posts.
     * @param   int     $postid The ID to check for
     * @return  int     $count
     */
    private function count_children_from_parent_recursively(array $postparents, int $postid) : int {
        if (!isset($postparents[$postid])) {
            // Post not found at all.
            return 0;
        }

        $count = 0;
        foreach ($postparents as $pid => $parentid) {
            if ($postid == $parentid) {
                $count += $this->count_children_from_parent_recursively($postparents, $pid) + 1;
            }
        }

        return $count;
    }

    /**
     * Get a mapping of unread post counts for the specified discussions.
     *
     * @param   stdClass    $user The user to fetch counts for
     * @param   int[]       $discussionids The list of discussions to fetch counts for
     * @param   bool        $canseeprivatereplies Whether this user can see all private replies or not
     * @return  int[]       The count of unread posts for each discussion returned in an associative array
     */
    public function get_unread_count_for_discussion_ids(stdClass $user, array $discussionids, bool $canseeprivatereplies) : array {
        global $CFG;

        if (empty($discussionids)) {
            return [];
        }

        [
            'where' => $privatewhere,
            'params' => $privateparams,
        ] = $this->get_private_reply_sql($user, $canseeprivatereplies);

        $alias = $this->get_table_alias();
        list($insql, $params) = $this->get_db()->get_in_or_equal($discussionids, SQL_PARAMS_NAMED);
        $sql = "SELECT p.discussion, COUNT(p.id) FROM {" . self::TABLE . "} p
             LEFT JOIN {forum_read} r ON r.postid = p.id AND r.userid = :userid
                 WHERE p.discussion {$insql} AND p.modified > :cutofftime AND r.id IS NULL {$privatewhere}
              GROUP BY p.discussion";

        $params['userid'] = $user->id;
        $params['cutofftime'] = floor((new \DateTime())
            ->sub(new \DateInterval("P{$CFG->forum_oldpostdays}D"))
            ->format('U') / 60) * 60;

        return $this->get_db()->get_records_sql_menu($sql, array_merge($params, $privateparams));
    }

    /**
     * Get a mapping of the most recent post record in each discussion based on post creation time.
     *
     * @param stdClass $user
     * @param array $discussionids
     * @param bool $canseeprivatereplies
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_latest_posts_for_discussion_ids(
        stdClass $user, array $discussionids, bool $canseeprivatereplies) : array {

        if (empty($discussionids)) {
            return [];
        }

        list($insql, $params) = $this->get_db()->get_in_or_equal($discussionids, SQL_PARAMS_NAMED);

        [
            'where' => $privatewhere,
            'params' => $privateparams,
        ] = $this->get_private_reply_sql($user, $canseeprivatereplies, "mp");

        $sql = "
            SELECT posts.*
            FROM {" . self::TABLE . "} posts
            JOIN (
                SELECT p.discussion, MAX(p.id) as latestpostid
                FROM {" . self::TABLE . "} p
                JOIN (
                    SELECT mp.discussion, MAX(mp.created) AS created
                      FROM {" . self::TABLE . "} mp
                     WHERE mp.discussion {$insql} {$privatewhere}
                  GROUP BY mp.discussion
                ) lp ON lp.discussion = p.discussion AND lp.created = p.created
            GROUP BY p.discussion
          ) plp on plp.discussion = posts.discussion AND plp.latestpostid = posts.id";

        $records = $this->get_db()->get_records_sql($sql, array_merge($params, $privateparams));
        $entities = $this->transform_db_records_to_entities($records);

        return array_reduce($entities, function($carry, $entity) {
            $carry[$entity->get_discussion_id()] = $entity;
            return $carry;
        }, []);
    }

    /**
     * Get the SQL where and additional parameters to use to restrict posts to private reply posts.
     *
     * @param   stdClass    $user The user to fetch counts for
     * @param   bool        $canseeprivatereplies Whether this user can see all private replies or not
     * @return  array       The SQL WHERE clause, and parameters to use in the SQL.
     */
    private function get_private_reply_sql(stdClass $user, bool $canseeprivatereplies, $posttablealias = "p") {
        $params = [];
        $privatewhere = '';
        if (!$canseeprivatereplies) {
            $privatewhere = " AND ({$posttablealias}.privatereplyto = :privatereplyto OR " .
                "{$posttablealias}.userid = :privatereplyfrom OR {$posttablealias}.privatereplyto = 0)";
            $params['privatereplyto'] = $user->id;
            $params['privatereplyfrom'] = $user->id;
        }

        return [
            'where' => $privatewhere,
            'params' => $params,
        ];
    }

    /**
     * Get a mapping of the first post in each discussion based on post creation time.
     *
     * @param   int[]         $discussionids The list of discussions to fetch counts for
     * @return  post_entity[] The post object of the first post for each discussions returned in an associative array
     */
    public function get_first_post_for_discussion_ids(array $discussionids) : array {

        if (empty($discussionids)) {
            return [];
        }

        list($insql, $params) = $this->get_db()->get_in_or_equal($discussionids, SQL_PARAMS_NAMED);

        $sql = "
            SELECT p.*
              FROM {" . self::TABLE . "} p
              JOIN (
                SELECT mp.discussion, MIN(mp.created) AS created
                  FROM {" . self::TABLE . "} mp
                 WHERE mp.discussion {$insql}
              GROUP BY mp.discussion
              ) lp ON lp.discussion = p.discussion AND lp.created = p.created";

        $records = $this->get_db()->get_records_sql($sql, $params);
        return $this->transform_db_records_to_entities($records);
    }
}
