<?PHP  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "choice", "view all", "index?id=$course->id", "");

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    } else {
        $navigation = "";
    }

    $strchoice = get_string("modulename", "choice");
    $strchoices = get_string("modulenameplural", "choice");

    print_header("$course->shortname: $strchoices", "$course->fullname",
                 "$navigation $strchoices", "", "", true, "", navmenu($course));


    if (! $choices = get_all_instances_in_course("choice", $course)) {
        notice("There are no choices", "../../course/view.php?id=$course->id");
    }

    if ( $allanswers = get_records("choice_answers", "userid", $USER->id)) {
        foreach ($allanswers as $aa) {
            $answers[$aa->choice] = $aa;
        }

    } else {
        $answers = array () ;
    }


    $timenow = time();

    if ($course->format == "weeks") {
        $table->head  = array (get_string("week"), get_string("question"), get_string("answer"));
        $table->align = array ("CENTER", "LEFT", "LEFT");
    } else if ($course->format == "topics") {
        $table->head  = array (get_string("topic"), get_string("question"), get_string("answer"));
        $table->align = array ("CENTER", "LEFT", "LEFT");
    } else {
        $table->head  = array (get_string("question"), get_string("answer"));
        $table->align = array ("LEFT", "LEFT");
    }

    $currentsection = "";

    foreach ($choices as $choice) {
        if (!empty($answers[$choice->id])) {
            $answer = $answers[$choice->id];
        } else {
            $answer = "";
        }
        if (!empty($answer->answer)) {
            $aa = choice_get_answer($choice, $answer->answer);
        } else {
            $aa = "";
        }
        $printsection = "";
        if ($choice->section !== $currentsection) {
            if ($choice->section) {
                $printsection = $choice->section;
            }
            if ($currentsection !== "") {
                $table->data[] = 'hr';
            }
            $currentsection = $choice->section;
        }
        
        //Calculate the href
        if (!$choice->visible) {
            //Show dimmed if the mod is hidden
            $tt_href = "<a class=\"dimmed\" href=\"view.php?id=$choice->coursemodule\">$choice->name</a>";
        } else {
            //Show normal if the mod is visible
            $tt_href = "<a href=\"view.php?id=$choice->coursemodule\">$choice->name</a>";
        }
        if ($course->format == "weeks" || $course->format == "topics") {
            $table->data[] = array ($printsection, $tt_href, $aa);
        } else {
            $table->data[] = array ($tt_href, $aa);
        }
    }
    echo "<br />";
    print_table($table);

    print_footer($course);
 
?>

