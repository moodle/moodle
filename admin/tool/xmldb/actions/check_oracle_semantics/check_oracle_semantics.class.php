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
 * @package   tool_xmldb
 * @copyright 2011 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This class will check all the varchar2() columns
 * in the Moodle installed DB, looking for incorrect (INT)
 * length semanticas providing one SQL script to fix all
 * them by changing to cross-db (CHAR) length semantics.
 * See MDL-29322 for more details.
 *
 * @package    tool_xmldb
 * @copyright 2011 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check_oracle_semantics extends XMLDBCheckAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        $this->introstr = 'confirmcheckoraclesemantics';
        parent::init();

        // Set own core attributes

        // Set own custom attributes

        // Get needed strings
        $this->loadStrings(array(
            'wrongoraclesemantics' => 'tool_xmldb',
            'nowrongoraclesemanticsfound' => 'tool_xmldb',
            'yeswrongoraclesemanticsfound' => 'tool_xmldb',
            'expected' => 'tool_xmldb',
            'actual' => 'tool_xmldb',
        ));
    }

    protected function check_table(xmldb_table $xmldb_table, array $metacolumns) {
        global $DB;
        $o = '';
        $wrong_fields = array();

        // Get and process XMLDB fields
        if ($xmldb_fields = $xmldb_table->getFields()) {
            $o .= '<ul>';
            foreach ($xmldb_fields as $xmldb_field) {

                // Get the type of the column, we only will process CHAR (VARCHAR2) ones
                if ($xmldb_field->getType() != XMLDB_TYPE_CHAR) {
                    continue;
                }

                $o.='<li>' . $this->str['field'] . ': ' . $xmldb_field->getName() . ' ';

                // Get current semantic from dictionary, we only will process B (BYTE) ones
                // suplying the SQL code to change them to C (CHAR) semantic
                $params = array(
                    'table_name' => textlib::strtoupper($DB->get_prefix() . $xmldb_table->getName()),
                    'column_name' => textlib::strtoupper($xmldb_field->getName()),
                    'data_type' => 'VARCHAR2');
                $currentsemantic = $DB->get_field_sql('
                    SELECT char_used
                      FROM user_tab_columns
                     WHERE table_name = :table_name
                       AND column_name = :column_name
                       AND data_type = :data_type', $params);

                // If using byte semantics, we'll need to change them to char semantics
                if ($currentsemantic == 'B') {
                    $info = '(' . $this->str['expected'] . " 'CHAR', " . $this->str['actual'] . " 'BYTE')";
                    $o .= '<font color="red">' . $this->str['wrong'] . " $info</font>";
                    // Add the wrong field to the list
                    $obj = new stdClass();
                    $obj->table = $xmldb_table;
                    $obj->field = $xmldb_field;
                    $wrong_fields[] = $obj;
                } else {
                    $o .= '<font color="green">' . $this->str['ok'] . '</font>';
                }
                $o .= '</li>';
            }
            $o .= '</ul>';
        }

        return array($o, $wrong_fields);
    }

    protected function display_results(array $wrong_fields) {
        global $DB;
        $dbman = $DB->get_manager();

        $s = '';
        $r = '<table class="generaltable boxaligncenter boxwidthwide" border="0" cellpadding="5" cellspacing="0" id="results">';
        $r.= '  <tr><td class="generalboxcontent">';
        $r.= '    <h2 class="main">' . $this->str['searchresults'] . '</h2>';
        $r.= '    <p class="centerpara">' . $this->str['wrongoraclesemantics'] . ': ' . count($wrong_fields) . '</p>';
        $r.= '  </td></tr>';
        $r.= '  <tr><td class="generalboxcontent">';

        // If we have found wrong defaults inform about them
        if (count($wrong_fields)) {
            $r.= '    <p class="centerpara">' . $this->str['yeswrongoraclesemanticsfound'] . '</p>';
            $r.= '        <ul>';
            foreach ($wrong_fields as $obj) {
                $xmldb_table = $obj->table;
                $xmldb_field = $obj->field;

                $r.= '            <li>' . $this->str['table'] . ': ' . $xmldb_table->getName() . '. ' .
                                          $this->str['field'] . ': ' . $xmldb_field->getName() . ', ' .
                                          $this->str['expected'] . ' ' . "'CHAR'" . ' ' .
                                          $this->str['actual'] . ' ' . "'BYTE'" . '</li>';

                $sql = 'ALTER TABLE ' . $DB->get_prefix() . $xmldb_table->getName() . ' MODIFY ' .
                       $xmldb_field->getName() . ' VARCHAR2(' . $xmldb_field->getLength() . ' CHAR)';
                $sql = $dbman->generator->getEndedStatements($sql);
                $s.= '<code>' . str_replace("\n", '<br />', $sql) . '</code><br />';
            }
            $r.= '        </ul>';
            // Add the SQL statements (all together)
            $r.= '<hr />' . $s;
        } else {
            $r.= '    <p class="centerpara">' . $this->str['nowrongoraclesemanticsfound'] . '</p>';
        }
        $r.= '  </td></tr>';
        $r.= '  <tr><td class="generalboxcontent">';
        // Add the complete log message
        $r.= '    <p class="centerpara">' . $this->str['completelogbelow'] . '</p>';
        $r.= '  </td></tr>';
        $r.= '</table>';

        return $r;
    }
}
