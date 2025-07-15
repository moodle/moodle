<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * List all the files needing reconcile because the definitions don't match the XML contents.
 *
 * @package    tool_xmldb
 * @copyright  2022 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reconcile_files extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    public function init() {
        parent::init();

        // Set own custom attributes.
        $this->sesskey_protected = false; // This action doesn't need sesskey protection.

        // Get needed strings.
        $this->loadStrings([
            'backtomainview' => 'tool_xmldb',
            'reconcile_files_intro' => 'tool_xmldb',
            'reconcile_files_no' => 'tool_xmldb',
            'reconcile_files_yes' => 'tool_xmldb',
            'searchresults' => 'tool_xmldb',
        ]);
    }

    /**
     * Invoke method, every class will have its own
     * returns true/false on completion, setting both
     * errormsg and output as necessary
     */
    public function invoke() {
        parent::invoke();

        $result = true;

        // Set own core attributes.
        $this->does_generate = ACTION_GENERATE_HTML;

        // These are always here.
        global $CFG, $XMLDB;

        // Do the job, setting $result as needed.

        // Add link back to home.
        $b = ' <p class="centerpara">';
        $b .= '&nbsp;<a href="index.php?action=main_view#lastused">[' . $this->str['backtomainview'] . ']</a>';
        $b .= '</p>';
        $this->output .= $b;

        $c = '<p class="centerpara">';
        $c .= $this->str['reconcile_files_intro'];
        $c .= '</p>';
        $this->output .= $c;

        // Get the list of DB directories.
        $result = $this->launch('get_db_directories');
        if ($result && !empty($XMLDB->dbdirs)) {
            $needfix = [];
            foreach ($XMLDB->dbdirs as $key => $dbdir) {
                // Verify it exists.
                if (!file_exists($key . '/install.xml') && !is_readable($key . '/install.xml')) {
                    continue;
                }

                // Read the raw contents of the file.
                $rawcontents = file_get_contents($key . '/install.xml');

                // Load the XMLDB file and its structure.
                $xmldb = new xmldb_file($key . '/install.xml');
                $xmldb->loadXMLStructure();

                // Generate the XML contents from the loaded structure.
                $xmlcontents = $xmldb->getStructure()->xmlOutput();
                $correctdom = new \DOMDocument();
                $correctdom->loadXML($xmlcontents);
                $correct = $correctdom->saveXML();

                $currentdom = new \DOMDocument();
                $currentdom->loadXML($rawcontents);
                $current = $currentdom->saveXML();

                if ($current !== $correct) {
                    $relpath = str_replace($CFG->dirroot . '/', '', $key) . '/install.xml';
                    $needfix[] = $relpath;
                    // Left here on purpose, as a quick way to fix problems. To be
                    // enabled and run by developers only, uncomment the next line:
                    // file_put_contents($key . '/install.xml', $xmlcontents);
                    // (this script won't ever do that officially).
                }
            }
        }

        $r = '<h3 class="main">' . $this->str['searchresults'] . '</h3>';
        if ($needfix) {
            $r .= '<h4 class="main">' . $this->str['reconcile_files_yes'] . count($needfix) . '</h4>';
            $r .= '<ul><li>' . implode('</li><li>', $needfix) . '</li></ul>';

        } else {
            $r .= '<h4 class="main">' . $this->str['reconcile_files_no'] . '</h4>';
        }

        // Set the output.
        $this->output .= $r;

        // Launch postaction if exists (leave this unmodified).
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

        return $result;
    }
}
