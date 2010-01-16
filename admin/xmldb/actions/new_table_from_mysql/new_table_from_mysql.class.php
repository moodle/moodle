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
 * @package   xmldb-editor
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This class will ask and retrofit all the information from one
 * mysql table present in the Moodle DB to one xmldb_table structure
 *
 * @package   xmldb-editor
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class new_table_from_mysql extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own custom attributes

    /// Get needed strings
        $this->loadStrings(array(
            'createtable' => 'xmldb',
            'aftertable' => 'xmldb',
            'create' => 'xmldb',
            'back' => 'xmldb'
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

    /// Set own core attributes
        $this->does_generate = ACTION_GENERATE_HTML;

    /// These are always here
        global $CFG, $XMLDB, $DB, $OUTPUT;

    /// Do the job, setting result as needed
    /// Get the dir containing the file
        $dirpath = required_param('dir', PARAM_PATH);
        $dirpath = $CFG->dirroot . $dirpath;

    /// Get the correct dirs
        if (!empty($XMLDB->dbdirs)) {
            $dbdir =& $XMLDB->dbdirs[$dirpath];
        } else {
            return false;
        }
        if (!empty($XMLDB->editeddirs)) {
            $editeddir =& $XMLDB->editeddirs[$dirpath];
            $structure =& $editeddir->xml_file->getStructure();
        }
    /// ADD YOUR CODE HERE
        $tableparam = optional_param('table', NULL, PARAM_CLEAN);

    /// If no table, show form
        if (!$tableparam) {
        /// No postaction here
            $this->postaction = NULL;
        /// Get list of tables
            $dbtables = $DB->get_tables();
            $selecttables = array();
            foreach ($dbtables as $dbtable) {
                $i = $structure->findTableInArray($dbtable);
                if ($i === NULL) {
                    $selecttables[$dbtable] = $dbtable;
                }
            }
        /// Get list of after tables
            $aftertables = array();
            if ($tables =& $structure->getTables()) {
                foreach ($tables as $aftertable) {
                    $aftertables[$aftertable->getName()] = $aftertable->getName();
                }
            }
            if (!$selecttables) {
                $this->errormsg = 'No tables available to be retrofitted';
                return false;
            }
        /// Now build the form
            $o = '<form id="form" action="index.php" method="post">';
            $o .= '<div>';
            $o.= '    <input type="hidden" name ="dir" value="' . str_replace($CFG->dirroot, '', $dirpath) . '" />';
            $o.= '    <input type="hidden" name ="action" value="new_table_from_mysql" />';
            $o.= '    <input type="hidden" name ="postaction" value="edit_table" />';
            $o.= '    <input type="hidden" name ="sesskey" value="' . sesskey() . '" />';
            $o.= '    <table id="formelements" class="boxaligncenter" cellpadding="5">';
            $o.= '      <tr><td><label for="table" accesskey="t">' . $this->str['createtable'] .' </label>' . html_writer::select($selecttables, 'table') . '<label for="after" accesskey="a">' . $this->str['aftertable'] . ' </label>' .html_writer::select($aftertables, 'after') . '</td></tr>';
            $o.= '      <tr><td colspan="2" align="center"><input type="submit" value="' .$this->str['create'] . '" /></td></tr>';
            $o.= '      <tr><td colspan="2" align="center"><a href="index.php?action=edit_xml_file&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['back'] . ']</a></td></tr>';
            $o.= '    </table>';
            $o.= '</div></form>';

            $this->output = $o;


    /// If table, retrofit information and, if everything works,
    /// go to the table edit action
        } else {
        /// Get some params (table is mandatory here)
            $tableparam = required_param('table', PARAM_CLEAN);
            $afterparam = required_param('after', PARAM_CLEAN);

        /// Create one new xmldb_table
            $table = new xmldb_table(strtolower(trim($tableparam)));
            $table->setComment($table->getName() . ' table retrofitted from MySQL');
        /// Get fields info from ADODb
            $dbfields = $DB->get_columns($tableparam);
            if ($dbfields) {
                foreach ($dbfields as $dbfield) {
                /// Create new XMLDB field
                    $field = new xmldb_field($dbfield->name);
                /// Set field with info retrofitted
                    $field->setFromADOField($dbfield);
                /// Add field to the table
                    $table->addField($field);
                }
            }
        /// Get PK, UK and indexes info from ADODb
            $dbindexes = $DB->get_indexes($tableparam);
            if ($dbindexes) {
                $lastkey = NULL; //To temp store the last key processed
                foreach ($dbindexes as $indexname => $dbindex) {
                /// Add the indexname to the array
                    $dbindex['name'] = $indexname;
                /// We are handling one xmldb_key (primaries + uniques)
                    if ($dbindex['unique']) {
                        $key = new xmldb_key(strtolower($dbindex['name']));
                    /// Set key with info retrofitted
                        $key->setFromADOKey($dbindex);
                    /// Set default comment to PKs
                        if ($key->getType() == XMLDB_KEY_PRIMARY) {
                        }
                    /// Add key to the table
                        $table->addKey($key);

                /// We are handling one xmldb_index (non-uniques)
                    } else {
                        $index = new xmldb_index(strtolower($dbindex['name']));
                    /// Set index with info retrofitted
                        $index->setFromADOIndex($dbindex);
                    /// Add index to the table
                        $table->addIndex($index);
                    }
                }
            }
        /// Finally, add the whole retroffited table to the structure
        /// in the place specified
            $structure->addTable($table, $afterparam);
        }

    /// Launch postaction if exists (leave this here!)
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

    /// Return ok if arrived here
        return $result;
    }
}

