<?php  // $Id$
    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);    // course module ID
    require_variable($entry);    // Entry ID
    optional_variable($confirm);     // confirmation
    optional_variable($mode);
    optional_variable($hook);

    $hook = strip_tags(urldecode($hook));  //XSS
    $mode = strip_tags(urldecode($mode));  //XSS
        
    global $THEME, $USER, $CFG;

    $PermissionGranted = 1;

    $cm = get_record("course_modules","id",$id);
    if ( ! $cm ) {
        $PermissionGranted = 0;
    } else {
        $mainglossary = get_record("glossary","course",$cm->course, "mainglossary",1);
        if ( ! $mainglossary ) {
            $PermissionGranted = 0;
        }
    }

    if ( !isteacher($cm->course) ) {
        $PermissionGranted = 0;
        error("You must be a teacher to use this page.");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    if (! $glossary = get_record("glossary", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    $strglossaries   = get_string("modulenameplural", "glossary");
    $entryalreadyexist = get_string("entryalreadyexist","glossary");
    $entryexported = get_string("entryexported","glossary");

    print_header_simple("$glossary->name", "",
                 "<a href=\"index.php?id=$course->id\">$strglossaries</a> -> $glossary->name",
                  "", "", true, "",
                  navmenu($course, $cm));

    if ( $PermissionGranted ) {
        $entry = get_record("glossary_entries", "id", $entry);

        if ( !$confirm ) {
            echo "<center>";
            $areyousure = get_string("areyousureexport","glossary");
            notice_yesno ("<center><h2>$entry->concept</h2><p align=\"center\">$areyousure<br /><b>$mainglossary->name</b>?",
                "exportentry.php?id=$id&amp;mode=$mode&amp;hook=$hook&amp;entry=$entry->id&amp;confirm=1",
                "view.php?id=$cm->id&amp;mode=$mode&amp;hook=$hook" );

        } else {
            if ( ! $mainglossary->allowduplicatedentries ) {
                $dupentry = get_record("glossary_entries","glossaryid", $mainglossary->id, "UCASE(concept)",strtoupper($entry->concept));
                if ( $dupentry ) {
                    $PermissionGranted = 0;
                }
            }
            if ( $PermissionGranted ) {
            
                $entry->glossaryid       = $mainglossary->id;
                $entry->sourceglossaryid = $glossary->id;
                
                if (! update_record("glossary_entries", $entry)) {
                    error("Could not export the entry to the main glossary");
                } else {
                    print_simple_box_start("center", "60%", "$THEME->cellheading");
                    echo "<p align=\"center\"><font size=\"3\">$entryexported</font></p></font>";

                    print_continue("view.php?id=$cm->id&amp;mode=entry&amp;hook=".$entry->id);
                    print_simple_box_end();

                    print_footer();

                    redirect("view.php?id=$cm->id&amp;mode=entry&amp;hook=".$entry->id);
                    die;
                }
            } else {
                print_simple_box_start("center", "60%", "#FFBBBB");
                echo "<p align=\"center\"><font size=\"3\">$entryalreadyexist</font></p></font>";
                echo "<p align=\"center\">";

                print_continue("view.php?id=$cm->id&amp;mode=entry&amp;hook=".$entry->id);

                print_simple_box_end();
            }
        }
    } else {
            print_simple_box_start("center", "60%", "#FFBBBB");
            notice("A weird error was found while trying to export this entry. Operation cancelled.");

            print_continue("view.php?id=$cm->id&amp;mode=entry&amp;hook=".$entry->id);

            print_simple_box_end();
    }

    print_footer();
?>
