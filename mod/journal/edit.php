<?PHP // $Id$

	require("../../config.php");

    require_variable($id);    // Course Module ID

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (! $journal = get_record("journal", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    $entry = get_record_sql("SELECT * FROM journal_entries 
                             WHERE user='$USER->id' AND journal='$journal->id'");


/// If data submitted, then process and store.

	if (match_referer() && isset($HTTP_POST_VARS)) {

		$timenow = time();

		if ($entry) {
            $newentry->id = $entry->id;
            $newentry->text = $text;
            $newentry->modified = $timenow;
			if (! update_record("journal_entries", $newentry)) {
				error("Could not update your journal");
			}
            add_to_log("Update journal: $journal->name", $course->id);
		} else {
            $newentry->user = $USER->id;
            $newentry->journal = $journal->id;
            $newentry->modified = $timenow;
            $newentry->text = $text;
			if (! insert_record("journal_entries", $newentry)) {
				error("Could not insert a new journal entry");
			}
            add_to_log("Add journal: $journal->name", $course->id);
		} 
		
		redirect("view.php?id=$cm->id");
		die;
	}

/// Otherwise fill and print the form.

    if (! $entry ) {
        $entry->text = "";
    }

    print_header("$course->shortname: $journal->name", "$course->fullname",
                 "<A HREF=/course/view.php?id=$course->id>$course->shortname</A> -> 
                  <A HREF=/mod/journal/index.php?id=$course->id>Journals</A> -> 
                  <A HREF=\"view.php?id=$cm->id\">$journal->name</A> -> Edit", "form.text");

    echo "<CENTER>\n";

    print_simple_box( text_to_html($journal->intro) , "center");

    echo "<BR>";

	include("edit.html");

    print_footer($course);

?>
