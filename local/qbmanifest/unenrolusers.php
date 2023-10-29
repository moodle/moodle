<?php
require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot.'/enrol/locallib.php');
require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->dirroot.'/enrol/manual/locallib.php');
require_once($CFG->dirroot.'/cohort/lib.php');

//$ref_csname = 'DCL02'; // Reference Course Short name
//$cohort_idnumber = 'bfsajman';
// Example http://qubits.localhost.com/local/qbmanifest/cloneenrolusers.php?cshortname=DCL03&cohortid=bfsajman

require_login();

if(!is_siteadmin()){
    throw new \moodle_exception('accessdenied');
}

$ref_csname = required_param('cshortname', PARAM_ALPHANUMEXT);
$cohort_idnumber = required_param('cohortid', PARAM_ALPHANUMEXT);

$manplugin = enrol_get_plugin('manual');

$parent_course = $DB->get_record("course",[
        "shortname" => $ref_csname
    ]);


// Get Groups and users from source course
// Get Source group users
$qry = "SELECT * FROM {groups} WHERE ";
$gparams["courseid"] = $parent_course->id;

$gparams["name1"] = $cohort_idnumber.$ref_csname.'%';
$gparams["name2"] = $cohort_idnumber.'%'.$ref_csname;
$gparams["name3"] = $ref_csname.'%'.$cohort_idnumber;

$where = "courseid = :courseid ";
$where .= " AND ( ".$DB->sql_like('name', ':name1', false)." OR ".$DB->sql_like('name', ':name2', false)." OR ".$DB->sql_like('name', ':name3', false)." )";
$course_groups = $DB->get_records_sql("$qry $where", $gparams);

if($cohort_idnumber=="dnsbarsha"){
    $manplugin = enrol_get_plugin('oneroster');
    $par_course_instance = $DB->get_record('enrol', array('courseid'=>$parent_course->id, 'enrol'=>'oneroster'), '*');
}else{
    $par_course_instance = $DB->get_record('enrol', array('courseid'=>$parent_course->id, 'enrol'=>'manual'), '*');    
}

foreach($course_groups as $course_group){
   $old_group_id = $course_group->id;
   $egroup_members = groups_get_members_by_role($course_group->id, $parent_course->id);

   foreach($egroup_members as $egroup_member){
	   $roleid = $egroup_member->id;
	   $egusers = $egroup_member->users;
	   foreach($egusers as $eguser){
           groups_remove_member($old_group_id, $eguser->id);
           $manplugin->unenrol_user($par_course_instance, $eguser->id);
	   }
   }
   groups_delete_group($old_group_id);
   
}

exit;