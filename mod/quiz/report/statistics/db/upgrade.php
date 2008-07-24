<?php  // $Id$

function xmldb_quizreport_statistics_upgrade($oldversion=0) {

    global $DB;
    
    $dbman = $DB->get_manager();

    $result = true;

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2008072401) {
        //register cron to run every 5 hours.
        $result = $result && $DB->set_field('quiz_report', 'cron', HOURSECS*5, array('name'=>'statistics'));
    }
    return $result;
}

?>
