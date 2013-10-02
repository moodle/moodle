<?php

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__)))))."/config.php");
require_once("upload_form.php");
require_once("../../locallib.php");

$cm  = required_param('cm', PARAM_INT);
$cm = get_coursemodule_from_id('workshop',$cm);
require_login($cm->course);
$context = context_module::instance($cm->id);
require_capability('mod/workshop:allocate', $context);

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$workshop = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);
$workshop = new workshop($workshop, $cm, $course);

$form = new workshop_allocation_teammode_manual_upload_form();
$csv = array_map('str_getcsv',preg_split("/[\r\n]+/",$form->get_file_content('file')));

if($form->exportValue('clear'))
{

	$vals = $DB->get_records('workshop_submissions',array('workshopid' => $workshop->id), '', 'id,title');
	list($select, $params) = $DB->get_in_or_equal(array_keys($vals));
	$delete = $DB->get_records_select('workshop_assessments',"submissionid $select AND grade is NULL",$params,'','id');
	$dontdelete = $DB->get_records_select('workshop_assessments',"submissionid $select AND grade is not NULL",$params,'','id,submissionid,reviewerid');
    $DB->delete_records_list('workshop_assessments','id',array_keys($delete));
	
	$reviewers = array();
	foreach ($dontdelete as $key => $value) {
		$reviewers[$value->reviewerid] = $value->reviewerid;
	}

	$failures = array();
	$users = $DB->get_records_list('user','id',$reviewers,'id,username,firstname,lastname');
	foreach($dontdelete as $i) {
		$failures[$users[$i->reviewerid]->username] = "error::Did not clear assessment by {$users[$i->reviewerid]->firstname} {$users[$i->reviewerid]->lastname} on {$vals[$i->submissionid]->title} because they already reviewed this submission.";
	}
	$SESSION->workshop_upload_messages = $failures;

} else {

	$usernames = array();
	foreach($csv as $a) {
		$usernames = array_merge($usernames,array_map('trim',array_slice($a,1)));
	}

	$users = $DB->get_records_list('user','username',$usernames,'','username,id,firstname,lastname');
	$groups = groups_get_all_groups($course->id,0,$cm->groupingid,'g.name,g.id');
    foreach ($groups as $g) {
        $g->members = array_keys(groups_get_members($g->id,'u.id','u.id'));
    }
 
	$failures = array(); // username => reason

	$submissions = $workshop->get_submissions_grouped();
    $submissions_by_group = array();
    foreach($submissions as $k => $s) {
        $submissions_by_group[$s->group->id] = $s;
    }
    
    // echo "<pre>";
    // print_r($users);
    // die;
    
	foreach($csv as $a) {
		if(!empty($a)) {
			$reviewee = trim($a[0]);
			$reviewers = array_slice($a,1);
			
			if (empty($reviewee)) continue;
			if (empty($reviewers)) continue;
			
			if (empty($groups[$reviewee])) {
				$failures[$reviewee] = "error::No group for name $reviewee";
				continue;
			}

			$group = $groups[$reviewee];
			
			if (empty($submissions_by_group[$group->id])) {
				$failures[$reviewee] = "error::No submission for $reviewee";
				continue;
			}

			$submission = $submissions_by_group[$group->id];
			
			foreach($reviewers as $i) {
                $i = trim($i);
				if (empty($i)) continue;
				if (empty($users[$i])) {
                    $failures[$i] = "error::No user for username $i";
				} else if (!$workshop->useselfassessment && in_array($users[$i]->id,$group->members)) {
				    $failures[$i] = "info::Self-assessment is disabled for this workshop. {$users[$i]->firstname} {$users[$i]->lastname} ($i) was not allocated to assess their own submission.";
				} else {
					$res = $workshop->add_allocation($submission, $users[$i]->id);
				}
			}
		}
	}

	$SESSION->workshop_upload_messages = $failures;

}

$url = new moodle_url('/mod/workshop/allocation.php', array('cmid' => $cm->id, 'method' => 'manual'));
redirect($url);