<?php  // $Id$

// This script uses installed report plugins to print quiz reports

    require_once("../../config.php");
    require_once("lib.php");

    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $b = optional_param('b', '', PARAM_INT);         // sco ID
    $user = optional_param('user', '', PARAM_INT);   // user ID

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
    } else if (!empty($b)) {
        if (! $sco = get_record("scorm_scoes", "id", $b)) {
            error("Course module is incorrect");
        }
        if (! $scorm = get_record("scorm", "id", $sco->scorm)) {
            error("Scorm activity is incorrect");
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

    if (!isteacher($course->id)) {
        error("You are not allowed to use this script");
    }

    add_to_log($course->id, "scorm", "report", "report.php?id=$cm->id", "$scorm->id");

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
        if (!empty($id)) {
            print_header("$course->shortname: ".format_string($scorm->name), "$course->fullname",
                     "$navigation <a href=\"index.php?id=$course->id\">$strscorms</a>
                      -> <a href=\"view.php?id=$cm->id\">".format_string($scorm->name,true)."</a> -> $strreport",
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
    if (!empty($id)) {
        if ($scoes = get_records_select("scorm_scoes","scorm='$scorm->id' ORDER BY id")) {
            if ($scousers=get_records_select("scorm_scoes_track", "scormid='$scorm->id' GROUP BY userid,scormid", "", "userid,scormid")) {
                $table->head = array('&nbsp;', $strname);
                $table->align = array('center', 'left');
                $table->wrap = array('nowrap', 'nowrap');
                $table->width = '100%';
                $table->size = array(10, '*');
                foreach ($scoes as $sco) {
                    if ($sco->launch!='') {
                        $table->head[]=scorm_string_round($sco->title);
                        $table->align[] = 'center';
                        $table->wrap[] = 'nowrap';
                        $table->size[] = '*';
                    }
                }

                foreach ($scousers as $scouser) {
                    if ($userdata = scorm_get_user_data($scouser->userid)) {
                        $row = '';
                        $row[] = print_user_picture($scouser->userid, $course->id, $userdata->picture, false, true);
                        $row[] = "<a href=\"$CFG->wwwroot/user/view.php?id=$scouser->userid&course=$course->id\">".
                                 "$userdata->firstname $userdata->lastname</a>";
                        foreach ($scoes as $sco) {
                            if ($sco->launch!='') {
                                $anchorstart = '';
                                $anchorend = '';
                                $scoreview = '';
                                if ($trackdata = scorm_get_tracks($sco->id,$scouser->userid)) {
                                    if ($trackdata->score_raw != '') {
                                        $scoreview = '<br />'.get_string('score','scorm').':&nbsp;'.$trackdata->score_raw;
                                    }
                                    if ($trackdata->status == '') {
                                        $trackdata->status = 'notattempted';
                                    } else {
                                        $anchorstart = '<a href="report.php?b='.$sco->id.'&user='.$scouser->userid.'" title="'.
                                                       get_string('details','scorm').'">';
                                        $anchorend = '</a>';
                                    }
                                } else {
                                    $trackdata->status = 'notattempted';
                                    $trackdata->total_time = '';
                                }
                                $strstatus = get_string($trackdata->status,'scorm');
                                $row[] = $anchorstart.'<img src="pix/'.$trackdata->status.'.gif" alt="'.$strstatus.'" title="'.
                                         $strstatus.'">&nbsp;'.$trackdata->total_time.$scoreview.$anchorend;
                            }
                        }
                        $table->data[] = $row;
                    }
                }
                print_table($table);
            } else {
                notice('No users to report');
            }
        }
    } else {
        if (!empty($user)) {
            if ($userdata = scorm_get_user_data($user)) {
                print_simple_box_start('center');
                print_heading(format_string($sco->title));
                echo '<div align="center">'."\n";
                print_user_picture($user, $course->id, $userdata->picture, false, false);
                echo "<a href=\"$CFG->wwwroot/user/view.php?id=$user&course=$course->id\">".
                     "$userdata->firstname $userdata->lastname</a><br />";
                $scoreview = '';
                if ($trackdata = scorm_get_tracks($sco->id,$user)) {
                    if ($trackdata->score_raw != '') {
                        $scoreview = get_string('score','scorm').':&nbsp;'.$trackdata->score_raw;
                    }
                    if ($trackdata->status == '') {
                        $trackdata->status = 'notattempted';
                    }
                } else {
                    $trackdata->status = 'notattempted';
                    $trackdata->total_time = '';
                }
                $strstatus = get_string($trackdata->status,'scorm');
                echo '<img src="pix/'.$trackdata->status.'.gif" alt="'.$strstatus.'" title="'.
                $strstatus.'">&nbsp;'.$trackdata->total_time.'<br />'.$scoreview.'<br />';
                echo '</div>'."\n";
                foreach($trackdata as $element => $value) {
                    if (substr($element,0,3) == 'cmi') {
                        echo $element.' => '.$value.'<br />';
                    }
                }
                print_simple_box_end();
            }
        } else {
            error('Missing script parameter');
        }
    }
    if (empty($noheader)) {
        print_footer($course);
    }
?>
