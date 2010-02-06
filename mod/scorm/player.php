<?PHP

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

    //IE 6 Bug workaround
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6') !== false && ini_get('zlib.output_compression') == 'On') {
        ini_set('zlib.output_compression', 'Off');
    }

    if (!empty($id)) {
        if (! $cm = get_coursemodule_from_id('scorm', $id)) {
            print_error('invalidcoursemodule');
        }
        if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
            print_error('coursemisconf');
        }
        if (! $scorm = $DB->get_record("scorm", array("id"=>$cm->instance))) {
            print_error('invalidcoursemodule');
        }
    } else if (!empty($a)) {
        if (! $scorm = $DB->get_record("scorm", array("id"=>$a))) {
            print_error('invalidcoursemodule');
        }
        if (! $course = $DB->get_record("course", array("id"=>$scorm->course))) {
            print_error('coursemisconf');
        }
        if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $course->id)) {
            print_error('invalidcoursemodule');
        }
    } else {
        print_error('missingparameter');
    }

    $url = new moodle_url('/mod/scorm/player.php', array('scoid'=>$scoid, 'id'=>$cm->id));
    if ($mode !== 'normal') {
        $url->param('mode', $mode);
    }
    if ($currentorg !== '') {
        $url->param('currentorg', $currentorg);
    }
    if ($newattempt !== 'off') {
        $url->param('newattempt', $newattempt);
    }
    $PAGE->set_url($url);

    require_login($course->id, false, $cm);

    $strscorms = get_string('modulenameplural', 'scorm');
    $strscorm  = get_string('modulename', 'scorm');
    $strpopup = get_string('popup','scorm');
    $strexit = get_string('exitactivity','scorm');

    if ($course->id != SITEID) {
        if ($scorms = get_all_instances_in_course('scorm', $course)) {
            // The module SCORM/AICC activity with the first id is the course
            $firstscorm = current($scorms);
            if (!(($course->format == 'scorm') && ($firstscorm->id == $scorm->id))) {
                $PAGE->navbar->add($strscorms, new moodle_url('/mod/scorm/index.php', array('id'=>$course->id)));
            }
        }
    }

    $pagetitle = strip_tags("$course->shortname: ".format_string($scorm->name));
    $PAGE->set_title($pagetitle);
    $PAGE->set_heading($course->fullname);
    $PAGE->navbar->add(format_string($scorm->name,true), new moodle_url('/mode/scorm/view.php', array('id'=>$cm->id)));

    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_COURSE,$course->id))) {
        echo $OUTPUT->header();
        notice(get_string("activityiscurrentlyhidden"));
        echo $OUTPUT->footer();
        die;
    }

    //check if scorm closed
    $timenow = time();
    if ($scorm->timeclose !=0) {
        if ($scorm->timeopen > $timenow) {
            echo $OUTPUT->header();
            echo $OUTPUT->box(get_string("notopenyet", "scorm", userdate($scorm->timeopen)), "generalbox boxaligncenter");
            echo $OUTPUT->footer();
            die;
        } elseif ($timenow > $scorm->timeclose) {
            echo $OUTPUT->header();
            echo $OUTPUT->box(get_string("expired", "scorm", userdate($scorm->timeclose)), "generalbox boxaligncenter");
            echo $OUTPUT->footer();
            die;
        }
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

    add_to_log($course->id, 'scorm', 'view', "player.php?id=$cm->id&scoid=$sco->id", "$scorm->id", $cm->id);


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

    $exitlink = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$scorm->course.'" title="'.$strexit.'">'.$strexit.'</a> ';

    $PAGE->set_button($exitlink);

    $PAGE->requires->data_for_js('scormplayerdata', Array('cwidth'=>$scorm->width,'cheight'=>$scorm->height), true);
    $PAGE->requires->js('/mod/scorm/request.js', true);
    $PAGE->requires->js('/lib/cookies.js', true);
    $PAGE->requires->js('/mod/scorm/loaddatamodel.php?id='.$cm->id.$scoidstr.$modestr.$attemptstr, true);
    $PAGE->requires->js('/mod/scorm/rd.js', true);

    echo $OUTPUT->header();

    $PAGE->requires->js_function_call('attach_resize_event');
    if (($sco->previd != 0) && ((!isset($sco->previous)) || ($sco->previous == 0))) {
        $scostr = '&scoid='.$sco->previd;
        $PAGE->requires->js_function_call('scorm_set_prev', Array($CFG->wwwroot.'/mod/scorm/player.php?id='.$cm->id.$orgstr.$modepop.$scostr));
    } else {
        $PAGE->requires->js_function_call('scorm_set_prev', Array($CFG->wwwroot.'/mod/scorm/view.php?id='.$cm->id));
    }
    if (($sco->nextid != 0) && ((!isset($sco->next)) || ($sco->next == 0))) {
        $scostr = '&scoid='.$sco->nextid;
        $PAGE->requires->js_function_call('scorm_set_next', Array($CFG->wwwroot.'/mod/scorm/player.php?id='.$cm->id.$orgstr.$modepop.$scostr));
    } else {
        $PAGE->requires->js_function_call('scorm_set_next', Array($CFG->wwwroot.'/mod/scorm/view.php?id='.$cm->id));
    }
?>
    <div id="scormpage">
<?php
    if ($scorm->hidetoc == 0) {
?>
        <div id="tocbox">
<?php
        if ($scorm->hidenav ==0){
?>
            <!-- Bottons nav at left-->
            <div id="tochead">
                <form name="tochead" method="post" action="player.php?id=<?php echo $cm->id ?>" target="_top">
<?php
            $orgstr = '&amp;currentorg='.$currentorg;
            if (($scorm->hidenav == 0) && ($sco->previd != 0) && (!isset($sco->previous) || $sco->previous == 0)) {
                // Print the prev LO button
                $scostr = '&amp;scoid='.$sco->previd;
                $url = $CFG->wwwroot.'/mod/scorm/player.php?id='.$cm->id.$orgstr.$modestr.$scostr;
?>
                    <input name="prev" type="button" value="<?php print_string('prev','scorm') ?>" onClick="document.location.href=' <?php echo $url; ?> '"/>
<?php
            }
            if (($scorm->hidenav == 0) && ($sco->nextid != 0) && (!isset($sco->next) || $sco->next == 0)) {
                // Print the next LO button
                $scostr = '&amp;scoid='.$sco->nextid;
                $url = $CFG->wwwroot.'/mod/scorm/player.php?id='.$cm->id.$orgstr.$modestr.$scostr;
?>
                    <input name="next" type="button" value="<?php print_string('next','scorm') ?>" onClick="document.location.href=' <?php echo $url; ?> '"/>
<?php
            }
?>
                </form>
            </div> <!-- tochead -->
<?php
        }
?>
            <div id="toctree" class="generalbox">
            <?php echo $result->toc; ?>
            </div> <!-- toctree -->
        </div> <!--  tocbox -->
<?php
        $class = ' class="toc"';
    } else {
        $class = ' class="no-toc"';
    }
?>
        <div id="scormbox"<?php echo $class; if(($scorm->hidetoc == 2) || ($scorm->hidetoc == 1)){echo 'style="width:100%"';}?>>
<?php
    // This very big test check if is necessary the "scormtop" div
    if (
           ($mode != 'normal') ||  // We are not in normal mode so review or browse text will displayed
           (
               ($scorm->hidenav == 0) &&  // Teacher want to display navigation links
               ($scorm->hidetoc != 0) &&  // The buttons has not been displayed
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
        <?php echo $mode == 'browse' ? '<div id="scormmode" class="scorm-left">'.get_string('browsemode','scorm')."</div>\n" : ''; ?>
        <?php echo $mode == 'review' ? '<div id="scormmode" class="scorm-left">'.get_string('reviewmode','scorm')."</div>\n" : ''; ?>
<?php
        if (($scorm->hidenav == 0) || ($scorm->hidetoc == 2) || ($scorm->hidetoc == 1)) {
?>
                <div id="scormnav" class="scorm-right">
        <?php
            $orgstr = '&amp;currentorg='.$currentorg;
            if (($scorm->hidenav == 0) && ($sco->previd != 0) && (!isset($sco->previous) || $sco->previous == 0) && (($scorm->hidetoc == 2) || ($scorm->hidetoc == 1)) ) {
                // Print the prev LO button
                $scostr = '&amp;scoid='.$sco->previd;
                $url = $CFG->wwwroot.'/mod/scorm/player.php?id='.$cm->id.$orgstr.$modestr.$scostr;
?>
                    <form name="scormnavprev" method="post" action="player.php?id=<?php echo $cm->id ?>" target="_top" style= "display:inline">
                        <input name="prev" type="button" value="<?php print_string('prev','scorm') ?>" onClick="document.location.href=' <?php echo $url; ?> '"/>
                    </form>
<?php
            }
            if ($scorm->hidetoc == 2) {
                echo $result->tocmenu;
            }
            if (($scorm->hidenav == 0) && ($sco->nextid != 0) && (!isset($sco->next) || $sco->next == 0) && (($scorm->hidetoc == 2) || ($scorm->hidetoc == 1))) {
                // Print the next LO button
                $scostr = '&amp;scoid='.$sco->nextid;
                $url = $CFG->wwwroot.'/mod/scorm/player.php?id='.$cm->id.$orgstr.$modestr.$scostr;
?>
                    <form name="scormnavnext" method="post" action="player.php?id=<?php echo $cm->id ?>" target="_top" style= "display:inline">
                        <input name="next" type="button" value="<?php print_string('next','scorm') ?>" onClick="document.location.href=' <?php echo $url; ?> '"/>
                    </form>
<?php
            }
        ?>
                </div>
<?php
        }
?>
            </div> <!-- Scormtop -->
<?php
    } // The end of the very big test
?>
            <div id="scormobject" class="scorm-right">
                <noscript>
                    <div id="noscript">
                        <?php print_string('noscriptnoscorm','scorm'); // No Martin(i), No Party ;-) ?>

                    </div>
                </noscript>
<?php
    if ($result->prerequisites) {
        if ($scorm->popup == 0) {
            $fullurl="loadSCO.php?id=".$cm->id.$scoidstr.$modestr;
            echo "                <iframe id=\"scoframe1\" class=\"scoframe\" name=\"scoframe1\" src=\"{$fullurl}\"></iframe>\n";
            $PAGE->requires->js_function_call('scorm_resize');
        } else {
            // Clean the name for the window as IE is fussy
            $name = preg_replace("/[^A-Za-z0-9]/", "", $scorm->name);
            if (!$name) {
                $name = 'DefaultPlayerWindow';
            }
            $name = 'scorm_'.$name;

            echo html_writer::script(js_writer::function_call('scorm_resize'));
            echo html_writer::script('', $CFG->wwwroot.'/mod/scorm/player.js');
            echo html_writer::script(js_writer::function_call('scorm_openpopup', Array("loadSCO.php?id=".$cm->id.$scoidpop, $name, $scorm->options, $scorm->width, $scorm->height)));
            ?>
                    <noscript>
                    <iframe id="main" class="scoframe" src="loadSCO.php?id=<?php echo $cm->id.$scoidstr.$modestr ?>">
                    </iframe>
                    </noscript>
<?php
            //Added incase javascript popups are blocked
            $link = '<a href="'.$CFG->wwwroot.'/mod/scorm/loadSCO.php?id='.$cm->id.$scoidstr.$modestr.'" target="new">'.get_string('popupblockedlinkname','scorm').'</a>';
            echo $OUTPUT->box(get_string('popupblocked','scorm',$link));
        }
    } else {
        echo $OUTPUT->box(get_string('noprerequisites','scorm'));
    }
?>
            </div> <!-- SCORM object -->
        </div> <!-- SCORM box  -->
    </div> <!-- SCORM page -->
<?php echo $OUTPUT->footer(); ?>
