<?php
    require_once("../../config.php");
    require_once("lib.php");

    optional_variable($id);    // Course Module ID, or
    optional_variable($a);     // scorm ID
    optional_variable($scoid); // sco ID
    optional_variable($mode);

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
        //
        // Already user
        //
	if (!empty($scoid)) {	
	    // Direct sco request
	    //$sco = get_record("scorm_scoes","id",$scoid);
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
	    // Search for first incomplete sco
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
	if (!isset($sco)) {  // If no sco was found get the first of SCORM package
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
		if ($sco->launch != "") {
		    if (!isset($first))
			$first = $sco;
		    $sco_user->userid = $USER->id;
		    $sco_user->scoid = $sco->id;
		    $sco_user->scormid = $scorm->id;
		    $element = "cmi_core_lesson_status";
		    if ($sco->type == "sco") 
			$sco_user->$element = "not attempted";
		    else if (($sco->type == "sca") || ($sco->type == "asset"))
			$sco_user->$element = "completed";
		    $ident = insert_record("scorm_sco_users",$sco_user);
		}
	    }
	    if (isset($first))
	        $sco = $first;
	    if (!empty($scoid)) {
		if ($sco = get_record("scorm_scoes","id",$scoid))
		    unset($first);
	    }
	}
    }
    //
    // Get first, last, prev and next scoes
    //
    $scoes = get_records("scorm_scoes","scorm",$scorm->id,"id ASC");
    $min = 0;
    $max = 0;
    $prevsco = 0;
    $nextsco = 0;
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
    $first = NULL;
    $last = NULL;
    if ($sco->id == $min)
	$first = $sco;
    if ($sco->id == $max)
	$last = $sco;

    // Get current sco User data
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
    $navObj = "top.";
    if ($scorm->popup == "")
        $navObj = "top.navigation.";
        
    include("api1_2.php");

?>

function hilightcurrent(popupmenu) {
    for (i=0;i < popupmenu.options.length;i++) {
	 if ( popupmenu.options[i].value == <?php echo $sco->id; ?> )
	    	popupmenu.options[i].selected = true;
    }
}

function SCOInitialize() { 
<?php
    if ( $sco->previous || $first) {
    	print "\t".$navObj."document.navform.prev.disabled = true;\n";
	print "\t".$navObj."document.navform.prev.style.display = 'none';\n";
    }
    if ( $sco->next || $last) {
    	print "\t".$navObj."document.navform.next.disabled = true;\n";
	print "\t".$navObj."document.navform.next.style.display = 'none';\n";
    }
?>
<?php	
    if ($scorm->popup == "") { 
	echo "\t    top.main.location=\"$result\";\n";
	echo "\t    hilightcurrent(".$navObj."document.navform.courseStructure);\n";
    } else {
   	echo "\t    top.main = window.open('$result','main','$scorm->popup');\n";
    }
?>
} 

function changeSco(direction) {
	if (direction == "previous")
	    <?php echo $navObj ?>document.navform.scoid.value="<?php echo $prevsco; ?>";
	else
	    <?php echo $navObj ?>document.navform.scoid.value="<?php echo $nextsco; ?>";
	    
	//alert ("Prev: <?php echo $prevsco; ?>\nNext: <?php echo $nextsco; ?>\nNew SCO: "+<?php echo $navObj ?>document.navform.scoid.value);
	<?php echo $navObj ?>document.navform.submit();
}   