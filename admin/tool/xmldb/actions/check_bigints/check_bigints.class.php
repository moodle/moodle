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
 * reporting about the ones not physically implemented as BIGINTs
 * and providing one SQL script to fix all them. MDL-11038
 *
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check_bigints extends XMLDBCheckAction {
    /**
     * Init method, every subclass will have its own
     */
    function init() {
        global $DB;

        $this->introstr = 'confirmcheckbigints';
        parent::init();

        // Set own core attributes

        // Set own custom attributes

        // Get needed strings
        $this->loadStrings(array(
            'wrongints' => 'tool_xmldb',
            'nowrongintsfound' => 'tool_xmldb',
            'yeswrongintsfound' => 'tool_xmldb',
        ));
    }

    protected function check_table(xmldb_table $xmldb_table, array $metacolumns) {
        $o = '';
        $wrong_fields = array();

        // Get and process XMLDB fields
        if ($xmldb_fields = $xmldb_table->getFields()) {
            $o.='        <ul>';
            foreach ($xmldb_fields as $xmldb_field) {
                // If the field isn't integer(10), skip
                if ($xmldb_field->getType() != XMLDB_TYPE_INTEGER) {
                    continue;
                }
                // If the metadata for that column doesn't exist, skip
                if (!isset($metacolumns[$xmldb_field->getName()])) {
                    continue;
                }
                $minlength = $xmldb_field->getLength();
                if ($minlength > 18) {
                    // Anything above 18 is borked, just ignore it here.
                    $minlength = 18;
                }
                // To variable for better handling
                $metacolumn = $metacolumns[$xmldb_field->getName()];
                // Going to check this field in DB
                $o.='            <li>' . $this->str['field'] . ': ' . $xmldb_field->getName() . ' ';
                // Detect if the physical field is wrong
                if (($metacolumn->meta_type != 'I' and $metacolumn->meta_type != 'R') or $metacolumn->max_length < $minlength) {
                    $o.='<font color="red">' . $this->str['wrong'] . '</font>';
                    // Add the wrong field to the list
                    $obj = new stdClass();
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

        return array($o, $wrong_fields);
    }

    protected function display_results(array $wrong_fields) {
        global $DB;
        $dbman = $DB->get_manager();

        $s = '';
        $r = '<table class="generaltable boxaligncenter boxwidthwide" border="0" cellpadding="5" cellspacing="0" id="results">';
        $r.= '  <tr><td class="generalboxcontent">';
        $r.= '    <h2 class="main">' . $this->str['searchresults'] . '</h2>';
        $r.= '    <p class="centerpara">' . $this->str['wrongints'] . ': ' . count($wrong_fields) . '</p>';
        $r.= '  </td></tr>';
        $r.= '  <tr><td class="generalboxcontent">';

        // If we have found wrong integers inform about them
        if (count($wrong_fields)) {
            $r.= '    <p class="centerpara">' . $this->str['yeswrongintsfound'] . '</p>';
            $r.= '        <ul>';
            foreach ($wrong_fields as $obj) {
                $xmldb_table = $obj->table;
                $xmldb_field = $obj->field;
                $sqlarr = $dbman->generator->getAlterFieldSQL($xmldb_table, $xmldb_field);
                $r.= '            <li>' . $this->str['table'] . ': ' . $xmldb_table->getName() . '. ' .
                                          $this->str['field'] . ': ' . $xmldb_field->getName() . '</li>';
                // Add to output if we have sentences
                if ($sqlarr) {
                    $sqlarr = $dbman->generator->getEndedStatements($sqlarr);
                    $s.= '<code>' . str_replace("\n", '<br />', implode('<br />', $sqlarr)). '</code><br />';
                }
            }
            $r.= '        </ul>';
            // Add the SQL statements (all together)
            $r.= '<hr />' . $s;
        } else {
            $r.= '    <p class="centerpara">' . $this->str['nowrongintsfound'] . '</p>';
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
