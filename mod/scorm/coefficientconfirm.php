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
        $strcoefficient = get_string('coefficient',"scorm");
        $strcoefficient = "Thiet lap he so";
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

  <?php
    $examScoes = get_records_select('scorm_scoes', 'scorm ='.($scorm->id).' and minnormalizedmeasure > -1');
    foreach ($examScoes as $examSco){
        $newcoefficient = optional_param($examSco->id,'',PARAM_INT);
        $sco = get_record('scorm_scoes','scorm',$scorm->id,'id',$examSco->id,'','');
        $sco->score_coefficient = $newcoefficient;
        $ketqua = update_record('scorm_scoes',$sco);
        //echo "Cap nhat $examSco->id voi he so diem ".$newcoefficient."<br>";
    }

    if ($ketqua)
    {
        echo "".get_string('updatesuccess','scorm');
    }
    else
    {
        echo "".get_string('updatefail','scorm');
    }

    echo "<br><br><a href=coefficientsetting.php?id=$id>".get_string('back','scorm')."</a>"
?>
<?php
    //ket thuc phan trinh bay chinh

    if (empty($noheader)) {
        print_footer($course);
    }
?>
