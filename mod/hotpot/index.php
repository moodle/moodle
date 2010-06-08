<?PHP

// This page lists all the instances of hotpot in a particular course

    require_once("../../config.php");
    require_once("../../course/lib.php");
    require_once("lib.php");

    $id = required_param('id', PARAM_INT);   // course

    $PAGE->set_url('/mod/hotpot/index.php', array('id'=>$id));

    if (!$course = $DB->get_record('course', array('id'=>$id))) {
        print_error('invalidcourseid');
    }

    require_login($course->id);
    $PAGE->set_pagelayout('incourse');

    $coursecontext = get_context_instance(CONTEXT_COURSE, $id);
    $sitecontext = get_context_instance(CONTEXT_SYSTEM);

    add_to_log($course->id, "hotpot", "view all", "index.php?id=$course->id", "");

    $sesskey = '<input type="hidden" name="sesskey" value="'.sesskey().'" />';

    // get message strings for titles
    $strmodulenameplural = get_string("modulenameplural", "hotpot");
    $strmodulename  = get_string("modulename", "hotpot");
    $strsectionname  = get_string('sectionname', 'format_'.$course->format);

    // string translation array for single and double quotes
    $quotes = array("'"=>"\'", '"'=>'&quot;');

    // Print the header
    $PAGE->navbar->add($strmodulenameplural);
    $PAGE->set_title(format_string($course->shortname) . ": $strmodulenameplural");
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();

    $next_url = "$CFG->wwwroot/course/view.php?id=$course->id";

    // get display section, if any
    $section = optional_param('section', 0, PARAM_ALPHANUM);
    if ($section=='all') {
        // do nothing
    } else {
        $section = intval($section);
    }
    if ($section) {
        $displaysection = course_set_display($course->id, $section);
    } else {
        if (isset($USER->display[$course->id])) {
            $displaysection = $USER->display[$course->id];
        } else {
            $displaysection = 0;
        }
    }

    // Get all hotpot instances in this course
    $hotpots = array();
    if ($hotpot_instances = hotpot_get_all_instances_in_course('hotpot', $course)) {
        foreach ($hotpot_instances as $hotpot_instance) {
            if ($displaysection>0 && $hotpot_instance->section>0 && $displaysection<>$hotpot_instance->section) {
                // do nothing (user is not diplaying this section)
            } else {
                $hotpots[$hotpot_instance->id] = $hotpot_instance;
            }
        }
    }
    if (empty($hotpots)) {
        notice(get_string('thereareno', 'moodle', $strmodulenameplural), $next_url);
        exit;
    }
    $hotpotids = implode(',', array_keys($hotpots));

    $usesections = course_format_uses_sections($course->format);
    if ($usesections) {
        $sections = get_all_sections($course->id);
    }

    if (has_capability('mod/hotpot:grade', $sitecontext)) {

        // array of hotpots to be regraded
        $regrade_hotpots = array();

        // do we need to regrade any or all of the hotpots?
        $regrade = optional_param('regrade', 0, PARAM_SEQUENCE);
        if ($regrade) {
            // add valid hotpot ids to the regrade array
            $regrade = explode(',', $regrade);
            foreach ($regrade as $id) {
                if (isset($hotpots[$id])) {
                    $regrade_hotpots[$id] = &$hotpots[$id];
                }
            }
            $regrade = implode(',', array_keys($regrade_hotpots));
        }
        if ($regrade) {

            $confirm = optional_param('confirm', 0, PARAM_BOOL);
            if (!$confirm) {

                echo $OUTPUT->box_start("generalbox boxaligncenter boxwidthnormal errorboxcontent");

                if (count($regrade_hotpots)==1) {
                    echo $OUTPUT->heading(get_string('regradecheck', 'hotpot', $regrade_hotpots[$regrade]->name));
                } else {
                    echo $OUTPUT->heading(get_string('regradecheck', 'hotpot', ''));
                    print '<ul>';
                    foreach ($regrade_hotpots as $hotpot) {
                        print "<li>$hotpot->name</li>";
                    }
                    print '</ul>';
                }
                print ''
                .   '<div class="mdl-align"><table border="0"><tr><td>'
                .   '<form target="_parent" method="post" action="index.php">'
                .   '<input type="hidden" name="id" value="'.$course->id.'" />'
                .   '<input type="hidden" name="regrade" value="'.$regrade.'" />'
                .   '<input type="hidden" name="confirm" value="1" />'
                .   $sesskey
                .   '<input type="submit" value="'.get_string("yes").'" />'
                .   '</form>'
                .   '</td><td> &nbsp; </td><td>'
                .   '<form target="_parent" method="post" action="index.php">'
                .   '<input type="hidden" name="id" value="'.$course->id.'" />'
                .   $sesskey
                .   '<input type="submit" value="'.get_string("no").'" />'
                .   '</form>'
                .   '</td></tr></table></div>'
                ;

                echo $OUTPUT->box_end();
                echo $OUTPUT->footer();
                exit;

            } else { // regrade has been confirmed, so proceed

                // start hotpot counter and timer
                $hotpotstart = microtime();
                $hotpotcount = 0;

                // regrade attempts for these hotpots
                foreach ($regrade_hotpots as $hotpot) {
                    echo $OUTPUT->notification("<b>$hotpot->name</b>");

                    // delete questions and responses for this hotpot
                    if ($records = $DB->get_records('hotpot_questions', array('hotpot'=>$hotpot->id), '', 'id,hotpot')) {
                        $questionids = implode(',', array_keys($records));
                        hotpot_delete_and_notify('hotpot_questions', "id IN ($questionids)", array(), get_string('question', 'quiz'));
                        hotpot_delete_and_notify('hotpot_responses', "question IN ($questionids)", array(), get_string('answer', 'quiz'));
                    }

                    // start attempt counter and timer
                    $attemptstart = microtime();
                    $attemptcount = 0;

                    // regrade attempts, if any, for this hotpot
                    if ($attempts = $DB->get_records('hotpot_attempts', array('hotpot'=>$hotpot->id))) {
                        foreach ($attempts as $attempt) {
                            $attempt->details = $DB->get_field('hotpot_details', 'details', array('attempt'=>$attempt->id));
                            if ($attempt->details) {
                                hotpot_add_attempt_details($attempt);
                                $DB->update_record('hotpot_attempts', $attempt);
                            }
                            $attemptcount++;
                        }
                    }
                    if ($attemptcount) {
                        $msg = get_string('added', 'moodle', "$attemptcount x ".get_string('attempts', 'quiz'));
                        if (!empty($CFG->hotpot_showtimes)) {
                            $msg .= ' ('.format_time(sprintf("%0.2f", microtime_diff($attemptstart, microtime()))).')';
                        }
                        echo $OUTPUT->notification($msg);
                    }
                    $hotpotcount++;
                } // end foreach $hotpots
                if ($hotpotcount) {
                    $msg = get_string('regrade', 'quiz').": $hotpotcount x ".get_string('modulenameplural', 'hotpot');
                    if (!empty($CFG->hotpot_showtimes)) {
                        $msg .= ' ('.format_time(sprintf("%0.2f", microtime_diff($hotpotstart, microtime()))).')';
                    }
                    echo $OUTPUT->notification($msg);
                }
                echo $OUTPUT->notification(get_string('regradecomplete', 'quiz'));
            } // end if $confirm
        } // end regrade

        // get duplicate hotpot-name questions
        //  - JMatch LHS is longer than 255 bytes
        //  - JQuiz question text is longer than 255 bytes
        //  - other unidentified situations ?!

        $regrade_hotpots = array();
        $concat_field = $DB->sql_concat('hotpot', "'_'", 'name');
        if ($concat_field) {
            $records = $DB->get_records_sql("
                SELECT $concat_field, COUNT(*), hotpot, name
                FROM {hotpot_questions}
                WHERE hotpot IN ($hotpotids)
                GROUP BY hotpot, name
                HAVING COUNT(*) >1
            ");
            if ($records) {
                foreach ($records as $record) {
                    $regrade_hotpots[$record->hotpot] = 1;
                }
                ksort($regrade_hotpots);
                $regrade_hotpots = array_keys($regrade_hotpots);
            }
        }
    }

    // start timer
    $start = microtime();

    // get total number of attempts, users and details for these hotpots
    $params = array();
    $tables = "{hotpot_attempts} a";
    $fields = "
        a.hotpot AS hotpot,
        COUNT(DISTINCT a.clickreportid) AS attemptcount,
        COUNT(DISTINCT a.userid) AS usercount,
        MAX(a.score) AS maxscore
    ";
    $select = "a.hotpot IN ($hotpotids)";
    if (has_capability('mod/hotpot:viewreport', $coursecontext)) {
        // do nothing (=get all users)
    } else {
        // restrict results to this user only
        $select .= " AND a.userid=:userid";
        $params['userid'] = $USER->id;
    }
    $usejoin = 0;
    if (has_capability('mod/hotpot:grade', get_context_instance(CONTEXT_SYSTEM)) && $usejoin) {
        // join attempts table and details table
        $tables .= ",{hotpot_details} d";
        $fields .= ',COUNT(DISTINCT d.id) AS detailcount';
        $select .= " AND a.id=d.attempt";

        // this may take about twice as long as getting the gradecounts separately :-(
        // so this operation could be done after getting the $totals from the attempts table
    }
    $totals = $DB->get_records_sql("SELECT $fields FROM $tables WHERE $select GROUP BY a.hotpot", $params);

    if (has_capability('mod/hotpot:grade', get_context_instance(CONTEXT_SYSTEM)) && empty($usejoin)) {
        foreach ($hotpots as $hotpot) {
            $totals[$hotpot->id]->detailcount = 0;
            if ($ids = $DB->get_records('hotpot_attempts', array('hotpot'=>$hotpot->id))) {
                $ids = join(',', array_keys($ids));
                $totals[$hotpot->id]->detailcount = $DB->count_records_select('hotpot_details', "attempt IN ($ids)");
            }
        }
    }

    // message strings for main table
    $strusers  = get_string('users');
    $strupdate = get_string('update');
    $strregrade = get_string('regrade', 'hotpot');
    $strneverclosed = get_string('neverclosed', 'hotpot');
    $strregraderequired = get_string('regraderequired', 'hotpot');

    // column headings and attributes
    $table = new html_table();
    $table->head = array();
    $table->align = array();

    if (!empty($CFG->hotpot_showtimes)) {
        print '<H3>'.sprintf("%0.3f", microtime_diff($start, microtime())).' secs'."</H3>\n";
    }

    $title = $strsectionname;
    if ($title) {
        array_push($table->head, $title);
        array_push($table->align, "center");
    }
    if (has_capability('moodle/course:manageactivities', $coursecontext)) {
        array_push($table->head, $strupdate);
        array_push($table->align, "center");
    }
    array_push($table->head,
        get_string("name"),
        get_string("quizcloses", "quiz"),
        get_string("bestgrade", "quiz"),
        get_string("attempts", "quiz")
    );
    array_push($table->align,
        "left", "left", "center", "left"
    );
    if (has_capability('mod/hotpot:grade', $coursecontext)) {
        array_push($table->head, $strregrade);
        array_push($table->align, "center");
    }

    $currentsection = -1;
    foreach ($hotpots as $hotpot) {

        $printsection = "";
        if ($hotpot->section != $currentsection) {
            if ($hotpot->section) {
                if ($usesections) {
                    $printsection = get_section_name($course, $sections[$hotpot->section]);
                    // Show the zoom boxes
                    if ($displaysection==$hotpot->section) {
                        $strshowall = get_string('showall'.$course->format);
                        $printsection .= '<br /><a href="index.php?id='.$course->id.'&amp;section=all" title="'.$strshowall.'"><img src="'.$OUTPUT->pix_url('i/all') . '" style="height:25px; width:16px; border:0px" alt="'.$strshowall.'" /></a><br />';
                    } else {
                        $strshowone = get_string('showonly'.preg_replace('|s$|', '', $course->format, 1), '', $hotpot->section);
                        $printsection .=  '<br /><a href="index.php?id='.$course->id.'&amp;section='.$hotpot->section.'" title="'.$strshowone.'"><img src="'.$OUTPUT->pix_url('i/one') . '" class="icon" alt="'.$strshowone.'" /></a><br />';
                    }
                }
            }
            if ($currentsection>=0) {
                $table->data[] = 'hr';
            }
            $currentsection = $hotpot->section;
        }

        $class = ($hotpot->visible) ? '' : 'class="dimmed" ';
        $quizname = '<a '.$class.'href="view.php?id='.$hotpot->coursemodule.'">'.$hotpot->name.'</a>';
        $quizclose = empty($hotpot->timeclose) ? $strneverclosed : userdate($hotpot->timeclose);

        // are there any totals for this hotpot?
        if (empty($totals[$hotpot->id]->attemptcount)) {
            $report = "&nbsp;";
            $bestscore = "&nbsp;";

        } else {

            $cm = get_coursemodule_from_instance('hotpot', $hotpot->id);
            // report number of attempts and users
            $report = get_string("viewallreports","quiz", $totals[$hotpot->id]->attemptcount);
            if (has_capability('mod/hotpot:viewreport', get_context_instance(CONTEXT_MODULE, $cm->id))) {
                $report .= " (".$totals[$hotpot->id]->usercount." $strusers)";
            }
            $report = '<a href="report.php?hp='.$hotpot->id.'">'.$report.'</a>';

            // get best score
            if (is_numeric($totals[$hotpot->id]->maxscore)) {
                $weighting = $hotpot->grade / 100;
                $precision = hotpot_get_precision($hotpot);
                $bestscore = round($totals[$hotpot->id]->maxscore * $weighting, $precision)." / $hotpot->grade";
            } else {
                $bestscore = "&nbsp;";
            }
        }

        if (has_capability('mod/hotpot:grade', $sitecontext)) {
            if (in_array($hotpot->id, $regrade_hotpots)) {
                $report .= ' <font color="red">'.$strregraderequired.'</font>';
            }
        }

        $data = array ();

        if ($usesections) {
            array_push($data, $printsection);
        }

        if (has_capability('moodle/course:manageactivities', $coursecontext)) {
            $updatebutton = ''
            .   '<form method="get" action="'.$CFG->wwwroot.'/course/mod.php">'
            .   '<input type="hidden" name="update" value="'.$hotpot->coursemodule.'" />'
            .   $sesskey
            .   '<input type="submit" value="'.$strupdate.'" />'
            .   '</form>'
            ;
            array_push($data, $updatebutton);
        }

        array_push($data, $quizname, $quizclose, $bestscore, $report);

        if (has_capability('mod/hotpot:grade', $sitecontext)) {
            if (empty($totals[$hotpot->id]->detailcount)) {
                // no details records for this hotpot, so disable regrade
                $regradebutton = '&nbsp;';
            } else {
                $strregradecheck = get_string('regradecheck', 'hotpot', strtr($hotpot->name, $quotes));
                $regradebutton = ''
                .   '<form target="_parent" method="post" action="index.php" onsubmit="var x=window.confirm('."'$strregradecheck'".');this.confirm.value=x;return x;">'
                .   '<input type="hidden" name="id" value="'.$course->id.'" />'
                .   '<input type="hidden" name="regrade" value="'.$hotpot->id.'" />'
                .   '<input type="hidden" name="confirm" value="" />'
                .   $sesskey
                .   '<input type="submit" value="'.$strregrade.'" />'
                .   '</form>'
                ;
            }
            array_push($data, $regradebutton);
        }

        $table->data[] = $data;
    }

    echo "<br />";

    echo html_writer::table($table);

    // Finish the page
    echo $OUTPUT->footer();

