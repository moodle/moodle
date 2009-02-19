<?php  //$Id$

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

require_once('../../../config.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/report/security/lib.php');
require_once($CFG->libdir.'/adminlib.php');

require_login();

$issue = optional_param('issue', '', PARAM_FILE); // show detailed info about one issue only

$issues = report_security_get_issue_list();

// test if issue valid string
if (array_search($issue, $issues, true) === false) {
    $issue = '';
}

// we may need a bit more memory and this may take a long time to process
@raise_memory_limit('128M');
@set_time_limit(0);

// Print the header.
admin_externalpage_setup('reportsecurity');
admin_externalpage_print_header();

print_heading(get_string('reportsecurity', 'report_security'));

echo '<div id="timewarning">'.get_string('timewarning', 'report_security').'</div>';
while(@ob_end_flush());
@flush();

$strok       = '<span class="statusok">'.get_string('statusok', 'report_security').'</span>';
$strinfo     = '<span class="statusinfo">'.get_string('statusinfo', 'report_security').'</span>';
$strwarning  = '<span class="statuswarning">'.get_string('statuswarning', 'report_security').'</span>';
$strserious  = '<span class="statusserious">'.get_string('statusserious', 'report_security').'</span>';
$strcritical = '<span class="statuscritical">'.get_string('statuscritical', 'report_security').'</span>';

$strissue    = get_string('issue', 'report_security');
$strstatus   = get_string('status', 'report_security');
$strdesc     = get_string('description', 'report_security');
$strconfig   = get_string('configuration', 'report_security');

$statusarr = array(REPORT_SECURITY_OK       => $strok,
                   REPORT_SECURITY_INFO     => $strinfo,
                   REPORT_SECURITY_WARNING  => $strwarning,
                   REPORT_SECURITY_SERIOUS  => $strserious,
                   REPORT_SECURITY_CRITICAL => $strcritical);

$url = "$CFG->wwwroot/$CFG->admin/report/security/index.php";

if ($issue and ($result = $issue(true))) {
    report_security_hide_timearning();

    $table = new object();
    $table->head  = array($strissue, $strstatus, $strdesc, $strconfig);
    $table->size  = array('30%', '10%', '50%', '10%' );
    $table->align = array('left', 'left', 'left', 'left');
    $table->width = '90%';
    $table->data  = array();

    // print detail of one issue only
    $row = array();
    $row[0] = report_security_doc_link($issue, $result->name);
    $row[1] = $statusarr[$result->status];
    $row[2] = $result->info;
    $row[3] = is_null($result->link) ? '&nbsp;' : $result->link;

    $CFG->pagepath = "report/security/$issue"; // help link in footer

    $table->data[] = $row;

    print_table($table);

    print_box($result->details, 'generalbox boxwidthnormal boxaligncenter'); // TODO: add proper css

    print_continue($url);

} else {
    report_security_hide_timearning();

    $table = new object();
    $table->head  = array($strissue, $strstatus, $strdesc);
    $table->size  = array('30%', '10%', '60%' );
    $table->align = array('left', 'left', 'left');
    $table->width = '90%';
    $table->data  = array();

    foreach ($issues as $issue) {
        $result = $issue(false);
        if (!$result) {
            // ignore this test
            continue;
        }
        $row = array();
        $row[0] = "<a href='$url?issue=$result->issue'>$result->name</a>";
        $row[1] = $statusarr[$result->status];
        $row[2] = $result->info;

        $table->data[] = $row;
    }
    print_table($table);
}

print_footer();