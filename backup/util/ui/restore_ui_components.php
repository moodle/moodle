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
 * This file contains components used by the restore UI
 *
 * @package   core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * A base class that can be used to build a specific search upon
 *
 * @package   core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class restore_search_base implements renderable {

    /**
     * The default values for this components params
     */
    const DEFAULT_SEARCH = '';

    /**
     * The param used to convey the current search string
     * @var string
     */
    static $VAR_SEARCH = 'search';

    /**
     * The current search string
     * @var string|null
     */
    private $search = null;
    /**
     * The URL for this page including required params to return to it
     * @var moodle_url
     */
    private $url = null;
    /**
     * The results of the search
     * @var array|null
     */
    private $results = null;
    /**
     * The total number of results available
     * @var int
     */
    private $totalcount = null;
    /**
     * Array of capabilities required for each item in the search
     * @var array
     */
    private $requiredcapabilities = array();
    /**
     * Max number of courses to return in a search.
     * @var int
     */
    private $maxresults = null;
    /**
     * Indicates if we have more than maxresults found.
     * @var boolean
     */
    private $hasmoreresults = false;

    /**
     * Constructor
     * @param array $config Config options
     */
    public function __construct(array $config = array()) {

        $this->search = optional_param($this->get_varsearch(), self::DEFAULT_SEARCH, PARAM_NOTAGS);
        $this->maxresults = get_config('backup', 'import_general_maxresults');

        foreach ($config as $name => $value) {
            $method = 'set_'.$name;
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * The URL for this search
     * @global moodle_page $PAGE
     * @return moodle_url The URL for this page
     */
    final public function get_url() {
        global $PAGE;
        $params = array(
            $this->get_varsearch()    => $this->get_search()
        );
        return ($this->url !== null) ? new moodle_url($this->url, $params) : new moodle_url($PAGE->url, $params);
    }

    /**
     * The current search string
     * @return string
     */
    final public function get_search() {
        return ($this->search !== null) ? $this->search : self::DEFAULT_SEARCH;
    }

    /**
     * The total number of results
     * @return int
     */
    final public function get_count() {
        if ($this->totalcount === null) {
            $this->search();
        }
        return $this->totalcount;
    }

    /**
     * Returns an array of results from the search
     * @return array
     */
    final public function get_results() {
        if ($this->results === null) {
            $this->search();
        }
        return $this->results;
    }

    /**
     * Sets the page URL
     * @param moodle_url $url
     */
    final public function set_url(moodle_url $url) {
        $this->url = $url;
    }

    /**
     * Invalidates the results collected so far
     */
    final public function invalidate_results() {
        $this->results = null;
        $this->totalcount = null;
    }

    /**
     * Adds a required capability which all results will be checked against
     * @param string $capability
     * @param int|null $user
     */
    final public function require_capability($capability, $user = null) {
        if (!is_int($user)) {
            $user = null;
        }
        $this->requiredcapabilities[] = array(
            'capability' => $capability,
            'user' => $user
        );
    }

    /**
     * Executes the search
     *
     * @global moodle_database $DB
     * @return int The number of results
     */
    final public function search() {
        global $DB;
        if (!is_null($this->results)) {
            return $this->results;
        }

        $this->results = array();
        $this->totalcount = 0;
        $contextlevel = $this->get_itemcontextlevel();
        list($sql, $params) = $this->get_searchsql();
        // Get total number, to avoid some incorrect iterations.
        $countsql = preg_replace('/ORDER BY.*/', '', $sql);
        $totalcourses = $DB->count_records_sql("SELECT COUNT(*) FROM ($countsql) sel", $params);
        if ($totalcourses > 0) {
            // User to be checked is always the same (usually null, get it from first element).
            $firstcap = reset($this->requiredcapabilities);
            $userid = isset($firstcap['user']) ? $firstcap['user'] : null;
            // Extract caps to check, this saves us a bunch of iterations.
            $requiredcaps = array();
            foreach ($this->requiredcapabilities as $cap) {
                $requiredcaps[] = $cap['capability'];
            }
            // Iterate while we have records and haven't reached $this->maxresults.
            $resultset = $DB->get_recordset_sql($sql, $params);
            foreach ($resultset as $result) {
                context_helper::preload_from_record($result);
                $classname = context_helper::get_class_for_level($contextlevel);
                $context = $classname::instance($result->id);
                if (count($requiredcaps) > 0) {
                    if (!has_all_capabilities($requiredcaps, $context, $userid)) {
                        continue;
                    }
                }
                // Check if we are over the limit.
                if ($this->totalcount + 1 > $this->maxresults) {
                    $this->hasmoreresults = true;
                    break;
                }
                // If not, then continue.
                $this->totalcount++;
                $this->results[$result->id] = $result;
            }
            $resultset->close();
        }

        return $this->totalcount;
    }

    /**
     * Returns true if there are more search results.
     * @return bool
     */
    final public function has_more_results() {
        if ($this->results === null) {
            $this->search();
        }
        return $this->hasmoreresults;
    }

    /**
     * Returns an array containing the SQL for the search and the params
     * @return array
     */
    abstract protected function get_searchsql();

    /**
     * Gets the context level associated with this components items
     * @return CONTEXT_*
     */
    abstract protected function get_itemcontextlevel();

    /**
     * Formats the results
     */
    abstract protected function format_results();

    /**
     * Gets the string used to transfer the search string for this compontents requests
     * @return string
     */
    abstract public function get_varsearch();
}

/**
 * A course search component
 *
 * @package   core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_course_search extends restore_search_base {

    /**
     * @var string
     */
    static $VAR_SEARCH = 'search';

    /**
     * The current course id.
     * @var int
     */
    protected $currentcourseid = null;

    /**
     * Determines if the current course is included in the results.
     * @var bool
     */
    protected $includecurrentcourse;

    /**
     * Constructor
     * @param array $config
     * @param int $currentcouseid The current course id so it can be ignored
     */
    public function __construct(array $config = array(), $currentcouseid = null) {
        parent::__construct($config);
        $this->setup_restrictions();
        $this->currentcourseid = $currentcouseid;
        $this->includecurrentcourse = false;
    }

    /**
     * Sets up any access restrictions for the courses to be displayed in the search.
     *
     * This will typically call $this->require_capability().
     */
    protected function setup_restrictions() {
        $this->require_capability('moodle/restore:restorecourse');
    }

    /**
     * Get the search SQL.
     * @global moodle_database $DB
     * @return array
     */
    protected function get_searchsql() {
        global $DB;

        $ctxselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
        $ctxjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
        $params = array(
            'contextlevel' => CONTEXT_COURSE,
            'fullnamesearch' => '%'.$this->get_search().'%',
            'shortnamesearch' => '%'.$this->get_search().'%'
        );

        $select     = " SELECT c.id, c.fullname, c.shortname, c.visible, c.sortorder ";
        $from       = " FROM {course} c ";
        $where      = " WHERE (".$DB->sql_like('c.fullname', ':fullnamesearch', false)." OR ".
            $DB->sql_like('c.shortname', ':shortnamesearch', false).")";
        $orderby    = " ORDER BY c.sortorder";

        if ($this->currentcourseid !== null && !$this->includecurrentcourse) {
            $where .= " AND c.id <> :currentcourseid";
            $params['currentcourseid'] = $this->currentcourseid;
        }

        return array($select.$ctxselect.$from.$ctxjoin.$where.$orderby, $params);
    }

    /**
     * Gets the context level for the search result items.
     * @return CONTEXT_|int
     */
    protected function get_itemcontextlevel() {
        return CONTEXT_COURSE;
    }

    /**
     * Formats results.
     */
    protected function format_results() {}

    /**
     * Returns the name the search variable should use
     * @return string
     */
    public function get_varsearch() {
        return self::$VAR_SEARCH;
    }

    /**
     * Returns true if the current course should be included in the results.
     */
    public function set_include_currentcourse() {
        $this->includecurrentcourse = true;
    }
}

/**
 * A category search component
 *
 * @package   core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_category_search extends restore_search_base  {

    /**
     * The search variable to use.
     * @var string
     */
    static $VAR_SEARCH = 'catsearch';

    /**
     * Constructor
     * @param array $config
     */
    public function __construct(array $config = array()) {
        parent::__construct($config);
        $this->require_capability('moodle/course:create');
    }
    /**
     * Returns the search SQL.
     * @global moodle_database $DB
     * @return array
     */
    protected function get_searchsql() {
        global $DB;

        $ctxselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
        $ctxjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
        $params = array(
            'contextlevel' => CONTEXT_COURSECAT,
            'namesearch' => '%'.$this->get_search().'%',
        );

        $select     = " SELECT c.id, c.name, c.visible, c.sortorder, c.description, c.descriptionformat ";
        $from       = " FROM {course_categories} c ";
        $where      = " WHERE ".$DB->sql_like('c.name', ':namesearch', false);
        $orderby    = " ORDER BY c.sortorder";

        return array($select.$ctxselect.$from.$ctxjoin.$where.$orderby, $params);
    }

    /**
     * Returns the context level of the search results.
     * @return CONTEXT_COURSECAT
     */
    protected function get_itemcontextlevel() {
        return CONTEXT_COURSECAT;
    }

    /**
     * Formats the results.
     */
    protected function format_results() {}

    /**
     * Returns the name to use for the search variable.
     * @return string
     */
    public function get_varsearch() {
        return self::$VAR_SEARCH;
    }
}
