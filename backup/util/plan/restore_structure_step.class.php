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
    protected $pathelements; // Array of pathelements to process

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $filename, $task = null) {
        if (!is_null($task) && !($task instanceof restore_task)) {
            throw new restore_step_exception('wrong_restore_task_specified');
        }
        $this->filename = $filename;
        $this->pathelements = array();
        parent::__construct($name, $task);
    }

    public function execute() {

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
        $this->pathelements = $this->prepare_pathelements($this->define_structure());

        // Create parser and processor
        $xmlparser = new progressive_parser();
        $xmlparser->set_file($fullpath);
        $xmlprocessor = new restore_structure_parser_processor($this->task->get_courseid(), $this);
        $xmlparser->set_processor($xmlprocessor);

        // Add pathelements to processor
        foreach ($this->pathelements as $element) {
            $xmlprocessor->add_path($element->get_path(), $element->is_grouped());
        }

        // And process it, dispatch to target methods in step will start automatically
        $xmlparser->process();
    }

// Protected API starts here

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
     * Function that will return the structure to be processed by this restore_step.
     * Must return one array of @restore_path_element elements
     */
    abstract protected function define_structure();
}
