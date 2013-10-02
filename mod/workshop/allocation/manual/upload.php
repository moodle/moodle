<?php

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__)))))."/config.php");
require_once("upload_form.php");
require_once("../../locallib.php");

$cm  = required_param('cm', PARAM_INT);
$cm = get_coursemodule_from_id('workshop',$cm);
require_login($cm->course);
$context = $PAGE->context;
require_capability('mod/workshop:allocate', $context);

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$workshop = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);
$workshop = new workshop($workshop, $cm, $course);

$form = $workshop->teammode ? new workshop_allocation_teammode_manual_upload_form() : new workshop_allocation_manual_upload_form();

if($form->exportValue('clear'))
{

	$vals = $DB->get_records('workshop_submissions',array('workshopid' => $workshop->id), '', 'id');
	$DB->delete_records_list('workshop_assessments','submissionid',array_keys($vals));

} else {

    $content = $form->get_file_content('file');
    $content = preg_replace('!\r\n?!', "\n", $content);
    $csv = array_map('str_getcsv',explode("\n",$content));

	$usernames = array();
	foreach($csv as $a) {
		$usernames = array_merge($usernames,$a);
	}

	$users = $DB->get_records_list('user','username',$usernames,'','username,id,firstname,lastname');

	$failures = array(); // username => reason
    
	foreach($csv as $a) {
		if(!empty($a)) {
			$reviewee = trim($a[0]);
			$reviewers = array_slice($a,1);
			
			if (empty($reviewee)) continue;
			if (empty($reviewers)) continue;
			
			if (empty($users[$reviewee])) {
                
				$failures[$reviewee] = "error::No user for username $reviewee";
				continue;
			}
            
			$submission = $workshop->get_submission_by_author($users[$reviewee]->id);
			
			if ($submission === false) {
				$failures[$reviewee] = "error::No submission for {$users[$reviewee]->firstname} {$users[$reviewee]->lastname} ($reviewee)";
				continue;
			}
			
			foreach($reviewers as $i) {
				if (empty($i)) continue;
				if (empty($users[$i])) {
                    $failures[$i] = "error::No user for username $i";
                } else if (!$workshop->useselfassessment && $reviewee == $i) {
                    $failures[$i] = "info::Self-assessment is disabled for this workshop. {$users[$reviewee]->firstname} {$users[$reviewee]->lastname} ($i) was not allocated to assess their own submission.";
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