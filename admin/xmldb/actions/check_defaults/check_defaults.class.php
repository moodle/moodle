<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas     http://dougiamas.com  //
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

/// This class will check all the default values existing in the DB
/// match those specified in the xml specs
/// and providing one SQL script to fix all them.

class check_defaults extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own core attributes

    /// Set own custom attributes

    /// Get needed strings
        $this->loadStrings(array(
            'confirmcheckdefaults' => 'xmldb',
            'ok' => '',
            'wrong' => 'xmldb',
            'table' => 'xmldb',
            'field' => 'xmldb',
            'searchresults' => 'xmldb',
            'wrongdefaults' => 'xmldb',
            'completelogbelow' => 'xmldb',
            'nowrongdefaultsfound' => 'xmldb',
            'yeswrongdefaultsfound' => 'xmldb',
            'yes' => '',
            'no' => '',
            'error' => '',
            'expected' => 'xmldb',
            'actual' => 'xmldb',
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
        global $CFG, $XMLDB, $DB;

    /// And we nedd some ddl suff
        require_once ($CFG->libdir . '/ddllib.php');
        $dbman = $DB->get_manager();

    /// Here we'll acummulate all the wrong fields found
        $wrong_fields = array();

    /// Do the job, setting $result as needed

    /// Get the confirmed to decide what to do
        $confirmed = optional_param('confirmed', false, PARAM_BOOL);

    /// If  not confirmed, show confirmation box
        if (!$confirmed) {
            $o = '<table class="generalbox" border="0" cellpadding="5" cellspacing="0" id="notice">';
            $o.= '  <tr><td class="generalboxcontent">';
            $o.= '    <p class="centerpara">' . $this->str['confirmcheckdefaults'] . '</p>';
            $o.= '    <table class="boxaligncenter" cellpadding="20"><tr><td>';
            $o.= '      <div class="singlebutton">';
            $o.= '        <form action="index.php?action=check_defaults&amp;confirmed=yes" method="post"><fieldset class="invisiblefieldset">';
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
                    $xmldb_file = new xmldb_file($dbdir->path . '/install.xml');

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
                            if (!$dbman->table_exists($xmldb_table)) {
                                continue;
                            }
                        /// Fetch metadata from phisical DB. All the columns info.
                            if (!$metacolumns = $DB->get_columns($xmldb_table->getName())) {
                            //// Skip table if no metacolumns is available for it
                                continue;
                            }
                        /// Table processing starts here
                            $o.='            <li>' . $xmldb_table->getName();
                        /// Get and process XMLDB fields
                            if ($xmldb_fields = $xmldb_table->getFields()) {
                                $o.='        <ul>';
                                foreach ($xmldb_fields as $xmldb_field) {

                                    // Get the default value for the field
                                    $xmldbdefault = $xmldb_field->getDefault();

                                    /// If the metadata for that column doesn't exist or 'id' field found, skip
                                    if (!isset($metacolumns[$xmldb_field->getName()]) or $xmldb_field->getName() == 'id') {
                                        continue;
                                    }

                                    /// To variable for better handling
                                    $metacolumn = $metacolumns[$xmldb_field->getName()];

                                    /// Going to check this field in DB
                                    $o.='            <li>' . $this->str['field'] . ': ' . $xmldb_field->getName() . ' ';

                                    // get the value of the physical default (or blank if there isn't one)
                                    if ($metacolumn->has_default==1) {
                                        $physicaldefault = $metacolumn->default_value;
                                    }
                                    else {
                                        $physicaldefault = '';
                                    }

                                    // there *is* a default and it's wrong
                                    if ($physicaldefault != $xmldbdefault) {
                                        $info = '('.$this->str['expected']." '$xmldbdefault', ".$this->str['actual'].
                                        " '$physicaldefault')";
                                        $o.='<font color="red">' . $this->str['wrong'] . " $info</font>";
                                    /// Add the wrong field to the list
                                        $obj = new object;
                                        $obj->table = $xmldb_table;
                                        $obj->field = $xmldb_field;
                                        $obj->physicaldefault = $physicaldefault;
                                        $obj->xmldbdefault = $xmldbdefault;
                                        $wrong_fields[] = $obj;
                                    } else {
                                        $o.='<font color="green">' . $this->str['ok'] . '</font>';
                                    }
                                    $o.='</li>';
                                }
                                $o.='        </ul>';
                            }
                            $o.='    </li>';
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
            $r.= '    <p class="centerpara">' . $this->str['wrongdefaults'] . ': ' . count($wrong_fields) . '</p>';
            $r.= '  </td></tr>';
            $r.= '  <tr><td class="generalboxcontent">';

        /// If we have found wrong defaults inform about them
            if (count($wrong_fields)) {
                $r.= '    <p class="centerpara">' . $this->str['yeswrongdefaultsfound'] . '</p>';
                $r.= '        <ul>';
                foreach ($wrong_fields as $obj) {
                    $xmldb_table = $obj->table;
                    $xmldb_field = $obj->field;
                    $physicaldefault = $obj->physicaldefault;
                    $xmldbdefault = $obj->xmldbdefault;

                    // get the alter table command
                    $sqlarr = $dbman->generator->getAlterFieldSQL($xmldb_table, $xmldb_field);

                    $r.= '            <li>' . $this->str['table'] . ': ' . $xmldb_table->getName() . '. ' .
                                              $this->str['field'] . ': ' . $xmldb_field->getName() . ', ' .
                                              $this->str['expected'] . ' ' . "'$xmldbdefault'" . ' ' .
                                              $this->str['actual'] . ' ' . "'$physicaldefault'" . '</li>';
                    /// Add to output if we have sentences
                    if ($sqlarr) {
                        $sqlarr = $dbman->generator->getEndedStatements($sqlarr);
                        $s.= '<code>' . str_replace("\n", '<br />', implode('<br />', $sqlarr)) . '</code><br />';
                    }
                }
                $r.= '        </ul>';
            /// Add the SQL statements (all together)
                $r.= '<hr />' . $s;
            } else {
                $r.= '    <p class="centerpara">' . $this->str['nowrongdefaultsfound'] . '</p>';
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
