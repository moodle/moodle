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
 * This class will display one XML file
 *
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_xml extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();
        // Set own core attributes
        $this->can_subaction = ACTION_NONE;
        //$this->can_subaction = ACTION_HAVE_SUBACTIONS;

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
     * @return mixed
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
        $file = required_param('file', PARAM_PATH);

        $fullpath = $CFG->dirroot . $file;
        // File param must start with / and end with /db/install.xml to be safe.
        if (substr($file, 0, 1) == '/' &&
            substr($file, -15, 15) == '/db/install.xml') {
            // Everything is ok. Load the file to memory
            $this->output = file_get_contents($fullpath);
        } else {
            // Switch to HTML and error
            $this->does_generate = ACTION_GENERATE_HTML;
            $this->errormsg = 'File not viewable (' . $file .')';
            $result = false;
        }

        // Return ok if arrived here
        return $result;
    }
}

