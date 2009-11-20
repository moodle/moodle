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
 * @copyright (C) 2001-3001 Eloy Lafuente (stronk7) {@link http://contiento.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This class will produce the documentation for all the XMLDB files in the server,
 * via XSL, performing the output in HTML format.
 *
 * @package   xmldb-editor
 * @copyright (C) 2001-3001 Eloy Lafuente (stronk7) {@link http://contiento.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generate_all_documentation extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own custom attributes
        $this->sesskey_protected = false; // This action doesn't need sesskey protection

    /// Get needed strings
        $this->loadStrings(array(
            'backtomainview' => 'xmldb',
            'documentationintro' => 'xmldb',
            'docindex' => 'xmldb'
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
        global $CFG, $XMLDB;

    /// Do the job, setting $result as needed

    /// Add link back to home
        $b = ' <p class="centerpara buttons">';
        $b .= '&nbsp;<a href="index.php?action=main_view#lastused">[' . $this->str['backtomainview'] . ']</a>';
        $b .= '</p>';
        $this->output=$b;

        $c = ' <p class="centerpara">';
        $c .= $this->str['documentationintro'];
        $c .= '</p>';
        $this->output.=$c;

        $this->docs = '';

        if(class_exists('XSLTProcessor')) {

            $doc = new DOMDocument();
            $xsl = new XSLTProcessor();

            $doc->load(dirname(__FILE__).'/../generate_documentation/xmldb.xsl');
            $xsl->importStyleSheet($doc);

            $dbdirs = get_db_directories();
            sort($dbdirs);
            $index = $this->str['docindex'] . ' ';
            foreach ($dbdirs as $path) {

                if (!file_exists($path . '/install.xml')) {
                    continue;
                }

                $dir = trim(dirname(str_replace($CFG->dirroot, '', $path)), '/');
                $index .= '<a href="#file_' . str_replace('/', '_', $dir) . '">' . $dir . '</a>, ';
                $this->docs .= '<div class="file" id="file_' . str_replace('/', '_', $dir) . '">';
                $this->docs .= '<h2>' . $dir . '</h2>';

                $doc->load($path . '/install.xml');
                $this->docs.=$xsl->transformToXML($doc);

                $this->docs .= '</div>';
            }

            $this->output .= '<div id="file_idex">' . trim($index, ' ,') . '</div>' . $this->docs;

            $this->output.=$b;
        } else {
            $this->output.=get_string('extensionrequired','xmldb','xsl');
        }

    /// Launch postaction if exists (leave this unmodified)
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

        return $result;
    }
}

