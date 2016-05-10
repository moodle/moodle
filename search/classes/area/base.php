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

namespace core_search\area;

defined('MOODLE_INTERNAL') || die();

/**
 * Base search implementation.
 *
 * Components and plugins interested in filling the search engine
 * with data should extend this class (or any extension of this class)
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
        $settingnames = array('_enabled', '_indexingstart', '_indexingend', '_lastindexrun', '_docsignored', '_docsprocessed', '_recordsprocessed');
        foreach ($settingnames as $name) {
            $config[$varname . $name] = get_config($componentname, $varname . $name);
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
        return (bool)get_config($componentname, $varname . '_enabled');
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
     * @param int $modifiedfrom
     * @return moodle_recordset
     */
    abstract public function get_recordset_by_timestamp($modifiedfrom = 0);

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
     * @param \stdClass $record A record containing, at least, the indexed document id and a modified timestamp
     * @param array     $options Options for document creation
     * @return \core_search\document
     */
    abstract public function get_document($record, $options = array());

    /**
     * Add any files to the document that should be indexed.
     *
     * @param document $document The current document
     * @return void
     */
    public function attach_files($document) {
        return;
    }

    /**
     * Can the current user see the document.
     *
     * @param int $id The internal search area entity id.
     * @return bool True if the user can see it, false otherwise
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
}
