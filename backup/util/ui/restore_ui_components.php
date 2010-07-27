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
    const DEFAULT_PAGE = 0;
    const DEFAULT_PAGELIMIT = 5;
    const DEFAULT_SEARCH = '';

    /**
     * The param used to convey the current page
     * @var string
     */
    static $VAR_PAGE = 'page';
    /**
     * The param used to convey the current page limit
     * @var string
     */
    static $VAR_PAGELIMIT = 'pagelimit';
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
     * The current page
     * @var int
     */
    private $page = null;
    /**
     * The current page limit
     * @var int
     */
    private $pagelimit = null;
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

        $this->page = optional_param($this->get_varpage(), self::DEFAULT_PAGE, PARAM_INT);
        $this->pagelimit = optional_param($this->get_varpagelimit(), self::DEFAULT_PAGELIMIT, PARAM_INT);
        $this->search = optional_param($this->get_varsearch(), self::DEFAULT_SEARCH, PARAM_ALPHANUMEXT);

        foreach ($config as $name=>$value) {
            $method = 'set_'.$name;
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }
    /**
     * The current page
     * @return int
     */
    final public function get_page() {
        return ($this->page !== null)?$this->page:self::DEFAULT_PAGE;
    }
    /**
     * The current page limit
     * @return int
     */
    final public function get_pagelimit() {
        return ($this->pagelimit !== null)?$this->pagelimit:self::DEFAULT_PAGELIMIT;
    }
    /**
     * The URL for this search
     * @global moodle_page $PAGE
     * @return moodle_url The URL for this page
     */
    final public function get_url() {
        global $PAGE;
        $params = array(
            $this->get_varpage()      => $this->get_page(),
            $this->get_varpagelimit() => $this->get_pagelimit(),
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
    final public function get_totalcount() {
        if ($this->totalcount === null) {
            $this->search();
        }
        return $this->totalcount;
    }
    /**
     * The number of results in this result set
     * @return int
     */
    final public function get_resultscount() {
        if ($this->results === null) {
            $this->search();
        }
        return count($this->results);
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
     * Sets the current page
     * @param int $page
     */
    final public function set_page($page) {
        $this->page = abs((int)$page);
        $this->invalidate_results();
    }
    /**
     * Sets the page limit for this component
     * @param int $pagelimit
     */
    final public function set_pagelimit($pagelimit) {
        $this->pagelimit = abs((int)$pagelimit);
        if ($this->pagelimit < 5 || $this->pagelimit > 500) {
            $this->pagelimit = null;
        }
        $this->invalidate_results();
    }
    /**
     * Returns the total number of pages
     * @return int
     */
    final public function get_totalpages() {
        return ceil($this->get_totalcount()/$this->pagelimit);
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
        $params = array(
            2=>$this->get_page()*$this->get_pagelimit(),
            3=>$this->get_pagelimit()
        );
        $this->results = call_user_func_array(array($DB, 'get_records_sql'), $this->get_searchsql()+$params);
        $this->totalcount = call_user_func_array(array($DB, 'count_records_sql'), $this->get_countsql()+$params);

        if (count($this->requiredcapabilities) > 0) {
            $contextlevel = $this->get_itemcontextlevel();
            foreach ($this->results as $key=>$result) {
                context_instance_preload($result);
                $context = get_context_instance($contextlevel, $result->id);
                foreach ($this->requiredcapabilities as $cap) {
                    if (!has_capability($cap['capability'], $context, $cap['user'])) {
                        unset($this->results[$key]);
                        break 1;
                    }
                }
            }
        }

        $this->format_results();

        return $this->get_resultscount();
    }
    /**
     * Returns an array containing the SQL for the search and the params
     * @return array
     */
    abstract protected function get_searchsql();
    /**
     * Returns an array containing the SQL to get the total number of search results
     * as well as an array of params for it
     * @return array
     */
    abstract protected function get_countsql();
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
     * Gets the string used to transfer the page for this compontents requests
     * @return string
     */
    abstract public function get_varpage();
    /**
     * Gets the string used to transfer the page limit for this compontents requests
     * @return string
     */
    abstract public function get_varpagelimit();
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

    static $VAR_PAGE = 'page';
    static $VAR_PAGELIMIT = 'pagelimit';
    static $VAR_SEARCH = 'search';

    public function __construct(array $config=array()) {
        parent::__construct($config);
        $this->require_capability('moodle/restore:restorecourse');
    }
    /**
     *
     * @global moodle_database $DB
     */
    protected function get_searchsql() {
        global $DB;

        list($ctxselect, $ctxjoin) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
        $like = $DB->sql_ilike();
        $params = array(
            'fullnamesearch' => $this->get_search().'%',
            'shortnamesearch' => '%'.$this->get_search().'%'
        );

        $select     = " SELECT c.id,c.fullname,c.shortname,c.visible,c.sortorder ";
        $from       = " FROM {course} c ";
        $where      = " WHERE c.fullname $like :fullnamesearch OR c.shortname $like :shortnamesearch ";
        $orderby    = " ORDER BY c.sortorder";

        return array($select.$ctxselect.$from.$ctxjoin.$where.$orderby, $params);
    }
    protected function get_countsql() {
        global $DB;
        
        $like = $DB->sql_ilike();
        $params = array(
            'fullnamesearch' => $this->get_search().'%',
            'shortnamesearch' => '%'.$this->get_search().'%'
        );

        $select     = " SELECT COUNT(c.id) ";
        $from       = " FROM {course} c ";
        $where      = " WHERE c.fullname $like :fullnamesearch OR c.shortname $like :shortnamesearch ";

        return array($select.$from.$where, $params);
    }
    protected function get_itemcontextlevel() {
        return CONTEXT_COURSE;
    }
    protected function format_results() {}
    public function get_varpage() {
        return self::$VAR_PAGE;
    }
    public function get_varpagelimit() {
        return self::$VAR_PAGELIMIT;
    }
    public function get_varsearch() {
        return self::$VAR_SEARCH;
    }
}

/**
 * A category search component
 */
class restore_category_search extends restore_search_base  {

    static $VAR_PAGE = 'catpage';
    static $VAR_PAGELIMIT = 'catpagelimit';
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
        $like = $DB->sql_ilike();
        $params = array(
            'namesearch' => $this->get_search().'%',
        );

        $select     = " SELECT c.id,c.name,c.visible,c.sortorder,c.description,c.descriptionformat ";
        $from       = " FROM {course_categories} c ";
        $where      = " WHERE c.name $like :namesearch ";
        $orderby    = " ORDER BY c.sortorder";

        return array($select.$ctxselect.$from.$ctxjoin.$where.$orderby, $params);
    }
    protected function get_countsql() {
        global $DB;

        $like = $DB->sql_ilike();
        $params = array(
            'namesearch' => $this->get_search().'%',
        );

        $select     = " SELECT COUNT(c.id) ";
        $from       = " FROM {course_categories} c ";
        $where      = " WHERE c.name $like :namesearch ";

        return array($select.$from.$where, $params);
    }
    protected function get_itemcontextlevel() {
        return CONTEXT_COURSECAT;
    }
    protected function format_results() {}
    public function get_varpage() {
        return self::$VAR_PAGE;
    }
    public function get_varpagelimit() {
        return self::$VAR_PAGELIMIT;
    }
    public function get_varsearch() {
        return self::$VAR_SEARCH;
    }
}

