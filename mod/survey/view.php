<?PHP // $Id$

    include("../../config.php");
    include("lib.php");

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
 
    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strsurveys = get_string("modulenameplural", "survey");

    print_header("$course->shortname: $survey->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strsurveys</A> -> $survey->name", "", "", true,
                  update_module_icon($cm->id, $course->id));

    if (isteacher($course->id)) {
        $numusers = survey_count_responses($survey->id);
        echo "<P align=right><A HREF=\"report.php?id=$cm->id\">".
              get_string("viewsurveyresponses", "survey", $numusers)."</A></P>";
    }


//  Check the survey hasn't already been filled out.

    if (survey_already_done($survey->id, $USER->id)) {
        add_to_log($course->id, "survey", "view graph", "view.php?id=$cm->id", "$survey->id");
        print_heading(get_string("surveycompleted", "survey"));
        $numusers = survey_count_responses($survey->id);
        print_heading(get_string("peoplecompleted", "survey", $numusers));
        echo "<CENTER>";
        echo "<IMG SRC=\"$CFG->wwwroot/mod/survey/graph.php?id=$cm->id&sid=$USER->id&type=student.png\">";
        echo "</CENTER>";
        print_footer($course);
        exit;
    }

//  Start the survey form
    add_to_log($course->id, "survey", "view form", "view.php?id=$cm->id", "$survey->id");

    echo "<FORM NAME=form METHOD=post ACTION=save.php>";
    echo "<INPUT TYPE=hidden NAME=id VALUE=$id>";

    print_simple_box(text_to_html($survey->intro), "center", "80%");

// Get all the major questions and their proper order
    if (! $questions = get_records_sql("SELECT * FROM survey_questions WHERE id in ($survey->questions)")) {
        error("Couldn't find any questions in this survey!!");
    }
    $questionorder = explode( ",", $survey->questions);

// Cycle through all the questions in order and print them

    $qnum = 0;
    foreach ($questionorder as $key => $val) {
        $question = $questions["$val"];
        $question->id = $val;
        
        if ($question->type > 0) {
            if ($question->multi) {
                survey_print_multi($question);
            } else {
                survey_print_single($question);
            }
        }
    }


// End the survey page
   echo "<CENTER><P>&nbsp;</P><P>";
   if ($ownerpreview) {
       echo "(Because this is only a preview, the button below will not send data)<BR>\n";
       echo "<FONT SIZE=+1><INPUT TYPE=submit VALUE=\"Click here to go back\"></FONT>";
   } else {
       echo "\n";
?>

<SCRIPT>
<!-- // BEGIN
function checkform() {

    var error=false;

    with (document.form) {
    <? foreach ($checklist as $question => $default) {
           echo "  if (".$question."[".$default."].checked) error=true;\n";
    }?>
    }

    if (error) {
        alert("Some of the multiple choice questions have not been answered.");
    } else {
        document.form.submit();
    }
}

document.write('<INPUT TYPE="button" VALUE="Click here to check and continue" onClick="checkform()">');

// END -->
</SCRIPT>

<NOSCRIPT>
    <!-- Without Javascript, no checking is done -->
    <INPUT TYPE="submit" VALUE="Click here to continue">
</NOSCRIPT>
<?

   }
   echo "</FORM>";

   print_footer($course);

?>
