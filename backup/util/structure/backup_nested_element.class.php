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
 * Instantiable class representing one nestable element (non final) piece of information on backup
 */
class backup_nested_element extends base_nested_element implements processable {

    protected $var_array; // To be used in case we pass one in-memory structure
    protected $table;     // Table (without prefix) to fetch records from
    protected $tablesortby; // The field to sort by when using the table methods
    protected $sql;       // Raw SQL to fetch records from
    protected $params;    // Unprocessed params as specified in the set_source() call
    protected $procparams;// Processed (path resolved) params array
    protected $aliases;   // Define DB->final element aliases
    protected $fileannotations;   // array of file areas to be searched by file annotations
    protected $counter;   // Number of instances of this element that have been processed
    protected $results;  // Logs the results we encounter during the process.
    protected $logs;     // Some log messages that could be retrieved later.

    /**
     * Constructor - instantiates one backup_nested_element, specifying its basic info.
     *
     * @param string $name name of the element
     * @param array  $attributes attributes this element will handle (optional, defaults to null)
     * @param array  $final_elements this element will handle (optional, defaults to null)
     */
    public function __construct($name, $attributes = null, $final_elements = null) {
        parent::__construct($name, $attributes, $final_elements);
        $this->var_array = null;
        $this->table     = null;
        $this->tablesortby = null;
        $this->sql       = null;
        $this->params    = null;
        $this->procparams= null;
        $this->aliases   = array();
        $this->fileannotations = array();
        $this->counter   = 0;
        $this->results  = array();
        $this->logs     = array();
    }

    /**
     * Process the nested element
     *
     * @param object $processor the processor
     * @return void
     */
    public function process($processor) {
        if (!$processor instanceof base_processor) { // No correct processor, throw exception
            throw new base_element_struct_exception('incorrect_processor');
        }

        $iterator = $this->get_iterator($processor); // Get the iterator over backup-able data

        foreach ($iterator as $key => $values) { // Process each "ocurrrence" of the nested element (recordset or array)

            // Fill the values of the attributes and final elements with the $values from the iterator
            $this->fill_values($values);

            // Perform pre-process tasks for the nested_element
            $processor->pre_process_nested_element($this);

            // Delegate the process of each attribute
            foreach ($this->get_attributes() as $attribute) {
                $attribute->process($processor);
            }

            // Main process tasks for the nested element, once its attributes have been processed
            $processor->process_nested_element($this);

            // Delegate the process of each final_element
            foreach ($this->get_final_elements() as $final_element) {
                $final_element->process($processor);
            }

            // Delegate the process to the optigroup
            if ($this->get_optigroup()) {
                $this->get_optigroup()->process($processor);
            }

            // Delegate the process to each child nested_element
            foreach ($this->get_children() as $child) {
                $child->process($processor);
            }

            // Perform post-process tasks for the nested element
            $processor->post_process_nested_element($this);

            // Everything processed, clean values before next iteration
            $this->clean_values();

            // Increment counter for this element
            $this->counter++;

            // For root element, check we only have 1 element
            if ($this->get_parent() === null && $this->counter > 1) {
                throw new base_element_struct_exception('root_only_one_ocurrence', $this->get_name());
            }
        }
        // Close the iterator (DB recordset / array iterator)
        $iterator->close();
    }

    /**
     * Saves a log message to an array
     *
     * @see backup_helper::log()
     * @param string $message to add to the logs
     * @param int $level level of importance {@link backup::LOG_DEBUG} and other constants
     * @param mixed $a to be included in $message
     * @param int $depth of the message
     * @param display $bool supporting translation via get_string() if true
     * @return void
     */
    protected function add_log($message, $level, $a = null, $depth = null, $display = false) {
        // Adding the result to the oldest parent.
        if ($this->get_parent()) {
            $parent = $this->get_grandparent();
            $parent->add_log($message, $level, $a, $depth, $display);
        } else {
            $log = new stdClass();
            $log->message = $message;
            $log->level = $level;
            $log->a = $a;
            $log->depth = $depth;
            $log->display = $display;
            $this->logs[] = $log;
        }
    }

    /**
     * Saves the results to an array
     *
     * @param array $result associative array
     * @return void
     */
    protected function add_result($result) {
        if (is_array($result)) {
            // Adding the result to the oldest parent.
            if ($this->get_parent()) {
                $parent = $this->get_grandparent();
                $parent->add_result($result);
            } else {
                $this->results = array_merge($this->results, $result);
            }
        }
    }

    /**
     * Returns the logs
     *
     * @return array of log objects
     */
    public function get_logs() {
        return $this->logs;
    }

    /**
     * Returns the results
     *
     * @return associative array of results
     */
    public function get_results() {
        return $this->results;
    }

    public function set_source_array($arr) {
        // TODO: Only elements having final elements can set source
        $this->var_array = $arr;
    }

    public function set_source_table($table, $params, $sortby = null) {
        if (!is_array($params)) { // Check we are passing array
            throw new base_element_struct_exception('setsourcerequiresarrayofparams');
        }
        // TODO: Only elements having final elements can set source
        $this->table = $table;
        $this->procparams = $this->convert_table_params($params);
        if ($sortby) {
            $this->tablesortby = $sortby;
        }
    }

    public function set_source_sql($sql, $params) {
        if (!is_array($params)) { // Check we are passing array
            throw new base_element_struct_exception('setsourcerequiresarrayofparams');
        }
        // TODO: Only elements having final elements can set source
        $this->sql = $sql;
        $this->procparams = $this->convert_sql_params($params);
    }

    public function set_source_alias($dbname, $finalelementname) {
        // Get final element
        $finalelement = $this->get_final_element($finalelementname);
        if (!$finalelement) { // Final element incorrect, throw exception
            throw new base_element_struct_exception('incorrectaliasfinalnamenotfound', $finalelementname);
        } else {
            $this->aliases[$dbname] = $finalelement;
        }
    }

    public function annotate_files($component, $filearea, $elementname, $filesctxid = null) {
        if (!array_key_exists($component, $this->fileannotations)) {
            $this->fileannotations[$component] = array();
        }

        if ($elementname !== null) { // Check elementname is valid
            $elementname = $this->find_element($elementname); //TODO: no warning here? (skodak)
        }

        if (array_key_exists($filearea, $this->fileannotations[$component])) {
            throw new base_element_struct_exception('annotate_files_duplicate_annotation', "$component/$filearea/$elementname");
        }

        $info = new stdclass();
        $info->element   = $elementname;
        $info->contextid = $filesctxid;
        $this->fileannotations[$component][$filearea] = $info;
    }

    public function annotate_ids($itemname, $elementname) {
        $element = $this->find_element($elementname);
        $element->set_annotation_item($itemname);
    }

    /**
     * Returns one array containing the element in the
     * @backup_structure and the areas to be searched
     */
    public function get_file_annotations() {
        return $this->fileannotations;
    }

    public function get_source_array() {
        return $this->var_array;
    }

    public function get_source_table() {
        return $this->table;
    }

    public function get_source_table_sortby() {
        return $this->tablesortby;
    }

    public function get_source_sql() {
        return $this->sql;
    }

    public function get_counter() {
        return $this->counter;
    }

    /**
     * Simple filler that, matching by name, will fill both attributes and final elements
     * depending of this nested element, debugging info about non-matching elements and/or
     * elements present in both places. Accept both arrays and objects.
     */
    public function fill_values($values) {
        $values = (array)$values;

        foreach ($values as $key => $value) {
            $found = 0;
            if ($attribute = $this->get_attribute($key)) { // Set value for attributes
                $attribute->set_value($value);
                $found++;
            }
            if ($final = $this->get_final_element($key)) { // Set value for final elements
                $final->set_value($value);
                $found++;
            }
            if (isset($this->aliases[$key])) { // Last chance, set value by processing final element aliases
                $this->aliases[$key]->set_value($value);
                $found++;
            }
            // Found more than once, notice
                // TODO: Route this through backup loggers
            if ($found > 1) {
                debugging('Key found more than once ' . $key, DEBUG_DEVELOPER);
            }
        }

    }

// Protected API starts here

    protected function convert_table_params($params) {
        return $this->convert_sql_params($params);
    }

    protected function convert_sql_params($params) {
        $procparams = array(); // Reset processed params
        foreach ($params as $key => $param) {
            $procparams[$key] = $this->find_element($param);
        }
        return $procparams;
    }

    protected function find_element($param) {
        if ($param == backup::VAR_PARENTID) { // Look for first parent having id attribute/final_element
            $param = $this->find_first_parent_by_name('id');

        // If the param is array, with key 'sqlparam', return the value without modifications
        } else if (is_array($param) && array_key_exists('sqlparam', $param)) {
            return $param['sqlparam'];

        } else if (((int)$param) >= 0) {  // Search by path if param isn't a backup::XXX candidate
            $param = $this->find_element_by_path($param);
        }
        return $param; // Return the param unmodified
    }

    /**
     * Returns one instace of the @base_attribute class to work with
     * when attributes are added simply by name
     */
    protected function get_new_attribute($name) {
        return new backup_attribute($name);
    }

    /**
     * Returns one instace of the @final_element class to work with
     * when final_elements are added simply by name
     */
    protected function get_new_final_element($name) {
        return new backup_final_element($name);
    }

    /**
     * Returns one PHP iterator over each "ocurrence" of this nested
     * element (array or DB recordset). Delegated to backup_structure_dbops class
     */
    protected function get_iterator($processor) {
        return backup_structure_dbops::get_iterator($this, $this->procparams, $processor);
    }
}
