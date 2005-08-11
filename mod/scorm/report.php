<?php  // $Id$

// This script uses installed report plugins to print quiz reports

    require_once("../../config.php");
    require_once("lib.php");

    optional_variable($id);    // Course Module ID, or
    optional_variable($b);     // SCO ID
    optional_variable($user);  // User ID

    if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
        if (! $scorm = get_record("scorm", "id", $cm->instance)) {
            error("Course module is incorrect");
        }
    } else if (isset($b)) {
        if (! $sco = get_record("scorm_scoes", "id", $b)) {
            error("Scorm activity is incorrect");
        }
        if (! $scorm = get_record("scorm", "id", $sco->scorm)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $scorm->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
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
                $table = new stdClass();
                $table->head = array('&nbsp;', $strname);
                $table->align = array('center', 'left');
                $table->wrap = array('nowrap', 'nowrap');
                $table->width = '100%';
                $table->size = array(10, '*');
                foreach ($scoes as $sco) {
                    if ($sco->launch!='') {
                        $table->head[]=scorm_string_wrap($sco->title);
                        //$table->head[]=$sco->title;
                        $table->align[] = 'center';
                        $table->wrap[] = 'nowrap';
                        $table->size[] = '*';
                    }
                }

                foreach ($scousers as $scouser) {
                    if ($userdata = scorm_get_user_data($scouser->userid)) {
                        $row = array();
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
                echo '<hr /><h2>'.get_string('details','scorm').'</h2>';
                
                // Print general score data
                $table = new stdClass();
                $table->head = array(get_string('element','scorm'), get_string('value','scorm'));
                $table->align = array('left', 'left');
                $table->wrap = array('nowrap', 'nowrap');
                $table->width = '100%';
                $table->size = array('*', '*');
                
                $existelements = false;
                if ($scorm->version == 'SCORM_1.3') {
                    $elements = array('raw' => 'cmi.score.raw',
                                      'min' => 'cmi.score.min',
                                      'max' => 'cmi.score.max',
                                      'status' => 'cmi.completition_status',
                                      'time' => 'cmi.total_time');
                } else {
                    $elements = array('raw' => 'cmi.core.score.raw',
                                      'min' => 'cmi.core.score.min',
                                      'max' => 'cmi.core.score.max',
                                      'status' => 'cmi.core.lesson_status',
                                      'time' => 'cmi.core.total_time');
                }
                foreach ($elements as $key => $element) {
                    if (isset($trackdata->$element)) {
                        $existelements = true;
                        $printedelements[]=$element;
                        $row = array();
                        $row[] = get_string($key,'scorm');
                        $row[] = $trackdata->$element;
                        $table->data[] = $row;
                    }
                }
                if ($existelements) {
                    echo '<h3>'.get_string('general','scorm').'</h3>';
                    print_table($table);
                }                
                
                // Print Interactions data
                $table = new stdClass();
                $table->head = array(get_string('identifier','scorm'),
                                     get_string('type','scorm'),
                                     get_string('result','scorm'),
                                     get_string('student_response','scorm'));
                $table->align = array('center', 'center', 'center', 'center');
                $table->wrap = array('nowrap', 'nowrap', 'nowrap', 'nowrap');
                $table->width = '100%';
                $table->size = array('*', '*', '*', '*', '*');
                
                $existinteraction = false;
                
                $i = 0;
                $interactionid = 'cmi.interactions.'.$i.'.id';
                
                while (isset($trackdata->$interactionid)) {
                    $existinteraction = true;
                    $printedelements[]=$interactionid;
                    $elements = array($interactionid,
                                      'cmi.interactions.'.$i.'.type',
                                      'cmi.interactions.'.$i.'.result',
                                      'cmi.interactions.'.$i.'.student_response');
                    $row = array();
                    foreach ($elements as $element) {
                        if (isset($trackdata->$element)) {
                            $row[] = $trackdata->$element;
                            $printedelements[]=$element;
                        } else {
                            $row[] = '&nbsp;';
                        }
                    }
                    $table->data[] = $row;
                    
                    $i++;
                    $interactionid = 'cmi.interactions.'.$i.'.id';
                }
                if ($existinteraction) {
                    echo '<h3>'.get_string('interactions','scorm').'</h3>';
                    print_table($table);
                }
                
                // Print Objectives data
                $table = new stdClass();
                $table->head = array(get_string('identifier','scorm'),
                                     get_string('status','scorm'),
                                     get_string('raw','scorm'),
                                     get_string('min','scorm'),
                                     get_string('max','scorm'));
                $table->align = array('center', 'center', 'center', 'center', 'center');
                $table->wrap = array('nowrap', 'nowrap', 'nowrap', 'nowrap', 'nowrap');
                $table->width = '100%';
                $table->size = array('*', '*', '*', '*', '*');
                
                $existobjective = false;
                
                $i = 0;
                $objectiveid = 'cmi.objectives.'.$i.'.id';
                
                while (isset($trackdata->$objectiveid)) {
                    $existobjective = true;
                    $printedelements[]=$objectiveid;
                    $elements = array($objectiveid,
                                      'cmi.objectives.'.$i.'.status',
                                      'cmi.objectives.'.$i.'.score.raw',
                                      'cmi.objectives.'.$i.'.score.min',
                                      'cmi.objectives.'.$i.'.score.max');
                    $row = array();
                    foreach ($elements as $element) {
                        if (isset($trackdata->$element)) {
                            $row[] = $trackdata->$element;
                            $printedelements[]=$element;
                        } else {
                            $row[] = '&nbsp;';
                        }
                    }
                    $table->data[] = $row;
                    
                    $i++;
                    $objectiveid = 'cmi.objectives.'.$i.'.id';
                }
                if ($existobjective) {
                    echo '<h3>'.get_string('objectives','scorm').'</h3>';
                    print_table($table);
                }
                $table = new stdClass();
                $table->head = array(get_string('element','scorm'), get_string('value','scorm'));
                $table->align = array('left', 'left');
                $table->wrap = array('nowrap', 'wrap');
                $table->width = '100%';
                $table->size = array('*', '*');
                
                $existelements = false;
                
                foreach($trackdata as $element => $value) {
                    if (substr($element,0,3) == 'cmi') { 
                        if (!(in_array ($element, $printedelements))) {
                            $existelements = true;
                            $row = array();
                            $row[] = get_string($element,'scorm') != '[['.$element.']]' ? get_string($element,'scorm') : $element;
                            $row[] = $value;
                            $table->data[] = $row;
                        }
                    }
                }
                if ($existelements) {
                    echo '<h3>'.get_string('othertracks','scorm').'</h3>';
                    print_table($table);
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
