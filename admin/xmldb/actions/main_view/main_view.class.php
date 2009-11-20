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

/// This class will show all the actions available under the XMLDB interface

class main_view extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own custom attributes
        $this->sesskey_protected = false; // This action doesn't need sesskey protection

    /// Get needed strings
        $this->loadStrings(array(
            'load' => 'xmldb',
            'create' => 'xmldb',
            'edit' => 'xmldb',
            'save' => 'xmldb',
            'revert' => 'xmldb',
            'unload' => 'xmldb',
            'delete' => 'xmldb',
            'reservedwords' => 'xmldb',
            'test' => 'xmldb',
            'gotolastused' => 'xmldb',
            'checkindexes' => 'xmldb',
            'checkbigints' => 'xmldb'
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
        global $CFG, $XMLDB, $SESSION;

    /// Get lastused
        $o = '';
        if (isset($SESSION->lastused)) {
            if ($lastused = $SESSION->lastused) {
            /// Print link
                $o .= '<p class="centerpara"><a href="#lastused">' . $this->str['gotolastused'] . '</a></p>';
            }
        } else {
            $lastused = NULL;
        }

    /// Calculate the buttons
        $b = '<p class="centerpara buttons">';
    /// The reserved_words button
        $b .= '&nbsp;<a href="index.php?action=view_reserved_words">[' . $this->str['reservedwords'] . ']</a>';
    /// The test button
        $b .= '&nbsp;<a href="index.php?action=test&amp;sesskey=' . sesskey() . '">[' . $this->str['test'] . ']</a>';
    /// The check indexes button
        $b .= '&nbsp;<a href="index.php?action=check_indexes&amp;sesskey=' . sesskey() . '">[' . $this->str['checkindexes'] . ']</a>';
    /// The check bigints button (only for MySQL and PostgreSQL) MDL-11038a
        if ($CFG->dbfamily == 'mysql' || $CFG->dbfamily == 'postgres') {
            $b .= '&nbsp;<a href="index.php?action=check_bigints&amp;sesskey=' . sesskey() . '">[' . $this->str['checkbigints'] . ']</a>';
        }
        $b .= '</p>';
    /// Send buttons to output
        $o .= $b;

    /// Do the job

    /// Get the list of DB directories
        $result = $this->launch('get_db_directories');
    /// Display list of DB directories if everything is ok
        if ($result && !empty($XMLDB->dbdirs)) {
            $o .= '<table id="listdirectories" border="0" cellpadding="5" cellspacing="1" class="boxaligncenter flexible">';
            $row = 0;
            foreach ($XMLDB->dbdirs as $key => $dbdir) {
            /// Detect if this is the lastused dir
                $hithis = false;
                if (str_replace($CFG->dirroot, '', $key) == $lastused) {
                    $hithis = true;
                }
                $elementtext = str_replace($CFG->dirroot . '/', '', $key);
            /// Calculate the dbdir has_changed field if needed
                if (!isset($dbdir->has_changed) && isset($dbdir->xml_loaded)) {
                    $dbdir->xml_changed = false;
                    if (isset($XMLDB->editeddirs[$key])) {
                        $editeddir =& $XMLDB->editeddirs[$key];
                        if (isset($editeddir->xml_file)) {
                            $structure =& $editeddir->xml_file->getStructure();
                            if ($structure->hasChanged()) {
                                $dbdir->xml_changed = true;
                                $editeddir->xml_changed = true;
                            }
                        }
                    }
                }
            /// Calculate the buttons
                $b = ' <td class="button cell">';
            /// The create button
                if ($dbdir->path_exists &&
                    !file_exists($key . '/install.xml') &&
                    is_writeable($key)) {
                    $b .= '<a href="index.php?action=create_xml_file&amp;sesskey=' . sesskey() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $key)) . '&amp;time=' . time() . '&amp;postaction=main_view#lastused">[' . $this->str['create'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['create'] . ']';
                }
                $b .= '</td><td class="button cell">';
            /// The load button
                if ($dbdir->path_exists &&
                    file_exists($key . '/install.xml') &&
                    is_readable($key . '/install.xml') &&
                    empty($dbdir->xml_loaded)) {
                    $b .= '<a href="index.php?action=load_xml_file&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $key)) . '&amp;time=' . time() . '&amp;postaction=main_view#lastused">[' . $this->str['load'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['load'] . ']';
                }
                $b .= '</td><td class="button cell">';
            /// The edit button
                if ($dbdir->path_exists &&
                    file_exists($key . '/install.xml') &&
                    is_readable($key . '/install.xml') &&
                    is_readable($key) &&
                    !empty($dbdir->xml_loaded)) {
                    $b .= '<a href="index.php?action=edit_xml_file&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $key)) . '">[' . $this->str['edit'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['edit'] . ']';
                }
                $b .= '</td><td class="button cell">';
            /// The save button
                if ($dbdir->path_exists &&
                    file_exists($key . '/install.xml') &&
                    is_writeable($key . '/install.xml') &&
                    is_writeable($key) &&
                    !empty($dbdir->xml_loaded) &&
                    !empty($dbdir->xml_changed)) {
                    $b .= '<a href="index.php?action=save_xml_file&amp;sesskey=' . sesskey() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $key)) . '&amp;time=' . time() . '&amp;postaction=main_view#lastused">[' . $this->str['save'] . ']</a>';
                /// Check if the file has been manually edited while being modified in the editor
                    if ($dbdir->filemtime != filemtime($key . '/install.xml')) {
                    /// File manually modified. Add to errors.
                        if ($structure =& $dbdir->xml_file->getStructure()) {
                            $structure->errormsg = 'Warning: File locally modified while using the XMLDB Editor. Saving will overwrite local changes';
                        }
                    }
                } else {
                    $b .= '[' . $this->str['save'] . ']';
                }
                $b .= '</td><td class="button cell">';
            /// The revert button
                if ($dbdir->path_exists &&
                    file_exists($key . '/install.xml') &&
                    is_readable($key . '/install.xml') &&
                    is_writeable($key) &&
                    !empty($dbdir->xml_loaded) &&
                    !empty($dbdir->xml_changed)) {
                    $b .= '<a href="index.php?action=revert_changes&amp;sesskey=' . sesskey() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $key)) . '">[' . $this->str['revert'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['revert'] . ']';
                }
                $b .= '</td><td class="button cell">';
            /// The unload button
                if ($dbdir->path_exists &&
                    file_exists($key . '/install.xml') &&
                    is_readable($key . '/install.xml') &&
                    !empty($dbdir->xml_loaded) &&
                    empty($dbdir->xml_changed)) {
                    $b .= '<a href="index.php?action=unload_xml_file&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $key)) . '&amp;time=' . time() . '&amp;postaction=main_view#lastused">[' . $this->str['unload'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['unload'] . ']';
                }
                $b .= '</td><td class="button cell">';
            /// The delete button
                if ($dbdir->path_exists &&
                    file_exists($key . '/install.xml') &&
                    is_readable($key . '/install.xml') &&
                    is_writeable($key) &&
                    empty($dbdir->xml_loaded)) {
                    $b .= '<a href="index.php?action=delete_xml_file&amp;sesskey=' . sesskey() . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $key)) . '">[' . $this->str['delete'] . ']</a>';
                } else {
                    $b .= '[' . $this->str['delete'] . ']';
                }
                $b .= '</td>';
            /// if the file, exist, XML is viewable
                if ($dbdir->path_exists &&
                    file_exists($key . '/install.xml') &&
                    is_readable($key . '/install.xml')) {
                    $elementtext = '<a href="index.php?action=view_xml&amp;file=' . urlencode(str_replace($CFG->dirroot, '', $key) . '/install.xml') . '">' . $elementtext . '</a></td>';
                } else {
                    $elementtext = $elementtext . '</td>';
                }
            /// include the higlight
                if ($hithis) {
                    $o .= '<tr class="highlight"><td class="directory cell"><a name="lastused" />' . $elementtext . $b . '</tr>';
                } else {
                    $o .= '<tr class="r' . $row . '"><td class="directory cell">' . $elementtext . $b . '</tr>';
                }
                $row = ($row + 1) % 2;
            /// show errors if they exist
                if (isset($dbdir->xml_file)) {
                    if ($structure =& $dbdir->xml_file->getStructure()) {
                        if ($errors = $structure->getAllErrors()) {
                            if ($hithis) {
                                $o .= '<tr class="highlight"><td class="error cell" colspan="8">' . implode (', ', $errors) . '</td></tr>';
                            } else {
                                $o .= '<tr class="r' . $row . '"><td class="error cell" colspan="8">' . implode (', ', $errors) . '</td></tr>';
                            }
                        }
                    }
                }
            }
            $o .= '</table>';

        /// Set the output
            $this->output = $o;
        }

    /// Finally, return result
        return $result;
    }
}
?>
