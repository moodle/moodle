<?PHP // $Id$

    require("../../config.php");
    require("lib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "survey", "view all", "index.php?id=$course->id", "");

    print_header("$course->shortname: Surveys", "$course->fullname",
                 "<A HREF=../../course/view.php?id=$course->id>$course->shortname</A> -> Surveys", "");


    if (! $surveys = get_all_instances_in_course("survey", $course->id, "cw.section ASC")) {
        notice("There are no surveys.", "../../course/view.php?id=$course->id");
    }
    
    $table->head  = array ("Week", "Name", "Status");
    $table->align = array ("CENTER", "LEFT", "LEFT");

    foreach ($surveys as $survey) {
        if (survey_already_done($survey->id, $USER->id)) {
            $ss = "Done";
        } else {
            $ss = "<A HREF=\"view.php?id=$survey->coursemodule\">Not done yet</A>";
        }
        $table->data[] = array ("$survey->section", 
                                "<A HREF=\"view.php?id=$survey->coursemodule\">$survey->name</A>",
                                "$ss");
    }

    print_table($table);
    print_footer($course);

?>
