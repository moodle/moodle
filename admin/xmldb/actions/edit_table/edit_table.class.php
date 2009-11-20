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

/// This class will provide the interface for all the edit table actions

class edit_table extends XMLDBAction {

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
            'viewsqlcode' => 'xmldb',
            'viewphpcode' => 'xmldb',
            'newfield' => 'xmldb',
            'newkey' => 'xmldb',
            'newindex' => 'xmldb',
            'fields' => 'xmldb',
            'keys' => 'xmldb',
            'indexes' => 'xmldb',
            'edit' => 'xmldb',
            'up' => 'xmldb',
            'down' => 'xmldb',
            'delete' => 'xmldb',
            'reserved' => 'xmldb',
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
        $tableparam = required_param('table', PARAM_CLEAN);
        if (!$table =& $structure->getTable($tableparam)) {
        /// Arriving here from a name change, looking for the new table name
            $tableparam = required_param('name', PARAM_CLEAN);
            $table =& $structure->getTable($tableparam);
        }

        $dbdir =& $XMLDB->dbdirs[$dirpath];
        $origstructure =& $dbdir->xml_file->getStructure();

    /// Add the main form
        $o = '<form id="form" action="index.php" method="post">';
        $o.= '<div>';        
        $o.= '    <input type="hidden" name ="dir" value="' . str_replace($CFG->dirroot, '', $dirpath) . '" />';
        $o.= '    <input type="hidden" name ="table" value="' . $tableparam .'" />';
        $o.= '    <input type="hidden" name ="action" value="edit_table_save" />';
        $o.= '    <input type="hidden" name ="sesskey" value="' . sesskey() .'" />';
        $o.= '    <input type="hidden" name ="postaction" value="edit_table" />';
        $o.= '    <table id="formelements" class="boxaligncenter">';
    /// If the table is being used, we cannot rename it
        if ($structure->getTableUses($table->getName())) {
            $o.= '      <tr valign="top"><td>Name:</td><td><input type="hidden" name ="name" value="' . s($table->getName()) . '" />' . s($table->getName()) .'</td></tr>';
        } else {
            $o.= '      <tr valign="top"><td><label for="name" accesskey="p">Name:</label></td><td><input name="name" type="text" size="28" maxlength="28" id="name" value="' . s($table->getName()) . '" /></td></tr>';
        }
        $o.= '      <tr valign="top"><td><label for="comment" accesskey="c">Comment:</label></td><td><textarea name="comment" rows="3" cols="80" id="comment">' . s($table->getComment()) . '</textarea></td></tr>';
        $o.= '      <tr valign="top"><td>&nbsp;</td><td><input type="submit" value="' .$this->str['change'] . '" /></td></tr>';
        $o.= '    </table>';
        $o.= '</div></form>';
    /// Calculate the buttons
        $b = ' <p class="centerpara buttons">';
    /// The view original XML button
        if ($origstructure->getTable($tableparam)) {
            $b .= '&nbsp;<a href="index.php?action=view_table_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;select=original&amp;table=' . $tableparam . '">[' . $this->str['vieworiginal'] . ']</a>';
        } else {
            $b .= '&nbsp;[' . $this->str['vieworiginal'] . ']';
        }
    /// The view edited XML button
        if ($table->hasChanged()) {
            $b .= '&nbsp;<a href="index.php?action=view_table_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;select=edited&amp;table=' . $tableparam . '">[' . $this->str['viewedited'] . ']</a>';
        } else {
            $b .= '&nbsp;[' . $this->str['viewedited'] . ']';
        }
    /// The new field button
        $b .= '&nbsp;<a href="index.php?action=new_field&amp;sesskey=' . sesskey() . '&amp;postaction=edit_field&amp;table=' . $tableparam . '&amp;field=changeme&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['newfield'] . ']</a>';
    /// The new key button
        $b .= '&nbsp;<a href="index.php?action=new_key&amp;sesskey=' . sesskey() . '&amp;postaction=edit_key&amp;table=' . $tableparam . '&amp;key=changeme&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['newkey'] . ']</a>';
    /// The new index button
        $b .= '&nbsp;<a href="index.php?action=new_index&amp;sesskey=' . sesskey() . '&amp;postaction=edit_index&amp;table=' . $tableparam . '&amp;index=changeme&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['newindex'] . ']</a>';
    /// The back to edit xml file button
        $b .= '&nbsp;<a href="index.php?action=edit_xml_file&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['back'] . ']</a>';
        $b .= '</p>';
        $b .= ' <p class="centerpara buttons">';
    /// The view sql code button
        $b .= '<a href="index.php?action=view_table_sql&amp;table=' . $tableparam . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' .$this->str['viewsqlcode'] . ']</a>';
    /// The view php code button
        $b .= '&nbsp;<a href="index.php?action=view_table_php&amp;table=' . $tableparam . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['viewphpcode'] . ']</a>';
        $b .= '</p>';
        $o .= $b;

    /// Join all the reserved words into one big array
    /// Calculate list of available SQL generators
        $plugins = get_list_of_plugins('lib/xmldb/classes/generators');
        $reserved_words = array();
        foreach($plugins as $plugin) {
            $classname = 'XMLDB' . $plugin;
            $generator = new $classname();
            $reserved_words = array_merge($reserved_words, $generator->getReservedWords());
        }
        sort($reserved_words);
        $reserved_words = array_unique($reserved_words);

    /// Delete any 'changeme' field/key/index
        $table->deleteField('changeme');
        $table->deleteKey('changeme');
        $table->deleteIndex('changeme');

    /// Add the fields list
        $fields =& $table->getFields();
        if (!empty($fields)) {
            $o .= '<h3 class="main">' . $this->str['fields'] . '</h3>';
            $o .= '<table id="listfields" border="0" cellpadding="5" cellspacing="1" class="boxaligncenter flexible">';
            $row = 0;
            foreach ($fields as $field) {
            /// Calculate buttons
                $b = '</td><td class="button cell">';
            /// The edit button (if the field has no uses)
                if (!$structure->getFieldUses($table->getName(), $field->getName())) {
                    $b .= '<a href="index.php?action=edit_field&amp;field=' .$field->getName() . '&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['edit'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['edit'] . ']';
                }
                $b .= '</td><td class="button cell">';
            /// The up button
                if ($field->getPrevious()) {
                    $b .= '<a href="index.php?action=move_updown_field&amp;direction=up&amp;sesskey=' . sesskey() . '&amp;field=' . $field->getName() . '&amp;table=' . $table->getName() . '&amp;postaction=edit_table' . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['up'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['up'] . ']';
                }
                $b .= '</td><td class="button cell">';
            /// The down button
                if ($field->getNext()) {
                    $b .= '<a href="index.php?action=move_updown_field&amp;direction=down&amp;sesskey=' . sesskey() . '&amp;field=' . $field->getName() . '&amp;table=' . $table->getName() . '&amp;postaction=edit_table' . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['down'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['down'] . ']';
                }
                $b .= '</td><td class="button cell">';
            /// The delete button (if we have more than one and it isn't used
                if (count($fields) > 1 &&
                !$structure->getFieldUses($table->getName(), $field->getName())) {
                    $b .= '<a href="index.php?action=delete_field&amp;sesskey=' . sesskey() . '&amp;field=' . $field->getName() . '&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['delete'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['delete'] . ']';
                }
            /// Detect if the table name is a reserved word
                if (in_array($field->getName(), $reserved_words)) {
                    $b .= '&nbsp;<a href="index.php?action=view_reserved_words"><span class="error">' . $this->str['reserved'] . '</span></a>';
                }
            /// The readable info
                $r = '</td><td class="readableinfo cell">' . $field->readableInfo() . '</td>';
            /// Print table row
            $o .= '<tr class="r' . $row . '"><td class="table cell"><a href="index.php?action=view_field_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;field=' . $field->getName() . '&amp;table=' . $table->getName() . '&amp;select=edited">' . $field->getName() . '</a>' . $b . $r . '</tr>';
                $row = ($row + 1) % 2;
            }
            $o .= '</table>';
        }
    /// Add the keys list
        $keys =& $table->getKeys();
        if (!empty($keys)) {
            $o .= '<h3 class="main">' . $this->str['keys'] . '</h3>';
            $o .= '<table id="listkeys" border="0"  cellpadding="5" cellspacing="1" class="boxaligncenter flexible">';
            $row = 0;
            foreach ($keys as $key) {
            /// Calculate buttons
                $b = '</td><td class="button cell">';
            /// The edit button (if the key hasn't uses)
                if (!$structure->getKeyUses($table->getName(), $key->getName())) {
                    $b .= '<a href="index.php?action=edit_key&amp;key=' .$key->getName() . '&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['edit'] . ']</a>';
                } else {
                     $b .= '[' . $this->str['edit'] . ']';
                }
                $b .= '</td><td class="button cell">';
            /// The up button
                if ($key->getPrevious()) {
                    $b .= '<a href="index.php?action=move_updown_key&amp;direction=up&amp;sesskey=' . sesskey() . '&amp;key=' . $key->getName() . '&amp;table=' . $table->getName() . '&amp;postaction=edit_table' . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['up'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['up'] . ']';
                }
                $b .= '</td><td class="button cell">';
            /// The down button
                if ($key->getNext()) {
                    $b .= '<a href="index.php?action=move_updown_key&amp;direction=down&amp;sesskey=' . sesskey() . '&amp;key=' . $key->getName() . '&amp;table=' . $table->getName() . '&amp;postaction=edit_table' . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['down'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['down'] . ']';
                }
                $b .= '</td><td class="button cell">';
            /// The delete button (if the key hasn't uses)
                if (!$structure->getKeyUses($table->getName(), $key->getName())) {
                    $b .= '<a href="index.php?action=delete_key&amp;sesskey=' . sesskey() . '&amp;key=' . $key->getName() . '&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['delete'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['delete'] . ']';
                }
            /// The readable info
                $r = '</td><td class="readableinfo cell">' . $key->readableInfo() . '</td>';
            /// Print table row
            $o .= '<tr class="r' . $row . '"><td class="table cell"><a href="index.php?action=view_key_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;key=' . $key->getName() . '&amp;table=' . $table->getName() . '&amp;select=edited">' . $key->getName() . '</a>' . $b . $r .'</tr>';
                $row = ($row + 1) % 2;
            }
            $o .= '</table>';
        }
   /// Add the indexes list
        $indexes =& $table->getIndexes();
        if (!empty($indexes)) {
            $o .= '<h3 class="main">' . $this->str['indexes'] . '</h3>';
            $o .= '<table id="listindexes" border="0" cellpadding="5" cellspacing="1" class="boxaligncenter flexible">';
            $row = 0;
            foreach ($indexes as $index) {
            /// Calculate buttons
                $b = '</td><td class="button cell">';
            /// The edit button
            $b .= '<a href="index.php?action=edit_index&amp;index=' .$index->getName() . '&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['edit'] . ']</a>';
                $b .= '</td><td class="button cell">';
            /// The up button
                if ($index->getPrevious()) {
                    $b .= '<a href="index.php?action=move_updown_index&amp;direction=up&amp;sesskey=' . sesskey() . '&amp;index=' . $index->getName() . '&amp;table=' . $table->getName() . '&amp;postaction=edit_table' . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['up'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['up'] . ']';
                }
                $b .= '</td><td class="button cell">';
            /// The down button
                if ($index->getNext()) {
                    $b .= '<a href="index.php?action=move_updown_index&amp;direction=down&amp;sesskey=' . sesskey() . '&amp;index=' . $index->getName() . '&amp;table=' . $table->getName() . '&amp;postaction=edit_table' . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['down'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['down'] . ']';
                }
                $b .= '</td><td class="button cell">';
            /// The delete button
                    $b .= '<a href="index.php?action=delete_index&amp;sesskey=' . sesskey() . '&amp;index=' . $index->getName() . '&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['delete'] . ']</a>';
            /// The readable info
                $r = '</td><td class="readableinfo cell">' . $index->readableInfo() . '</td>';
            /// Print table row
            $o .= '<tr class="r' . $row . '"><td class="table cell"><a href="index.php?action=view_index_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;index=' . $index->getName() . '&amp;table=' . $table->getName() . '&amp;select=edited">' . $index->getName() . '</a>' . $b . $r .'</tr>';
                $row = ($row + 1) % 2;
            }
            $o .= '</table>';
        }

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
