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

    if ( $scoes_user = get_records_select("scorm_sco_users","userid = ".$USER->id." AND scormid = ".$scorm->id,"scoid ASC") ) {
        //
        // Already user
        //
	if (!empty($scoid)) {	
	    //
	    // Direct sco request
	    //
	    if ($sco = get_record("scorm_scoes","id",$scoid)) {
	        if ($sco->launch == '') {
	            // Search for th first launchable sco 
	            if ($scoes = get_records("scorm_scoes","scorm",$scorm->id,"id ASC")) {
	                $sco = current($scoes);
	                while ($sco->id < $scoid) {
	                    $sco = next($scoes);
	                }
	                while ($sco->launch == '') {
	                    $sco = next($scoes);
	                }
	            }
	        }
	    }
	} else {
	    //
	    // Search for first incomplete sco
	    //
	    foreach ( $scoes_user as $sco_user ) {
		if (($sco_user->cmi_core_lesson_status != "completed") && ($sco_user->cmi_core_lesson_status != "passed") && ($sco_user->cmi_core_lesson_status != "failed")) {
		    $sco = get_record("scorm_scoes","id",$sco_user->scoid);
		    break;
		} else {
		    // If review mode get the first
		    if ($mode == "review") {
			$sco = get_record("scorm_scoes","id",$sco_user->scoid);
			break;
		    }
		}
	    }
	}
	//
	// If no sco was found get the first of SCORM package
	//
	if (!isset($sco)) {
	    $scoes = get_records_select("scorm_scoes","scorm=".$scorm->id." AND launch<>'' order by id ASC");
	    $sco = each($scoes);
	}
    } else {
        //
        // A new user
        //
	if ($scoes = get_records("scorm_scoes","scorm",$scorm->id,"id ASC")) {
	    //
	    // Create user scoes records
	    //
	    foreach ($scoes as $sco) {
		if (($sco->launch != "") && ($sco->type != "sca") && ($sco->type != "asset")){
		    if (!isset($first)) {
			$first = $sco;
		    }
		    $sco_user->userid = $USER->id;
		    $sco_user->scoid = $sco->id;
		    $sco_user->scormid = $scorm->id;
		    $element = "cmi_core_lesson_status";
		    $sco_user->$element = "not attempted";
		    $ident = insert_record("scorm_sco_users",$sco_user);
		}
	    }
	    if (isset($first)) {
	        $sco = $first;
	    }
	    if (!empty($scoid)) {
		if ($sco = get_record("scorm_scoes","id",$scoid)) {
		    unset($first);
		}
	    }
	}
    }
    //
    // Forge SCO URL
    //
    if (scorm_external_link($sco->launch)) {
	$result = $sco->launch;
    } else {
	if ($CFG->slasharguments) {
	    $result = "$CFG->wwwroot/file.php/$scorm->course/moddata/scorm$scorm->datadir/$sco->launch";
	} else {
	    $result = "$CFG->wwwroot/file.php?file=/$scorm->course/moddata/scorm$scorm->datadir/$sco->launch";
	}
    }
?>
<html>
    <head>
	<title>LoadSCO</title>
    </head>
    <body>
	<script language="javascript">
<?php	
    if ($scorm->popup == "") { 
	echo "\t    document.location=\"$result\";\n";
    } else {
        $popuplocation = '';
        if (isset($_COOKIE["SCORMpopup"])) {
            $popuplocation = $_COOKIE["SCORMpopup"];
        }
   	echo "\t    top.main = window.open('$result','main','$scorm->popup$popuplocation');\n";
    }
?>
	</script>
    </body>
</html>