<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas        http://dougiamas.com  //
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

/// This class will provide the interface for all the edit field actions

class edit_field extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own custom attributes
        $this->sesskey_protected = false; // This action doesn't need sesskey protection

    /// Get needed strings
        $this->loadStrings(array(
            'change' => 'xmldb',
            'vieworiginal' => 'xmldb',
            'viewedited' => 'xmldb',
            'yes' => '',
            'no' => '',
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

    /// Fetch request data
        $tableparam = required_param('table', PARAM_CLEAN);
        if (!$table =& $structure->getTable($tableparam)) {
            $this->errormsg = 'Wrong table specified: ' . $tableparam;
            return false;
        }
        $fieldparam = required_param('field', PARAM_CLEAN);
        if (!$field =& $table->getField($fieldparam)) {
        /// Arriving here from a name change, looking for the new field name
            $fieldparam = required_param('name', PARAM_CLEAN);
            $field =& $table->getField($fieldparam);
        }

        $dbdir =& $XMLDB->dbdirs[$dirpath];
        $origstructure =& $dbdir->xml_file->getStructure();

    /// Add the main form
        $o = '<form id="form" action="index.php" method="post">';
        $o.= '    <div>';
        $o.= '    <input type="hidden" name ="dir" value="' . str_replace($CFG->dirroot, '', $dirpath) . '" />';
        $o.= '    <input type="hidden" name ="table" value="' . $tableparam .'" />';
        $o.= '    <input type="hidden" name ="field" value="' . $fieldparam .'" />';
        $o.= '    <input type="hidden" name ="sesskey" value="' . sesskey() .'" />';
        $o.= '    <input type="hidden" name ="action" value="edit_field_save" />';
        $o.= '    <input type="hidden" name ="postaction" value="edit_table" />';
        $o.= '    <table id="formelements" class="boxaligncenter">';
    /// XMLDB field name
    /// If the field has dependencies, we cannot change its name
        $disabled = '';
        if ($structure->getFieldUses($table->getName(), $field->getName())) {
            $o.= '      <input type="hidden" name ="name" value="' .  s($field->getName()) .'" />';
            $o.= '      <tr valign="top"><td>Name:</td><td colspan="2">' . s($field->getName()) . '</td></tr>';
        } else {
            $o.= '      <tr valign="top"><td><label for="name" accesskey="n">Name:</label></td><td colspan="2"><input name="name" type="text" size="30" maxlength="30" id="name" value="' . s($field->getName()) . '" /></td></tr>';
        }
    /// XMLDB field comment
        $o.= '      <tr valign="top"><td><label for="comment" accesskey="c">Comment:</label></td><td colspan="2"><textarea name="comment" rows="3" cols="80" id="comment">' . s($field->getComment()) . '</textarea></td></tr>';
    /// XMLDBField Type
        $typeoptions = array (XMLDB_TYPE_INTEGER => $field->getXMLDBTypeName(XMLDB_TYPE_INTEGER),
                              XMLDB_TYPE_NUMBER  => $field->getXMLDBTypeName(XMLDB_TYPE_NUMBER),
                              XMLDB_TYPE_FLOAT   => $field->getXMLDBTypeName(XMLDB_TYPE_FLOAT),
                              XMLDB_TYPE_DATETIME=> $field->getXMLDBTypeName(XMLDB_TYPE_DATETIME),
                              XMLDB_TYPE_CHAR    => $field->getXMLDBTypeName(XMLDB_TYPE_CHAR),
                              XMLDB_TYPE_TEXT    => $field->getXMLDBTypeName(XMLDB_TYPE_TEXT),
                              XMLDB_TYPE_BINARY  => $field->getXMLDBTypeName(XMLDB_TYPE_BINARY));
    /// If current field isnt float, delete such column type to avoid its creation from the interface
    /// Note that float fields are supported completely but it's possible than in a next future
    /// we delete them completely from Moodle DB, using, exlusively, number(x,y) types
        if ($field->getType() != XMLDB_TYPE_FLOAT) {
            unset ($typeoptions[XMLDB_TYPE_FLOAT]);
        }
    /// Also we hide datetimes. Only edition of them is allowed (and retrofit) but not new creation
        if ($field->getType() != XMLDB_TYPE_DATETIME) {
            unset ($typeoptions[XMLDB_TYPE_DATETIME]);
        }
        $o.= '      <tr valign="top"><td><label for="menutype" accesskey="t">Type:</label></td>';
        $o.= '        <td colspan="2">' . choose_from_menu($typeoptions, 'type', $field->getType(), '', '', '', true) . '</td></tr>';
    /// XMLDBField Length
        $o.= '      <tr valign="top"><td><label for="length" accesskey="l">Length:</label></td>';
        $o.= '        <td colspan="2"><input name="length" type="text" size="6" maxlength="6" id="length" value="' . s($field->getLength()) . '" /><span id="lengthtip"></span></td></tr>';
    /// XMLDBField Decimals
        $o.= '      <tr valign="top"><td><label for="decimals" accesskey="d">Decimals:</label></td>';
        $o.= '        <td colspan="2"><input name="decimals" type="text" size="6" maxlength="6" id="decimals" value="' . s($field->getDecimals()) . '" /><span id="decimalstip"></span></td></tr>';
    /// XMLDBField Unsigned
        $unsignedoptions = array (0 => 'signed', 1 => 'unsigned');
        $o.= '      <tr valign="top"><td><label for="menuunsigned" accesskey="u">Unsigned:</label></td>';
        $o.= '        <td colspan="2">' . choose_from_menu($unsignedoptions, 'unsigned', $field->getUnsigned(), '', '', '', true) . '</td></tr>';
    /// XMLDBField NotNull
        $notnulloptions = array (0 => 'null', 'not null');
        $o.= '      <tr valign="top"><td><label for="menunotnull" accesskey="n">Not Null:</label></td>';
        $o.= '        <td colspan="2">' . choose_from_menu($notnulloptions, 'notnull', $field->getNotNull(), '', '', '', true) . '</td></tr>';
    /// XMLDBField Sequence
        $sequenceoptions = array (0 => $this->str['no'], 1 => 'auto-numbered');
        $o.= '      <tr valign="top"><td><label for="menusequence" accesskey="s">Sequence:</label></td>';
        $o.= '        <td colspan="2">' . choose_from_menu($sequenceoptions, 'sequence', $field->getSequence(), '', '', '', true) . '</td></tr>';
    /// XMLDBField Enum and enumvalues
        $enumoptions = array (0 => $this->str['no'], 1 => $this->str['yes']);
        $o.= '      <tr valign="top"><td><label for="menuenum" accesskey="s">Enum:</label></td>';
        $o.= '        <td>' . choose_from_menu($enumoptions, 'enum', $field->getEnum(), '', '', '', true) . '</td>';
        if (is_array($field->getEnumValues())) {
            $enumvalues = implode(', ', $field->getEnumValues());
        } else {
            $enumvalues = '';
        }
        $o.= '            <td><textarea name="enumvalues" rows="3" cols="70" id="enumvalues">' . s($enumvalues) . '</textarea></td></tr>';
    /// XMLDBField Default
        $o.= '      <tr valign="top"><td><label for="default" accesskey="d">Default:</label></td>';
        $o.= '        <td colspan="2"><input type="text" name="default" size="30" maxlength="80" id="default" value="' . s($field->getDefault()) . '" /></td></tr>';
    /// Change button
        $o.= '      <tr valign="top"><td>&nbsp;</td><td colspan="2"><input type="submit" value="' .$this->str['change'] . '" /></td></tr>';
        $o.= '    </table>';
        $o.= '</div></form>';
    /// Calculate the buttons
        $b = ' <p class="centerpara buttons">';
    /// The view original XML button
        if ($table->getField($fieldparam)) {
            $b .= '&nbsp;<a href="index.php?action=view_field_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;select=original&amp;table=' . $tableparam . '&amp;field=' . $fieldparam . '">[' . $this->str['vieworiginal'] . ']</a>';
        } else {
            $b .= '&nbsp;[' . $this->str['vieworiginal'] . ']';
        }
    /// The view edited XML button
        if ($field->hasChanged()) {
            $b .= '&nbsp;<a href="index.php?action=view_field_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;select=edited&amp;table=' . $tableparam . '&amp;field=' . $fieldparam . '">[' . $this->str['viewedited'] . ']</a>';
        } else {
            $b .= '&nbsp;[' . $this->str['viewedited'] . ']';
        }
    /// The back to edit table button
        $b .= '&nbsp;<a href="index.php?action=edit_table&amp;table=' . $tableparam . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['back'] . ']</a>';
        $b .= '</p>';
        $o .= $b;

        $this->output = $o;

    /// Launch postaction if exists (leave this here!)
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

    /// Return ok if arrived here
        return $result;
    }
}
?>
