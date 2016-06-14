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
 * This class will compare all the indexes found in the XMLDB definitions
 * with the physical DB implementation, reporting about all the missing
 * indexes to be created to be 100% ok.
 *
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check_indexes extends XMLDBCheckAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        $this->introstr = 'confirmcheckindexes';
        parent::init();

        // Set own core attributes

        // Set own custom attributes

        // Get needed strings
        $this->loadStrings(array(
            'missing' => 'tool_xmldb',
            'key' => 'tool_xmldb',
            'index' => 'tool_xmldb',
            'missingindexes' => 'tool_xmldb',
            'nomissingindexesfound' => 'tool_xmldb',
            'yesmissingindexesfound' => 'tool_xmldb',
        ));
    }

    protected function check_table(xmldb_table $xmldb_table, array $metacolumns) {
        global $DB;
        $dbman = $DB->get_manager();

        $o = '';
        $missing_indexes = array();

        // Keys
        if ($xmldb_keys = $xmldb_table->getKeys()) {
            $o.='        <ul>';
            foreach ($xmldb_keys as $xmldb_key) {
                $o.='            <li>' . $this->str['key'] . ': ' . $xmldb_key->readableInfo() . ' ';
                // Primaries are skipped
                if ($xmldb_key->getType() == XMLDB_KEY_PRIMARY) {
                    $o.='<font color="green">' . $this->str['ok'] . '</font></li>';
                    continue;
                }
                // If we aren't creating the keys or the key is a XMLDB_KEY_FOREIGN (not underlying index generated
                // automatically by the RDBMS) create the underlying (created by us) index (if doesn't exists)
                if (!$dbman->generator->getKeySQL($xmldb_table, $xmldb_key) || $xmldb_key->getType() == XMLDB_KEY_FOREIGN) {
                    // Create the interim index
                    $xmldb_index = new xmldb_index('anyname');
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
                    // Check if the index exists in DB
                    if ($dbman->index_exists($xmldb_table, $xmldb_index)) {
                        $o.='<font color="green">' . $this->str['ok'] . '</font>';
                    } else {
                        $o.='<font color="red">' . $this->str['missing'] . '</font>';
                        // Add the missing index to the list
                        $obj = new stdClass();
                        $obj->table = $xmldb_table;
                        $obj->index = $xmldb_index;
                        $missing_indexes[] = $obj;
                    }
                }
                $o.='</li>';
            }
            $o.='        </ul>';
        }
        // Indexes
        if ($xmldb_indexes = $xmldb_table->getIndexes()) {
            $o.='        <ul>';
            foreach ($xmldb_indexes as $xmldb_index) {
                $o.='            <li>' . $this->str['index'] . ': ' . $xmldb_index->readableInfo() . ' ';
                // Check if the index exists in DB
                if ($dbman->index_exists($xmldb_table, $xmldb_index)) {
                    $o.='<font color="green">' . $this->str['ok'] . '</font>';
                } else {
                    $o.='<font color="red">' . $this->str['missing'] . '</font>';
                    // Add the missing index to the list
                    $obj = new stdClass();
                    $obj->table = $xmldb_table;
                    $obj->index = $xmldb_index;
                    $missing_indexes[] = $obj;
                }
                $o.='</li>';
            }
            $o.='        </ul>';
        }

        return array($o, $missing_indexes);
    }

    protected function display_results(array $missing_indexes) {
        global $DB;
        $dbman = $DB->get_manager();

        $s = '';
        $r = '<table class="generaltable boxaligncenter boxwidthwide" border="0" cellpadding="5" cellspacing="0" id="results">';
        $r.= '  <tr><td class="generalboxcontent">';
        $r.= '    <h2 class="main">' . $this->str['searchresults'] . '</h2>';
        $r.= '    <p class="centerpara">' . $this->str['missingindexes'] . ': ' . count($missing_indexes) . '</p>';
        $r.= '  </td></tr>';
        $r.= '  <tr><td class="generalboxcontent">';

        // If we have found missing indexes inform about them
        if (count($missing_indexes)) {
            $r.= '    <p class="centerpara">' . $this->str['yesmissingindexesfound'] . '</p>';
            $r.= '        <ul>';
            foreach ($missing_indexes as $obj) {
                $xmldb_table = $obj->table;
                $xmldb_index = $obj->index;
                $sqlarr = $dbman->generator->getAddIndexSQL($xmldb_table, $xmldb_index);
                $r.= '            <li>' . $this->str['table'] . ': ' . $xmldb_table->getName() . '. ' .
                                          $this->str['index'] . ': ' . $xmldb_index->readableInfo() . '</li>';
                $sqlarr = $dbman->generator->getEndedStatements($sqlarr);
                $s.= '<code>' . str_replace("\n", '<br />', implode('<br />', $sqlarr)) . '</code><br />';

            }
            $r.= '        </ul>';
            // Add the SQL statements (all together)
            $r.= '<hr />' . $s;
        } else {
            $r.= '    <p class="centerpara">' . $this->str['nomissingindexesfound'] . '</p>';
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
