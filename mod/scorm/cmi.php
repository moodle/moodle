<?php
    require_once("../../config.php");
    require_once("lib.php");
    
    optional_variable($id);    // Course Module ID, or
    optional_variable($a);     // scorm ID

    if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }
    
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
    
        if (! $scorm = get_record("scorm", "id", $cm->instance)) {
            error("Course module is incorrect");
        }

    } else {
        if (! $scorm = get_record("scorm", "id", $a)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $scorm->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

    require_login($course->id);
    
    if (!empty($_POST["scoid"])) {
        if (!empty($_POST["cmi_core_lesson_location"])) {
    	    set_field("scorm_sco_users","cmi_core_lesson_location",$_POST["cmi_core_lesson_location"],"scoid",$_POST["scoid"],"userid",$USER->id);
    	}
    	if (!empty($_POST["cmi_core_lesson_status"])) {
            set_field("scorm_sco_users","cmi_core_lesson_status",$_POST["cmi_core_lesson_status"],"scoid",$_POST["scoid"],"userid",$USER->id);
        }
    	if (!empty($_POST["cmi_core_exit"])) {
            set_field("scorm_sco_users","cmi_core_exit",$_POST["cmi_core_exit"],"scoid",$_POST["scoid"],"userid",$USER->id);
        }
    	if (!empty($_POST["cmi_core_total_time"])) {
            set_field("scorm_sco_users","cmi_core_total_time",$_POST["cmi_core_total_time"],"scoid",$_POST["scoid"],"userid",$USER->id);
        }
    	if (!empty($_POST["cmi_core_score_raw"])) {
            set_field("scorm_sco_users","cmi_core_score_raw",$_POST["cmi_core_score_raw"],"scoid",$_POST["scoid"],"userid",$USER->id);
        }
    	if (!empty($_POST["cmi_suspend_data"])) {
            set_field("scorm_sco_users","cmi_suspend_data",$_POST["cmi_suspend_data"],"scoid",$_POST["scoid"],"userid",$USER->id);
        }
    }
?>
<html>
<head>
   <title>cmi</title>
</head>
<body>
   <form name="theform" method="POST" action="<?php echo $ME ?>?id=<?php echo $cm->id ?>"> 
	<input type="hidden" name="scoid" />
	<input type="hidden" name="cmi_core_lesson_location" />
	<input type="hidden" name="cmi_core_lesson_status" />
	<input type="hidden" name="cmi_core_exit" />
	<input type="hidden" name="cmi_core_session_time" />
	<input type="hidden" name="cmi_core_total_time"  />
	<input type="hidden" name="cmi_core_score_raw" />
	<input type="hidden" name="cmi_suspend_data" />
   </form>
</body>
</html>
