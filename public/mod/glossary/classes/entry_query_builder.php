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
 * Entry query builder.
 *
 * @package    mod_glossary
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Entry query builder class.
 *
 * The purpose of this class is to avoid duplicating SQL statements to fetch entries
 * which are very similar with each other. This builder is not meant to be smart, it
 * will not out rule any previously set condition, or join, etc...
 *
 * You should be using this builder just like you would be creating your SQL query. Only
 * some methods are shorthands to avoid logic duplication and common mistakes.
 *
 * @package    mod_glossary
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
class mod_glossary_entry_query_builder {

    /** Alias for table glossary_alias. */
    const ALIAS_ALIAS = 'ga';
    /** Alias for table glossary_categories. */
    const ALIAS_CATEGORIES = 'gc';
    /** Alias for table glossary_entries_categories. */
    const ALIAS_ENTRIES_CATEGORIES = 'gec';
    /** Alias for table glossary_entries. */
    const ALIAS_ENTRIES = 'ge';
    /** Alias for table user. */
    const ALIAS_USER = 'u';

    /** Include none of the entries to approve. */
    const NON_APPROVED_NONE = 'na_none';
    /** Including all the entries. */
    const NON_APPROVED_ALL = 'na_all';
    /** Including only the entries to be approved. */
    const NON_APPROVED_ONLY = 'na_only';
    /** Including my entries to be approved. */
    const NON_APPROVED_SELF = 'na_self';

    /** @var array Raw SQL statements representing the fields to select. */
    protected $fields = array();
    /** @var array Raw SQL statements representing the JOINs to make. */
    protected $joins = array();
    /** @var string Raw SQL statement representing the FROM clause. */
    protected $from;
    /** @var object The glossary we are fetching from. */
    protected $glossary;
    /** @var int The number of records to fetch from. */
    protected $limitfrom = 0;
    /** @var int The number of records to fetch. */
    protected $limitnum = 0;
    /** @var array List of SQL parameters. */
    protected $params = array();
    /** @var array Raw SQL statements representing the ORDER clause. */
    protected $order = array();
    /** @var array Raw SQL statements representing the WHERE clause. */
    protected $where = array();

    /**
     * Constructor.
     *
     * @param object $glossary The glossary.
     */
    public function __construct($glossary = null) {
        $this->from = sprintf('FROM {glossary_entries} %s', self::ALIAS_ENTRIES);
        if (!empty($glossary)) {
            $this->glossary = $glossary;
            $this->where[] = sprintf('(%s.glossaryid = :gid OR %s.sourceglossaryid = :gid2)',
                self::ALIAS_ENTRIES, self::ALIAS_ENTRIES);
            $this->params['gid'] = $glossary->id;
            $this->params['gid2'] = $glossary->id;
        }
    }

    /**
     * Add a field to select.
     *
     * @param string $field The field, or *.
     * @param string $table The table name, without the prefix 'glossary_'.
     * @param string $alias An alias for the field.
     */
    public function add_field($field, $table, $alias = null) {
        $field = self::resolve_field($field, $table);
        if (!empty($alias)) {
            $field .= ' AS ' . $alias;
        }
        $this->fields[] = $field;
    }

    /**
     * Adds the user fields.
     *
     * @return void
     */
    public function add_user_fields() {
        $userfieldsapi = \core_user\fields::for_userpic();
        $fields = $userfieldsapi->get_sql('u', false, 'userdata', '', false)->selects;
        $this->fields[] = $fields;
    }

    /**
     * Internal method to build the query.
     *
     * @param bool $count Query to count?
     * @return string The SQL statement.
     */
    protected function build_query($count = false) {
        $sql = 'SELECT ';

        if ($count) {
            $sql .= 'COUNT(\'x\') ';
        } else {
            $sql .= implode(', ', $this->fields) . ' ';
        }

        $sql .= $this->from . ' ';
        $sql .= implode(' ', $this->joins) . ' ';

        if (!empty($this->where)) {
            $sql .= 'WHERE (' . implode(') AND (', $this->where) . ') ';
        }

        if (!$count && !empty($this->order)) {
            $sql .= 'ORDER BY ' . implode(', ', $this->order);
        }

        return $sql;
    }

    /**
     * Count the records.
     *
     * @return int The number of records.
     */
    public function count_records() {
        global $DB;
        return $DB->count_records_sql($this->build_query(true), $this->params);
    }

    /**
     * Filter a field using a letter.
     *
     * @param string $letter     The letter.
     * @param string $finalfield The SQL statement representing the field.
     */
    protected function filter_by_letter($letter, $finalfield) {
        global $DB;

        $letter = core_text::strtoupper($letter);
        $len = core_text::strlen($letter);
        $sql = $DB->sql_substr(sprintf('upper(%s)', $finalfield), 1, $len);

        $this->where[] = "$sql = :letter";
        $this->params['letter'] = $letter;
    }

    /**
     * Filter a field by special characters.
     *
     * @param string $finalfield The SQL statement representing the field.
     */
    protected function filter_by_non_letter($finalfield) {
        global $DB;

        $alphabet = explode(',', get_string('alphabet', 'langconfig'));
        list($nia, $aparams) = $DB->get_in_or_equal($alphabet, SQL_PARAMS_NAMED, 'nonletter', false);

        $sql = $DB->sql_substr(sprintf('upper(%s)', $finalfield), 1, 1);

        $this->where[] = "$sql $nia";
        $this->params = array_merge($this->params, $aparams);
    }

    /**
     * Filter the author by letter.
     *
     * @param string  $letter         The letter.
     * @param bool    $firstnamefirst Whether or not the firstname is first in the author's name.
     */
    public function filter_by_author_letter($letter, $firstnamefirst = false) {
        $field = self::get_fullname_field($firstnamefirst);
        $this->filter_by_letter($letter, $field);
    }

    /**
     * Filter the author by special characters.
     *
     * @param bool $firstnamefirst Whether or not the firstname is first in the author's name.
     */
    public function filter_by_author_non_letter($firstnamefirst = false) {
        $field = self::get_fullname_field($firstnamefirst);
        $this->filter_by_non_letter($field);
    }

    /**
     * Filter non approved entries.
     *
     * @param string $constant One of the NON_APPROVED_* constants.
     * @param int    $userid   The user ID when relevant, otherwise current user.
     */
    public function filter_by_non_approved($constant, $userid = null) {
        global $USER;
        if (!$userid) {
            $userid = $USER->id;
        }

        if ($constant === self::NON_APPROVED_ALL) {
            // Nothing to do.

        } else if ($constant === self::NON_APPROVED_SELF) {
            $this->where[] = sprintf('%s != 0 OR %s = :toapproveuserid',
                self::resolve_field('approved', 'entries'), self::resolve_field('userid', 'entries'));
            $this->params['toapproveuserid'] = $USER->id;

        } else if ($constant === self::NON_APPROVED_NONE) {
            $this->where[] = sprintf('%s != 0', self::resolve_field('approved', 'entries'));

        } else if ($constant === self::NON_APPROVED_ONLY) {
            $this->where[] = sprintf('%s = 0', self::resolve_field('approved', 'entries'));

        } else {
            throw new coding_exception('Invalid constant');
        }
    }

    /**
     * Filter by concept or alias.
     *
     * This requires the alias table to be joined in the query. See {@link self::join_alias()}.
     *
     * @param string $term What the concept or aliases should be.
     */
    public function filter_by_term($term) {
        $this->where[] = sprintf("(%s LIKE :filterterma OR %s LIKE :filtertermb)",
            self::resolve_field('concept', 'entries'),
            self::resolve_field('alias', 'alias'));
        $this->params['filterterma'] = "%" . $term . "%";
        $this->params['filtertermb'] = "%" . $term . "%";
    }

    /**
     * Convenience method to get get the SQL statement for the full name.
     *
     * @param bool $firstnamefirst Whether or not the firstname is first in the author's name.
     * @return string The SQL statement.
     */
    public static function get_fullname_field($firstnamefirst = false) {
        global $DB;
        if ($firstnamefirst) {
            return $DB->sql_fullname(self::resolve_field('firstname', 'user'), self::resolve_field('lastname', 'user'));
        }
        return $DB->sql_fullname(self::resolve_field('lastname', 'user'), self::resolve_field('firstname', 'user'));
    }

    /**
     * Get the records.
     *
     * @return array
     */
    public function get_records() {
        global $DB;
        return $DB->get_records_sql($this->build_query(), $this->params, $this->limitfrom, $this->limitnum);
    }

    /**
     * Get the recordset.
     *
     * @return moodle_recordset
     */
    public function get_recordset() {
        global $DB;
        return $DB->get_recordset_sql($this->build_query(), $this->params, $this->limitfrom, $this->limitnum);
    }

    /**
     * Retrieve a user object from a record.
     *
     * This comes handy when {@link self::add_user_fields} was used.
     *
     * @param stdClass $record The record.
     * @return stdClass A user object.
     */
    public static function get_user_from_record($record) {
        return user_picture::unalias($record, null, 'userdataid', 'userdata');
    }

    /**
     * Join the alias table.
     *
     * Note that this may cause the same entry to be returned more than once. You might want
     * to add a distinct on the entry id.
     *
     * @return void
     */
    public function join_alias() {
        $this->joins[] = sprintf('LEFT JOIN {glossary_alias} %s ON %s = %s',
            self::ALIAS_ALIAS, self::resolve_field('id', 'entries'), self::resolve_field('entryid', 'alias'));
    }

    /**
     * Join on the category tables.
     *
     * Depending on the category passed the joins will be different. This is due to the display
     * logic that assumes that when displaying all categories the non categorised entries should
     * not be returned, etc...
     *
     * @param int $categoryid The category ID, or GLOSSARY_SHOW_* constant.
     */
    public function join_category($categoryid) {

        if ($categoryid === GLOSSARY_SHOW_ALL_CATEGORIES) {
            $this->joins[] = sprintf('JOIN {glossary_entries_categories} %s ON %s = %s',
                self::ALIAS_ENTRIES_CATEGORIES, self::resolve_field('id', 'entries'),
                self::resolve_field('entryid', 'entries_categories'));

            $this->joins[] = sprintf('JOIN {glossary_categories} %s ON %s = %s',
                self::ALIAS_CATEGORIES, self::resolve_field('id', 'categories'),
                self::resolve_field('categoryid', 'entries_categories'));

        } else if ($categoryid === GLOSSARY_SHOW_NOT_CATEGORISED) {
            $this->joins[] = sprintf('LEFT JOIN {glossary_entries_categories} %s ON %s = %s',
                self::ALIAS_ENTRIES_CATEGORIES, self::resolve_field('id', 'entries'),
                self::resolve_field('entryid', 'entries_categories'));

        } else {
            $this->joins[] = sprintf('JOIN {glossary_entries_categories} %s ON %s = %s AND %s = :joincategoryid',
                self::ALIAS_ENTRIES_CATEGORIES, self::resolve_field('id', 'entries'),
                self::resolve_field('entryid', 'entries_categories'),
                self::resolve_field('categoryid', 'entries_categories'));
            $this->params['joincategoryid'] = $categoryid;

        }
    }

    /**
     * Join the user table.
     *
     * @param bool $strict When strict uses a JOIN rather than a LEFT JOIN.
     */
    public function join_user($strict = false) {
        $join = $strict ? 'JOIN' : 'LEFT JOIN';
        $this->joins[] = sprintf("$join {user} %s ON %s = %s",
            self::ALIAS_USER, self::resolve_field('id', 'user'), self::resolve_field('userid', 'entries'));
    }

    /**
     * Limit the number of records to fetch.
     * @param int $from Fetch from.
     * @param int $num  Number to fetch.
     */
    public function limit($from, $num) {
        $this->limitfrom = $from;
        $this->limitnum = $num;
    }

    /**
     * Normalise a direction.
     *
     * This ensures that the value is either ASC or DESC.
     *
     * @param string $direction The desired direction.
     * @return string ASC or DESC.
     */
    protected function normalize_direction($direction) {
        $direction = core_text::strtoupper($direction);
        if ($direction == 'DESC') {
            return 'DESC';
        }
        return 'ASC';
    }

    /**
     * Order by a field.
     *
     * @param string $field The field, or *.
     * @param string $table The table name, without the prefix 'glossary_'.
     * @param string $direction ASC, or DESC.
     */
    public function order_by($field, $table, $direction = '') {
        $direction = self::normalize_direction($direction);
        $this->order[] = self::resolve_field($field, $table) . ' ' . $direction;
    }

    /**
     * Order by author name.
     *
     * @param bool   $firstnamefirst Whether or not the firstname is first in the author's name.
     * @param string $direction ASC, or DESC.
     */
    public function order_by_author($firstnamefirst = false, $direction = '') {
        $field = self::get_fullname_field($firstnamefirst);
        $direction = self::normalize_direction($direction);
        $this->order[] = $field . ' ' . $direction;
    }

    /**
     * Convenience method to transform a field into SQL statement.
     *
     * @param string $field The field, or *.
     * @param string $table The table name, without the prefix 'glossary_'.
     * @return string SQL statement.
     */
    protected static function resolve_field($field, $table) {
        $prefix = constant(__CLASS__ . '::ALIAS_' . core_text::strtoupper($table));
        return sprintf('%s.%s', $prefix, $field);
    }

    /**
     * Simple where conditions.
     *
     * @param string $field The field, or *.
     * @param string $table The table name, without the prefix 'glossary_'.
     * @param mixed $value The value to be equal to.
     */
    public function where($field, $table, $value) {
        static $i = 0;
        $sql = self::resolve_field($field, $table) . ' ';

        if ($value === null) {
            $sql .= 'IS NULL';

        } else {
            $param = 'where' . $i++;
            $sql .= " = :$param";
            $this->params[$param] = $value;
        }

        $this->where[] = $sql;
    }

}
