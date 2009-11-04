<?php
function quiz_report_statistics_cron(){
    global $DB;
    if ($todelete = $DB->get_records_select_menu('quiz_statistics', 'timemodified < ?', array(time()-5*HOURSECS))){
        list($todeletesql, $todeleteparams) = $DB->get_in_or_equal(array_keys($todelete));
        if (!$DB->delete_records_select('quiz_statistics', "id $todeletesql", $todeleteparams)){
            mtrace('Error deleting out of date quiz_statistics records.');
        }
        if (!$DB->delete_records_select('quiz_question_statistics', "quizstatisticsid $todeletesql", $todeleteparams)){
            mtrace('Error deleting out of date quiz_question_statistics records.');
        }
    }
    return true;
}

