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
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This class will display the XML for one index being edited
 *
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_index_xml extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

        // Set own custom attributes
        $this->sesskey_protected = false; // This action doesn't need sesskey protection

        // Get needed strings
        $this->loadStrings(array(
            // 'key' => 'module',
        ));
    }

    /**
     * Invoke method, every class will have its own
     * returns true/false on completion, setting both
     * errormsg and output as necessary
     */
    function invoke() {
        parent::invoke();

        $result = true;

        // Set own core attributes
        $this->does_generate = ACTION_GENERATE_XML;

        // These are always here
        global $CFG, $XMLDB;

        // Do the job, setting result as needed

        // Get the file parameter
        $index =  required_param('index', PARAM_PATH);
        $table =  required_param('table', PARAM_PATH);
        $select = required_param('select', PARAM_ALPHA); //original/edited
        // Get the dir containing the file
        $dirpath = required_param('dir', PARAM_PATH);
        $dirpath = $CFG->dirroot . $dirpath;

        // Get the correct dir
        if ($select == 'original') {
            if (!empty($XMLDB->dbdirs)) {
                $base = $XMLDB->dbdirs[$dirpath];
            }
        } else if ($select == 'edited') {
            if (!empty($XMLDB->editeddirs)) {
                $base = $XMLDB->editeddirs[$dirpath];
            }
        } else {
            $this->errormsg = 'Cannot access to ' . $select . ' info';
            $result = false;
        }
        if ($base) {
            // Only if the directory exists and it has been loaded
            if (!$base->path_exists || !$base->xml_loaded) {
                $this->errormsg = 'Directory ' . $dirpath . ' not loaded';
                return false;
            }
        } else {
            $this->errormsg = 'Problem handling ' . $select . ' files';
            return false;
        }

        // Get the structure
        if ($result) {
            if (!$structure = $base->xml_file->getStructure()) {
                $this->errormsg = 'Error retrieving ' . $select . ' structure';
                $result = false;
            }
        }
        // Get the tables
        if ($result) {
            if (!$tables = $structure->getTables()) {
                $this->errormsg = 'Error retrieving ' . $select . ' tables';
                $result = false;
            }
        }
        // Get the table
        if ($result && !$t = $structure->getTable($table)) {
            $this->errormsg = 'Error retrieving ' . $table . ' table';
            $result = false;
        }
        // Get the indexes
        if ($result) {
            if (!$indexes = $t->getIndexes()) {
                $this->errormsg = 'Error retrieving ' . $select . ' indexes';
                $result = false;
            }
        }
        // Get the index
        if ($result && !$i = $t->getIndex($index)) {
            $this->errormsg = 'Error retrieving ' . $index . ' index';
            $result = false;
        }

        if ($result) {
            // Everything is ok. Generate the XML output
            $this->output = $i->xmlOutput();
        } else {
            // Switch to HTML and error
            $this->does_generate = ACTION_GENERATE_HTML;
        }

        // Return ok if arrived here
        return $result;
    }
}

