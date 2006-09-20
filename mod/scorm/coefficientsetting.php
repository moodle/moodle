<?php  // $Id$

    require_once("../../config.php");
    require_once('locallib.php');
    
    $id = optional_param('id', '', PARAM_INT);    // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);     // SCORM ID
    $b = optional_param('b', '', PARAM_INT);     // SCO ID
    $user = optional_param('user', '', PARAM_INT);  // User ID

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
    } else {
        if (!empty($b)) {
            if (! $sco = get_record("scorm_scoes", "id", $b)) {
                error("Scorm activity is incorrect");
            }
            $a = $sco->scorm;
        }
        if (!empty($a)) {
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
    }

    require_login($course->id, false, $cm);
    require_capability('mod/scorm:viewgrades', get_context_instance(COTNEXT_MODULE, $cm->id));

    add_to_log($course->id, "scorm", "report", "cofficientsetting.php?id=$cm->id", "$scorm->id");

/// Print the page header
    if (empty($noheader)) {
        if ($course->category) {
            $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
        } else {
            $navigation = '';
        }

        $strscorms = get_string("modulenameplural", "scorm");
        $strscorm  = get_string("modulename", "scorm");
        $strreport  = get_string("report", "scorm");
        $strname  = get_string('name');
        $strcoefficient = get_string('coefficient','scorm');
        if (empty($b)) {
            print_header("$course->shortname: ".format_string($scorm->name), "$course->fullname",
                     "$navigation <a href=\"index.php?id=$course->id\">$strscorms</a>
                      -> <a href=\"view.php?id=$cm->id\">".format_string($scorm->name,true)."</a> -> $strcoefficient",
                     "", "", true);
        } else {
            print_header("$course->shortname: ".format_string($scorm->name), "$course->fullname",
                     "$navigation <a href=\"index.php?id=$course->id\">$strscorms</a>
                      -> <a href=\"view.php?id=$cm->id\">".format_string($scorm->name,true)."</a>
              -> <a href=\"report.php?id=$cm->id\">$strreport</a> -> $sco->title",
                     "", "", true);
        }
        print_heading(format_string($scorm->name));
    }

    $scormpixdir = $CFG->modpixpath.'/scorm/pix';

    //Phan trinh bay chinh
?>
<script type="text/javascript">
function validate_form()
{
    return true;
}
</script>
<form name="form" method="post" action="coefficientconfirm.php" onsubmit="return validate_form();" >
  <table width="50%" border="0">
    <tr>
      <td class="scormtableheader"><?php echo(get_string('title','scorm')); ?></td>
      <td class="scormtableheader"><?php echo(get_string('coefficient','scorm')); ?></td>
    </tr>
 
  <?php
    $examScoes = get_records_select('scorm_scoes', 'scorm ='.($scorm->id).' and minnormalizedmeasure > -1');
    if(!empty($examScoes))
    {
    
        foreach ($examScoes as $examSco){
        echo "<tr><td>";    
        echo "$examSco->identifier.</td><td><input type='text' name='$examSco->id' class='scormtextbox' value=$examSco->score_coefficient /></td></tr><br>";
        }
    }

?>
 </table>
<br>
<input type="hidden" name="id" value="<?php p($id) ?>" />
<input type="submit" value="<?php print_string('savechanges') ?>" />
</form>
<?php
    //ket thuc phan trinh bay chinh

    if (empty($noheader)) {
        print_footer($course);
    }
?>
