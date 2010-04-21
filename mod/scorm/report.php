<?php  // $Id$

// This script uses installed report plugins to print quiz reports

    require_once("../../config.php");
    require_once('locallib.php');

    $id = optional_param('id', '', PARAM_INT);    // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);     // SCORM ID
    $b = optional_param('b', '', PARAM_INT);     // SCO ID
    $user = optional_param('user', '', PARAM_INT);  // User ID
    $attempt = optional_param('attempt', '1', PARAM_INT);  // attempt number
    $action     = optional_param('action', '', PARAM_ALPHA);
    $attemptids = optional_param('attemptid', array(), PARAM_RAW); //get array of responses to delete.

    if (!empty($id)) {
        if (! $cm = get_coursemodule_from_id('scorm', $id)) {
            error('Course Module ID was incorrect');
        }
        if (! $course = get_record('course', 'id', $cm->course)) {
            error('Course is misconfigured');
        }
        if (! $scorm = get_record('scorm', 'id', $cm->instance)) {
            error('Course module is incorrect');
        }
    } else {
        if (!empty($b)) {
            if (! $sco = get_record('scorm_scoes', 'id', $b)) {
                error('Scorm activity is incorrect');
            }
            $a = $sco->scorm;
        }
        if (!empty($a)) {
            if (! $scorm = get_record('scorm', 'id', $a)) {
                error('Course module is incorrect');
            }
            if (! $course = get_record('course', 'id', $scorm->course)) {
                error('Course is misconfigured');
            }
            if (! $cm = get_coursemodule_from_instance('scorm', $scorm->id, $course->id)) {
                error('Course Module ID was incorrect');
            }
        }
    }

    require_login($course->id, false, $cm);

    $contextmodule = get_context_instance(CONTEXT_MODULE,$cm->id);

    require_capability('mod/scorm:viewreport', $contextmodule);

    add_to_log($course->id, 'scorm', 'report', 'report.php?id='.$cm->id, $scorm->id, $cm->id);

    if (!empty($user)) {
        $userdata = scorm_get_user_data($user);
    } else {
        $userdata = null;
    }

/// Print the page header
    if (empty($noheader)) {

        $strscorms = get_string('modulenameplural', 'scorm');
        $strscorm  = get_string('modulename', 'scorm');
        $strreport  = get_string('report', 'scorm');
        $strattempt  = get_string('attempt', 'scorm');
        $strname  = get_string('name');

        if (empty($b)) {
            if (empty($a)) {
                $navigation = build_navigation($strreport, $cm);
                print_header("$course->shortname: ".format_string($scorm->name), $course->fullname,$navigation,
                             '', '', true);
            } else {

                $navlinks = array();
                $navlinks[] = array('name' => $strreport, 'link' => "report.php?id=$cm->id", 'type' => 'title');
                $navlinks[] = array('name' => "$strattempt $attempt - ".fullname($userdata), 'link' => '', 'type' => 'title');
                $navigation = build_navigation($navlinks, $cm);

                print_header("$course->shortname: ".format_string($scorm->name), $course->fullname,
                             $navigation, '', '', true);
            }
        } else {

            $navlinks = array();
            $navlinks[] = array('name' => $strreport, 'link' => "report.php?id=$cm->id", 'type' => 'title');
            $navlinks[] = array('name' => "$strattempt $attempt - ".fullname($userdata), 'link' => "report.php?a=$a&amp;user=$user&amp;attempt=$attempt", 'type' => 'title');
            $navlinks[] = array('name' => $sco->title, 'link' => '', 'type' => 'title');
            $navigation = build_navigation($navlinks, $cm);

            print_header("$course->shortname: ".format_string($scorm->name), $course->fullname, $navigation,
                     '', '', true);
        }
        print_heading(format_string($scorm->name));
    }

    if ($action == 'delete' && has_capability('mod/scorm:deleteresponses',$contextmodule)) {
        if (scorm_delete_responses($attemptids, $scorm->id)) { //delete responses.
            notify(get_string('scormresponsedeleted', 'scorm'), 'notifysuccess');
        }
    }

    $scormpixdir = $CFG->modpixpath.'/scorm/pix';

    if (empty($b)) {
        if (empty($a)) {
            // No options, show the global scorm report

            if (!empty($CFG->enablegroupings) && !empty($cm->groupingid)) {
                $sql = "SELECT st.userid, st.scormid
                        FROM {$CFG->prefix}scorm_scoes_track st
                            INNER JOIN {$CFG->prefix}groups_members gm ON st.userid = gm.userid
                            INNER JOIN {$CFG->prefix}groupings_groups gg ON gm.groupid = gg.groupid
                        WHERE st.scormid = {$scorm->id} AND gg.groupingid = {$cm->groupingid}
                        GROUP BY st.userid,st.scormid
                        ";
            } else {
                $sql = "SELECT st.userid, st.scormid
                        FROM {$CFG->prefix}scorm_scoes_track st
                        WHERE st.scormid = {$scorm->id}
                        GROUP BY st.userid,st.scormid
                        ";
            }

            if ($scousers=get_records_sql($sql)) {
                $table = new stdClass();
                $table->head = array();
                $table->width = '100%';
                if (has_capability('mod/scorm:deleteresponses',$contextmodule)) {
                    $table->head[]  = '&nbsp;';
                    $table->align[] = 'center';
                    $table->wrap[]  = 'nowrap';
                    $table->size[]  = '10';
                }

                $table->head[]  = '&nbsp;';
                $table->align[] = 'center';
                $table->wrap[]  = 'nowrap';
                $table->size[]  = '10';

                $table->head[]  = get_string('name');
                $table->align[] = 'left';
                $table->wrap[]  = 'nowrap';
                $table->size[]  = '*';

                $table->head[]= get_string('attempt','scorm');
                $table->align[] = 'center';
                $table->wrap[] = 'nowrap';
                $table->size[] = '*';

                $table->head[]= get_string('started','scorm');
                $table->align[] = 'center';
                $table->wrap[] = 'nowrap';
                $table->size[] = '*';

                $table->head[]= get_string('last','scorm');
                $table->align[] = 'center';
                $table->wrap[] = 'nowrap';
                $table->size[] = '*';

                $table->head[]= get_string('score','scorm');
                $table->align[] = 'center';
                $table->wrap[] = 'nowrap';
                $table->size[] = '*';

                foreach($scousers as $scouser){
                    $userdata = scorm_get_user_data($scouser->userid);
                    $attempt = scorm_get_last_attempt($scorm->id,$scouser->userid);
                    for ($a = 1; $a<=$attempt; $a++) {
                        $row = array();
                        if (has_capability('mod/scorm:deleteresponses',$contextmodule)) {
                            $row[] = '<input type="checkbox" name="attemptid[]" value="'. $scouser->userid . ':' . $a . '" />';
                        }
                        $row[] = print_user_picture($scouser->userid, $course->id, $userdata->picture, false, true);
                        $row[] = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$scouser->userid.'&amp;course='.$course->id.'">'.
                                 fullname($userdata).'</a>';
                        $row[] = '<a href="report.php?a='.$scorm->id.'&amp;user='.$scouser->userid.'&amp;attempt='.$a.'">'.$a.'</a>';
                        $select = 'scormid = '.$scorm->id.' and userid = '.$scouser->userid.' and attempt = '.$a;
//                        $timetracks = get_record_select('scorm_scoes_track', $select,'min(timemodified) as started, max(timemodified) as last');
                        $timetracks = scorm_get_sco_runtime($scorm->id, false, $scouser->userid, $a);
                        // jump out here if this attempt doesnt exist
//                        if (!$timetracks->started) {
                        if (!$timetracks->start) {
                            continue;
                        }
//                        $row[] = userdate($timetracks->started, get_string('strftimedaydatetime'));
//                        $row[] = userdate($timetracks->last, get_string('strftimedaydatetime'));
                        $row[] = userdate($timetracks->start, get_string('strftimedaydatetime'));
                        $row[] = userdate($timetracks->finish, get_string('strftimedaydatetime'));
                        $row[] = scorm_grade_user_attempt($scorm, $scouser->userid, $a);
                        $table->data[] = $row;
                    }
                }
                echo '<div id="scormtablecontainer">';
                if (has_capability('mod/scorm:deleteresponses',$contextmodule)) {
                    echo '<form id="attemptsform" method="post" action="'.$_SERVER['PHP_SELF'].'" onsubmit="var menu = document.getElementById(\'menuaction\'); return (menu.options[menu.selectedIndex].value == \'delete\' ? \''.addslashes_js(get_string('deleteattemptcheck','quiz')).'\' : true);">';
                    echo '<input type="hidden" name="id" value="'.$id.'">';
                    print_table($table);
                    echo '<a href="javascript:select_all_in(\'DIV\',null,\'scormtablecontainer\');">'.get_string('selectall', 'quiz').'</a> / ';
                    echo '<a href="javascript:deselect_all_in(\'DIV\',null,\'scormtablecontainer\');">'.get_string('selectnone', 'quiz').'</a> ';
                    echo '&nbsp;&nbsp;';
                    $options = array('delete' => get_string('delete'));
                    echo choose_from_menu($options, 'action', '', get_string('withselected', 'quiz'), 'if(this.selectedIndex > 0) submitFormById(\'attemptsform\');', '', true);
                    echo '<noscript id="noscriptmenuaction" style="display: inline;">';
                    echo '<div>';
                    echo '<input type="submit" value="'.get_string('go').'" /></div></noscript>';
                    echo '<script type="text/javascript">'."\n<!--\n".'document.getElementById("noscriptmenuaction").style.display = "none";'."\n-->\n".'</script>';
                    echo '</form>';
                } else {
                    print_table($table);
                }
                echo '</div>';
            } else {
                notify(get_string('noactivity', 'scorm'));
            }
        } else {
            if (!empty($user)) {
                // User SCORM report
                if ($scoes = get_records_select('scorm_scoes',"scorm='$scorm->id' ORDER BY id")) {
                    if (!empty($userdata)) {
                        print_simple_box_start('center');
                        echo '<div class="mdl-align">'."\n";
                        print_user_picture($user, $course->id, $userdata->picture, false, false);
                        echo "<a href=\"$CFG->wwwroot/user/view.php?id=$user&amp;course=$course->id\">".
                             "$userdata->firstname $userdata->lastname</a><br />";
                        echo get_string('attempt','scorm').': '.$attempt;
                        echo '</div>'."\n";
                        print_simple_box_end();

                        // Print general score data
                        $table = new stdClass();
                        $table->head = array(get_string('title','scorm'),
                                             get_string('status','scorm'),
                                             get_string('time','scorm'),
                                             get_string('score','scorm'),
                                             '');
                        $table->align = array('left', 'center','center','right','left');
                        $table->wrap = array('nowrap', 'nowrap','nowrap','nowrap','nowrap');
                        $table->width = '80%';
                        $table->size = array('*', '*', '*', '*', '*');
                        foreach ($scoes as $sco) {
                            if ($sco->launch!='') {
                                $row = array();
                                $score = '&nbsp;';
                                if ($trackdata = scorm_get_tracks($sco->id,$user,$attempt)) {
                                    if ($trackdata->score_raw != '') {
                                        $score = $trackdata->score_raw;
                                    }
                                    if ($trackdata->status == '') {
                                        $trackdata->status = 'notattempted';
                                    }
                                    $detailslink = '<a href="report.php?b='.$sco->id.'&amp;user='.$user.'&amp;attempt='.$attempt.'" title="'.
                                                    get_string('details','scorm').'">'.get_string('details','scorm').'</a>';
                                } else {
                                    $trackdata->status = 'notattempted';
                                    $trackdata->total_time = '&nbsp;';
                                    $detailslink = '&nbsp;';
                                }
                                $strstatus = get_string($trackdata->status,'scorm');
                                $row[] = '<img src="'.$scormpixdir.'/'.$trackdata->status.'.gif" alt="'.$strstatus.'" title="'.
                                         $strstatus.'" />&nbsp;'.format_string($sco->title);
                                $row[] = get_string($trackdata->status,'scorm');
                                $row[] = scorm_format_date_time($trackdata->total_time);
                                $row[] = $score;
                                $row[] = $detailslink;
                            } else {
                                $row = array(format_string($sco->title), '&nbsp;', '&nbsp;', '&nbsp;', '&nbsp;');
                            }
                            $table->data[] = $row;
                        }
                        print_table($table);
                    }
                }
            } else {
                notice('No users to report');
            }
        }
    } else {
        // User SCO report
        if (!empty($userdata)) {
            print_simple_box_start('center');
            //print_heading(format_string($sco->title));
            print_heading('<a href="'.$CFG->wwwroot.'/mod/scorm/player.php?a='.$scorm->id.'&amp;mode=browse&amp;scoid='.$sco->id.'" target="_new">'.format_string($sco->title).'</a>');
            echo '<div class="mdl-align">'."\n";
            print_user_picture($user, $course->id, $userdata->picture, false, false);
            echo "<a href=\"$CFG->wwwroot/user/view.php?id=$user&amp;course=$course->id\">".
                 "$userdata->firstname $userdata->lastname</a><br />";
            $scoreview = '';
            if ($trackdata = scorm_get_tracks($sco->id,$user,$attempt)) {
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
            echo '<img src="'.$scormpixdir.'/'.$trackdata->status.'.gif" alt="'.$strstatus.'" title="'.
            $strstatus.'" />&nbsp;'.scorm_format_date_time($trackdata->total_time).'<br />'.$scoreview.'<br />';
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
                                  'status' => 'cmi.completion_status',
                                  'time' => 'cmi.total_time');
            } else {
                $elements = array('raw' => 'cmi.core.score.raw',
                                  'min' => 'cmi.core.score.min',
                                  'max' => 'cmi.core.score.max',
                                  'status' => 'cmi.core.lesson_status',
                                  'time' => 'cmi.core.total_time');
            }
            $printedelements = array();
            foreach ($elements as $key => $element) {
                if (isset($trackdata->$element)) {
                    $existelements = true;
                    $printedelements[]=$element;
                    $row = array();
                    $row[] = get_string($key,'scorm');
                    if ($key == 'time') {
                        $row[] = s(scorm_format_date_time($trackdata->$element));
                    } else {
                        $row[] = s($trackdata->$element);
                    }
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
                                  'cmi.interactions.'.$i.'.learner_response');
                $row = array();
                foreach ($elements as $element) {
                    if (isset($trackdata->$element)) {
                        $row[] = s($trackdata->$element);
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
                        $row[] = s($trackdata->$element);
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
                        if (strpos($element, '_time') === false) {
                            $row[] = s($value);
                        } else {
                            $row[] = s(scorm_format_date_time($value));
                        }
                        $table->data[] = $row;
                    }
                }
            }
            if ($existelements) {
                echo '<h3>'.get_string('othertracks','scorm').'</h3>';
                print_table($table);
            }
            print_simple_box_end();
        } else {
            error('Missing script parameter');
        }
    }


    if (empty($noheader)) {
        print_footer($course);
    }
?>
