<?php

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

    $url = new moodle_url('/mod/scorm/report.php');
    if ($user !== '') {
        $url->param('user', $user);
    }
    if ($attempt !== '1') {
        $url->param('attempt', $attempt);
    }
    if ($action !== '') {
        $url->param('action', $action);
    }

    if (!empty($id)) {
        $url->param('id', $id);
        if (! $cm = get_coursemodule_from_id('scorm', $id)) {
            print_error('invalidcoursemodule');
        }
        if (! $course = $DB->get_record('course', array('id'=>$cm->course))) {
            print_error('coursemisconf');
        }
        if (! $scorm = $DB->get_record('scorm', array('id'=>$cm->instance))) {
            print_error('invalidcoursemodule');
        }
    } else {
        if (!empty($b)) {
            $url->param('b', $b);
            if (! $sco = $DB->get_record('scorm_scoes', array('id'=>$b))) {
                print_error('invalidactivity', 'scorm');
            }
            $a = $sco->scorm;
        }
        if (!empty($a)) {
            $url->param('a', $a);
            if (! $scorm = $DB->get_record('scorm', array('id'=>$a))) {
                print_error('invalidcoursemodule');
            }
            if (! $course = $DB->get_record('course', array('id'=>$scorm->course))) {
                print_error('coursemisconf');
            }
            if (! $cm = get_coursemodule_from_instance('scorm', $scorm->id, $course->id)) {
                print_error('invalidcoursemodule');
            }
        }
    }
    $PAGE->set_url($url);

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

        $PAGE->set_title("$course->shortname: ".format_string($scorm->name));
        $PAGE->set_heading($course->fullname);
        $PAGE->navbar->add($strreport, new moodle_url('/mod/scorm/report.php', array('id'=>$cm->id)));

        if (empty($b)) {
            if (!empty($a)) {
                $PAGE->navbar->add("$strattempt $attempt - ".fullname($userdata));
            }
        } else {
            $PAGE->navbar->add("$strattempt $attempt - ".fullname($userdata), new moodle_url('/mod/scorm/report.php', array('a'=>$a, 'user'=>$user, 'attempt'=>$attempt)));
            $PAGE->navbar->add($sco->title);
        }
        echo $OUTPUT->header();
        echo $OUTPUT->heading(format_string($scorm->name));
    }

    if ($action == 'delete' && has_capability('mod/scorm:deleteresponses',$contextmodule)) {
        if (scorm_delete_responses($attemptids, $scorm->id)) { //delete responses.
            echo $OUTPUT->notification(get_string('scormresponsedeleted', 'scorm'), 'notifysuccess');
        }
    }

    if (empty($b)) {
        if (empty($a)) {
            // No options, show the global scorm report

            if (!empty($CFG->enablegroupings) && !empty($cm->groupingid)) {
                $sql = "SELECT st.userid, st.scormid
                        FROM {scorm_scoes_track} st
                            INNER JOIN {groups_members} gm ON st.userid = gm.userid
                            INNER JOIN {groupings_groups} gg ON gm.groupid = gg.groupid
                        WHERE st.scormid = ? AND gg.groupingid = ?
                        GROUP BY st.userid,st.scormid
                        ";
                $params = array($scorm->id, $cm->groupingid);
            } else {
                $sql = "SELECT st.userid, st.scormid
                        FROM {scorm_scoes_track} st
                        WHERE st.scormid = ?
                        GROUP BY st.userid,st.scormid
                        ";
                $params = array($scorm->id);
            }

            if ($scousers=$DB->get_records_sql($sql, $params)) {
                $table = new html_table();
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
                        //TODO: fetch the user details elsewhere - this is a performance problem!!
                        $user = (object)array('id'=>$scouser->userid);
                        $row[] = $OUTPUT->user_picture($user, array('courseid'=>$course->id));
                        $row[] = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$scouser->userid.'&amp;course='.$course->id.'">'.
                                 fullname($userdata).'</a>';
                        $row[] = '<a href="report.php?a='.$scorm->id.'&amp;user='.$scouser->userid.'&amp;attempt='.$a.'">'.$a.'</a>';
                        $select = 'scormid = ? and userid = ? and attempt = ?';
                        $params = array($scorm->id, $scouser->userid, $a);
                        $timetracks = scorm_get_sco_runtime($scorm->id, false, $scouser->userid, $a);
                        // jump out here if this attempt doesnt exist
                        if (!$timetracks->start) {
                            continue;
                        }
                        $row[] = userdate($timetracks->start, get_string('strftimedaydatetime'));
                        $row[] = userdate($timetracks->finish, get_string('strftimedaydatetime'));

                        $row[] = scorm_grade_user_attempt($scorm, $scouser->userid, $a);
                        $table->data[] = $row;
                    }
                }
                echo '<div id="scormtablecontainer">';
                if (has_capability('mod/scorm:deleteresponses',$contextmodule)) {
                    echo '<form id="attemptsform" method="post" action="'.$FULLSCRIPT.'" onsubmit="var menu = document.getElementById(\'menuaction\'); return (menu.options[menu.selectedIndex].value == \'delete\' ? \''.addslashes_js(get_string('deleteattemptcheck','quiz')).'\' : true);">';
                    echo '<input type="hidden" name="id" value="'.$id.'">';
                    echo $OUTPUT->table($table);
                    echo '<a href="javascript:select_all_in(\'DIV\',null,\'scormtablecontainer\');">'.get_string('selectall', 'quiz').'</a> / ';
                    echo '<a href="javascript:deselect_all_in(\'DIV\',null,\'scormtablecontainer\');">'.get_string('selectnone', 'quiz').'</a> ';
                    echo '&nbsp;&nbsp;';
                    echo html_writer::tag('label', get_string('withselected', 'quiz'), array('for'=>'menuaction'));
                    echo html_writer::select(array('delete' => get_string('delete')), 'action', '', array(''=>'choosedots'), array('id'=>'menuaction'));
                    $PAGE->requires->js_init_call('M.util.init_select_autosubmit', array('attemptsform', 'menuaction', ''));
                    echo '<noscript id="noscriptmenuaction" style="disaply:inline">';
                    echo '<div>';
                    echo '<input type="submit" value="'.get_string('go').'" /></div></noscript>';
                    echo '</form>';
                } else {
                    echo $OUTPUT->table($table);
                }
                echo '</div>';
            } else {
                echo $OUTPUT->notification(get_string('noactivity', 'scorm'));
            }
        } else {
            if (!empty($user)) {
                // User SCORM report
                if ($scoes = $DB->get_records_select('scorm_scoes',"scorm=? ORDER BY id", array($scorm->id))) {
                    if (!empty($userdata)) {
                        echo $OUTPUT->box_start('generalbox boxaligncenter');
                        echo '<div class="mdl-align">'."\n";
                        $userrec = (object)array('id'=>$user);
                        echo $OUTPUT->user_picture($userrec, array('courseid'=>$course->id));
                        echo "<a href=\"$CFG->wwwroot/user/view.php?id=$user&amp;course=$course->id\">".
                             "$userdata->firstname $userdata->lastname</a><br />";
                        echo get_string('attempt','scorm').': '.$attempt;
                        echo '</div>'."\n";
                        echo $OUTPUT->box_end();

                        // Print general score data
                        $table = new html_table();
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
                                $row[] = '<img src="'.$OUTPUT->pix_url('pix/' . $trackdata->status, 'scorm').'" alt="'.$strstatus.'" title="'.
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
                        echo $OUTPUT->table($table);
                    }
                }
            } else {
                notice('No users to report');
            }
        }
    } else {
        // User SCO report
        if (!empty($userdata)) {
            echo $OUTPUT->box_start('generalbox boxaligncenter');
            //print_heading(format_string($sco->title));
            echo $OUTPUT->heading('<a href="'.$CFG->wwwroot.'/mod/scorm/player.php?a='.$scorm->id.'&amp;mode=browse&amp;scoid='.$sco->id.'" target="_new">'.format_string($sco->title).'</a>');
            echo '<div class="mdl-align">'."\n";
            $userrec = (object)array('id'=>$user);
            echo $OUTPUT->user_picture($userrec, array('courseid'=>$course->id));
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
            echo '<img src="'.$$OUTPUT->pix_url('pix/'.$trackdata->status, 'scorm').'" alt="'.$strstatus.'" title="'.
            $strstatus.'" />&nbsp;'.scorm_format_date_time($trackdata->total_time).'<br />'.$scoreview.'<br />';
            echo '</div>'."\n";
            echo '<hr /><h2>'.get_string('details','scorm').'</h2>';

            // Print general score data
            $table = new html_table();
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
                echo $OUTPUT->table($table);
            }

            // Print Interactions data
            $table = new html_table();
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
                echo $OUTPUT->table($table);
            }

            // Print Objectives data
            $table = new html_table();
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
                echo $OUTPUT->table($table);
            }
            $table = new html_table();
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
                echo $OUTPUT->table($table);
            }
            echo $OUTPUT->box_end();
        } else {
            print_error('missingparameter');
        }
    }


    if (empty($noheader)) {
        echo $OUTPUT->footer();
    }

