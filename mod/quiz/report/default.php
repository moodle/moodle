<?php  // $Id$ 

////////////////////////////////////////////////////////////////////
/// Default class for report plugins                            
///                                                               
/// Doesn't do anything on it's own -- it needs to be extended.   
/// This class displays quiz reports.  Because it is called from 
/// within /mod/quiz/report.php you can assume that the page header
/// and footer are taken care of.
/// 
/// This file can refer to itself as report.php to pass variables 
/// to itself - all these will also be globally available.  You must 
/// pass "id=$cm->id" or q=$quiz->id", and "mode=reportname".
////////////////////////////////////////////////////////////////////

// Included by ../report.php

class quiz_default_report {

    function display($cm, $course, $quiz) {     /// This function just displays the report
        return true;
    }

    function print_header_and_tabs($cm, $course, $quiz, $reportmode="overview", $meta=""){
        global $CFG;
    /// Define some strings
        $strquizzes = get_string("modulenameplural", "quiz");
        $strquiz  = get_string("modulename", "quiz");
    /// Print the page header
        $navlinks[] = array('name' => $strquizzes, 'link' => "index.php?id=$course->id", 'type' => 'activity');
        $navlinks[] = array('name' => format_string($quiz->name), 'link' => '', 'type' => 'activityinstance');
        $navigation = build_navigation($navlinks);
        
        print_header_simple(format_string($quiz->name), "", $navigation,
                     '', $meta, true, update_module_button($cm->id, $course->id, $strquiz), navmenu($course, $cm));
    /// Print the tabs    
        $currenttab = 'reports';
        $mode = $reportmode;
        include('tabs.php');
    }
}

?>
