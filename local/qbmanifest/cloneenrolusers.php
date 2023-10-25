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

$ref_csname = required_param('cshortname', PARAM_ALPHANUMEXT);
$cohort_idnumber = required_param('cohortid', PARAM_ALPHANUMEXT);

$manplugin = enrol_get_plugin('manual');

$parent_course = $DB->get_record("course",[
        "shortname" => $ref_csname
    ]);

$current_course = $DB->get_record("course",[
        "shortname" => $ref_csname.$cohort_idnumber
    ]);


// Get Groups and users from source course
// Get Source group users
$qry = "SELECT * FROM {groups} WHERE ";
$gparams["courseid"] = $parent_course->id;

if($cohort_idnumber=="dnsbarsha")
  $gparams["name"] = $cohort_idnumber.'%'.$ref_csname;
else
  $gparams["name"] = $cohort_idnumber.$ref_csname.'%';

$where = "courseid = :courseid ";
$where .= " AND ".$DB->sql_like('name', ':name', false);

$course_groups = $DB->get_records_sql("$qry $where", $gparams);
if($cohort_idnumber=="dnsbarsha"){
    $manplugin = enrol_get_plugin('oneroster');
    $cur_course_instance = $DB->get_record('enrol', array('courseid'=>$current_course->id, 'enrol'=>'oneroster'), '*');
    $par_course_instance = $DB->get_record('enrol', array('courseid'=>$parent_course->id, 'enrol'=>'oneroster'), '*');
}else{
    $cur_course_instance = $DB->get_record('enrol', array('courseid'=>$current_course->id, 'enrol'=>'manual'), '*');
    $par_course_instance = $DB->get_record('enrol', array('courseid'=>$parent_course->id, 'enrol'=>'manual'), '*');    
}

foreach($course_groups as $course_group){
   $old_group_id = $course_group->id;
   $egroup_members = groups_get_members_by_role($course_group->id, $parent_course->id);
   $newgroup = $DB->get_record("groups", [
       "courseid" => $current_course->id,
	   "name" => $course_group->name
   ]);
   
   if($newgroup->id){
	   $gid = $newgroup->id;
   }else{
	    $newgroup = $course_group;
	    $newgroup->id = null;
		$newgroup->courseid = $current_course->id;
        $gid = groups_create_group($newgroup);
		// groups_add_member($groupid, $user->id);
   }
   foreach($egroup_members as $egroup_member){
	   $roleid = $egroup_member->id;
	   $egusers = $egroup_member->users;
	   foreach($egusers as $eguser){
		   $manplugin->enrol_user($cur_course_instance, $eguser->id, $roleid);
		   groups_add_member($gid, $eguser->id);
           //groups_remove_member($old_group_id, $eguser->id);
           //$manplugin->unenrol_user($par_course_instance, $eguser->id); // unenroll user from parent course
	   }
	 // $manplugin->enrol_user($cur_course_instance, $user1->id, $studentrole->id);  
   }
   
   
   
   
}




// Create Groups and assigned users to destination course

echo "<pre>";
print_r($course_groups);
print_r($parent_course);
print_r($current_course);
echo "</pre>";
exit;