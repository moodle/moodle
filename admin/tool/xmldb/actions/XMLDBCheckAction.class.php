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
 * @copyright  2008 onwards Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This is a base class for the various actions that interate over all the
 * tables and check some aspect of their definition.
 *
 * @package    tool_xmldb
 * @copyright  2008 onwards Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class XMLDBCheckAction extends XMLDBAction {
    /**
     * @var string This string is displayed with a yes/no choice before the report is run.
     * You must set this to the name of a lang string in xmldb.php before calling init.
     */
    protected $introstr = '';

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

        // Set own core attributes

        // Set own custom attributes

        // Get needed strings
        $this->loadStrings(array(
            $this->introstr => 'tool_xmldb',
            'ok' => '',
            'wrong' => 'tool_xmldb',
            'table' => 'tool_xmldb',
            'field' => 'tool_xmldb',
            'searchresults' => 'tool_xmldb',
            'completelogbelow' => 'tool_xmldb',
            'yes' => '',
            'no' => '',
            'error' => '',
            'back' => 'tool_xmldb'
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

        // Set own core attributes
        $this->does_generate = ACTION_GENERATE_HTML;

        // These are always here
        global $CFG, $XMLDB, $DB, $OUTPUT;

        // And we nedd some ddl suff
        $dbman = $DB->get_manager();

        // Here we'll acummulate all the wrong fields found
        $problemsfound = array();

        // Do the job, setting $result as needed

        // Get the confirmed to decide what to do
        $confirmed = optional_param('confirmed', false, PARAM_BOOL);

        // If  not confirmed, show confirmation box
        if (!$confirmed) {
            $o = '<table class="generalbox" border="0" cellpadding="5" cellspacing="0" id="notice">';
            $o.= '  <tr><td class="generalboxcontent">';
            $o.= '    <p class="centerpara">' . $this->str[$this->introstr] . '</p>';
            $o.= '    <table class="boxaligncenter" cellpadding="20"><tr><td>';
            $o.= '      <div class="singlebutton">';
            $o.= '        <form action="index.php?action=' . $this->title . '&amp;confirmed=yes&amp;sesskey=' . sesskey() . '" method="post"><fieldset class="invisiblefieldset">';
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
            // The back to edit table button
            $b = ' <p class="centerpara buttons">';
            $b .= '<a href="index.php">[' . $this->str['back'] . ']</a>';
            $b .= '</p>';

            // Iterate over $XMLDB->dbdirs, loading their XML data to memory
            if ($XMLDB->dbdirs) {
                $dbdirs = $XMLDB->dbdirs;
                $o='<ul>';
                foreach ($dbdirs as $dbdir) {
                    // Only if the directory exists
                    if (!$dbdir->path_exists) {
                        continue;
                    }
                    // Load the XML file
                    $xmldb_file = new xmldb_file($dbdir->path . '/install.xml');

                    // Only if the file exists
                    if (!$xmldb_file->fileExists()) {
                        continue;
                    }
                    // Load the XML contents to structure
                    $loaded = $xmldb_file->loadXMLStructure();
                    if (!$loaded || !$xmldb_file->isLoaded()) {
                        echo $OUTPUT->notification('Errors found in XMLDB file: '. $dbdir->path . '/install.xml');
                        continue;
                    }
                    // Arriving here, everything is ok, get the XMLDB structure
                    $structure = $xmldb_file->getStructure();

                    $o.='    <li>' . str_replace($CFG->dirroot . '/', '', $dbdir->path . '/install.xml');
                    // Getting tables
                    if ($xmldb_tables = $structure->getTables()) {
                        $o.='        <ul>';
                        // Foreach table, process its fields
                        foreach ($xmldb_tables as $xmldb_table) {
                            // Skip table if not exists
                            if (!$dbman->table_exists($xmldb_table)) {
                                continue;
                            }
                            // Fetch metadata from physical DB. All the columns info.
                            if (!$metacolumns = $DB->get_columns($xmldb_table->getName())) {
                                // / Skip table if no metacolumns is available for it
                                continue;
                            }
                            // Table processing starts here
                            $o.='            <li>' . $xmldb_table->getName();
                            // Do the specific check.
                            list($output, $newproblems) = $this->check_table($xmldb_table, $metacolumns);
                            $o.=$output;
                            $problemsfound = array_merge($problemsfound, $newproblems);
                            $o.='    </li>';
                            // Give the script some more time (resetting to current if exists)
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

            // Create a report of the problems found.
            $r = $this->display_results($problemsfound);

            // Combine the various bits of output.
            $this->output = $b . $r . $o;
        }

        // Launch postaction if exists (leave this here!)
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

        // Return ok if arrived here
        return $result;
    }

    /**
     * Do the checks necessary on one particular table.
     *
     * @param xmldb_table $xmldb_table the table definition from the install.xml file.
     * @param array $metacolumns the column information read from the database.
     * @return array an array with two elements: First, some additional progress output,
     *      for example a list (<ul>) of the things check each with an one work ok/not ok summary.
     *      Second, an array giving the details of any problems found. These arrays
     *      for all tables will be aggregated, and then passed to
     */
    abstract protected function check_table(xmldb_table $xmldb_table, array $metacolumns);

    /**
     * Display a list of the problems found.
     *
     * @param array $problems_found an aggregation of all the problems found by
     *      all the check_table calls.
     * @return string a display of all the problems found as HTML.
     */
    abstract protected function display_results(array $problems_found);
}
