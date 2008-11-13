<?php  // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
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

/**
 * Code to search for users in response to an ajax call from a user selector.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package userselector
 *//** */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/user/selector/lib.php');

// In developer debug mode, when there is a debug=1 in the URL send as plain text
// for easier debugging.
if (debugging('', DEBUG_DEVELOPER) && optional_param('debug', false, PARAM_BOOL)) {
    header('Content-type: text/plain; charset=UTF-8');
    $debugmode = true;
} else {
    header('Content-type: application/json');
    $debugmode = false;
}

// Check access.
if (!isloggedin()) {;
    print_error('mustbeloggedin');
}
if (!confirm_sesskey()) {
    print_error('invalidsesskey');
}

// Get the search parameter.
$search = required_param('search', PARAM_RAW);

// Get and validate the selectorid parameter.
$selectorhash = required_param('selectorid', PARAM_ALPHANUM);
if (!isset($USER->userselectors[$selectorhash])) {
    print_error('unknownuserselector');
}

// Get the options.
$options = $USER->userselectors[$selectorhash];

if ($debugmode) {
    echo 'Search string: ', $search, "\n";
    echo 'Options: ';
    print_r($options);
    echo "\n";
}

// Create the appropriate userselector.
$classname = $options['class'];
unset($options['class']);
$name = $options['name'];
unset($options['name']);
if (isset($options['file'])) {
    require_once($CFG->dirroot . '/' . $options['file']);
    unset($options['file']);
}
$userselector = new $classname($name, $options);

// Do the search and output the results.
$users = $userselector->find_users($search);
foreach ($users as &$group) {
    foreach ($group as $user) {
        $output = new stdClass;
        $output->id = $user->id;
        $output->name = $userselector->output_user($user);
        if (!empty($user->disabled)) {
            $output->disabled = true;
        }
        $group[$user->id] = $output;
    }
}

echo json_encode(array('results' => $users));
?>