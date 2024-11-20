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
 * This is the main script for the complete XMLDB interface. From here
 * all the actions supported will be launched.
 *
 * @package    tool_xmldb
 * @copyright  (C) 1999 onwards Martin Dougiamas http://dougiamas.com,
 *             (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/ddllib.php');
// Add required XMLDB action classes
require_once('actions/XMLDBAction.class.php');
require_once('actions/XMLDBCheckAction.class.php');


admin_externalpage_setup('toolxmld');

// Add other used libraries
require_once($CFG->libdir . '/xmlize.php');

// Handle session data
global $XMLDB;

// State is stored in session - we have to serialise it because the classes are not loaded when creating session
if (!isset($SESSION->xmldb)) {
    $XMLDB = new stdClass;
} else {
    $XMLDB = unserialize($SESSION->xmldb);
}

// Some previous checks
$site = get_site();


// Body of the script, based on action, we delegate the work
$action = optional_param ('action', 'main_view', PARAM_ALPHAEXT);

// Get the action path and invoke it
$actionsroot = "$CFG->dirroot/$CFG->admin/tool/xmldb/actions";
$actionclass = $action . '.class.php';
$actionpath = "$actionsroot/$action/$actionclass";

// Load and invoke the proper action
if (file_exists($actionpath) && is_readable($actionpath)) {
    require_once($actionpath);
    if ($xmldb_action = new $action) {
        // Invoke it
        $result = $xmldb_action->invoke();
        // store the result in session
        $SESSION->xmldb = serialize($XMLDB);

        if ($result) {
            // Based on getDoesGenerate()
            switch ($xmldb_action->getDoesGenerate()) {
                case ACTION_GENERATE_HTML:

                    $action = optional_param('action', '', PARAM_ALPHAEXT);
                    $postaction = optional_param('postaction', '', PARAM_ALPHAEXT);
                    // If the js exists, load it
                    if ($action) {
                        $script = $CFG->admin . '/tool/xmldb/actions/' . $action . '/' . $action . '.js';
                        $file = $CFG->dirroot . '/' . $script;
                        if (file_exists($file) && is_readable($file)) {
                            $PAGE->requires->js('/'.$script);
                        } else if ($postaction) {
                            // Try to load the postaction javascript if exists
                            $script = $CFG->admin . '/tool/xmldb/actions/' . $postaction . '/' . $postaction . '.js';
                            $file = $CFG->dirroot . '/' . $script;
                            if (file_exists($file) && is_readable($file)) {
                                $PAGE->requires->js('/'.$script);
                            }
                        }
                    }

                    // Go with standard admin header
                    echo $OUTPUT->header();
                    echo $OUTPUT->heading($xmldb_action->getTitle());
                    echo $xmldb_action->getOutput();
                    echo $OUTPUT->footer();
                    break;
                case ACTION_GENERATE_XML:
                    header('Content-type: application/xhtml+xml; charset=utf-8');
                    echo $xmldb_action->getOutput();
                    break;
            }
        } else {
            // TODO: need more detailed error info
            throw new \moodle_exception('xmldberror');
        }
    } else {
        $a = new stdClass();
        $a->action = $action;
        $a->actionclass = $actionclass;
        throw new \moodle_exception('cannotinstantiateclass', 'tool_xmldb', '', $a);
    }
} else {
    throw new \moodle_exception('invalidaction');
}

if ($xmldb_action->getDoesGenerate() != ACTION_GENERATE_XML) {
    if (debugging()) {
        // print_object($XMLDB);
    }
}
