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
            'extraindexesfound' => 'tool_xmldb',
            'missing' => 'tool_xmldb',
            'key' => 'tool_xmldb',
            'index' => 'tool_xmldb',
            'missingindexes' => 'tool_xmldb',
            'nomissingorextraindexesfound' => 'tool_xmldb',
            'yesextraindexesfound' => 'tool_xmldb',
            'yesmissingindexesfound' => 'tool_xmldb',
        ));
    }

    protected function check_table(xmldb_table $xmldb_table, array $metacolumns) {
        global $DB;
        $dbman = $DB->get_manager();

        $o = '';
        $dbindexes = $DB->get_indexes($xmldb_table->getName());
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
                        $this->remove_index_from_dbindex($dbindexes, $xmldb_index);
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
                    $this->remove_index_from_dbindex($dbindexes, $xmldb_index);
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

        // Hack - skip for table 'search_simpledb_index' as this plugin adds indexes dynamically on install
        // which are not included in install.xml. See search/engine/simpledb/db/install.php.
        if ($xmldb_table->getName() != 'search_simpledb_index') {
            foreach ($dbindexes as $indexname => $index) {
                $missing_indexes[] = $indexname;
            }
        }

        return array($o, $missing_indexes);
    }

    protected function display_results(array $missing_indexes) {
        global $DB;
        $dbman = $DB->get_manager();

        $missingindexes = [];
        $extraindexes = [];

        foreach ($missing_indexes as $missingindex) {
            if (is_object($missingindex)) {
                $missingindexes[] = $missingindex;
            } else {
                $extraindexes[] = $missingindex;
            }
        }

        $s = '';
        $r = '<table class="table generaltable table-hover" border="0" cellpadding="5" cellspacing="0" id="results">';
        $r.= '  <tr><td class="generalboxcontent">';
        $r.= '    <h2 class="main">' . $this->str['searchresults'] . '</h2>';
        $r .= '    <p class="centerpara">' . $this->str['missingindexes'] . ': ' . count($missingindexes) . '</p>';
        $r .= '    <p class="centerpara">' . $this->str['extraindexesfound'] . ': ' . count($extraindexes) . '</p>';
        $r.= '  </td></tr>';
        $r.= '  <tr><td class="generalboxcontent">';

        // If we have found missing indexes or extra indexes inform the user about them.
        if (!empty($missingindexes) || !empty($extraindexes)) {
            if ($missingindexes) {
                $r.= '    <p class="centerpara">' . $this->str['yesmissingindexesfound'] . '</p>';
                $r.= '        <ul>';
                foreach ($missingindexes as $obj) {
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
            }
            if ($extraindexes) {
                $r .= '<p class="centerpara">' . $this->str['yesextraindexesfound'] . '</p>';
                $r .= '<ul>';
                foreach ($extraindexes as $ei) {
                    $r .= '<li>' . $ei . '</li>';
                }
                $r .= '</ul>';
                $r .= '<hr />';
            }
        } else {
            $r .= '<p class="centerpara">' . $this->str['nomissingorextraindexesfound'] . '</p>';
        }
        $r.= '  </td></tr>';
        $r.= '  <tr><td class="generalboxcontent">';
        // Add the complete log message
        $r.= '    <p class="centerpara">' . $this->str['completelogbelow'] . '</p>';
        $r.= '  </td></tr>';
        $r.= '</table>';

        return $r;
    }

    /**
     * Removes an index from the array $dbindexes if it is found.
     *
     * @param array $dbindexes
     * @param xmldb_index $index
     */
    private function remove_index_from_dbindex(array &$dbindexes, xmldb_index $index) {
        foreach ($dbindexes as $key => $dbindex) {
            if ($dbindex['columns'] == $index->getFields()) {
                unset($dbindexes[$key]);
            }
        }
    }
}
