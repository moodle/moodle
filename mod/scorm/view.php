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
    	echo "  <tr><td nowrap=\"nowrap\">\n";
    	$incomplete = scorm_display_structure($scorm,'scormlist',$orgidentifier);
	echo "</td></tr>\n";
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
<style type="text/css">
        .scormlist { 
            list-style-type:none; 
        } 
</style>
<script language="javascript" type="text/javascript">
<!--
    function playSCO(scoid) {
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
