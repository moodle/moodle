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

    $pagetitle = strip_tags("$course->shortname: $scorm->name");

    if (!$cm->visible and !isteacher($course->id)) {
        print_header($pagetitle, "$course->fullname", "$navigation $scorm->name", '', '', true, 
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
    $bodyscripts = "onUnload='SCOFinish();'";
    print_header($pagetitle, "$course->fullname",
	"$navigation <a target='{$CFG->framename}' href='view.php?id=$cm->id'>$scorm->name</a>",
	'', '', true, update_module_button($cm->id, $course->id, $strscorm), "", "", $bodyscripts);
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

    <script language="JavaScript" type="text/javascript" src="request.js"></script>
    <script language="JavaScript" type="text/javascript" src="api.php?id=<?php echo $cm->id ?>"></script>
    <table border=1 class="fullscreen" height="90%">
    <tr><td valign="top">
    	<p><?php echo text_to_html($scorm->summary, false, false) ?></p>
    	<p><?php echo $mode == 'browse' ? get_string('browsemode','scorm') : '&nbsp;'; ?></p>
	<table class='generalbox' cellpadding='5' cellspacing='0'>
	    <tr>
	        <th>
	            <div style='float: left;'><?php print_string('coursestruct','scorm') ?></div>
	    	    <div style='float:right;'>
	    	    	<a href='#' onClick='expandCollide(imgmain,0);'>
	    	    	     <img id='imgmain' src="pix/minus.gif" alt="<?php echo $strexpand ?>" title="<?php echo $strexpand ?>"/>
	    	    	</a>
	    	    </div>
	    	</th>
	    </tr>
	    <tr><td nowrap>
		<ul id='0' class='scormlist'>  
<?php
    $incomplete = false;
    if ($scoes = get_records_select('scorm_scoes',"scorm='$scorm->id' AND organization='$currentorg' order by id ASC")){
    	$level=0;
    	$sublist=1;
    	$previd = 0;
    	$nextid = 0; 
    	$parents[$level]="/";
    	foreach ($scoes as $sco) {
    	    if ($parents[$level]!=$sco->parent) {
    		if ($level>0 && $parents[$level-1]==$sco->parent) {
    		    echo "\t\t</ul></li>\n";
    		    $level--;
    		} else {
    		    $i = $level;
    		    $closelist = '';
    		    while (($i > 0) && ($parents[$level] != $sco->parent)) {
	 	    	$closelist .= "\t\t</ul></li>\n";
	 	    	$i--;
	 	    }
	 	    if (($i == 0) && ($sco->parent != $currentorg)) {
	 	    	echo "\t\t<li><ul id='".$sublist."' class='scormlist'>\n";
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
    	    if (($nextsco !== false) && ($sco->parent != $nextsco->parent) && (($level==0) || 
		(($level>0) && ($nextsco->parent == $sco->identifier)))) {
    		$sublist++;
    		
    		echo "<a href='#' onClick='expandCollide(img".$sublist.",".$sublist.");'><img id='img".$sublist."' src='pix/minus.gif' alt='$strexpand' title='$strexpand'/></a>";
    	    } else {
    		echo "<img src='pix/spacer.gif' />";
    	    }
    	    
    	    if ($sco->launch) {
    	        $startbold = '';
    	        $endbold = '';
    	        if ($sco->id == $scoid) {
    		    $startbold = '-> <b>';
    		    $endbold = '</b> <-';
    		    if ($nextsco !== false) {
    			$nextid = $nextsco->id;
    		    } else {
    			$nextid = 0;
    		    }
    	    	} else if ($nextid == 0) {
    	    	    $previd = $sco->id;
    	    	}
    	    	if (($scoid == "") && ($mode != "normal")) {
    	    	    $scoid = $sco->id;
 		    $startbold = '-> <b>';
    		    $endbold = '</b> <-';
    	    	}
    	    	$score = "";
    		if ($user_tracks=scorm_get_tracks($sco->id,$USER->id)) {
    		    if ( $user_tracks->status == '') {
    	    		$user_tracks->status = 'notattempted';
    	    	    }
    	    	    $strstatus = get_string($user_tracks->status,'scorm');
    		    echo "<img src='pix/".$user_tracks->status.".gif' alt='$strstatus' title='$strstatus' />";
 		    if (($user_tracks->status == 'notattempted') || ($user_tracks->status == 'incomplete')) {
 		        if ($scoid == '') {
 			    $incomplete = true;
 			    $scoid = $sco->id;
 			    $startbold = '-> <b>';
    		    	    $endbold = '</b> <-';
 			}
 		    }
 		    if ($user_tracks->score_raw != "") {
    		    	$score = '('.get_string('score','scorm').':&nbsp;'.$user_tracks->score_raw.')';
		    }
    		} else {
    		    if ($sco->scormtype == 'sco') {
    			echo "<img src='pix/notattempted.gif' alt='".get_string('notattempted','scorm')."' />";
    			$incomplete = true;
    		    } else {
    			echo "<img src='pix/asset.gif' alt='".get_string('asset','scorm')."' />";
    		    }
    		}
    		echo "&nbsp;$startbold<a href='javascript:playSCO(".$sco->id.");'>$sco->title</a> $score$endbold</li>\n";
    	    } else {
		echo "&nbsp;$sco->title</li>\n";
	    }
	}
	for ($i=0;$i<$level;$i++) {
	    echo "\t\t</ul></li>\n";
	}
    }
    add_to_log($course->id, 'scorm', 'view', "reviewscorm.php?id=$cm->id&uid=$USER->id&scoid=$scoid", "$scorm->id");
?>
		</ul>
	    </td></tr>
	    <tr><td align="center">
		<form name="navform" method="post" action="playscorm.php?id=<?php echo $cm->id ?>" target="_top">
		    <input name="scoid" type="hidden" />
		    <input name="currentorg" type="hidden" value="<?php echo $currentorg ?>" />
		    <input name="mode" type="hidden" value="<?php echo $mode ?>" />
		    <input name="prev" type="<?php if ($previd == 0) { echo 'hidden'; } else { echo 'button'; } ?>" value="<?php print_string('prev','scorm') ?>" onClick="playSCO(<?php echo $previd ?>);" />
		    <input name="next" type="button" value="<?php if ($nextid == 0) { print_string('exit','scorm'); } else { print_string('next','scorm'); } ?>" onClick="playSCO(<?php echo $nextid ?>)" />
		</form>
	    </td></tr>
	</table>
    </td>
    <td class="fullscreen">
    	<iframe name="main" class="fullscreen" height="100%" src="loadSCO.php?id=<?php echo $cm->id.$scoidstring ?>"></iframe>
    	<script language="javascript">
    	    if (parseInt(navigator.appVersion)>3) {
		if (navigator.appName.indexOf("Microsoft")!=-1) {
		    winH = document.body.offsetHeight-20;
		} else {
		    winH = window.innerHeight-16;
		}
	    }
	    main.height = winH;
    	</script>
    </td></tr>
    </table>
</body>
</html>
