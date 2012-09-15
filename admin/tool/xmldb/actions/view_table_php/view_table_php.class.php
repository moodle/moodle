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
 * the desired DDL action with the specified field/key/index
 *
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_table_php extends XMLDBAction {

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
            'selectfieldkeyindex' => 'tool_xmldb',
            'view' => 'tool_xmldb',
            'table' => 'tool_xmldb',
            'selectonecommand' => 'tool_xmldb',
            'selectonefieldkeyindex' => 'tool_xmldb',
            'mustselectonefield' => 'tool_xmldb',
            'mustselectonekey' => 'tool_xmldb',
            'mustselectoneindex' => 'tool_xmldb',
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

        $tableparam = required_param('table', PARAM_PATH);

        $table = $structure->getTable($tableparam);
        $fields = $table->getFields();
        $field = reset($fields);
        $defaultfieldkeyindex = null;
        if ($field) {
            $defaultfieldkeyindex = 'f#' . $field->getName();
        }
        $keys = $table->getKeys();
        $indexes = $table->getIndexes();

        // Get parameters
        $commandparam = optional_param('command', 'add_field', PARAM_PATH);
        $origfieldkeyindexparam = optional_param('fieldkeyindex', $defaultfieldkeyindex, PARAM_PATH);
        $fieldkeyindexparam = preg_replace('/[fki]#/i', '', $origfieldkeyindexparam); // Strip the initials
        $fieldkeyindexinitial = substr($origfieldkeyindexparam, 0, 1); //To know what we have selected

        // The back to edit xml button
        $b = ' <p class="centerpara buttons">';
        $b .= '<a href="index.php?action=edit_table&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;table=' . $tableparam . '">[' . $this->str['back'] . ']</a>';
        $b .= '</p>';
        $o = $b;

        // The table currently being edited
        $o .= '<h3 class="main">' . $this->str['table'] . ': ' . s($tableparam) . '</h3>';

        // To indent the menu selections
        $optionspacer = '&nbsp;&nbsp;&nbsp;';

        // Calculate the popup of commands
        $commands = array('Fields',
                         $optionspacer . 'add_field',
                         $optionspacer . 'drop_field',
                         $optionspacer . 'rename_field',
                         $optionspacer . 'change_field_type',
                         $optionspacer . 'change_field_precision',
                         $optionspacer . 'change_field_notnull',
                         $optionspacer . 'change_field_default',
                         'Keys',
                         $optionspacer . 'add_key',
                         $optionspacer . 'drop_key',
                         $optionspacer . 'rename_key',
                         'Indexes',
                         $optionspacer . 'add_index',
                         $optionspacer . 'drop_index',
                         $optionspacer . 'rename_index');
        foreach ($commands as $command) {
            $popcommands[str_replace($optionspacer, '', $command)] = str_replace('_', ' ', $command);
        }
        // Calculate the popup of fields/keys/indexes
        if ($fields) {
            $popfields['fieldshead'] = 'Fields';
            foreach ($fields as $field) {
                $popfields['f#' . $field->getName()] = $optionspacer . $field->getName();
            }
        }
        if ($keys) {
            $popfields['keyshead'] = 'Keys';
            foreach ($keys as $key) {
                $popfields['k#' . $key->getName()] = $optionspacer . $key->getName();
            }
        }
        if ($indexes) {
            $popfields['indexeshead'] = 'Indexes';
            foreach ($indexes as $index) {
                $popfields['i#' . $index->getName()] = $optionspacer . $index->getName();
            }
        }

        // Now build the form
        $o.= '<form id="form" action="index.php" method="post">';
        $o.= '<div>';
        $o.= '    <input type="hidden" name ="dir" value="' . str_replace($CFG->dirroot, '', $dirpath) . '" />';
        $o.= '    <input type="hidden" name ="table" value="' . s($tableparam) . '" />';
        $o.= '    <input type="hidden" name ="action" value="view_table_php" />';
        $o.= '    <table id="formelements" class="boxaligncenter" cellpadding="5">';
        $o.= '      <tr><td><label for="menucommand" accesskey="c">' . $this->str['selectaction'] .' </label>' . html_writer::select($popcommands, 'command', $commandparam, false) . '&nbsp;<label for="menufieldkeyindex" accesskey="f">' . $this->str['selectfieldkeyindex'] . ' </label>' .html_writer::select($popfields, 'fieldkeyindex', $origfieldkeyindexparam, false) . '</td></tr>';
        $o.= '      <tr><td colspan="2" align="center"><input type="submit" value="' .$this->str['view'] . '" /></td></tr>';
        $o.= '    </table>';
        $o.= '</div></form>';

        $o.= '    <table id="phpcode" class="boxaligncenter" cellpadding="5">';
        $o.= '      <tr><td><textarea cols="80" rows="32">';
        // Check we have selected some field/key/index from the popup
        if ($fieldkeyindexparam == 'fieldshead' || $fieldkeyindexparam == 'keyshead' || $fieldkeyindexparam == 'indexeshead') {
            $o.= s($this->str['selectonefieldkeyindex']);
        // Check we have selected some command from the popup
        } else if ($commandparam == 'Fields' || $commandparam == 'Keys' || $commandparam == 'Indexes') {
            $o.= s($this->str['selectonecommand']);
        } else {
            // Based on current params, call the needed function
            switch ($commandparam) {
                case 'add_field':
                    if ($fieldkeyindexinitial == 'f') { //Only if we have got one field
                        $o.= s($this->add_field_php($structure, $tableparam, $fieldkeyindexparam));
                    } else {
                        $o.= $this->str['mustselectonefield'];
                    }
                    break;
                case 'drop_field':
                    if ($fieldkeyindexinitial == 'f') { //Only if we have got one field
                        $o.= s($this->drop_field_php($structure, $tableparam, $fieldkeyindexparam));
                    } else {
                        $o.= $this->str['mustselectonefield'];
                    }
                    break;
                case 'rename_field':
                    if ($fieldkeyindexinitial == 'f') { //Only if we have got one field
                        $o.= s($this->rename_field_php($structure, $tableparam, $fieldkeyindexparam));
                    } else {
                        $o.= $this->str['mustselectonefield'];
                    }
                    break;
                case 'change_field_type':
                    if ($fieldkeyindexinitial == 'f') { //Only if we have got one field
                        $o.= s($this->change_field_type_php($structure, $tableparam, $fieldkeyindexparam));
                    } else {
                        $o.= $this->str['mustselectonefield'];
                    }
                    break;
                case 'change_field_precision':
                    if ($fieldkeyindexinitial == 'f') { //Only if we have got one field
                        $o.= s($this->change_field_precision_php($structure, $tableparam, $fieldkeyindexparam));
                    } else {
                        $o.= $this->str['mustselectonefield'];
                    }
                    break;
                case 'change_field_notnull':
                    if ($fieldkeyindexinitial == 'f') { // Only if we have got one field
                        $o.= s($this->change_field_notnull_php($structure, $tableparam, $fieldkeyindexparam));
                    } else {
                        $o.= $this->str['mustselectonefield'];
                    }
                    break;
                case 'change_field_default':
                    if ($fieldkeyindexinitial == 'f') { // Only if we have got one field
                        $o.= s($this->change_field_default_php($structure, $tableparam, $fieldkeyindexparam));
                    } else {
                        $o.= $this->str['mustselectonefield'];
                    }
                    break;
                case 'add_key':
                    if ($fieldkeyindexinitial == 'k') { // Only if we have got one key
                        $o.= s($this->add_key_php($structure, $tableparam, $fieldkeyindexparam));
                    } else {
                        $o.= $this->str['mustselectonekey'];
                    }
                    break;
                case 'drop_key':
                    if ($fieldkeyindexinitial == 'k') { // Only if we have got one key
                        $o.= s($this->drop_key_php($structure, $tableparam, $fieldkeyindexparam));
                    } else {
                        $o.= $this->str['mustselectonekey'];
                    }
                    break;
                case 'rename_key':
                    if ($fieldkeyindexinitial == 'k') { // Only if we have got one key
                        $o.= s($this->rename_key_php($structure, $tableparam, $fieldkeyindexparam));
                    } else {
                        $o.= $this->str['mustselectonekey'];
                    }
                    break;
                case 'add_index':
                    if ($fieldkeyindexinitial == 'i') { // Only if we have got one index
                        $o.= s($this->add_index_php($structure, $tableparam, $fieldkeyindexparam));
                    } else {
                        $o.= $this->str['mustselectoneindex'];
                    }
                    break;
                case 'drop_index':
                    if ($fieldkeyindexinitial == 'i') { // Only if we have got one index
                        $o.= s($this->drop_index_php($structure, $tableparam, $fieldkeyindexparam));
                    } else {
                        $o.= $this->str['mustselectoneindex'];
                    }
                    break;
                case 'rename_index':
                    if ($fieldkeyindexinitial == 'i') { // Only if we have got one index
                        $o.= s($this->rename_index_php($structure, $tableparam, $fieldkeyindexparam));
                    } else {
                        $o.= $this->str['mustselectoneindex'];
                    }
                    break;
            }
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
     * create one field using XMLDB objects and functions
     *
     * @param xmldb_structure structure object containing all the info
     * @param string table table name
     * @param string field field name to be created
     * @return string PHP code to be used to create the field
     */
    function add_field_php($structure, $table, $field) {

        $result = '';
        // Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if (!$field = $table->getField($field)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

        // Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

        // Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '        // Define field ' . $field->getName() . ' to be added to ' . $table->getName() . XMLDB_LINEFEED;
        $result .= '        $table = new xmldb_table(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= '        $field = new xmldb_field(' . "'" . $field->getName() . "', " . $field->getPHP(true) . ');' . XMLDB_LINEFEED;

        // Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '        // Conditionally launch add field ' . $field->getName() . XMLDB_LINEFEED;
        $result .= '        if (!$dbman->field_exists($table, $field)) {'. XMLDB_LINEFEED;
        $result .= '            $dbman->add_field($table, $field);' . XMLDB_LINEFEED;
        $result .= '        }'. XMLDB_LINEFEED;

        // Add the proper upgrade_xxxx_savepoint call
        $result .= $this->upgrade_savepoint_php ($structure);

        // Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * drop one field using XMLDB objects and functions
     *
     * @param xmldb_structure structure object containing all the info
     * @param string table table name
     * @param string field field name to be dropped
     * @return string PHP code to be used to drop the field
     */
    function drop_field_php($structure, $table, $field) {

        $result = '';
        // Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if (!$field = $table->getField($field)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

        // Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

        // Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '        // Define field ' . $field->getName() . ' to be dropped from ' . $table->getName() . XMLDB_LINEFEED;
        $result .= '        $table = new xmldb_table(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= '        $field = new xmldb_field(' . "'" . $field->getName() . "'" . ');' . XMLDB_LINEFEED;

        // Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '        // Conditionally launch drop field ' . $field->getName() . XMLDB_LINEFEED;
        $result .= '        if ($dbman->field_exists($table, $field)) {' . XMLDB_LINEFEED;
        $result .= '            $dbman->drop_field($table, $field);' . XMLDB_LINEFEED;
        $result .= '        }' . XMLDB_LINEFEED;

        // Add the proper upgrade_xxxx_savepoint call
        $result .= $this->upgrade_savepoint_php ($structure);

        // Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * rename one field using XMLDB objects and functions
     *
     * @param xmldb_structure structure object containing all the info
     * @param string table table name
     * @param string field field name to be renamed
     * @return string PHP code to be used to rename the field
     */
    function rename_field_php($structure, $table, $field) {

        $result = '';
        // Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if (!$field = $table->getField($field)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

        // Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

        // Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '        // Rename field ' . $field->getName() . ' on table ' . $table->getName() . ' to NEWNAMEGOESHERE'. XMLDB_LINEFEED;
        $result .= '        $table = new xmldb_table(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= '        $field = new xmldb_field(' . "'" . $field->getName() . "', " . $field->getPHP(true) . ');' . XMLDB_LINEFEED;

        // Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '        // Launch rename field ' . $field->getName() . XMLDB_LINEFEED;
        $result .= '        $dbman->rename_field($table, $field, ' . "'" . 'NEWNAMEGOESHERE' . "'" . ');' . XMLDB_LINEFEED;

        // Add the proper upgrade_xxxx_savepoint call
        $result .= $this->upgrade_savepoint_php ($structure);

        // Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * change the type of one field using XMLDB objects and functions.
     * Currently these conversions are supported:
     *     integer to char
     *     char to integer
     *     number to char
     *     char to number
     *     float to char
     *     char to float
     *
     * @param xmldb_structure structure object containing all the info
     * @param string table table name
     * @param string field field name to change precision
     */
    function change_field_type_php($structure, $table, $field) {

        $result = '';
        // Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if (!$field = $table->getField($field)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

        // Calculate the type tip text
        $type = $field->getXMLDBTypeName($field->getType());

        // Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

        // Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '        // Changing type of field ' . $field->getName() . ' on table ' . $table->getName() . ' to ' . $type . XMLDB_LINEFEED;
        $result .= '        $table = new xmldb_table(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= '        $field = new xmldb_field(' . "'" . $field->getName() . "', " . $field->getPHP(true) . ');' . XMLDB_LINEFEED;

        // Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '        // Launch change of type for field ' . $field->getName() . XMLDB_LINEFEED;
        $result .= '        $dbman->change_field_type($table, $field);' . XMLDB_LINEFEED;

        // Add the proper upgrade_xxxx_savepoint call
        $result .= $this->upgrade_savepoint_php ($structure);

        // Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * change the precision of one field using XMLDB objects and functions
     *
     * @param xmldb_structure structure object containing all the info
     * @param string table table name
     * @param string field field name to change precision
     */
    function change_field_precision_php($structure, $table, $field) {

        $result = '';
        // Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if (!$field = $table->getField($field)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

        // Calculate the precision tip text
        $precision = '(' . $field->getLength();
        if ($field->getDecimals()) {
            $precision .= ', ' . $field->getDecimals();
        }
        $precision .= ')';

        // Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

        // Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '        // Changing precision of field ' . $field->getName() . ' on table ' . $table->getName() . ' to ' . $precision . XMLDB_LINEFEED;
        $result .= '        $table = new xmldb_table(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= '        $field = new xmldb_field(' . "'" . $field->getName() . "', " .$field->getPHP(true) . ');' . XMLDB_LINEFEED;

        // Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '        // Launch change of precision for field ' . $field->getName() . XMLDB_LINEFEED;
        $result .= '        $dbman->change_field_precision($table, $field);' . XMLDB_LINEFEED;

        // Add the proper upgrade_xxxx_savepoint call
        $result .= $this->upgrade_savepoint_php ($structure);

        // Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * change the nullability of one field using XMLDB objects and functions
     *
     * @param xmldb_structure structure object containing all the info
     * @param string table table name
     * @param string field field name to change null/not null
     */
    function change_field_notnull_php($structure, $table, $field) {

        $result = '';
        // Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if (!$field = $table->getField($field)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

        // Calculate the notnull tip text
        $notnull = $field->getNotnull() ? 'not null' : 'null';

        // Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

        // Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '        // Changing nullability of field ' . $field->getName() . ' on table ' . $table->getName() . ' to ' . $notnull . XMLDB_LINEFEED;
        $result .= '        $table = new xmldb_table(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= '        $field = new xmldb_field(' . "'" . $field->getName() . "', " . $field->getPHP(true) . ');' . XMLDB_LINEFEED;

        // Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '        // Launch change of nullability for field ' . $field->getName() . XMLDB_LINEFEED;
        $result .= '        $dbman->change_field_notnull($table, $field);' . XMLDB_LINEFEED;

        // Add the proper upgrade_xxxx_savepoint call
        $result .= $this->upgrade_savepoint_php ($structure);

        // Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * change the default of one field using XMLDB objects and functions
     *
     * @param xmldb_structure structure object containing all the info
     * @param string table table name
     * @param string field field name to change null/not null
     */
    function change_field_default_php($structure, $table, $field) {

        $result = '';
        // Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if (!$field = $table->getField($field)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

        // Calculate the default tip text
        $default = $field->getDefault() === null ? 'drop it' : $field->getDefault();

        // Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

        // Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '        // Changing the default of field ' . $field->getName() . ' on table ' . $table->getName() . ' to ' . $default . XMLDB_LINEFEED;
        $result .= '        $table = new xmldb_table(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= '        $field = new xmldb_field(' . "'" . $field->getName() . "', " . $field->getPHP(true) . ');' . XMLDB_LINEFEED;

        // Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '        // Launch change of default for field ' . $field->getName() . XMLDB_LINEFEED;
        $result .= '        $dbman->change_field_default($table, $field);' . XMLDB_LINEFEED;

        // Add the proper upgrade_xxxx_savepoint call
        $result .= $this->upgrade_savepoint_php ($structure);

        // Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * create one key using XMLDB objects and functions
     *
     * @param xmldb_structure structure object containing all the info
     * @param string table table name
     * @param string key key name to be created
     * @return string PHP code to be used to create the key
     */
    function add_key_php($structure, $table, $key) {

        $result = '';
        // Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if (!$key = $table->getKey($key)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

        // Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

        // Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '        // Define key ' . $key->getName() . ' ('. $key->getXMLDBKeyName($key->getType()) . ') to be added to ' . $table->getName() . XMLDB_LINEFEED;
        $result .= '        $table = new xmldb_table(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= '        $key = new xmldb_key(' . "'" . $key->getName() . "', " . $key->getPHP(true) . ');' . XMLDB_LINEFEED;

        // Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '        // Launch add key ' . $key->getName() . XMLDB_LINEFEED;
        $result .= '        $dbman->add_key($table, $key);' . XMLDB_LINEFEED;

        // Add the proper upgrade_xxxx_savepoint call
        $result .= $this->upgrade_savepoint_php ($structure);

        // Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * drop one key using XMLDB objects and functions
     *
     * @param xmldb_structure structure object containing all the info
     * @param string table table name
     * @param string key key name to be dropped
     * @return string PHP code to be used to drop the key
     */
    function drop_key_php($structure, $table, $key) {

        $result = '';
        // Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if (!$key = $table->getKey($key)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

        // Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

        // Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '        // Define key ' . $key->getName() . ' ('. $key->getXMLDBKeyName($key->getType()) . ') to be dropped form ' . $table->getName() . XMLDB_LINEFEED;
        $result .= '        $table = new xmldb_table(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= '        $key = new xmldb_key(' . "'" . $key->getName() . "', " . $key->getPHP(true) . ');' . XMLDB_LINEFEED;

        // Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '        // Launch drop key ' . $key->getName() . XMLDB_LINEFEED;
        $result .= '        $dbman->drop_key($table, $key);' . XMLDB_LINEFEED;

        // Add the proper upgrade_xxxx_savepoint call
        $result .= $this->upgrade_savepoint_php ($structure);

        // Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * rename one key using XMLDB objects and functions
     *
     * @param xmldb_structure structure object containing all the info
     * @param string table table name
     * @param string key key name to be renamed
     * @return string PHP code to be used to rename the key
     */
    function rename_key_php($structure, $table, $key) {

        $result = '';
        // Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if (!$key = $table->getKey($key)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

        // Prepend warning. This function isn't usable!
        $result .= 'DON\'T USE THIS FUNCTION (IT\'S ONLY EXPERIMENTAL). SOME DBs DON\'T SUPPORT IT!' . XMLDB_LINEFEED . XMLDB_LINEFEED;

        // Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

        // Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '        // Define key ' . $key->getName() . ' ('. $key->getXMLDBKeyName($key->getType()) . ') to be renamed to NEWNAMEGOESHERE' . XMLDB_LINEFEED;
        $result .= '        $table = new xmldb_table(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= '        $key = new xmldb_key(' . "'" . $key->getName() . "', " . $key->getPHP(true) . ');' . XMLDB_LINEFEED;

        // Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '        // Launch rename key ' . $key->getName() . XMLDB_LINEFEED;
        $result .= '        $dbman->rename_key($table, $key, ' . "'" . 'NEWNAMEGOESHERE' . "'" . ');' . XMLDB_LINEFEED;

        // Add the proper upgrade_xxxx_savepoint call
        $result .= $this->upgrade_savepoint_php ($structure);

        // Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * create one index using XMLDB objects and functions
     *
     * @param xmldb_structure structure object containing all the info
     * @param string table table name
     * @param string index index name to be created
     * @return string PHP code to be used to create the index
     */
    function add_index_php($structure, $table, $index) {

        $result = '';
        // Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if (!$index = $table->getIndex($index)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

        // Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

        // Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '        // Define index ' . $index->getName() . ' ('. ($index->getUnique() ? 'unique' : 'not unique') . ') to be added to ' . $table->getName() . XMLDB_LINEFEED;
        $result .= '        $table = new xmldb_table(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= '        $index = new xmldb_index(' . "'" . $index->getName() . "', " . $index->getPHP(true) . ');' . XMLDB_LINEFEED;

        // Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '        // Conditionally launch add index ' . $index->getName() . XMLDB_LINEFEED;
        $result .= '        if (!$dbman->index_exists($table, $index)) {' . XMLDB_LINEFEED;
        $result .= '            $dbman->add_index($table, $index);' . XMLDB_LINEFEED;
        $result .= '        }' . XMLDB_LINEFEED;

        // Add the proper upgrade_xxxx_savepoint call
        $result .= $this->upgrade_savepoint_php ($structure);

        // Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * drop one index using XMLDB objects and functions
     *
     * @param xmldb_structure structure object containing all the info
     * @param string table table name
     * @param string index index name to be dropped
     * @return string PHP code to be used to drop the index
     */
    function drop_index_php($structure, $table, $index) {

        $result = '';
        // Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if (!$index = $table->getIndex($index)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

        // Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

        // Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '        // Define index ' . $index->getName() . ' ('. ($index->getUnique() ? 'unique' : 'not unique') . ') to be dropped form ' . $table->getName() . XMLDB_LINEFEED;
        $result .= '        $table = new xmldb_table(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= '        $index = new xmldb_index(' . "'" . $index->getName() . "', " . $index->getPHP(true) . ');' . XMLDB_LINEFEED;

        // Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '        // Conditionally launch drop index ' . $index->getName() . XMLDB_LINEFEED;
        $result .= '        if ($dbman->index_exists($table, $index)) {' . XMLDB_LINEFEED;
        $result .= '            $dbman->drop_index($table, $index);' . XMLDB_LINEFEED;
        $result .= '        }' . XMLDB_LINEFEED;

        // Add the proper upgrade_xxxx_savepoint call
        $result .= $this->upgrade_savepoint_php ($structure);

        // Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * rename one index using XMLDB objects and functions
     *
     * @param xmldb_structure structure object containing all the info
     * @param string table table name
     * @param string index index name to be renamed
     * @return string PHP code to be used to rename the index
     */
    function rename_index_php($structure, $table, $index) {

        $result = '';
        // Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if (!$index = $table->getIndex($index)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

        // Prepend warning. This function isn't usable!
        $result .= 'DON\'T USE THIS FUNCTION (IT\'S ONLY EXPERIMENTAL). SOME DBs DON\'T SUPPORT IT!' . XMLDB_LINEFEED . XMLDB_LINEFEED;

        // Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

        // Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '        // Define index ' . $index->getName() . ' ('. ($index->getUnique() ? 'unique' : 'not unique') . ') to be renamed to NEWNAMEGOESHERE' . XMLDB_LINEFEED;
        $result .= '        $table = new xmldb_table(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= '        $index = new xmldb_index(' . "'" . $index->getName() . "', " . $index->getPHP(true) . ');' . XMLDB_LINEFEED;

        // Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '        // Launch rename index ' . $index->getName() . XMLDB_LINEFEED;
        $result .= '        $dbman->rename_index($table, $index, ' . "'" . 'NEWNAMEGOESHERE' . "'" . ');' . XMLDB_LINEFEED;

        // Add the proper upgrade_xxxx_savepoint call
        $result .= $this->upgrade_savepoint_php ($structure);

        // Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

}

