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

defined('MOODLE_INTERNAL') || die();

/**
 * This class will check all the default values existing in the DB
 * match those specified in the xml specs
 * and providing one SQL script to fix all them.
 *
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check_defaults extends XMLDBCheckAction {

    /**
     * Init method, every subclass will have its own
     */
    public function init() {
        $this->introstr = 'confirmcheckdefaults';
        parent::init();

        // Set own core attributes.

        // Set own custom attributes.

        // Get needed strings.
        $this->loadStrings(array(
            'wrongdefaults' => 'tool_xmldb',
            'nowrongdefaultsfound' => 'tool_xmldb',
            'yeswrongdefaultsfound' => 'tool_xmldb',
            'expected' => 'tool_xmldb',
            'actual' => 'tool_xmldb',
        ));
    }

    protected function check_table(xmldb_table $xmldbtable, array $metacolumns) {
        $o = '';
        $wrongfields = array();

        // Get and process XMLDB fields.
        if ($xmldbfields = $xmldbtable->getFields()) {
            $o .= '        <ul>';
            foreach ($xmldbfields as $xmldbfield) {

                // Get the default value for the field.
                $xmldbdefault = $xmldbfield->getDefault();

                // Char fields with not null currently have default '' when actually installed.
                if ($xmldbdefault === null && $xmldbfield->getType() === XMLDB_TYPE_CHAR &&
                        $xmldbfield->getNotNull()) {
                    $xmldbdefault = '';
                }
                if ($xmldbdefault !== null) {
                    $xmldbdefault = (string)$xmldbdefault;
                }

                // If the metadata for that column doesn't exist or 'id' field found, skip.
                if (!isset($metacolumns[$xmldbfield->getName()]) or $xmldbfield->getName() == 'id') {
                    continue;
                }

                // To variable for better handling.
                $metacolumn = $metacolumns[$xmldbfield->getName()];

                // Going to check this field in DB.
                $o .= '            <li>' . $this->str['field'] . ': ' . $xmldbfield->getName() . ' ';

                // Get the value of the physical default (or blank if there isn't one).
                if ($metacolumn->has_default == 1) {
                    $physicaldefault = $metacolumn->default_value;
                } else {
                    $physicaldefault = null;
                }

                // For number fields there are issues with type differences, so let's convert
                // everything to a float.
                if ($xmldbfield->getType() === XMLDB_TYPE_NUMBER) {
                    if ($physicaldefault !== null) {
                        $physicaldefault = (float) $physicaldefault;
                    }
                    if ($xmldbdefault !== null) {
                        $xmldbdefault = (float) $xmldbdefault;
                    }
                }

                // There *is* a default and it's wrong.
                if ($physicaldefault !== $xmldbdefault) {
                    $xmldbtext = self::display_default($xmldbdefault);
                    $physicaltext = self::display_default($physicaldefault);
                    $info = "({$this->str['expected']} {$xmldbtext}, {$this->str['actual']} {$physicaltext})";
                    $o .= '<font color="red">' . $this->str['wrong'] . " $info</font>";
                    // Add the wrong field to the list.
                    $obj = new stdClass();
                    $obj->table = $xmldbtable;
                    $obj->field = $xmldbfield;
                    $obj->physicaldefault = $physicaldefault;
                    $obj->xmldbdefault = $xmldbdefault;
                    $wrongfields[] = $obj;
                } else {
                    $o .= '<font color="green">' . $this->str['ok'] . '</font>';
                }
                $o .= '</li>';
            }
            $o .= '        </ul>';
        }

        return array($o, $wrongfields);
    }

    /**
     * Converts a default value suitable for display.
     *
     * @param string|null $defaultvalue Default value
     * @return string Displayed version
     */
    protected static function display_default($defaultvalue) {
        if ($defaultvalue === null) {
            return '-';
        } else {
            return "'" . s($defaultvalue) . "'";
        }
    }

    protected function display_results(array $wrongfields) {
        global $DB;
        $dbman = $DB->get_manager();

        $s = '';
        $r = '<table class="generaltable boxaligncenter boxwidthwide" border="0" cellpadding="5" cellspacing="0" id="results">';
        $r .= '  <tr><td class="generalboxcontent">';
        $r .= '    <h2 class="main">' . $this->str['searchresults'] . '</h2>';
        $r .= '    <p class="centerpara">' . $this->str['wrongdefaults'] . ': ' . count($wrongfields) . '</p>';
        $r .= '  </td></tr>';
        $r .= '  <tr><td class="generalboxcontent">';

        // If we have found wrong defaults inform about them.
        if (count($wrongfields)) {
            $r .= '    <p class="centerpara">' . $this->str['yeswrongdefaultsfound'] . '</p>';
            $r .= '        <ul>';
            foreach ($wrongfields as $obj) {
                $xmldbtable = $obj->table;
                $xmldbfield = $obj->field;
                $physicaltext = self::display_default($obj->physicaldefault);
                $xmldbtext = self::display_default($obj->xmldbdefault);

                // Get the alter table command.
                $sqlarr = $dbman->generator->getAlterFieldSQL($xmldbtable, $xmldbfield);

                $r .= '            <li>' . $this->str['table'] . ': ' . $xmldbtable->getName() . '. ' .
                        $this->str['field'] . ': ' . $xmldbfield->getName() . ', ' .
                        $this->str['expected'] . ' ' . $xmldbtext . ' ' .
                        $this->str['actual'] . ' ' . $physicaltext . '</li>';
                // Add to output if we have sentences.
                if ($sqlarr) {
                    $sqlarr = $dbman->generator->getEndedStatements($sqlarr);
                    $s .= '<code>' . str_replace("\n", '<br />', implode('<br />', $sqlarr)) . '</code><br />';
                }
            }
            $r .= '        </ul>';
            // Add the SQL statements (all together).
            $r .= '<hr />' . $s;
        } else {
            $r .= '    <p class="centerpara">' . $this->str['nowrongdefaultsfound'] . '</p>';
        }
        $r .= '  </td></tr>';
        $r .= '  <tr><td class="generalboxcontent">';
        // Add the complete log message.
        $r .= '    <p class="centerpara">' . $this->str['completelogbelow'] . '</p>';
        $r .= '  </td></tr>';
        $r .= '</table>';

        return $r;
    }
}
