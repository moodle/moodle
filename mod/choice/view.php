<?PHP  // $Id$

    require("../../config.php");

    require_variable($id);    // Course Module ID

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (! $choice = get_record("choice", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    if ($current = get_record_sql("SELECT * FROM choice_answers
                                     WHERE choice='$choice->id' AND user='$USER->id'")) {
        if ($current->answer == "1") {
            $answer1checked = "CHECKED";
        } else if ($current->answer == "2") {
            $answer2checked = "CHECKED";
        }
    }

    if (match_referer() && isset($HTTP_POST_VARS)) {    // form submitted
        $form = (object)$HTTP_POST_VARS;
        if ($current) {
            if (! update_choice_in_database($current, $form->answer)) {
                error("Could not update your choice");
            }
            add_to_log($course->id, "choice", "update", "view.php?id=$cm->id", "$choice->id");
        } else {
            if (! add_new_choice_to_database($choice, $form->answer)) {
                error("Could not save your choice");
            }
            add_to_log($course->id, "choice", "add", "view.php?id=$cm->id", "$choice->id");
        }
        redirect("$CFG->wwwroot/course/view.php?id=$course->id");
        exit;
    }

    add_to_log($course->id, "choice", "view", "view.php?id=$cm->id", "$choice->id");

    print_header("$course->shortname: $choice->name", "$course->fullname",
                 "<A HREF=../../course/view.php?id=$course->id>$course->shortname</A> -> 
                  <A HREF=index.php?id=$course->id>Choices</A> -> $choice->name", "", "", true,
                  update_module_icon($cm->id));

    if (isteacher($course->id)) {
        echo "<P align=right><A HREF=\"report.php?id=$cm->id\">View all responses</A></P>";
    }

    print_simple_box( text_to_html($choice->text) , "center");

    require("view.html");

    print_footer($course);



// Functions /////////////////////////////////////////////////

function add_new_choice_to_database($choice, $answer) {
    global $db;
    global $USER;

    $timenow = time();

    $rs = $db->Execute("INSERT INTO choice_answers (choice, user, answer, timemodified)
                        VALUES ( '$choice->id', '$USER->id', '$answer', '$timenow')");
    return $rs;
}

function update_choice_in_database($current, $answer) {
    global $db;

    $timenow = time();

    $rs = $db->Execute("UPDATE choice_answers
                        SET answer='$answer', timemodified='$timenow' 
                        WHERE id = '$current->id'");
    return $rs;
}

?>
