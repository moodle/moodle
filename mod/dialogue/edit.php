<?PHP // $Id$

	require_once("../../config.php");

    require_variable($id);    // Course Module ID

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (isguest()) {
        error("Guests are not allowed to edit dialogues", $_SERVER["HTTP_REFERER"]);
    }

    if (! $dialogue = get_record("dialogue", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    $entry = get_record("dialogue_entries", "userid", $USER->id, "dialogue", $dialogue->id);


/// If data submitted, then process and store.

    if ($form = data_submitted()) {

		$timenow = time();

        $form->text = clean_text($form->text, $form->format);

		if ($entry) {
            $newentry->id = $entry->id;
            $newentry->text = $form->text;
            $newentry->format = $form->format;
            $newentry->modified = $timenow;
			if (! update_record("dialogue_entries", $newentry)) {
				error("Could not update your dialogue");
			}
            add_to_log($course->id, "dialogue", "update entry", "view.php?id=$cm->id", "$newentry->id");
		} else {
            $newentry->userid = $USER->id;
            $newentry->dialogue = $dialogue->id;
            $newentry->text = $form->text;
            $newentry->format = $form->format;
            $newentry->modified = $timenow;
			if (! $newentry->id = insert_record("dialogue_entries", $newentry)) {
				error("Could not insert a new dialogue entry");
			}
            add_to_log($course->id, "dialogue", "add entry", "view.php?id=$cm->id", "$newentry->id");
		} 
		
		redirect("view.php?id=$cm->id");
		die;
	}

/// Otherwise fill and print the form.

    $strdialogue = get_string("modulename", "dialogue");
    $strdialogues = get_string("modulenameplural", "dialogue");
    $stredit = get_string("edit");

    if ($usehtmleditor = can_use_richtext_editor()) {
        $defaultformat = FORMAT_HTML;
        $onsubmit = "onsubmit=\"copyrichtext(theform.text);\"";
    } else {
        $defaultformat = FORMAT_MOODLE;
        $onsubmit = "";
    }

    if (empty($entry)) {
        $entry->text = "";
        $entry->format = $defaultformat;
    }

    print_header("$course->shortname: $dialogue->name", "$course->fullname",
                 "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> 
                  <A HREF=\"index.php?id=$course->id\">$strdialogues</A> -> 
                  <A HREF=\"view.php?id=$cm->id\">$dialogue->name</A> -> $stredit", "theform.text",
                  "", true, "", navmenu($course, $cm));

    echo "<CENTER>\n";

    print_simple_box( text_to_html($dialogue->intro) , "center");

    echo "<BR>";

	include("edit.html");

    print_footer($course);

?>
