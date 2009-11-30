<?php  // $Id$

/*************************************************
    ACTIONS handled are:

    displayfinalgrade (for students)
    notavailable (for students)
    studentsview
    submitexample
    teachersview
    showdescription
    showallsubmissions

************************************************/

    require("../../config.php");
    require("lib.php");
    require("locallib.php");

    $id     = required_param('id', PARAM_INT);    // Course Module ID
    $action = optional_param('action', '', PARAM_ALPHA);
    $sort   = optional_param('sort', 'lastname', PARAM_ALPHA);
    $dir    = optional_param('dir', 'ASC', PARAM_ALPHA);

    $timenow = time();

    // get some useful stuff...
    if (! $cm = get_coursemodule_from_id('workshop', $id)) {
        error("Course Module ID was incorrect");
    }
    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }
    if (! $workshop = get_record("workshop", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $strworkshops = get_string("modulenameplural", "workshop");
    $strworkshop  = get_string("modulename", "workshop");

    // ...and if necessary set default action
    if (workshop_is_teacher($workshop)) {
        if (empty($action)) { // no action specified, either go straight to elements page else the admin page
            // has the assignment any elements
            if (count_records("workshop_elements", "workshopid", $workshop->id) >= $workshop->nelements) {
                $action = "teachersview";
            }
            else {
                redirect("assessments.php?action=editelements&id=$cm->id");
            }
        }
    } else { // it's a student then
        if (!$cm->visible) {
            notice(get_string("activityiscurrentlyhidden"));
        }
        if ($timenow < $workshop->submissionstart) {
            $action = 'notavailable';
        } else if (!$action) {
            if ($timenow < $workshop->assessmentend) {
                $action = 'studentsview';
            } else {
                $action = 'displayfinalgrade';
            }
        }
    }

    // ...display header...
    $navigation = build_navigation($action, $cm);
    print_header_simple(format_string($workshop->name), "", $navigation,
                  "", "", true, update_module_button($cm->id, $course->id, $strworkshop), navmenu($course, $cm));


    // ...log activity...
    add_to_log($course->id, "workshop", "view", "view.php?id=$cm->id", $workshop->id, $cm->id);

    if ($action == 'studentsview' and !workshop_is_student($workshop)) {
        $action = 'showdescription';
    }

    /****************** display final grade (for students) ************************************/
    if ($action == 'displayfinalgrade' ) {
        require_capability('mod/workshop:participate', $context);

        print_heading("<b><a href=\"view.php?id=$cm->id&amp;action=showdescription\">".
                get_string("showdescription", 'workshop')."</a></b>");
        // show the final grades as stored in the tables...
        if ($submissions = workshop_get_user_submissions($workshop, $USER)) { // any submissions from user?
            print_heading(get_string("displayoffinalgrades", "workshop"));
            echo "<div class=\"boxaligncenter\"><table border=\"1\" width=\"90%\"><tr>";
            echo "<td><b>".get_string("submissions", "workshop")."</b></td>";
            if ($workshop->wtype) {
                echo "<td align=\"center\"><b>".get_string("assessmentsdone", "workshop")."</b></td>";
                echo "<td align=\"center\"><b>".get_string("gradeforassessments", "workshop")."</b></td>";
            }
            echo "<td align=\"center\"><b>".get_string("teacherassessments", "workshop",
                        $course->teacher)."</b></td>";
            if ($workshop->wtype) {
                echo "<td align=\"center\"><b>".get_string("studentassessments", "workshop",
                        $course->student)."</b></td>";
            }
            echo "<td align=\"center\"><b>".get_string("gradeforsubmission", "workshop")."</b></td>";
            echo "<td align=\"center\"><b>".get_string("overallgrade", "workshop")."</b></td></tr>\n";
            foreach ($submissions as $submission) {
                $grade = workshop_submission_grade($workshop, $submission);
                if ($workshop->wtype) {
                    $gradinggrade = workshop_gradinggrade($workshop, $USER);
                } else { // ignore grading grades for simple assignments
                    $gradinggrade = 0;
                }
                echo "<tr><td>".workshop_print_submission_title($workshop, $submission)."</td>\n";
                if ($workshop->wtype) {
                    echo "<td align=\"center\">".workshop_print_user_assessments($workshop, $USER, $gradinggrade)."</td>";
                    echo "<td align=\"center\">$gradinggrade</td>";
                }
                echo "<td align=\"center\">".workshop_print_submission_assessments($workshop,
                            $submission, "teacher")."</td>";
                if ($workshop->wtype) {
                    echo "<td align=\"center\">".workshop_print_submission_assessments($workshop,
                            $submission, "student")."</td>";
                }
                echo "<td align=\"center\">$grade</td>";
                echo "<td align=\"center\">".number_format($gradinggrade + $grade, 1)."</td></tr>\n";
            }
            echo "</table></div><br clear=\"all\" />\n";
            workshop_print_key($workshop);
        } else {
            print_heading(get_string('nowork', 'workshop'));
        }
        if ($workshop->showleaguetable) {
            workshop_print_league_table($workshop);
        }
    }


    /****************** assignment not available (for students)***********************/
    elseif ($action == 'notavailable') {
        print_heading(get_string("notavailable", "workshop"));
    }

    /****************** student's view could be in 1 of 4 stages ***********************/
    elseif ($action == 'studentsview') {
        require_capability('mod/workshop:participate', $context);

        // is a password needed?
        if ($workshop->usepassword) {
            $correctpass = false;
            if (isset($_POST['userpassword'])) {
                if ($workshop->password == md5(trim($_POST['userpassword']))) {
                    $USER->workshoploggedin[$workshop->id] = true;
                    $correctpass = true;
                }
            } elseif (isset($USER->workshoploggedin[$workshop->id])) {
                $correctpass = true;
            }

            if (!$correctpass) {
                print_simple_box_start("center");
                echo "<form id=\"password\" method=\"post\" action=\"view.php\">\n";
                echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />\n";
                echo "<table cellpadding=\"7px\">";
                if (isset($_POST['userpassword'])) {
                    echo "<tr align=\"center\" style='color:#DF041E;'><td>".get_string("wrongpassword", "workshop").
                        "</td></tr>";
                }
                echo "<tr align=\"center\"><td>".get_string("passwordprotectedworkshop", "workshop", format_string($workshop->name)).
                    "</td></tr>";
                echo "<tr align=\"center\"><td>".get_string("enterpassword", "workshop").
                    " <input type=\"password\" name=\"userpassword\" /></td></tr>";

                echo "<tr align=\"center\"><td>";
                echo "<input type=\"button\" value=\"".get_string("cancel").
                    "\" onclick=\"parent.location='../../course/view.php?id=$course->id';\">  ";
                echo "<input type=\"submit\" value=\"".get_string("continue")."\"/>";
                echo "</td></tr></table></form>";
                print_simple_box_end();
                print_footer($course);
                exit();
            }
        }
        workshop_print_assignment_info($workshop);

        // if the student has not yet submitted show the full description
        if (!record_exists('workshop_submissions', 'workshopid', $workshop->id, 'userid', $USER->id)) {
            print_box(format_text($workshop->description, $workshop->format), 'generalbox', 'intro');
        } else {
            print_heading("<b><a href=\"view.php?id=$cm->id&amp;action=showdescription\">".
                get_string("showdescription", 'workshop')."</a></b>");
        }

        // in Stage 1? - are there any teacher's submissions? and...
        // ...has student assessed the required number of the teacher's submissions
        if ($workshop->ntassessments and (!workshop_test_user_assessments($workshop, $USER))) {
            print_heading(get_string("pleaseassesstheseexamplesfromtheteacher", "workshop",
                        $course->teacher));
            workshop_list_teacher_submissions($workshop, $USER);
        }
        // in stage 2? - submit own first attempt
        else {
            if ($workshop->ntassessments) {
                // show assessment of the teacher's examples, there may be feedback from teacher
                print_heading(get_string("yourassessmentsofexamplesfromtheteacher", "workshop",
                            $course->teacher));
                workshop_list_teacher_submissions($workshop, $USER);
            }
            // has user submitted anything yet?
            if (!workshop_get_user_submissions($workshop, $USER)) {
                if ($timenow < $workshop->submissionend) {
                    // print upload form
                    print_heading(get_string("submitassignmentusingform", "workshop").":");
                    workshop_print_upload_form($workshop);
                } else {
                    print_heading(get_string("submissionsnolongerallowed", "workshop"));
                }
            }
            // in stage 3? - grade other student's submissions, resubmit and list all submissions
            else {
                // is self assessment used in this workshop?
                if ($workshop->includeself) {
                    // prints a table if there are any submissions which have not been self assessed yet
                    workshop_list_self_assessments($workshop, $USER);
                }
                // if peer assessments are being done then show some  to assess...
                if ($workshop->nsassessments and ($workshop->assessmentstart < $timenow and $workshop->assessmentend > $timenow)) {
                    workshop_list_student_submissions($workshop, $USER);
                }
                // ..and any they have already done (and have gone cold)...
                if (workshop_count_user_assessments($workshop, $USER, "student")) {
                    print_heading(get_string("yourassessments", "workshop"));
                    workshop_list_assessed_submissions($workshop, $USER);
                }
                // list any assessments by teachers
                if (workshop_count_teacher_assessments_by_user($workshop, $USER) and ($timenow > $workshop->releasegrades)) {
                    print_heading(get_string("assessmentsby", "workshop", $course->teachers));
                    workshop_list_teacher_assessments_by_user($workshop, $USER);
                }
                // ... and show peer assessments
                if (workshop_count_peer_assessments($workshop, $USER)) {
                    print_heading(get_string("assessmentsby", "workshop", $course->students));
                    workshop_list_peer_assessments($workshop, $USER);
                }
                // list previous submissions
                print_heading(get_string("yoursubmissions", "workshop"));
                workshop_list_user_submissions($workshop, $USER);

                // are resubmissions allowed and the workshop is in submission/assessment phase?
                if ($workshop->resubmit and ($timenow > $workshop->assessmentstart and $timenow < $workshop->submissionend)) {
                    // see if there are any cold assessments of the last submission
                    // if there are then print upload form
                    if ($submissions = workshop_get_user_submissions($workshop, $USER)) {
                        foreach ($submissions as $submission) {
                            $lastsubmission = $submission;
                            break;
                        }
                        $n = 0; // number of cold assessments (not include self assessments)
                        if ($assessments = workshop_get_assessments($lastsubmission)) {
                            foreach ($assessments as $assessment) {
                                if ($assessment->userid <> $USER->id) {
                                    $n++;
                                }
                            }
                        }
                        if ($n) {
                            echo "<hr size=\"1\" noshade=\"noshade\" />";
                            print_heading(get_string("submitrevisedassignment", "workshop").":");
                            workshop_print_upload_form($workshop);
                            echo "<hr size=\"1\" noshade=\"noshade\" />";
                        }
                    }
                }
            }
        }
    }


    /****************** submission of example by teacher only***********************/
    elseif ($action == 'submitexample') {

        require_capability('mod/workshop:manage', $context);

        // list previous submissions from teacher
        workshop_list_user_submissions($workshop, $USER);

        echo "<hr size=\"1\" noshade=\"noshade\" />";

        // print upload form
        print_heading(get_string("submitexampleassignment", "workshop").":");
        workshop_print_upload_form($workshop);

        print_heading("<a $CFG->frametarget href=\"view.php?id=$cm->id\">".get_string("cancel")."</a>");
    }


    /****************** teacher's view - display admin page  ************/
    elseif ($action == 'teachersview') {

        require_capability('mod/workshop:manage', $context);

        // automatically grade assessments if workshop has examples and/or peer assessments
        if ($workshop->gradingstrategy and ($workshop->ntassessments or $workshop->nsassessments)) {
            workshop_grade_assessments($workshop);
        }

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        /// find out current groups mode
        $groupmode = groups_get_activity_groupmode($cm);
        $currentgroup = groups_get_activity_group($cm, true);
        groups_print_activity_menu($cm, $CFG->wwwroot . "/mod/workshop/view.php?id=$cm->id");

        /// Print admin links
        echo "<table width=\"100%\"><tr><td>";
        echo "<a href=\"submissions.php?id=$cm->id&amp;action=adminlist\">".
            get_string("administration")."</a>\n";

        echo '</td></tr>';

        echo '<tr><td>';
        workshop_print_assignment_info($workshop);
        print_heading("<a href=\"view.php?id=$cm->id&amp;action=showdescription\">".
               get_string("showdescription", 'workshop')."</a>");
        echo '</td></tr>';

        /// Print grade tables /////////////////////////////////////////////////

        // display the teacher's submissions
        if ($workshop->ntassessments) {
            $table->head = array(get_string("examplesubmissions", "workshop"),
                get_string("assessmentsby", "workshop", $course->teachers),
                get_string("assessmentsby", "workshop", $course->students));
            $table->data = array();
            if ($submissions = workshop_get_teacher_submissions($workshop)) {
                foreach ($submissions as $submission) {
                    $teacherassessments = workshop_print_submission_assessments($workshop, $submission, "teacher");
                    // If not yet assessed, show assess link
                    if ($teacherassessments == '&nbsp;') {
                        $teacherassessments = '<a href="assess.php?id='.
                            $cm->id.'&amp;sid='.$submission->id.'">'.get_string('assess', 'workshop').'</a>';
                    }
                    $title = workshop_print_submission_title($workshop, $submission).
                        " <a href=\"submissions.php?action=editsubmission&amp;id=$cm->id&amp;sid=$submission->id\">".
                        "<img src=\"$CFG->pixpath/t/edit.gif\" ".
                        'class="iconsmall" alt="'.get_string('edit').'" /></a>'.
                        " <a href=\"submissions.php?action=confirmdelete&amp;id=$cm->id&amp;sid=$submission->id\">".
                        "<img src=\"$CFG->pixpath/t/delete.gif\" ".
                        'class="iconsmall" alt="'.get_string('delete', 'workshop').'" /></a>';
                    $table->data[] = array($title, $teacherassessments,
                        workshop_print_submission_assessments($workshop, $submission, "student"));
                }
            }
            // Put in a submission link
            $table->data[] = array("<b><a href=\"view.php?id=$cm->id&amp;action=submitexample\">".
                get_string("submitexampleassignment", "workshop")."</a></b>".
                helpbutton("submissionofexamples", get_string("submitexampleassignment", "workshop"), "workshop", true, false, '', true),
                '&nbsp;', '&nbsp;');
            print_table($table);
            workshop_print_key($workshop);
        }

        // Get all the students
        if (!$users = workshop_get_students($workshop)) {
            echo ('</table>');
            print_heading(get_string("nostudentsyet"));
            print_footer($course);
            exit;
        }

        if (!empty($CFG->enablegroupings) && !empty($cm->groupingid) && !empty($users)) {
            $groupingusers = groups_get_grouping_members($cm->groupingid, 'u.id', 'u.id');
            foreach($users as $key => $user) {
                if (!isset($groupingusers[$user->id])) {
                    unset($users[$key]);
                }
            }
        }

        /// Now prepare table with student assessments and submissions
        $tablesort->data = array();
        $tablesort->sortdata = array();
        foreach ($users as $user) {
            // skip if student not in group
            if ($currentgroup) {
                if (!groups_is_member($currentgroup, $user->id)) {
                    continue;
                }
            }
            if ($submissions = workshop_get_user_submissions($workshop, $user)) {
                foreach ($submissions as $submission) {
                    $data = array();
                    $sortdata = array();

                    $data[] = "<a name=\"userid$user->id\" href=\"{$CFG->wwwroot}/user/view.php?id=$user->id&amp;course=$course->id\">".
                        fullname($user).'</a>';
                    $sortdata['firstname'] = $user->firstname;
                    $sortdata['lastname'] = $user->lastname;

                    if ($workshop->wtype) {
                        $data[] = workshop_print_user_assessments($workshop, $user, $gradinggrade);

                        $data[] = $gradinggrade;
                        $sortdata['agrade'] = $gradinggrade;
                    }

                    $data[] = workshop_print_submission_title($workshop, $submission).
                        " <a href=\"submissions.php?action=adminamendtitle&amp;id=$cm->id&amp;sid=$submission->id\">".
                        "<img src=\"$CFG->pixpath/t/edit.gif\" ".
                        'class="iconsmall" alt="'.get_string('amendtitle', 'workshop').'" /></a>'.
                        " <a href=\"submissions.php?action=confirmdelete&amp;id=$cm->id&amp;sid=$submission->id\">".
                        "<img src=\"$CFG->pixpath/t/delete.gif\" ".
                        'class="iconsmall" alt="'.get_string('delete', 'workshop').'" /></a>';
                    $sortdata['title'] = $submission->title;

                    $data[] = userdate($submission->timecreated, get_string('datestr', 'workshop'));
                    $sortdata['date'] = $submission->timecreated;

                    if (($tmp = workshop_print_submission_assessments($workshop, $submission, "teacher")) == '&nbsp;') {
                        $data[] = '<a href="assess.php?id='.
                            $cm->id.'&amp;sid='.$submission->id.'">'.get_string('assess', 'workshop').'</a>';
                        $sortdata['tassmnt'] = -1;
                    } else {
                        $data[] = $tmp;
                        $sortdata['tassmnt'] = 1; // GWD still have to fix this
                    }

                    if ($workshop->wtype) {
                        $data[] = workshop_print_submission_assessments($workshop, $submission, "student");
                    }

                    $grade = workshop_submission_grade($workshop, $submission);
                    $data[] = $grade;
                    $sortdata['sgrade'] = $grade;

                    if ($workshop->wtype) {
                        $data[] = number_format($gradinggrade + $grade, 1);
                        $sortdata['ograde'] = $gradinggrade + $grade;
                    }

                    $tablesort->data[] = $data;
                    $tablesort->sortdata[] = $sortdata;
                }
            }
        }

        function workshop_sortfunction($a, $b) {
           global $sort, $dir;
           if ($dir == 'ASC') {
               return ($a[$sort] > $b[$sort]);
           } else {
               return ($a[$sort] < $b[$sort]);
           }
        }
        uasort($tablesort->sortdata, 'workshop_sortfunction');
        $table->data = array();
        foreach($tablesort->sortdata as $key => $row) {
            $table->data[] = $tablesort->data[$key];
        }

        if ($workshop->wtype) {
            $table->align = array ('left', 'center', 'center', 'left', 'center', 'center', 'center', 'center', 'center', 'center');
            $columns = array('firstname', 'lastname', 'agrade', 'title', 'date', 'tassmnt', 'sgrade', 'ograde');
        } else {
            $table->align = array ('left', 'left', 'center', 'center', 'center', 'center');
            $columns = array('firstname', 'lastname', 'title', 'date', 'tassmnt', 'ograde');
        }
        $table->width = "95%";

        foreach ($columns as $column) {
            $string[$column] = get_string("$column", 'workshop');
            if ($sort != $column) {
                $columnicon = '';
                $columndir = 'ASC';
            } else {
                $columndir = $dir == 'ASC' ? 'DESC':'ASC';
                if ($column == 'lastaccess') {
                    $columnicon = $dir == 'ASC' ? 'up':'down';
                } else {
                    $columnicon = $dir == 'ASC' ? 'down':'up';
                }
                $columnicon = " <img src=\"$CFG->pixpath/t/$columnicon.gif\" alt=\"$columnicon\" />";

            }
            $$column = "<a href=\"view.php?id=$id&amp;sort=$column&amp;dir=$columndir\">".$string[$column]."</a>$columnicon";
        }

        if ($workshop->wtype) {
            $table->head = array ("$firstname / $lastname", get_string("assmnts", "workshop"), $agrade,
                $title, $date, $tassmnt, get_string('passmnts', 'workshop'), $sgrade, $ograde);
        } else {
            $table->head = array ("$firstname / $lastname", $title, $date, $tassmnt, $ograde);
        }

        echo '<tr><td>';
        print_table($table);
        echo '</td></tr>';
        echo '<tr><td>';
        workshop_print_key($workshop);
        echo '</td></tr>';

        // grading grade analysis
        unset($table);
        $table->head = array (get_string("count", "workshop"), get_string("mean", "workshop"),
            get_string("standarddeviation", "workshop"), get_string("maximum", "workshop"),
            get_string("minimum", "workshop"));
        $table->align = array ("center", "center", "center", "center", "center");
        $table->size = array ("*", "*", "*", "*", "*");
        $table->cellpadding = 2;
        $table->cellspacing = 0;
        if ($currentgroup) {
            $stats = get_record_sql("SELECT COUNT(*) as count, AVG(gradinggrade) AS mean,
                    STDDEV(gradinggrade) AS stddev, MIN(gradinggrade) AS min, MAX(gradinggrade) AS max
                    FROM {$CFG->prefix}groups_members g, {$CFG->prefix}workshop_assessments a
                    WHERE g.groupid = $currentgroup AND a.userid = g.userid AND a.timegraded > 0
                    AND a.workshopid = $workshop->id");
        } elseif (!empty($cm->groupingid) && !empty($CFG->enablegroupings)) {
            $stats = get_record_sql("SELECT COUNT(*) as count, AVG(gradinggrade) AS mean,
                    STDDEV(gradinggrade) AS stddev, MIN(gradinggrade) AS min, MAX(gradinggrade) AS max
                    FROM {$CFG->prefix}workshop_assessments a
                    INNER JOIN {$CFG->prefix}groups_members g ON a.userid = g.userid
                    INNER JOIN {$CFG->prefix}groupings_groups gg ON g.groupid = gg.groupid
                    WHERE gg.groupingid = {$cm->groupingid} AND a.timegraded > 0
                    AND a.workshopid = $workshop->id");
        } else { // no group/all participants
            $stats = get_record_sql("SELECT COUNT(*) as count, AVG(gradinggrade) AS mean,
                    STDDEV(gradinggrade) AS stddev, MIN(gradinggrade) AS min, MAX(gradinggrade) AS max
                    FROM {$CFG->prefix}workshop_assessments a
                    WHERE a.timegraded > 0 AND a.workshopid = $workshop->id");
        }
        $table->data[] = array($stats->count, number_format($stats->mean * $workshop->gradinggrade / 100, 1),
                number_format($stats->stddev * $workshop->gradinggrade /100, 1),
                number_format($stats->max * $workshop->gradinggrade / 100, 1),
                number_format($stats->min* $workshop->gradinggrade / 100, 1));
        echo '<tr><td>';
        print_heading(get_string("gradinggrade", "workshop")." ".get_string("analysis", "workshop"));
        print_table($table);
        echo '</td></tr>';

        if ($workshop->showleaguetable and time() > $workshop->assessmentend) {
            workshop_print_league_table($workshop);
            if ($workshop->anonymous) {
                echo "<p>".get_string("namesnotshowntostudents", "workshop", $course->students)."</p>\n";
            }
        }
        echo '</table>';
    }


    /****************** show description  ************/
    elseif ($action == 'showdescription') {
        workshop_print_assignment_info($workshop);
        print_box(format_text($workshop->description, $workshop->format), 'generalbox', 'intro');
        if (isset($_SERVER["HTTP_REFERER"])) {
            print_continue(htmlentities($_SERVER["HTTP_REFERER"]));
        } else {
            print_continue("$CFG->wwwroot/course/view.php?id=$cm->id");
        }
    }


    /****************** teacher's view - list all submissions  ************/
    elseif ($action == 'allsubmissions') {
        require_capability('mod/workshop:manage', $context);

        if ($submissions = get_records('workshop_submissions', 'workshopid', $workshop->id)) {
            foreach($submissions as $submission) {
                $user = get_record('user', 'id', $submission->userid);
                print_heading('"'.$submission->title.'" '.get_string('by', 'workshop').' '.fullname($user));
                workshop_print_submission($workshop, $submission);
            }
        }
    }


    /*************** no man's land **************************************/
    else {
        error("Fatal Error: Unknown Action: ".$action."\n");
    }


    print_footer($course);

?>
