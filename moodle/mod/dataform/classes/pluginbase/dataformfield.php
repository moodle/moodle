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
 * @package dataformfield
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\pluginbase;

/**
 * Base class for Dataform Field Types
 */
abstract class dataformfield {

    const VISIBLE_NONE = 0;
    const VISIBLE_OWNER = 1;
    const VISIBLE_ALL = 2;

    const DEFAULT_NEW = 0;
    const DEFAULT_ANY = 1;

    protected $_field;
    protected $_renderer = null;
    protected $_distinctvalues = null;

    /**
     * @return array List of the field file areas
     */
    public static function get_file_areas() {
        return array();
    }

    /**
     * Class constructor
     *
     * @param var $field    field id or DB record
     */
    public function __construct($field) {

        if (empty($field)) {
            throw new \coding_exception('Field object must be passed to field constructor.');
        }

        $this->_field = $field;
    }

    /**
     * Magic property method
     *
     * Attempts to call a set_$key method if one exists otherwise falls back
     * to simply set the property
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        if (method_exists($this, 'set_'.$key)) {
            $this->{'set_'.$key}($value);
        }
        $this->_field->{$key} = $value;
    }

    /**
     * Magic get method
     *
     * Attempts to call a get_$key method to return the property and ralls over
     * to return the raw property
     *
     * @param str $key
     * @return mixed
     */
    public function __get($key) {
        if (method_exists($this, 'get_'.$key)) {
            return $this->{'get_'.$key}();
        }
        if (isset($this->_field->{$key})) {
            return $this->_field->{$key};
        }
        return null;
    }

    /**
     * Sets up a field object
     *
     * @return void
     */
    public function set_field($data) {
        $this->name = !empty($data->name) ? trim($data->name) : $this->type;
        $this->description = !empty($data->description) ? trim($data->description) : '';
        $this->visible = isset($data->visible) ? $data->visible : 2;
        $this->editable = isset($data->editable) ? $data->editable : -1;
        $this->label = !empty($data->label) ? $data->label : '';
        $this->defaultcontentmode = !empty($data->defaultcontentmode) ? $data->defaultcontentmode : 0;
        $this->defaultcontent = !empty($data->defaultcontent) ? $data->defaultcontent : null;
        for ($i = 1; $i <= 10; $i++) {
            $this->{"param$i"} = !empty($data->{"param$i"}) ? trim($data->{"param$i"}) : null;
        }
    }

    /**
     * Insert a new field in the database
     */
    public function create($data) {
        global $DB;

        if (!empty($data)) {
            $this->set_field($data);
        }
        if ($this->id = $DB->insert_record('dataform_fields', $this->data)) {
            // Trigger an event for creating this field.
            $event = \mod_dataform\event\field_created::create($this->default_event_params);
            $event->add_record_snapshot('dataform_fields', $this->data);
            $event->trigger();

            return $this->id;
        }
        return false;
    }

    /**
     * Update a field in the database
     */
    public function update($data) {
        global $DB;

        if (!empty($data)) {
            $this->set_field($data);
        }

        if ($DB->update_record('dataform_fields', $this->data)) {
            // Trigger an event for updating this field.
            $event = \mod_dataform\event\field_updated::create($this->default_event_params);
            $event->add_record_snapshot('dataform_fields', $this->data);
            $event->trigger();

            return $this->id;
        }
        return false;
    }

    /**
     * Delete a field completely
     */
    public function delete() {
        global $DB;

        if ($this->id) {
            // Delete field deinition files.
            $component = 'dataformfield_'. $this->type;
            $fs = get_file_storage();
            $contextid = $this->df->context->id;
            foreach ($this::get_file_areas() as $filearea) {
                $fs->delete_area_files($contextid, $component, $filearea, $this->id);
            }

            // Delete field content.
            $this->delete_content();

            // Delete the field record.
            $DB->delete_records('dataform_fields', array('id' => $this->id));

            // Trigger an event for updating this field.
            $event = \mod_dataform\event\field_deleted::create($this->default_event_params);
            $event->add_record_snapshot('dataform_fields', $this->data);
            $event->trigger();

            return true;
        }
        return false;
    }

    // GETTERS/SETTERS.

    /**
     * Returns the type name of the field
     */
    public function get_typename() {
        return get_string('pluginname', "dataformfield_{$this->type}");
    }

    /**
     * Returns the name of the field with all spaces replaced with underscore.
     *
     * @return string
     */
    public function get_name_normalized() {
        return str_replace(' ', '_', $this->name);
    }

    /**
     * Prints the respective type icon
     */
    public function get_image() {
        global $OUTPUT;

        $image = $OUTPUT->pix_icon(
                            'icon',
                            $this->type,
                            "dataformfield_{$this->type}");

        return $image;

    }

    /**
     *
     */
    public function get_df() {
        return \mod_dataform_dataform::instance($this->dataid);
    }

    /**
     *
     */
    public function get_form() {
        global $CFG;

        $formclass = "dataformfield_{$this->type}_form";
        if (!class_exists($formclass)) {
            $formclass = 'mod_dataform\pluginbase\dataformfieldform';
        }
        $actionurl = new \moodle_url(
            '/mod/dataform/field/edit.php',
            array('d' => $this->dataid, 'fid' => $this->id, 'type' => $this->type)
        );
        return new $formclass($this, $actionurl);
    }

    /**
     *
     */
    public function get_data() {
        return $this->_field;
    }

    /**
     * Returns default params for field events.
     * These params can be extended or overriden where the event is created.
     *
     * @return array
     */
    public function get_default_event_params() {
        return array(
            'objectid' => $this->id,
            'context' => $this->df->context,
            'other' => array(
                'fieldname' => $this->name,
                'dataid' => $this->dataid
            )
        );
    }

    /**
     *
     */
    public function get_renderer() {
        global $CFG;

        if (!$this->_renderer) {
            $rendererclass = "dataformfield_{$this->type}_renderer";
            $this->_renderer = new $rendererclass($this);
        }
        return $this->_renderer;
    }

    // CONTENT MANAGEMENT.
    /**
     * Returns true if current user is allowed to see the field's content.
     *
     * @return bool
     */
    public function is_visible($entry) {
        $params = array('dataformid' => $this->dataid, 'fieldid' => $this->id, 'entry' => $entry);
        return \mod_dataform\access\field_view::validate($params);
    }

    /**
     * Returns true if current user is allowed to update the field's content.
     *
     * @return bool
     */
    public function is_editable($entry) {
        $params = array('dataformid' => $this->dataid, 'fieldid' => $this->id, 'entry' => $entry);
        return \mod_dataform\access\field_update::validate($params);
    }

    /**
     *
     */
    public function get_definitions($patterns, $entry, $options = array()) {
        if (!$this->is_visible($entry)) {
            return array_fill_keys($patterns, '');
        }

        return $this->renderer->get_replacements($patterns, $entry, $options);
    }

    /**
     * Updates the field content for the specified entry and returns the content id
     * if new content is created or the true|false result of the update.
     *
     * @param stdClass $entry
     * @param array $values An associative array of values (see {@link dataformfield::get_content_from_data()})
     * @param bool $savenew Whether an existing entry is saved as a new one
     * @return bool|int
     */
    public function update_content($entry, array $values = null, $savenew = false) {
        global $DB;

        if (!$this->is_editable($entry)) {
            return false;
        }

        $fieldid = $this->id;
        $contentid = isset($entry->{"c{$fieldid}_id"}) ? $entry->{"c{$fieldid}_id"} : null;

        list($contents, $oldcontents) = $this->format_content($entry, $values);

        // Delete if old content but not new.
        if ($contentid and empty($contents)) {
            return $this->delete_content($entry->id);
        }

        $rec = new \stdClass;
        $rec->fieldid = $this->id;
        $rec->entryid = $entry->id;
        $rec->content = null;
        $rec->content1 = null;
        $rec->content2 = null;
        $rec->content3 = null;
        $rec->content4 = null;
        foreach ($contents as $key => $content) {
            $c = $key ? $key : '';
            $rec->{"content$c"} = $content;
        }

        $updated = false;
        if ($savenew) {
            // Insert if saving an existing entry to a new one.
            $rec->id = $DB->insert_record('dataform_contents', $rec);
            $updated = true;

        } else if (is_null($contentid) and !empty($contents)) {
            // Insert only if no old contents and there is new contents.
            $rec->id = $DB->insert_record('dataform_contents', $rec);
            $updated = true;

        } else {
            // Update if new is different from old.
            foreach ($contents as $key => $content) {
                if (!isset($oldcontents[$key]) or $content !== $oldcontents[$key]) {
                    $rec->id = $contentid;
                    $DB->update_record('dataform_contents', $rec);
                    $updated = true;
                    break;
                }
            }
        }

        if ($updated) {
            // Trigger an event for updating this field.
            $eventparams = $this->default_event_params;
            $eventparams['objectid'] = $rec->id;
            $eventparams['other']['entryid'] = $entry->id;
            $event = \mod_dataform\event\field_content_updated::create($eventparams);
            $event->add_record_snapshot('dataform_contents', $rec);
            $event->trigger();

            return $rec->id;
        }

        return false;
    }

    /**
     *
     */
    public function duplicate_content($entry, $newentry) {
        return true;
    }

    /**
     * delete all content associated with the field
     */
    public function delete_content($entryid = 0) {
        global $DB;

        if ($entryid) {
            $params = array('fieldid' => $this->id, 'entryid' => $entryid);
        } else {
            $params = array('fieldid' => $this->id);
        }

        $rs = $DB->get_recordset('dataform_contents', $params);
        if ($rs->valid()) {
            $fs = get_file_storage();
            foreach ($rs as $content) {
                $fs->delete_area_files($this->df->context->id, 'mod_dataform', 'content', $content->id);
            }
        }
        $rs->close();

        return $DB->delete_records('dataform_contents', $params);
    }

    /**
     * transfer all content associated with the field
     */
    public function transfer_content($tofieldid) {
        global $CFG, $DB;

        if ($contents = $DB->get_records('dataform_contents', array('fieldid' => $this->id))) {
            if (!$tofieldid) {
                return false;
            } else {
                foreach ($contents as $content) {
                    $content->fieldid = $tofieldid;
                    $DB->update_record('dataform_contents', $content);
                }

                // Rename content dir if exists.
                $path = $CFG->dataroot.'/'.$this->df->course->id.'/'.$CFG->moddata.'/dataform/'.$this->df->id;
                $olddir = "$path/". $this->id;
                $newdir = "$path/$tofieldid";
                file_exists($olddir) and rename($olddir, $newdir);
                return true;
            }
        }
        return false;
    }

    /**
     * Returns an array of distinct content of the field.
     *
     * @param string $element
     * @param int $sortdir Sort direction 0|1 ASC|DESC
     * @return array
     */
    public function get_distinct_content($element, $sortdir = 0) {
        global $DB;

        if (is_null($this->_distinctvalues)) {
            $this->_distinctvalues = array();
            $fieldid = $this->id;
            $sortdir = $sortdir ? 'DESC' : 'ASC';
            $contentname = $this->get_sort_sql();
            $sql = "SELECT DISTINCT $contentname
                        FROM {dataform_contents} c$fieldid
                        WHERE c$fieldid.fieldid = $fieldid AND $contentname IS NOT NULL
                        ORDER BY $contentname $sortdir";

            if ($options = $DB->get_records_sql($sql)) {
                foreach ($options as $data) {
                    $value = $data->content;
                    if ($value === '') {
                        continue;
                    }
                    $this->_distinctvalues[] = $value;
                }
            }
        }
        return $this->_distinctvalues;
    }

    /**
     * Returns from the specified (form) data an associative array of content values of the field
     * for the specified entry. The content keys are specified in {@link dataformfield::content_names()}.
     *
     * @param int $entryid
     * @param stdClass $data
     * @return array
     */
    public function get_content_from_data($entryid, $data) {
        $fieldid = $this->id;

        $content = array();
        foreach ($this->content_names() as $name) {
            $delim = $name ? '_' : '';
            $contentname = "field_{$fieldid}_$entryid". $delim. $name;
            if (isset($data->$contentname) and !$this->content_is_empty($contentname, $data->$contentname)) {
                $content[$name] = $data->$contentname;
            }
        }

        return $content;
    }

    /**
     * Loads the field content in the entry and returns the entry.
     * This will fetch from DB and add to the entry object any of the field
     * content that is not already there.
     *
     * @param stdClass $entry
     * @return stdClass
     */
    public function load_entry_content($entry) {
        global $DB;

        $fieldid = $this->id;

        // Must have content parts.
        if (!$contentvars = $this->content_parts) {
            return $entry;
        }

        $fetch = false;

        // Make sure we have the field content in the entry.
        if (!isset($entry->{"c{$fieldid}_id"})) {
            $fetch = true;
        } else {
            foreach ($contentvars as $var) {
                if (!isset($entry->{"c{$fieldid}_$var"})) {
                    $fetch = true;
                    break;
                }
            }
        }

        if ($fetch) {
            $params = array('fieldid' => $fieldid, 'entryid' => $entry->id);
            if (!$content = $DB->get_record('dataform_contents', $params)) {
                return $entry;
            }
            // Add the content to the entry.
            $entry->{"c{$fieldid}_id"} = $content->id;
            foreach ($contentvars as $var) {
                $entry->{"c{$fieldid}_$var"} = $content->$var;
            }
        }
        return $entry;
    }

    /**
     * Validate form data in entries form
     */
    public function validate($eid, $patterns, $formdata) {
        return $this->renderer->validate_data($eid, $patterns, $formdata);
    }

    /**
     *
     */
    public function get_content_parts() {
        return array('content');
    }

    /**
     * Returns a list of form data content names for the field.
     *
     * @return array
     */
    public function content_names() {
        return array('');
    }

    /**
     * Returns true if content for content name is empty.
     * Subtypes may need to override if they require a different test from empty().
     *
     * @param string $contentname A content name from {@link dataformfield::content_names()}
     * @return string|array $content
     * @return bool
     */
    public function content_is_empty($contentname, $content) {
        // Null content is empty.
        if (is_null($content)) {
            return true;
        }

        // String content '' is empty.
        if (is_string($content)) {
            $content = trim($content);
            return ($content === '');
        }
        return false;
    }

    /**
     *
     */
    protected function format_content($entry, array $values = null) {
        $fieldid = $this->id;
        $oldcontents = array();
        $contents = array();

        // Old content.
        if (isset($entry->{"c{$fieldid}_content"})) {
            $oldcontents[] = $entry->{"c{$fieldid}_content"};
        }
        // New content.
        if (!empty($values)) {
            $content = reset($values);
            $contents[] = (string) clean_param($content, PARAM_NOTAGS);
        }
        return array($contents, $oldcontents);
    }

    /**
     * Returns a filearea name for the field.
     * The file area name is composed of the field name and additional suffix if provided.
     *
     * @param string $suffix Additional name component
     * @return string|null
     */
    protected function filearea($suffix = null) {
        if (!$this->name) {
            return null;
        }
        $filearea = 'field-'. str_replace(' ', '_', $this->name);
        if (!empty($suffix)) {
            $filearea = $filearea. str_replace(' ', '_', $suffix);
        }
        return $filearea;
    }

    // IMPORT EXPORT.
    /**
     * Adds to the data the import content for the field.
     * Assumes one applicable pattern for the field. If there are more or none,
     * this methods should be overriden in the subclass.
     *
     * @param array $importsettings
     * @param array $csvrecord
     * @param int $entryid
     * @return stdClass The data object
     */
    public function prepare_import_content($data, $importsettings, $csvrecord = null, $entryid = 0) {
        $fieldid = $this->id;
        $csvname = '';

        $setting = reset($importsettings);
        if (!empty($setting['name'])) {
            $csvname = $setting['name'];
        }

        if ($csvname and isset($csvrecord[$csvname]) and $csvrecord[$csvname] !== '') {
            $data->{"field_{$fieldid}_{$entryid}"} = $csvrecord[$csvname];
        }

        return $data;
    }

    // SQL MANAGEMENT.
    /**
     * Whether this field content resides in dataform_contents
     *
     * @return bool
     */
    public function is_dataform_content() {
        return true;
    }

    /**
     * Whether this field provides join sql for fetching content
     *
     * @return bool
     */
    public function is_joined() {
        return false;
    }

    /**
     *
     */
    public function get_select_sql() {
        if ($this->id > 0) {
            $alias = $this->get_sql_alias();

            $arr = array();
            $arr[] = " $alias.id AS {$alias}_id ";
            foreach ($this->get_content_parts() as $part) {
                $arr[] = $this->get_sql_compare_text($part). " AS {$alias}_$part";
            }
            $selectsql = implode(',', $arr);
            return " $selectsql ";
        } else {
            return null;
        }
    }

    /**
     *
     * @param array
     * @return string
     */
    public function format_search_value($searchparams) {
        return implode(' ', $searchparams);
    }

    /**
     * Returns {@link DB::sql_compare_text()} for the specified field column.
     *
     * @param string $element The DB element of the field
     * @return string
     */
    protected function get_sql_compare_text($element = 'content') {
        global $DB;

        $alias = $this->get_sql_alias();
        return $DB->sql_compare_text("$alias.$element");
    }

    /**
     * Returns the field alias for sql queries.
     *
     * @param string The field element to query
     * @return string
     */
    protected function get_sql_alias($element = null) {
        $fieldid = $this->id;
        return "c$fieldid";
    }

    // Sort.
    /**
     *
     */
    public function get_sort_sql($element = null) {
        return $this->get_sql_compare_text($element);
    }

    /**
     *
     */
    public function get_sort_from_sql() {
        $fieldid = $this->id;
        if ($fieldid > 0) {
            $sql = " LEFT JOIN {dataform_contents} c$fieldid ON c$fieldid.fieldid = $fieldid AND c$fieldid.entryid = e.id ";
            return array($sql, null);
        } else {
            return null;
        }
    }

    /**
     * Returns array of sort options menu as
     * $fieldid,element => name, for the filter form.
     * Override if the field is non-sortable or has different sort options.
     * Elements:
     * - Content
     *
     * @return array
     */
    public function get_sort_options_menu() {
        $fieldid = $this->id;
        $fieldname = $this->name;
        return array(
            "$fieldid,content" => "$fieldname ". get_string('content'),
        );
    }

    // Search.
    /**
     * Converts the given search string to its content representation.
     *
     * @param string
     * @return mixed
     */
    public function get_search_value($value) {
        return $value;
    }

    /**
     *
     */
    public function get_search_sql($search) {
        global $DB;

        list($element, $not, $operator, $value) = $search;
        $params = array();

        $isdataformcontent = $this->is_dataform_content();

        // NOT/IS EMPTY.
        if ($operator === '') {
            // Get entry ids where field has content.
            $eids = $this->get_entry_ids_for_element($element);

            if ($not) {
                // NOT EMPTY.
                if (!$eids) {
                    $eids = array(-1);
                    $isdataformcontent = false;
                }
                list($inids, $params) = $DB->get_in_or_equal($eids);
            } else {
                // IS EMPTY.
                if (!$eids) {
                    // No entry with content so no need for criterion.
                    return null;
                } else {
                    list($inids, $params) = $DB->get_in_or_equal($eids, SQL_PARAMS_QM, '', false);
                    $isdataformcontent = false;
                }
            }
            $sql = " e.id $inids ";
            return array($sql, $params, $isdataformcontent);
        }

        // SOMETHING.
        $varcharcontent = $this->get_sql_compare_text($element);

        if ($operator === '=') {
            // EQUAL.
            $searchvalue = trim($value);
            list($sql, $params) = $DB->get_in_or_equal($searchvalue);
            $sql = " $varcharcontent $sql ";
        } else if ($operator === 'IN') {
            // IN.
            $searchvalue = is_array($value) ? $value : array_map('trim', explode(',', $value));
            list($sql, $params) = $DB->get_in_or_equal($searchvalue);
            $sql = " $varcharcontent $sql ";
        } else if ($operator === 'BETWEEN') {
            // BETWEEN.
            $searchvalue = is_array($value) ? $value : array_map('trim', explode(',', $value));
            list($arg1, $arg2) = ($searchvalue + array(''));
            $params[] = $arg1;
            $params[] = $arg2;
            $sql = " ($not $varcharcontent >= ? AND $varcharcontent <= ?) ";
        } else if ($operator === 'LIKE') {
            // LIKE.
            $params = array("%$value%");
            $sql = $DB->sql_like($varcharcontent, '?', false);
        } else {
            $params = array($value);
            $sql = " $varcharcontent $operator ? ";
        }

        // NOT SOMETHING.
        // For all NOT something criteria,
        // exclude entries which don't meet the positive criterion
        // because some fields may not have content records
        // and the respective entries may be filter out
        // despite meeting the criterion.
        if ($not and $operator !== '') {
            // Get entry ids for entries that meet the criterion.
            $eids = $this->get_entry_ids_for_content($sql, $params);

            if ($eids) {
                // Get NOT IN sql.
                list($notinids, $params) = $DB->get_in_or_equal($eids, SQL_PARAMS_QM, '', false);
                $sql = " e.id $notinids ";
                return array($sql, $params, false);
            } else {
                // No content entries so no need for this criterion.
                return null;
            }
        }

        return array($sql, $params, $isdataformcontent);
    }

    /**
     *
     */
    public function get_search_from_sql() {
        $fieldid = $this->id;
        if ($fieldid > 0) {
            return " JOIN {dataform_contents} c$fieldid ON c$fieldid.entryid = e.id AND c$fieldid.fieldid = $fieldid";
        } else {
            return '';
        }
    }

    /**
     * Return array of search options menu as
     * $fieldid,element => name, for the filter form.
     * By default sortable options are also the searchable ones.
     * Override if the field has different search options from sort options.
     *
     * @return array
     */
    public function get_search_options_menu() {
        return $this->sort_options_menu;
    }

    /**
     * Returns a list of elements to which quick search may be applied.
     *
     * @return array
     */
    public function get_simple_search_elements() {
        $elements = array();
        if ($searchoptionsmenu = $this->search_options_menu) {
            // Extract element names from the keys.
            foreach (array_keys($searchoptionsmenu) as $pair) {
                list($fieldid, $element) = explode(',', $pair);
                $elements[] = $element;
            }
        }
        return $elements;
    }

    /**
     * Returns array entry ids where the entry has any content in the given element.
     * The default implementation is for fields whose content resides in the content
     * field of dataform contents. In this case we retrieve the entry ids from all
     * field content records we can find.
     * Other fields have to override.
     *
     * @return null|array
     */
    public function get_entry_ids_for_element($element) {
        return $this->get_entry_ids_for_content();
    }

    /**
     * Returns array of ids of entry which contain a certain content
     * as specified in the passed sql.
     *
     * @return null|array
     */
    public function get_entry_ids_for_content($sqlwhere = '', array $params = array()) {
        global $DB;

        $searchtable = $this->get_search_from_sql();
        $sql = "
            SELECT DISTINCT e.id
            FROM {dataform_entries} e $searchtable
        ";

        if (!empty($sqlwhere)) {
            $sql .= " WHERE $sqlwhere ";
        }

        if ($entryids = $DB->get_records_sql_menu($sql, $params)) {
            return array_keys($entryids);
        }
        return null;
    }

}
