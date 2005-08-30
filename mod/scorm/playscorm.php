<?PHP  // $Id$

/// This page prints a particular instance of scorm

    require_once('../../config.php');
    require_once('lib.php');

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
    if ($mode == 'normal') {
        if ($trackdata = scorm_get_tracks($USER->id,$sco->id)) {
            if (($trackdata->status == 'completed') || ($trackdata->status == 'passed') || ($trackdata->status == 'failed')) {
                $mode = 'review';
            }
        }
    }
    add_to_log($course->id, 'scorm', 'view', "playscorm.php?id=$cm->id&scoid=$sco->id", "$scorm->id");
    $scoidstring = '&amp;scoid='.$sco->id;
    $modestring = '&amp;mode='.$mode;

    $SESSION->scorm_scoid = $sco->id;
    $SESSION->scorm_status = 'Not Initialized';
    $SESSION->scorm_mode = $mode;

    //
    // Print the page header
    //
    if ($scorm->popup == 1) {
        print_header($pagetitle);
        echo '    <script language="Javascript">'."\n";
        echo "         top.resizeTo({$scorm->width},{$scorm->height});\n";
        echo '    </script>'."\n";
    } else {
        print_header($pagetitle, "$course->fullname",
                 "$navigation <a target='{$CFG->framename}' href='view.php?id=$cm->id'>".format_string($scorm->name,true)."</a>",
                 '', '', true, update_module_button($cm->id, $course->id, $strscorm), '', false);
    }
?>
    <script language="JavaScript" type="text/javascript" src="request.js"></script>
    <script language="JavaScript" type="text/javascript" src="api.php?id=<?php echo $cm->id.$scoidstring.$modestring ?>"></script>

    <table class="fullscreen">
    <tr>
<?php  
    if (($scorm->hidetoc == 0) && ($scorm->popup == 0)) {
?>
	    <td class="top">
            <table class='generalbox'>
               <tr>
                   <td class="structurehead"><?php print_string('coursestruct','scorm') ?></td>
               </tr>
               <tr>
                   <td><?php echo $result->toc; ?></td>
               </tr>
            </table>
        </td>
<?php
    }
?>
        <td class="top">
            <table class="fullscreen">
                <tr>
                    <?php echo $mode == 'browse' ? '<td class="left">'.get_string('browsemode','scorm')."</td>\n" : ''; ?>
                    <td class="right">       
                        <form name="navform" method="post" action="playscorm.php?id=<?php echo $cm->id ?>" target="_top">
                            <input name="scoid" type="hidden" />
                            <input name="currentorg" type="hidden" value="<?php echo $currentorg ?>" />
                            <input name="mode" type="hidden" value="<?php echo $mode ?>" />
                            <input name="prev" type="<?php if (($sco->prev == 0) || ($sco->showprev == 1)) { echo 'hidden'; } else { echo 'button'; } ?>" value="<?php print_string('prev','scorm') ?>" onClick="prevSCO();" />
                            <input name="next" type="<?php if (($sco->next == 0) || ($sco->shownext == 1)) { echo 'hidden'; } else { echo 'button'; } ?>" value="<?php print_string('next','scorm') ?>" onClick="nextSCO();" />
                            <input name="exit" type="button" value="<?php print_string('exit','scorm') ?>" onClick="playSCO(0)" />
                        </form>
                    </td>
                </tr>
                <tr><td <?php echo $mode=='browse'?'colspan="2" ':'' ?>class="right">
<?php
        if ($result->prerequisites) {
            $width = $scorm->width;
            if ($scorm->popup) {
                $width = '100%';
            } else {
                if ($width<=100) {
                    $width = $width.'%';
                }
            }
?>
                    <iframe name="main" class="scoframe" width="<?php echo $width ?>" height="<?php echo $scorm->height<=100 ? $scorm->height.'%' : $scorm->height ?>" src="loadSCO.php?id=<?php echo $cm->id.$scoidstring.$modestring ?>"></iframe>
<?php
        } else {
            print_simple_box(get_string('noprerequisites','scorm'),'center');
        }
?>
                </td></tr>
            </table>
         </td>
    </tr>
    </table>

    <script language="javascript" type="text/javascript">
    <!--
        function playSCO(scoid) {
<?php if ($scorm->popup == 1) { ?>
            if (self.opener && !self.opener.closed){
                self.opener.location.reload();
            }
<?php } ?>
            if (scoid == 0) {
<?php if ($scorm->popup == 1) { ?>
                self.close();
<?php } else { ?>
                document.location = '<?php echo $CFG->wwwroot ?>/course/view.php?id=<?php echo $cm->course ?>';
<?php } ?>
            } else {
                document.navform.scoid.value=scoid;
                document.navform.submit();
            }
        }

        function prevSCO() {
            playSCO(<?php echo $sco->prev ?>);
        }

        function nextSCO() {
            playSCO(<?php echo $sco->next ?>);
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
    </div> <!-- Content -->
    </div> <!-- Page -->
</body>
</html>