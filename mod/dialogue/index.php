<?PHP // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);   // course

    if (!$course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);
    add_to_log($course->id, "dialogue", "view all", "index.php?id=$course->id", "");

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strdialogue = get_string("modulename", "dialogue");
    $strdialogues = get_string("modulenameplural", "dialogue");
    $strname = get_string("name");
    $stropendialogues = get_string("open", "dialogue")." ".$strdialogues;
    $strcloseddialogues = get_string("closed", "dialogue")." ".$strdialogues;

    print_header("$course->shortname: $strdialogues", "$course->fullname", "$navigation $strdialogues", 
                 "", "", true, "", navmenu($course));


    if (!$dialogues = get_all_instances_in_course("dialogue", $course)) {
        notice("There are no dialogues", "../../course/view.php?id=$course->id");
        die;
    }

    $timenow = time();

    $table->head  = array ($strname, $stropendialogues, $strcloseddialogues);
    $table->align = array ("CENTER", "CENTER", "CENTER");
 
    foreach ($dialogues as $dialogue) {

       if (!$cm = get_coursemodule_from_instance("dialogue", $dialogue->id, $course->id)) {
           error("Course Module ID was incorrect");
       }
	   $table->data[] = array ("<a href=\"view.php?id=$cm->id\">$dialogue->name</a>",
                dialogue_count_open($dialogue, $USER), dialogue_count_closed($dialogue, $USER));
    }
    echo "<br />";
    print_table($table);

    print_footer($course);
 
?>

