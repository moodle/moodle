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

    if ( $scoes_user = get_records_select("scorm_sco_users","userid = ".$USER->id." AND scormid = ".$scorm->id,"scoid ASC") ) {
	if ($_GET["scoid"]) {
	    $sco = get_record("scorm_scoes","id",$_GET["scoid"]);
	} else {
	    foreach ( $scoes_user as $sco_user ) {
		if (($sco_user->cmi_core_lesson_status != "completed") && ($sco_user->cmi_core_lesson_status != "passed") && ($sco_user->cmi_core_lesson_status != "failed")) {
		    $sco = get_record("scorm_scoes","id",$sco_user->scoid);
		    break;
		} else {
		    if ($_GET["mode"] == "review") {
			$sco = get_record("scorm_scoes","id",$sco_user->scoid);
			break;
		    }
		}
	    }
	}
	if (!$sco)
	    $sco = get_record_select("scorm_scoes","scorm=".$scorm->id." AND launch<>'' order by id ASC");
    } else {	
	if ($scoes = get_records("scorm_scoes","scorm",$scorm->id,"id ASC")) {
	    foreach ($scoes as $sco) {
		if ($sco->launch != "") {
		    if (!$first)
			$first = $sco;
		    $sco_user->userid = $USER->id;
		    $sco_user->scoid = $sco->id;
		    $sco_user->scormid = $scorm->id;
		    $element = "cmi_core_lesson_status";
		    if ($sco->type == "sco") 
			$sco_user->$element = "not attempted";
		    else if ($sco->type == "sca")
			$sco_user->$element = "completed";
		    $ident = insert_record("scorm_sco_users",$sco_user);
	}
	    }
	    $sco = $first;
	    if ($_GET["scoid"])
		$sco = get_record("scorm_scoes","id",$_GET["scoid"]);
	}
    }
    //
    // Get first, last, prev and next scoes
    //
    $scoes = get_records("scorm_scoes","scorm",$scorm->id,"id ASC");
    foreach ($scoes as $fsco) {
	if ($fsco->launch != "") {
	    if (!$min || ($min > $fsco->id))
		$min = $fsco->id;
	    if (!$max || ($max < $fsco->id))
		$max = $fsco->id;
	    if ((!$prevsco) || ($sco->id > $fsco->id)) {
		$prevsco = $fsco->id;
	    }
	    if ((!$nextsco) && ($sco->id < $fsco->id)) {
		$nextsco = $fsco->id;
	    }
	}
    }
    if ($sco->id == $min)
	$first = $sco;
    if ($sco->id == $max)
	$last = $sco;

    // Get User data
    $sco_user = get_record("scorm_sco_users","userid",$USER->id,"scoid",$sco->id);
    if (scorm_external_link($sco->launch)) {
	$result = $sco->launch;
    } else {
	if ($CFG->slasharguments) {
	    $result = "$CFG->wwwroot/file.php/$scorm->course/moddata/scorm$scorm->datadir/$sco->launch";
	} else {
	    $result = "$CFG->wwwroot/file.php?file=/$scorm->course/moddata/scorm$scorm->datadir/$sco->launch";
	}
    }
    include("api1_2.php");
?>
