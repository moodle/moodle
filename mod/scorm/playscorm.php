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

    if (!$cm->visible and !isteacher($course->id)) {
        print_header($pagetitle, "$course->fullname", "$navigation $scorm->name", "", "", true, 
                     update_module_button($cm->id, $course->id, $strscorm), navmenu($course, $cm));
        notice(get_string("activityiscurrentlyhidden"));
    }
    switch ($frameset) {
        case "top":
    	    add_to_log($course->id, "scorm", "view", "playscorm.php?id=$cm->id", "$scorm->id");
	    //
	    // Print the page header
	    //
    	    print_header($pagetitle, "$course->fullname",
		"$navigation <a target=\"{$CFG->framename}\" href=\"view.php?id=$cm->id\" title=\"$scorm->summary\">$scorm->name</a>",
		"", "", true, update_module_button($cm->id, $course->id, $strscorm), navmenu($course, $cm));
    	
    	    echo "<table width=\"100%\"><tr><td align=\"center\">".text_to_html($scorm->summary, true, false)."</td></tr></table>\n";
	    echo "</body>\n</html>\n";
	break;
	case "left":
	    //
 	    // Print SCORM Nav button
 	    //
 	    if (file_exists("$CFG->dirroot/theme/$CFG->theme/styles.php")) {
        	$styles = $CFG->stylesheet;
            	require_once("$CFG->dirroot/theme/$CFG->theme/config.php");
    	    } else {
            	$styles = "$CFG->wwwroot/theme/standard/styles.php";
            	require_once("$CFG->dirroot/theme/standard/config.php");
    	    }
    	    if ( get_string("thisdirection") == "rtl" ) {
        	$direction = " dir=\"rtl\"";
    	    } else {
        	$direction = " dir=\"ltr\"";
    	    }
    	    $meta = "<meta http-equiv=\"content-type\" content=\"text/html; charset=$encoding\" />\n$meta\n";
 	    echo "<html $direction>\n<head>\n";
 	    echo $meta;
 	    echo "<title>Navigator</title>\n";
 	    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$styles."\" />\n";
 	    //echo "<script language=\"JavaScript\" type=\"text/javascript\" src=\"scormAPI.php?id=$cm->id&mode=".$_GET["mode"].$scoid."\"></script>\n";
 	    echo "</head>\n<body bgcolor=\"".$THEME->body."\" leftmargin=\"0\">\n";
    	    echo "<table width=\"100%\">\n";
	    if ($_GET["mode"] == "browse")
	    	echo "    <tr><td align=\"center\"><b>".get_string("browsemode","scorm")."</b></td></tr>";
	    echo "   <tr><td align=\"center\" width=\"20%\" nowrap>
		     <iframe name=\"cmi\" width=\"1\" height=\"1\" src=\"cmi.php?id=$cm->id\" style=\"visibility: hidden;\"></iframe>
		     <form name=\"navform\" method=\"POST\" action=\"playscorm.php?id=$cm->id\" target=\"_top\">
		     	<input name=\"scoid\" type=\"hidden\" />
		     	<input name=\"mode\" type=\"hidden\" value=\"".$_GET["mode"]."\" />
		     	<input name=\"prev\" type=\"button\" value=\"".get_string("prev","scorm")."\" onClick=\"top.changeSco('prev');\" />&nbsp;
		     	<input name=\"next\" type=\"button\" value=\"".get_string("next","scorm")."\" onClick=\"top.changeSco('next')\" />
		     </form>
		</td></tr>\n";
	    echo "</table>\n";
	    echo "<style type=\"text/css\">.scormlist { list-style-type:none;font-size:small; }</style>\n";
	    echo "<ul class=\"scormlist\">\n";
    	    if ($scoes = get_records_select("scorm_scoes","scorm='$scorm->id' order by id ASC")){
    	    	$level=0;
    	    	$sublist=0;			
    	    	$parents[$level]="/";
    	    	$incomplete=false;
    	    	foreach ($scoes as $sco) {
    		    if ($parents[$level]!=$sco->parent) {
    		    	if ($level>0 && $parents[$level-1]==$sco->parent) {
    			    echo "  </ul>\n";
    			    $level--;
    		    	} else {
    			    echo "  <ul id='".$sublist."' class=\"scormlist\">\n";
    			    $level++;
    			    $parents[$level]=$sco->parent;
    		    	}
    		    }
    		    echo "    <li>";
    		    $nextsco = next($scoes);
    		    if (($nextsco !== false) && ($sco->parent != $nextsco->parent) && ($nextsco->parent != $parents[$level-1])) {
    		    	$sublist++;
    		    	echo "<img src=\"pix/minus.gif\" onClick='expandCollide(this,".$sublist.");'/>";
    		    } else
    		    	echo "<img src=\"pix/spacer.gif\" />";
    		    if ($sco->launch) {
    		    	if ($sco_user=get_record("scorm_sco_users","scoid",$sco->id,"userid",$USER->id)) {
    		    	    if ( $sco_user->cmi_core_lesson_status == "")
    		    		$sco_user->cmi_core_lesson_status = "not attempted";
    			    echo "<img src=\"pix/".scorm_remove_spaces($sco_user->cmi_core_lesson_status).".gif\" alt=\"".get_string(scorm_remove_spaces($sco_user->cmi_core_lesson_status),"scorm")."\" title=\"".get_string(scorm_remove_spaces($sco_user->cmi_core_lesson_status),"scorm")."\" />";
    			    switch ($sco_user->cmi_core_lesson_status) {
    				case "not attempted":
    				case "incomplete":
    				case "browsed":
    				    $incomplete = true;
    			    }
    		    	} else {
    			    echo "<img src=\"pix/notattempted.gif\" alt=\"".get_string("notattempted","scorm")."\" />";
    			    $incomplete = true;
    		    	}
    		    	echo "&nbsp;<a href=\"javascript:playSCO(".$sco->id.")\">$sco->title</a></li>\n";
    		    } else {
		    	echo "&nbsp;$sco->title</li>\n";
		    }
	    	}
	    	
	    	for ($i=0;$i<$level;$i++){
	    	     echo "  </ul>\n";
	    	}
	    }
	    echo "</ul></p>\n";
	    echo "<script language=\"javascript\">
<!--
    function playSCO(scoid,status) {
    	document.navform.scoid.value = scoid;
    	document.navform.submit();
    }
    
    function expandCollide(which,list) {
    	var nn=document.ids?true:false
	var w3c=document.getElementById?true:false
	var beg=nn?\"document.ids.\":w3c?\"document.getElementById(\":\"document.all.\";
	var mid=w3c?\").style\":\".style\";
    	
    	if (eval(beg+list+mid+\".display\") != \"none\") {
    	    which.src = \"pix/plus.gif\";
    	    eval(beg+list+mid+\".display='none';\");
    	} else {
    	    which.src = \"pix/minus.gif\";
    	    eval(beg+list+mid+\".display='block';\");
    	}
    	
    }	
    
-->
</script>\n";
	    echo "</body>\n</html>\n";
	break;
   	default:
    	    // 
    	    // Frameset
    	    //
	    if ($_POST["scoid"])
    	    	$scoid = "&scoid=".$_POST["scoid"];
	    echo "<html>\n";
            echo "<head><title>$course->shortname: $scorm->name</title></head>\n";
            echo "<script id=\"scormAPI\" language=\"JavaScript\" type=\"text/javascript\" src=\"scormAPI.php?id=$cm->id&mode=".$_GET["mode"].$scoid."\"></script>\n";
            echo "<frameset rows=\"$SCORM_TOP_FRAME_SIZE,*\">\n";
            echo "	    <frame name=\"upper\" src=\"playscorm.php?id=$cm->id&mode=".$_POST["mode"]."&frameset=top\">\n";
            echo "	    <frameset cols=\"$SCORM_LEFT_FRAME_SIZE,*\" onload=\"SCOInitialize();\">\n";
            echo "	          <frame name=\"nav\" src=\"playscorm.php?id=$cm->id&mode=".$_POST["mode"]."&frameset=left\">\n";
            echo "	          <frame name=\"main\" src=\"\">\n";
            echo "      </frameset>\n";
            echo "</frameset>\n";
	    echo "</html>\n";
	break;
    }
?>
