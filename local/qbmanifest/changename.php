<?php
require_once('../../config.php');
global $CFG,$DB;
require_once($CFG->libdir . '/adminlib.php');
defined('MOODLE_INTERNAL') || die();


require_login();

if(!is_siteadmin()){
    throw new \moodle_exception('accessdenied');
}

$rec = $DB->get_records_sql(
    "SELECT * FROM {course} WHERE ".$DB->sql_like('idnumber', ':within', false, false),
    ['within' => 'dcl%']
);

echo '<pre>';
if ($rec) {
    foreach ($rec as $course) {
        $level = substr($course->idnumber,3,2); 
        $coursename = 'DigiChamps Level '.$level;
        echo $course->idnumber.'=> '.$coursename.'<br>'; 
        $DB->set_field('course', 'fullname', $coursename, array('id'=>$course->id));
    }
}


$rec = $DB->get_records_sql(
    "SELECT * FROM {course} WHERE ".$DB->sql_like('idnumber', ':within', false, false),
    ['within' => 'dpl%']
);

if ($rec) {
    foreach ($rec as $course) {
        $level = substr($course->idnumber,3,2);
        $coursename = 'DigiPro Level '.$level;
        echo $course->idnumber.'=> '.$coursename.'<br>'; 
        $DB->set_field('course', 'fullname', $coursename, array('id'=>$course->id));
    }
}

exit;