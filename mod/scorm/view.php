<?PHP  // $Id$

/// This page prints a particular instance of scorm
/// (Replace scorm with the name of your module)

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

    
    $strscorms = get_string("modulenameplural", "scorm");
    $strscorm  = get_string("modulename", "scorm");
    	
    if ($course->category) {
        $navigation = "<a target=\"{$CFG->framename}\" href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->
                       <a target=\"{$CFG->framename}\" href=\"index.php?id=$course->id\">$strscorms</a> ->";
    } else {
        $navigation = "<a target=\"{$CFG->framename}\" href=\"index.php?id=$course->id\">$strscorms</a> ->";
    }

    $pagetitle = strip_tags("$course->shortname: $scorm->name");
    
    add_to_log($course->id, "scorm", "pre-view", "view.php?id=$cm->id", "$scorm->id");
    //
    // Checking if parsed scorm manifest
    //
    if ($scorm->launch == 0) {
	$basedir = $CFG->dataroot."/".$course->id;
       	$scormdir = "/moddata/scorm";
	$scorm->launch = scorm_parse($basedir,$scormdir.$scorm->datadir."/imsmanifest.xml",$scorm->id);
	set_field("scorm","launch",$scorm->launch,"id",$scorm->id);
    }
    //
    // Print the page header
    //
    if (!$cm->visible and !isteacher($course->id)) {
        print_header($pagetitle, "$course->fullname", "$navigation $scorm->name", "", "", true, 
                     update_module_button($cm->id, $course->id, $strscorm), navmenu($course, $cm));
        notice(get_string("activityiscurrentlyhidden"));
    } else {
    	print_header($pagetitle, "$course->fullname","$navigation <a target=\"{$CFG->framename}\" href=\"$ME?id=$cm->id\" title=\"$scorm->summary\">$scorm->name</a>",
       	         "", "", true, update_module_button($cm->id, $course->id, $strscorm), navmenu($course, $cm));
        
    	if (isteacher($course->id)) {
    	    if ($sco_users = get_records_select("scorm_sco_users", "scormid='$scorm->id' GROUP BY userid")) {
        	echo "<p align=right><a target=\"{$CFG->framename}\" href=\"report.php?id=$cm->id\">".get_string("viewallreports","scorm",count($sco_users))."</a></p>";
            } else {
           	echo "<p align=right>".get_string("noreports","scorm")."</p>";
            }
    	}
    	// Print the main part of the page

    	print_heading($scorm->name);

    	print_simple_box(text_to_html($scorm->summary), "CENTER");

    	if (isguest()) {
            print_heading(get_string("guestsno", "scorm"));
            print_footer($course);
            exit;
    	}
        echo "<br />";
        echo "<style type=\"text/css\">.scormlist { list-style-type:none; }</style>\n";
        print_simple_box_start("CENTER");
    	echo "<table>\n";
    	echo "  <tr><th>".get_string("coursestruct","scorm")."</th></tr>\n";
    	echo "  <tr><td nowrap>\n<ul class=\"scormlist\"'>\n";
    	$incomplete = false;
    	if ($scoes = get_records_select("scorm_scoes","scorm='$scorm->id' order by id ASC")){
    	    $level=0;
    	    $sublist=0;
    	    $parents[$level]="/";
    	    foreach ($scoes as $sco) {
    		if ($parents[$level]!=$sco->parent) {
    		    if ($level>0 && $parents[$level-1]==$sco->parent) {
    			echo "  </ul></li>\n";
    			$level--;
    		    } else {
    			$i = $level;
    			$closelist = "";
    			while (($i > 0) && ($parents[$level] != $sco->parent)) {
	    	 	    $closelist .= "  </ul></li>\n";
	    	 	    $i--;
	    	 	}
	    	 	if (($i == 0) && ($sco->parent != "/")) {
	    	 	    echo "  <li><ul id='".$sublist."' class=\"scormlist\"'>\n";
    			    $level++;
    			} else {
    			    echo $closelist;
    			    $level = $i;
    			}
    			$parents[$level]=$sco->parent;
    		    }
    		} 
    		
    		echo "    <li>\n";
    		$nextsco = next($scoes);
    		if (($nextsco !== false) && ($sco->parent != $nextsco->parent) && (($level==0) || (($level>0) && ($nextsco->parent == $sco->identifier)))) {
    		    $sublist++;
    		    echo "      <img src=\"pix/minus.gif\" onClick='expandCollide(this,".$sublist.");'/>\n";
    		} else {
    		    echo "      <img src=\"pix/spacer.gif\" />\n";
    		}
    		if ($sco->launch) {
    		    if ($sco_user=get_record("scorm_sco_users","scoid",$sco->id,"userid",$USER->id)) {
    		    	if ( $sco_user->cmi_core_lesson_status == "") {
    		    	    $sco_user->cmi_core_lesson_status = "not attempted";
    		    	}
    			echo "      <img src=\"pix/".scorm_remove_spaces($sco_user->cmi_core_lesson_status).".gif\" alt=\"".get_string(scorm_remove_spaces($sco_user->cmi_core_lesson_status),"scorm")."\" title=\"".get_string(scorm_remove_spaces($sco_user->cmi_core_lesson_status),"scorm")."\" />\n";
 			if (($sco_user->cmi_core_lesson_status == "not attempted") || ($sco_user->cmi_core_lesson_status == "incomplete")) {
 			    $incomplete = true;
 			}
    		    } else {
    			echo "      <img src=\"pix/notattempted.gif\" alt=\"".get_string("notattempted","scorm")."\" />";
    			$incomplete = true;
    		    }
    		    $score = "";
    		    if ($sco_user->cmi_core_score_raw > 0) {
    			$score = "(".get_string("score","scorm").":&nbsp;".$sco_user->cmi_core_score_raw.")";
    		    }
    		    echo "      &nbsp;<a href=\"javascript:playSCO(".$sco->id.")\">$sco->title</a> $score\n    </li>\n";
    		} else {
		    echo "      &nbsp;$sco->title\n    </li>\n";
		}
	    }
	    for ($i=0;$i<$level;$i++){
	    	 echo "  </ul></li>\n";
	    }
	}
	echo "</ul></td></tr>\n";
    	echo "</table>\n";
    	print_simple_box_end();
    	echo "<form name=\"theform\" method=\"POST\" action=\"playscorm.php?id=$cm->id\">\n";
    	echo "<table align=\"CENTER\">\n<tr>\n<td align=\"center\">";
    	print_string("mode","scorm");
        echo ": <input type=\"radio\" id=\"b\" name=\"mode\" value=\"browse\" /><label for=\"b\">".get_string("browse","scorm")."</label>\n";
        if ($incomplete === true) {
            echo "<input type=\"radio\" id=\"n\" name=\"mode\" value=\"normal\" checked /><label for=\"n\">".get_string("normal","scorm")."</label>\n";
        } else {
            echo "<input type=\"radio\" id=\"r\" name=\"mode\" value=\"review\" checked /><label for=\"r\">".get_string("review","scorm")."</label>\n";
	}
	echo "</td>\n</tr>\n<tr><td align=\"center\">";
	echo '<input type="hidden" name="scoid" />
	<input type="submit" value="'.get_string("entercourse","scorm").'" />';
        echo "\n</td>\n</tr>\n</table>\n</form><br />";
?>
<script language="javascript">
<!--
    function playSCO(scoid,status) {
    	document.theform.scoid.value = scoid;
    	document.theform.submit();
    }
    
    function expandCollide(which,list) {
    	var nn=document.ids?true:false
	var w3c=document.getElementById?true:false
	var beg=nn?"document.ids.":w3c?"document.getElementById(":"document.all.";
	var mid=w3c?").style":".style";
    	
    	if (eval(beg+list+mid+".display") != "none") {
    	    which.src = "pix/plus.gif";
    	    eval(beg+list+mid+".display='none';");
    	} else {
    	    which.src = "pix/minus.gif";
    	    eval(beg+list+mid+".display='block';");
    	}
    	
    }
-->
</script>
<?php
    	print_footer($course);
    }
?>
