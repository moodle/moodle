<?php
/**
* prints the tabbed bar
*
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/
    defined('MOODLE_INTERNAL') OR die('not allowed');

    $tabs = array();
    $row  = array();
    $inactive = array();
    $activated = array();

    $courseid = optional_param('courseid', false, PARAM_INT);
    // $current_tab = $SESSION->feedback->current_tab;
    if (!isset($current_tab)) {
        $current_tab = '';
    }

    $row[] = new tabobject('view', $CFG->wwwroot.htmlspecialchars('/mod/feedback/view.php?id='.$id.'&do_show=view'), get_string('overview', 'feedback'));

    if($capabilities->edititems) {
        $row[] = new tabobject('edit', $CFG->wwwroot.htmlspecialchars('/mod/feedback/edit.php?id='.$id.'&do_show=edit'), get_string('edit_items', 'feedback'));
        $row[] = new tabobject('templates', $CFG->wwwroot.htmlspecialchars('/mod/feedback/edit.php?id='.$id.'&do_show=templates'), get_string('templates', 'feedback'));
    }

    if($capabilities->viewreports) {
        if($feedback->course == SITEID){
            $row[] = new tabobject('analysis', $CFG->wwwroot.htmlspecialchars('/mod/feedback/analysis_course.php?id='.$id.'&courseid='.$courseid.'&do_show=analysis'), get_string('analysis', 'feedback'));
        }else {
            $row[] = new tabobject('analysis', $CFG->wwwroot.htmlspecialchars('/mod/feedback/analysis.php?id='.$id.'&courseid='.$courseid.'&do_show=analysis'), get_string('analysis', 'feedback'));
        }
    }

    if($capabilities->viewreports) {
        $row[] = new tabobject('showentries', $CFG->wwwroot.htmlspecialchars('/mod/feedback/show_entries.php?id='.$id.'&do_show=showentries'), get_string('show_entries', 'feedback'));
    }

    if(count($row) > 1) {
        $tabs[] = $row;

        print_tabs($tabs, $current_tab, $inactive, $activated);
    }

