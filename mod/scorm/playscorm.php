<?PHP  // $Id$

/// This page prints a particular instance of scorm

    require_once('../../config.php');
    require_once('lib.php');

    optional_variable($id);    // Course Module ID, or
    optional_variable($a);     // scorm ID

    if ($id) {
        if (! $cm = get_record('course_modules', 'id', $id)) {
            error('Course Module ID was incorrect');
        }
    
        if (! $course = get_record('course', 'id', $cm->course)) {
            error('Course is misconfigured');
        }
    
        if (! $scorm = get_record('scorm', 'id', $cm->instance)) {
            error('Course module is incorrect');
        }

    } else {
        if (! $scorm = get_record('scorm', 'id', $a)) {
            error('Course module is incorrect');
        }
        if (! $course = get_record('course', 'id', $scorm->course)) {
            error('Course is misconfigured');
        }
        if (! $cm = get_coursemodule_from_instance('scorm', $scorm->id, $course->id)) {
            error('Course Module ID was incorrect');
        }
    }

    require_login($course->id, false, $cm);
    
    $strscorms = get_string('modulenameplural', 'scorm');
    $strscorm  = get_string('modulename', 'scorm');
    	
    if ($course->category) {
        $navigation = "<a target=\"{$CFG->framename}\" href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->
                       <a target=\"{$CFG->framename}\" href=\"index.php?id=$course->id\">$strscorms</a> ->";
    } else {
        $navigation = "<a target=\"{$CFG->framename}\" href=\"index.php?id=$course->id\">$strscorms</a> ->";
    }

    $pagetitle = strip_tags("$course->shortname: ".format_string($scorm->name));

    if (!$cm->visible and !isteacher($course->id)) {
        print_header($pagetitle, "$course->fullname", "$navigation ".format_string($scorm->name), '', '', true, 
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
    if (!empty($_POST['mode'])) {
        $mode = $_POST['mode'];
        $modestring = '&mode='.$mode;
    }
    if (!empty($_POST['scoid'])) {
        $scoid = $_POST['scoid'];
        $scoidstring = '&scoid='.$scoid;
    }
    if (!empty($_POST['currentorg'])) {
	$currentorg = $_POST['currentorg'];
	$currentorgstring = '&currentorg='.$currentorg;
    }
    
    $strexpand = get_string('expcoll','scorm');
    
    //
    // Print the page header
    //
    //$bodyscripts = "onUnload='SCOFinish();'";
    print_header($pagetitle, "$course->fullname",
	"$navigation <a target='{$CFG->framename}' href='view.php?id=$cm->id'>".format_string($scorm->name,true)."</a>",
	'', '', true, update_module_button($cm->id, $course->id, $strscorm));
?>
    <style type="text/css">
        .scormlist { 
            list-style-type:none; 
            text-indent:-4ex;
        } 
        
        .fullscreen {
            width: 100%;
            vertical-align: top;
        }
    </style>
    <script language="Javascript">
    <!--
        function playSCO(scoid) {
            if (scoid == 0) {
        	document.location = 'view.php?id=<?php echo $cm->id ?>';
            } else {
        	document.navform.scoid.value=scoid;
        	document.navform.submit();
            }
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

    <table class="fullscreen" height="90%">
    <tr><td valign="top">
    	<p><?php echo format_text($scorm->summary) ?></p>
    	<p><?php echo $mode == 'browse' ? get_string('browsemode','scorm') : '&nbsp;'; ?></p>
	<table class='generalbox' cellpadding='5' cellspacing='0'>
	    <tr>
	        <th>
	            <div style='float: left;'><?php print_string('coursestruct','scorm') ?></div>
	    	    <div style='float:right;'>
	    	    	<a href='#' onClick='expandCollide(imgmain,0);'><img id='imgmain' src="pix/minus.gif" alt="<?php echo $strexpand ?>" title="<?php echo $strexpand ?>"/></a>
	    	    </div>
	    	</th>
	    </tr>
	    <tr><td nowrap>  
<?php
    $sco = scorm_display_structure($scorm,'scormlist',$currentorg,$scoid,$mode,true);
    add_to_log($course->id, 'scorm', 'view', "playscorm.php?id=$cm->id&scoid=$sco->id", "$scorm->id");
    $scoidstring = '&scoid='.$sco->id;

    $SESSION->scorm_scoid = $sco->id;
?>
	    </td></tr>
	    <tr><td align="center">
		<form name="navform" method="post" action="playscorm.php?id=<?php echo $cm->id ?>" target="_top">
		    <input name="scoid" type="hidden" />
		    <input name="currentorg" type="hidden" value="<?php echo $currentorg ?>" />
		    <input name="mode" type="hidden" value="<?php echo $mode ?>" />
		    <input name="prev" type="<?php if ($sco->prev == 0) { echo 'hidden'; } else { echo 'button'; } ?>" value="<?php print_string('prev','scorm') ?>" onClick="playSCO(<?php echo $sco->prev ?>);" />
		    <input name="next" type="<?php if ($sco->next == 0) { echo 'hidden'; } else { echo 'button'; } ?>" value="<?php print_string('next','scorm') ?>" onClick="playSCO(<?php echo $sco->next ?>);" /><br />
		    <input name="exit" type="button" value="<?php print_string('exit','scorm') ?>" onClick="playSCO(0)" />
		</form>
	    </td></tr>
	</table>
    </td>
    <td class="fullscreen" height="90%">
    	<iframe name="main" class="fullscreen" height="640" src="loadSCO.php?id=<?php echo $cm->id.$scoidstring ?>"></iframe>
    </td></tr>
    </table>
    <script language="JavaScript" type="text/javascript" src="request.js"></script>
    <script language="JavaScript" type="text/javascript" src="api.php?id=<?php echo $cm->id.$scoidstring ?>"></script>
</body>
</html>
