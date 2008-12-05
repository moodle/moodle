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
 * Allows a user to request a course be created for them.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package course
 *//** */

    require_once(dirname(__FILE__) . '/../config.php');
    require_once($CFG->dirroot . '/course/request_form.php');

/// Where we came from. Used in a number of redirects.
    $returnurl = $CFG->wwwroot . '/course/index.php';

/// Check permissions.
    require_login();
    if (isguestuser()) {
        print_error('guestsarenotallowed', '', $returnurl);
    }
    if (empty($CFG->enablecourserequests)) {
        print_error('courserequestdisabled', '', $returnurl);
    }

/// Set up the form.
    $requestform = new course_request_form($CFG->wwwroot . '/course/request.php');

/// Standard form processing if statement.
    if ($requestform->is_cancelled()){
        redirect($returnurl);

    } else if ($data = $requestform->get_data()) {
    /// Record the request.
        $data->requester = $USER->id;
        if (!$DB->insert_record('course_request', $data)) {
            print_error('errorsavingrequest', '', $returnurl);
        }

    /// and redirect back to the course listing.
        notice(get_string('courserequestsuccess'), $returnurl);
    }

/// Show the request form.
    $strtitle = get_string('courserequest');
    print_header($strtitle, $strtitle, build_navigation($strtitle), $requestform->focus());
    print_heading($strtitle);
    $requestform->display();
    print_footer();

?>