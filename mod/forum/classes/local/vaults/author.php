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
 * Author vault class.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\vaults;

defined('MOODLE_INTERNAL') || die();

/**
 * Author vault class.
 *
 * This should be the only place that accessed the database.
 *
 * This uses the repository pattern. See:
 * https://designpatternsphp.readthedocs.io/en/latest/More/Repository/README.html
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class author extends db_table_vault {
    /** The table for this vault */
    private const TABLE = 'user';

    /**
     * Get the table alias.
     *
     * @return string
     */
    protected function get_table_alias(): string {
        return 'a';
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
     * Convert the DB records into author entities.
     *
     * @param array $results The DB records
     * @return author_entity[]
     */
    protected function from_db_records(array $results) {
        $entityfactory = $this->get_entity_factory();

        return array_map(function(array $result) use ($entityfactory) {
            [
                'record' => $record,
            ] = $result;
            return $entityfactory->get_author_from_stdclass($record);
        }, $results);
    }

    /**
     * Get the authors for the given posts.
     *
     * Returns a distinct list of authors indexed by author id.
     *
     * @param post_entity[] $posts The list of posts
     * @return author_entity[]
     */
    public function get_authors_for_posts(array $posts): array {
        $authorids = array_reduce($posts, function($carry, $post) {
            $carry[$post->get_author_id()] = true;
            return $carry;
        }, []);
        $authorids = array_keys($authorids);
        return $this->get_from_ids($authorids);
    }

    /**
     * Get the context ids for a set of author ids. The results are indexed
     * by the author id.
     *
     * @param int[] $authorids The list of author ids to fetch.
     * @return int[] Results indexed by author id.
     */
    public function get_context_ids_for_author_ids(array $authorids): array {
        $db = $this->get_db();
        [$insql, $params] = $db->get_in_or_equal($authorids);
        $sql = "SELECT instanceid, id FROM {context} WHERE contextlevel = ? AND instanceid {$insql}";
        $records = $db->get_records_sql($sql, array_merge([CONTEXT_USER], $params));
        return array_reduce($authorids, function($carry, $id) use ($records) {
            $carry[$id] = isset($records[$id]) ? (int) $records[$id]->id : null;
            return $carry;
        }, []);
    }
}
