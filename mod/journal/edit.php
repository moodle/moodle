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

    if (isguest()) {
        error("Guests are not allowed to edit journals", $HTTP_REFERER);
    }

    if (! $journal = get_record("journal", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    $entry = get_record_sql("SELECT * FROM journal_entries 
                             WHERE user='$USER->id' AND journal='$journal->id'");


/// If data submitted, then process and store.

	if (match_referer() && isset($HTTP_POST_VARS)) {

		$timenow = time();

        $text = clean_text($text, $format);

		if ($entry) {
            $newentry->id = $entry->id;
            $newentry->text = $text;
            $newentry->modified = $timenow;
            $newentry->format = $format;
			if (! update_record("journal_entries", $newentry)) {
				error("Could not update your journal");
			}
            add_to_log($course->id, "journal", "update entry", "view.php?id=$cm->id", "$newentry->id");
		} else {
            $newentry->user = $USER->id;
            $newentry->journal = $journal->id;
            $newentry->modified = $timenow;
            $newentry->text = $text;
            $newentry->format = $format;
			if (! $newentry->id = insert_record("journal_entries", $newentry)) {
				error("Could not insert a new journal entry");
			}
            add_to_log($course->id, "journal", "add entry", "view.php?id=$cm->id", "$newentry->id");
		} 
		
		redirect("view.php?id=$cm->id");
		die;
	}

/// Otherwise fill and print the form.

    $strjournal = get_string("modulename", "journal");
    $strjournals = get_string("modulenameplural", "journal");
    $stredit = get_string("edit");

    if ($usehtmleditor = can_use_richtext_editor()) {
        $defaultformat = FORMAT_HTML;
        $onsubmit = "onsubmit=\"copyrichtext(theform.text);\"";
    } else {
        $defaultformat = FORMAT_MOODLE;
    }

    if (! $entry ) {
        $entry->text = "";
        $entry->format = $defaultformat;
    }

    print_header("$course->shortname: $journal->name", "$course->fullname",
                 "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> 
                  <A HREF=\"index.php?id=$course->id\">$strjournals</A> -> 
                  <A HREF=\"view.php?id=$cm->id\">$journal->name</A> -> $stredit", "form.text",
                  "", true, "", navmenu($course, $cm));

    echo "<CENTER>\n";

    print_simple_box( text_to_html($journal->intro) , "center");

    echo "<BR>";

	include("edit.html");

    print_footer($course);

?>
