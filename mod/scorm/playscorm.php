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

    if (!$cm->visible and !isteacher($course->id)) {
        print_header($pagetitle, "$course->fullname", "$navigation $scorm->name", "", "", true, 
                     update_module_button($cm->id, $course->id, $strscorm), navmenu($course, $cm));
        notice(get_string("activityiscurrentlyhidden"));
    }
    
    //
    // Checkin script parameters
    //
    $mode = '';
    $scoid='';
    $currentorg='';
    $modestring = '';
    $scoidstring = '';
    $currentorgstring = '';
    if (!empty($_POST["mode"])) {
        $mode = $_POST["mode"];
        $modestring = '&mode='.$mode;
    }
    if (!empty($_POST["scoid"])) {
        $scoid = $_POST["scoid"];
        $scoidstring = '&scoid='.$scoid;
    }
    if (!empty($_POST['currentorg'])) {
	$currentorg = $_POST['currentorg'];
	$currentorgstring = '&currentorg='.$currentorg;
    }
    
    add_to_log($course->id, "scorm", "view", "playscorm.php?id=$cm->id", "$scorm->id");
    //
    // Print the page header
    //
    $bodyscripts = "onUnload='SCOFinish(); closeMain();'";
    print_header($pagetitle, "$course->fullname",
	"$navigation <a target=\"{$CFG->framename}\" href=\"view.php?id=$cm->id\">$scorm->name</a>",
	"", "", true, update_module_button($cm->id, $course->id, $strscorm), "", "", $bodyscripts);
?>
    <style type="text/css">
        .scormlist { 
            list-style-type:none; 
            text-indent:-4ex;
        } 
    </style>
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

    <script language="JavaScript" type="text/javascript" src="request.js"></script>
    <script language="JavaScript" type="text/javascript" src="api.php?id=<?php echo $cm->id ?>"></script>
    <table width="100%">
    <tr><td valign="top">
    	<p><?php echo text_to_html($scorm->summary, false, false) ?></p>
    	<p><?php echo $mode == "browse" ? get_string("browsemode","scorm") : '&nbsp;'; ?></p>
	<?php print_simple_box_start(); ?>
	<table>
	    <tr><th><?php print_string("coursestruct","scorm") ?></th></tr>
	    <tr><td nowrap>
		<ul class="scormlist">  
<?php
    $incomplete = false;
    if ($scoes = get_records_select("scorm_scoes","scorm='$scorm->id' AND organization='$currentorg' order by id ASC")){
    	$level=0;
    	$sublist=0;
    	$parents[$level]="/";
    	foreach ($scoes as $sco) {
    	    if ($parents[$level]!=$sco->parent) {
    		if ($level>0 && $parents[$level-1]==$sco->parent) {
    		    echo "\t\t</ul></li>\n";
    		    $level--;
    		} else {
    		    $i = $level;
    		    $closelist = "";
    		    while (($i > 0) && ($parents[$level] != $sco->parent)) {
	 	    	$closelist .= "\t\t</ul></li>\n";
	 	    	$i--;
	 	    }
	 	    if (($i == 0) && ($sco->parent != $currentorg)) {
	 	    	echo "\t\t<li><ul id='".$sublist."' class=\"scormlist\"'>\n";
    		    	$level++;
    		    } else {
    		    	echo $closelist;
    		    	$level = $i;
    		    }
    		    $parents[$level]=$sco->parent;
    		}
    	    }
    	    echo "\t\t<li>";
    	    $nextsco = next($scoes);
    	    if (($nextsco !== false) && ($sco->parent != $nextsco->parent) && (($level==0) || (($level>0) && ($nextsco->parent == $sco->identifier)))) {
    		$sublist++;
    		echo "<img id='img".$sublist."' src=\"pix/minus.gif\" onClick='expandCollide(this,".$sublist.");'/>";
    	    } else {
    		echo "<img src=\"pix/spacer.gif\" />";
    	    }
    	    
    	    if ($sco->launch) {
    	        $startbold = '';
    	        $endbold = '';
    	        if ($sco->id == $scoid) {
    		    $startbold = '-> <b>';
    		    $endbold = '</b> <-';
    	    	}
    	    	if (($scoid == "") && ($mode != "normal")) {
    	    	    $scoid = $sco->id;
 		    $startbold = '-> <b>';
    		    $endbold = '</b> <-';
    	    	}
    	    	$score = "";
    		if ($sco_user=get_record("scorm_sco_users","scoid",$sco->id,"userid",$USER->id)) {
    		    if ( $sco_user->cmi_core_lesson_status == "") {
    	    		$sco_user->cmi_core_lesson_status = "not attempted";
    	    	    }
    		    echo "<img src=\"pix/".scorm_remove_spaces($sco_user->cmi_core_lesson_status).".gif\" alt=\"".get_string(scorm_remove_spaces($sco_user->cmi_core_lesson_status),"scorm")."\" title=\"".get_string(scorm_remove_spaces($sco_user->cmi_core_lesson_status),"scorm")."\" />\n";
 		    if (($sco_user->cmi_core_lesson_status == "not attempted") || ($sco_user->cmi_core_lesson_status == "incomplete")) {
 		        if ($scoid == "") {
 			    $incomplete = true;
 			    $scoid = $sco->id;
 			    $startbold = '-> <b>';
    		    	    $endbold = '</b> <-';
 			}
 		    }
 		    if ($sco_user->cmi_core_score_raw > 0) {
    		    	$score = "(".get_string("score","scorm").":&nbsp;".$sco_user->cmi_core_score_raw.")";
		    }
    		} else {
    		    if ($sco->type == 'sco') {
    			echo "<img src=\"pix/notattempted.gif\" alt=\"".get_string("notattempted","scorm")."\" />";
    			$incomplete = true;
    		    } else {
    			echo "<img src=\"pix/asset.gif\" alt=\"".get_string("asset","scorm")."\" />";
    		    }
    		}
    		echo "&nbsp;$startbold<a href=\"javascript:playSCO(".$sco->id.");\">$sco->title</a> $score$endbold</li>\n";
    	    } else {
		echo "&nbsp;$sco->title</li>\n";
	    }
	}
	for ($i=0;$i<$level;$i++) {
	    echo "\t\t</ul></li>\n";
	}
    }
?>
		</ul>
	    </td></tr>
	    <tr><td align="center">
		<form name="navform" method="post" action="playscorm.php?id=<?php echo $cm->id ?>" target="_top">
		    <input name="scoid" type="hidden" />
		    <input name="currentorg" type="hidden" value="<?php echo $currentorg ?>" />
		    <input name="mode" type="hidden" value="<?php echo $mode ?>" />
		    <input name="prev" type="button" value="<?php print_string('prev','scorm') ?>" onClick="top.changeSco('previous');" />
		    <input name="next" type="button" value="<?php print_string('next','scorm') ?>" onClick="top.changeSco('continue')" />
		</form>
	    </td></tr>
	</table>
	<?php print_simple_box_end(); ?>
    </td>
    <td width="100%">
    	<iframe name="main" height="500" width="100%" src="loadSCO.php?id=<?php echo $cm->id.$scoidstring ?>"></iframe>
    </td></tr>
    </table>
<?php    
    /*$popuplocation = '';
    if (isset($_COOKIE["SCORMpopup"])) {
       	$popuplocation = $_COOKIE["SCORMpopup"];
    }
    echo "<script language=\"javascript\">\n";
    echo "\t    top.main = window.open('loadSCO.php?id=$cm->id".$scoidstring."','main','$scorm->popup$popuplocation');\n";
    echo "</script>\n"; */
?>
</body>
</html>
