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
 * This class will look for data in the database that violates the foreign
 * key definitions found in the XMLDB definitions.
 *
 * Note that by default, this check does not complain about foreign key
 * violations from, say, a userid column defined as NOT NULL DEFAULT '0'.
 * Each 0 in that column will violate the foreign key, but we ignore them.
 * If you want a strict check performed, then add &strict=1 to the URL.
 *
 * @package   xmldb-editor
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check_foreign_keys extends XMLDBCheckAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        $this->introstr = 'confirmcheckforeignkeys';
        parent::init();

    /// Set own core attributes

    /// Set own custom attributes

    /// Get needed strings
        $this->loadStrings(array(
            'key' => 'xmldb',
            'violatedforeignkeys' => 'xmldb',
            'noviolatedforeignkeysfound' => 'xmldb',
            'violatedforeignkeysfound' => 'xmldb',
            'violations' => 'xmldb',
        ));
    }

    protected function check_table(xmldb_table $xmldb_table, array $metacolumns) {
        global $DB;
        $dbman = $DB->get_manager();

        $strictchecks = optional_param('strict', false, PARAM_BOOL);

        $o = '';
        $violatedkeys = array();

        /// Keys
        if ($xmldb_keys = $xmldb_table->getKeys()) {
            $o.='        <ul>';
            foreach ($xmldb_keys as $xmldb_key) {
            /// We are only interested in foreign keys.
                if (!in_array($xmldb_key->getType(), array(XMLDB_KEY_FOREIGN, XMLDB_KEY_FOREIGN_UNIQUE))) {
                    continue;
                }
                $o.='            <li>' . $this->str['key'] . ': ' . $xmldb_key->readableInfo() . ' ';

            /// Work out the SQL to find key violations.
                $keyfields = $xmldb_key->getFields();
                $reffields = $xmldb_key->getRefFields();
                $joinconditions = array();
                $nullnessconditions = array();
                $params = array();
                foreach ($keyfields as $i => $field) {
                    $joinconditions[] = 't1.' . $field . ' = t2.' . $reffields[$i];
                    $xmldb_field = $xmldb_table->getField($field);
                    $default = $xmldb_field->getDefault();
                    if (!$xmldb_field->getNotNull()) {
                        $nullnessconditions[] = 't1.' . $field . ' IS NOT NULL';
                    } else if (!$strictchecks && ($default == '0' || !$default)) {
                        // We have a default of 0 or '' or something like that.
                        // These generate a lot of false-positives, so ignore them
                        // for now.
                        $nullnessconditions[] = 't1.' . $field . ' <> ?';
                        $params[] = $xmldb_field->getDefault();
                    }
                }
                $nullnessconditions[] = 't2.id IS NULL';
                $sql = 'SELECT count(1) FROM {' . $xmldb_table->getName() .
                        '} t1 LEFT JOIN {' . $xmldb_key->getRefTable() . '} t2 ON ' .
                        implode(' AND ', $joinconditions) . ' WHERE ' .
                        implode(' AND ', $nullnessconditions);

            /// Check there are any problems in the database.
                $violations = $DB->count_records_sql($sql, $params);
                if ($violations == 0) {
                    $o.='<font color="green">' . $this->str['ok'] . '</font>';
                } else {
                    $o.='<font color="red">' . $this->str['violations'] . '</font>';
                /// Add the missing index to the list
                    $violation = new stdClass;
                    $violation->table = $xmldb_table;
                    $violation->key = $xmldb_key;
                    $violation->numviolations = $violations;
                    $violation->numrows = $DB->count_records($xmldb_table->getName());
                    $violation->sql = str_replace('count(1)', '*', $sql);
                    if (!empty($params)) {
                        $violation->sqlparams = '(' . implode(', ', $params) . ')';
                    } else {
                        $violation->sqlparams = '';
                    }
                    $violatedkeys[] = $violation;
                }
                $o.='</li>';
            }
            $o.='        </ul>';
        }

        return array($o, $violatedkeys);
    }

    protected function display_results(array $violatedkeys) {
        $r = '<table class="generalbox boxaligncenter boxwidthwide" border="0" cellpadding="5" cellspacing="0" id="results">';
        $r.= '  <tr><td class="generalboxcontent">';
        $r.= '    <h2 class="main">' . $this->str['searchresults'] . '</h2>';
        $r.= '    <p class="centerpara">' . $this->str['violatedforeignkeys'] . ': ' . count($violatedkeys) . '</p>';
        $r.= '  </td></tr>';
        $r.= '  <tr><td class="generalboxcontent">';

    /// If we have found wrong integers inform about them
        if (count($violatedkeys)) {
            $r.= '    <p class="centerpara">' . $this->str['violatedforeignkeysfound'] . '</p>';
            $r.= '        <ul>';
            foreach ($violatedkeys as $violation) {
                $violation->tablename = $violation->table->getName();
                $violation->keyname = $violation->key->getName();

                $r.= '            <li>' .get_string('fkviolationdetails', 'xmldb', $violation) .
                        '<pre>' . s($violation->sql) . '; ' . s($violation->sqlparams) . '</pre></li>';
            }
            $r.= '        </ul>';
        } else {
            $r.= '    <p class="centerpara">' . $this->str['noviolatedforeignkeysfound'] . '</p>';
        }
        $r.= '  </td></tr>';
        $r.= '  <tr><td class="generalboxcontent">';
    /// Add the complete log message
        $r.= '    <p class="centerpara">' . $this->str['completelogbelow'] . '</p>';
        $r.= '  </td></tr>';
        $r.= '</table>';

        return $r;
    }
}
