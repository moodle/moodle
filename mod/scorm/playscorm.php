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
    if ($frameset == "top") {
    	add_to_log($course->id, "scorm", "view", "playscorm.php?id=$cm->id", "$scorm->id");
	//
	// Print the page header
	//
    	print_header($pagetitle, "$course->fullname",
		"$navigation <a target=\"{$CFG->framename}\" href=\"view.php?id=$cm->id\" title=\"$scorm->summary\">$scorm->name</a>",
		"", "", true, update_module_button($cm->id, $course->id, $strscorm), navmenu($course, $cm, '_top'));
    	
    	echo "<table width=\"100%\">\n    <tr><td align=\"center\">".text_to_html($scorm->summary, true, false)."</td></tr></table>\n";
    	
    	if ($scoes = get_records_select("scorm_scoes","scorm='$scorm->id' order by id ASC")){
    	    $level=0;			
    	    $parents[$level]="/";
    	    foreach ($scoes as $sco) {
    		if ($parents[$level]!=$sco->parent) {
    		    if ($level>0 && $parents[$level-1]==$sco->parent) {
    			$level--;
    		    } else {
    			$level++;
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
	    
    	echo "<table width=\"100%\">\n    <tr>\n";
    	echo "          <td nowrap>
		     <iframe name=\"cmi\" width=\"1\" height=\"1\" src=\"cmi.php?id=$cm->id\" style=\"visibility: hidden;\"></iframe>
		     <form name=\"navform\" method=\"POST\" action=\"playscorm.php?id=$cm->id\" target=\"_top\">
		     	<input name=\"scoid\" type=\"hidden\" />
		     	<input name=\"mode\" type=\"hidden\" value=\"".$_GET["mode"]."\" />
		     	<input name=\"prev\" type=\"button\" value=\"".get_string("prev","scorm")."\" onClick=\"top.changeSco('prev');\" />&nbsp;\n";
	choose_from_menu($options, "courseStructure", "", "", "document.navform.scoid.value=document.navform.courseStructure.options[document.navform.courseStructure.selectedIndex].value;document.navform.submit();");
	echo "     	&nbsp;<input name=\"next\" type=\"button\" value=\"".get_string("next","scorm")."\" onClick=\"top.changeSco('next')\" />
		     </form>
		</td>\n";
	if ($_GET["mode"] == "browse")
	    echo "<td align=\"right\">".get_string("browsemode","scorm")."</td>\n";
    	echo "</tr>\n</table>\n";
	echo "</body>\n</html>\n";
    } else {
    	// 
    	// Frameset
    	//
	if ($_POST["scoid"])
    	    $scoid = "&scoid=".$_POST["scoid"];
	echo "<html>\n";
        echo "<head><title>$course->shortname: $scorm->name</title></head>\n";
        echo "<script id=\"scormAPI\" language=\"JavaScript\" type=\"text/javascript\" src=\"scormAPI.php?id=$cm->id&mode=".$_POST["mode"].$scoid."\"></script>\n";
        echo "<frameset rows=\"$SCORM_FRAME_SIZE,*\" onLoad=\"SCOInitialize();\">\n";
        echo "	    <frame name=\"nav\" src=\"playscorm.php?id=$cm->id&mode=".$_POST["mode"]."&frameset=top\">\n";
        echo "	    <frame name=\"main\" src=\"\">\n";
        echo "</frameset>\n";
        echo "</html>\n";
    }
?>
