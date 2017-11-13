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
     * @return \core_search\base
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
     * @param \core_search\base $searcharea
     * @param array $docdata
     * @return \core_search\document
     */
    protected function to_document(\core_search\base $searcharea, $docdata) {

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
     * Loop through given iterator of search documents
     * and and have the search engine back end add them
     * to the index.
     *
     * @param iterator $iterator the iterator of documents to index
     * @param searcharea $searcharea the area for the documents to index
     * @param array $options document indexing options
     * @return array Processed document counts
     */
    public function add_documents($iterator, $searcharea, $options) {
        $numrecords = 0;
        $numdocs = 0;
        $numdocsignored = 0;
        $lastindexeddoc = 0;
        $firstindexeddoc = 0;
        $partial = false;

        foreach ($iterator as $document) {
            // Stop if we have exceeded the time limit (and there are still more items). Always
            // do at least one second's worth of documents otherwise it will never make progress.
            if ($lastindexeddoc !== $firstindexeddoc &&
                    !empty($options['stopat']) && manager::get_current_time() >= $options['stopat']) {
                $partial = true;
                break;
            }

            if (!$document instanceof \core_search\document) {
                continue;
            }

            if (isset($options['lastindexedtime']) && $options['lastindexedtime'] == 0) {
                // If we have never indexed this area before, it must be new.
                $document->set_is_new(true);
            }

            if ($options['indexfiles']) {
                // Attach files if we are indexing.
                $searcharea->attach_files($document);
            }

            if ($this->add_document($document, $options['indexfiles'])) {
                $numdocs++;
            } else {
                $numdocsignored++;
            }

            $lastindexeddoc = $document->get('modified');
            if (!$firstindexeddoc) {
                $firstindexeddoc = $lastindexeddoc;
            }
            $numrecords++;
        }

        return array($numrecords, $numdocs, $numdocsignored, $lastindexeddoc, $partial);
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
     * Run any pre-indexing operations.
     *
     * Should be overwritten if the search engine needs to do any pre index preparation.
     *
     * @param bool $fullindex True if a full index will be performed
     * @return void
     */
    public function index_starting($fullindex = false) {
        // Nothing by default.
    }

    /**
     * Run any post indexing operations.
     *
     * Should be overwritten if the search engine needs to do any post index cleanup.
     *
     * @param int $numdocs The number of documents that were added to the index
     * @param bool $fullindex True if a full index was performed
     * @return void
     */
    public function index_complete($numdocs = 0, $fullindex = false) {
        // Nothing by default.
    }

    /**
     * Do anything that may need to be done before an area is indexed.
     *
     * @param \core_search\base $searcharea The search area that was complete
     * @param bool $fullindex True if a full index is being performed
     * @return void
     */
    public function area_index_starting($searcharea, $fullindex = false) {
        // Nothing by default.
    }

    /**
     * Do any area cleanup needed, and do anything to confirm contents.
     *
     * Return false to prevent the search area completed time and stats from being updated.
     *
     * @param \core_search\base $searcharea The search area that was complete
     * @param int $numdocs The number of documents that were added to the index
     * @param bool $fullindex True if a full index is being performed
     * @return bool True means that data is considered indexed
     */
    public function area_index_complete($searcharea, $numdocs = 0, $fullindex = false) {
        return true;
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
     * Returns the total number of documents available for the most recent call to execute_query.
     *
     * This can be an estimate, but should get more accurate the higher the limited passed to execute_query is.
     * To do that, the engine can use (actual result returned count + count of unchecked documents), or
     * (total possible docs - docs that have been checked and rejected).
     *
     * Engine can limit to manager::MAX_RESULTS if there is cost to determining more.
     * If this cannot be computed in a reasonable way, manager::MAX_RESULTS may be returned.
     *
     * @return int
     */
    abstract public function get_query_total_count();

    /**
     * Return true if file indexing is supported and enabled. False otherwise.
     *
     * @return bool
     */
    public function file_indexing_enabled() {
        return false;
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
     * @param document $document
     * @param bool     $fileindexing True if file indexing is to be used
     * @return bool    False if the file was skipped or failed, true on success
     */
    abstract function add_document($document, $fileindexing = false);

    /**
     * Executes the query on the engine.
     *
     * Implementations of this function should check user context array to limit the results to contexts where the
     * user have access. They should also limit the owneruserid field to manger::NO_OWNER_ID or the current user's id.
     * Engines must use area->check_access() to confirm user access.
     *
     * Engines should reasonably attempt to fill up to limit with valid results if they are available.
     *
     * @param  stdClass $filters Query and filters to apply.
     * @param  array    $usercontexts Contexts where the user has access. True if the user can access all contexts.
     * @param  int      $limit The maximum number of results to return. If empty, limit to manager::MAX_RESULTS.
     * @return \core_search\document[] Results or false if no results
     */
    abstract function execute_query($filters, $usercontexts, $limit = 0);

    /**
     * Delete all documents.
     *
     * @param string $areaid To filter by area
     * @return void
     */
    abstract function delete($areaid = null);
}
