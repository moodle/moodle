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
 * Base class for search engines.
 *
 * All search engines must extend this class.
 *
 * @package   core_search
 * @copyright 2015 Daniel Neis
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search;

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for search engines.
 *
 * All search engines must extend this class.
 *
 * @package   core_search
 * @copyright 2015 Daniel Neis
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class engine {

    /**
     * The search engine configuration.
     *
     * @var stdClass
     */
    protected $config = null;

    /**
     * Last executed query error, if there was any.
     * @var string
     */
    protected $queryerror = null;

    /**
     * @var array Internal cache.
     */
    protected $cachedareas = array();

    /**
     * @var array Internal cache.
     */
    protected $cachedcourses = array();

    /**
     * User data required to show their fullnames. Indexed by userid.
     *
     * @var stdClass[]
     */
    protected static $cachedusers = array();

    /**
     * @var string Frankenstyle plugin name.
     */
    protected $pluginname = null;

    /**
     * Initialises the search engine configuration.
     *
     * Search engine availability should be checked separately.
     *
     * @see self::is_installed
     * @see self::is_server_ready
     * @return void
     */
    public function __construct() {

        $classname = get_class($this);
        if (strpos($classname, '\\') === false) {
            throw new \coding_exception('"' . $classname . '" class should specify its component namespace and it should be named engine.');
        } else if (strpos($classname, '_') === false) {
            throw new \coding_exception('"' . $classname . '" class namespace should be its frankenstyle name');
        }

        // This is search_xxxx config.
        $this->pluginname = substr($classname, 0, strpos($classname, '\\'));
        if ($config = get_config($this->pluginname)) {
            $this->config = $config;
        } else {
            $this->config = new stdClass();
        }
    }

    /**
     * Returns a course instance checking internal caching.
     *
     * @param int $courseid
     * @return stdClass
     */
    protected function get_course($courseid) {
        if (!empty($this->cachedcourses[$courseid])) {
            return $this->cachedcourses[$courseid];
        }

        // No need to clone, only read.
        $this->cachedcourses[$courseid] = get_course($courseid, false);

        return $this->cachedcourses[$courseid];
    }

    /**
     * Returns user data checking the internal static cache.
     *
     * Including here the minimum required user information as this may grow big.
     *
     * @param int $userid
     * @return stdClass
     */
    public function get_user($userid) {
        global $DB;

        if (empty(self::$cachedusers[$userid])) {
            $fields = get_all_user_name_fields(true);
            self::$cachedusers[$userid] = $DB->get_record('user', array('id' => $userid), 'id, ' . $fields);
        }
        return self::$cachedusers[$userid];
    }

    /**
     * Returns a search instance of the specified area checking internal caching.
     *
     * @param string $areaid Area id
     * @return \core_search\area\base
     */
    protected function get_search_area($areaid) {

        if (isset($this->cachedareas[$areaid]) && $this->cachedareas[$areaid] === false) {
            // We already checked that area and it is not available.
            return false;
        }

        if (!isset($this->cachedareas[$areaid])) {
            // First result that matches this area.

            $this->cachedareas[$areaid] = \core_search\manager::get_search_area($areaid);
            if ($this->cachedareas[$areaid] === false) {
                // The area does not exist or it is not available any more.

                $this->cachedareas[$areaid] = false;
                return false;
            }

            if (!$this->cachedareas[$areaid]->is_enabled()) {
                // We skip the area if it is not enabled.

                // Marking it as false so next time we don' need to check it again.
                $this->cachedareas[$areaid] = false;

                return false;
            }
        }

        return $this->cachedareas[$areaid];
    }

    /**
     * Returns a document instance prepared to be rendered.
     *
     * @param \core_search\area\base $searcharea
     * @param array $docdata
     * @return \core_search\document
     */
    protected function to_document(\core_search\area\base $searcharea, $docdata) {

        list($componentname, $areaname) = \core_search\manager::extract_areaid_parts($docdata['areaid']);
        $doc = \core_search\document_factory::instance($docdata['itemid'], $componentname, $areaname, $this);
        $doc->set_data_from_engine($docdata);
        $doc->set_doc_url($searcharea->get_doc_url($doc));
        $doc->set_context_url($searcharea->get_context_url($doc));

        // Uses the internal caches to get required data needed to render the document later.
        $course = $this->get_course($doc->get('courseid'));
        $doc->set_extra('coursefullname', $course->fullname);

        if ($doc->is_set('userid')) {
            $user = $this->get_user($doc->get('userid'));
            $doc->set_extra('userfullname', fullname($user));
        }

        return $doc;
    }

    /**
     * Returns the plugin name.
     *
     * @return string Frankenstyle plugin name.
     */
    public function get_plugin_name() {
        return $this->pluginname;
    }

    /**
     * Gets the document class used by this search engine.
     *
     * Search engines can overwrite \core_search\document with \search_ENGINENAME\document class.
     *
     * Looks for a document class in the current search engine namespace, falling back to \core_search\document.

     * Publicly available because search areas do not have access to the engine details,
     * \core_search\document_factory accesses this function.
     *
     * @return string
     */
    public function get_document_classname() {
        $classname = $this->pluginname . '\\document';
        if (!class_exists($classname)) {
            $classname = '\\core_search\\document';
        }
        return $classname;
    }

    /**
     * Optimizes the search engine.
     *
     * Should be overwritten if the search engine can optimize its contents.
     *
     * @return void
     */
    public function optimize() {
        // Nothing by default.
    }

    /**
     * Does the system satisfy all the requirements.
     *
     * Should be overwritten if the search engine has any system dependencies
     * that needs to be checked.
     *
     * @return bool
     */
    public function is_installed() {
        return true;
    }

    /**
     * Returns any error reported by the search engine when executing the provided query.
     *
     * It should be called from static::execute_query when an exception is triggered.
     *
     * @return string
     */
    public function get_query_error() {
        return $this->queryerror;
    }

    /**
     * Clears the current query error value.
     *
     * @return void
     */
    public function clear_query_error() {
        $this->queryerror = null;
    }

    /**
     * Is the server ready to use?
     *
     * This should also check that the search engine configuration is ok.
     *
     * @return true|string Returns true if all good or an error string.
     */
    abstract function is_server_ready();

    /**
     * Adds a document to the search engine.
     *
     * @param array $doc
     * @return void
     */
    abstract function add_document($doc);

    /**
     * Commits changes to the server.
     *
     * @return void
     */
    abstract function commit();

    /**
     * Executes the query on the engine.
     *
     * Implementations of this function should check user context array to limit the results to contexts where the
     * user have access.
     *
     * @param  stdClass $filters Query and filters to apply.
     * @param  array    $usercontexts Contexts where the user has access. True if the user can access all contexts.
     * @return \core_search\document[] Results or false if no results
     */
    abstract function execute_query($filters, $usercontexts);

    /**
     * Delete all documents.
     *
     * @param string $areaid To filter by area
     * @return void
     */
    abstract function delete($areaid = null);
}
