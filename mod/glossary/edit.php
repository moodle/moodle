<?PHP // $Id$

	require_once("../../config.php");

    require_variable($id);    // Course Module ID 
    optional_variable($e);    // EntryID

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (isguest()) {
        error("Guests are not allowed to edit glossaries", $_SERVER["HTTP_REFERER"]);
    }

    if (! $glossary = get_record("glossary", "id", $cm->instance)) {
        error("Course module is incorrect");
    }
    
    if ($e) {
        $form = get_record("glossary_entries", "id", $e);

        $newentry->id = $form->id;
	  $newentry->concept = $form->concept;
	  $newentry->definition = $form->definition;
	  $newentry->format = $form->format;
	  $newentry->timemodified = time();

	  $entry->id = $form->id;
	  $entry->text = $form->definition;
	  $entry->format = $form->format;
    } else {
      if ($form = data_submitted()) {
/// If data submitted, then process and store.
		$timenow = time();

            $form->text = clean_text($form->text, $form->format);

		if ($entry) {
               $newentry->id = $entry;
               $newentry->concept = $form->concept;
               $newentry->definition = $form->text;
               $newentry->format = $form->format;
	  	   $newentry->timemodified = time();		   
		   $newentry->teacherentry = isteacher($course->id,$USER->id);
		   $PermissionGranted = 1;
		   if ( !$glossary->allowduplicatedentries ) {
			$dupentries = get_records("glossary_entries","UCASE(concept)", strtoupper($newentry->concept));
			if ($dupentries) {
				foreach ($dupentries as $curentry) {
					if ( $curentry->id != $entry) {
						$PermissionGranted = 0;
					}
				}
			}
		   }
		   if ( $PermissionGranted ) {
		   	if (! update_record("glossary_entries", $newentry)) {
				error("Could not update your glossary");
			} else {
               		add_to_log($course->id, "glossary", "update entry", "view.php?id=$cm->id&eid=$newentry->id", "$newentry->id");
			}
		   } else {
				error("Could not update this glossary entry because this concept already exist.");
		   }
		} else {
               $newentry->userid = $USER->id;
               $newentry->glossaryid = $glossary->id;
               $newentry->concept = $form->concept;
               $newentry->definition = $form->text;
               $newentry->format = $form->format;
	  	   $newentry->timecreated = time();
	   	   $newentry->timemodified = time();
		   $newentry->teacherentry = isteacher($course->id,$USER->id);

		   $PermissionGranted = 1;
		   if ( !$glossary->allowduplicatedentries ) {
			$dupentries = get_record("glossary_entries","UCASE(concept)", strtoupper($newentry->concept));
			if ($dupentries) {
				$PermissionGranted = 0;
			}
		   }
		   if ( $PermissionGranted ) {
		   	if (! $newentry->id = insert_record("glossary_entries", $newentry)) {
				error("Could not insert a new glossary entry");
			} else {
               		add_to_log($course->id, "glossary", "add entry", "view.php?id=$cm->id&eid=$newentry->id", "$newentry->id");
			}
		   } else {
				error("Could not insert this glossary entry because this concept already exist.");
		   }
		} 
		
		redirect("view.php?id=$cm->id&eid=$newentry->id");
		die;
	   }
    }
/// Otherwise fill and print the form.

    $strglossary = get_string("modulename", "glossary");
    $strglossaries = get_string("modulenameplural", "glossary");
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

    print_header("$course->shortname: $glossary->name", "$course->fullname",
                 "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> 
                  <A HREF=\"index.php?id=$course->id\">$strglossaries</A> -> 
                  <A HREF=\"view.php?id=$cm->id\">$glossary->name</A> -> $stredit", "theform.text",
                  "", true, "", navmenu($course, $cm));

    echo "<CENTER>\n";

    print_simple_box( text_to_html($glossary->name) , "center");

    echo "<BR>";

	include("edit.html");

    print_footer($course);

?>
