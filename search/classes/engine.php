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
     * @var \stdClass
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
     * @var \stdClass[]
     */
    protected static $cachedusers = array();

    /**
     * @var string Frankenstyle plugin name.
     */
    protected $pluginname = null;

    /**
     * @var bool If true, should skip schema validity check when checking the search engine is ready
     */
    protected $skipschemacheck = false;

    /**
     * Initialises the search engine configuration.
     *
     * Search engine availability should be checked separately.
     *
     * The alternate configuration option is only used to construct a special second copy of the
     * search engine object, as described in {@see has_alternate_configuration}.
     *
     * @param bool $alternateconfiguration If true, use alternate configuration settings
     * @return void
     */
    public function __construct(bool $alternateconfiguration = false) {

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

        // For alternate configuration, automatically replace normal configuration values with
        // those beginning with 'alternate'.
        if ($alternateconfiguration) {
            foreach ((array)$this->config as $key => $value) {
                if (preg_match('~^alternate(.*)$~', $key, $matches)) {
                    $this->config->{$matches[1]} = $value;
                }
            }
        }

        // Flag just in case engine needs to know it is using the alternate configuration.
        $this->config->alternateconfiguration = $alternateconfiguration;
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
            $userfieldsapi = \core_user\fields::for_name();
            $fields = $userfieldsapi->get_sql('', false, '', '', false)->selects;
            self::$cachedusers[$userid] = $DB->get_record('user', array('id' => $userid), 'id, ' . $fields);
        }
        return self::$cachedusers[$userid];
    }

    /**
     * Clears the users cache.
     *
     * @return null
     */
    public static function clear_users_cache() {
        self::$cachedusers = [];
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
        $doc->set_doc_icon($searcharea->get_doc_icon($doc));

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
     * @param \iterator $iterator the iterator of documents to index
     * @param base $searcharea the area for the documents to index
     * @param array $options document indexing options
     * @return array Processed document counts
     */
    public function add_documents($iterator, $searcharea, $options) {
        $numrecords = 0;
        $numdocs = 0;
        $numdocsignored = 0;
        $numbatches = 0;
        $lastindexeddoc = 0;
        $firstindexeddoc = 0;
        $partial = false;
        $lastprogress = manager::get_current_time();

        $batchmode = $this->supports_add_document_batch();
        $currentbatch = [];

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

            if ($batchmode && strlen($document->get('content')) <= $this->get_batch_max_content()) {
                $currentbatch[] = $document;
                if (count($currentbatch) >= $this->get_batch_max_documents()) {
                    [$processed, $failed, $batches] = $this->add_document_batch($currentbatch, $options['indexfiles']);
                    $numdocs += $processed;
                    $numdocsignored += $failed;
                    $numbatches += $batches;
                    $currentbatch = [];
                }
            } else {
                if ($this->add_document($document, $options['indexfiles'])) {
                    $numdocs++;
                } else {
                    $numdocsignored++;
                }
                $numbatches++;
            }

            $lastindexeddoc = $document->get('modified');
            if (!$firstindexeddoc) {
                $firstindexeddoc = $lastindexeddoc;
            }
            $numrecords++;

            // If indexing the area takes a long time, periodically output progress information.
            if (isset($options['progress'])) {
                $now = manager::get_current_time();
                if ($now - $lastprogress >= manager::DISPLAY_INDEXING_PROGRESS_EVERY) {
                    $lastprogress = $now;
                    // The first date format is the same used in \core\cron::trace_time_and_memory().
                    $options['progress']->output(date('H:i:s', (int)$now) . ': Done to ' . userdate(
                            $lastindexeddoc, get_string('strftimedatetimeshort', 'langconfig')), 1);
                }
            }
        }

        // Add remaining documents from batch.
        if ($batchmode && $currentbatch) {
            [$processed, $failed, $batches] = $this->add_document_batch($currentbatch, $options['indexfiles']);
            $numdocs += $processed;
            $numdocsignored += $failed;
            $numbatches += $batches;
        }

        return [$numrecords, $numdocs, $numdocsignored, $lastindexeddoc, $partial, $numbatches];
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
        mtrace('The ' . get_string('pluginname', $this->get_plugin_name()) .
                ' search engine does not require automatic optimization.');
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
     * If the function $this->should_skip_schema_check() returns true, then this function may leave
     * out time-consuming checks that the schema is valid. (This allows for improved performance on
     * critical pages such as the main search form.)
     *
     * @return true|string Returns true if all good or an error string.
     */
    abstract function is_server_ready();

    /**
     * Tells the search engine to skip any time-consuming checks that it might do as part of the
     * is_server_ready function, and only carry out a basic check that it can contact the server.
     *
     * This setting is not remembered and applies only to the current request.
     *
     * @since Moodle 3.5
     * @param bool $skip True to skip the checks, false to start checking again
     */
    public function skip_schema_check($skip = true) {
        $this->skipschemacheck = $skip;
    }

    /**
     * For use by subclasses. The engine can call this inside is_server_ready to check whether it
     * should skip time-consuming schema checks.
     *
     * @since Moodle 3.5
     * @return bool True if schema checks should be skipped
     */
    protected function should_skip_schema_check() {
        return $this->skipschemacheck;
    }

    /**
     * Adds a document to the search engine.
     *
     * @param document $document
     * @param bool     $fileindexing True if file indexing is to be used
     * @return bool    False if the file was skipped or failed, true on success
     */
    abstract function add_document($document, $fileindexing = false);

    /**
     * Adds multiple documents to the search engine.
     *
     * It should return the number successfully processed, and the number of batches they were
     * processed in (for example if you add 100 documents and there is an error processing one of
     * those documents, and it took 4 batches, it would return [99, 1, 4]).
     *
     * If the engine implements this, it should return true to {@see supports_add_document_batch}.
     *
     * The system will only call this function with up to {@see get_batch_max_documents} documents,
     * and each document in the batch will have content no larger than specified by
     * {@see get_batch_max_content}.
     *
     * @param document[] $documents Documents to add
     * @param bool $fileindexing True if file indexing is to be used
     * @return int[] Array of three elements, successfully processed, failed processed, batch count
     */
    public function add_document_batch(array $documents, bool $fileindexing = false): array {
        throw new \coding_exception('add_document_batch not supported by this engine');
    }

    /**
     * Executes the query on the engine.
     *
     * Implementations of this function should check user context array to limit the results to contexts where the
     * user have access. They should also limit the owneruserid field to manger::NO_OWNER_ID or the current user's id.
     * Engines must use area->check_access() to confirm user access.
     *
     * Engines should reasonably attempt to fill up to limit with valid results if they are available.
     *
     * The $filters object may include the following fields (optional except q):
     * - q: value of main search field; results should include this text
     * - title: if included, title must match this search
     * - areaids: array of search area id strings (only these areas will be searched)
     * - courseids: array of course ids (only these courses will be searched)
     * - groupids: array of group ids (only results specifically from these groupids will be
     *   searched) - this option will be ignored if the search engine doesn't support groups
     *
     * The $accessinfo parameter has two different values (for historical compatibility). If the
     * engine returns false to supports_group_filtering then it is an array of user contexts, or
     * true if the user can access all contexts. (This parameter used to be called $usercontexts.)
     * If the engine returns true to supports_group_filtering then it will be an object containing
     * these fields:
     * - everything (true if admin is searching with no restrictions)
     * - usercontexts (same as above)
     * - separategroupscontexts (array of context ids where separate groups are used)
     * - visiblegroupscontextsareas (array of subset of those where some areas use visible groups)
     * - usergroups (array of relevant group ids that user belongs to)
     *
     * The engine should apply group restrictions to those contexts listed in the
     * 'separategroupscontexts' array. In these contexts, it shouled only include results if the
     * groupid is not set, or if the groupid matches one of the values in USER_GROUPS array, or
     * if the search area is one of those listed in 'visiblegroupscontextsareas' for that context.
     *
     * @param \stdClass $filters Query and filters to apply.
     * @param \stdClass $accessinfo Information about the contexts the user can access
     * @param  int      $limit The maximum number of results to return. If empty, limit to manager::MAX_RESULTS.
     * @return \core_search\document[] Results or false if no results
     */
    public abstract function execute_query($filters, $accessinfo, $limit = 0);

    /**
     * Delete all documents.
     *
     * @param string $areaid To filter by area
     * @return void
     */
    abstract function delete($areaid = null);

    /**
     * Deletes information related to a specific context id. This should be used when the context
     * itself is deleted from Moodle.
     *
     * This only deletes information for the specified context - not for any child contexts.
     *
     * This function is optional; if not supported it will return false and the information will
     * not be deleted from the search index.
     *
     * If an engine implements this function it should also implement delete_index_for_course;
     * otherwise, nothing will be deleted when users delete an entire course at once.
     *
     * @param int $oldcontextid ID of context that has been deleted
     * @return bool True if implemented
     * @throws \core_search\engine_exception Engines may throw this exception for any problem
     */
    public function delete_index_for_context(int $oldcontextid) {
        return false;
    }

    /**
     * Deletes information related to a specific course id. This should be used when the course
     * itself is deleted from Moodle.
     *
     * This deletes all information relating to that course from the index, including all child
     * contexts.
     *
     * This function is optional; if not supported it will return false and the information will
     * not be deleted from the search index.
     *
     * If an engine implements this function then, ideally, it should also implement
     * delete_index_for_context so that deletion of single activities/blocks also works.
     *
     * @param int $oldcourseid ID of course that has been deleted
     * @return bool True if implemented
     * @throws \core_search\engine_exception Engines may throw this exception for any problem
     */
    public function delete_index_for_course(int $oldcourseid) {
        return false;
    }

    /**
     * Checks that the schema is the latest version. If the version stored in config does not match
     * the current, this function will attempt to upgrade the schema.
     *
     * @return bool|string True if schema is OK, a string if user needs to take action
     */
    public function check_latest_schema() {
        if (empty($this->config->schemaversion)) {
            $currentversion = 0;
        } else {
            $currentversion = $this->config->schemaversion;
        }
        if ($currentversion < document::SCHEMA_VERSION) {
            return $this->update_schema((int)$currentversion, (int)document::SCHEMA_VERSION);
        } else {
            return true;
        }
    }

    /**
     * Usually called by the engine; marks that the schema has been updated.
     *
     * @param int $version Records the schema version now applied
     */
    public function record_applied_schema_version($version) {
        set_config('schemaversion', $version, $this->pluginname);
    }

    /**
     * Requests the search engine to upgrade the schema. The engine should update the schema if
     * possible/necessary, and should ensure that record_applied_schema_version is called as a
     * result.
     *
     * If it is not possible to upgrade the schema at the moment, it can do nothing and return; the
     * function will be called again next time search is initialised.
     *
     * The default implementation just returns, with a DEBUG_DEVELOPER warning.
     *
     * @param int $oldversion Old schema version
     * @param int $newversion New schema version
     * @return bool|string True if schema is updated successfully, a string if it needs updating manually
     */
    protected function update_schema($oldversion, $newversion) {
        debugging('Unable to update search engine schema: ' . $this->pluginname, DEBUG_DEVELOPER);
        return get_string('schemanotupdated', 'search');
    }

    /**
     * Checks if this search engine supports groups.
     *
     * Note that returning true to this function causes the parameters to execute_query to be
     * passed differently!
     *
     * In order to implement groups and return true to this function, the search engine should:
     *
     * 1. Handle the fields ->separategroupscontexts and ->usergroups in the $accessinfo parameter
     *    to execute_query (ideally, using these to automatically restrict search results).
     * 2. Support the optional groupids parameter in the $filter parameter for execute_query to
     *    restrict results to only those where the stored groupid matches the given value.
     *
     * @return bool True if this engine supports searching by group id field
     */
    public function supports_group_filtering() {
        return false;
    }

    /**
     * Obtain a list of results orders (and names for them) that are supported by this
     * search engine in the given context.
     *
     * By default, engines sort by relevance only.
     *
     * @param \context $context Context that the user requested search from
     * @return array Array from order name => display text
     */
    public function get_supported_orders(\context $context) {
        return ['relevance' => get_string('order_relevance', 'search')];
    }

    /**
     * Checks if the search engine supports searching by user.
     *
     * If it returns true to this function, the search engine should support the 'userids' option
     * in the $filters value passed to execute_query(), returning only items where the userid in
     * the search document matches one of those user ids.
     *
     * @return bool True if the search engine supports searching by user
     */
    public function supports_users() {
        return false;
    }

    /**
     * Checks if the search engine supports adding documents in a batch.
     *
     * If it returns true to this function, the search engine must implement the add_document_batch
     * function.
     *
     * @return bool True if the search engine supports adding documents in a batch
     */
    public function supports_add_document_batch(): bool {
        return false;
    }

    /**
     * Gets the maximum number of documents to send together in batch mode.
     *
     * Only relevant if the engine returns true to {@see supports_add_document_batch}.
     *
     * Can be overridden by search engine if required.
     *
     * @var int Number of documents to send together in batch mode, default 100.
     */
    public function get_batch_max_documents(): int {
        return 100;
    }

    /**
     * Gets the maximum size of document content to be included in a shared batch (if the
     * document is bigger then it will be sent on its own; batching does not provide a performance
     * improvement for big documents anyway).
     *
     * Only relevant if the engine returns true to {@see supports_add_document_batch}.
     *
     * Can be overridden by search engine if required.
     *
     * @return int Max size in bytes, default 1MB
     */
    public function get_batch_max_content(): int {
        return 1024 * 1024;
    }

    /**
     * Checks if the search engine has an alternate configuration.
     *
     * This is used where the same search engine class supports two different configurations,
     * which are both shown on the settings screen. The alternate configuration is selected by
     * passing 'true' parameter to the constructor.
     *
     * The feature is used when a different connection is in use for indexing vs. querying
     * the search engine.
     *
     * This function should only return true if the engine supports an alternate configuration
     * and the user has filled in the settings. (We do not need to test they are valid, that will
     * happen as normal.)
     *
     * @return bool True if an alternate configuration is defined
     */
    public function has_alternate_configuration(): bool {
        return false;
    }
}
