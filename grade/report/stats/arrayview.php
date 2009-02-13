<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
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
 * Displays data from an array in the report/stats plugin.
 * Array's data is encoded in the data param using the
 * grade_report_stats::encode_array function.
 * TODO: Improve look.
 * @package gradebook
 */

require_once '../../../config.php';

$rawdata = required_param('data');
$courseid = required_param('id');

$data = explode('"', stripslashes(base64_decode(strtr($rawdata, '-_,', '+/='))));
$context = get_context_instance(CONTEXT_COURSE, $courseid);

require_login($courseid);
require_capability('gradereport/stats:view', $context);

foreach($data as $stat) {
    echo format_text($stat, FORMAT_HTML) . '<br/>';
}
?>
