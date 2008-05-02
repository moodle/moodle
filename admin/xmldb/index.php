<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// This is the main script for the complete XMLDB interface. From here
/// all the actions supported will be launched.

/// Add required XMLDB constants
    require_once('../../lib/xmldb/classes/XMLDBConstants.php');

/// Add required XMLDB action classes
    require_once('actions/XMLDBAction.class.php');

/// Add main XMLDB Generator
    require_once('../../lib/xmldb/classes/generators/XMLDBGenerator.class.php');

/// Add required XMLDB DB classes
    require_once('../../lib/xmldb/classes/XMLDBObject.class.php');
    require_once('../../lib/xmldb/classes/XMLDBFile.class.php');
    require_once('../../lib/xmldb/classes/XMLDBStructure.class.php');
    require_once('../../lib/xmldb/classes/XMLDBTable.class.php');
    require_once('../../lib/xmldb/classes/XMLDBField.class.php');
    require_once('../../lib/xmldb/classes/XMLDBKey.class.php');
    require_once('../../lib/xmldb/classes/XMLDBIndex.class.php');
    require_once('../../lib/xmldb/classes/XMLDBStatement.class.php');

/// Add Moodle config script (this is loaded AFTER all the rest
/// of classes because it starts the SESSION and classes to be
/// stored there MUST be declared before in order to avoid
/// getting "incomplete" objects
    require_once('../../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/ddllib.php'); // Install/upgrade related db functions

    admin_externalpage_setup('xmldbeditor');

/// Add other used libraries
    require_once($CFG->libdir . '/xmlize.php');

/// Add all the available SQL generators
    $generators = get_list_of_plugins('lib/xmldb/classes/generators');
    foreach($generators as $generator) {
        require_once ('../../lib/xmldb/classes/generators/' . $generator . '/' . $generator . '.class.php');
    }

/// Handle session data
    global $XMLDB;
/// The global SESSION object where everything will happen
    if (!isset($SESSION->xmldb)) {
        $SESSION->xmldb = new stdClass;
    }
    $XMLDB =& $SESSION->xmldb;

/// Some previous checks
    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

    require_login();
    require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

/// Body of the script, based on action, we delegate the work
    $action = optional_param ('action', 'main_view', PARAM_ALPHAEXT);

/// Get the action path and invoke it
    $actionsroot = "$CFG->dirroot/$CFG->admin/xmldb/actions";
    $actionclass = $action . '.class.php';
    $actionpath = "$actionsroot/$action/$actionclass";

/// Load and invoke the proper action
    if (file_exists($actionpath) && is_readable($actionpath)) {
        require_once($actionpath);
        if ($xmldb_action = new $action) {
            //Invoke it
            $result = $xmldb_action->invoke();
            if ($result) {
            /// Based on getDoesGenerate()
                switch ($xmldb_action->getDoesGenerate()) {
                    case ACTION_GENERATE_HTML:
                    /// Define $CFG->javascript to use our custom javascripts.
                    /// Save the original one to add it from ours. Global too! :-(
                        global $standard_javascript;
                        $standard_javascript = $CFG->javascript;  // Save original javascript file
                        $CFG->javascript = $CFG->dirroot.'/'.$CFG->admin.'/xmldb/javascript.php';  //Use our custom javascript code
                    /// Go with standard admin header
                        admin_externalpage_print_header();
                        print_heading($xmldb_action->getTitle());
                        echo $xmldb_action->getOutput();
                        admin_externalpage_print_footer();
                        break;
                    case ACTION_GENERATE_XML:
                        header('Content-type: application/xhtml+xml');
                        echo $xmldb_action->getOutput();
                        break;
                }
            } else {
                error($xmldb_action->getError());
            }
        } else {
            error ("Error: cannot instantiate class (actions/$action/$actionclass)");
        }
    } else {
        error ("Error: wrong action specified ($action)");
    }

    if ($xmldb_action->getDoesGenerate() != ACTION_GENERATE_XML) {
        if (debugging()) {
            ///print_object($XMLDB);
        }
    }

?>
