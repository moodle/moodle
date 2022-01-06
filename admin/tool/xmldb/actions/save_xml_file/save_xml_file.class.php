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
 * This class will save one edited xml file
 *
 * This class will save the in-session xml structure to its
 * corresponding xml file, optionally reloading it if editing
 * is going to continue (unload=false). Else (default) the
 * file is unloaded once saved.
 *
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class save_xml_file extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

        // Set own custom attributes

        // Get needed strings
        $this->loadStrings(array(
            'filenotwriteable' => 'tool_xmldb'
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

        // These are always here
        global $CFG, $XMLDB;

        // Do the job, setting result as needed

        // Get the dir containing the file
        $dirpath = required_param('dir', PARAM_PATH);
        $dirpath = $CFG->dirroot . $dirpath;
        $unload = optional_param('unload', true, PARAM_BOOL);

        // Get the edited dir
        if (!empty($XMLDB->editeddirs)) {
            if (isset($XMLDB->editeddirs[$dirpath])) {
                $editeddir = $XMLDB->editeddirs[$dirpath];
            }
        }
        // Copy the edited dir over the original one
        if (!empty($XMLDB->dbdirs)) {
            if (isset($XMLDB->dbdirs[$dirpath])) {
                $XMLDB->dbdirs[$dirpath] = unserialize(serialize($editeddir));
                $dbdir = $XMLDB->dbdirs[$dirpath];
            }
        }

        // Check for perms
        if (!is_writeable($dirpath . '/install.xml')) {
            $this->errormsg = $this->str['filenotwriteable'] . '(' . $dirpath . '/install.xml)';
            return false;
        }

        // Save the original dir
        $result = $dbdir->xml_file->saveXMLFile();

        if ($result) {
            // Delete the edited dir
            unset ($XMLDB->editeddirs[$dirpath]);
            // Unload de originaldir
            unset($XMLDB->dbdirs[$dirpath]->xml_file);
            unset($XMLDB->dbdirs[$dirpath]->xml_loaded);
            unset($XMLDB->dbdirs[$dirpath]->xml_changed);
            unset($XMLDB->dbdirs[$dirpath]->xml_exists);
            unset($XMLDB->dbdirs[$dirpath]->xml_writeable);
        } else {
            $this->errormsg = 'Error saving XML file (' . $dirpath . ')';
            return false;
        }

        // If unload has been disabled, simulate it by reloading the file now
        if (!$unload) {
            return $this->launch('load_xml_file');
        }

        // Launch postaction if exists (leave this here!)
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

        // Return ok if arrived here
        return $result;
    }
}

