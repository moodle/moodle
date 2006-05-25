<?php
    require_once('../../config.php');
    require_once('locallib.php');
	require_once('sequencinglib.php');
		
	//$f = "D:\\test.txt";
	//@$ft = fopen($f,"a");
	//fwrite($ft,"Bat dau ghi tron datamodel.php \n");

    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);         // scorm ID
    $scoid = required_param('scoid', PARAM_INT);  // sco ID
//    $attempt = required_param('attempt', PARAM_INT);  // attempt number
	$attempt = $USER->attempt;
	//fwrite($ft,"\n --------Gia tri attempt thu duoc tu datamodel.php-------- : ".$attempt);


    if (!empty($id)) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
        if (! $scorm = get_record("scorm", "id", $cm->instance)) {
            error("Course module is incorrect");
        }
    } else if (!empty($a)) {
        if (! $scorm = get_record("scorm", "id", $a)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $scorm->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    } else {
        error('A required parameter is missing');
    }

    require_login($course->id, false, $cm);

    if (confirm_sesskey() && (!empty($scoid))) {
		//fwrite($ft," --Ghi du lieu--- \n");
        $result = true;
        if (isstudent($course->id) || (isteacher($course->id) && !isadmin())) {
            foreach ($_POST as $element => $value) {
                if (substr($element,0,3) == 'cmi') {
                    $element = str_replace('__','.',$element);
                    $element = preg_replace('/_(\d+)/',".\$1",$element);
                    $result = scorm_insert_track($USER->id, $scorm->id, $scoid, $attempt, $element, $value) && $result;
					//fwrite($ft,"\n Ghi xong mot phan tu tai Datamodel.php-- ".$scoid);

                }
            }
        }
        if ($result) {
            echo "true\n0";
			//fwrite($ft,"Ghi thanh cong trong  Datamodel.php-");
        } else {
            echo "false\n101";
			//fwrite($ft,"Ghi that bai trong  Datamodel.php-");
        }
    }
?>

