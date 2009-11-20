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

/// This class will compare all the indexes found in the XMLDB definitions
/// with the phisical DB implementation, reporting about all the missing
/// indexes to be created to be 100% ok.

class check_indexes extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own core attributes

    /// Set own custom attributes

    /// Get needed strings
        $this->loadStrings(array(
            'confirmcheckindexes' => 'xmldb',
            'ok' => '',
            'missing' => 'xmldb',
            'table' => 'xmldb',
            'key' => 'xmldb',
            'index' => 'xmldb',
            'searchresults' => 'xmldb',
            'missingindexes' => 'xmldb',
            'completelogbelow' => 'xmldb',
            'nomissingindexesfound' => 'xmldb',
            'yesmissingindexesfound' => 'xmldb',
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

    /// And we nedd some ddl suff
        require_once ($CFG->libdir . '/ddllib.php');

    /// Here we'll acummulate all the missing indexes found
        $missing_indexes = array();

    /// Do the job, setting $result as needed

    /// Get the confirmed to decide what to do
        $confirmed = optional_param('confirmed', false, PARAM_BOOL);

    /// If  not confirmed, show confirmation box
        if (!$confirmed) {
            $o = '<table class="generalbox" border="0" cellpadding="5" cellspacing="0" id="notice">';
            $o.= '  <tr><td class="generalboxcontent">';
            $o.= '    <p class="centerpara">' . $this->str['confirmcheckindexes'] . '</p>';
            $o.= '    <table class="boxaligncenter" cellpadding="20"><tr><td>';
            $o.= '      <div class="singlebutton">';
            $o.= '        <form action="index.php?action=check_indexes&amp;sesskey=' . sesskey() . '&amp;confirmed=yes" method="post"><fieldset class="invisiblefieldset">';
            $o.= '          <input type="submit" value="'. $this->str['yes'] .'" /></fieldset></form></div>';
            $o.= '      </td><td>';
            $o.= '      <div class="singlebutton">';
            $o.= '        <form action="index.php?action=main_view" method="post"><fieldset class="invisiblefieldset">';
            $o.= '          <input type="submit" value="'. $this->str['no'] .'" /></fieldset></form></div>';
            $o.= '      </td></tr>';
            $o.= '    </table>';
            $o.= '  </td></tr>';
            $o.= '</table>';

            $this->output = $o;
        } else {
        /// The back to edit table button
            $b = ' <p class="centerpara buttons">';
            $b .= '<a href="index.php">[' . $this->str['back'] . ']</a>';
            $b .= '</p>';

        /// Iterate over $XMLDB->dbdirs, loading their XML data to memory
            if ($XMLDB->dbdirs) {
                $dbdirs =& $XMLDB->dbdirs;
                $o='<ul>';
                foreach ($dbdirs as $dbdir) {
                /// Only if the directory exists
                    if (!$dbdir->path_exists) {
                        continue;
                    }
                /// Load the XML file
                    $xmldb_file = new XMLDBFile($dbdir->path . '/install.xml');
                /// Load the needed XMLDB generator
                    $classname = 'XMLDB' . $CFG->dbtype;
                    $generator = new $classname();
                    $generator->setPrefix($CFG->prefix);

                /// Only if the file exists
                    if (!$xmldb_file->fileExists()) {
                        continue;
                    }
                /// Load the XML contents to structure
                    $loaded = $xmldb_file->loadXMLStructure();
                    if (!$loaded || !$xmldb_file->isLoaded()) {
                        notify('Errors found in XMLDB file: '. $dbdir->path . '/install.xml');
                        continue;
                    }
                /// Arriving here, everything is ok, get the XMLDB structure
                    $structure = $xmldb_file->getStructure();
                    $o.='    <li>' . str_replace($CFG->dirroot . '/', '', $dbdir->path . '/install.xml');
                /// Getting tables
                    if ($xmldb_tables = $structure->getTables()) {
                        $o.='        <ul>';
                    /// Foreach table, process its indexes and keys
                        foreach ($xmldb_tables as $xmldb_table) {
                        /// Skip table if not exists
                            if (!table_exists($xmldb_table)) {
                                continue;
                            }
                            $o.='            <li>' . $xmldb_table->getName();
                        /// Keys
                            if ($xmldb_keys = $xmldb_table->getKeys()) {
                                $o.='        <ul>';
                                foreach ($xmldb_keys as $xmldb_key) {
                                    $o.='            <li>' . $this->str['key'] . ': ' . $xmldb_key->readableInfo() . ' ';
                                /// Primaries are skipped
                                    if ($xmldb_key->getType() == XMLDB_KEY_PRIMARY) {
                                        $o.='<font color="green">' . $this->str['ok'] . '</font></li>';
                                        continue;
                                    }
                                /// If we aren't creating the keys or the key is a XMLDB_KEY_FOREIGN (not underlying index generated
                                /// automatically by the RDBMS) create the underlying (created by us) index (if doesn't exists)
                                    if (!$generator->getKeySQL($xmldb_table, $xmldb_key) || $xmldb_key->getType() == XMLDB_KEY_FOREIGN) {
                                    /// Create the interim index
                                        $xmldb_index = new XMLDBIndex('anyname');
                                        $xmldb_index->setFields($xmldb_key->getFields());
                                        switch ($xmldb_key->getType()) {
                                            case XMLDB_KEY_UNIQUE:
                                            case XMLDB_KEY_FOREIGN_UNIQUE:
                                                $xmldb_index->setUnique(true);
                                                break;
                                            case XMLDB_KEY_FOREIGN:
                                                $xmldb_index->setUnique(false);
                                                break;
                                        }
                                    /// Check if the index exists in DB
                                        if (index_exists($xmldb_table, $xmldb_index)) {
                                            $o.='<font color="green">' . $this->str['ok'] . '</font>';
                                        } else {
                                            $o.='<font color="red">' . $this->str['missing'] . '</font>';
                                        /// Add the missing index to the list
                                            $obj = new object;
                                            $obj->table = $xmldb_table;
                                            $obj->index = $xmldb_index;
                                            $missing_indexes[] = $obj;
                                        }
                                    }
                                    $o.='</li>';
                                }
                                $o.='        </ul>';
                            }
                        /// Indexes
                            if ($xmldb_indexes = $xmldb_table->getIndexes()) {
                                $o.='        <ul>';
                                foreach ($xmldb_indexes as $xmldb_index) {
                                    $o.='            <li>' . $this->str['index'] . ': ' . $xmldb_index->readableInfo() . ' ';
                                /// Check if the index exists in DB
                                    if (index_exists($xmldb_table, $xmldb_index)) {
                                        $o.='<font color="green">' . $this->str['ok'] . '</font>';
                                    } else {
                                        $o.='<font color="red">' . $this->str['missing'] . '</font>';
                                    /// Add the missing index to the list
                                        $obj = new object;
                                        $obj->table = $xmldb_table;
                                        $obj->index = $xmldb_index;
                                        $missing_indexes[] = $obj;
                                    }
                                    $o.='</li>';
                                }
                                $o.='        </ul>';
                            }
                            $o.='    </li>';
                        /// Give the script some more time (resetting to current if exists)
                            if ($currenttl = @ini_get('max_execution_time')) {
                                @ini_set('max_execution_time',$currenttl);
                            }
                        }
                        $o.='        </ul>';
                    }
                    $o.='    </li>';
                }
                $o.='</ul>';
            }

        /// We have finished, let's show the results of the search
            $s = '';
            $r = '<table class="generalbox boxaligncenter boxwidthwide" border="0" cellpadding="5" cellspacing="0" id="results">';
            $r.= '  <tr><td class="generalboxcontent">';
            $r.= '    <h2 class="main">' . $this->str['searchresults'] . '</h2>';
            $r.= '    <p class="centerpara">' . $this->str['missingindexes'] . ': ' . count($missing_indexes) . '</p>';
            $r.= '  </td></tr>';
            $r.= '  <tr><td class="generalboxcontent">';

        /// If we have found missing indexes inform about them
            if (count($missing_indexes)) {
                $r.= '    <p class="centerpara">' . $this->str['yesmissingindexesfound'] . '</p>';
                $r.= '        <ul>';
                foreach ($missing_indexes as $obj) {
                    $xmldb_table = $obj->table;
                    $xmldb_index = $obj->index;
                    $sqlarr = $xmldb_table->getAddIndexSQL($CFG->dbtype, $CFG->prefix, $xmldb_index, true);
                    $r.= '            <li>' . $this->str['table'] . ': ' . $xmldb_table->getName() . '. ' .
                                              $this->str['index'] . ': ' . $xmldb_index->readableInfo() . '</li>';
                    $s.= '<code>' . str_replace("\n", '<br />', implode('<br />', $sqlarr)) . '</code><br />';
                    
                }
                $r.= '        </ul>';
            /// Add the SQL statements (all together)
                $r.= '<hr />' . $s;
            } else {
                $r.= '    <p class="centerpara">' . $this->str['nomissingindexesfound'] . '</p>';
            }
            $r.= '  </td></tr>';
            $r.= '  <tr><td class="generalboxcontent">';
        /// Add the complete log message
            $r.= '    <p class="centerpara">' . $this->str['completelogbelow'] . '</p>';
            $r.= '  </td></tr>';
            $r.= '</table>';

            $this->output = $b . $r . $o;
        }

    /// Launch postaction if exists (leave this here!)
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

    /// Return ok if arrived here
        return $result;
    }
}
?>
