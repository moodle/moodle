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
 * Search subsystem manager.
 *
 * @package   core_search
 * @copyright Prateek Sachan {@link http://prateeksachan.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/lib/accesslib.php');

/**
 * Search subsystem manager.
 *
 * @package   core_search
 * @copyright Prateek Sachan {@link http://prateeksachan.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /**
     * @var int Text contents.
     */
    const TYPE_TEXT = 1;

    /**
     * @var int User can not access the document.
     */
    const ACCESS_DENIED = 0;

    /**
     * @var int User can access the document.
     */
    const ACCESS_GRANTED = 1;

    /**
     * @var int The document was deleted.
     */
    const ACCESS_DELETED = 2;

    /**
     * @var int Maximum number of results that will be retrieved from the search engine.
     */
    const MAX_RESULTS = 100;

    /**
     * @var int Number of results per page.
     */
    const DISPLAY_RESULTS_PER_PAGE = 10;

    /**
     * @var \core_search\area\base[] Enabled search areas.
     */
    protected static $enabledsearchareas = null;

    /**
     * @var \core_search\area\base[] All system search areas.
     */
    protected static $allsearchareas = null;

    /**
     * @var \core_search\manager
     */
    protected static $instance = null;

    /**
     * @var \core_search\engine
     */
    protected $engine = null;

    /**
     * Constructor, use \core_search\manager::instance instead to get a class instance.
     *
     * @param \core_search\area\base The search engine to use
     */
    public function __construct($engine) {
        $this->engine = $engine;
    }

    /**
     * Returns an initialised \core_search instance.
     *
     * It requires global search to be enabled. Use \core_search\manager::is_global_search_enabled
     * to verify it is enabled.
     *
     * @throws \moodle_exception
     * @throws \core_search\engine_exception
     * @return \core_search\manager
     */
    public static function instance() {
        global $CFG;

        // One per request, this should be purged during testing.
        if (static::$instance !== null) {
            return static::$instance;
        }

        if (!static::is_global_search_enabled()) {
            throw new \moodle_exception('globalsearchdisabled', 'search');
        }

        if (!$engine = static::search_engine_instance()) {
            throw new \core_search\engine_exception('enginenotfound', 'search', '', $CFG->searchengine);
        }

        if (!$engine->is_installed()) {
            throw new \core_search\engine_exception('enginenotinstalled', 'search', '', $CFG->searchengine);
        }

        $serverstatus = $engine->is_server_ready();
        if ($serverstatus !== true) {
            // Error message with no details as this is an exception that any user may find if the server crashes.
            throw new \core_search\engine_exception('engineserverstatus', 'search');
        }

        static::$instance = new \core_search\manager($engine);
        return static::$instance;
    }

    /**
     * Returns whether global search is enabled or not.
     *
     * @return bool
     */
    public static function is_global_search_enabled() {
        global $CFG;
        return !empty($CFG->enableglobalsearch);
    }

    /**
     * Returns an instance of the search engine.
     *
     * @return \core_search\engine
     */
    public static function search_engine_instance() {
        global $CFG;

        $classname = '\\search_' . $CFG->searchengine . '\\engine';
        if (!class_exists($classname)) {
            return false;
        }

        return new $classname();
    }

    /**
     * Returns the search engine.
     *
     * @return \core_search\engine
     */
    public function get_engine() {
        return $this->engine;
    }

    /**
     * Returns a search area class name.
     *
     * @param string $areaid
     * @return string
     */
    protected static function get_area_classname($areaid) {
        list($componentname, $areaname) = static::extract_areaid_parts($areaid);
        return '\\' . $componentname . '\\search\\' . $areaname;
    }

    /**
     * Returns a new area search indexer instance.
     *
     * @param string $areaid
     * @return \core_search\area\base|bool False if the area is not available.
     */
    public static function get_search_area($areaid) {

        // Try both caches, it does not matter where it comes from.
        if (!empty(static::$allsearchareas[$areaid])) {
            return static::$allsearchareas[$areaid];
        }
        if (!empty(static::$enabledsearchareas[$areaid])) {
            return static::$enabledsearchareas[$areaid];
        }

        $classname = static::get_area_classname($areaid);
        if (class_exists($classname)) {
            return new $classname();
        }

        return false;
    }

    /**
     * Return the list of available search areas.
     *
     * @param bool $enabled Return only the enabled ones.
     * @return \core_search\area\base[]
     */
    public static function get_search_areas_list($enabled = false) {

        // Two different arrays, we don't expect these arrays to be big.
        if (!$enabled && static::$allsearchareas !== null) {
            return static::$allsearchareas;
        } else if ($enabled && static::$enabledsearchareas !== null) {
            return static::$enabledsearchareas;
        }

        $searchareas = array();

        $plugintypes = \core_component::get_plugin_types();
        foreach ($plugintypes as $plugintype => $unused) {
            $plugins = \core_component::get_plugin_list($plugintype);
            foreach ($plugins as $pluginname => $pluginfullpath) {

                $componentname = $plugintype . '_' . $pluginname;
                $searchclasses = \core_component::get_component_classes_in_namespace($componentname, 'search');
                foreach ($searchclasses as $classname => $classpath) {
                    $areaname = substr(strrchr($classname, '\\'), 1);
                    $areaid = static::generate_areaid($componentname, $areaname);
                    $searchclass = new $classname();
                    if (!$enabled || ($enabled && $searchclass->is_enabled())) {
                        $searchareas[$areaid] = $searchclass;
                    }
                }
            }
        }

        $subsystems = \core_component::get_core_subsystems();
        foreach ($subsystems as $subsystemname => $subsystempath) {
            $componentname = 'core_' . $subsystemname;
            $searchclasses = \core_component::get_component_classes_in_namespace($componentname, 'search');

            foreach ($searchclasses as $classname => $classpath) {
                $areaname = substr(strrchr($classname, '\\'), 1);
                $areaid = static::generate_areaid($componentname, $areaname);
                $searchclass = new $classname();
                if (!$enabled || ($enabled && $searchclass->is_enabled())) {
                    $searchareas[$areaid] = $searchclass;
                }
            }
        }

        // Cache results.
        if ($enabled) {
            static::$enabledsearchareas = $searchareas;
        } else {
            static::$allsearchareas = $searchareas;
        }

        return $searchareas;
    }

    /**
     * Clears all static caches.
     *
     * @return void
     */
    public static function clear_static() {

        static::$enabledsearchareas = null;
        static::$allsearchareas = null;
        static::$instance = null;
    }

    /**
     * Generates an area id from the componentname and the area name.
     *
     * There should not be any naming conflict as the area name is the
     * class name in component/classes/search/.
     *
     * @param string $componentname
     * @param string $areaname
     * @return void
     */
    public static function generate_areaid($componentname, $areaname) {
        return $componentname . '-' . $areaname;
    }

    /**
     * Returns all areaid string components (component name and area name).
     *
     * @param string $areaid
     * @return array Component name (Frankenstyle) and area name (search area class name)
     */
    public static function extract_areaid_parts($areaid) {
        return explode('-', $areaid);
    }

    /**
     * Returns the contexts the user can access.
     *
     * The returned value is a multidimensional array because some search engines can group
     * information and there will be a performance benefit on passing only some contexts
     * instead of the whole context array set.
     *
     * @return bool|array Indexed by area identifier (component + area name). Returns true if the user can see everything.
     */
    protected function get_areas_user_accesses() {
        global $CFG, $USER;

        // All results for admins. Eventually we could add a new capability for managers.
        if (is_siteadmin()) {
            return true;
        }

        $areasbylevel = array();

        // Split areas by context level so we only iterate only once through courses and cms.
        $searchareas = static::get_search_areas_list(true);
        foreach ($searchareas as $areaid => $unused) {
            $classname = static::get_area_classname($areaid);
            $searcharea = new $classname();
            foreach ($classname::get_levels() as $level) {
                $areasbylevel[$level][$areaid] = $searcharea;
            }
        }

        // This will store area - allowed contexts relations.
        $areascontexts = array();

        if (!empty($areasbylevel[CONTEXT_SYSTEM])) {
            // We add system context to all search areas working at this level. Here each area is fully responsible of
            // the access control as we can not automate much, we can not even check guest access as some areas might
            // want to allow guests to retrieve data from them.

            $systemcontextid = \context_system::instance()->id;
            foreach ($areasbylevel[CONTEXT_SYSTEM] as $areaid => $searchclass) {
                $areascontexts[$areaid][] = $systemcontextid;
            }
        }

        // Get the courses where the current user has access.
        $courses = enrol_get_my_courses(array('id', 'cacherev'));
        $courses[SITEID] = get_course(SITEID);
        $site = \course_modinfo::instance(SITEID);
        foreach ($courses as $course) {

            // Info about the course modules.
            $modinfo = get_fast_modinfo($course);

            if (!empty($areasbylevel[CONTEXT_COURSE])) {
                // Add the course contexts the user can view.

                $coursecontext = \context_course::instance($course->id);
                foreach ($areasbylevel[CONTEXT_COURSE] as $areaid => $searchclass) {
                    if ($course->visible || has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
                        $areascontexts[$areaid][$coursecontext->id] = $coursecontext->id;
                    }
                }
            }

            if (!empty($areasbylevel[CONTEXT_MODULE])) {
                // Add the module contexts the user can view (cm_info->uservisible).

                foreach ($areasbylevel[CONTEXT_MODULE] as $areaid => $searchclass) {

                    // Removing the plugintype 'mod_' prefix.
                    $modulename = substr($searchclass->get_component_name(), 4);

                    $modinstances = $modinfo->get_instances_of($modulename);
                    foreach ($modinstances as $modinstance) {
                        if ($modinstance->uservisible) {
                            $areascontexts[$areaid][$modinstance->context->id] = $modinstance->context->id;
                        }
                    }
                }
            }
        }

        return $areascontexts;
    }

    /**
     * Returns documents from the engine based on the data provided.
     *
     * This function does not perform any kind of security checking, the caller code
     * should check that the current user have moodle/search:query capability.
     *
     * It might return the results from the cache instead.
     *
     * @param stdClass $formdata
     * @return \core_search\document[]
     */
    public function search(\stdClass $formdata) {

        $cache = \cache::make('core', 'search_results');

        // Generate a string from all query filters
        // Not including $areascontext here, being a user cache it is not needed.
        $querykey = $this->generate_query_key($formdata);

        // Look for cached results before executing it.
        if ($results = $cache->get($querykey)) {
            return $results;
        }

        // Clears previous query errors.
        $this->engine->clear_query_error();

        $areascontexts = $this->get_areas_user_accesses();
        if (!$areascontexts) {
            // User can not access any context.
            $docs = array();
        } else {
            $docs = $this->engine->execute_query($formdata, $areascontexts);
        }

        // Cache results.
        $cache->set($querykey, $docs);

        return $docs;
    }

    /**
     * We generate the key ourselves so MUC knows that it contains simplekeys.
     *
     * @param stdClass $formdata
     * @return string
     */
    protected function generate_query_key($formdata) {

        // Empty values by default (although q should always have a value).
        $fields = array('q', 'title', 'areaid', 'timestart', 'timeend', 'page');

        // Just in this function scope.
        $params = clone $formdata;
        foreach ($fields as $field) {
            if (empty($params->{$field})) {
                $params->{$field} = '';
            }
        }

        // Although it is not likely, we prevent cache hits if available search areas change during the session.
        $enabledareas = implode('-', array_keys(static::get_search_areas_list(true)));

        return md5($params->q . 'title=' . $params->title . 'areaid=' . $params->areaid .
            'timestart=' . $params->timestart . 'timeend=' . $params->timeend . 'page=' . $params->page .
            $enabledareas);
    }

    /**
     * Merge separate index segments into one.
     */
    public function optimize_index() {
        $this->engine->optimize();
    }

    /**
     * Index all documents.
     *
     * @param bool $fullindex Whether we should reindex everything or not.
     * @throws \moodle_exception
     * @return bool Whether there was any updated document or not.
     */
    public function index($fullindex = false) {
        global $CFG;

        // Unlimited time.
        \core_php_time_limit::raise();

        $anyupdate = false;

        $searchareas = $this->get_search_areas_list(true);
        foreach ($searchareas as $areaid => $searcharea) {

            if (CLI_SCRIPT && !PHPUNIT_TEST) {
                mtrace('Processing ' . $searcharea->get_visible_name() . ' area');
            }

            $indexingstart = time();

            // This is used to store this component config.
            list($componentconfigname, $varname) = $searcharea->get_config_var_name();

            $numrecords = 0;
            $numdocs = 0;
            $numdocsignored = 0;
            $lastindexeddoc = 0;

            if ($fullindex === true) {
                $prevtimestart = 0;
            } else {
                $prevtimestart = intval(get_config($componentconfigname, $varname . '_indexingstart'));
            }

            // Getting the recordset from the area.
            $recordset = $searcharea->get_recordset_by_timestamp($prevtimestart);

            // Pass get_document as callback.
            $iterator = new \core\dml\recordset_walk($recordset, array($searcharea, 'get_document'));
            foreach ($iterator as $document) {

                if (!$document instanceof \core_search\document) {
                    continue;
                }

                $docdata = $document->export_for_engine();
                switch ($docdata['type']) {
                    case static::TYPE_TEXT:
                        $this->engine->add_document($docdata);
                        $numdocs++;
                        break;
                    default:
                        $numdocsignored++;
                        $iterator->close();
                        throw new \moodle_exception('doctypenotsupported', 'search');
                }

                $lastindexeddoc = $document->get('modified');
                $numrecords++;
            }

            if ($numdocs > 0) {
                $anyupdate = true;

                // Commit all remaining documents.
                $this->engine->commit();

                if (CLI_SCRIPT && !PHPUNIT_TEST) {
                    mtrace('Processed ' . $numrecords . ' records containing ' . $numdocs . ' documents for ' .
                        $searcharea->get_visible_name() . ' area. Commits completed.');
                }
            } else if (CLI_SCRIPT && !PHPUNIT_TEST) {
                mtrace('No new documents to index for ' . $searcharea->get_visible_name() . ' area.');
            }

            // Store last index run once documents have been commited to the search engine.
            set_config($varname . '_indexingstart', $indexingstart, $componentconfigname);
            set_config($varname . '_indexingend', time(), $componentconfigname);
            set_config($varname . '_docsignored', $numdocsignored, $componentconfigname);
            set_config($varname . '_docsprocessed', $numdocs, $componentconfigname);
            set_config($varname . '_recordsprocessed', $numrecords, $componentconfigname);
            if ($lastindexeddoc > 0) {
                set_config($varname . '_lastindexrun', $lastindexeddoc, $componentconfigname);
            }
        }

        if ($anyupdate) {
            $event = \core\event\search_indexed::create(
                    array('context' => \context_system::instance()));
            $event->trigger();
        }

        return $anyupdate;
    }

    /**
     * Resets areas config.
     *
     * @throws \moodle_exception
     * @param string $areaid
     * @return void
     */
    public function reset_config($areaid = false) {

        if (!empty($areaid)) {
            $searchareas = array();
            if (!$searchareas[$areaid] = static::get_search_area($areaid)) {
                throw new \moodle_exception('errorareanotavailable', 'search', '', $areaid);
            }
        } else {
            // Only the enabled ones.
            $searchareas = static::get_search_areas_list(true);
        }

        foreach ($searchareas as $searcharea) {
            list($componentname, $varname) = $searcharea->get_config_var_name();
            $config = $searcharea->get_config();

            foreach ($config as $key => $value) {
                // We reset them all but the enable/disabled one.
                if ($key !== $varname . '_enabled') {
                    set_config($key, 0, $componentname);
                }
            }
        }
    }

    /**
     * Deletes an area's documents or all areas documents.
     *
     * @param string $areaid The area id or false for all
     * @return void
     */
    public function delete_index($areaid = false) {
        if (!empty($areaid)) {
            $this->engine->delete($areaid);
            $this->reset_config($areaid);
        } else {
            $this->engine->delete();
            $this->reset_config();
        }
        $this->engine->commit();
    }

    /**
     * Deletes index by id.
     *
     * @param int Solr Document string $id
     */
    public function delete_index_by_id($id) {
        $this->engine->delete_by_id($id);
        $this->engine->commit();
    }

    /**
     * Returns search areas configuration.
     *
     * @param \core_search\area\base[] $searchareas
     * @return \stdClass[] $configsettings
     */
    public function get_areas_config($searchareas) {

        $allconfigs = get_config('search');
        $vars = array('indexingstart', 'indexingend', 'lastindexrun', 'docsignored', 'docsprocessed', 'recordsprocessed');

        $configsettings =  array();
        foreach ($searchareas as $searcharea) {

            $areaid = $searcharea->get_area_id();

            $configsettings[$areaid] = new \stdClass();
            list($componentname, $varname) = $searcharea->get_config_var_name();

            if (!$searcharea->is_enabled()) {
                // We delete all indexed data on disable so no info.
                foreach ($vars as $var) {
                    $configsettings[$areaid]->{$var} = 0;
                }
            } else {
                foreach ($vars as $var) {
                    $configsettings[$areaid]->{$var} = get_config($componentname, $varname .'_' . $var);
                }
            }

            // Formatting the time.
            if (!empty($configsettings[$areaid]->lastindexrun)) {
                $configsettings[$areaid]->lastindexrun = userdate($configsettings[$areaid]->lastindexrun);
            } else {
                $configsettings[$areaid]->lastindexrun = get_string('never');
            }
        }
        return $configsettings;
    }
}
