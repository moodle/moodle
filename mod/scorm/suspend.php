<?php
    require_once('../../config.php');
    require_once('locallib.php');
	require_once('sequencinglib.php');
		
//	$f = "D:\\test.txt";
//	@$ft = fopen($f,"a");
//	fwrite($ft,"Bat dau ghi tron datamodel.php \n");

    $id = required_param('id', PARAM_INT);         // course ID
    $scormid = required_param('scorm', PARAM_INT);         // scorm ID
    $scoid = required_param('sco', PARAM_INT);  // suspend sco ID
    $userid = required_param('userid', PARAM_INT);  // user ID

	$attempt = scorm_get_last_attempt($scormid,$userid);
	$statistic = get_record('scorm_statistic',"scormid",$scormid,"userid",$userid);
	$statisticInput->accesstime = $statistic->accesstime;
	$statisticInput->durationtime = $statistic->durationtime + time()- $statistic->accesstime;
	$statisticInput->status = 'suspend';
	$statisticInput->attemptnumber = $attempt;
	$statisticInput->scormid = $statistic->scormid;
	$statisticInput->userid = $statistic->userid;	
	$statisticid = scorm_insert_statistic($statisticInput);

    $result = scorm_insert_trackmodel($userid, $scormid, $scoid,$attempt);
        if ($result) {
			echo "<script language='Javascript' type='text/javascript'>";
			echo "location.href='".$CFG->wwwroot." /course/view.php?id=".$id."';";	    
			echo "</script>";
        } 
		else {
			echo "Suspend failed";
        }

?>

