<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas and others                //
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
 * Page to edit the question bank
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 */

    require_once("../config.php");
    require_once("editlib.php");

    $url = new moodle_url('/question/edit.php');
    if (($lastchanged = optional_param('lastchanged', 0, PARAM_INT)) !== 0) {
        $url->param('lastchanged', $lastchanged);
    }
    if (($category = optional_param('category', 0, PARAM_TEXT)) !== 0) {
        $url->param('category', $category);
    }
    if (($qpage = optional_param('qpage', 0, PARAM_INT)) !== 0) {
        $url->param('qpage', $qpage);
    }
    if (($cat = optional_param('cat', 0, PARAM_TEXT)) !== 0) {
        $url->param('cat', $cat);
    }
    if (($courseid = optional_param('courseid', 0, PARAM_INT)) !== 0) {
        $url->param('courseid', $courseid);
    }
    if (($returnurl = optional_param('returnurl', 0, PARAM_INT)) !== 0) {
        $url->param('returnurl', $returnurl);
    }
    if (($cmid = optional_param('cmid', 0, PARAM_INT)) !== 0) {
        $url->param('cmid', $cmid);
    }
    $PAGE->set_url($url);
    $PAGE->set_pagelayout('standard');

    list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) =
            question_edit_setup('questions', '/question/edit.php');
    $questionbank = new question_bank_view($contexts, $thispageurl, $COURSE, $cm);
    $questionbank->process_actions();

    // TODO log this page view.

    $context = $contexts->lowest();
    $streditingquestions = get_string('editquestions', "quiz");
    $PAGE->set_title($streditingquestions);
    $PAGE->set_heading($COURSE->fullname);
    echo $OUTPUT->header();

    echo '<div class="questionbankwindow boxwidthwide boxaligncenter">';
    $questionbank->display('questions', $pagevars['qpage'],
            $pagevars['qperpage'], $pagevars['qsortorder'], $pagevars['qsortorderdecoded'],
            $pagevars['cat'], $pagevars['recurse'], $pagevars['showhidden'], $pagevars['showquestiontext']);
    echo "</div>\n";

    echo $OUTPUT->footer();

