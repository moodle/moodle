<?PHP // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);    // Course Module ID

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (! $survey = get_record("survey", "id", $cm->instance)) {
        error("Survey ID was incorrect");
    }

    if (! $template = get_record("survey", "id", $survey->template)) {
        error("Template ID was incorrect");
    }

    $showscales = ($template->name != 'ciqname');
 
    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strsurveys = get_string("modulenameplural", "survey");
    $strsurvey = get_string("modulename", "survey");

    print_header("$course->shortname: $survey->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strsurveys</A> -> $survey->name", "", "", true,
                  update_module_button($cm->id, $course->id, $strsurvey), navmenu($course, $cm));

/// Check to see if groups are being used in this survey
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = get_current_group($course->id);
    } else {
        $currentgroup = 0;
    }

    if (isteacheredit($course->id) or ($groupmode == VISIBLEGROUPS)) {
        $currentgroup = 0;
    }

    if (isteacher($course->id)) {
        $numusers = survey_count_responses($survey->id, $currentgroup);
        echo "<p align=right><a href=\"report.php?id=$cm->id\">".
              get_string("viewsurveyresponses", "survey", $numusers)."</A></P>";
    } else if (!$cm->visible) {
        notice(get_string("activityiscurrentlyhidden"));
    }

    if (isguest()) {
        notify(get_string("guestsnotallowed", "survey"));
    }


//  Check the survey hasn't already been filled out.

    if (survey_already_done($survey->id, $USER->id)) {

        add_to_log($course->id, "survey", "view graph", "view.php?id=$cm->id", $survey->id, $cm->id);
        $numusers = survey_count_responses($survey->id, $currentgroup);

        if ($showscales) {
            print_heading(get_string("surveycompleted", "survey"));
            print_heading(get_string("peoplecompleted", "survey", $numusers));
            echo "<center>";
            survey_print_graph("id=$cm->id&sid=$USER->id&group=$currentgroup&type=student.png");
            echo "</center>";

        } else {

            print_simple_box(format_text($survey->intro), "center", "80%");
            print_spacer(30);

            $questions = get_records_list("survey_questions", "id", $survey->questions);
            $questionorder = explode(",", $survey->questions);
            foreach ($questionorder as $key => $val) {
                $question = $questions[$val];
                if ($question->type == 0 or $question->type == 1) {
                    if ($answer = survey_get_user_answer($survey->id, $question->id, $USER->id)) {
                        $table = NULL;
                        $table->head = array(get_string($question->text, "survey"));
                        $table->align = array ("left");
                        $table->data[] = array("$answer->answer1");
                        print_table($table);
                        print_spacer(30);
                    }
                }
            }
        }
   
        print_footer($course);
        exit;
    }

//  Start the survey form
    add_to_log($course->id, "survey", "view form", "view.php?id=$cm->id", $survey->id, $cm->id);

    echo "<form name=form method=post action=save.php>";
    echo "<input type=hidden name=id value=$id>";

    print_simple_box(format_text($survey->intro), "center", "80%");

// Get all the major questions and their proper order
    if (! $questions = get_records_list("survey_questions", "id", $survey->questions)) {
        error("Couldn't find any questions in this survey!!");
    }
    $questionorder = explode( ",", $survey->questions);

// Cycle through all the questions in order and print them

    $qnum = 0;
    foreach ($questionorder as $key => $val) {
        $question = $questions["$val"];
        $question->id = $val;
        
        if ($question->type >= 0) {

            if ($question->text) {
                $question->text = get_string($question->text, "survey");
            }

            if ($question->shorttext) {
                $question->shorttext = get_string($question->shorttext, "survey");
            }

            if ($question->intro) {
                $question->intro = get_string($question->intro, "survey");
            }

            if ($question->options) {
                $question->options = get_string($question->options, "survey");
            }

            if ($question->multi) {
                survey_print_multi($question);
            } else {
                survey_print_single($question);
            }
        }
    }

    if (isguest()) {
        echo "</form>";
        print_footer($course);
        exit;
    }

?>

<center>
<br />
<script>
<!-- // BEGIN
function checkform() {

    var error=false;

    with (document.form) {
    <?php
       if (!empty($checklist)) {
           foreach ($checklist as $question => $default) {
               echo "  if (".$question."[".$default."].checked) error=true;\n";
           }
       }
    ?>
    }

    if (error) {
        alert("<?php print_string("questionsnotanswered", "survey") ?>");
    } else {
        document.form.submit();
    }
}

<?php echo "document.write('<INPUT TYPE=button VALUE=\"".get_string("clicktocontinuecheck", "survey")."\" onClick=\"checkform()\">');";  ?>

// END -->
</SCRIPT>

<noscript>
    <!-- Without Javascript, no checking is done -->
    <input type="submit" value="<?php  get_string("clicktocontinue", "survey") ?>">
</noscript>

</center>

<?php
   echo "</form>";

   print_footer($course);

?>
