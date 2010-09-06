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
 * This class will edit one loaded XML file
 *
 * Main page to start editing one XML file. From here it's possible to access
 * to tables edition plus PHP code generation and other utilities
 *
 * @package   xmldb-editor
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_xml_file extends XMLDBAction {

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
            'edit' => 'xmldb',
            'up' => 'xmldb',
            'down' => 'xmldb',
            'delete' => 'xmldb',
            'vieworiginal' => 'xmldb',
            'viewedited' => 'xmldb',
            'tables' => 'xmldb',
            'newtable' => 'xmldb',
            'newtablefrommysql' => 'xmldb',
            'viewsqlcode' => 'xmldb',
            'viewphpcode' => 'xmldb',
            'reserved' => 'xmldb',
            'backtomainview' => 'xmldb',
            'viewxml' => 'xmldb',
            'pendingchanges' => 'xmldb',
            'pendingchangescannotbesaved' => 'xmldb',
            'save' => 'xmldb'
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
        //$this->does_generate = ACTION_NONE;
        $this->does_generate = ACTION_GENERATE_HTML;

    /// These are always here
        global $CFG, $XMLDB, $DB;

    /// Do the job, setting $result as needed

    /// Get the dir containing the file
        $dirpath = required_param('dir', PARAM_PATH);
        $dirpath = $CFG->dirroot . $dirpath;

    /// Get the correct dir
        if (!empty($XMLDB->dbdirs)) {
            $dbdir =& $XMLDB->dbdirs[$dirpath];
            if ($dbdir) {
            /// Only if the directory exists and it has been loaded
                if (!$dbdir->path_exists || !$dbdir->xml_loaded) {
                    return false;
                }
            /// Check if the in-memory object exists and create it
                if (empty($XMLDB->editeddirs)) {
                    $XMLDB->editeddirs = array();
                }
            /// Check if the dir exists and copy it from dbdirs
                if (!isset($XMLDB->editeddirs[$dirpath])) {
                    $XMLDB->editeddirs[$dirpath] = unserialize(serialize($dbdir));
                }
            /// Get it
                $editeddir =& $XMLDB->editeddirs[$dirpath];
                $structure =& $editeddir->xml_file->getStructure();

            /// Add the main form
                $o = '<form id="form" action="index.php" method="post">';
                $o.= '<div>';
                $o.= '    <input type="hidden" name ="dir" value="' . str_replace($CFG->dirroot, '', $dirpath) . '" />';
                $o.= '    <input type="hidden" name ="action" value="edit_xml_file_save" />';
                $o.= '    <input type="hidden" name ="postaction" value="edit_xml_file" />';
                $o.= '    <input type="hidden" name ="path" value="' . s($structure->getPath()) .'" />';
                $o.= '    <input type="hidden" name ="version" value="' . s($structure->getVersion()) .'" />';
                $o.= '    <input type="hidden" name ="sesskey" value="' . sesskey() .'" />';
                $o.= '    <table id="formelements" class="boxaligncenter">';
                $o.= '      <tr valign="top"><td>Path:</td><td>' . s($structure->getPath()) . '</td></tr>';
                $o.= '      <tr valign="top"><td>Version:</td><td>' . s($structure->getVersion()) . '</td></tr>';
                $o.= '      <tr valign="top"><td><label for="comment" accesskey="c">Comment:</label></td><td><textarea name="comment" rows="3" cols="80" id="comment">' . $structure->getComment() . '</textarea></td></tr>';
                $o.= '      <tr><td>&nbsp;</td><td><input type="submit" value="' .$this->str['change'] . '" /></td></tr>';
                $o.= '    </table>';
                $o.= '</div></form>';
            /// Calculate the pending changes / save message
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
            /// Calculate the buttons
                $b = ' <p class="centerpara buttons">';
            /// The view original XML button
                $b .= '&nbsp;<a href="index.php?action=view_structure_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;select=original">[' . $this->str['vieworiginal'] . ']</a>';
            /// The view edited XML button
                if ($structure->hasChanged()) {
                    $b .= '&nbsp;<a href="index.php?action=view_structure_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;select=edited">[' . $this->str['viewedited'] . ']</a>';
                } else {
                    $b .= '&nbsp;[' . $this->str['viewedited'] . ']';
                }
            /// The new table button
                $b .= '&nbsp;<a href="index.php?action=new_table&amp;sesskey=' . sesskey() . '&amp;postaction=edit_table&amp;table=changeme&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['newtable'] . ']</a>';
            /// The new from MySQL button
                if ($DB->get_dbfamily() == 'mysql') {
                    $b .= '&nbsp;<a href="index.php?action=new_table_from_mysql&amp;sesskey=' . sesskey() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['newtablefrommysql'] . ']</a>';
                } else {
                    $b .= '&nbsp;[' . $this->str['newtablefrommysql'] . ']';
                }

            /// The view sql code button
                $b .= '<a href="index.php?action=view_structure_sql&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' .$this->str['viewsqlcode'] . ']</a>';
            /// The view php code button
                $b .= '&nbsp;<a href="index.php?action=view_structure_php&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['viewphpcode'] . ']</a>';
            /// The save button (if possible)
                if ($cansavenow) {
                    $b .= '&nbsp;<a href="index.php?action=save_xml_file&amp;sesskey=' . sesskey() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;time=' . time() . '&amp;unload=false&amp;postaction=edit_xml_file&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['save'] . ']</a>';
                }

            /// The back to main menu button
                $b .= '&nbsp;<a href="index.php?action=main_view#lastused">[' . $this->str['backtomainview'] . ']</a>';
                $b .= '</p>';
                $o .= $e . $b;

            /// Join all the reserved words into one big array
            /// Calculate list of available SQL generators
                require_once("$CFG->libdir/ddl/sql_generator.php");
                $reserved_words = sql_generator::getAllReservedWords();

            /// Add the tables list
                $tables = $structure->getTables();
                if ($tables) {
                    $o .= '<h3 class="main">' . $this->str['tables'] . '</h3>';
                    $o .= '<table id="listtables" border="0" cellpadding="5" cellspacing="1" class="boxaligncenter flexible">';
                    $row = 0;
                    foreach ($tables as $table) {
                    /// The table name (link to edit table)
                        $t = '<a href="index.php?action=edit_table&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">' . $table->getName() . '</a>';
                    /// Calculate buttons
                        $b = '</td><td class="button cell">';
                    /// The edit button
                        $b .= '<a href="index.php?action=edit_table&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['edit'] . ']</a>';
                        $b .= '</td><td class="button cell">';
                    /// The up button
                        if ($table->getPrevious()) {
                            $b .= '<a href="index.php?action=move_updown_table&amp;direction=up&amp;sesskey=' . sesskey() . '&amp;table=' . $table->getName() . '&amp;postaction=edit_xml_file' . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['up'] . ']</a>';
                        } else {
                            $b .= '[' . $this->str['up'] . ']';
                        }
                        $b .= '</td><td class="button cell">';
                    /// The down button
                        if ($table->getNext()) {
                            $b .= '<a href="index.php?action=move_updown_table&amp;direction=down&amp;sesskey=' . sesskey() . '&amp;table=' . $table->getName() . '&amp;postaction=edit_xml_file' . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['down'] . ']</a>';
                        } else {
                            $b .= '[' . $this->str['down'] . ']';
                        }
                        $b .= '</td><td class="button cell">';
                    /// The delete button (if we have more than one and it isn't used)
                        if (count($tables) > 1 &&
                            !$structure->getTableUses($table->getName())) {
                            ///!$structure->getTableUses($table->getName())) {
                            $b .= '<a href="index.php?action=delete_table&amp;sesskey=' . sesskey() . '&amp;table=' . $table->getName() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['delete'] . ']</a>';
                        } else {
                            $b .= '[' . $this->str['delete'] . ']';
                        }
                        $b .= '</td><td class="button cell">';
                    /// The view xml button
                        $b .= '<a href="index.php?action=view_table_xml&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '&amp;table=' . $table->getName() . '&amp;select=edited">[' . $this->str['viewxml'] . ']</a>';
                    /// Detect if the table name is a reserved word
                         if (array_key_exists($table->getName(), $reserved_words)) {
                             $b .= '&nbsp;<a href="index.php?action=view_reserved_words"><span class="error">' . $this->str['reserved'] . '</span></a>';
                         }
                        $b .= '</td>';
                    /// Print table row
                        $o .= '<tr class="r' . $row . '"><td class="table cell">' . $t . $b . '</tr>';
                        $row = ($row + 1) % 2;
                    }
                    $o .= '</table>';
                }
            ///Add the back to main
            $this->output = $o;
            }
        }

    /// Launch postaction if exists (leave this unmodified)
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

        return $result;
    }
}

