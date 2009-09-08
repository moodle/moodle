<?php // $Id$

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
*//** */

    require_once("../config.php");
    require_once("editlib.php");

    list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) = question_edit_setup('questions');
    $questionbank = new question_bank_view($contexts, $thispageurl, $COURSE, $cm);
    $questionbank->process_actions();

    // TODO log this page view.

    $localcss = '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.
            '/lib/yui/container/assets/container.css" />';
    $context = $contexts->lowest();
    $streditingquestions = get_string('editquestions', "quiz");
    if ($cm!==null) {
        $strupdatemodule = has_capability('moodle/course:manageactivities', $contexts->lowest())
            ? update_module_button($cm->id, $COURSE->id, get_string('modulename', $cm->modname))
            : "";
        $PAGE->navbar->add(get_string('modulenameplural', $cm->modname), new moodle_url($CFG->wwwroot.'/mod/'.$cm->modname.'/index.php', array('id'=>$COURSE->id)));
        $PAGE->navbar->add(format_string($module->name), new moodle_url($CFG->wwwroot.'/mod/'.$cm->modname.'/view.php', array('id'=>$cm->id)));
        $PAGE->navbar->add($streditingquestions);
        $PAGE->set_title($streditingquestions);
        $PAGE->set_button($strupdatemodule);
        echo $OUTPUT->header();

        $currenttab = 'edit';
        $mode = 'questions';
        ${$cm->modname} = $module;
        include($CFG->dirroot."/mod/$cm->modname/tabs.php");
    } else {
        // Print basic page layout.
        $PAGE->navbar->add($streditingquestions);
        $PAGE->set_title($streditingquestions);
        echo $OUTPUT->header();

        // print tabs
        $currenttab = 'questions';
        include('tabs.php');
    }

    echo '<div class="questionbankwindow boxwidthwide boxaligncenter">';
    $questionbank->display('questions', $pagevars['qpage'],
            $pagevars['qperpage'], $pagevars['qsortorder'], $pagevars['qsortorderdecoded'],
            $pagevars['cat'], $pagevars['recurse'], $pagevars['showhidden'], $pagevars['showquestiontext']);
    echo "</div>\n";

    echo $OUTPUT->footer();
?>
