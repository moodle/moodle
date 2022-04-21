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
 * Document representation.
 *
 * @package    core_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search;

use context;

defined('MOODLE_INTERNAL') || die();

/**
 * Represents a document to index.
 *
 * Note that, if you are writting a search engine and you want to change \core_search\document
 * behaviour, you can overwrite this class, will be automatically loaded from \search_YOURENGINE\document.
 *
 * @package    core_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class document implements \renderable, \templatable {

    /**
     * @var array $data The document data.
     */
    protected $data = array();

    /**
     * @var array Extra data needed to render the document.
     */
    protected $extradata = array();

    /**
     * @var \moodle_url Link to the document.
     */
    protected $docurl = null;

    /**
     * @var \moodle_url Link to the document context.
     */
    protected $contexturl = null;

    /**
     * @var \core_search\document_icon Document icon instance.
     */
    protected $docicon = null;

    /**
     * @var int|null The content field filearea.
     */
    protected $contentfilearea = null;

    /**
     * @var int|null The content field itemid.
     */
    protected $contentitemid = null;

    /**
     * @var bool Should be set to true if document hasn't been indexed before. False if unknown.
     */
    protected $isnew = false;

    /**
     * @var \stored_file[] An array of stored files to attach to the document.
     */
    protected $files = array();

    /**
     * Change list (for engine implementers):
     * 2017091700 - add optional field groupid
     *
     * @var int Schema version number (update if any change)
     */
    const SCHEMA_VERSION = 2017091700;

    /**
     * All required fields any doc should contain.
     *
     * We have to choose a format to specify field types, using solr format as we have to choose one and solr is the
     * default search engine.
     *
     * Search engine plugins are responsible of setting their appropriate field types and map these naming to whatever format
     * they need.
     *
     * @var array
     */
    protected static $requiredfields = array(
        'id' => array(
            'type' => 'string',
            'stored' => true,
            'indexed' => false
        ),
        'itemid' => array(
            'type' => 'int',
            'stored' => true,
            'indexed' => true
        ),
        'title' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'content' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'contextid' => array(
            'type' => 'int',
            'stored' => true,
            'indexed' => true
        ),
        'areaid' => array(
            'type' => 'string',
            'stored' => true,
            'indexed' => true
        ),
        'type' => array(
            'type' => 'int',
            'stored' => true,
            'indexed' => true
        ),
        'courseid' => array(
            'type' => 'int',
            'stored' => true,
            'indexed' => true
        ),
        'owneruserid' => array(
            'type' => 'int',
            'stored' => true,
            'indexed' => true
        ),
        'modified' => array(
            'type' => 'tdate',
            'stored' => true,
            'indexed' => true
        ),
    );

    /**
     * All optional fields docs can contain.
     *
     * Although it matches solr fields format, this is just to define the field types. Search
     * engine plugins are responsible of setting their appropriate field types and map these
     * naming to whatever format they need.
     *
     * @var array
     */
    protected static $optionalfields = array(
        'userid' => array(
            'type' => 'int',
            'stored' => true,
            'indexed' => true
        ),
        'groupid' => array(
            'type' => 'int',
            'stored' => true,
            'indexed' => true
        ),
        'description1' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        ),
        'description2' => array(
            'type' => 'text',
            'stored' => true,
            'indexed' => true,
            'mainquery' => true
        )
    );

    /**
     * Any fields that are engine specifc. These are fields that are solely used by a search engine plugin
     * for internal purposes.
     *
     * Field names should be prefixed with engine name to avoid potential conflict with core fields.
     *
     * Uses same format as fields above.
     *
     * @var array
     */
    protected static $enginefields = array();

    /**
     * We ensure that the document has a unique id across search areas.
     *
     * @param int $itemid An id unique to the search area
     * @param string $componentname The search area component Frankenstyle name
     * @param string $areaname The area name (the search area class name)
     * @return void
     */
    public function __construct($itemid, $componentname, $areaname) {

        if (!is_numeric($itemid)) {
            throw new \coding_exception('The itemid should be an integer');
        }

        $this->data['areaid'] = \core_search\manager::generate_areaid($componentname, $areaname);
        $this->data['id'] = $this->data['areaid'] . '-' . $itemid;
        $this->data['itemid'] = intval($itemid);
    }

    /**
     * Add a stored file to the document.
     *
     * @param \stored_file|int $file The file to add, or file id.
     * @return void
     */
    public function add_stored_file($file) {
        if (is_numeric($file)) {
            $this->files[$file] = $file;
        } else {
            $this->files[$file->get_id()] = $file;
        }
    }

    /**
     * Returns the array of attached files.
     *
     * @return \stored_file[]
     */
    public function get_files() {
        // The files array can contain stored file ids, so we need to get instances if asked.
        foreach ($this->files as $id => $listfile) {
            if (is_numeric($listfile)) {
                $fs = get_file_storage();

                if ($file = $fs->get_file_by_id($id)) {
                    $this->files[$id] = $file;
                } else {
                    unset($this->files[$id]); // Index is out of date and referencing a file that does not exist.
                }
            }
        }

        return $this->files;
    }

    /**
     * Setter.
     *
     * Basic checkings to prevent common issues.
     *
     * If the field is a string tags will be stripped, if it is an integer or a date it
     * will be casted to a PHP integer. tdate fields values are expected to be timestamps.
     *
     * @throws \coding_exception
     * @param string $fieldname The field name
     * @param string|int $value The value to store
     * @return string|int The stored value
     */
    public function set($fieldname, $value) {

        if (!empty(static::$requiredfields[$fieldname])) {
            $fielddata = static::$requiredfields[$fieldname];
        } else if (!empty(static::$optionalfields[$fieldname])) {
            $fielddata = static::$optionalfields[$fieldname];
        } else if (!empty(static::$enginefields[$fieldname])) {
            $fielddata = static::$enginefields[$fieldname];
        }

        if (empty($fielddata)) {
            throw new \coding_exception('"' . $fieldname . '" field does not exist.');
        }

        // tdate fields should be set as timestamps, later they might be converted to
        // a date format, it depends on the search engine.
        if (($fielddata['type'] === 'int' || $fielddata['type'] === 'tdate') && !is_numeric($value)) {
            throw new \coding_exception('"' . $fieldname . '" value should be an integer and its value is "' . $value . '"');
        }

        // We want to be strict here, there might be engines that expect us to
        // provide them data with the proper type already set.
        if ($fielddata['type'] === 'int' || $fielddata['type'] === 'tdate') {
            $this->data[$fieldname] = intval($value);
        } else {
            // Remove disallowed Unicode characters.
            $value = \core_text::remove_unicode_non_characters($value);

            // Replace all groups of line breaks and spaces by single spaces.
            $this->data[$fieldname] = preg_replace("/\s+/u", " ", $value);
            if ($this->data[$fieldname] === null) {
                if (isset($this->data['id'])) {
                    $docid = $this->data['id'];
                } else {
                    $docid = '(unknown)';
                }
                throw new \moodle_exception('error_indexing', 'search', '', null, '"' . $fieldname .
                        '" value causes preg_replace error (may be caused by unusual characters) ' .
                        'in document with id "' . $docid . '"');
            }
        }

        return $this->data[$fieldname];
    }

    /**
     * Sets data to this->extradata
     *
     * This data can be retrieved using \core_search\document->get($fieldname).
     *
     * @param string $fieldname
     * @param string $value
     * @return void
     */
    public function set_extra($fieldname, $value) {
        $this->extradata[$fieldname] = $value;
    }

    /**
     * Getter.
     *
     * Use self::is_set if you are not sure if this field is set or not
     * as otherwise it will trigger a \coding_exception
     *
     * @throws \coding_exception
     * @param string $field
     * @return string|int
     */
    public function get($field) {

        if (isset($this->data[$field])) {
            return $this->data[$field];
        }

        // Fallback to extra data.
        if (isset($this->extradata[$field])) {
            return $this->extradata[$field];
        }

        throw new \coding_exception('Field "' . $field . '" is not set in the document');
    }

    /**
     * Checks if a field is set.
     *
     * @param string $field
     * @return bool
     */
    public function is_set($field) {
        return (isset($this->data[$field]) || isset($this->extradata[$field]));
    }

    /**
     * Set if this is a new document. False if unknown.
     *
     * @param bool $new
     */
    public function set_is_new($new) {
       $this->isnew = (bool)$new;
    }

    /**
     * Returns if the document is new. False if unknown.
     *
     * @return bool
     */
    public function get_is_new() {
       return $this->isnew;
    }

    /**
     * Returns all default fields definitions.
     *
     * @return array
     */
    public static function get_default_fields_definition() {
        return static::$requiredfields + static::$optionalfields + static::$enginefields;
    }

    /**
     * Formats the timestamp preparing the time fields to be inserted into the search engine.
     *
     * By default it just returns a timestamp so any search engine could just store integers
     * and use integers comparison to get documents between x and y timestamps, but search
     * engines might be interested in using their own field formats. They can do it extending
     * this class in \search_xxx\document.
     *
     * @param int $timestamp
     * @return string
     */
    public static function format_time_for_engine($timestamp) {
        return $timestamp;
    }

    /**
     * Formats a string value for the search engine.
     *
     * Search engines may overwrite this method to apply restrictions, like limiting the size.
     * The default behaviour is just returning the string.
     *
     * @param string $string
     * @return string
     */
    public static function format_string_for_engine($string) {
        return $string;
    }

    /**
     * Formats a text value for the search engine.
     *
     * Search engines may overwrite this method to apply restrictions, like limiting the size.
     * The default behaviour is just returning the string.
     *
     * @param string $text
     * @return string
     */
    public static function format_text_for_engine($text) {
        return $text;
    }

    /**
     * Returns a timestamp from the value stored in the search engine.
     *
     * By default it just returns a timestamp so any search engine could just store integers
     * and use integers comparison to get documents between x and y timestamps, but search
     * engines might be interested in using their own field formats. They should do it extending
     * this class in \search_xxx\document.
     *
     * @param string $time
     * @return int
     */
    public static function import_time_from_engine($time) {
        return $time;
    }

    /**
     * Returns how text is returned from the search engine.
     *
     * @return int
     */
    protected function get_text_format() {
        return FORMAT_PLAIN;
    }

    /**
     * Fills the document with data coming from the search engine.
     *
     * @throws \core_search\engine_exception
     * @param array $docdata
     * @return void
     */
    public function set_data_from_engine($docdata) {
        $fields = static::$requiredfields + static::$optionalfields + static::$enginefields;
        foreach ($fields as $fieldname => $field) {

            // Optional params might not be there.
            if (isset($docdata[$fieldname])) {
                if ($field['type'] === 'tdate') {
                    // Time fields may need a preprocessing.
                    $this->set($fieldname, static::import_time_from_engine($docdata[$fieldname]));
                } else {
                    // No way we can make this work if there is any multivalue field.
                    if (is_array($docdata[$fieldname])) {
                        throw new \core_search\engine_exception('multivaluedfield', 'search_solr', '', $fieldname);
                    }
                    $this->set($fieldname, $docdata[$fieldname]);
                }
            }
        }
    }

    /**
     * Sets the document url.
     *
     * @param \moodle_url $url
     * @return void
     */
    public function set_doc_url(\moodle_url $url) {
        $this->docurl = $url;
    }

    /**
     * Gets the url to the doc.
     *
     * @return \moodle_url
     */
    public function get_doc_url() {
        return $this->docurl;
    }

    /**
     * Sets document icon instance.
     *
     * @param \core_search\document_icon $docicon
     */
    public function set_doc_icon(document_icon $docicon) {
        $this->docicon = $docicon;
    }

    /**
     * Gets document icon instance.
     *
     * @return \core_search\document_icon
     */
    public function get_doc_icon() {
        return $this->docicon;
    }

    public function set_context_url(\moodle_url $url) {
        $this->contexturl = $url;
    }

    /**
     * Gets the url to the context.
     *
     * @return \moodle_url
     */
    public function get_context_url() {
        return $this->contexturl;
    }

    /**
     * Returns the document ready to submit to the search engine.
     *
     * @throws \coding_exception
     * @return array
     */
    public function export_for_engine() {
        // Set any unset defaults.
        $this->apply_defaults();

        // We don't want to affect the document instance.
        $data = $this->data;

        // Apply specific engine-dependant formats and restrictions.
        foreach (static::$requiredfields as $fieldname => $field) {

            // We also check that we have everything we need.
            if (!isset($data[$fieldname])) {
                throw new \coding_exception('Missing "' . $fieldname . '" field in document with id "' . $this->data['id'] . '"');
            }

            if ($field['type'] === 'tdate') {
                // Overwrite the timestamp with the engine dependant format.
                $data[$fieldname] = static::format_time_for_engine($data[$fieldname]);
            } else if ($field['type'] === 'string') {
                // Overwrite the string with the engine dependant format.
                $data[$fieldname] = static::format_string_for_engine($data[$fieldname]);
            } else if ($field['type'] === 'text') {
                // Overwrite the text with the engine dependant format.
                $data[$fieldname] = static::format_text_for_engine($data[$fieldname]);
            }

        }

        $fields = static::$optionalfields + static::$enginefields;
        foreach ($fields as $fieldname => $field) {
            if (!isset($data[$fieldname])) {
                continue;
            }
            if ($field['type'] === 'tdate') {
                // Overwrite the timestamp with the engine dependant format.
                $data[$fieldname] = static::format_time_for_engine($data[$fieldname]);
            } else if ($field['type'] === 'string') {
                // Overwrite the string with the engine dependant format.
                $data[$fieldname] = static::format_string_for_engine($data[$fieldname]);
            } else if ($field['type'] === 'text') {
                // Overwrite the text with the engine dependant format.
                $data[$fieldname] = static::format_text_for_engine($data[$fieldname]);
            }
        }

        return $data;
    }

    /**
     * Apply any defaults to unset fields before export. Called after document building, but before export.
     *
     * Sub-classes of this should make sure to call parent::apply_defaults().
     */
    protected function apply_defaults() {
        // Set the default type, TYPE_TEXT.
        if (!isset($this->data['type'])) {
            $this->data['type'] = manager::TYPE_TEXT;
        }
    }

    /**
     * Export the document data to be used as a template context.
     *
     * Adding more info than the required one as people might be interested in extending the template.
     *
     * Although content is a required field when setting up the document, it accepts '' (empty) values
     * as they may be the result of striping out HTML.
     *
     * SECURITY NOTE: It is the responsibility of the document to properly escape any text to be displayed.
     * The renderer will output the content without any further cleaning.
     *
     * @param renderer_base $output The renderer.
     * @return array
     */
    public function export_for_template(\renderer_base $output) {
        global $USER;

        list($componentname, $areaname) = \core_search\manager::extract_areaid_parts($this->get('areaid'));
        $context = context::instance_by_id($this->get('contextid'));

        $searcharea = \core_search\manager::get_search_area($this->data['areaid']);
        $title = $this->is_set('title') ? $this->format_text($searcharea->get_document_display_title($this)) : '';
        $data = [
            'componentname' => $componentname,
            'areaname' => $areaname,
            'courseurl' => course_get_url($this->get('courseid')),
            'coursefullname' => format_string($this->get('coursefullname'), true, ['context' => $context->id]),
            'modified' => userdate($this->get('modified')),
            'title' => ($title !== '') ? $title : get_string('notitle', 'search'),
            'docurl' => $this->get_doc_url(),
            'content' => $this->is_set('content') ? $this->format_text($this->get('content')) : null,
            'contexturl' => $this->get_context_url(),
            'description1' => $this->is_set('description1') ? $this->format_text($this->get('description1')) : null,
            'description2' => $this->is_set('description2') ? $this->format_text($this->get('description2')) : null,
        ];

        // Now take any attached any files.
        $files = $this->get_files();
        if (!empty($files)) {
            if (count($files) > 1) {
                $filenames = [];
                foreach ($files as $file) {
                    $filenames[] = format_string($file->get_filename(), true, ['context' => $context->id]);
                }
                $data['multiplefiles'] = true;
                $data['filenames'] = $filenames;
            } else {
                $file = reset($files);
                $data['filename'] = format_string($file->get_filename(), true, ['context' => $context->id]);
            }
        }

        if ($this->is_set('userid')) {
            if ($this->get('userid') == $USER->id ||
                    (has_capability('moodle/user:viewdetails', $context) &&
                    has_capability('moodle/course:viewparticipants', $context))) {
                $data['userurl'] = new \moodle_url(
                    '/user/view.php',
                    ['id' => $this->get('userid'), 'course' => $this->get('courseid')]
                );
                $data['userfullname'] = format_string($this->get('userfullname'), true, ['context' => $context->id]);
            }
        }

        if ($docicon = $this->get_doc_icon()) {
            $data['icon'] = $output->image_url($docicon->get_name(), $docicon->get_component());
        }

        return $data;
    }

    /**
     * Formats a text string coming from the search engine.
     *
     * By default just return the text as it is:
     * - Search areas are responsible of sending just plain data, the search engine may
     *   append HTML or markdown to it (highlighing for example).
     * - The view is responsible of shortening the text if it is too big
     *
     * @param  string $text Text to format
     * @return string HTML text to be renderer
     */
    protected function format_text($text) {
        return format_text($text, $this->get_text_format(), array('context' => $this->get('contextid')));
    }
}
