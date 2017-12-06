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
 * This class will load one XML file to memory if necessary
 *
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class load_xml_file extends XMLDBAction {

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
     */
    function invoke() {
        parent::invoke();

        $result = true;

        // Set own core attributes
        $this->does_generate = ACTION_NONE;
        //$this->does_generate = ACTION_GENERATE_HTML;

        // These are always here
        global $CFG, $XMLDB;

        // Do the job, setting $result as needed

        // Get the dir containing the file
        $dirpath = required_param('dir', PARAM_PATH);
        $dirpath = $CFG->dirroot . $dirpath;

        // Get the correct dir
        if (!empty($XMLDB->dbdirs)) {
            $dbdir = $XMLDB->dbdirs[$dirpath];
            if ($dbdir) {
                // Set some defaults
                $dbdir->xml_exists = false;
                $dbdir->xml_writeable = false;
                $dbdir->xml_loaded  = false;
                // Only if the directory exists
                if (!$dbdir->path_exists) {
                    return false;
                }
                $xmldb_file = new xmldb_file($dbdir->path . '/install.xml');
                // Set the XML DTD and schema
                $xmldb_file->setDTD($CFG->dirroot . '/lib/xmldb/xmldb.dtd');
                $xmldb_file->setSchema($CFG->dirroot . '/lib/xmldb/xmldb.xsd');
                // Set dbdir as necessary
                if ($xmldb_file->fileExists()) {
                    $dbdir->xml_exists = true;
                }
                if ($xmldb_file->fileWriteable()) {
                    $dbdir->xml_writeable = true;
                }
                // Load the XML contents to structure
                $loaded = $xmldb_file->loadXMLStructure();
                if ($loaded && $xmldb_file->isLoaded()) {
                    $dbdir->xml_loaded = true;
                    $dbdir->filemtime = filemtime($dbdir->path . '/install.xml');
                }
                $dbdir->xml_file = $xmldb_file;
            } else {
                $this->errormsg = 'Wrong directory (' . $dirpath . ')';
                $result = false;
            }
        } else {
            $this->errormsg = 'XMLDB structure not found';
            $result = false;
        }
        // Launch postaction if exists
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

        return $result;
    }
}

