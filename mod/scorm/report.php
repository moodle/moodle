<?PHP  // $Id$

// This script uses installed report plugins to print quiz reports

    require_once("../../config.php");
    require_once("lib.php");

    optional_variable($id);    // Course Module ID, or

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
        if (! $scorm = get_record("scorm", "id", $q)) {
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

    if (!isteacher($course->id)) {
        error("You are not allowed to use this script");
    }

    add_to_log($course->id, "scorm", "report", "report.php?id=$cm->id", "$scorm->id");

/// Print the page header
    if (empty($noheader)) {

        if ($course->category) {
            $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
        }
    
        $strscorms = get_string("modulenameplural", "scorm");
        $strscorm  = get_string("modulename", "scorm");
        $strreport  = get_string("report", "scorm");
    
        print_header("$course->shortname: $scorm->name", "$course->fullname",
                     "$navigation <A HREF=index.php?id=$course->id>$strscorms</A> 
                      -> <a href=\"view.php?id=$cm->id\">$scorm->name</a> -> $strreport", 
                     "", "", true);
    
        print_heading($scorm->name);
    }
    if ($scoes =get_records_select("scorm_scoes","scorm='$scorm->id' ORDER BY id")) {
    	if ($sco_users=get_records_select("scorm_sco_users", "scormid='$scorm->id' GROUP BY userid")) {
        		
        	$strname  = get_string("name");

        	$table->head = array("&nbsp;", $strname);
        	$table->align = array("center", "left");
        	$table->wrap = array("nowrap", "nowrap");
        	$table->width = "100%";
        	$table->size = array(10, "*");
        	foreach ($scoes as $sco) {
        		if ($sco->launch!="") {
        		    $table->head[]=scorm_string_round($sco->title);
        		    $table->align[] = "center";
        			$table->wrap[] = "nowrap";
        			$table->size[] = "*";
        		}
        	}

        	foreach ($sco_users as $sco_user) {
        		$user_data=scorm_get_scoes_records($sco_user);
            	$picture = print_user_picture($sco_user->userid, $course->id, $user_data->picture, false, true);
            	$row="";
    			$row[] = $picture;
    			if (is_array($user_data)) {
    				$data = current($user_data);
    				$row[] = "<a href=\"$CFG->wwwroot/user/view.php?id=$data->userid&course=$course->id\">".
    					 "$data->firstname $data->lastname</a>";
    				foreach ($user_data as $data) {
    				    $scoreview = "";
    				    if ($data->cmi_core_score_raw > 0)
    				    	$scoreview = "<br />".get_string("score","scorm").":&nbsp;".$data->cmi_core_score_raw;
    				    if ( $data->cmi_core_lesson_status == "")
    		    			$data->cmi_core_lesson_status = "not attempted";
        		    	    $row[]="<img src=\"pix/".scorm_remove_spaces($data->cmi_core_lesson_status).".gif\" 
    						   alt=\"".get_string(scorm_remove_spaces($data->cmi_core_lesson_status),"scorm")."\"
    						   title=\"".get_string(scorm_remove_spaces($data->cmi_core_lesson_status),"scorm")."\">&nbsp;"
    						   .$data->cmi_core_total_time.$scoreview;
        			}
        		}
            	$table->data[] = $row; 
        	}
    
        	print_table($table);
        
    	} else {
    		notice("No users to report");
    	}
    }
    if (empty($noheader)) {
        print_footer($course);
    }


?>
