<?PHP // $Id$
global $CFG, $USER, $THEME;

require_once("../../config.php");
require_once("lib.php");

require_variable($id);    // Course Module ID
optional_variable($e);    // EntryID
optional_variable($confirm,0);    // proceed. Edit the edtry

optional_variable($tab);   // categories if by category?
optional_variable($cat);    // CategoryID

if (! $cm = get_record("course_modules", "id", $id)) {
    error("Course Module ID was incorrect");
}

if (! $course = get_record("course", "id", $cm->course)) {
    error("Course is misconfigured");
}

require_login($course->id);

if ( isguest() ) {
    error("Guests are not allowed to edit glossaries", $_SERVER["HTTP_REFERER"]);
}

if (! $glossary = get_record("glossary", "id", $cm->instance)) {
    error("Course module is incorrect");
}
if ( $confirm ) {
    $form = data_submitted();
    if ( !isset($form->usedynalink) ) {
        $form->usedynalink = 0;
    }
    $timenow = time();
    $form->text = clean_text($form->text, $form->format);

    $newentry->course = $glossary->course;
    $newentry->glossaryid = $glossary->id;

    $newentry->concept = $form->concept;
    $newentry->definition = $form->text;
    $newentry->format = $form->format;
    $newentry->usedynalink = $form->usedynalink;
    $newentry->casesensitive = $form->casesensitive;
    $newentry->fullmatch = $form->fullmatch;
    $newentry->timemodified = $timenow;		

    if ($form->concept == '' or trim($form->text) == '' ) {
        $errors = get_string('fillfields','glossary');
        $strglossary = get_string("modulename", "glossary");
        $strglossaries = get_string("modulenameplural", "glossary");
        $stredit = get_string("edit");

        if ($usehtmleditor = can_use_richtext_editor()) {
            $defaultformat = FORMAT_HTML;
            $onsubmit = "onsubmit=\"copyrichtext(form.text);\"";
        } else {
            $defaultformat = FORMAT_MOODLE;
            $onsubmit = "";
        }

        print_header(strip_tags("$course->shortname: $glossary->name"), "$course->fullname",
             "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> 
              <A HREF=\"index.php?id=$course->id\">$strglossaries</A> -> 
              <A HREF=\"view.php?id=$cm->id\">$glossary->name</A> -> $stredit", "form.text",
              "", true, "", navmenu($course, $cm));

        print_heading($glossary->name);

        include("edit.html");

        print_footer($course);
        die;
    }

    if ($e) {
        $newentry->id = $e;
    
        $permissiongranted = 1;
        if ( !$glossary->allowduplicatedentries ) {
            if ($dupentries = get_records("glossary_entries","UCASE(concept)", strtoupper($newentry->concept))) {
                foreach ($dupentries as $curentry) {
                    if ( $glossary->id == $curentry->glossaryid ) {
                       if ( $curentry->id != $e ) {
                          $permissiongranted = 0;
                           break;
                       }
                    }
                }
            }
        }
    
        if ( $permissiongranted ) {
            $newentry->attachment = $_FILES["attachment"];
            if ($newfilename = glossary_add_attachment($newentry, $newentry->attachment)) {
                $newentry->attachment = $newfilename;
            } else {
                unset($newentry->attachment);
            }
            if (! update_record("glossary_entries", $newentry)) {
                error("Could not update your glossary");
            } else {
                add_to_log($course->id, "glossary", "update entry", "view.php?id=$cm->id&eid=$newentry->id&tab=$tab&cat=$cat", "$newentry->id");
           	}
        } else {
            error("Could not update this glossary entry because this concept already exist.");
        }
    } else {
        $newentry->userid = $USER->id;
        $newentry->timecreated = $timenow;
        $newentry->sourceglossaryid = 0;
        $newentry->approved = $glossary->defaultapproval or isteacher($course->id);
        $newentry->teacherentry = isteacher($course->id);

        $permissiongranted = 1;
        if ( !$glossary->allowduplicatedentries ) {
            if ($dupentries = get_record("glossary_entries","UCASE(concept)", strtoupper($newentry->concept), "glossaryid", $glossary->id)) {
                $permissiongranted = 0;
            }
        }
        if ( $permissiongranted ) {
            if (! $newentry->id = insert_record("glossary_entries", $newentry)) {
                error("Could not insert this new entry");
            } else {
                $newentry->attachment = $_FILES["attachment"];
                if ($newfilename = glossary_add_attachment($newentry, $newentry->attachment)) {
                    $newentry->attachment = $newfilename;
                } else {
                     unset($newentry->attachment);
                }
                set_field("glossary_entries", "attachment", $newfilename, "id", $newentry->id);
                add_to_log($course->id, "glossary", "add entry", "view.php?id=$cm->id&eid=$newentry->id&tab=$tab&cat=$cat", "$newentry->id");
            }
        } else {
            error("Could not insert this glossary entry because this concept already exist.");
        }
    }

    delete_records("glossary_entries_categories","entryid",$e);

    if ( isset($form->categories) ) {
        $newcategory->entryid = $newentry->id;
        foreach ($form->categories as $category) {
            if ( $category > 0 ) {
                $newcategory->categoryid =$category;
                insert_record("glossary_entries_categories",$newcategory);
            } else {
                break;
            }
        }
    }
    redirect("view.php?id=$cm->id&eid=$newentry->id&tab=$tab&cat=$cat");
    die;
} else {
    if ($e) {
        $form = get_record("glossary_entries", "id", $e);

        $newentry->id = $form->id;
        $newentry->concept = $form->concept;
        $newentry->definition = $form->definition;
        $newentry->format = $form->format;
        $newentry->timemodified = time();
        $newentry->approved = $glossary->defaultapproval or isteacher($course->id);
        $newentry->usedynalink = $form->usedynalink;
        $newentry->casesensitive = $form->casesensitive;
        $newentry->fullmatch = $form->fullmatch;
    } else {
        $newentry->concept = "";
        $newentry->definition = "";
        $newentry->usedynalink = 1;
        $newentry->casesensitive = 0;
        $newentry->fullmatch = 1;
    }
}
/// Otherwise fill and print the form.

$strglossary = get_string("modulename", "glossary");
$strglossaries = get_string("modulenameplural", "glossary");
$stredit = get_string("edit");

if ($usehtmleditor = can_use_richtext_editor()) {
    $defaultformat = FORMAT_HTML;
    $onsubmit = "onsubmit=\"copyrichtext(form.text);\"";
} else {
    $defaultformat = FORMAT_MOODLE;
    $onsubmit = "";
}

print_header(strip_tags("$course->shortname: $glossary->name"), "$course->fullname",
             "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> 
              <A HREF=\"index.php?id=$course->id\">$strglossaries</A> -> 
              <A HREF=\"view.php?id=$cm->id\">$glossary->name</A> -> $stredit", "form.text",
              "", true, "", navmenu($course, $cm));

print_heading($glossary->name);

include("edit.html");

print_footer($course);

?>
