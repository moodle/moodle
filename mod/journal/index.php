<?PHP // $Id$

    require("../../config.php");
    require("lib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);
    add_to_log($course->id, "journal", "view all", "index.php?id=$course->id", "");

    print_header("$course->shortname: Journals", "$course->fullname",
                 "<A HREF=../../course/view.php?id=$course->id>$course->shortname</A> -> Journals", "");


    if (! $journals = get_all_instances_in_course("journal", $course->id, "cw.section ASC")) {
        notice("There are no journals", "../../course/view.php?id=$course->id");
        die;
    }

    $timenow = time();

    if ($course->format == "weeks") {
        $table->head  = array ("Week", "Question", "Answer");
        $table->align = array ("CENTER", "LEFT", "LEFT");
    } else if ($course->format == "topics") {
        $table->head  = array ("Topic", "Question", "Answer");
        $table->align = array ("CENTER", "LEFT", "LEFT");
    } else {
        $table->head  = array ("Name", "Answer");
        $table->align = array ("LEFT", "LEFT");
    }

    foreach ($journals as $journal) {

        $entry = get_record_sql("SELECT text FROM journal_entries 
                                 WHERE user='$USER->id' AND journal='$journal->id'");

        $journal->timestart  = $course->startdate + (($journal->section - 1) * 608400);
        if ($journal->daysopen) {
            $journal->timefinish = $journal->timestart + (3600 * 24 * $journal->daysopen);
        } else {
            $journal->timefinish = 9999999999;
        }
        $journalopen = ($journal->timestart < $timenow && $timenow < $journal->timefinish);


        $text = text_to_html($entry->text)."<P ALIGN=right><A HREF=\"view.php?id=$journal->coursemodule\">";
        if ($journalopen) {
            $text .= "Edit</A></P>";
        } else {
            $text .= "View</A></P>";
        }
        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ("$journal->section",
                                    text_to_html($journal->intro),
                                    $text);
        } else {
            $table->data[] = array (text_to_html($journal->intro),
                                    $text);
        }
    }

    print_table($table);

    print_footer($course);

 
?>

