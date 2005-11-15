<?PHP  // $Id$

/// This page prints a particular instance of aicc/scorm package

    require_once('../../config.php');
    require_once('locallib.php');

    //
    // Checkin' script parameters
    //
    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);         // scorm ID
    $scoid = required_param('scoid', '', PARAM_INT);  // sco ID
    $mode = optional_param('mode', '', PARAM_ALPHA); // navigation mode
    $currentorg = optional_param('currentorg', '', PARAM_RAW); // selected organization
    
    $modestring = '';
    $scoidstring = '';
    $currentorgstring = '';
    if (!empty($mode)) {
        $modestring = '&mode='.$mode;
    }
    if (!empty($scoid)) {
        $scoidstring = '&scoid='.$scoid;
    }
    if (!empty($currentorg)) {
        $currentorgstring = '&currentorg='.$currentorg;
    }

    if (!empty($id)) {
        if (! $cm = get_record("course_modules", "id", $id)) {
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
    $strexpand = get_string('expcoll','scorm');
    $strpopup = get_string('popup','scorm');

    if ($course->category) {
        $navigation = "<a target=\"{$CFG->framename}\" href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->
                       <a target=\"{$CFG->framename}\" href=\"index.php?id=$course->id\">$strscorms</a> ->";
    } else {
        $navigation = "<a target=\"{$CFG->framename}\" href=\"index.php?id=$course->id\">$strscorms</a> ->";
    }

    $pagetitle = strip_tags("$course->shortname: ".format_string($scorm->name));

    if (!$cm->visible and !isteacher($course->id)) {
        print_header($pagetitle, "$course->fullname",
                 "$navigation <a target='{$CFG->framename}' href='view.php?id=$cm->id'>".format_string($scorm->name,true)."</a>",
                 '', '', true, update_module_button($cm->id, $course->id, $strscorm), '', false);
        notice(get_string("activityiscurrentlyhidden"));
    }

    //
    // TOC processing
    //
    $result = scorm_get_toc($scorm,'structurelist',$currentorg,$scoid,$mode,true);
    $sco = $result->sco;

    if (($mode == 'browse') && ($scorm->hidebrowse == 1)) {
       $mode = 'normal';
    }
    if ($mode == 'normal') {
        if ($trackdata = scorm_get_tracks($USER->id,$sco->id)) {
            if (($trackdata->status == 'completed') || ($trackdata->status == 'passed') || ($trackdata->status == 'failed')) {
                $mode = 'review';
            }
        }
    }

    add_to_log($course->id, 'scorm', 'view', "player.php?id=$cm->id&scoid=$sco->id", "$scorm->id");

    $scoidstr = '&amp;scoid='.$sco->id;
    $scoidpop = '&scoid='.$sco->id;
    $modestr = '&amp;mode='.$mode;
    $modepop = '&mode='.$mode;
    if ((!$result->incomplete) && ($result->attemptleft > 0)) {
        $attemptstr = '&amp;attempt=new';
        $attemptpop = '&attempt=new';
    }

    $SESSION->scorm_scoid = $sco->id;
    $SESSION->scorm_status = 'Not Initialized';
    $SESSION->scorm_mode = $mode;

    //
    // Print the page header
    //
    $bodyscript = '';
    if ($scorm->popup == 1) {
        $bodyscript = 'onunload="main.close();"';
    }
    $exitlink = '(<a href="'.$CFG->wwwroot.'/course/view.php?id='.$cm->course.'">'.get_string('exit','scorm').'</a>)&nbsp;';
    print_header($pagetitle, "$course->fullname",
                 "$navigation <a target='{$CFG->framename}' href='view.php?id=$cm->id'>".format_string($scorm->name,true)."</a>",
                 '', '', true, $exitlink.update_module_button($cm->id, $course->id, $strscorm), '', false, $bodyscript);
    if ($sco->scormtype == 'sco') {
?>
    <script language="JavaScript" type="text/javascript" src="request.js"></script>
    <script language="JavaScript" type="text/javascript" src="api.php?id=<?php echo $cm->id.$scoidstr.$modestr.$attemptstr ?>"></script>
<?php
    }
?>
    <div id="scormpage">
<?php  
    if ($scorm->hidetoc == 0) {
?>
        <div id="tocbox" class="generalbox">
            <div id="tochead" class="header"><?php print_string('coursestruct','scorm') ?></div>
            <div id="toctree">
            <?php echo $result->toc; ?>
            </div>
            <noscript>
                <div id="noscript">
                <?php  
                    print_string('noscriptnoscorm','scorm'); // No Martin(i), No Party ;-)
                ?>
                </div>
            </noscript>
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
                       ($sco->previous == 0)   // Moodle must manage the previous link
                   ) || 
                   (
                       ($sco->nextid != 0) &&  // This is not the last learning object of the package
                       ($sco->next == 0)       // Moodle must manage the next link
                   )
               )
            )
        ) {
?>
            <div id="scormtop">
                <?php echo $mode == 'browse' ? '<div id="scormmode" class="left">'.get_string('browsemode','scorm')."</div>\n" : ''; ?>
                <?php echo $mode == 'review' ? '<div id="scormmode" class="left">'.get_string('reviewmode','scorm')."</div>\n" : ''; ?>
<?php
        if ($scorm->hidenav == 0) {
?>
                <div id="scormnav" class="right">
<?php
            $orgstr = '&amp;currentorg='.$currentorg;
            if (($sco->previd != 0) && ($sco->previous == 0)) {
                /// Print the prev LO link
                $scostr = '&amp;scoid='.$sco->previd;
                $prevlink = $CFG->wwwroot.'/mod/scorm/player.php?id='.$cm->id.$orgstr.$modestr.$scostr;
                echo '<a href="'.$prevlink.'">&lt; '.get_string('prev','scorm').'</a>';
            }
            if (($sco->nextid != 0) && ($sco->next == 0)) {
                /// Print the next LO link
                $scostr = '&amp;scoid='.$sco->nextid;
                $nextlink = $CFG->wwwroot.'/mod/scorm/player.php?id='.$cm->id.$orgstr.$modestr.$scostr;
                echo '&nbsp;<a href="'.$nextlink.'">'.get_string('next','scorm').' &gt;</a>';
            }
?>
                </div>
<?php
        } 
?>
            </div>
<?php
    } // The end of the very test
?>
            <div id="scormobject" class="right">
<?php
    if ($result->prerequisites) {
        if ($scorm->popup == 0) {
            if (strpos('MSIE',$_SERVER['HTTP_USER_AGENT']) === false) { 
                /// Internet Explorer does not has full support to objects
?>
                    <iframe id="main"
                            width="<?php echo $scorm->width<=100 ? $scorm->width.'%' : $scorm->width ?>" 
                            height="<?php echo $scorm->height<=100 ? $scorm->height.'%' : $scorm->height ?>" 
                            src="loadSCO.php?id=<?php echo $cm->id.$scoidstr.$modestr ?>">
                    </iframe>
<?php
            } else {
?>
                    <object id="main" 
                            class="scoframe" 
                            width="<?php echo $scorm->width<=100 ? $scorm->width.'%' : $scorm->width ?>" 
                            height="<?php echo $scorm->height<=100 ? $scorm->height.'%' : $scorm->height ?>" 
                            data="loadSCO.php?id=<?php echo $cm->id.$scoidstr.$modestr ?>"
                            type="text/html">
                         <?php print_string('noobjectsupport', 'scorm'); ?>
                    </object>
<?php
            }
        } else {
?>
                    <script lanuguage="javascript">
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

                        url = "loadSCO.php?id=<?php echo $cm->id.$scoidpop.$modepop ?>";
                        width = <?php p($scorm->width) ?>;
                        height = <?php p($scorm->height) ?>;
                        var main = openpopup(url, "<?php p($scorm->name) ?>", "<?php p($scorm->options) ?>", width, height);
                    </script>
                    <noscript>
<?php
            if (strpos('MSIE',$_SERVER['HTTP_USER_AGENT']) === false) { 
                /// Internet Explorer does not has full support to objects
?>
                    <iframe id="main"
                            width="<?php echo $scorm->width<=100 ? $scorm->width.'%' : $scorm->width ?>" 
                            height="<?php echo $scorm->height<=100 ? $scorm->height.'%' : $scorm->height ?>" 
                            src="loadSCO.php?id=<?php echo $cm->id.$scoidstr.$modestr ?>">
                    </iframe>
<?php
            } else {
?>
                    <object id="main" 
                            class="scoframe" 
                            width="<?php echo $scorm->width<=100 ? $scorm->width.'%' : $scorm->width ?>" 
                            height="<?php echo $scorm->height<=100 ? $scorm->height.'%' : $scorm->height ?>" 
                            data="loadSCO.php?id=<?php echo $cm->id.$scoidstr.$modestr ?>"
                            type="text/html">
                         <?php print_string('noobjectsupport', 'scorm'); ?>
                    </object>
<?php
            }
?>
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
<?php
    if ($scorm->hidetoc == 0) {
?>
    <script language="javascript" type="text/javascript">
    <!--
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
    }
?>
    </div> <!-- Content -->
    </div> <!-- Page -->
</body>
</html>
