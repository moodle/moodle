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
 * Post read receipt collection class.
 *
 * @package    mod_forum
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\vaults;

defined('MOODLE_INTERNAL') || die();

use stdClass;

/**
 * Post read receipt collection class.
 *
 * This should be the only place that accessed the database.
 *
 * This uses the repository pattern. See:
 * https://designpatternsphp.readthedocs.io/en/latest/More/Repository/README.html
 *
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class post_read_receipt_collection extends db_table_vault {
    /** The table for this vault */
    private const TABLE = 'forum_read';

    /**
     * Get the table alias.
     *
     * @return string
     */
    protected function get_table_alias(): string {
        return 'fr';
    }

    /**
     * Build the SQL to be used in get_records_sql.
     *
     * @param string|null $wheresql Where conditions for the SQL
     * @param string|null $sortsql Order by conditions for the SQL
     * @param int|null $userid The user ID
     * @return string
     */
    protected function generate_get_records_sql(string $wheresql = null, string $sortsql = null, ?int $userid = null): string {
        $selectsql = 'SELECT * FROM {' . self::TABLE . '} ' . $this->get_table_alias();
        $selectsql .= $wheresql ? ' WHERE ' . $wheresql : '';
        $selectsql .= $sortsql ? ' ORDER BY ' . $sortsql : '';

        return $selectsql;
    }

    /**
     * Convert the DB records into post_read_receipt_collection entities.
     *
     * @param array $results The DB records
     * @return post_read_receipt_collection
     */
    protected function from_db_records(array $results) {
        $entityfactory = $this->get_entity_factory();
        $records = array_map(function($result) {
            return $result['record'];
        }, $results);

        return $entityfactory->get_post_read_receipt_collection_from_stdclasses($records);
    }

    /**
     * Load the post_read_receipt_collection for the given user and set
     * of posts.
     *
     * @param int $userid Id of the user to load receipts for
     * @param int[] $postids List of post ids to load receipts for
     * @return post_read_receipt_collection
     */
    public function get_from_user_id_and_post_ids(int $userid, array $postids) {
        $alias = $this->get_table_alias();
        [$postidinsql, $params] = $this->get_db()->get_in_or_equal($postids);
        $params[] = $userid;

        $wheresql = "{$alias}.postid {$postidinsql}";
        $wheresql .= " AND {$alias}.userid = ?";
        $sql = $this->generate_get_records_sql($wheresql);
        $records = $this->get_db()->get_records_sql($sql, $params);

        return $this->transform_db_records_to_entities($records);
    }

    /**
     * Load the post_read_receipt_collection for the given user and set
     * of posts.
     *
     * @param stdClass $user The user to load receipts for
     * @param post_entity[] $posts List of posts to load receipts for
     * @return post_read_receipt_collection
     */
    public function get_from_user_and_posts(stdClass $user, array $posts) {
        $postids = array_map(function($post) {
            return $post->get_id();
        }, $posts);
        return $this->get_from_user_id_and_post_ids($user->id, $postids);
    }
}
