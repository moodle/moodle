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
 * Forum vault class.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\vaults;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\forum as forum_entity;
use mod_forum\local\vaults\preprocessors\extract_context as extract_context_preprocessor;
use mod_forum\local\vaults\preprocessors\extract_record as extract_record_preprocessor;
use core\dml\table as dml_table;
use context_helper;

/**
 * Forum vault class.
 *
 * This should be the only place that accessed the database.
 *
 * This uses the repository pattern. See:
 * https://designpatternsphp.readthedocs.io/en/latest/More/Repository/README.html
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class forum extends db_table_vault {
    /** The table for this vault */
    private const TABLE = 'forum';

    /**
     * Get the table alias.
     *
     * @return string
     */
    protected function get_table_alias() : string {
        return 'f';
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
        $db = $this->get_db();
        $alias = $this->get_table_alias();

        $thistable = new dml_table(self::TABLE, $alias, $alias);
        $cmtable = new dml_table('course_modules', 'cm', 'cm_');
        $coursetable = new dml_table('course', 'c', 'c_');

        $fields = implode(', ', [
            $thistable->get_field_select(),
            context_helper::get_preload_record_columns_sql('ctx'),
            $cmtable->get_field_select(),
            $coursetable->get_field_select(),
        ]);

        $tables = $thistable->get_from_sql();
        $tables .= " JOIN {modules} m ON m.name = 'forum'";
        $tables .= " JOIN " . $cmtable->get_from_sql() . " ON cm.module = m.id AND cm.instance = {$alias}.id";
        $tables .= ' JOIN {context} ctx ON ctx.contextlevel = ' . CONTEXT_MODULE .  ' AND ctx.instanceid = cm.id';
        $tables .= " JOIN " . $coursetable->get_from_sql() . " ON c.id = {$alias}.course";

        $selectsql = 'SELECT ' . $fields . ' FROM ' . $tables;
        $selectsql .= $wheresql ? ' WHERE ' . $wheresql : '';
        $selectsql .= $sortsql ? ' ORDER BY ' . $sortsql : '';

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
                'forum' => new extract_record_preprocessor(self::TABLE, $this->get_table_alias()),
                'course_module' => new extract_record_preprocessor('course_modules', 'cm_'),
                'course' => new extract_record_preprocessor('course', 'c_'),
                'context' => new extract_context_preprocessor(),
            ]
        );
    }

    /**
     * Convert the DB records into forum entities.
     *
     * @param array $results The DB records
     * @return forum_entity[]
     */
    protected function from_db_records(array $results) : array {
        $entityfactory = $this->get_entity_factory();

        return array_map(function(array $result) use ($entityfactory) {
            [
                'forum' => $forumrecord,
                'course_module' => $coursemodule,
                'course' => $course,
                'context' => $context,
            ] = $result;
            return $entityfactory->get_forum_from_stdclass($forumrecord, $context, $coursemodule, $course);
        }, $results);
    }

    /**
     * Get the forum for the given course module id.
     *
     * @param int $id The course module id
     * @return forum_entity|null
     */
    public function get_from_course_module_id(int $id) : ?forum_entity {
        $records = $this->get_from_course_module_ids([$id]);
        return count($records) ? array_shift($records) : null;
    }

    /**
     * Get the forums for the given course module ids
     *
     * @param int[] $ids The course module ids
     * @return forum_entity[]
     */
    public function get_from_course_module_ids(array $ids) : array {
        $alias = $this->get_table_alias();
        list($insql, $params) = $this->get_db()->get_in_or_equal($ids);
        $wheresql = 'cm.id ' . $insql;
        $sql = $this->generate_get_records_sql($wheresql);
        $records = $this->get_db()->get_records_sql($sql, $params);

        return $this->transform_db_records_to_entities($records);
    }

    /**
     * Get the forum entity for the given post id.
     *
     * @param int $id The course module id
     * @return forum_entity|null
     */
    public function get_from_post_id(int $id) : ?forum_entity {
        $alias = $this->get_table_alias();
        $thistable = new dml_table(self::TABLE, $alias, $alias);
        $coursemoduletable = new dml_table('course_modules', 'cm', 'cm_');
        $coursetable = new dml_table('course', 'c', 'c_');

        $tablefields = $thistable->get_field_select();
        $coursemodulefields = $coursemoduletable->get_field_select();
        $coursefields = $coursetable->get_field_select();

        $fields = implode(', ', [
            $tablefields,
            context_helper::get_preload_record_columns_sql('ctx'),
            $coursemodulefields,
            $coursefields,
        ]);

        $tables = "{forum_posts} p";
        $tables .= " JOIN {forum_discussions} d ON d.id = p.discussion";
        $tables .= ' JOIN {' . self::TABLE . "} {$alias} ON {$alias}.id = d.forum";
        $tables .= " JOIN {modules} m ON m.name = 'forum'";
        $tables .= " JOIN {course_modules} cm ON cm.module = m.id AND cm.instance = {$alias}.id";
        $tables .= ' JOIN {context} ctx ON ctx.contextlevel = ' . CONTEXT_MODULE .  ' AND ctx.instanceid = cm.id';
        $tables .= " JOIN {course} c ON c.id = {$alias}.course";

        $sql = "SELECT {$fields} FROM {$tables} WHERE p.id = :postid";
        $records = $this->get_db()->get_records_sql($sql, [
            'postid' => $id,
        ]);

        $records = $this->transform_db_records_to_entities($records);
        return count($records) ? array_shift($records) : null;
    }
}
