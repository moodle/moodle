<?php
    require_once("../../config.php");
    require_once("lib.php");

    optional_variable($id);    // Course Module ID, or
    optional_variable($a);     // scorm ID
    optional_variable($scoid); // sco ID

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

    require_login($course->id, false, $cm);

    if (!empty($scoid)) {	
    //
    // Direct sco request
    //
	if ($sco = get_record("scorm_scoes","id",$scoid)) {
	    if ($sco->launch == '') {
		// Search for the next launchable sco 
		if ($scoes = get_records_select("scorm_scoes","scorm=".$scorm->id." AND launch<>'' AND id>".$scoid,"id ASC")) {
		    $sco = current($scoes);
		}
	    }
	}
    } else {
    //
    // Search for first incomplete sco
    //
    	if ( $scoes_track = get_records_select("scorm_scoes_track","userid=".$USER->id." AND element='cmi.core.lesson_status' AND scormid=".$scorm->id,"scoid ASC") ) {
    	    $sco_track = current($scoes_track);
	    while ((($sco_track->value == "completed") || ($sco_track->value == "passed") || ($sco_track->value == "failed")) && ($mode == "normal")) {
		$sco_track = next($scoes_track);
	    }
	    $sco = get_record("scorm_scoes","id",$sco_track->scoid);
	}
	
    }
    
    //
    // If no sco was found get the first of SCORM package
    //
    if (!isset($sco)) {
	$scoes = get_records_select("scorm_scoes","scorm=".$scorm->id." AND launch<>''","id ASC");
	$sco = current($scoes);
    }

    //
    // Forge SCO URL
    //
    $connector = '';
    if (!empty($sco->parameters)) {
	if (stripos($sco->launch,'?') !== false) {
	    $connector = '&';
	} else {
    	    $connector = '?';
    	}
    }
    if (scorm_external_link($sco->launch)) {
	$result = $sco->launch;
    } else {
	if ($CFG->slasharguments) {
	    $result = $CFG->wwwroot.'/file.php/'.$scorm->course.'/moddata/scorm'.$scorm->datadir.'/'.$sco->launch.$connector.$sco->parameters;
	} else {
	    $result = $CFG->wwwroot.'/file.php?file=/'.$scorm->course.'/moddata/scorm'.$scorm->datadir.'/'.$sco->launch.$connector.$sco->parameters;
	}
    }
?>
<html>
    <head>
	<title>LoadSCO</title>
    </head>
    <body>
	<script language="javascript">
	    document.location='<?php echo $result ?>';
	</script>
    </body>
</html>