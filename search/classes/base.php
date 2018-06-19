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
 * Search base class to be extended by search areas.
 *
 * @package    core_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search;

defined('MOODLE_INTERNAL') || die();

/**
 * Base search implementation.
 *
 * Components and plugins interested in filling the search engine with data should extend this class (or any extension of this
 * class).
 *
 * @package    core_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base {

    /**
     * The area name as defined in the class name.
     *
     * @var string
     */
    protected $areaname = null;

    /**
     * The component frankenstyle name.
     *
     * @var string
     */
    protected $componentname = null;

    /**
     * The component type (core or the plugin type).
     *
     * @var string
     */
    protected $componenttype = null;

    /**
     * The context levels the search implementation is working on.
     *
     * @var array
     */
    protected static $levels = [CONTEXT_SYSTEM];

    /**
     * Constructor.
     *
     * @throws \coding_exception
     * @return void
     */
    public final function __construct() {

        $classname = get_class($this);

        // Detect possible issues when defining the class.
        if (strpos($classname, '\search') === false) {
            throw new \coding_exception('Search area classes should be located in \PLUGINTYPE_PLUGINNAME\search\AREANAME.');
        } else if (strpos($classname, '_') === false) {
            throw new \coding_exception($classname . ' class namespace level 1 should be its component frankenstyle name');
        }

        $this->areaname = substr(strrchr($classname, '\\'), 1);
        $this->componentname = substr($classname, 0, strpos($classname, '\\'));
        $this->areaid = \core_search\manager::generate_areaid($this->componentname, $this->areaname);
        $this->componenttype = substr($this->componentname, 0, strpos($this->componentname, '_'));
    }

    /**
     * Returns context levels property.
     *
     * @return int
     */
    public static function get_levels() {
        return static::$levels;
    }

    /**
     * Returns the area id.
     *
     * @return string
     */
    public function get_area_id() {
        return $this->areaid;
    }

    /**
     * Returns the moodle component name.
     *
     * It might be the plugin name (whole frankenstyle name) or the core subsystem name.
     *
     * @return string
     */
    public function get_component_name() {
        return $this->componentname;
    }

    /**
     * Returns the component type.
     *
     * It might be a plugintype or 'core' for core subsystems.
     *
     * @return string
     */
    public function get_component_type() {
        return $this->componenttype;
    }

    /**
     * Returns the area visible name.
     *
     * @param bool $lazyload Usually false, unless when in admin settings.
     * @return string
     */
    public function get_visible_name($lazyload = false) {

        $component = $this->componentname;

        // Core subsystem strings go to lang/XX/search.php.
        if ($this->componenttype === 'core') {
            $component = 'search';
        }
        return get_string('search:' . $this->areaname, $component, null, $lazyload);
    }

    /**
     * Returns the config var name.
     *
     * It depends on whether it is a moodle subsystem or a plugin as plugin-related config should remain in their own scope.
     *
     * @access private
     * @return string Config var path including the plugin (or component) and the varname
     */
    public function get_config_var_name() {

        if ($this->componenttype === 'core') {
            // Core subsystems config in core_search and setting name using only [a-zA-Z0-9_]+.
            $parts = \core_search\manager::extract_areaid_parts($this->areaid);
            return array('core_search', $parts[0] . '_' . $parts[1]);
        }

        // Plugins config in the plugin scope.
        return array($this->componentname, 'search_' . $this->areaname);
    }

    /**
     * Returns all the search area configuration.
     *
     * @return array
     */
    public function get_config() {
        list($componentname, $varname) = $this->get_config_var_name();

        $config = [];
        $settingnames = array('_enabled', '_indexingstart', '_indexingend', '_lastindexrun',
                '_docsignored', '_docsprocessed', '_recordsprocessed', '_partial');
        foreach ($settingnames as $name) {
            $config[$varname . $name] = get_config($componentname, $varname . $name);
        }

        // Search areas are enabled by default.
        if ($config[$varname . '_enabled'] === false) {
            $config[$varname . '_enabled'] = 1;
        }
        return $config;
    }

    /**
     * Is the search component enabled by the system administrator?
     *
     * @return bool
     */
    public function is_enabled() {
        list($componentname, $varname) = $this->get_config_var_name();

        $value = get_config($componentname, $varname . '_enabled');

        // Search areas are enabled by default.
        if ($value === false) {
            $value = 1;
        }
        return (bool)$value;
    }

    public function set_enabled($isenabled) {
        list($componentname, $varname) = $this->get_config_var_name();
        return set_config($varname . '_enabled', $isenabled, $componentname);
    }

    /**
     * Gets the length of time spent indexing this area (the last time it was indexed).
     *
     * @return int|bool Time in seconds spent indexing this area last time, false if never indexed
     */
    public function get_last_indexing_duration() {
        list($componentname, $varname) = $this->get_config_var_name();
        $start = get_config($componentname, $varname . '_indexingstart');
        $end = get_config($componentname, $varname . '_indexingend');
        if ($start && $end) {
            return $end - $start;
        } else {
            return false;
        }
    }

    /**
     * Returns true if this area uses file indexing.
     *
     * @return bool
     */
    public function uses_file_indexing() {
        return false;
    }

    /**
     * Returns a recordset ordered by modification date ASC.
     *
     * Each record can include any data self::get_document might need but it must:
     * - Include an 'id' field: Unique identifier (in this area's scope) of a document to index in the search engine
     *   If the indexed content field can contain embedded files, the 'id' value should match the filearea itemid.
     * - Only return data modified since $modifiedfrom, including $modifiedform to prevent
     *   some records from not being indexed (e.g. your-timemodified-fieldname >= $modifiedfrom)
     * - Order the returned data by time modified in ascending order, as \core_search::manager will need to store the modified time
     *   of the last indexed document.
     *
     * Since Moodle 3.4, subclasses should instead implement get_document_recordset, which has
     * an additional context parameter. This function continues to work for implementations which
     * haven't been updated, or where the context parameter is not required.
     *
     * @param int $modifiedfrom
     * @return \moodle_recordset
     */
    public function get_recordset_by_timestamp($modifiedfrom = 0) {
        $result = $this->get_document_recordset($modifiedfrom);
        if ($result === false) {
            throw new \coding_exception(
                    'Search area must implement get_document_recordset or get_recordset_by_timestamp');
        }
        return $result;
    }

    /**
     * Returns a recordset containing all items from this area, optionally within the given context,
     * and including only items modifed from (>=) the specified time. The recordset must be ordered
     * in ascending order of modified time.
     *
     * Each record can include any data self::get_document might need. It must include an 'id'
     * field,a unique identifier (in this area's scope) of a document to index in the search engine.
     * If the indexed content field can contain embedded files, the 'id' value should match the
     * filearea itemid.
     *
     * The return value can be a recordset, null (if this area does not provide any results in the
     * given context and there is no need to do a database query to find out), or false (if this
     * facility is not currently supported by this search area).
     *
     * If this function returns false, then:
     * - If indexing the entire system (no context restriction) the search indexer will try
     *   get_recordset_by_timestamp instead
     * - If trying to index a context (e.g. when restoring a course), the search indexer will not
     *   index this area, so that restored content may not be indexed.
     *
     * The default implementation returns false, indicating that this facility is not supported and
     * the older get_recordset_by_timestamp function should be used.
     *
     * @param int $modifiedfrom Return only records modified after this date
     * @param \context|null $context Context (null means no context restriction)
     * @return \moodle_recordset|null|false Recordset / null if no results / false if not supported
     * @since Moodle 3.4
     */
    public function get_document_recordset($modifiedfrom = 0, \context $context = null) {
        return false;
    }

    /**
     * Returns the document related with the provided record.
     *
     * This method receives a record with the document id and other info returned by get_recordset_by_timestamp
     * or get_recordset_by_contexts that might be useful here. The idea is to restrict database queries to
     * minimum as this function will be called for each document to index. As an alternative, use cached data.
     *
     * Internally it should use \core_search\document to standarise the documents before sending them to the search engine.
     *
     * Search areas should send plain text to the search engine, use the following function to convert any user
     * input data to plain text: {@link content_to_text}
     *
     * Valid keys for the options array are:
     *     indexfiles => File indexing is enabled if true.
     *     lastindexedtime => The last time this area was indexed. 0 if never indexed.
     *
     * The lastindexedtime value is not set if indexing a specific context rather than the whole
     * system.
     *
     * @param \stdClass $record A record containing, at least, the indexed document id and a modified timestamp
     * @param array     $options Options for document creation
     * @return \core_search\document
     */
    abstract public function get_document($record, $options = array());

    /**
     * Returns the document title to display.
     *
     * Allow to customize the document title string to display.
     *
     * @param \core_search\document $doc
     * @return string Document title to display in the search results page
     */
    public function get_document_display_title(\core_search\document $doc) {

        return $doc->get('title');
    }

    /**
     * Return the context info required to index files for
     * this search area.
     *
     * Should be onerridden by each search area.
     *
     * @return array
     */
    public function get_search_fileareas() {
        $fileareas = array();

        return $fileareas;
    }

    /**
     * Files related to the current document are attached,
     * to the document object ready for indexing by
     * Global Search.
     *
     * The default implementation retrieves all files for
     * the file areas returned by get_search_fileareas().
     * If you need to filter files to specific items per
     * file area, you will need to override this method
     * and explicitly provide the items.
     *
     * @param document $document The current document
     * @return void
     */
    public function attach_files($document) {
        $fileareas = $this->get_search_fileareas();
        $contextid = $document->get('contextid');
        $component = $this->get_component_name();
        $itemid = $document->get('itemid');

        foreach ($fileareas as $filearea) {
            $fs = get_file_storage();
            $files = $fs->get_area_files($contextid, $component, $filearea, $itemid, '', false);

            foreach ($files as $file) {
                $document->add_stored_file($file);
            }
        }

    }

    /**
     * Can the current user see the document.
     *
     * @param int $id The internal search area entity id.
     * @return int manager:ACCESS_xx constant
     */
    abstract public function check_access($id);

    /**
     * Returns a url to the document, it might match self::get_context_url().
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    abstract public function get_doc_url(\core_search\document $doc);

    /**
     * Returns a url to the document context.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    abstract public function get_context_url(\core_search\document $doc);

    /**
     * Helper function that gets SQL useful for restricting a search query given a passed-in
     * context, for data stored at course level.
     *
     * The SQL returned will be zero or more JOIN statements, surrounded by whitespace, which act
     * as restrictions on the query based on the rows in a module table.
     *
     * You can pass in a null or system context, which will both return an empty string and no
     * params.
     *
     * Returns an array with two nulls if there can be no results for a course within this context.
     *
     * If named parameters are used, these will be named gclcrs0, gclcrs1, etc. The table aliases
     * used in SQL also all begin with gclcrs, to avoid conflicts.
     *
     * @param \context|null $context Context to restrict the query
     * @param string $coursetable Name of alias for course table e.g. 'c'
     * @param int $paramtype Type of SQL parameters to use (default question mark)
     * @return array Array with SQL and parameters; both null if no need to query
     * @throws \coding_exception If called with invalid params
     */
    protected function get_course_level_context_restriction_sql(\context $context = null,
            $coursetable, $paramtype = SQL_PARAMS_QM) {
        global $DB;

        if (!$context) {
            return ['', []];
        }

        switch ($paramtype) {
            case SQL_PARAMS_QM:
                $param1 = '?';
                $param2 = '?';
                $key1 = 0;
                $key2 = 1;
                break;
            case SQL_PARAMS_NAMED:
                $param1 = ':gclcrs0';
                $param2 = ':gclcrs1';
                $key1 = 'gclcrs0';
                $key2 = 'gclcrs1';
                break;
            default:
                throw new \coding_exception('Unexpected $paramtype: ' . $paramtype);
        }

        $params = [];
        switch ($context->contextlevel) {
            case CONTEXT_SYSTEM:
                $sql = '';
                break;

            case CONTEXT_COURSECAT:
                // Find all courses within the specified category or any sub-category.
                $pathmatch = $DB->sql_like('gclcrscc2.path',
                        $DB->sql_concat('gclcrscc1.path', $param2));
                $sql = " JOIN {course_categories} gclcrscc1 ON gclcrscc1.id = $param1
                         JOIN {course_categories} gclcrscc2 ON gclcrscc2.id = $coursetable.category
                              AND (gclcrscc2.id = gclcrscc1.id OR $pathmatch) ";
                $params[$key1] = $context->instanceid;
                // Note: This param is a bit annoying as it obviously never changes, but sql_like
                // throws a debug warning if you pass it anything with quotes in, so it has to be
                // a bound parameter.
                $params[$key2] = '/%';
                break;

            case CONTEXT_COURSE:
                // We just join again against the same course entry and confirm that it has the
                // same id as the context.
                $sql = " JOIN {course} gclcrsc ON gclcrsc.id = $coursetable.id
                              AND gclcrsc.id = $param1";
                $params[$key1] = $context->instanceid;
                break;

            case CONTEXT_BLOCK:
            case CONTEXT_MODULE:
            case CONTEXT_USER:
                // Context cannot contain any courses.
                return [null, null];

            default:
                throw new \coding_exception('Unexpected contextlevel: ' . $context->contextlevel);
        }

        return [$sql, $params];
    }
}
