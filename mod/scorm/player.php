<?PHP  // $Id$

/// This page prints a particular instance of aicc/scorm package

    require_once('../../config.php');
    require_once('locallib.php');
    require_once('sequencinglib.php');
    
    //
    // Checkin' script parameters
    //
    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);         // scorm ID
    $scoid = required_param('scoid', PARAM_INT);  // sco ID
    $mode = optional_param('mode', 'normal', PARAM_ALPHA); // navigation mode
    $currentorg = optional_param('currentorg', '', PARAM_RAW); // selected organization
    $newattempt = optional_param('newattempt', 'off', PARAM_ALPHA); // the user request to start a new attempt

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

    //$f = "D:\\test.txt";
    //@$ft = fopen($f,"a");
    //fwrite($ft,"\n ++ Thong tin quyen set attempt ".$USER->setAttempt);
    $strscorms = get_string('modulenameplural', 'scorm');
    $strscorm  = get_string('modulename', 'scorm');
    $strpopup = get_string('popup','scorm');

    $attempt = scorm_get_last_attempt($scorm->id,$USER->id);    
    
    //Kiem tra xem co phai la tiep tuc khong 
    if ($mode=='continue') {
        $scoid = scorm_get_suspendscoid($scorm->id,$USER->id);
        $USER->setAttempt = 'set';
        $mode = 'normal';
    }
    if (($mode == 'normal') && ($USER->setAttempt == 'notset')) {
        $attempt++;
        $USER->setAttempt = 'set';
    }
    //Thuc hien Sequencing

    if ($mode!='review') {
        $sequencingResult = scorm_sequecingrule_implement($scorm->id,$scoid,$USER->id);
        //echo "<script language='JavaScript'>";
        //    echo "alert('Sequencing');";
        //echo "<script>";
        if (($sequencingResult->rule == 'pre') && ($sequencingResult->action == 'disabled')){
            echo "<script language='JavaScript'>";
            echo "alert('Disabling');";
            echo "</script>";        
        }
        if (($sequencingResult->rule == 'exit') && ($sequencingResult->action == 'exit')){
            $exitscoid = get_sco_after_exit($scoid,$scorm->id);
            //fwrite($ft,"\n ++ Thong tin exit sco la ".$exitscoid);
            $orgstr = '&currentorg='.$currentorg;
            $modepop = '&mode='.$mode;
            $scostr = '&scoid='.$exitscoid;
            echo "<script language='JavaScript'>";
            echo "alert('Exiting');";
            echo "location.href='".$CFG->wwwroot.'/mod/scorm/player.php?id='.$cm->id.$orgstr.$modepop.$scostr."';";
            echo "</script>";                
        }        
    }    

    //Thiet lap attempt_status cho scoid
    scorm_set_attempt($scoid,$USER->id);
    //Ket thuc thiet lap attemp_status
    if ($mode!='review') {
        //Update trang thai
        scorm_rollup_updatestatus($scorm->id,$scoid,$USER->id);
        //------------------------------
    }    
    //Thiet lap thong tin lien quan truy xuat Scorm
    $statistic = get_record('scorm_statistic',"scormid",$scorm->id,"userid",$USER->id);
    if (empty($statistic)) {
        $statisticInput->accesstime = time();
        $statisticInput->durationtime = 0;
        $statisticInput->status = 'during';
        $statisticInput->attemptnumber = $attempt;
        $statisticInput->scormid = $scorm->id;
        $statisticInput->userid = $USER->id;
        $statisticid = scorm_insert_statistic($statisticInput);
    } else {
        if ($statistic->status=='suspend') {
            $statisticInput->accesstime = time();
            $statisticInput->durationtime = $statistic->durationtime;
            $statisticInput->status = 'during';
            $statisticInput->attemptnumber = $attempt;
            $statisticInput->scormid = $scorm->id;
            $statisticInput->userid = $USER->id;
        }
    }    

    //---------------------Ket thuc thiet lap thoi gian ---------------

    //Lay thoi gian toi da cho phep
    $absoluteTimeLimit = scorm_get_AbsoluteTimeLimit($scoid);
    if ($absoluteTimeLimit > 0) {    
        echo "<script type='text/javascript'>"; 
        echo "alert('Bai nay co thoi gian lam la: ".$absoluteTimeLimit."');";
        echo "function remind(msg1) {"; 
        echo "var msg = 'Da het gio lam bai ' + msg1 +' Secs.Lua chon bai khac de tiep tuc';";
        echo "alert(msg);"; 
        echo "window.location.href = 'view.php?id=".$scorm->id."';";
        echo "}";
        echo "setTimeout('remind(".$absoluteTimeLimit.")',".$absoluteTimeLimit.");";
        echo "</script>";
    }
    //--------------------------------

    
    if ($course->category != 0) {
        $navigation = "<a target=\"{$CFG->framename}\" href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
        if ($scorms = get_all_instances_in_course('scorm', $course)) {
            // The module SCORM activity with the least id is the course  
            $firstscorm = current($scorms);
            if (!(($course->format == 'scorm') && ($firstscorm->id == $scorm->id))) {
                $navigation .= "<a target=\"{$CFG->framename}\" href=\"index.php?id=$course->id\">$strscorms</a> ->";
            }
        }
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
    //$attempt = scorm_get_last_attempt($scorm->id, $USER->id);
    //$f = "D:\\test.txt";
    //@$ft = fopen($f,"a");
    ////fwrite($ft,"\n ++ ++ + ++ Gia tri $attempt lay duoc la ".$attempt);

    //if ($mode=='normal'){
    //    $newattempt = 'on';
    //}
    if (($newattempt=='on') && (($attempt < $scorm->maxattempt) || ($scorm->maxattempt == 0))) {
        $attempt++;
        //$f = "D:\\test.txt";
        //@$ft = fopen($f,"a");
        //fwrite($ft,"\n ----New attempt------- ".$attempt);

    }
    $attemptstr = '&amp;attempt=' . $attempt;

    //fwrite($ft,"\n ----Gia tri attempt bay gio la------- ".$attempt);
    $result = scorm_get_toc($USER,$scorm,'structurelist',$currentorg,$scoid,$mode,$attempt,true);
    $sco = $result->sco;

    if (($mode == 'browse') && ($scorm->hidebrowse == 1)) {
       $mode = 'normal';
    }
    if ($mode != 'browse') {
        ////fwrite($ft,"\n ++ ++ + ++ Gia tri $mode khac browser ".$mode);
        if ($trackdata = scorm_get_tracks($sco->id,$USER->id,$attempt)) {
            if (($trackdata->status == 'completed') || ($trackdata->status == 'passed') || ($trackdata->status == 'failed')) {
                $mode = 'review';
                ////fwrite($ft,"\n ++ ++ + ++ Gia tri $mode ".$mode);
            } else {
                $mode = 'normal';
                ////fwrite($ft,"\n ++ ++ + ++ Gia tri $mode ".$mode);
            }
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
    $SESSION->attempt = $attempt;

    //    Doan code them
    ////fwrite($ft,"\n ++ ++ + ++ Gia tri attempt duoc gan cho user la ".$attempt);
    $USER->attempt = $attempt;
    //------------Ket thuc doan them

    //
    // Print the page header
    //
    $bodyscript = '';
    if ($scorm->popup == 1) {
        $bodyscript = 'onunload="main.close();"';
    }

    // Kiem tra xem co duoc exit khong
    if (scorm_isChoiceexit($sco->scorm,$sco->id)) {
        $exitlink = '(<a href="'.$CFG->wwwroot.'/course/view.php?id='.$cm->course.'">'.get_string('exit','scorm').'</a>)&nbsp;';
    } else {
        $exitlink = get_string('exitisnotallowed','scorm');
    }

    //Luu giu khoa hoc thoat ra
    $suspend = '(<a href="suspend.php?scorm='.$sco->scorm.'&sco='.$sco->id.'&userid='.$USER->id.'&id='.$cm->course.'">'.get_string('suspend','scorm').'</a>)&nbsp;';

    print_header($pagetitle, "$course->fullname",
                 "$navigation <a target='{$CFG->framename}' href='view.php?id=$cm->id'>".format_string($scorm->name,true)."</a>",
                 '', '', true, $exitlink.$suspend.update_module_button($cm->id, $course->id, $strscorm), '', false, $bodyscript);
    if ($sco->scormtype == 'sco') {
?>
    <script language="JavaScript" type="text/javascript" src="request.js"></script>
    <script language="JavaScript" type="text/javascript" src="api.php?id=<?php echo $cm->id.$scoidstr.$modestr.$attemptstr ?>"></script>
<?php
    }
    if (($sco->previd != 0) && ($sco->previous == 0)) {
        $scostr = '&scoid='.$sco->previd;
        echo '    <script language="javascript">var prev="'.$CFG->wwwroot.'/mod/scorm/player.php?id='.$cm->id.$orgstr.$modepop.$scostr."\";</script>\n";
    } else {
        echo '    <script language="javascript">var prev="'.$CFG->wwwroot.'/mod/scorm/view.php?id='.$cm->id."\";</script>\n";
    }
    if (($sco->nextid != 0) && ($sco->next == 0)) {
        $scostr = '&scoid='.$sco->nextid;
        echo '    <script language="javascript">var next="'.$CFG->wwwroot.'/mod/scorm/player.php?id='.$cm->id.$orgstr.$modepop.$scostr."\";</script>\n";
    } else {
        echo '    <script language="javascript">var next="'.$CFG->wwwroot.'/mod/scorm/view.php?id='.$cm->id."\";</script>\n";
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
            if (($scorm->hidenav == 0) && ($sco->previd != 0) && ($sco->previous == 0)) {
                /// Print the prev LO link
                $scostr = '&amp;scoid='.$sco->previd;
                $url = $CFG->wwwroot.'/mod/scorm/player.php?id='.$cm->id.$orgstr.$modestr.$scostr;
                echo '<a href="'.$url.'">&lt; '.get_string('prev','scorm').'</a>';
            }
            if ($scorm->hidetoc == 2) {
                echo $result->tocmenu;
            }
            if (($scorm->hidenav == 0) && ($sco->nextid != 0) && ($sco->next == 0)) {
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
            if (strpos('MSIE',$_SERVER['HTTP_USER_AGENT']) === false) { 
                /// Internet Explorer does not has full support to objects
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

                        url = "loadSCO.php?id=<?php echo $cm->id.$scoidpop ?>";
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
                            class="scoframe"
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
    </div> <!-- Content -->
    </div> <!-- Page -->

</body>
</html>

