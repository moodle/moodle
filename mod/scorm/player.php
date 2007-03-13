<?PHP  // $Id$

/// This page prints a particular instance of aicc/scorm package

    require_once('../../config.php');
    require_once('locallib.php');

    //
    // Checkin' script parameters
    //
    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);         // scorm ID
    $scoid = required_param('scoid', PARAM_INT);  // sco ID
    $mode = optional_param('mode', 'normal', PARAM_ALPHA); // navigation mode
    $currentorg = optional_param('currentorg', '', PARAM_RAW); // selected organization
    $newattempt = optional_param('newattempt', 'off', PARAM_ALPHA); // the user request to start a new attempt
	
	$scoid=$_GET['scoid'];
	
	
	//$scoid=$_POST['scoid'];
	//echo 'SCOID'.$scoid;
	if ($sco1 = get_record("scorm_scoes", "id", $scoid,"parent",'/')) {
           $scoid++;
    }

    if (!empty($id)) {
        if (! $cm = get_coursemodule_from_id('scorm', $id)) {
            error("Course Module ID was incorrect");
        }
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
        if (! $scorm = get_record("scorm", "id", $cm->instance)) {
            error("Course module is incorrect");
        }
    } else if (!empty($a)) {
        if (! $scorm = get_record("scorm", "id", $a)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $scorm->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    } else {
        error('A required parameter is missing');
    }

    require_login($course->id, false, $cm);

    $strscorms = get_string('modulenameplural', 'scorm');
    $strscorm  = get_string('modulename', 'scorm');
    $strpopup = get_string('popup','scorm');

    if ($course->id != SITEID) {
        $navigation = "<a $CFG->frametarget href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
        if ($scorms = get_all_instances_in_course('scorm', $course)) {
            // The module SCORM/AICC activity with the first id is the course  
            $firstscorm = current($scorms);
            if (!(($course->format == 'scorm') && ($firstscorm->id == $scorm->id))) {
                $navigation .= "<a $CFG->frametarget href=\"index.php?id=$course->id\">$strscorms</a> ->";
            }
        }
    } else {
        $navigation = "<a $CFG->frametarget href=\"index.php?id=$course->id\">$strscorms</a> ->";
    }

    $pagetitle = strip_tags("$course->shortname: ".format_string($scorm->name));

    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_COURSE,$course->id))) {
        print_header($pagetitle, $course->fullname,
                 "$navigation <a $CFG->frametarget href=\"view.php?id=$cm->id\">".format_string($scorm->name,true)."</a>",
                 '', '', true, update_module_button($cm->id, $course->id, $strscorm), '', false);
        notice(get_string("activityiscurrentlyhidden"));
    }

    //
    // TOC processing
    //
    $scorm->version = strtolower(clean_param($scorm->version, PARAM_SAFEDIR));   // Just to be safe
    if (!file_exists($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'lib.php')) {
        $scorm->version = 'scorm_12';
    }
    require_once($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'lib.php');
    $attempt = scorm_get_last_attempt($scorm->id, $USER->id);
    if (($newattempt=='on') && (($attempt < $scorm->maxattempt) || ($scorm->maxattempt == 0))) {
        $attempt++;
        $mode = 'normal';
    }
    $attemptstr = '&amp;attempt=' . $attempt;

    $result = scorm_get_toc($USER,$scorm,'structurelist',$currentorg,$scoid,$mode,$attempt,true);
    $sco = $result->sco;

    if (($mode == 'browse') && ($scorm->hidebrowse == 1)) {
       $mode = 'normal';
    }
    if ($mode != 'browse') {
        if ($trackdata = scorm_get_tracks($sco->id,$USER->id,$attempt)) {
            if (($trackdata->status == 'completed') || ($trackdata->status == 'passed') || ($trackdata->status == 'failed')) {
                $mode = 'review';
            } else {
                $mode = 'normal';
            }
        } else {
            $mode = 'normal';
        }
    }

    add_to_log($course->id, 'scorm', 'view', "player.php?id=$cm->id&scoid=$sco->id", "$scorm->id");

    $scoidstr = '&amp;scoid='.$sco->id;
    $scoidpop = '&scoid='.$sco->id;
    $modestr = '&amp;mode='.$mode;
    if ($mode == 'browse') {
        $modepop = '&mode='.$mode;
    } else {
        $modepop = '';
    }
    $orgstr = '&currentorg='.$currentorg;

    $SESSION->scorm_scoid = $sco->id;
    $SESSION->scorm_status = 'Not Initialized';
    $SESSION->scorm_mode = $mode;
    $SESSION->scorm_attempt = $attempt;

    //
    // Print the page header
    //
    $bodyscript = '';
    if ($scorm->popup == 1) {
        $bodyscript = 'onunload="main.close();"';
    }

    print_header($pagetitle, $course->fullname,
                 "$navigation <a $CFG->frametarget href=\"view.php?id=$cm->id\">".format_string($scorm->name,true)."</a>",
                 '', '', true, update_module_button($cm->id, $course->id, $strscorm), '', false, $bodyscript);
    if ($sco->scormtype == 'sco') {
?>
    <script type="text/javascript" src="request.js"></script>
    <script type="text/javascript" src="api.php?id=<?php echo $cm->id.$scoidstr.$modestr.$attemptstr ?>"></script>
<?php
    }
    if (($sco->previd != 0) && ((!isset($sco->previous)) || ($sco->previous == 0))) {
        $scostr = '&scoid='.$sco->previd;
        echo '    <script type="text/javascript">'."\n//<![CDATA[\n".'var prev="'.$CFG->wwwroot.'/mod/scorm/player.php?id='.$cm->id.$orgstr.$modepop.$scostr."\";\n//]]>\n</script>\n";
    } else {
        echo '    <script type="text/javascript">'."\n//<![CDATA[\n".'var prev="'.$CFG->wwwroot.'/mod/scorm/view.php?id='.$cm->id."\";\n//]]>\n</script>\n";
    }
    if (($sco->nextid != 0) && ((!isset($sco->next)) || ($sco->next == 0))) {
        $scostr = '&scoid='.$sco->nextid;
        echo '    <script type="text/javascript">'."\n//<![CDATA[\n".'var next="'.$CFG->wwwroot.'/mod/scorm/player.php?id='.$cm->id.$orgstr.$modepop.$scostr."\";\n//]]>\n</script>\n";
    } else {
        echo '    <script type="text/javascript">'."\n//<![CDATA[\n".'var next="'.$CFG->wwwroot.'/mod/scorm/view.php?id='.$cm->id."\";\n//]]>\n</script>\n";
    }
?>
    <div id="scormpage">
<?php  
    if ($scorm->hidetoc == 0) {
?>
        <div id="tocbox" class="generalbox">
            <div id="tochead" class="header"><?php print_string('contents','scorm') ?></div>
            <div id="toctree">
            <?php echo $result->toc; ?>
            </div>
        </div>
<?php
        $class = ' class="toc"';
    } else {
        $class = ' class="no-toc"';
    }
?>
        <div id="scormbox"<?php echo $class ?>>
<?php
    // This very big test check if is necessary the "scormtop" div
    if (
           ($mode != 'normal') ||  // We are not in normal mode so review or browse text will displayed
           (
               ($scorm->hidenav == 0) &&  // Teacher want to display navigation links
               (
                   (
                       ($sco->previd != 0) &&  // This is not the first learning object of the package
                       ((!isset($sco->previous)) || ($sco->previous == 0))   // Moodle must manage the previous link
                   ) || 
                   (
                       ($sco->nextid != 0) &&  // This is not the last learning object of the package
                       ((!isset($sco->next)) || ($sco->next == 0))       // Moodle must manage the next link
                   ) 
               )
           ) || ($scorm->hidetoc == 2)      // Teacher want to display toc in a small dropdown menu 
       ) {
?>
            <div id="scormtop">
        <?php echo $mode == 'browse' ? '<div id="scormmode" class="left">'.get_string('browsemode','scorm')."</div>\n" : ''; ?>
        <?php echo $mode == 'review' ? '<div id="scormmode" class="left">'.get_string('reviewmode','scorm')."</div>\n" : ''; ?>
<?php
        if (($scorm->hidenav == 0) || ($scorm->hidetoc == 2)) {
?>
                <div id="scormnav" class="right">
        <?php
            $orgstr = '&amp;currentorg='.$currentorg;
            if (($scorm->hidenav == 0) && ($sco->previd != 0) && ((!isset($sco->previous)) || ($sco->previous == 0))) {
                /// Print the prev LO link
                $scostr = '&amp;scoid='.$sco->previd;
                $url = $CFG->wwwroot.'/mod/scorm/player.php?id='.$cm->id.$orgstr.$modestr.$scostr;
                echo '<a href="'.$url.'">&lt; '.get_string('prev','scorm').'</a>';
            }
            if ($scorm->hidetoc == 2) {
                echo $result->tocmenu;
            }
            if (($scorm->hidenav == 0) && ($sco->nextid != 0) && ((!isset($sco->next)) || ($sco->next == 0))) {
                /// Print the next LO link
                $scostr = '&amp;scoid='.$sco->nextid;
                $url = $CFG->wwwroot.'/mod/scorm/player.php?id='.$cm->id.$orgstr.$modestr.$scostr;
                echo '            &nbsp;<a href="'.$url.'">'.get_string('next','scorm').' &gt;</a>';
            }
        ?>

                </div>
<?php
        } 
?>
            </div>
<?php
    } // The end of the very big test
?>
            <div id="scormobject" class="right">
                <noscript>
                    <div id="noscript">
                        <?php print_string('noscriptnoscorm','scorm'); // No Martin(i), No Party ;-) ?>

                    </div>
                </noscript>
<?php
    if ($result->prerequisites) {
        if ($scorm->popup == 0) {
?>
                <iframe id="main"
                        class="scoframe"
                        width="<?php echo $scorm->width<=100 ? $scorm->width.'%' : $scorm->width ?>" 
                        height="<?php echo $scorm->height<=100 ? $scorm->height.'%' : $scorm->height ?>" 
                        src="loadSCO.php?id=<?php echo $cm->id.$scoidstr.$modestr ?>">
                </iframe>
<?php
        } else {
?>
                    <script type="text/javascript">
                    //<![CDATA[
                        function openpopup(url,name,options,width,height) {
                            fullurl = "<?php echo $CFG->wwwroot.'/mod/scorm/' ?>" + url;
                            windowobj = window.open(fullurl,name,options);
                            if ((width==100) && (height==100)) {
                                // Fullscreen
                                windowobj.moveTo(0,0);
                            } 
                            if (width<=100) {
                                width = Math.round(screen.availWidth * width / 100);
                            }
                            if (height<=100) {
                                height = Math.round(screen.availHeight * height / 100);
                            }
                            windowobj.resizeTo(width,height);
                            windowobj.focus();
                            return windowobj;
                        }

                        url = "loadSCO.php?id=<?php echo $cm->id.$scoidpop ?>";
                        width = <?php p($scorm->width) ?>;
                        height = <?php p($scorm->height) ?>;
                        var main = openpopup(url, "scormpopup", "<?php p($scorm->options) ?>", width, height);
                    //]]>
                    </script>
                    <noscript>
                    <iframe id="main"
                            class="scoframe"
                            width="<?php echo $scorm->width<=100 ? $scorm->width.'%' : $scorm->width ?>" 
                            height="<?php echo $scorm->height<=100 ? $scorm->height.'%' : $scorm->height ?>" 
                            src="loadSCO.php?id=<?php echo $cm->id.$scoidstr.$modestr ?>">
                    </iframe>
                    </noscript>
<?php            
        }
    } else {
        print_simple_box(get_string('noprerequisites','scorm'),'center');
    }
?>
            </div> <!-- SCORM object -->
        </div> <!-- SCORM box  -->
    </div> <!-- SCORM content -->
    </div> <!-- Content -->
    </div> <!-- Page -->
</body>
</html>
