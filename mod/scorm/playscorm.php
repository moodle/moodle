<?PHP  // $Id$

/// This page prints a particular instance of scorm
/// (Replace scorm with the name of your module)

    require_once("../../config.php");
    require_once("lib.php");

    optional_variable($id);    // Course Module ID, or
    optional_variable($a);     // scorm ID
    optional_variable($frameset, "");

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

    if (!$cm->visible and !isteacher($course->id)) {
        print_header($pagetitle, "$course->fullname", "$navigation $scorm->name", "", "", true, 
                     update_module_button($cm->id, $course->id, $strscorm), navmenu($course, $cm));
        notice(get_string("activityiscurrentlyhidden"));
    }
    
    if (!empty($_POST["scoid"]))
    	    $scoid = "&scoid=".$_POST["scoid"];
    if (($scorm->popup != "") && (!empty($_POST["mode"])))
    	$mode = $_POST["mode"];
    if (($scorm->popup == "") && (!empty($_GET["mode"])))
    	$mode = $_GET["mode"];
    
    if (($frameset == "top") || ($scorm->popup != "")) {
    	add_to_log($course->id, "scorm", "view", "playscorm.php?id=$cm->id", "$scorm->id");
	//
	// Print the page header
	//
    	print_header($pagetitle, "$course->fullname",
		"$navigation <a target=\"{$CFG->framename}\" href=\"view.php?id=$cm->id\" title=\"$scorm->summary\">$scorm->name</a>",
		"", "", true, update_module_button($cm->id, $course->id, $strscorm), navmenu($course, $cm, '_top'));
    	
    	echo "<table width=\"100%\">\n    <tr><td align=\"center\">".text_to_html($scorm->summary, true, false)."</td>\n";
    	if ($mode == "browse")
	    echo "<td align=\"right\" width=\"10%\" nowrap>".get_string("browsemode","scorm")."</td>\n";
    	echo "     </tr>\n</table>\n";
    	
    	if ($scorm->popup != "") {
    	    echo "<script id=\"scormAPI\" language=\"JavaScript\" type=\"text/javascript\" src=\"scormAPI.php?id=$cm->id&mode=".$mode.$scoid."\"></script>\n";
	    $currentSCO = "";
            if (!empty($_POST['scoid']))
                $currentSCO = $_POST['scoid'];
        ?>
            <br />
            <script language="Javascript">
            <!--
                function playSCO(scoid) {
            	    document.navform.scoid.value=scoid;
            	    document.navform.submit();
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
            <style type="text/css">
                .scormlist { list-style-type:none; }
            </style>
        <?php
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
    		        $startbold = '';
    		        $endbold = '';
    		        if ($sco->id == $currentSCO) {
    			    $startbold = '-> <b>';
    			    $endbold = '</b> <-';
    		    	}
    		    	if (($currentSCO == "") && ($mode != "normal")) {
    		    	    $currentSCO = $sco->id;
 			    $startbold = '-> <b>';
    			    $endbold = '</b> <-';
    		    	}
    			if ($sco_user=get_record("scorm_sco_users","scoid",$sco->id,"userid",$USER->id)) {
    			    if ( $sco_user->cmi_core_lesson_status == "")
    		    		$sco_user->cmi_core_lesson_status = "not attempted";
    			    echo "      <img src=\"pix/".scorm_remove_spaces($sco_user->cmi_core_lesson_status).".gif\" alt=\"".get_string(scorm_remove_spaces($sco_user->cmi_core_lesson_status),"scorm")."\" title=\"".get_string(scorm_remove_spaces($sco_user->cmi_core_lesson_status),"scorm")."\" />\n";
 			    if (($sco_user->cmi_core_lesson_status == "not attempted") || ($sco_user->cmi_core_lesson_status == "incomplete")) {
 			        if ($currentSCO == "") {
 				    $incomplete = true;
 				    $currentSCO = $sco->id;
 				    $startbold = '-> <b>';
    			    	    $endbold = '</b> <-';
 				}
 			    }
    			} else {
    			    echo "      <img src=\"pix/notattempted.gif\" alt=\"".get_string("notattempted","scorm")."\" />";
    			    $incomplete = true;
    			}
    			$score = "";
    			if ($sco_user->cmi_core_score_raw > 0)
    			    $score = "(".get_string("score","scorm").":&nbsp;".$sco_user->cmi_core_score_raw.")";
    		        echo "      &nbsp;$startbold<a href=\"javascript:playSCO(".$sco->id.");\">$sco->title</a> $score$endbold\n    </li>\n";
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
    	    
        }
	    
    	echo "<table width=\"100%\">\n    <tr>\n";
    	echo "          <td align=\"center\" nowrap>
		     <iframe name=\"cmi\" width=\"1\" height=\"1\" src=\"cmi.php?id=$cm->id\" style=\"visibility: hidden\"></iframe>
		     <form name=\"navform\" method=\"POST\" action=\"playscorm.php?id=$cm->id\" target=\"_top\">
		     	<input name=\"scoid\" type=\"hidden\" />
		     	<input name=\"mode\" type=\"hidden\" value=\"".$mode."\" />
		     	<input name=\"prev\" type=\"button\" value=\"".get_string("prev","scorm")."\" onClick=\"top.changeSco('previous');\" />&nbsp;\n";
		     	
	if ($scorm->popup == "") {
	    if ($scoes = get_records_select("scorm_scoes","scorm='$scorm->id' order by id ASC")){
    	    	$level=0;			
    	    	$parents[$level]="/";
    	    	foreach ($scoes as $sco) {
    		    if ($parents[$level]!=$sco->parent) {
    			if ($level>0 && $parents[$level-1]==$sco->parent) {
    			    $level--;
    			} else {
    			    $i = $level;
    			    while (($i > 0) && ($parents[$level] != $sco->parent)) {
	    	 	    	$i--;
	    	 	    }
	    	 	    if (($i == 0) && ($sco->parent != "/")) {
    			    	$level++;
    			    } else {
    			    	$level = $i;
    			    }
    			    $parents[$level]=$sco->parent;
    			}
    		    }
    		    $indenting = "";
    		    for ($i=0;$i<$level;$i++) {
    		        $indenting .= "-";
    		    }
    		    $options[$sco->id] = $indenting."&gt; ".$sco->title;
	    	}
	    }
	    choose_from_menu($options, "courseStructure", "", "", "document.navform.scoid.value=document.navform.courseStructure.options[document.navform.courseStructure.selectedIndex].value;document.navform.submit();");
	}
	echo "     	&nbsp;<input name=\"next\" type=\"button\" value=\"".get_string("next","scorm")."\" onClick=\"top.changeSco('continue')\" />\n";
	echo "	     </form>
		</td>\n";
	
    	echo "</tr>\n</table>\n";
    	
    	if ($scorm->popup != "") {
    	?>
    	    <script language="Javascript">
		SCOInitialize();
            </script>
        <?php
        }
        
	echo "</body>\n</html>\n";
    } else {
        if ($scorm->popup == "") {
    	    // 
    	    // Frameset
    	    //
    	    echo "<html>\n";
            echo "<head><title>$course->shortname: $scorm->name</title></head>\n";
            echo "<script id=\"scormAPI\" language=\"JavaScript\" type=\"text/javascript\" src=\"scormAPI.php?id=$cm->id&mode=".$mode.$scoid."\"></script>\n";
	    echo "<frameset rows=\"$CFG->scorm_framesize,*\" onLoad=\"SCOInitialize();\">\n";
            echo "\t    <frame name=\"navigation\" src=\"playscorm.php?id=$cm->id&mode=".$mode."&frameset=top\">\n";
            echo "\t    <frame name=\"main\" src=\"\">\n";
            echo "</frameset>\n";
            echo "</html>\n";
        }
    }
?>
