<?PHP  // $Id$

    require("../../config.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "choice", "view all", "index?id=$course->id", "");

    print_header("$course->shortname: Choices", "$course->fullname",
                 "<A HREF=../../course/view.php?id=$course->id>$course->shortname</A> -> Choices", "");


    if (! $choices = get_all_instances_in_course("choice", $course->id, "cw.section ASC")) {
        notice("There are no choices", "../../course/view.php?id=$course->id");
    }

    if ( $allanswers = get_records_sql("SELECT * FROM choice_answers WHERE user='$USER->id'")) {
        foreach ($allanswers as $aa) {
            $answers[$aa->choice] = $aa;
        }

    } else {
        $answers = array () ;
    }


    $timenow = time();

    if ($course->format == "weeks") {
        $table->head  = array ("Week", "Question", "Answer");
        $table->align = array ("CENTER", "LEFT", "LEFT");
    } else if ($course->format == "topics") {
        $table->head  = array ("Topic", "Question", "Answer");
        $table->align = array ("CENTER", "LEFT", "LEFT");
    } else {
        $table->head  = array ("Question", "Answer");
        $table->align = array ("LEFT", "LEFT");
    }

    foreach ($choices as $choice) {
        $answer = $answers[$choice->id];
        switch ($answer->answer) {
            case 1:
                $aa = "$choice->answer1";
                break;
            case 2:
                $aa = "$choice->answer2";
                break;
            default:
                $aa = "Undecided";
                break;
        }

        if ($course->format == "weeks" || $course->format == "topics") {
            $table->data[] = array ("$choice->section",
                                    "<A HREF=\"view.php?id=$choice->coursemodule\">$choice->name</A>",
                                    "$aa");
        } else {
            $table->data[] = array ("<A HREF=\"view.php?id=$choice->coursemodule\">$choice->name</A>",
                                    "$aa");
        }
    }
    print_table($table);

    print_footer($course);

 
?>

