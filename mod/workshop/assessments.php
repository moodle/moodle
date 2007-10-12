<?php  // $Id$

/*************************************************
    ACTIONS handled are:

    addcomment
    addstockcomment
    confirmdelete
    delete
    adminlist
    agreeassessment
    displaygradingform
    editcomment
    editelements (teachers only)
    gradeallassessments (teachers only)
    gradeassessment (teachers only)
    insertcomment
    insertelements (for teachers)
    listungradedstudentsubmissions (for teachers)
    listungradedteachersubmissions (for teachers)
    listteachersubmissions
    regradestudentassessments (for teachers)
    updateassessment
    updatecomment
    updategrading

************************************************/

    require("../../config.php");
    require("lib.php");
    require("locallib.php");

    $action         = required_param('action', PARAM_ALPHA);
    $id             = optional_param('id', 0, PARAM_INT);    // Course Module ID
    $wid            = optional_param('wid', 0, PARAM_INT);    // Workshop ID
    $aid            = optional_param('aid', 0, PARAM_INT); 
    $userid         = optional_param('userid', 0, PARAM_INT);
    $cid            = optional_param('cid', 0, PARAM_INT ); // comment id
    $sid            = optional_param('sid', 0, PARAM_INT); // submission id
    $elementno      = optional_param('elementno', -1, PARAM_INT);
    $stockcommentid = optional_param('stockcommentid', 0, PARAM_INT);

    // get some useful stuff...
    if ($id) {
        if (! $cm = get_coursemodule_from_id('workshop', $id)) {
            error("Course Module ID was incorrect");
        }
        if (! $workshop = get_record("workshop", "id", $cm->instance)) {
            error("Course module is incorrect");
        }
    } else if ($wid) {
        if (! $workshop = get_record("workshop", "id", $wid)) {
            error("Workshop id is incorrect");
        }
        if (! $cm = get_coursemodule_from_instance("workshop", $workshop->id, $workshop->course)) {
            error("No coursemodule found");
        }
    } else {
        error("No id given");
    }
    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $strworkshops = get_string("modulenameplural", "workshop");
    $strworkshop  = get_string("modulename", "workshop");
    $strassessments = get_string("assessments", "workshop");

    // ... print the header and...
    $navigation = build_navigation($strassessments, $cm);
    print_header_simple(format_string($workshop->name), "", $navigation,
                  "", "", true);

    /*************** add comment to assessment (by author, assessor or teacher) ***************************/
    if ($action == 'addcomment') {

        print_heading_with_help(get_string("addacomment", "workshop"), "addingacomment", "workshop");
        // get assessment record
        if (!$assessmentid = $aid) { // comes from link or hidden form variable
            error("Assessment id not given");
        }
        $assessment = get_record("workshop_assessments", "id", $assessmentid);
        if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
            error("Submission not found");
        }
        ?>
        <form id="commentform" action="assessments.php" method="post">
        <input type="hidden" name="action" value="insertcomment" />
        <input type="hidden" name="id" value="<?php echo $cm->id ?>" />
        <input type="hidden" name="aid" value="<?php echo $aid ?>" />
        <div class="boxaligncenter">
        <table cellpadding="5" border="1">
        <?php

        // now get the comment
        echo "<tr valign=\"top\">\n";

        echo "  <td align=\"right\"><b>". get_string("comment", "workshop").":</b></td>\n";

        echo "  <td>\n";

        echo "      <textarea name=\"comments\" rows=\"5\" cols=\"75\">\n";
        echo "</textarea>\n";

        echo "  </td></tr></table>\n";
        echo "<input type=\"submit\" value=\"".get_string("savemycomment", "workshop")."\" />\n";
        echo "</div></form>\n";
        echo "<div style=\"text-align:center\"><b>".get_string("assessment", "workshop"). "</b></div>\n";
        workshop_print_assessment($workshop, $assessment);
    }


    /*************** add stock comment (by teacher ) ***************************/
    elseif ($action == 'addstockcomment') {

        if (empty($aid) or ($elementno<0)) {
            error("Workshop Assessment ID and/or Element Number missing");
        }

        require_capability('mod/workshop:manage', $context);

        if (!$assessment = get_record("workshop_assessments", "id", $aid)) {
            error("workshop assessment is misconfigured");
        }
        $form = data_submitted('nomatch'); //Nomatch because we can come from assess.php

        // store the comment in the stock comments table
        if ($elementno == 99) { // it's the general comment
            $form->feedback_99 = $form->generalcomment;
        }
        $comment->workshopid = $workshop->id;
        $comment->elementno = $elementno;
        $comment->comments = clean_param($form->{"feedback_$elementno"}, PARAM_CLEAN);
        if (!(trim($comment->comments))) {
            // no comment given - just redisplay assessment form
            workshop_print_assessment($workshop, $assessment, true, true, $form->returnto);
            print_footer($course);
            exit();
        }

        if (!$element->id = insert_record("workshop_stockcomments", $comment)) {
            error("Could not insert comment into comment bank");
        }

        // now upate the assessment (just the elements, the assessment itself is not updated)

        // first get the assignment elements for maxscores and weights...
        $elementsraw = get_records("workshop_elements", "workshopid", $workshop->id, "elementno ASC");
        if (count($elementsraw) < $workshop->nelements) {
            print_string("noteonassignmentelements", "workshop");
        }
        if ($elementsraw) {
            foreach ($elementsraw as $element) {
                $elements[] = $element;   // to renumber index 0,1,2...
            }
        } else {
            $elements = null;
        }

        $timenow = time();
        // don't fiddle about, delete all the old and add the new!
        delete_records("workshop_grades", "assessmentid",  $assessment->id);


        //determine what kind of grading we have
        switch ($workshop->gradingstrategy) {
            case 0: // no grading
                // Insert all the elements that contain something
                for ($i = 0; $i < $workshop->nelements; $i++) {
                    unset($element);
                    $element->workshopid = $workshop->id;
                    $element->assessmentid = $assessment->id;
                    $element->elementno = $i;
                    $element->feedback   = clean_param($form->{"feedback_$i"}, PARAM_CLEAN);
                    if (!$element->id = insert_record("workshop_grades", $element)) {
                        error("Could not insert workshop grade!");
                    }
                }
                $grade = 0; // set to satisfy save to db
                break;

            case 1: // accumulative grading
                // Insert all the elements that contain something
                foreach ($form->grade as $key => $thegrade) {
                    unset($element);
                    $element->workshopid = $workshop->id;
                    $element->assessmentid = $assessment->id;
                    $element->elementno = clean_param($key, PARAM_INT);
                    $element->feedback   = clean_param($form->{"feedback_$key"}, PARAM_CLEAN);
                    $element->grade = $thegrade;
                    if (!$element->id = insert_record("workshop_grades", $element)) {
                        error("Could not insert workshop grade!");
                        }
                    }
                // now work out the grade...
                $rawgrade=0;
                $totalweight=0;
                foreach ($form->grade as $key => $grade) {
                    $maxscore = $elements[$key]->maxscore;
                    $weight = $WORKSHOP_EWEIGHTS[$elements[$key]->weight];
                    if ($weight > 0) {
                        $totalweight += $weight;
                    }
                    $rawgrade += ($grade / $maxscore) * $weight;
                    // echo "\$key, \$maxscore, \$weight, \$totalweight, \$grade, \$rawgrade : $key, $maxscore, $weight, $totalweight, $grade, $rawgrade<br />";
                }
                $grade = 100.0 * ($rawgrade / $totalweight);
                break;

            case 2: // error banded graded
                // Insert all the elements that contain something
                $error = 0.0;
                for ($i =0; $i < $workshop->nelements; $i++) {
                    unset($element);
                    $element->workshopid = $workshop->id;
                    $element->assessmentid = $assessment->id;
                    $element->elementno = $i;
                    $element->feedback   = clean_param($form->{"feedback_$i"}, PARAM_CLEAN);
                    $element->grade = $form->grade[$i];
                    if (!$element->id = insert_record("workshop_grades", $element)) {
                        error("Could not insert workshop grade!");
                    }
                    if (empty($form->grade[$i])){
                        $error += $WORKSHOP_EWEIGHTS[$elements[$i]->weight];
                    }
                }
                // now save the adjustment
                unset($element);
                $i = $workshop->nelements;
                $element->workshopid = $workshop->id;
                $element->assessmentid = $assessment->id;
                $element->elementno = $i;
                $element->grade = $form->grade[$i];
                if (!$element->id = insert_record("workshop_grades", $element)) {
                    error("Could not insert workshop grade!");
                }
                $grade = ($elements[intval($error + 0.5)]->maxscore + $form->grade[$i]) * 100 / $workshop->grade;
                // do sanity check
                if ($grade < 0) {
                    $grade = 0;
                } elseif ($grade > 100) {
                    $grade = 100;
                }
                echo "<b>".get_string("weightederrorcount", "workshop", intval($error + 0.5))."</b>\n";
                break;

            case 3: // criteria grading
                // save in the selected criteria value in element zero,
                unset($element);
                $element->workshopid = $workshop->id;
                $element->assessmentid = $assessment->id;
                $element->elementno = 0;
                $element->grade = $form->grade[0];
                if (!$element->id = insert_record("workshop_grades", $element)) {
                    error("Could not insert workshop grade!");
                }
                // now save the adjustment in element one
                unset($element);
                $element->workshopid = $workshop->id;
                $element->assessmentid = $assessment->id;
                $element->elementno = 1;
                $element->grade = $form->grade[1];
                if (!$element->id = insert_record("workshop_grades", $element)) {
                    error("Could not insert workshop grade!");
                }
                $grade = ($elements[$form->grade[0]]->maxscore + $form->grade[1]);
                break;

            case 4: // rubric grading (identical to accumulative grading)
                // Insert all the elements that contain something
                foreach ($form->grade as $key => $thegrade) {
                    unset($element);
                    $element->workshopid = $workshop->id;
                    $element->assessmentid = $assessment->id;
                    $element->elementno = clean_param($key, PARAM_INT);
                    $element->feedback   = clean_param($form->{"feedback_$key"}, PARAM_CLEAN);
                    $element->grade = $thegrade;
                    if (!$element->id = insert_record("workshop_grades", $element)) {
                        error("Could not insert workshop grade!");
                    }
                }
                // now work out the grade...
                $rawgrade=0;
                $totalweight=0;
                foreach ($form->grade as $key => $grade) {
                    $maxscore = $elements[$key]->maxscore;
                    $weight = $WORKSHOP_EWEIGHTS[$elements[$key]->weight];
                    if ($weight > 0) {
                        $totalweight += $weight;
                    }
                    $rawgrade += ($grade / $maxscore) * $weight;
                }
                $grade = 100.0 * ($rawgrade / $totalweight);
                break;

        } // end of switch


        // any comment?
        if (!empty($form->generalcomment)) { // update the object (no need to update the db record)
            $assessment->generalcomment = clean_param($form->generalcomment, PARAM_CLEAN);
        }

        // redisplay form, going back to original returnto address
        workshop_print_assessment($workshop, $assessment, true, true, $form->returnto);

        // add_to_log($course->id, "workshop", "assess", "viewassessment.php?id=$cm->id&amp;aid=$assessment->id", "$assessment->id", "$cm->id");

    }


    /******************* confirm delete ************************************/
    elseif ($action == 'confirmdelete' ) {

        if (empty($aid)) {
            error("Confirm delete: assessment id missing");
            }

        notice_yesno(get_string("confirmdeletionofthisitem","workshop", get_string("assessment", "workshop")),
             "assessments.php?action=delete&amp;id=$cm->id&amp;aid=$aid", "submissions.php?action=adminlist&amp;id=$cm->id");
        }


    /******************* delete ************************************/
    elseif ($action == 'delete' ) {

        if (empty($aid)) {
            error("Delete: submission id missing");
            }

        print_string("deleting", "workshop");
        // first delete all the associated records...
        delete_records("workshop_comments", "assessmentid", $aid);
        delete_records("workshop_grades", "assessmentid", $aid);
        // ...now delete the assessment...
        delete_records("workshop_assessments", "id", $aid);

        print_continue("view.php?id=$cm->id");
        }


    /*********************** admin list of asssessments (of a submission) (by teachers)**************/
    elseif ($action == 'adminlist') {

        require_capability('mod/workshop:manage', $context);

        if (empty($sid)) {
            error ("Workshop asssessments: adminlist called with no sid");
            }
        $submission = get_record("workshop_submissions", "id", $sid);
        workshop_print_assessments_for_admin($workshop, $submission);
        print_continue("submissions.php?action=adminlist&amp;id=$cm->id");
        }


    /*********************** admin list of asssessments by a student (used by teachers only )******************/
    elseif ($action == 'adminlistbystudent') {

        require_capability('mod/workshop:manage', $context);

        if (empty($userid)) {
            error ("Workshop asssessments: adminlistbystudent called with no userid");
            }
        $user = get_record("user", "id", $userid);
        workshop_print_assessments_by_user_for_admin($workshop, $user);
        print_continue("submissions.php?action=adminlist&amp;id=$cm->id");
        }


    /*************** agree (to) assessment (by student) ***************************/
    elseif ($action == 'agreeassessment') {
        $timenow = time();
        // assessment id comes from link or hidden form variable
        if (!$assessment = get_record("workshop_assessments", "id", $aid)) {
            error("Assessment : agree assessment failed");
            }
        //save time of agreement
        set_field("workshop_assessments", "timeagreed", $timenow, "id", $assessment->id);
        echo "<div style=\"text-align:center\"><b>".get_string("savedok", "workshop")."</b></div><br />\n";

        add_to_log($course->id, "workshop", "agree", "viewassessment.php?id=$cm->id&amp;aid=$assessment->id", "$assessment->id");
        print_continue("view.php?id=$cm->id");
        }



    /*************** display grading form (viewed by student) *********************************/
    elseif ($action == 'displaygradingform') {

        print_heading_with_help(get_string("specimenassessmentform", "workshop"), "specimen", "workshop");

        workshop_print_assessment($workshop); // called with no assessment
        print_continue("view.php?id=$cm->id");
    }


    /*************** edit comment on assessment (by author, assessor or teacher) ***************************/
    elseif ($action == 'editcomment') {

        print_heading_with_help(get_string("editacomment", "workshop"), "editingacomment", "workshop");
        // get the comment record...
        if (!$comment = get_record("workshop_comments", "id", $cid)) {
            error("Edit Comment: Comment not found");
            }
        if (!$assessment = get_record("workshop_assessments", "id", $comment->assessmentid)) {
            error("Edit Comment: Assessment not found");
            }
        if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
            error("Edit Comment: Submission not found");
            }
        ?>
        <form id="gradingform" action="assessments.php" method="post">
        <input type="hidden" name="action" value="updatecomment" />
        <input type="hidden" name="id" value="<?php echo $cm->id ?>" />
        <input type="hidden" name="cid" value="<?php echo $cid ?>" />
        <div class="boxaligncenter">
        <table cellpadding="5" border="1">
        <?php

        // now show the comment
        echo "<tr valign=\"top\">\n";
        echo "  <td align=\"right\"><b>". get_string("comment", "workshop").":</b></td>\n";
        echo "  <td>\n";
        echo "      <textarea name=\"comments\" rows=\"5\" cols=\"75\">\n";
        if (isset($comment->comments)) {
            echo $comment->comments;
            }
        echo "      </textarea>\n";
        echo "  </td></tr></table>\n";
        echo "<input type=\"submit\" value=\"".get_string("savemycomment", "workshop")."\" />\n";
        echo "</div></form>\n";
        workshop_print_assessment($workshop, $assessment);
        }


    /*********************** edit assessment elements (for teachers) ***********************/
    elseif ($action == 'editelements') {

        require_capability('mod/workshop:manage', $context);

        $count = count_records("workshop_grades", "workshopid", $workshop->id);
        if ($count) {
            notify(get_string("warningonamendingelements", "workshop"));
        }
        // set up heading, form and table
        print_heading_with_help(get_string("editingassessmentelements", "workshop"), "elements", "workshop");
        ?>
        <form id="form" method="post" action="assessments.php">
        <input type="hidden" name="id" value="<?php echo $cm->id ?>" />
        <input type="hidden" name="action" value="insertelements" />
        <div class="boxaligncenter"><table cellpadding="5" border="1">
        <?php

        // get existing elements, if none set up appropriate default ones
        if ($elementsraw = get_records("workshop_elements", "workshopid", $workshop->id, "elementno ASC" )) {
            foreach ($elementsraw as $element) {
                $elements[] = $element;   // to renumber index 0,1,2...
            }
        }
        // check for missing elements (this happens either the first time round or when the number of elements is icreased)
        for ($i=0; $i<$workshop->nelements; $i++) {
            if (!isset($elements[$i])) {
                $elements[$i]->description = '';
                $elements[$i]->scale =0;
                $elements[$i]->maxscore = 0;
                $elements[$i]->weight = 11;
            }
        }

        switch ($workshop->gradingstrategy) {
            case 0: // no grading
                for ($i=0; $i<$workshop->nelements; $i++) {
                    $iplus1 = $i+1;
                    echo "<tr valign=\"top\">\n";
                    echo "  <td align=\"right\"><b>". get_string("element","workshop")." $iplus1:</b></td>\n";
                    echo "<td><textarea name=\"description[]\" rows=\"3\" cols=\"75\">".$elements[$i]->description."</textarea>\n";
                    echo "  </td></tr>\n";
                    echo "<tr valign=\"top\">\n";
                    echo "  <td colspan=\"2\" class=\"workshopassessmentheading\">&nbsp;</td>\n";
                    echo "</tr>\n";
                }
                break;

            case 1: // accumulative grading
                // set up scales name
                foreach ($WORKSHOP_SCALES as $KEY => $SCALE) {
                    $SCALES[] = $SCALE['name'];
                }
                for ($i=0; $i<$workshop->nelements; $i++) {
                    $iplus1 = $i+1;
                    echo "<tr valign=\"top\">\n";
                    echo "  <td align=\"right\"><b>". get_string("element","workshop")." $iplus1:</b></td>\n";
                    echo "<td><textarea name=\"description[]\" rows=\"3\" cols=\"75\">".$elements[$i]->description."</textarea>\n";
                    echo "  </td></tr>\n";
                    echo "<tr valign=\"top\">\n";
                    echo "  <td align=\"right\"><b>". get_string("typeofscale", "workshop"). ":</b></td>\n";
                    echo "<td valign=\"top\">\n";
                    choose_from_menu($SCALES, "scale[]", $elements[$i]->scale, "");
                    if ($elements[$i]->weight == '') { // not set
                        $elements[$i]->weight = 11; // unity
                    }
                    echo "</td></tr>\n";
                    echo "<tr valign=\"top\"><td align=\"right\"><b>".get_string("elementweight", "workshop").":</b></td><td>\n";
                    workshop_choose_from_menu($WORKSHOP_EWEIGHTS, "weight[]", $elements[$i]->weight, "");
                    echo "      </td>\n";
                    echo "</tr>\n";
                    echo "<tr valign=\"top\">\n";
                    echo "  <td colspan=\"2\" class=\"workshopassessmentheading\">&nbsp;</td>\n";
                    echo "</tr>\n";
                }
                break;

            case 2: // error banded grading
                for ($i=0; $i<$workshop->nelements; $i++) {
                    $iplus1 = $i+1;
                    echo "<tr valign=\"top\">\n";
                    echo "  <td align=\"right\"><b>". get_string("element","workshop")." $iplus1:</b></td>\n";
                    echo "<td><textarea name=\"description[$i]\" rows=\"3\" cols=\"75\">".$elements[$i]->description."</textarea>\n";
                    echo "  </td></tr>\n";
                    if ($elements[$i]->weight == '') { // not set
                        $elements[$i]->weight = 11; // unity
                        }
                    echo "</tr>\n";
                    echo "<tr valign=\"top\"><td align=\"right\"><b>".get_string("elementweight", "workshop").":</b></td><td>\n";
                    workshop_choose_from_menu($WORKSHOP_EWEIGHTS, "weight[]", $elements[$i]->weight, "");
                    echo "      </td>\n";
                    echo "</tr>\n";
                    echo "<tr valign=\"top\">\n";
                    echo "  <td colspan=\"2\" class=\"workshopassessmentheading\">&nbsp;</td>\n";
                    echo "</tr>\n";
                }
                echo "</div></table><br />\n";
                echo "<div style=\"text-align:center\"><b>".get_string("gradetable","workshop")."</b></div>\n";
                echo "<div class=\"boxaligncenter\"><table cellpadding=\"5\" border=\"1\"><tr><td align=\"CENTER\">".
                    get_string("numberofnegativeresponses", "workshop");
                echo "</td><td>". get_string("suggestedgrade", "workshop")."</td></tr>\n";
                for ($j = $workshop->grade; $j >= 0; $j--) {
                    $numbers[$j] = $j;
                }
                for ($i=0; $i<=$workshop->nelements; $i++) {
                    echo "<tr><td align=\"CENTER\">$i</td><td align=\"CENTER\">";
                    if (!isset($elements[$i])) {  // the "last one" will be!
                        $elements[$i]->description = "";
                        $elements[$i]->maxscore = 0;
                    }
                    choose_from_menu($numbers, "maxscore[$i]", $elements[$i]->maxscore, "");
                    echo "</td></tr>\n";
                }
                echo "</table></div>\n";
                break;

            case 3: // criterion grading
                for ($j = 100; $j >= 0; $j--) {
                    $numbers[$j] = $j;
                }
                for ($i=0; $i<$workshop->nelements; $i++) {
                    $iplus1 = $i+1;
                    echo "<tr valign=\"top\">\n";
                    echo "  <td align=\"right\"><b>". get_string("criterion","workshop")." $iplus1:</b></td>\n";
                    echo "<td><textarea name=\"description[$i]\" rows=\"3\" cols=\"75\">".$elements[$i]->description."</textarea>\n";
                    echo "  </td></tr>\n";
                    echo "<tr><td><b>". get_string("suggestedgrade", "workshop").":</b></td><td>\n";
                    choose_from_menu($numbers, "maxscore[$i]", $elements[$i]->maxscore, "");
                    echo "</td></tr>\n";
                    echo "<tr valign=\"top\">\n";
                    echo "  <td colspan=\"2\" class=\"workshopassessmentheading\">&nbsp;</td>\n";
                    echo "</tr>\n";
                }
                break;

            case 4: // rubric
                for ($j = 100; $j >= 0; $j--) {
                    $numbers[$j] = $j;
                }
                if ($rubricsraw = get_records("workshop_rubrics", "workshopid", $workshop->id)) {
                    foreach ($rubricsraw as $rubric) {
                        $rubrics[$rubric->elementno][$rubric->rubricno] = $rubric->description;   // reindex 0,1,2...
                    }
                }
                for ($i=0; $i<$workshop->nelements; $i++) {
                    $iplus1 = $i+1;
                    echo "<tr valign=\"top\">\n";
                    echo "  <td align=\"right\"><b>". get_string("element","workshop")." $iplus1:</b></td>\n";
                    echo "<td><textarea name=\"description[$i]\" rows=\"3\" cols=\"75\">".$elements[$i]->description."</textarea>\n";
                    echo "  </td></tr>\n";
                    echo "<tr valign=\"top\"><td align=\"right\"><b>".get_string("elementweight", "workshop").":</b></td><td>\n";
                    workshop_choose_from_menu($WORKSHOP_EWEIGHTS, "weight[]", $elements[$i]->weight, "");
                    echo "      </td>\n";
                    echo "</tr>\n";

                    for ($j=0; $j<5; $j++) {
                        $jplus1 = $j+1;
                        if (empty($rubrics[$i][$j])) {
                            $rubrics[$i][$j] = "";
                        }
                        echo "<tr valign=\"top\">\n";
                        echo "  <td align=\"right\"><b>". get_string("grade","workshop")." $j:</b></td>\n";
                        echo "<td><textarea name=\"rubric[$i][$j]\" rows=\"3\" cols=\"75\">".$rubrics[$i][$j]."</textarea>\n";
                        echo "  </td></tr>\n";
                        }
                    echo "<tr valign=\"top\">\n";
                    echo "  <td colspan=\"2\" class=\"workshopassessmentheading\">&nbsp;</td>\n";
                    echo "</tr>\n";
                    }
                break;
            }
        // close table and form

        ?>
        </table><br />
        <input type="submit" value="<?php  print_string("savechanges") ?>" />
        <input type="submit" name="cancel" value="<?php  print_string("cancel") ?>" />
        </div>
        </form>
        <?php
    }


    /*************** grade all assessments (by teacher) ***************************/
    elseif ($action == 'gradeallassessments') {

        require_capability('mod/workshop:manage', $context);

        print_heading(get_string("gradingallassessments", "workshop"));
        workshop_grade_assessments($workshop);
        print_continue("view.php?id=$cm->id");
    }


    /*************** grade (student's) assessment (by teacher) ***************************/
    elseif ($action == 'gradeassessment') {

        require_capability('mod/workshop:manage', $context);

        print_heading_with_help(get_string("gradeassessment", "workshop"), "gradingassessments", "workshop");
        // get assessment record
        if (!$assessmentid = $aid) {
            error("Assessment id not given");
        }
        $assessment = get_record("workshop_assessments", "id", $assessmentid);
        if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
            error("Submission not found");
        }
        // get the teacher's assessment first
        if ($teachersassessment = workshop_get_submission_assessment($submission, $USER)) {
            echo "<div style=\"text-align:center\"><b>".get_string("teacherassessments", "workshop", $course->teacher)."</b></div>\n";
            workshop_print_assessment($workshop, $teachersassessment);
        }
        // now the student's assessment (don't allow changes)
        $user = get_record("user", "id", $assessment->userid);
        echo "<div style=\"text-align:center\"><b>".get_string("assessmentby", "workshop", $user->firstname." ".$user->lastname)."</b></div>\n";
        workshop_print_assessment($workshop, $assessment);

        include('assessment_grading_form.html');
        die;
    }


    /*************** insert (new) comment (by author, assessor or teacher) ***************************/
    elseif ($action == 'insertcomment') {
        $timenow = time();

        $form = (object)$_POST;

        if (!$assessment = get_record("workshop_assessments", "id", $aid)) {
            error("Unable to insert comment");
            }
        // save the comment...
        $comment->workshopid = $workshop->id;
        $comment->assessmentid   = $assessment->id;
        $comment->userid   = $USER->id;
        $comment->timecreated   = $timenow;
        $comment->comments   = clean_param($form->comments, PARAM_CLEAN);
        if (!$comment->id = insert_record("workshop_comments", $comment)) {
            error("Could not insert workshop comment!");
            }

        add_to_log($course->id, "workshop", "comment", "view.php?id=$cm->id", "$comment->id");

        print_continue("viewassessment.php?id=$cm->id&amp;aid=$assessment->id");
        }


    /*********************** insert/update assignment elements (for teachers)***********************/
    elseif ($action == 'insertelements') {

        require_capability('mod/workshop:manage', $context);

        $form = data_submitted();

        // let's not fool around here, dump the junk!
        delete_records("workshop_elements", "workshopid", $workshop->id);

        // determine wich type of grading
        switch ($workshop->gradingstrategy) {
            case 0: // no grading
                // Insert all the elements that contain something
                foreach ($form->description as $key => $description) {
                    if ($description) {
                        unset($element);
                        $element->description   = $description;
                        $element->workshopid = $workshop->id;
                        $element->elementno = $key;
                        if (!$element->id = insert_record("workshop_elements", $element)) {
                            error("Could not insert workshop element!");
                        }
                    }
                }
                break;

            case 1: // accumulative grading
                // Insert all the elements that contain something
                foreach ($form->description as $key => $description) {
                    if ($description) {
                        unset($element);
                        $element->description   = $description;
                        $element->workshopid = $workshop->id;
                        $element->elementno = clean_param($key, PARAM_INT);
                        if (isset($form->scale[$key])) {
                            $element->scale = $form->scale[$key];
                            switch ($WORKSHOP_SCALES[$form->scale[$key]]['type']) {
                                case 'radio' :  $element->maxscore = $WORKSHOP_SCALES[$form->scale[$key]]['size'] - 1;
                                                        break;
                                case 'selection' :  $element->maxscore = $WORKSHOP_SCALES[$form->scale[$key]]['size'];
                                                        break;
                            }
                        }
                        if (isset($form->weight[$key])) {
                            $element->weight = $form->weight[$key];
                        }
                        if (!$element->id = insert_record("workshop_elements", $element)) {
                            error("Could not insert workshop element!");
                        }
                    }
                }
                break;

            case 2: // error banded grading...
            case 3: // ...and criterion grading
                // Insert all the elements that contain something, the number of descriptions is one less than the number of grades
                foreach ($form->maxscore as $key => $themaxscore) {
                    unset($element);
                    $element->workshopid = $workshop->id;
                    $element->elementno = clean_param($key, PARAM_INT);
                    $element->maxscore = $themaxscore;
                    if (isset($form->description[$key])) {
                        $element->description   = $form->description[$key];
                    }
                    if (isset($form->weight[$key])) {
                        $element->weight = $form->weight[$key];
                    }
                    if (!$element->id = insert_record("workshop_elements", $element)) {
                        error("Could not insert workshop element!");
                    }
                }
                break;

            case 4: // ...and criteria grading
                // Insert all the elements that contain something
                foreach ($form->description as $key => $description) {
                    unset($element);
                    $element->workshopid = $workshop->id;
                    $element->elementno = clean_param($key, PARAM_INT);
                    $element->description   = $description;
                    $element->weight = $form->weight[$key];
                    for ($j=0;$j<5;$j++) {
                        if (empty($form->rubric[$key][$j]))
                            break;
                    }
                    $element->maxscore = $j - 1;
                    if (!$element->id = insert_record("workshop_elements", $element)) {
                        error("Could not insert workshop element!");
                    }
                }
                // let's not fool around here, dump the junk!
                delete_records("workshop_rubrics", "workshopid", $workshop->id);
                for ($i=0;$i<$workshop->nelements;$i++) {
                    for ($j=0;$j<5;$j++) {
                        unset($element);
                        if (empty($form->rubric[$i][$j])) {  // OK to have an element with fewer than 5 items
                             break;
                         }
                        $element->workshopid = $workshop->id;
                        $element->elementno = $i;
                        $element->rubricno = $j;
                        $element->description   = $form->rubric[$i][$j];
                        if (!$element->id = insert_record("workshop_rubrics", $element)) {
                            error("Could not insert workshop element!");
                        }
                    }
                }
                break;
        } // end of switch
        echo "</div>"; // not sure where this one came from MDL-7861
        redirect("view.php?id=$cm->id", get_string("savedok","workshop"));
    }


    /*********************** list assessments for grading (Student submissions)(by teachers)***********************/
    elseif ($action == 'listungradedstudentsubmissions') {

        require_capability('mod/workshop:manage', $context);

        workshop_list_ungraded_assessments($workshop, "student");
        print_continue("view.php?id=$cm->id");
        }


    /*********************** list assessments for grading (Teacher submissions) (by teachers)***********************/
    elseif ($action == 'listungradedteachersubmissions') {

        require_capability('mod/workshop:manage', $context);

        workshop_list_ungraded_assessments($workshop, "teacher");
        print_continue("view.php?id=$cm->id");
        }


    /****************** list teacher submissions ***********************/
    elseif ($action == 'listteachersubmissions') {

        workshop_list_teacher_submissions($workshop, $USER);
        print_continue("view.php?id=$cm->id");
    }


    /******************* regrade student assessments ************************************/
    elseif ($action == 'regradestudentassessments' ) {

        $timenow = time();
        require_capability('mod/workshop:manage', $context);
        // get all the submissions...
        if ($submissions = get_records("workshop_submissions", "workshopid", $workshop->id)) {
            foreach ($submissions as $submission) {
                // ...if cold...
                if (($submission->timecreated + $CFG->maxeditingtime) < $timenow) {
                    // ...clear assessment count so workshop_grade_assessments() can do its thing
                    set_field("workshop_submissions", "nassessments", 0, "id", $submission->id);
                }
            }
        }
        echo "<pre>";
        workshop_grade_assessments($workshop);
        echo '</pre>';
        print_continue("submissions.php?id=$cm->id&action=adminlist");
    }


    /*************** remove stock comment (by teacher ) ***************************/
    elseif ($action == 'removestockcomment') {

        if (empty($aid) or empty($stockcommentid)) {
            error("Workshop Assessment id and/or Stock Comment id missing");
        }

        require_capability('mod/workshop:manage', $context);

        if (!$assessment = get_record("workshop_assessments", "id", $aid)) {
            error("workshop assessment is misconfigured");
        }
        $form = data_submitted('nomatch'); //Nomatch because we can come from assess.php

        // delete the comment from the stock comments table
        if (!delete_records("workshop_stockcomments", "id", $stockcommentid)) {
            error("Could not remove comment from the comment bank");
        }

        // now upate the assessment (just the elements, the assessment itself is not updated)

        // first get the assignment elements for maxscores and weights...
        $elementsraw = get_records("workshop_elements", "workshopid", $workshop->id, "elementno ASC");
        if (count($elementsraw) < $workshop->nelements) {
            print_string("noteonassignmentelements", "workshop");
        }
        if ($elementsraw) {
            foreach ($elementsraw as $element) {
                $elements[] = $element;   // to renumber index 0,1,2...
            }
        } else {
            $elements = null;
        }

        $timenow = time();
        // don't fiddle about, delete all the old and add the new!
        delete_records("workshop_grades", "assessmentid",  $assessment->id);


        //determine what kind of grading we have
        switch ($workshop->gradingstrategy) {
            case 0: // no grading
                // Insert all the elements that contain something
                for ($i =0; $i < $workshop->nelements; $i++) {
                    unset($element);
                    $element->workshopid = $workshop->id;
                    $element->assessmentid = $assessment->id;
                    $element->elementno = $i;
                    $element->feedback   = clean_param($form->{"feedback_$i"}, PARAM_CLEAN);
                    if (!$element->id = insert_record("workshop_grades", $element)) {
                        error("Could not insert workshop grade!");
                    }
                }
                $grade = 0; // set to satisfy save to db
                break;

            case 1: // accumulative grading
                // Insert all the elements that contain something
                foreach ($form->grade as $key => $thegrade) {
                    unset($element);
                    $element->workshopid = $workshop->id;
                    $element->assessmentid = $assessment->id;
                    $element->elementno = clean_param($key, PARAM_INT);
                    $element->feedback   = clean_param($form->{"feedback_$key"}, PARAM_CLEAN);
                    $element->grade = $thegrade;
                    if (!$element->id = insert_record("workshop_grades", $element)) {
                        error("Could not insert workshop grade!");
                        }
                    }
                // now work out the grade...
                $rawgrade=0;
                $totalweight=0;
                foreach ($form->grade as $key => $grade) {
                    $maxscore = $elements[$key]->maxscore;
                    $weight = $WORKSHOP_EWEIGHTS[$elements[$key]->weight];
                    if ($weight > 0) {
                        $totalweight += $weight;
                    }
                    $rawgrade += ($grade / $maxscore) * $weight;
                    // echo "\$key, \$maxscore, \$weight, \$totalweight, \$grade, \$rawgrade : $key, $maxscore, $weight, $totalweight, $grade, $rawgrade<br />";
                }
                $grade = 100.0 * ($rawgrade / $totalweight);
                break;

            case 2: // error banded graded
                // Insert all the elements that contain something
                $error = 0.0;
                for ($i =0; $i < $workshop->nelements; $i++) {
                    unset($element);
                    $element->workshopid = $workshop->id;
                    $element->assessmentid = $assessment->id;
                    $element->elementno = $i;
                    $element->feedback   = clean_param($form->{"feedback_$i"}, PARAM_CLEAN);
                    $element->grade = $form->grade[$i];
                    if (!$element->id = insert_record("workshop_grades", $element)) {
                        error("Could not insert workshop grade!");
                    }
                    if (empty($form->grade[$i])){
                        $error += $WORKSHOP_EWEIGHTS[$elements[$i]->weight];
                    }
                }
                // now save the adjustment
                unset($element);
                $i = $workshop->nelements;
                $element->workshopid = $workshop->id;
                $element->assessmentid = $assessment->id;
                $element->elementno = $i;
                $element->grade = $form->grade[$i];
                if (!$element->id = insert_record("workshop_grades", $element)) {
                    error("Could not insert workshop grade!");
                }
                $grade = ($elements[intval($error + 0.5)]->maxscore + $form->grade[$i]) * 100 / $workshop->grade;
                // do sanity check
                if ($grade < 0) {
                    $grade = 0;
                } elseif ($grade > 100) {
                    $grade = 100;
                }
                echo "<b>".get_string("weightederrorcount", "workshop", intval($error + 0.5))."</b>\n";
                break;

            case 3: // criteria grading
                // save in the selected criteria value in element zero,
                unset($element);
                $element->workshopid = $workshop->id;
                $element->assessmentid = $assessment->id;
                $element->elementno = 0;
                $element->grade = $form->grade[0];
                if (!$element->id = insert_record("workshop_grades", $element)) {
                    error("Could not insert workshop grade!");
                }
                // now save the adjustment in element one
                unset($element);
                $element->workshopid = $workshop->id;
                $element->assessmentid = $assessment->id;
                $element->elementno = 1;
                $element->grade = $form->grade[1];
                if (!$element->id = insert_record("workshop_grades", $element)) {
                    error("Could not insert workshop grade!");
                }
                $grade = ($elements[$form->grade[0]]->maxscore + $form->grade[1]);
                break;

            case 4: // rubric grading (identical to accumulative grading)
                // Insert all the elements that contain something
                foreach ($form->grade as $key => $thegrade) {
                    unset($element);
                    $element->workshopid = $workshop->id;
                    $element->assessmentid = $assessment->id;
                    $element->elementno = clean_param($key, PARAM_INT);
                    $element->feedback   = clean_param($form->{"feedback_$key"}, PARAM_CLEAN);
                    $element->grade = $thegrade;
                    if (!$element->id = insert_record("workshop_grades", $element)) {
                        error("Could not insert workshop grade!");
                    }
                }
                // now work out the grade...
                $rawgrade=0;
                $totalweight=0;
                foreach ($form->grade as $key => $grade) {
                    $maxscore = $elements[$key]->maxscore;
                    $weight = $WORKSHOP_EWEIGHTS[$elements[$key]->weight];
                    if ($weight > 0) {
                        $totalweight += $weight;
                    }
                    $rawgrade += ($grade / $maxscore) * $weight;
                }
                $grade = 100.0 * ($rawgrade / $totalweight);
                break;

        } // end of switch


        // any comment?
        if (!empty($form->generalcomment)) { // update the object (no need to update the db record)
            $assessment->generalcomment = clean_param($form->generalcomment, PARAM_CLEAN);
        }

        // redisplay form, going back to original returnto address
        workshop_print_assessment($workshop, $assessment, true, true, $form->returnto);

        // add_to_log($course->id, "workshop", "assess", "viewassessment.php?id=$cm->id&amp;aid=$assessment->id", "$assessment->id", "$cm->id");

    }


    /*************** update assessment (by teacher or student) ***************************/
    elseif ($action == 'updateassessment') {

        if (empty($aid)) {
            error("Workshop Assessment id missing");
        }

        if (! $assessment = get_record("workshop_assessments", "id", $aid)) {
            error("workshop assessment is misconfigured");
        }

        // first get the assignment elements for maxscores and weights...
        $elementsraw = get_records("workshop_elements", "workshopid", $workshop->id, "elementno ASC");
        if (count($elementsraw) < $workshop->nelements) {
            print_string("noteonassignmentelements", "workshop");
        }
        if ($elementsraw) {
            foreach ($elementsraw as $element) {
                $elements[] = $element;   // to renumber index 0,1,2...
            }
        } else {
            $elements = null;
        }

        $timenow = time();
        // don't fiddle about, delete all the old and add the new!
        delete_records("workshop_grades", "assessmentid",  $assessment->id);

        $form = data_submitted('nomatch'); //Nomatch because we can come from assess.php

        //determine what kind of grading we have
        switch ($workshop->gradingstrategy) {
            case 0: // no grading
                // Insert all the elements that contain something
                for ($i = 0; $i < $workshop->nelements; $i++) {
                    unset($element);
                    $element->workshopid = $workshop->id;
                    $element->assessmentid = $assessment->id;
                    $element->elementno = $i;
                    $element->feedback = clean_param($form->{"feedback_$i"}, PARAM_CLEAN);
                    if (!$element->id = insert_record("workshop_grades", $element)) {
                        error("Could not insert workshop grade!");
                    }
                }
                $grade = 0; // set to satisfy save to db
                break;

            case 1: // accumulative grading
                // Insert all the elements that contain something
                foreach ($form->grade as $key => $thegrade) {
                    unset($element);
                    $element->workshopid = $workshop->id;
                    $element->assessmentid = $assessment->id;
                    $element->elementno = $key;
                    $element->feedback   = clean_param($form->{"feedback_$key"}, PARAM_CLEAN);
                    $element->grade = $thegrade;
                    if (!$element->id = insert_record("workshop_grades", $element)) {
                        error("Could not insert workshop grade!");
                        }
                    }
                // now work out the grade...
                $rawgrade=0;
                $totalweight=0;
                foreach ($form->grade as $key => $grade) {
                    $maxscore = $elements[$key]->maxscore;
                    $weight = $WORKSHOP_EWEIGHTS[$elements[$key]->weight];
                    if ($weight > 0) {
                        $totalweight += $weight;
                    }
                    $rawgrade += ($grade / $maxscore) * $weight;
                    // echo "\$key, \$maxscore, \$weight, \$totalweight, \$grade, \$rawgrade : $key, $maxscore, $weight, $totalweight, $grade, $rawgrade<br />";
                }
                $grade = 100.0 * ($rawgrade / $totalweight);
                break;

            case 2: // error banded graded
                // Insert all the elements that contain something
                $error = 0.0;
                for ($i =0; $i < $workshop->nelements; $i++) {
                    unset($element);
                    $element->workshopid = $workshop->id;
                    $element->assessmentid = $assessment->id;
                    $element->elementno = $i;
                    $element->feedback   = $form->{"feedback_$i"};
                    $element->grade = clean_param($form->grade[$i], PARAM_CLEAN);
                    if (!$element->id = insert_record("workshop_grades", $element)) {
                        error("Could not insert workshop grade!");
                    }
                    if (empty($form->grade[$i])){
                        $error += $WORKSHOP_EWEIGHTS[$elements[$i]->weight];
                    }
                }
                // now save the adjustment
                unset($element);
                $i = $workshop->nelements;
                $element->workshopid = $workshop->id;
                $element->assessmentid = $assessment->id;
                $element->elementno = $i;
                $element->grade = $form->grade[$i];
                if (!$element->id = insert_record("workshop_grades", $element)) {
                    error("Could not insert workshop grade!");
                }
                $grade = ($elements[intval($error + 0.5)]->maxscore + $form->grade[$i]) * 100 / $workshop->grade;
                // do sanity check
                if ($grade < 0) {
                    $grade = 0;
                } elseif ($grade > 100) {
                    $grade = 100;
                }
                echo "<b>".get_string("weightederrorcount", "workshop", intval($error + 0.5))."</b>\n";
                break;

            case 3: // criteria grading
                // save in the selected criteria value in element zero,
                unset($element);
                $element->workshopid = $workshop->id;
                $element->assessmentid = $assessment->id;
                $element->elementno = 0;
                $element->grade = $form->grade[0];
                if (!$element->id = insert_record("workshop_grades", $element)) {
                    error("Could not insert workshop grade!");
                }
                // now save the adjustment in element one
                unset($element);
                $element->workshopid = $workshop->id;
                $element->assessmentid = $assessment->id;
                $element->elementno = 1;
                $element->grade = $form->grade[1];
                if (!$element->id = insert_record("workshop_grades", $element)) {
                    error("Could not insert workshop grade!");
                }
                $grade = ($elements[$form->grade[0]]->maxscore + $form->grade[1]);
                break;

            case 4: // rubric grading (identical to accumulative grading)
                // Insert all the elements that contain something
                foreach ($form->grade as $key => $thegrade) {
                    unset($element);
                    $element->workshopid = $workshop->id;
                    $element->assessmentid = $assessment->id;
                    $element->elementno = clean_param($key, PARAM_INT);
                    $element->feedback = clean_param($form->{"feedback_$key"}, PARAM_CLEAN);
                    $element->grade = $thegrade;
                    if (!$element->id = insert_record("workshop_grades", $element)) {
                        error("Could not insert workshop grade!");
                    }
                }
                // now work out the grade...
                $rawgrade=0;
                $totalweight=0;
                foreach ($form->grade as $key => $grade) {
                    $maxscore = $elements[$key]->maxscore;
                    $weight = $WORKSHOP_EWEIGHTS[$elements[$key]->weight];
                    if ($weight > 0) {
                        $totalweight += $weight;
                    }
                    $rawgrade += ($grade / $maxscore) * $weight;
                }
                $grade = 100.0 * ($rawgrade / $totalweight);
                break;

        } // end of switch

        // update the time of the assessment record (may be re-edited)...
        set_field("workshop_assessments", "timecreated", $timenow, "id", $assessment->id);

        if (!$submission = get_record("workshop_submissions", "id", $assessment->submissionid)) {
            error ("Updateassessment: submission record not found");
        }

        // if the workshop does need peer agreement AND it's self assessment then set timeagreed
        if ($workshop->agreeassessments and ($submission->userid == $assessment->userid)) {
            set_field("workshop_assessments", "timeagreed", $timenow, "id", $assessment->id);
        }

        // set grade...
        set_field("workshop_assessments", "grade", $grade, "id", $assessment->id);
        // ...and clear the timegraded but set the graddinggrade to maximum, may be reduced subsequently...
        set_field("workshop_assessments", "timegraded", 0, "id", $assessment->id);
        set_field("workshop_assessments", "gradinggrade", 100, "id", $assessment->id);
        // ...and the resubmission flag
        set_field("workshop_assessments", "resubmission", 0, "id", $assessment->id);

        // if there's examples or peer assessments clear the counter in the submission so that
        // all assessments for this submission will be regraded
        if ($workshop->ntassessments or $workshop->nsassessments) {
            set_field("workshop_submissions", "nassessments", 0, "id", $submission->id);
            workshop_grade_assessments($workshop);
        } else { // it could be self assessment....
            // now see if there's a corresponding assessment so that the gradinggrade can be set
            if (workshop_is_teacher($workshop)) {
                // see if there's are student assessments, if so set their gradinggrade
                if ($assessments = workshop_get_assessments($submission)) {
                    foreach($assessments as $studentassessment) {
                        // skip if it's not a student assessment
                        if (!workshop_is_student($workshop, $studentassessment->userid)) {
                            continue;
                        }
                        $gradinggrade = workshop_compare_assessments($workshop, $assessment, $studentassessment);
                        set_field("workshop_assessments", "timegraded", $timenow, "id", $studentassessment->id);
                        set_field("workshop_assessments", "gradinggrade", $gradinggrade, "id", $studentassessment->id);
                    }
                }
            } else { //it's a student assessment, see if there's a corresponding teacher's assessment
                if ($assessments = workshop_get_assessments($submission)) {
                    foreach($assessments as $teacherassessment) {
                        if (workshop_is_teacher($workshop, $teacherassessment->userid)) {
                            $gradinggrade = workshop_compare_assessments($workshop, $assessment, $teacherassessment);
                            set_field("workshop_assessments", "timegraded", $timenow, "id", $assessment->id);
                            set_field("workshop_assessments", "gradinggrade", $gradinggrade, "id", $assessment->id);
                            break; // only look for the first teacher assessment
                        }
                    }
                }
            }
        }

        // any comment?
        if (!empty($form->generalcomment)) {
            set_field("workshop_assessments", "generalcomment", clean_param($form->generalcomment, PARAM_CLEAN), "id", $assessment->id);
        }

        add_to_log($course->id, "workshop", "assess",
                "viewassessment.php?id=$cm->id&amp;aid=$assessment->id", "$assessment->id", "$cm->id");

        // set up return address
        if (!$returnto = $form->returnto) {
            $returnto = "view.php?id=$cm->id";
        }

        // show grade if grading strategy is not zero
        if ($workshop->gradingstrategy) {
            echo "</div>"; // MDL-7861, this is from <div id=page>
            redirect($returnto, get_string("thegradeis", "workshop").": ".
                    number_format($grade * $workshop->grade / 100, 2).
                    " (".get_string("maximumgrade")." ".number_format($workshop->grade).")");
        }
        else {
            redirect($returnto);
        }
        
    }


    /****************** update comment (by author, assessor or teacher) ********************/
    elseif ($action == 'updatecomment') {
        $timenow = time();

        $form = (object)$_POST;

        // get the comment record...
        if (!$comment = get_record("workshop_comments", "id", $_POST['cid'])) {
            error("Update to Comment failed");
        }
        if (!$assessment = get_record("workshop_assessments", "id", $comment->assessmentid)) {
            error("Update Comment: Assessment not found");
        }
        //save the comment for the assessment...
        if (isset($form->comments)) {
            set_field("workshop_comments", "comments", $form->comments, "id", $comment->id);
            set_field("workshop_comments", "timecreated", $timenow, "id", $comment->id);
            // ..and kick to comment into life (probably not needed but just in case)
            set_field("workshop_comments", "mailed", 0, "id", $comment->id);
            echo "<centre><b>".get_string("savedok", "workshop")."</b></div><br />\n";

            add_to_log($course->id, "workshop", "comment",
                    "viewassessment.php?id=$cm->id&amp;aid=$assessment->id", "$comment->id");
        }

        print_continue("viewassessment.php?id=$cm->id&amp;aid=$assessment->id");
    }


    /****************** update grading (by teacher) ***************************/
    elseif ($action == 'updategrading') {
        $timenow = time();

        require_capability('mod/workshop:manage', $context);

        $form = (object)$_POST;

        if (!$assessment = get_record("workshop_assessments", "id", $_POST['aid'])) {
            error("Update Grading failed");
        }
        //save the comment and grade for the assessment
        if (isset($form->teachercomment)) {
            set_field("workshop_assessments", "teachercomment", $form->teachercomment, "id", $assessment->id);
            set_field("workshop_assessments", "gradinggrade", $form->gradinggrade, "id", $assessment->id);
            set_field("workshop_assessments", "timegraded", $timenow, "id", $assessment->id);
            set_field("workshop_assessments", "mailed", 0, "id", $assessment->id);
            set_field("workshop_assessments", "teachergraded", 1, "id", $assessment->id);
            echo "<centre><b>".get_string("savedok", "workshop")."</b></centre><br />\n";

            add_to_log($course->id, "workshop", "grade",
                 "viewassessment.php?id=$cm->id&amp;aid=$assessment->id", "$assessment->id", "$cm->id");
        }
        redirect($form->redirect);
    }


    /****************** view all assessments ***********************/
    elseif ($action == 'viewallassessments') {

        if (!$submission = get_record("workshop_submissions", "id", $sid)) {
            error("View All Assessments: submission record not found");
        }

        if ($assessments = workshop_get_assessments($submission)) {
            foreach ($assessments as $assessment) {
                workshop_print_assessment($workshop, $assessment);
            }
        }
        // only called from list all submissions
        print_continue("submissions.php?action=listallsubmissions&amp;id=$cm->id");
    }

    /*************** no man's land **************************************/
    else {
        error("Fatal Error: Unknown Action: ".$action."\n");
    }
    print_footer($course);

?>
