<?PHP // $Id$

	require_once("../../config.php");

    require_variable($id);    // course module ID
    require_variable($mode);  // edit or delete
    optional_variable($go);  // commit the operation?
    optional_variable($entry);  // edit or delete

    $strglossary = get_string("modulename", "glossary");
    $strglossaries = get_string("modulenameplural", "glossary");
    $stredit = get_string("edit");
    $entrydeleted = get_string("entrydeleted","glossary");


    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (isguest()) {
        error("Guests are not allowed to edit ir delete entries", $_SERVER["HTTP_REFERER"]);
    }

    if (! $glossary = get_record("glossary", "id", $cm->instance)) {
        error("Glossary is incorrect");
    }

    $entryfields = get_record("glossary_entries", "id", $entry);
    $strareyousuredelete = get_string("areyousuredelete","glossary");


    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }
    print_header("$course->shortname: $glossary->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strglossaries</A> -> $glossary->name", 
                  "", "", true, update_module_button($cm->id, $course->id, $strglossary), 
                  navmenu($course, $cm));

/// If data submitted, then process and store.
    
    if ($mode == "edit" or $mode == "delete" ) {
	echo "<p>";
	if ( isteacher($cm->id) or $glossary->studentcanpost ) {
		if ($go) {	// the operation was confirmed.
			if ( $mode == "delete") {
				glossary_delete_old_attachments($entry);
				delete_records("glossary_entries","id", $entry);				
				print_simple_box_start("center","40%", "#FFBBBB");
				echo "<center>$entrydeleted"; //CAMBIAR
				echo "</center>";
				print_simple_box_end();
			}
    			print_footer($course);
                  add_to_log($course->id, "glossary", "delete entry", "view.php?id=$cm->id&currentview=$currentview&cat=$cat", $entry);
			redirect("view.php?id=$cm->id&currentview=$currentview&cat=$cat");
		} else {		// the operation has not been confirmed yet so ask the user to do so
			if ( $mode == "delete") {				
				print_simple_box_start("center","40%", "#FFBBBB");
				echo "<center><b>$entryfields->concept</b><br>$strareyousuredelete";
				
				?>
                    <form name="form" method="post" action="deleteentry.php">

                    <input type="hidden" name=id 		   value="<?php p($cm->id) ?>">
                    <input type="hidden" name=mode         value="delete">
                    <input type="hidden" name=go       value="1">
                    <input type="hidden" name=entry         value="<?php p($entry) ?>">
                    <input type="hidden" name=currentview value=<? p($currentview) ?>>
                    <input type="hidden" name=cat=<? p($cat) ?>>

                    <input type="submit" value=" <?php print_string("yes")?> ">
                    <input type=button value=" <?php print_string("no")?> " onclick="javascript:history.go(-1);">

                    </form>
               	</center>
               	<?
				print_simple_box_end();
			}
		} 
	} else {
		error("You are not allowed to edit or delete entries");
	}
    } else {
    }
    print_footer($course);
?>
