<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-3001 Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// This class will show the PHP needed to perform the desired action
/// with the specified fields/keys/indexes.

class view_table_php extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own custom attributes

    /// Get needed strings
        $this->loadStrings(array(
            'selectaction' => 'xmldb',
            'selectfieldkeyindex' => 'xmldb',
            'view' => 'xmldb',
            'table' => 'xmldb',
            'selectonefieldkeyindex' => 'xmldb',
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
        global $CFG, $XMLDB;

    /// Do the job, setting result as needed
    /// Get the dir containing the file
        $dirpath = required_param('dir', PARAM_PATH);
        $dirpath = $CFG->dirroot . stripslashes_safe($dirpath);

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

        $tableparam = required_param('table', PARAM_PATH);

        $table =& $structure->getTable($tableparam);
        $fields = $table->getFields();
        $field = reset($fields);
        $defaultfieldkeyindex = null;
        if ($field) {
            $defaultfieldkeyindex = 'f#' . $field->getName();
        }
        $keys = $table->getKeys();
        $indexes = $table->getIndexes();

    /// Get parameters
        $commandparam = optional_param('command', 'add_field', PARAM_PATH);
        $origfieldkeyindexparam = optional_param('fieldkeyindex', $defaultfieldkeyindex, PARAM_PATH);
        $fieldkeyindexparam = preg_replace('/[fki]#/i', '', $origfieldkeyindexparam); ///Strip the initials

    /// The back to edit xml button
        $b = ' <p align="center" class="buttons">';
        $b .= '<a href="index.php?action=edit_table&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;table=' . $tableparam . '">[' . $this->str['back'] . ']</a>';
        $b .= '</p>';
        $o = $b;

    /// The table currently being edited
        $o .= '<h3 class="main">' . $this->str['table'] . ': ' . s($tableparam) . '</h3>';

    /// Calculate the popup of commands
        $commands = array('add_field',
                         'drop_field',
                         'rename_field');
        foreach ($commands as $command) {
            $popcommands[$command] = str_replace('_', ' ', $command);
        }
    /// Calculate the popup of fields/keys/indexes
        $optionspacer = '&nbsp;&nbsp;&nbsp;';
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

    /// Now build the form
        $o.= '<form id="form" action="index.php" method="post">';
        $o.= '    <input type="hidden" name ="dir" value="' . str_replace($CFG->dirroot, '', $dirpath) . '" />';
        $o.= '    <input type="hidden" name ="table" value="' . s($tableparam) . '" />';
        $o.= '    <input type="hidden" name ="action" value="view_table_php" />';
        $o.= '    <table id="formelements" align="center" cellpadding="5">';
        $o.= '      <tr><td><label for="action" accesskey="c">' . $this->str['selectaction'] .' </label>' . choose_from_menu($popcommands, 'command', $commandparam, '', '', 0, true) . '&nbsp;<label for="fieldkeyindex" accesskey="f">' . $this->str['selectfieldkeyindex'] . ' </label>' .choose_from_menu($popfields, 'fieldkeyindex', $origfieldkeyindexparam, '', '', 0, true) . '</td></tr>';
        $o.= '      <tr><td colspan="2" align="center"><input type="submit" value="' .$this->str['view'] . '" /></td></tr>';
        $o.= '    </table>';
        $o.= '</form>';

        $o.= '    <table id="phpcode" align="center" cellpadding="5">';
        $o.= '      <tr><td><textarea cols="80" rows="32">';
    /// Check we have selected some field/key/index from the popup
        if ($fieldkeyindexparam == 'fieldshead' || $fieldkeyindexparam == 'keyshead' || $fieldkeyindexparam == 'indexeshead') {
            $o.= s($this->str['selectonefieldkeyindex']);
         } else {
        /// Based on current params, call the needed function
            switch ($commandparam) {
                case 'add_field':
                    $o.= s($this->add_field_php($structure, $tableparam, $fieldkeyindexparam));
                    break;
                case 'drop_field':
                    $o.= s($this->drop_field_php($structure, $tableparam, $fieldkeyindexparam));
                    break;
                case 'rename_field':
                    $o.= s($this->rename_field_php($structure, $tableparam, $fieldkeyindexparam));
                    break;
            }
        }
        $o.= '</textarea></td></tr>';
        $o.= '    </table>';

        $this->output = $o;

    /// Launch postaction if exists (leave this here!)
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

    /// Return ok if arrived here
        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * create one field using XMLDB objects and functions
     *
     * @param XMLDBStructure structure object containing all the info
     * @param string table table name
     * @param string field field name to be created
     * @return string PHP code to be used to create the field
     */
    function add_field_php($structure, $table, $field) {

        $result = '';
    /// Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if (!$field = $table->getField($field)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

    /// Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

    /// Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '    /// Define field ' . $field->getName() . ' to be added to ' . $table->getName() . XMLDB_LINEFEED;
        $result .= '        $table = new XMLDBTable(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= '        $field = new XMLDBField(' . "'" . $field->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= '        $field->setAttributes(' . $field->getPHP(true) . ');' . XMLDB_LINEFEED;

    /// Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '    /// Launch add field ' . $field->getName() . XMLDB_LINEFEED;
        $result .= '        $status = $status && add_field($table, $field);' . XMLDB_LINEFEED;

    /// Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * drop one field using XMLDB objects and functions
     *
     * @param XMLDBStructure structure object containing all the info
     * @param string table table name
     * @param string field field name to be dropped
     * @return string PHP code to be used to drop the field
     */
    function drop_field_php($structure, $table, $field) {

        $result = '';
    /// Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if (!$field = $table->getField($field)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

    /// Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

    /// Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '    /// Define field ' . $field->getName() . ' to be dropped from ' . $table->getName() . XMLDB_LINEFEED;
        $result .= '        $table = new XMLDBTable(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= '        $field = new XMLDBField(' . "'" . $field->getName() . "'" . ');' . XMLDB_LINEFEED;

    /// Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '    /// Launch drop field ' . $field->getName() . XMLDB_LINEFEED;
        $result .= '        $status = $status && drop_field($table, $field);' . XMLDB_LINEFEED;

    /// Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

    /**
     * This function will generate all the PHP code needed to
     * rename one field using XMLDB objects and functions
     *
     * @param XMLDBStructure structure object containing all the info
     * @param string table table name
     * @param string field field name to be renamed
     * @return string PHP code to be used to drop the field
     */
    function rename_field_php($structure, $table, $field) {

        $result = '';
    /// Validate if we can do it
        if (!$table = $structure->getTable($table)) {
            return false;
        }
        if (!$field = $table->getField($field)) {
            return false;
        }
        if ($table->getAllErrors()) {
            return false;
        }

    /// Add the standard PHP header
        $result .= XMLDB_PHP_HEADER;

    /// Add contents
        $result .= XMLDB_LINEFEED;
        $result .= '    /// Rename field ' . $field->getName() . ' on table ' . $table->getName() . ' to NEWNAMEGOESHERE'. XMLDB_LINEFEED;
        $result .= '        $table = new XMLDBTable(' . "'" . $table->getName() . "'" . ');' . XMLDB_LINEFEED;
        $result .= '        $field = new XMLDBField(' . "'" . $field->getName() . "'" . ');' . XMLDB_LINEFEED;

    /// Launch the proper DDL
        $result .= XMLDB_LINEFEED;
        $result .= '    /// Launch rename field ' . $field->getName() . XMLDB_LINEFEED;
        $result .= '        $status = $status && rename_field($table, $field, ' . "'" . 'NEWNAMEGOESHERE' . "'" . ');' . XMLDB_LINEFEED;

    /// Add standard PHP footer
        $result .= XMLDB_PHP_FOOTER;

        return $result;
    }

}
?>
