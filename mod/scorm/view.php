<?php  // $Id$

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

    require_login($course->id, false, $cm);

    
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
    // Print the page header
    //
    if (!$cm->visible and !isteacher($course->id)) {
        print_header($pagetitle, "$course->fullname", "$navigation $scorm->name", "", "", true, 
        	     update_module_button($cm->id, $course->id, $strscorm), navmenu($course, $cm));
        notice(get_string("activityiscurrentlyhidden"));
    } else {
    	print_header($pagetitle, "$course->fullname","$navigation <a target=\"{$CFG->framename}\" href=\"view.php?id=$cm->id\">$scorm->name</a>",
       	         "", "", true, update_module_button($cm->id, $course->id, $strscorm), navmenu($course, $cm));
        
    	if (isteacher($course->id)) {
    	    if ($sco_users = get_records_select("scorm_scoes_track", "scormid='$scorm->id' GROUP BY userid")) {
        	echo "<p align=\"right\"><a target=\"{$CFG->framename}\" href=\"report.php?id=$cm->id\">".get_string("viewallreports","scorm",count($sco_users))."</a></p>";
            } else {
           	echo "<p align=\"right\">".get_string("noreports","scorm")."</p>";
            }
    	}
    	// Print the main part of the page

    	print_heading($scorm->name);

    	print_simple_box(format_text($scorm->summary), 'center', '70%', '', 5, 'generalbox', 'intro');

    	if (isguest()) {
            print_heading(get_string("guestsno", "scorm"));
            print_footer($course);
            exit;
    	}
        echo "<br />";
        $liststyle = "style=\"list-style-type:none;\"";
        print_simple_box_start("center");
    	echo "<table>\n";
    	echo "  <tr><th>".get_string("coursestruct","scorm")."</th></tr>\n";
	$organization = $scorm->launch;
	if ($orgs = get_records_select_menu('scorm_scoes',"scorm='$scorm->id' AND organization='' AND launch=''",'id','id,title')) {
	    if (count($orgs) > 1) {
		if (isset($_POST['organization'])) {
		    $organization = $_POST['organization'];
		}
		echo "<tr><td align='center'><form name='changeorg' method='post' action='view.php?id=$cm->id'>".get_string('organizations','scorm').": \n";
		choose_from_menu($orgs, 'organization', "$organization", '','submit()');
		echo "</form></td></tr>\n"; 
	    }
	}
	$orgidentifier = '';
	if ($org = get_record('scorm_scoes','id',$organization)) {
	    if (($org->organization == '') && ($org->launch == '')) {
	    	$orgidentifier = $org->identifier;
	    } else {
	    	$orgidentifier = $org->organization;
	    }
	}
    	echo "  <tr><td nowrap=\"nowrap\">\n<ul $liststyle>\n";
    	$incomplete = false;
    	if ($scoes = get_records_select("scorm_scoes","scorm='$scorm->id' AND organization='$orgidentifier' order by id ASC")){
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
	    	 	if (($i == 0) && ($sco->parent != $orgidentifier)) {
	    	 	    echo "  <li><ul id='s".$sublist."' $liststyle>\n";
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
    		    echo "      <img src=\"pix/minus.gif\" onclick='expandCollide(this,\"s".$sublist."\");' alt=\"-\" title=\"".get_string('collide','scorm')."\" />\n";
    		} else {
    		    echo "      <img src=\"pix/spacer.gif\" alt=\" \" />\n";
    		}
    		//print_r ($sco->title);
    		if ($sco->title == "") {
    		    $sco->title = get_string('notitle','scorm');
    		    //echo '-'.$sco->title.'-';
    		}
    		if ($sco->launch) {
    		    $score = "";
    		    if ($user_tracks=scorm_get_tracks($sco->id,$USER->id)) {
    		    	if ( $user_tracks->status == "") {
    		    	    $user_tracks->status = "notattempted";
    		    	}
    		    	$strstatus = get_string($user_tracks->status,'scorm');
    			echo "<img src='pix/".$user_tracks->status.".gif' alt='$strstatus' title='$strstatus' />";
    			if (($user_tracks->status == "notattempted") || ($user_tracks->status == "incomplete")) {
 			    $incomplete = true;
 			}
 			if ($user_tracks->score_raw != "") {
    			    $score = "(".get_string("score","scorm").":&nbsp;".$user_tracks->score_raw.")";
    		    	}
    		    } else {
    			if ($sco->scormtype == 'sco') {
    			    echo "      <img src=\"pix/notattempted.gif\" alt=\"".get_string("notattempted","scorm")."\" title=\"".get_string("notattempted","scorm")."\" />";
    			    $incomplete = true;
    			} else {
    			    echo "      <img src=\"pix/asset.gif\" alt=\"".get_string("asset","scorm")."\" title=\"".get_string("asset","scorm")."\" />";
    			}
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
    	echo "<form name=\"theform\" method=\"post\" action=\"playscorm.php?id=$cm->id\">\n";
    	echo "<table align=\"center\">\n<tr>\n<td align=\"center\">";
    	print_string("mode","scorm");
        echo ": <input type=\"radio\" id=\"b\" name=\"mode\" value=\"browse\" /><label for=\"b\">".get_string("browse","scorm")."</label>\n";
        if ($incomplete === true) {
            echo "<input type=\"radio\" id=\"n\" name=\"mode\" value=\"normal\" checked=\"checked\" /><label for=\"n\">".get_string("normal","scorm")."</label>\n";
        } else {
            echo "<input type=\"radio\" id=\"r\" name=\"mode\" value=\"review\" checked=\"checked\" /><label for=\"r\">".get_string("review","scorm")."</label>\n";
	}
	echo "</td>\n</tr>\n<tr><td align=\"center\">";
	echo '<input type="hidden" name="scoid" />
	<input type="hidden" name="currentorg" value="'.$orgidentifier.'" />
	<input type="submit" value="'.get_string("entercourse","scorm").'" />';
        echo "\n</td>\n</tr>\n</table>\n</form><br />";
?>
<script language="javascript" type="text/javascript">
<!--
    function playSCO(scoid) {
    	document.theform.scoid.value = scoid;
    	document.theform.submit();
    }
    
    function expandCollide(which,list) {
    	var nn=document.ids?true:false
	var w3c=document.getElementById?true:false
	var beg=nn?"document.ids.":w3c?"document.getElementById('":"document.all.";
	var mid=w3c?"').style":".style";
	
    	if (eval(beg+list+mid+".display") != "none") {
    	    which.src = "pix/plus.gif";
    	    which.alt = "+";
    	    which.title = "<?php echo get_string('expand','scorm') ?>";
    	    eval(beg+list+mid+".display='none';");
    	} else {
    	    which.src = "pix/minus.gif";
    	    which.alt = "-";
    	    which.title = "<?php echo get_string('collide','scorm') ?>";
    	    eval(beg+list+mid+".display='block';");
    	}
    	
    }
-->
</script>
<?php
    	print_footer($course);
    }
?>
