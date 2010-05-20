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

    //some pages deliver the cmid instead the id
    if(isset($cmid) AND intval($cmid) AND $cmid > 0) {
        $usedid = $cmid;
    }else {
        $usedid = $id;
    }

    if (!$context = get_context_instance(CONTEXT_MODULE, $usedid)) {
            print_error('badcontext');
    }


    $courseid = optional_param('courseid', false, PARAM_INT);
    // $current_tab = $SESSION->feedback->current_tab;
    if (!isset($current_tab)) {
        $current_tab = '';
    }

    $viewurl = new moodle_url('/mod/feedback/view.php', array('id'=>$usedid, 'do_show'=>'view'));
    $row[] = new tabobject('view', $viewurl->out(), get_string('overview', 'feedback'));

    if(has_capability('mod/feedback:edititems', $context)) {
        $editurl = new moodle_url('/mod/feedback/edit.php', array('id'=>$usedid, 'do_show'=>'edit'));
        $row[] = new tabobject('edit', $editurl->out(), get_string('edit_items', 'feedback'));
        
        $templateurl = new moodle_url('/mod/feedback/edit.php', array('id'=>$usedid, 'do_show'=>'templates'));
        $row[] = new tabobject('templates', $templateurl->out(), get_string('templates', 'feedback'));
    }

    if(has_capability('mod/feedback:viewreports', $context)) {
        if($feedback->course == SITEID){
            $analysisurl = new moodle_url('/mod/feedback/analysis_course.php', array('id'=>$usedid, 'courseid'=>$courseid, 'do_show'=>'analysis'));
            $row[] = new tabobject('analysis', $analysisurl->out(), get_string('analysis', 'feedback'));
        }else {
            $analysisurl = new moodle_url('/mod/feedback/analysis.php', array('id'=>$usedid, 'courseid'=>$courseid, 'do_show'=>'analysis'));
            $row[] = new tabobject('analysis', $analysisurl->out(), get_string('analysis', 'feedback'));
        }
        
        $reporturl = new moodle_url('/mod/feedback/show_entries.php', array('id'=>$usedid, 'do_show'=>'showentries'));
        $row[] = new tabobject('showentries', $reporturl->out(), get_string('show_entries', 'feedback'));
        
        if($feedback->anonymous == FEEDBACK_ANONYMOUS_NO AND $feedback->course != SITEID) {
            $nonrespondenturl = new moodle_url('/mod/feedback/show_nonrespondents.php', array('id'=>$usedid));
            $row[] = new tabobject('nonrespondents', $nonrespondenturl->out(), get_string('show_nonrespondents', 'feedback'));
        }
    }

    if(count($row) > 1) {
        $tabs[] = $row;

        print_tabs($tabs, $current_tab, $inactive, $activated);
    }

