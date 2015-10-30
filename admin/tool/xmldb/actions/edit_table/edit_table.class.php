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
 * This class provides the interface for all the edit table actions
 *
 * Main page of edit table actions, from here fields/indexes/keys edition
 * can be invoked, plus links to PHP code generator, view SQL, rearrange
 * elements and so on.
 *
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_table extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

        // Set own custom attributes
        $this->sesskey_protected = false; // This action doesn't need sesskey protection

        // Get needed strings
        $this->loadStrings(array(
            'change' => 'tool_xmldb',
            'vieworiginal' => 'tool_xmldb',
            'viewedited' => 'tool_xmldb',
            'viewsqlcode' => 'tool_xmldb',
            'viewphpcode' => 'tool_xmldb',
            'newfield' => 'tool_xmldb',
            'newkey' => 'tool_xmldb',
            'newindex' => 'tool_xmldb',
            'fields' => 'tool_xmldb',
            'keys' => 'tool_xmldb',
            'indexes' => 'tool_xmldb',
            'edit' => 'tool_xmldb',
            'up' => 'tool_xmldb',
            'down' => 'tool_xmldb',
            'delete' => 'tool_xmldb',
            'reserved' => 'tool_xmldb',
            'back' => 'tool_xmldb',
            'viewxml' => 'tool_xmldb',
            'pendingchanges' => 'tool_xmldb',
            'pendingchangescannotbesaved' => 'tool_xmldb',
            'save' => 'tool_xmldb'
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
        global $CFG, $XMLDB;

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
        // Check if the dir exists and copy it from dbdirs
        // (because we need straight load in case of saving from here)
        if (!isset($XMLDB->editeddirs[$dirpath])) {
            $XMLDB->editeddirs[$dirpath] = unserialize(serialize($dbdir));
        }

        if (!empty($XMLDB->editeddirs)) {
            $editeddir = $XMLDB->editeddirs[$dirpath];
            $structure = $editeddir->xml_file->getStructure();
        }

        $tableparam = required_param('table', PARAM_CLEAN);
        if (!$table = $structure->getTable($tableparam)) {
            // Arriving here from a name change, looking for the new table name
            $tableparam = required_param('name', PARAM_CLEAN);
            $table = $structure->getTable($tableparam);
        }

        $dbdir = $XMLDB->dbdirs[$dirpath];
        $origstructure = $dbdir->xml_file->getStructure();

        // Add the main form
        $o = '<form id="form" action="index.php" method="post">';
        $o.= '<div>';
        $o.= '    <input type="hidden" name ="dir" value="' . str_replace($CFG->dirroot, '', $dirpath) . '" />';
        $o.= '    <input type="hidden" name ="table" value="' . $tableparam .'" />';
        $o.= '    <input type="hidden" name ="action" value="edit_table_save" />';
        $o.= '    <input type="hidden" name ="sesskey" value="' . sesskey() .'" />';
        $o.= '    <input type="hidden" name ="postaction" value="edit_table" />';
        $o.= '    <table id="formelements" class="boxaligncenter">';
        // If the table is being used, we cannot rename it
        if ($structure->getTableUses($table->getName())) {
            $o.= '      <tr valign="top"><td>Name:</td><td><input type="hidden" name ="name" value="' . s($table->getName()) . '" />' . s($table->getName()) .'</td></tr>';
        } else {
            $o.= '      <tr valign="top"><td><label for="name" accesskey="p">Name:</label></td><td><input name="name" type="text" size="28" maxlength="28" id="name" value="' . s($table->getName()) . '" /></td></tr>';
        }
        $o.= '      <tr valign="top"><td><label for="comment" accesskey="c">Comment:</label></td><td><textarea name="comment" rows="3" cols="80" id="comment">' . s($table->getComment()) . '</textarea></td></tr>';
        $o.= '      <tr valign="top"><td>&nbsp;</td><td><input type="submit" value="' .$this->str['change'] . '" /></td></tr>';
        $o.= '    </table>';
        $o.= '</div></form>';
        // Calculate the pending changes / save message
        $e = '';
        $cansavenow = false;
        if ($structure->hasChanged()) {
            if (!is_writeable($dirpath . '/install.xml') || !is_writeable($dirpath)) {
                $e .= '<p class="centerpara error">' . $this->str['pendingchangescannotbesaved'] . '</p>';
            } else {
                $e .= '<p class="centerpara warning">' . $this->str['pendingchanges'] . '</p>';
                $cansavenow = true;
            }
        }
        // Calculate the buttons
        $b = ' <p class="centerpara buttons">';
        // The view original XML button
        if ($origstructure->getTable($tableparam)) {
            $b .= '&nbsp;<a href="index.php?action=view_table_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;select=original&amp;table=' . $tableparam . '">[' . $this->str['vieworiginal'] . ']</a>';
        } else {
            $b .= '&nbsp;[' . $this->str['vieworiginal'] . ']';
        }
        // The view edited XML button
        if ($table->hasChanged()) {
            $b .= '&nbsp;<a href="index.php?action=view_table_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;select=edited&amp;table=' . $tableparam . '">[' . $this->str['viewedited'] . ']</a>';
        } else {
            $b .= '&nbsp;[' . $this->str['viewedited'] . ']';
        }
        // The new field button
        $b .= '&nbsp;<a href="index.php?action=new_field&amp;sesskey=' . sesskey() . '&amp;postaction=edit_field&amp;table=' . $tableparam . '&amp;field=changeme&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['newfield'] . ']</a>';
        // The new key button
        $b .= '&nbsp;<a href="index.php?action=new_key&amp;sesskey=' . sesskey() . '&amp;postaction=edit_key&amp;table=' . $tableparam . '&amp;key=changeme&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['newkey'] . ']</a>';
        // The new index button
        $b .= '&nbsp;<a href="index.php?action=new_index&amp;sesskey=' . sesskey() . '&amp;postaction=edit_index&amp;table=' . $tableparam . '&amp;index=changeme&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['newindex'] . ']</a>';
        $b .= '</p>';

        $b .= ' <p class="centerpara buttons">';
        // The view sql code button
        $b .= '<a href="index.php?action=view_table_sql&amp;table=' . $tableparam . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' .$this->str['viewsqlcode'] . ']</a>';
        // The view php code button
        $b .= '&nbsp;<a href="index.php?action=view_table_php&amp;table=' . $tableparam . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['viewphpcode'] . ']</a>';
        // The save button (if possible)
        if ($cansavenow) {
            $b .= '&nbsp;<a href="index.php?action=save_xml_file&amp;sesskey=' . sesskey() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;time=' . time() . '&amp;unload=false&amp;postaction=edit_table&amp;table=' . $tableparam . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['save'] . ']</a>';
        }
        // The back to edit xml file button
        $b .= '&nbsp;<a href="index.php?action=edit_xml_file&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['back'] . ']</a>';
        $b .= '</p>';
        $o .= $e . $b;

        require_once("$CFG->libdir/ddl/sql_generator.php");
        $reserved_words = sql_generator::getAllReservedWords();

        // Delete any 'changeme' field/key/index
        $table->deleteField('changeme');
        $table->deleteKey('changeme');
        $table->deleteIndex('changeme');

        // Add the fields list
        $fields = $table->getFields();
        if (!empty($fields)) {
            $o .= '<h3 class="main">' . $this->str['fields'] . '</h3>';
            $o .= '<table id="listfields" border="0" cellpadding="5" cellspacing="1" class="boxaligncenter flexible">';
            $row = 0;
            foreach ($fields as $field) {
                // The field name (link to edit - if the field has no uses)
                if (!$structure->getFieldUses($table->getName(), $field->getName())) {
                    $f = '<a href="index.php?action=edit_field&amp;field=' .$field->getName() . '&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">' . $field->getName() . '</a>';
                } else {
                    $f = $field->getName();
                }
                // Calculate buttons
                $b = '</td><td class="button cell">';
                // The edit button (if the field has no uses)
                if (!$structure->getFieldUses($table->getName(), $field->getName())) {
                    $b .= '<a href="index.php?action=edit_field&amp;field=' .$field->getName() . '&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['edit'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['edit'] . ']';
                }
                $b .= '</td><td class="button cell">';
                // The up button
                if ($field->getPrevious()) {
                    $b .= '<a href="index.php?action=move_updown_field&amp;direction=up&amp;sesskey=' . sesskey() . '&amp;field=' . $field->getName() . '&amp;table=' . $table->getName() . '&amp;postaction=edit_table' . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['up'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['up'] . ']';
                }
                $b .= '</td><td class="button cell">';
                // The down button
                if ($field->getNext()) {
                    $b .= '<a href="index.php?action=move_updown_field&amp;direction=down&amp;sesskey=' . sesskey() . '&amp;field=' . $field->getName() . '&amp;table=' . $table->getName() . '&amp;postaction=edit_table' . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['down'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['down'] . ']';
                }
                $b .= '</td><td class="button cell">';
                // The delete button (if we have more than one and it isn't used
                if (count($fields) > 1 &&
                !$structure->getFieldUses($table->getName(), $field->getName())) {
                    $b .= '<a href="index.php?action=delete_field&amp;sesskey=' . sesskey() . '&amp;field=' . $field->getName() . '&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['delete'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['delete'] . ']';
                }
                $b .= '</td><td class="button cell">';
                // The view xml button
                $b .= '<a href="index.php?action=view_field_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;field=' . $field->getName() . '&amp;table=' . $table->getName() . '&amp;select=edited">[' . $this->str['viewxml'] . ']</a>';
                // Detect if the table name is a reserved word
                if (array_key_exists($field->getName(), $reserved_words)) {
                    $b .= '&nbsp;<a href="index.php?action=view_reserved_words"><span class="error">' . $this->str['reserved'] . '</span></a>';
                }
                // The readable info
                $r = '</td><td class="readableinfo cell">' . $field->readableInfo() . '</td>';
                // Print table row
                $o .= '<tr class="r' . $row . '"><td class="table cell">' . $f . $b . $r . '</tr>';
                $row = ($row + 1) % 2;
            }
            $o .= '</table>';
        }
        // Add the keys list
        $keys = $table->getKeys();
        if (!empty($keys)) {
            $o .= '<h3 class="main">' . $this->str['keys'] . '</h3>';
            $o .= '<table id="listkeys" border="0"  cellpadding="5" cellspacing="1" class="boxaligncenter flexible">';
            $row = 0;
            foreach ($keys as $key) {
                // The key name (link to edit - if the key has no uses)
                if (!$structure->getKeyUses($table->getName(), $key->getName())) {
                    $k = '<a href="index.php?action=edit_key&amp;key=' .$key->getName() . '&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">' . $key->getName() . '</a>';
                } else {
                    $k = $key->getName();
                }
                // Calculate buttons
                $b = '</td><td class="button cell">';
                // The edit button (if the key hasn't uses)
                if (!$structure->getKeyUses($table->getName(), $key->getName())) {
                    $b .= '<a href="index.php?action=edit_key&amp;key=' .$key->getName() . '&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['edit'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['edit'] . ']';
                }
                $b .= '</td><td class="button cell">';
                // The up button
                if ($key->getPrevious()) {
                    $b .= '<a href="index.php?action=move_updown_key&amp;direction=up&amp;sesskey=' . sesskey() . '&amp;key=' . $key->getName() . '&amp;table=' . $table->getName() . '&amp;postaction=edit_table' . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['up'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['up'] . ']';
                }
                $b .= '</td><td class="button cell">';
                // The down button
                if ($key->getNext()) {
                    $b .= '<a href="index.php?action=move_updown_key&amp;direction=down&amp;sesskey=' . sesskey() . '&amp;key=' . $key->getName() . '&amp;table=' . $table->getName() . '&amp;postaction=edit_table' . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['down'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['down'] . ']';
                }
                $b .= '</td><td class="button cell">';
                // The delete button (if the key hasn't uses)
                if (!$structure->getKeyUses($table->getName(), $key->getName())) {
                    $b .= '<a href="index.php?action=delete_key&amp;sesskey=' . sesskey() . '&amp;key=' . $key->getName() . '&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['delete'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['delete'] . ']';
                }
                $b .= '</td><td class="button cell">';
                // The view xml button
                $b .= '<a href="index.php?action=view_key_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;key=' . $key->getName() . '&amp;table=' . $table->getName() . '&amp;select=edited">[' . $this->str['viewxml'] . ']</a>';
                // The readable info
                $r = '</td><td class="readableinfo cell">' . $key->readableInfo() . '</td>';
                // Print table row
            $o .= '<tr class="r' . $row . '"><td class="table cell">' . $k . $b . $r .'</tr>';
                $row = ($row + 1) % 2;
            }
            $o .= '</table>';
        }
       // Add the indexes list
        $indexes = $table->getIndexes();
        if (!empty($indexes)) {
            $o .= '<h3 class="main">' . $this->str['indexes'] . '</h3>';
            $o .= '<table id="listindexes" border="0" cellpadding="5" cellspacing="1" class="boxaligncenter flexible">';
            $row = 0;
            foreach ($indexes as $index) {
                // The index name (link to edit)
                $i = '<a href="index.php?action=edit_index&amp;index=' .$index->getName() . '&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">' . $index->getName() . '</a>';
                // Calculate buttons
                $b = '</td><td class="button cell">';
                // The edit button
                $b .= '<a href="index.php?action=edit_index&amp;index=' .$index->getName() . '&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['edit'] . ']</a>';
                $b .= '</td><td class="button cell">';
                // The up button
                if ($index->getPrevious()) {
                    $b .= '<a href="index.php?action=move_updown_index&amp;direction=up&amp;sesskey=' . sesskey() . '&amp;index=' . $index->getName() . '&amp;table=' . $table->getName() . '&amp;postaction=edit_table' . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['up'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['up'] . ']';
                }
                $b .= '</td><td class="button cell">';
                // The down button
                if ($index->getNext()) {
                    $b .= '<a href="index.php?action=move_updown_index&amp;direction=down&amp;sesskey=' . sesskey() . '&amp;index=' . $index->getName() . '&amp;table=' . $table->getName() . '&amp;postaction=edit_table' . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['down'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['down'] . ']';
                }
                $b .= '</td><td class="button cell">';
                // The delete button
                    $b .= '<a href="index.php?action=delete_index&amp;sesskey=' . sesskey() . '&amp;index=' . $index->getName() . '&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['delete'] . ']</a>';
                $b .= '</td><td class="button cell">';
                // The view xml button
                $b .= '<a href="index.php?action=view_index_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;index=' . $index->getName() . '&amp;table=' . $table->getName() . '&amp;select=edited">[' . $this->str['viewxml'] . ']</a>';
                // The readable info
                $r = '</td><td class="readableinfo cell">' . $index->readableInfo() . '</td>';
                // Print table row
            $o .= '<tr class="r' . $row . '"><td class="table cell">' . $i . $b . $r .'</tr>';
                $row = ($row + 1) % 2;
            }
            $o .= '</table>';
        }

        $this->output = $o;

        // Launch postaction if exists (leave this here!)
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

        // Return ok if arrived here
        return $result;
    }
}

