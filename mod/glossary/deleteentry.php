<?PHP // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);    // course module ID
    require_variable($mode);  // edit or delete
    optional_variable($go);  // commit the operation?
    optional_variable($entry);  // entry id
    require_variable($prevmode);  //  current frame
    optional_variable($hook);         // pivot id 

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
        if ( isteacher($course->id) or $glossary->studentcanpost ) {
            if ($go) {	// the operation was confirmed.
                if ( $mode == "delete") {
                    // if it is an imported entry, just delete the relation
                    $entry = get_record("glossary_entries","id", $entry);
                    if ( $entry->sourceglossaryid ) {
                        $entry->glossaryid = $entry->sourceglossaryid;
                        $entry->sourceglossaryid = 0;
                        if (! update_record("glossary_entries", $entry)) {
                   	        error("Could not update your glossary");
                        }
                    } else {
                        if ( $entry->attachment ) {
                            glossary_delete_old_attachments($entry);
                        }
                        delete_records("glossary_comments", "entryid",$entry->id);
                        delete_records("glossary_alias", "entryid", $entry->id);
                        delete_records("glossary_ratings", "entryid", $entry->id);

                        delete_records("glossary_entries","id", $entry->id);				
                    }

                    print_simple_box_start("center","40%", "#FFBBBB");
                    echo "<center>$entrydeleted"; 
                    echo "</center>";
                    print_simple_box_end();
                }
                print_footer($course);
                add_to_log($course->id, "glossary", "delete entry", "view.php?id=$cm->id&mode=$prevmode&hook=$hook", $entry,$cm->id);
                redirect("view.php?id=$cm->id&mode=$prevmode&hook=$hook");
            } else {        // the operation has not been confirmed yet so ask the user to do so
                if ( $mode == "delete") {				
                    print_simple_box_start("center","40%", "#FFBBBB");
                    echo "<center><b>$entryfields->concept</b><br>$strareyousuredelete";

                    ?>
                        <form name="form" method="post" action="deleteentry.php">

                        <input type="hidden" name=id 		   value="<?php p($cm->id) ?>">
                        <input type="hidden" name=mode         value="delete">
                        <input type="hidden" name=go       value="1">
                        <input type="hidden" name=entry         value="<?php p($entry) ?>">
                        <input type="hidden" name=prevmode value=<?php p($prevmode) ?>>
                        <input type="hidden" name=hook     value=<?php p($hook) ?>>

                        <input type="submit" value=" <?php print_string("yes")?> ">
                        <input type=button value=" <?php print_string("no")?> " onclick="javascript:history.go(-1);">

                        </form>
                   	</center>
                   	<?php
                    print_simple_box_end();
                }
            }
        } else {
            error("You are not allowed to edit or delete entries");
        }
    }
    print_footer($course);
?>
