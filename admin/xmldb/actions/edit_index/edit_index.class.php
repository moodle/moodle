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
 * This class will provide the interface for all the edit index actions
 *
 * @package   xmldb-editor
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_index extends XMLDBAction {

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
        global $CFG, $XMLDB, $OUTPUT;

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

    /// Fetch request data
        $tableparam = required_param('table', PARAM_CLEAN);
        if (!$table =& $structure->getTable($tableparam)) {
            $this->errormsg = 'Wrong table specified: ' . $tableparam;
            return false;
        }
        $indexparam = required_param('index', PARAM_CLEAN);
        if (!$index =& $table->getIndex($indexparam)) {
        /// Arriving here from a name change, looking for the new key name
            $indexparam = required_param('name', PARAM_CLEAN);
            $index =& $table->getIndex($indexparam);
        }

        $dbdir =& $XMLDB->dbdirs[$dirpath];
        $origstructure =& $dbdir->xml_file->getStructure();

    /// Add the main form
        $o = '<form id="form" action="index.php" method="post">';
        $o.= '<div>';
        $o.= '    <input type="hidden" name ="dir" value="' . str_replace($CFG->dirroot, '', $dirpath) . '" />';
        $o.= '    <input type="hidden" name ="table" value="' . $tableparam .'" />';
        $o.= '    <input type="hidden" name ="index" value="' . $indexparam .'" />';
        $o.= '    <input type="hidden" name ="sesskey" value="' . sesskey() .'" />';
        $o.= '    <input type="hidden" name ="action" value="edit_index_save" />';
        $o.= '    <input type="hidden" name ="postaction" value="edit_table" />';
        $o.= '    <table id="formelements" class="boxaligncenter">';
    /// XMLDB index name
    /// If the index has dependencies, we cannot change its name
        $disabled = '';
        if ($structure->getIndexUses($table->getName(), $index->getName())) {
            $disabled = ' disabled="disabled " ';
        }
        $o.= '      <tr valign="top"><td><label for="name" accesskey="n">Name:</label></td><td colspan="2"><input name="name" type="text" size="30" id="name"' . $disabled . ' value="' . s($index->getName()) . '" /></td></tr>';
    /// XMLDB key comment
        $o.= '      <tr valign="top"><td><label for="comment" accesskey="c">Comment:</label></td><td colspan="2"><textarea name="comment" rows="3" cols="80" id="comment">' . s($index->getComment()) . '</textarea></td></tr>';
    /// xmldb_index Type
        $typeoptions = array (0 => 'not unique',
                              1 => 'unique');
        $o.= '      <tr valign="top"><td><label for="menuunique" accesskey="t">Type:</label></td>';
        $select = html_writer::select($typeoptions, 'unique', $index->getUnique(), false);
        $o.= '        <td colspan="2">' . $select . '</td></tr>';
    /// xmldb_index Fields
        $o.= '      <tr valign="top"><td><label for="fields" accesskey="f">Fields:</label></td>';
        $o.= '        <td colspan="2"><input name="fields" type="text" size="40" maxlength="80" id="fields" value="' . s(implode(', ', $index->getFields())) . '" /></td></tr>';
    /// Change button
        $o.= '      <tr valign="top"><td>&nbsp;</td><td colspan="2"><input type="submit" value="' .$this->str['change'] . '" /></td></tr>';
        $o.= '    </table>';
        $o.= '</div></form>';
    /// Calculate the buttons
        $b = ' <p class="centerpara buttons">';
    /// The view original XML button
        if ($table->getIndex($indexparam)) {
            $b .= '&nbsp;<a href="index.php?action=view_index_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;select=original&amp;table=' . $tableparam . '&amp;index=' . $indexparam . '">[' . $this->str['vieworiginal'] . ']</a>';
        } else {
            $b .= '&nbsp;[' . $this->str['vieworiginal'] . ']';
        }
    /// The view edited XML button
        if ($index->hasChanged()) {
            $b .= '&nbsp;<a href="index.php?action=view_index_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;select=edited&amp;table=' . $tableparam . '&amp;index=' . $indexparam . '">[' . $this->str['viewedited'] . ']</a>';
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

