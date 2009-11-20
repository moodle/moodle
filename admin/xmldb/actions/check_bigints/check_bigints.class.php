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

/// This class will check all the int(10) fields existing in the DB
/// reporting about the ones not phisically implemented as BIGINTs
/// and providing one SQL script to fix all them. Also, under MySQL,
/// it performs one check of signed bigints. MDL-11038

class check_bigints extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own core attributes

    /// Set own custom attributes

    /// Get needed strings
        $this->loadStrings(array(
            'confirmcheckbigints' => 'xmldb',
            'ok' => '',
            'wrong' => 'xmldb',
            'table' => 'xmldb',
            'field' => 'xmldb',
            'searchresults' => 'xmldb',
            'wrongints' => 'xmldb',
            'completelogbelow' => 'xmldb',
            'nowrongintsfound' => 'xmldb',
            'yeswrongintsfound' => 'xmldb',
            'mysqlextracheckbigints' => 'xmldb',
            'yes' => '',
            'no' => '',
            'error' => '',
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
        global $CFG, $XMLDB, $db;

    /// And we nedd some ddl suff
        require_once ($CFG->libdir . '/ddllib.php');

    /// Here we'll acummulate all the wrong fields found
        $wrong_fields = array();

    /// Correct fields must be type bigint for MySQL and int8 for PostgreSQL
        switch ($CFG->dbfamily) {
            case 'mysql':
                $correct_type = 'bigint';
                break;
            case 'postgres':
                $correct_type = 'int8';
                break;
            default:
                $correct_type = NULL;
        }

    /// Do the job, setting $result as needed

    /// Get the confirmed to decide what to do
        $confirmed = optional_param('confirmed', false, PARAM_BOOL);

    /// If  not confirmed, show confirmation box
        if (!$confirmed) {
            $o = '<table class="generalbox" border="0" cellpadding="5" cellspacing="0" id="notice">';
            $o.= '  <tr><td class="generalboxcontent">';
            $o.= '    <p class="centerpara">' . $this->str['confirmcheckbigints'] . '</p>';
            if ($CFG->dbfamily == 'mysql') {
                $o.= '    <p class="centerpara">' . $this->str['mysqlextracheckbigints'] . '</p>';
            }
            $o.= '    <table class="boxaligncenter" cellpadding="20"><tr><td>';
            $o.= '      <div class="singlebutton">';
            $o.= '        <form action="index.php?action=check_bigints&amp;sesskey=' . sesskey() . '&amp;confirmed=yes" method="post"><fieldset class="invisiblefieldset">';
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
                    /// Foreach table, process its fields
                        foreach ($xmldb_tables as $xmldb_table) {
                        /// Skip table if not exists
                            if (!table_exists($xmldb_table)) {
                                continue;
                            }
                        /// Fetch metadata from phisical DB. All the columns info.
                            if ($metacolumns = $db->MetaColumns($CFG->prefix . $xmldb_table->getName())) {
                                $metacolumns = array_change_key_case($metacolumns, CASE_LOWER);
                            } else {
                            //// Skip table if no metacolumns is available for it
                                continue;
                            }
                        /// Table processing starts here
                            $o.='            <li>' . $xmldb_table->getName();
                        /// Get and process XMLDB fields
                            if ($xmldb_fields = $xmldb_table->getFields()) {
                                $o.='        <ul>';
                                foreach ($xmldb_fields as $xmldb_field) {
                                /// If the field isn't integer(10), skip
                                    if ($xmldb_field->getType() != XMLDB_TYPE_INTEGER || $xmldb_field->getLength() != 10) {
                                        continue;
                                    }
                                /// If the metadata for that column doesn't exist, skip
                                    if (!isset($metacolumns[$xmldb_field->getName()])) {
                                        continue;
                                    }
                                /// To variable for better handling
                                    $metacolumn = $metacolumns[$xmldb_field->getName()];
                                /// Going to check this field in DB
                                    $o.='            <li>' . $this->str['field'] . ': ' . $xmldb_field->getName() . ' ';
                                /// Detect if the phisical field is wrong and, under mysql, check for incorrect signed fields too
                                    if ($metacolumn->type != $correct_type || ($CFG->dbfamily == 'mysql' && $xmldb_field->getUnsigned() && !$metacolumn->unsigned)) {
                                        $o.='<font color="red">' . $this->str['wrong'] . '</font>';
                                    /// Add the wrong field to the list
                                        $obj = new object;
                                        $obj->table = $xmldb_table;
                                        $obj->field = $xmldb_field;
                                        $wrong_fields[] = $obj;
                                    } else {
                                        $o.='<font color="green">' . $this->str['ok'] . '</font>';
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
            $r.= '    <p class="centerpara">' . $this->str['wrongints'] . ': ' . count($wrong_fields) . '</p>';
            $r.= '  </td></tr>';
            $r.= '  <tr><td class="generalboxcontent">';

        /// If we have found wrong integers inform about them
            if (count($wrong_fields)) {
                $r.= '    <p class="centerpara">' . $this->str['yeswrongintsfound'] . '</p>';
                $r.= '        <ul>';
                foreach ($wrong_fields as $obj) {
                    $xmldb_table = $obj->table;
                    $xmldb_field = $obj->field;
                /// MySQL directly supports this
                    if ($CFG->dbfamily == 'mysql') {
                        $sqlarr = $xmldb_table->getAlterFieldSQL($CFG->dbtype, $CFG->prefix, $xmldb_field, true);
                /// PostgreSQL (XMLDB implementation) is a bit, er... imperfect.
                    } else if ($CFG->dbfamily == 'postgres') {
                        $sqlarr = array('ALTER TABLE ' . $CFG->prefix . $xmldb_table->getName() .
                                  ' ALTER COLUMN ' . $xmldb_field->getName() . ' TYPE BIGINT;');
                    }
                    $r.= '            <li>' . $this->str['table'] . ': ' . $xmldb_table->getName() . '. ' .
                                              $this->str['field'] . ': ' . $xmldb_field->getName() . '</li>';
                /// Add to output if we have sentences
                    if ($sqlarr) {
                        $s.= '<code>' . str_replace("\n", '<br />', implode('<br />', $sqlarr)) . '</code><br />';
                    }
                }
                $r.= '        </ul>';
            /// Add the SQL statements (all together)
                $r.= '<hr />' . $s;
            } else {
                $r.= '    <p class="centerpara">' . $this->str['nowrongintsfound'] . '</p>';
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
