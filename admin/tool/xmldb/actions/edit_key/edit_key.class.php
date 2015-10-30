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
 * This class will provide the interface for all the edit key actions
 *
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_key extends XMLDBAction {

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
            'yes' => '',
            'no' => '',
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

        // Fetch request data
        $tableparam = required_param('table', PARAM_CLEAN);
        if (!$table = $structure->getTable($tableparam)) {
            $this->errormsg = 'Wrong table specified: ' . $tableparam;
            return false;
        }
        $keyparam = required_param('key', PARAM_CLEAN);
        if (!$key = $table->getKey($keyparam)) {
            // Arriving here from a name change, looking for the new key name
            $keyparam = required_param('name', PARAM_CLEAN);
            $key = $table->getKey($keyparam);
        }

        $dbdir = $XMLDB->dbdirs[$dirpath];
        $origstructure = $dbdir->xml_file->getStructure();

        // Add the main form
        $o = '<form id="form" action="index.php" method="post">';
        $o.= '<div>';
        $o.= '    <input type="hidden" name ="dir" value="' . str_replace($CFG->dirroot, '', $dirpath) . '" />';
        $o.= '    <input type="hidden" name ="table" value="' . $tableparam .'" />';
        $o.= '    <input type="hidden" name ="key" value="' . $keyparam .'" />';
        $o.= '    <input type="hidden" name ="sesskey" value="' . sesskey() .'" />';
        $o.= '    <input type="hidden" name ="action" value="edit_key_save" />';
        $o.= '    <input type="hidden" name ="postaction" value="edit_table" />';
        $o.= '    <table id="formelements" class="boxaligncenter">';
        // XMLDB key name
        // If the key has dependencies, we cannot change its name
        $disabled = '';
        if ($structure->getKeyUses($table->getName(), $key->getName())) {
            $disabled = ' disabled="disabled " ';
        }
        $o.= '      <tr valign="top"><td><label for="name" accesskey="n">Name:</label></td><td colspan="2"><input name="name" type="text" size="30" id="name"' . $disabled . ' value="' . s($key->getName()) . '" /></td></tr>';
        // XMLDB key comment
        $o.= '      <tr valign="top"><td><label for="comment" accesskey="c">Comment:</label></td><td colspan="2"><textarea name="comment" rows="3" cols="80" id="comment">' . s($key->getComment()) . '</textarea></td></tr>';
        // xmldb_key Type
        $typeoptions = array (XMLDB_KEY_PRIMARY => $key->getXMLDBKeyName(XMLDB_KEY_PRIMARY),
                              XMLDB_KEY_UNIQUE  => $key->getXMLDBKeyName(XMLDB_KEY_UNIQUE),
                              XMLDB_KEY_FOREIGN   => $key->getXMLDBKeyName(XMLDB_KEY_FOREIGN),
                              XMLDB_KEY_FOREIGN_UNIQUE => $key->getXMLDBKeyName(XMLDB_KEY_FOREIGN_UNIQUE));
        // Only show the XMLDB_KEY_FOREIGN_UNIQUE if the Key has that type
        // if ($key->getType() != XMLDB_KEY_FOREIGN_UNIQUE) {
        // unset ($typeoptions[XMLDB_KEY_FOREIGN_UNIQUE);
        // }
        $select = html_writer::select($typeoptions, 'type', $key->getType(), false);

        $o.= '      <tr valign="top"><td><label for="menutype" accesskey="t">Type:</label></td>';
        $o.= '        <td colspan="2">' . $select . '</td></tr>';
        // xmldb_key Fields
        $o.= '      <tr valign="top"><td><label for="fields" accesskey="f">Fields:</label></td>';
        $o.= '        <td colspan="2"><input name="fields" type="text" size="40" maxlength="80" id="fields" value="' . s(implode(', ', $key->getFields())) . '" /></td></tr>';
        // xmldb_key Reftable
        $o.= '      <tr valign="top"><td><label for="reftable" accesskey="t">Reftable:</label></td>';
        $o.= '        <td colspan="2"><input name="reftable" type="text" size="20" maxlength="40" id="reftable" value="' . s($key->getReftable()) . '" /></td></tr>';
        // xmldb_key Reffields
        $o.= '      <tr valign="top"><td><label for="reffields" accesskey="t">Reffields:</label></td>';
        $o.= '        <td colspan="2"><input name="reffields" type="text" size="40" maxlength="80" id="reffields" value="' . s(implode(', ', $key->getRefFields())) . '" /></td></tr>';
        // Change button
        $o.= '      <tr valign="top"><td>&nbsp;</td><td colspan="2"><input type="submit" value="' .$this->str['change'] . '" /></td></tr>';
        $o.= '    </table>';
        $o.= '</div></form>';
        // Calculate the buttons
        $b = ' <p class="centerpara buttons">';
        // The view original XML button
        if ($table->getKey($keyparam)) {
            $b .= '&nbsp;<a href="index.php?action=view_key_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;select=original&amp;table=' . $tableparam . '&amp;key=' . $keyparam . '">[' . $this->str['vieworiginal'] . ']</a>';
        } else {
            $b .= '&nbsp;[' . $this->str['vieworiginal'] . ']';
        }
        // The view edited XML button
        if ($key->hasChanged()) {
            $b .= '&nbsp;<a href="index.php?action=view_key_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;select=edited&amp;table=' . $tableparam . '&amp;key=' . $keyparam . '">[' . $this->str['viewedited'] . ']</a>';
        } else {
            $b .= '&nbsp;[' . $this->str['viewedited'] . ']';
        }
        // The back to edit table button
        $b .= '&nbsp;<a href="index.php?action=edit_table&amp;table=' . $tableparam . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['back'] . ']</a>';
        $b .= '</p>';
        $o .= $b;

        $this->output = $o;

        // Launch postaction if exists (leave this here!)
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

        // Return ok if arrived here
        return $result;
    }
}

