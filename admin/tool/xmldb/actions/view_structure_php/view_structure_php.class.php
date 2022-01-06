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
 * This class will show the PHP needed (upgrade block) to perform
 * the desired DDL action with the specified table
 *
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_structure_php extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

        // Set own custom attributes
        $this->sesskey_protected = false; // This action doesn't need sesskey protection

        // Get needed strings
        $this->loadStrings(array(
            'selectaction' => 'tool_xmldb',
            'selecttable' => 'tool_xmldb',
            'view' => 'tool_xmldb',
            'back' => 'tool_xmldb'
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
        $this->does_generate = ACTION_GENERATE_HTML;

        // These are always here
        global $CFG, $XMLDB, $OUTPUT;

        // Do the job, setting result as needed
        // Get the dir containing the file
        $dirpath = required_param('dir', PARAM_PATH);
        $dirpath = $CFG->dirroot . $dirpath;

        // Get the correct dirs
        if (!empty($XMLDB->dbdirs)) {
            $dbdir = $XMLDB->dbdirs[$dirpath];
        } else {
            return false;
        }
        if (!empty($XMLDB->editeddirs)) {
            $editeddir = $XMLDB->editeddirs[$dirpath];
            $structure = $editeddir->xml_file->getStructure();
        }

        $tables = $structure->getTables();
        $table = reset($tables);
        $defaulttable = null;
        if ($table) {
            $defaulttable = $table->getName();
        }

        // Get parameters
        $commandparam = optional_param('command', 'create_table', PARAM_PATH);
        $tableparam = optional_param('table', $defaulttable, PARAM_PATH);

        // The back to edit xml button
        $b = ' <p class="centerpara buttons">';
        $b .= '<a href="index.php?action=edit_xml_file&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['back'] . ']</a>';
        $b .= '</p>';
        $o = $b;

        // Calculate the popup of commands
        $commands = array('create_table',
                         'drop_table',
                         'rename_table');
        foreach ($commands as $command) {
            $popcommands[$command] = str_replace('_', ' ', $command);
        }
        // Calculate the popup of tables
        foreach ($tables as $table) {
            $poptables[$table->getName()] = $table->getName();
        }
        // Now build the form
        $o.= '<form id="form" action="index.php" method="post">';
        $o.='<div>';
        $o.= '    <input type="hidden" name ="dir" value="' . str_replace($CFG->dirroot, '', $dirpath) . '" />';
        $o.= '    <input type="hidden" name ="action" value="view_structure_php" />';
        $o.= '    <table id="formelements" class="boxaligncenter" cellpadding="5">';
        $o.= '      <tr><td><label for="menucommand" accesskey="c">' . $this->str['selectaction'] .' </label>' . html_writer::select($popcommands, 'command', $commandparam, false) . '&nbsp;<label for="menutable" accesskey="t">' . $this->str['selecttable'] . ' </label>' .html_writer::select($poptables, 'table', $tableparam, false) . '</td></tr>';
        $o.= '      <tr><td colspan="2" align="center"><input type="submit" value="' .$this->str['view'] . '" /></td></tr>';
        $o.= '    </table>';
        $o.= '</div></form>';
        $o.= '    <table id="phpcode" class="boxaligncenter" cellpadding="5">';
        $o .= '      <tr><td><textarea cols="80" rows="32" class="form-control">';
        // Based on current params, call the needed function
        switch ($commandparam) {
            case 'create_table':
                $o.= s($this->create_table_php($structure, $tableparam));
                break;
            case 'drop_table':
                $o.= s($this->drop_table_php($structure, $tableparam));
                break;
            case 'rename_table':
                $o.= s($this->rename_table_php($structure, $tableparam));
                break;
        }
        $o.= '</textarea></td></tr>';
        $o.= '    </table>';

        $this->output = $o;

        // Launch postaction if exists (leave this here!)
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

        // Return ok if arrived here
        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * create one table using XMLDB objects and functions
     *
     * @param xmldb_structure structure object containing all the info
     * @param string table table code to be created
     * @return string PHP code to be used to create the table
     */
    function create_table_php($structure, $table) {

        $result = '';
        // Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

        // Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

        // Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '        // Define table ' . $table->getName() . ' to be created.' . XMLDB_LINEFEED;
        $result .= '        $table = new xmldb_table(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= XMLDB_LINEFEED;
        $result .= '        // Adding fields to table ' . $table->getName() . '.' . XMLDB_LINEFEED;
        // Iterate over each field
        foreach ($table->getFields() as $field) {
            // The field header, with name
            $result .= '        $table->add_field(' . "'" . $field->getName() . "', ";
            // The field PHP specs
            $result .= $field->getPHP(false);
            // The end of the line
            $result .= ');' . XMLDB_LINEFEED;
        }
        // Iterate over each key
        if ($keys = $table->getKeys()) {
            $result .= XMLDB_LINEFEED;
            $result .= '        // Adding keys to table ' . $table->getName() . '.' . XMLDB_LINEFEED;
            foreach ($keys as $key) {
                // The key header, with name
                $result .= '        $table->add_key(' . "'" . $key->getName() . "', ";
                // The key PHP specs
                $result .= $key->getPHP();
                // The end of the line
                $result .= ');' . XMLDB_LINEFEED;
            }
        }
        // Iterate over each index
        if ($indexes = $table->getIndexes()) {
            $result .= XMLDB_LINEFEED;
            $result .= '        // Adding indexes to table ' . $table->getName() . '.' . XMLDB_LINEFEED;
            foreach ($indexes as $index) {
                // The index header, with name
                $result .= '        $table->add_index(' . "'" . $index->getName() . "', ";
                // The index PHP specs
                $result .= $index->getPHP();
                // The end of the line
                $result .= ');' . XMLDB_LINEFEED;
            }
        }

        // Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '        // Conditionally launch create table for ' . $table->getName() . '.' . XMLDB_LINEFEED;
        $result .= '        if (!$dbman->table_exists($table)) {' . XMLDB_LINEFEED;
        $result .= '            $dbman->create_table($table);' . XMLDB_LINEFEED;
        $result .= '        }' . XMLDB_LINEFEED;

        // Add the proper upgrade_xxxx_savepoint call
        $result .= $this->upgrade_savepoint_php ($structure);

        // Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * drop one table using XMLDB objects and functions
     *
     * @param xmldb_structure structure object containing all the info
     * @param string table table code to be dropped
     * @return string PHP code to be used to drop the table
     */
    function drop_table_php($structure, $table) {

        $result = '';
        // Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

        // Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

        // Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '        // Define table ' . $table->getName() . ' to be dropped.' . XMLDB_LINEFEED;
        $result .= '        $table = new xmldb_table(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;

        // Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '        // Conditionally launch drop table for ' . $table->getName() . '.' . XMLDB_LINEFEED;
        $result .= '        if ($dbman->table_exists($table)) {' . XMLDB_LINEFEED;
        $result .= '            $dbman->drop_table($table);' . XMLDB_LINEFEED;
        $result .= '        }' . XMLDB_LINEFEED;

        // Add the proper upgrade_xxxx_savepoint call
        $result .= $this->upgrade_savepoint_php ($structure);

        // Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * rename one table using XMLDB objects and functions
     *
     * @param xmldb_structure structure object containing all the info
     * @param string table table code to be renamed
     * @return string PHP code to be used to rename the table
     */
    function rename_table_php($structure, $table) {

        $result = '';
        // Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

        // Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

        // Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '        // Define table ' . $table->getName() . ' to be renamed to NEWNAMEGOESHERE.' . XMLDB_LINEFEED;
        $result .= '        $table = new xmldb_table(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;

        // Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '        // Launch rename table for ' . $table->getName() . '.' . XMLDB_LINEFEED;
        $result .= '        $dbman->rename_table($table, ' . "'NEWNAMEGOESHERE'" . ');' . XMLDB_LINEFEED;

        // Add the proper upgrade_xxxx_savepoint call
        $result .= $this->upgrade_savepoint_php ($structure);

        // Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

}

