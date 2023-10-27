<?php
require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot.'/enrol/locallib.php');
require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->dirroot.'/enrol/manual/locallib.php');
require_once($CFG->dirroot.'/cohort/lib.php');

//$ref_csname = 'DCL02'; // Reference Course Short name
//$cohort_idnumber = 'bfsajman';
// Example http://qubits.localhost.com/local/qbmanifest/cloneenrolusers-bulk.php?cohortid=bfsajman

$farr = array(
    "DCL01" => "/books-json/digichamps/dcl01.json",
    "DCL02" => "/books-json/digichamps/dcl02.json",
    "DCL03" => "/books-json/digichamps/dcl03.json",
    "DCL04" => "/books-json/digichamps/dcl04.json",
    "DCL05" => "/books-json/digichamps/dcl05.json",
    "DCL06" => "/books-json/digichamps/dcl06.json",
    "DCL07" => "/books-json/digichamps/dcl07.json",
    "DCL08" => "/books-json/digichamps/dcl08.json",
    "DCL09" => "/books-json/digichamps/dcl09.json",
    "DCL10" => "/books-json/digichamps/dcl10.json",
    "DCL11" => "/books-json/digichamps/dcl11.json",
    "DCL12" => "/books-json/digichamps/dcl12.json",
    "DPL01" => "/books-json/digipro/dpl01.json",
    "DPL02" => "/books-json/digipro/dpl02.json",
    "DPL03" => "/books-json/digipro/dpl03.json",
    "DPL04" => "/books-json/digipro/dpl04.json",
    "DPL05" => "/books-json/digipro/dpl05.json",
    "DPL06" => "/books-json/digipro/dpl06.json",
    "DPL07" => "/books-json/digipro/dpl07.json",
    "DPL08" => "/books-json/digipro/dpl08.json",
    "DPL09" => "/books-json/digipro/dpl09.json",
    "DPL10" => "/books-json/digipro/dpl10.json",
    "DPL11" => "/books-json/digipro/dpl11.json",
    "DPL12" => "/books-json/digipro/dpl12.json",
    "DJL01" => "/books-json/djl01.json"
);
$cohort_idnumber = required_param('cohortid', PARAM_ALPHANUMEXT);
$farr1 = $farr;
unset($farr1["DJL01"]);
unset($farr1["DPL12"]);

foreach($farr1 as $fak1 => $fav1)
{
$ref_csname = $fak1;


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

$gparams["name1"] = $cohort_idnumber.$ref_csname.'%';
$gparams["name2"] = $cohort_idnumber.'%'.$ref_csname;
$gparams["name3"] = $ref_csname.'%'.$cohort_idnumber;

$where = "courseid = :courseid ";
$where .= " AND ( ".$DB->sql_like('name', ':name1', false)." OR ".$DB->sql_like('name', ':name2', false)." OR ".$DB->sql_like('name', ':name3', false)." )";
$course_groups = $DB->get_records_sql("$qry $where", $gparams);

if($cohort_idnumber=="dnsbarsha"){
    $manplugin = enrol_get_plugin('oneroster');
    $cur_course_instance = $DB->get_record('enrol', array('courseid'=>$current_course->id, 'enrol'=>'oneroster'), '*');
    $par_course_instance = $DB->get_record('enrol', array('courseid'=>$parent_course->id, 'enrol'=>'oneroster'), '*');
}else{
    $cur_course_instance = $DB->get_record('enrol', array('courseid'=>$current_course->id, 'enrol'=>'manual'), '*');
    $par_course_instance = $DB->get_record('enrol', array('courseid'=>$parent_course->id, 'enrol'=>'manual'), '*');    
}
echo "<pre>"; print_r($course_groups);

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
           echo 'Group '.$gid.' User '.$eguser->id.' Role '.$roleid."<br/>";
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
}
exit;