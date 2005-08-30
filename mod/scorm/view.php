<?php  // $Id$

/// This page prints a particular instance of scorm
/// (Replace scorm with the name of your module)

    require_once("../../config.php");
    require_once("lib.php");

    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);         // scorm ID
    $organization = optional_param('organization', '', PARAM_INT); // organization ID

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

    if (isset($SESSION->scorm_scoid)) {
        unset($SESSION->scorm_scoid);
    }

    $strscorms = get_string("modulenameplural", "scorm");
    $strscorm  = get_string("modulename", "scorm");

    if ($course->category) {
        $navigation = "<a target=\"{$CFG->framename}\" href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->
                       <a target=\"{$CFG->framename}\" href=\"index.php?id=$course->id\">$strscorms</a> ->";
    } else {
        $navigation = "<a target=\"{$CFG->framename}\" href=\"index.php?id=$course->id\">$strscorms</a> ->";
    }

    $pagetitle = strip_tags($course->shortname.': '.format_string($scorm->name));

    add_to_log($course->id, 'scorm', 'pre-view', 'view.php?id='.$cm->id, "$scorm->id");

    //
    // Print the page header
    //
    if (!$cm->visible and !isteacher($course->id)) {
        print_header($pagetitle, "$course->fullname", "$navigation ".format_string($scorm->name), '', '', true,
                     update_module_button($cm->id, $course->id, $strscorm), navmenu($course, $cm));
        notice(get_string('activityiscurrentlyhidden'));
    } else {
        print_header($pagetitle, "$course->fullname",
                     "$navigation <a target=\"{$CFG->framename}\" href=\"view.php?id=$cm->id\">".format_string($scorm->name,true)."</a>",
                     '', '', true, update_module_button($cm->id, $course->id, $strscorm), navmenu($course, $cm));

        if (isteacher($course->id)) {
            $trackedusers = get_record('scorm_scoes_track', 'scormid', $scorm->id, '', '', '', '', 'count(distinct(userid)) as c');
            if ($trackedusers->c > 0) {
                echo "<div class=\"reportlink\"><a target=\"{$CFG->framename}\" href=\"report.php?id=$cm->id\">".get_string('viewallreports','scorm',$trackedusers->c).'</a></div>';
            } else {
                echo '<div class="reportlink">'.get_string('noreports','scorm').'</div>';
            }
        }
        // Print the main part of the page

        print_heading(format_string($scorm->name));

        print_simple_box(format_text($scorm->summary), 'center', '70%', '', 5, 'generalbox', 'intro');

        if (isguest()) {
            print_heading(get_string("guestsno", "scorm"));
            print_footer($course);
            exit;
        }
        print_simple_box_start('center');
?>
        <div class="structurehead"><?php print_string('coursestruct','scorm') ?></div>
<?php
        if (empty($organization)) {
            $organization = $scorm->launch;
        }
        if ($orgs = get_records_select_menu('scorm_scoes',"scorm='$scorm->id' AND organization='' AND launch=''",'id','id,title')) {
            if (count($orgs) > 1) {       
?>
            <div class='center'>
		        <?php print_string('organizations','scorm') ?>
                <form name='changeorg' method='post' action='view.php?id=<?php echo $cm->id ?>'>
                    <?php choose_from_menu($orgs, 'organization', "$organization", '','submit()') ?>
                </form>
            </div>
<?php
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
        $result = scorm_get_toc($scorm,'structurelist',$orgidentifier);
        $incomplete = $result->incomplete;
        echo $result->toc;
        print_simple_box_end();
 ?>
    <div class="center">
        <form name="theform" method="post" action="playscorm.php?id=<?php echo $cm->id ?>"<?php echo $scorm->popup == 1?' target="newwin"':'' ?>>
<?php
    if ($scorm->hidebrowse == 0) {
        print_string("mode","scorm");
        echo ': <input type="radio" id="b" name="mode" value="browse" /><label for="b">'.get_string('browse','scorm').'</label>'."\n";
        if ($incomplete === true) {
            echo '<input type="radio" id="n" name="mode" value="normal" checked="checked" /><label for="n">'.get_string('normal','scorm')."</label>\n";
        } else {
            echo '<input type="radio" id="r" name="mode" value="review" checked="checked" /><label for="r">'.get_string('review','scorm')."</label>\n";
        }
    } else {
        if ($incomplete === true) {
            echo '<input type="hidden" name="mode" value="normal" />'."\n";
        } else {
            echo '<input type="hidden" name="mode" value="review" />'."\n";
        }
    }
?>
            <br />
            <input type="hidden" name="scoid" />
            <input type="hidden" name="currentorg" value="<?php echo $orgidentifier ?>" />
            <input type="submit" value="<? print_string('entercourse','scorm') ?>" />
        </form>
    </div>
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
            which.src = "<?php echo $CFG->wwwroot ?>/mod/scorm/pix/plus.gif";
            eval(beg+list+mid+".display='none';");
        } else {
            which.src = "<?php echo $CFG->wwwroot ?>/mod/scorm/pix/minus.gif";
            eval(beg+list+mid+".display='block';");
        }
    }
-->
</script>
<?php
        print_footer($course);
    }
?>
