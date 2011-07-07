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
 * @package   moodlecore
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * A base class that can be used to build a specific search upon
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

    static $MAXRESULTS = 10;
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
     * Constructor
     * @param array $config Config options
     */
    public function __construct(array $config=array()) {

        $this->search = optional_param($this->get_varsearch(), self::DEFAULT_SEARCH, PARAM_NOTAGS);

        foreach ($config as $name=>$value) {
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
        return ($this->url !== null)?new moodle_url($this->url, $params):new moodle_url($PAGE->url, $params);
    }
    /**
     * The current search string
     * @return string
     */
    final public function get_search() {
        return ($this->search !== null)?$this->search:self::DEFAULT_SEARCH;
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
    final public function require_capability($capability, $user=null) {
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
        $resultset = $DB->get_recordset_sql($sql, $params, 0, 250);
        foreach ($resultset as $result) {
            context_instance_preload($result);
            $context = get_context_instance($contextlevel, $result->id);
            if (count($this->requiredcapabilities) > 0) {
                foreach ($this->requiredcapabilities as $cap) {
                    if (!has_capability($cap['capability'], $context, $cap['user'])) {
                        continue 2;
                    }
                }
            }
            $this->results[$result->id] = $result;
            $this->totalcount++;
            if ($this->totalcount >= self::$MAXRESULTS) {
                break;
            }
        }

        return $this->totalcount;
    }

    final public function has_more_results() {
        return $this->get_count() >= self::$MAXRESULTS;
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
 */
class restore_course_search extends restore_search_base {

    static $VAR_SEARCH = 'search';

    protected $currentcourseid = null;
    protected $includecurrentcourse;

    /**
     * @param array $config
     * @param int $currentcouseid The current course id so it can be ignored
     */
    public function __construct(array $config=array(), $currentcouseid = null) {
        parent::__construct($config);
        $this->require_capability('moodle/restore:restorecourse');
        $this->currentcourseid = $currentcouseid;
        $this->includecurrentcourse = false;
    }
    /**
     *
     * @global moodle_database $DB
     */
    protected function get_searchsql() {
        global $DB;

        list($ctxselect, $ctxjoin) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
        $params = array(
            'fullnamesearch' => '%'.$this->get_search().'%',
            'shortnamesearch' => '%'.$this->get_search().'%',
            'siteid' => SITEID
        );

        $select     = " SELECT c.id,c.fullname,c.shortname,c.visible,c.sortorder ";
        $from       = " FROM {course} c ";
        $where      = " WHERE (".$DB->sql_like('c.fullname', ':fullnamesearch', false)." OR ".$DB->sql_like('c.shortname', ':shortnamesearch', false).") AND c.id <> :siteid";
        $orderby    = " ORDER BY c.sortorder";

        if ($this->currentcourseid !== null && !$this->includecurrentcourse) {
            $where .= " AND c.id <> :currentcourseid";
            $params['currentcourseid'] = $this->currentcourseid;
        }

        return array($select.$ctxselect.$from.$ctxjoin.$where.$orderby, $params);
    }
    protected function get_itemcontextlevel() {
        return CONTEXT_COURSE;
    }
    protected function format_results() {}
    public function get_varsearch() {
        return self::$VAR_SEARCH;
    }
    public function set_include_currentcourse() {
        $this->includecurrentcourse = true;
    }
}

/**
 * A category search component
 */
class restore_category_search extends restore_search_base  {

    static $VAR_SEARCH = 'catsearch';

    public function __construct(array $config=array()) {
        parent::__construct($config);
        $this->require_capability('moodle/course:create');
    }
    /**
     *
     * @global moodle_database $DB
     */
    protected function get_searchsql() {
        global $DB;

        list($ctxselect, $ctxjoin) = context_instance_preload_sql('c.id', CONTEXT_COURSECAT, 'ctx');
        $params = array(
            'namesearch' => '%'.$this->get_search().'%',
        );

        $select     = " SELECT c.id,c.name,c.visible,c.sortorder,c.description,c.descriptionformat ";
        $from       = " FROM {course_categories} c ";
        $where      = " WHERE ".$DB->sql_like('c.name', ':namesearch', false);
        $orderby    = " ORDER BY c.sortorder";

        return array($select.$ctxselect.$from.$ctxjoin.$where.$orderby, $params);
    }
    protected function get_itemcontextlevel() {
        return CONTEXT_COURSECAT;
    }
    protected function format_results() {}
    public function get_varsearch() {
        return self::$VAR_SEARCH;
    }
}

