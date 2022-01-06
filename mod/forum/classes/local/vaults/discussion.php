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
 * Discussion vault class.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\vaults;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\forum as forum_entity;
use mod_forum\local\entities\discussion as discussion_entity;

/**
 * Discussion vault class.
 *
 * This should be the only place that accessed the database.
 *
 * This uses the repository pattern. See:
 * https://designpatternsphp.readthedocs.io/en/latest/More/Repository/README.html
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class discussion extends db_table_vault {
    /** The table for this vault */
    private const TABLE = 'forum_discussions';

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
     * @param int|null $userid The user ID
     * @return string
     */
    protected function generate_get_records_sql(string $wheresql = null, string $sortsql = null, ?int $userid = null) : string {
        $selectsql = 'SELECT * FROM {' . self::TABLE . '} ' . $this->get_table_alias();
        $selectsql .= $wheresql ? ' WHERE ' . $wheresql : '';
        $selectsql .= $sortsql ? ' ORDER BY ' . $sortsql : '';

        return $selectsql;
    }

    /**
     * Convert the DB records into discussion entities.
     *
     * @param array $results The DB records
     * @return discussion_entity[]
     */
    protected function from_db_records(array $results) {
        $entityfactory = $this->get_entity_factory();

        return array_map(function(array $result) use ($entityfactory) {
            [
                'record' => $record,
            ] = $result;
            return $entityfactory->get_discussion_from_stdclass($record);
        }, $results);
    }

    /**
     * Get all discussions in the specified forum.
     *
     * @param   forum_entity $forum
     * @return  array
     */
    public function get_all_discussions_in_forum(forum_entity $forum, string $sort = null): ?array {
        $records = $this->get_db()->get_records(self::TABLE, [
            'forum' => $forum->get_id(),
        ], $sort ?? '');

        return $this->transform_db_records_to_entities($records);
    }

    /**
     * Get the first discussion in the specified forum.
     *
     * @param   forum_entity $forum
     * @return  discussion_entity|null
     */
    public function get_first_discussion_in_forum(forum_entity $forum) : ?discussion_entity {
        $records = $this->get_db()->get_records(self::TABLE, [
            'forum' => $forum->get_id(),
        ], 'timemodified ASC', '*', 0, 1);

        $records = $this->transform_db_records_to_entities($records);
        return count($records) ? array_shift($records) : null;
    }

    /**
     * Get the last discussion in the specified forum.
     *
     * @param   forum_entity $forum
     * @return  discussion_entity|null
     */
    public function get_last_discussion_in_forum(forum_entity $forum) : ?discussion_entity {
        $records = $this->get_db()->get_records(self::TABLE, [
            'forum' => $forum->get_id(),
        ], 'timemodified DESC', '*', 0, 1);

        $records = $this->transform_db_records_to_entities($records);
        return count($records) ? array_shift($records) : null;
    }

    /**
     * Get the count of the discussions in the specified forum.
     *
     * @param   forum_entity $forum
     * @return  int
     */
    public function get_count_discussions_in_forum(forum_entity $forum) : ?int {
        return $this->get_db()->count_records(self::TABLE, [
            'forum' => $forum->get_id()]);
    }

    /**
     * Update the discussion
     *
     * @param discussion_entity $discussion
     * @return discussion_entity|null
     */
    public function update_discussion(discussion_entity $discussion) : ?discussion_entity {
        $discussionrecord = $this->get_legacy_factory()->to_legacy_object($discussion);
        if ($this->get_db()->update_record('forum_discussions', $discussionrecord)) {
            $records = $this->transform_db_records_to_entities([$discussionrecord]);

            return count($records) ? array_shift($records) : null;
        }

        return null;
    }
}
