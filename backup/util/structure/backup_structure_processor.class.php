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
 * @package    moodlecore
 * @subpackage backup-structure
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * TODO: Finish phpdocs
 */

/**
 * Instantiable class defining the process of backup structures
 *
 * This class will process the given backup structure (nested/final/attribute)
 * based on its definition, triggering as many actions as necessary (pre/post
 * triggers, ids annotations, deciding based on settings, xml output...). Somehow
 * one visitor pattern to allow backup structures to work with nice decoupling
 */
class backup_structure_processor extends base_processor {

    protected $writer; // xml_writer where the processor is going to output data
    protected $vars;   // array of backup::VAR_XXX => helper value pairs to be used by source specifications

    /**
     * @var \core\progress\base Progress tracker (null if none)
     */
    protected $progress;

    /**
     * Constructor.
     *
     * @param xml_writer $writer XML writer to save data
     * @param c\core\progress\base$progress Progress tracker (optional)
     */
    public function __construct(xml_writer $writer, \core\progress\base $progress = null) {
        $this->writer = $writer;
        $this->progress = $progress;
        $this->vars = array();
    }

    public function set_var($key, $value) {
        if (isset($this->vars[$key])) {
            throw new backup_processor_exception('processorvariablealreadyset', $key);
        }
        $this->vars[$key] = $value;
    }

    public function get_var($key) {
        if (!isset($this->vars[$key])) {
            throw new backup_processor_exception('processorvariablenotfound', $key);
        }
        return $this->vars[$key];
    }

    public function pre_process_nested_element(base_nested_element $nested) {
        // Send open tag to xml_writer
        $attrarr = array();
        foreach ($nested->get_attributes() as $attribute) {
            $attrarr[$attribute->get_name()] = $attribute->get_value();
        }
        $this->writer->begin_tag($nested->get_name(), $attrarr);
    }

    public function process_nested_element(base_nested_element $nested) {
        // Proceed with all the file annotations for this element
        $fileannotations = $nested->get_file_annotations();
        if ($fileannotations) { // If there are areas to search
            $backupid  = $this->get_var(backup::VAR_BACKUPID);
            foreach ($fileannotations as $component => $area) {
                foreach ($area as $filearea => $info) {
                    $contextid = !is_null($info->contextid) ? $info->contextid : $this->get_var(backup::VAR_CONTEXTID);
                    $itemid    = !is_null($info->element) ? $info->element->get_value() : null;
                    backup_structure_dbops::annotate_files($backupid, $contextid, $component, $filearea, $itemid);
                }
            }
        }
    }

    public function post_process_nested_element(base_nested_element $nested) {
        // Send close tag to xml_writer
        $this->writer->end_tag($nested->get_name());
        if ($this->progress) {
            $this->progress->progress();
        }
    }

    public function process_final_element(base_final_element $final) {
        // Send full tag to xml_writer and annotations (only if has value)
        if ($final->is_set()) {
            $attrarr = array();
            foreach ($final->get_attributes() as $attribute) {
                $attrarr[$attribute->get_name()] = $attribute->get_value();
            }
            $this->writer->full_tag($final->get_name(), $final->get_value(), $attrarr);
            if ($this->progress) {
                $this->progress->progress();
            }
            // Annotate current value if configured to do so
            $final->annotate($this->get_var(backup::VAR_BACKUPID));
        }
    }

    public function process_attribute(base_attribute $attribute) {
        // Annotate current value if configured to do so
        $attribute->annotate($this->get_var(backup::VAR_BACKUPID));
    }
}

/**
 * backup_processor exception to control all the errors while working with backup_processors
 *
 * This exception will be thrown each time the backup_processors detects some
 * inconsistency related with the elements to process or its configuration
 */
class backup_processor_exception extends base_processor_exception {

    /**
     * Constructor - instantiates one backup_processor_exception
     *
     * @param string $errorcode key for the corresponding error string
     * @param object $a extra words and phrases that might be required in the error string
     * @param string $debuginfo optional debugging information
     */
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
