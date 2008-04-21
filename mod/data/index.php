<?php // $Id$
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1990-onwards Moodle Pty Ltd   http://moodle.com         //
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

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id', PARAM_INT);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_course_login($course);

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    add_to_log($course->id, "data", "view all", "index.php?id=$course->id", "");

    $strweek = get_string('week');
    $strtopic = get_string('topic');
    $strname = get_string('name');
    $strdata = get_string('modulename','data');
    $strdataplural  = get_string('modulenameplural','data');

    $navlinks = array();
    $navlinks[] = array('name' => $strdata, 'link' => "index.php?id=$course->id", 'type' => 'activity');
    $navigation = build_navigation($navlinks);

    print_header_simple($strdata, '', $navigation, '', '', true, "", navmenu($course));

    if (! $datas = get_all_instances_in_course("data", $course)) {
        notice(get_string('thereareno', 'moodle',$strdataplural) , "$CFG->wwwroot/course/view.php?id=$course->id");
    }

    $timenow  = time();
    $strname  = get_string('name');
    $strweek  = get_string('week');
    $strtopic = get_string('topic');
    $strdescription = get_string("description");
    $strentries = get_string('entries', 'data');
    $strnumnotapproved = get_string('numnotapproved', 'data');

    if ($course->format == 'weeks') {
        $table->head  = array ($strweek, $strname, $strdescription, $strentries, $strnumnotapproved);
        $table->align = array ('center', 'center', 'center', 'center', 'center');
    } else if ($course->format == 'topics') {
        $table->head  = array ($strtopic, $strname, $strdescription, $strentries, $strnumnotapproved);
        $table->align = array ('center', 'center', 'center', 'center', 'center');
    } else {
        $table->head  = array ($strname, $strdescription, $strentries, $strnumnotapproved);
        $table->align = array ('center', 'center', 'center', 'center');
    }

    $rss = (!empty($CFG->enablerssfeeds) && !empty($CFG->data_enablerssfeeds));

    if ($rss) {
        require_once($CFG->libdir."/rsslib.php");
        array_push($table->head, 'RSS');
        array_push($table->align, 'center');
    }

    $options = new object();
    $options->noclean = true;

    $currentsection = "";

    foreach ($datas as $data) {

        $printsection = "";

        //Calculate the href
        if (!$data->visible) {
            //Show dimmed if the mod is hidden
            $link = "<a class=\"dimmed\" href=\"view.php?id=$data->coursemodule\">".format_string($data->name,true)."</a>";
        } else {
            //Show normal if the mod is visible
            $link = "<a href=\"view.php?id=$data->coursemodule\">".format_string($data->name,true)."</a>";
        }

        // TODO: add group restricted counts here, and limit unapproved to ppl with approve cap only + link to approval page

        $numrecords = count_records_sql('SELECT COUNT(r.id) FROM '.$CFG->prefix.
                'data_records r WHERE r.dataid ='.$data->id);

        if ($data->approval == 1) {
            $numunapprovedrecords = count_records_sql('SELECT COUNT(r.id) FROM '.$CFG->prefix.
                    'data_records r WHERE r.dataid ='.$data->id.
                    ' AND r.approved <> 1');
        } else {
            $numunapprovedrecords = '-';
        }

        $rsslink = '';
        if ($rss && $data->rssarticles > 0) {
            $rsslink = rss_get_link($course->id, $USER->id, 'data', $data->id, 'RSS');
        }

        if ($course->format == 'weeks' or $course->format == 'topics') {
            if ($data->section !== $currentsection) {
                if ($data->section) {
                    $printsection = $data->section;
                }
                if ($currentsection !== '') {
                    $table->data[] = 'hr';
                }
                $currentsection = $data->section;
            }
            $row = array ($printsection, $link, format_text($data->intro, FORMAT_MOODLE, $options), $numrecords, $numunapprovedrecords);

        } else {
            $row = array ($link, format_text($data->intro, FORMAT_MOODLE, $options), $numrecords, $numunapprovedrecords);
        }

        if ($rss) {
            array_push($row, $rsslink);
        }

        $table->data[] = $row;
    }

    echo "<br />";
    print_table($table);
    print_footer($course);

?>
