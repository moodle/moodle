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
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class create_xml_file extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();
        // Set own core attributes
        $this->can_subaction = ACTION_NONE;
        //$this->can_subaction = ACTION_HAVE_SUBACTIONS;

        // Set own custom attributes

        // Get needed strings
        $this->loadStrings(array(
            // 'key' => 'module',
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
        $this->does_generate = ACTION_NONE;
        //$this->does_generate = ACTION_GENERATE_HTML;

        // These are always here
        global $CFG, $XMLDB;

        // Do the job, setting result as needed

        // Get the dir containing the file
        $dirpath = required_param('dir', PARAM_PATH);
        $plugintype = $this->get_plugin_type($dirpath);
        $dirpath = $CFG->dirroot . $dirpath;
        $file = $dirpath . '/install.xml';

        // Some variables
        $xmlpath = dirname(str_replace($CFG->dirroot . '/', '', $file));
        $xmlversion = userdate(time(), '%Y%m%d', 99, false);
        $xmlcomment = 'XMLDB file for Moodle ' . dirname($xmlpath);

        $xmltable = strtolower(basename(dirname($xmlpath)));
        if ($plugintype && $plugintype != 'mod') {
            $xmltable = $plugintype.'_'.$xmltable;
        }

        // Initial contents
        $c = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
        $c.= '  <XMLDB PATH="' . $xmlpath . '" VERSION="' . $xmlversion .'" COMMENT="' . $xmlcomment .'">' . "\n";
        $c.= '    <TABLES>' . "\n";
        $c.= '      <TABLE NAME="' . $xmltable . '" COMMENT="Default comment for ' . $xmltable .', please edit me">' . "\n";
        $c.= '        <FIELDS>' . "\n";
        $c.= '          <FIELD NAME="id" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="true" />' . "\n";
        $c.= '        </FIELDS>' . "\n";
        $c.= '        <KEYS>' . "\n";
        $c.= '          <KEY NAME="primary" TYPE="primary" FIELDS="id" />' . "\n";
        $c.= '        </KEYS>' . "\n";
        $c.= '      </TABLE>' . "\n";
        $c.= '    </TABLES>' . "\n";
        $c.= '  </XMLDB>';

        if (!file_put_contents($file, $c)) {
            $errormsg = 'Error creando fichero ' . $file;
            $result = false;
        }

        // Launch postaction if exists
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

        // Return ok if arrived here
        return $result;
    }

    /**
     * From a given path, work out what type of plugin
     * this belongs to
     * @param string $dirpath Path to the db file for this plugin
     * @return string the type of the plugin or null if not found
     */
    function get_plugin_type($dirpath) {
        global $CFG;
        $dirpath = $CFG->dirroot.$dirpath;
        // Reverse order so that we get subplugin matches.
        $plugintypes = array_reverse(get_plugin_types());
        foreach ($plugintypes as $plugintype => $pluginbasedir) {
            if (substr($dirpath, 0, strlen($pluginbasedir)) == $pluginbasedir) {
                return $plugintype;
            }
        }
        return null;
    }
}

