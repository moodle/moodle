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
 * @package moodlecore
 * @subpackage backup-plan
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Abstract class defining the needed stuff to restore one xml file
 *
 * TODO: Finish phpdocs
 */
abstract class restore_structure_step extends restore_step {

    protected $filename; // Name of the file to be parsed
    protected $contentprocessor; // xml parser processor being used
                                 // (need it here, apart from parser
                                 // thanks to serialized data to process -
                                 // say thanks to blocks!)
    protected $pathelements;  // Array of pathelements to process
    protected $elementsoldid; // Array to store last oldid used on each element
    protected $elementsnewid; // Array to store last newid used on each element

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $filename, $task = null) {
        if (!is_null($task) && !($task instanceof restore_task)) {
            throw new restore_step_exception('wrong_restore_task_specified');
        }
        $this->filename = $filename;
        $this->contentprocessor = null;
        $this->pathelements = array();
        $this->elementsoldid = array();
        $this->elementsnewid = array();
        parent::__construct($name, $task);
    }

    public function execute() {

        if (!$this->execute_condition()) { // Check any condition to execute this
            return;
        }

        $fullpath = $this->task->get_taskbasepath();

        // We MUST have one fullpath here, else, error
        if (empty($fullpath)) {
            throw new restore_step_exception('restore_structure_step_undefined_fullpath');
        }

        // Append the filename to the fullpath
        $fullpath = rtrim($fullpath, '/') . '/' . $this->filename;

        // And it MUST exist
        if (!file_exists($fullpath)) { // Shouldn't happen ever, but...
            throw new restore_step_exception('missing_moodle_backup_xml_file', $fullpath);
        }

        // Get restore_path elements array adapting and preparing it for processing
        $structure = $this->define_structure();
        if (!is_array($structure)) {
            throw new restore_step_exception('restore_step_structure_not_array', $this->get_name());
        }
        $this->pathelements = $this->prepare_pathelements($structure);

        // Populate $elementsoldid and $elementsoldid based on available pathelements
        foreach ($this->pathelements as $pathelement) {
            $this->elementsoldid[$pathelement->get_name()] = null;
            $this->elementsnewid[$pathelement->get_name()] = null;
        }

        // Create parser and processor
        $xmlparser = new progressive_parser();
        $xmlparser->set_file($fullpath);
        $xmlprocessor = new restore_structure_parser_processor($this->task->get_courseid(), $this);
        $this->contentprocessor = $xmlprocessor; // Save the reference to the contentprocessor
                                                 // as far as we are going to need it out
                                                 // from parser (blame serialized data!)
        $xmlparser->set_processor($xmlprocessor);

        // Add pathelements to processor
        foreach ($this->pathelements as $element) {
            $xmlprocessor->add_path($element->get_path(), $element->is_grouped());
        }

        // And process it, dispatch to target methods in step will start automatically
        $xmlparser->process();

        // Have finished, call to the after_execute method
        $this->after_execute();
    }

    /**
     * Receive one chunk of information form the xml parser processor and
     * dispatch it, following the naming rules
     */
    public function process($data) {
        if (!array_key_exists($data['path'], $this->pathelements)) { // Incorrect path, must not happen
            throw new restore_step_exception('restore_structure_step_missing_path', $data['path']);
        }
        $element = $this->pathelements[$data['path']];
        $object = $element->get_processing_object();
        $method = $element->get_processing_method();
        if (empty($object)) { // No processing object defined
            throw new restore_step_exception('restore_structure_step_missing_pobject', $object);
        }
        $rdata = $object->$method($data['tags']); // Dispatch to proper object/method
        if ($rdata !== null) { // If the method has returned any info, set element data to it
            $element->set_data($rdata);
        } else {               // Else, put the original parsed data
            $element->set_data($data);
        }
    }

// Protected API starts here

    /**
     * This method will be executed after the whole structure step have been processed
     *
     * After execution method for code needed to be executed after the whole structure
     * has been processed. Useful for cleaning tasks, files process and others. Simply
     * overwrite in in your steps if needed
     */
    protected function after_execute() {
        // do nothing by default
    }

    /**
     * Prepare the pathelements for processing, looking for duplicates, applying
     * processing objects and other adjustments
     */
    protected function prepare_pathelements($elementsarr) {

        // First iteration, push them to new array, indexed by name
        // detecting duplicates in names or paths
        $names = array();
        $paths = array();
        foreach($elementsarr as $element) {
            if (!$element instanceof restore_path_element) {
                throw new restore_step_exception('restore_path_element_wrong_class', get_class($element));
            }
            if (array_key_exists($element->get_name(), $names)) {
                throw new restore_step_exception('restore_path_element_name_alreadyexists', $element->get_name());
            }
            if (array_key_exists($element->get_path(), $paths)) {
                throw new restore_step_exception('restore_path_element_path_alreadyexists', $element->get_path());
            }
            $names[$element->get_name()] = true;
            $elements[$element->get_path()] = $element;
        }
        // Now, for each element not having one processing object, if
        // not child of grouped element, assign $this (the step itself) as processing element
        // Note method must exist or we'll get one @restore_path_element_exception
        foreach($elements as $key => $pelement) {
            if ($pelement->get_processing_object() === null && !$this->grouped_parent_exists($pelement, $elements)) {
                $elements[$key]->set_processing_object($this);
            }
        }
        // Done, return them
        return $elements;
    }

    /**
     * Given one pathelement, return true if grouped parent was found
     */
    protected function grouped_parent_exists($pelement, $elements) {
        foreach ($elements as $element) {
            if ($pelement->get_path() == $element->get_path()) {
                continue; // Don't compare against itself
            }
            // If element is grouped and parent of pelement, return true
            if ($element->is_grouped() and strpos($pelement->get_path() .  '/', $element->get_path()) === 0) {
                return true;
            }
        }
        return false; // no grouped parent found
    }

    /**
     * To conditionally decide if one step will be executed or no
     *
     * For steps needing to be executed conditionally, based in dynamic
     * conditions (at execution time vs at declaration time) you must
     * override this function. It will return true if the step must be
     * executed and false if not
     */
    protected function execute_condition() {
        return true;
    }

    /**
     * To send ids pairs to backup_ids_table and to store them into paths
     *
     * This method will send the given itemname and old/new ids to the
     * backup_ids_temp table, and, at the same time, will save the new id
     * into the corresponding restore_path_element for easier access
     * by children. Also will inject the known old context id for the task
     * in case it's going to be used for restoring files later
     */
    protected function set_mapping($itemname, $oldid, $newid, $restorefiles = false, $filesctxid = null) {
        // If we haven't specified one context for the files, use the task one
        if ($filesctxid == null) {
            $parentitemid = $restorefiles ? $this->task->get_old_contextid() : null;
        } else { // Use the specified one
            $parentitemid = $restorefiles ? $filesctxid : null;
        }
        // Let's call the low level one
        restore_dbops::set_backup_ids_record($this->get_restoreid(), $itemname, $oldid, $newid, $parentitemid);
        // Now, if the itemname matches any pathelement->name, store the latest $newid
        if (array_key_exists($itemname, $this->elementsoldid)) { // If present in  $this->elementsoldid, is valid, put both ids
            $this->elementsoldid[$itemname] = $oldid;
            $this->elementsnewid[$itemname] = $newid;
        }
    }

    /**
     * Returns the latest (parent) old id mapped by one pathelement
     */
    protected function get_old_parentid($itemname) {
        return array_key_exists($itemname, $this->elementsoldid) ? $this->elementsoldid[$itemname] : null;
    }

    /**
     * Returns the latest (parent) new id mapped by one pathelement
     */
    protected function get_new_parentid($itemname) {
        return array_key_exists($itemname, $this->elementsnewid) ? $this->elementsnewid[$itemname] : null;
    }

    /**
     * Return the new id of a mapping for the given itemname
     *
     */
    protected function get_mappingid($itemname, $oldid) {
        $mapping = $this->get_mapping($itemname, $oldid);
        return $mapping ? $mapping->newitemid : false;
    }

    /**
     * Return the complete mapping from the given itemname, itemid
     */
    protected function get_mapping($itemname, $oldid) {
        return restore_dbops::get_backup_ids_record($this->get_restoreid(), $itemname, $oldid);
    }

    /**
     * Add all the existing file, given their component and filearea and one backup_ids itemname to match with
     */
    protected function add_related_files($component, $filearea, $mappingitemname, $filesctxid = null) {
        $filesctxid = is_null($filesctxid) ? $this->task->get_old_contextid() : $filesctxid;
        restore_dbops::send_files_to_pool($this->get_basepath(), $this->get_restoreid(), $component,
                                          $filearea, $filesctxid, $mappingitemname);
    }

    /**
     * Apply course startdate offset based in original course startdate and course_offset_startdate setting
     * Note we are using one static cache here, but *by restoreid*, so it's ok for concurrence/multiple
     * executions in the same request
     */
    protected function apply_date_offset($value) {

        static $cache = array();
        // Lookup cache
        if (isset($cache[$this->get_restoreid()])) {
            return $value + $cache[$this->get_restoreid()];
        }
        // No cache, let's calculate the offset
        $original = $this->task->get_info()->original_course_startdate;
        $setting  = $this->get_setting_value('course_startdate');

        // Original course has not startdate, offset = 0
        if (empty($original)) {
            $cache[$this->get_restoreid()] = 0;

        // Less than 24h of difference, offset = 0 (this avoids some problems with timezones)
        } else if (abs($setting - $original) < 24 * 60 * 60) {
            $cache[$this->get_restoreid()] = 0;

        // Re-enforce 'moodle/restore:rolldates' capability for the user in the course, just in case
        } else if (!has_capability('moodle/restore:rolldates',
                                   get_context_instance(CONTEXT_COURSE, $this->get_courseid()),
                                   $this->task->get_userid())) {
            $cache[$this->get_restoreid()] = 0;

        // Arrived here, let's calculate the real offset
        } else {
            $cache[$this->get_restoreid()] = $setting - $original;
        }

        // Return the passed value with cached offset applied
        return $value + $cache[$this->get_restoreid()];
    }

    /**
     * Function that will return the structure to be processed by this restore_step.
     * Must return one array of @restore_path_element elements
     */
    abstract protected function define_structure();
}
