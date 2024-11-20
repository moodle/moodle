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
 * This class will save changes in table name and/or comments
 *
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_table_save extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

        // Set own custom attributes

        // Get needed strings
        $this->loadStrings(array(
            'tablenameempty' => 'tool_xmldb',
            'incorrecttablename' => 'tool_xmldb',
            'duplicatetablename' => 'tool_xmldb',
            'back' => 'tool_xmldb',
            'administration' => ''
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
        //$this->does_generate = ACTION_NONE;
        $this->does_generate = ACTION_GENERATE_HTML;

        // These are always here
        global $CFG, $XMLDB;

        // Do the job, setting result as needed

        if (!data_submitted()) { // Basic prevention
            throw new \moodle_exception('wrongcall', 'error');
        }

        // Get parameters
        $dirpath = required_param('dir', PARAM_PATH);
        $dirpath = $CFG->dirroot . $dirpath;

        $tableparam = strtolower(required_param('table', PARAM_PATH));
        $name = substr(trim(strtolower(required_param('name', PARAM_PATH))),0,xmldb_table::NAME_MAX_LENGTH);
        $comment = required_param('comment', PARAM_CLEAN);
        $comment = $comment;

        $dbdir = $XMLDB->dbdirs[$dirpath];

        $editeddir = $XMLDB->editeddirs[$dirpath];
        $structure = $editeddir->xml_file->getStructure();
        $table = $structure->getTable($tableparam);

        $errors = array(); // To store all the errors found

        // Perform some checks
        // Check empty name
        if (empty($name)) {
            $errors[] = $this->str['tablenameempty'];
        }
        // Check incorrect name
        if ($name == 'changeme') {
            $errors[] = $this->str['incorrecttablename'];
        }
        // Check duplicatename
        if ($tableparam != $name && $structure->getTable($name)) {
            $errors[] = $this->str['duplicatetablename'];
        }

        if (!empty($errors)) {
            $temptable = new xmldb_table($name);
                // Prepare the output
            $o = '<p>' .implode(', ', $errors) . '</p>
                  <p>' . $temptable->getName() . '</p>';
            $o.= '<a href="index.php?action=edit_table&amp;table=' . $tableparam .
                 '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['back'] . ']</a>';
            $this->output = $o;


        // Continue if we aren't under errors
        } else if (empty($errors)) {
            // If there is one name change, do it, changing the prev and next
            // atributes of the adjacent tables
            if ($tableparam != $name) {
                $table->setName($name);
                if ($table->getPrevious()) {
                    $prev = $structure->getTable($table->getPrevious());
                    $prev->setNext($name);
                    $prev->setChanged(true);
                }
                if ($table->getNext()) {
                    $next = $structure->getTable($table->getNext());
                    $next->setPrevious($name);
                    $next->setChanged(true);
                }
                // Table has changed
                $table->setChanged(true);
            }

            // Set comment
            if ($table->getComment() != $comment) {
                $table->setComment($comment);
                // Table has changed
                $table->setChanged(true);
            }

            // Recalculate the hash
            $structure->calculateHash(true);

            // If the hash has changed from the original one, change the version
            // and mark the structure as changed
            $origstructure = $dbdir->xml_file->getStructure();
            if ($structure->getHash() != $origstructure->getHash()) {
                $structure->setVersion(userdate(time(), '%Y%m%d', 99, false));
                $structure->setChanged(true);
            }

            // Launch postaction if exists (leave this here!)
            if ($this->getPostAction() && $result) {
                return $this->launch($this->getPostAction());
            }
        }

        // Return ok if arrived here
        return $result;
    }
}

