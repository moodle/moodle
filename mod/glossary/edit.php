<?php // $Id$

require_once('../../config.php');
require_once('lib.php');

global $CFG, $USER;

$id = required_param('id', PARAM_INT);                // Course Module ID
$e  = optional_param('e', 0, PARAM_INT);              // EntryID
$confirm = optional_param('confirm',0, PARAM_INT);    // proceed. Edit the edtry

$mode = optional_param('mode', '', PARAM_ALPHA);      // categories if by category?
$hook = optional_param('hook', '', PARAM_ALPHANUM);   // CategoryID

if (! $cm = get_coursemodule_from_id('glossary', $id)) {
    error("Course Module ID was incorrect");
}

$context = get_context_instance(CONTEXT_MODULE, $cm->id);

if (! $course = get_record("course", "id", $cm->course)) {
    error("Course is misconfigured");
}

require_login($course->id, false, $cm);

if ( isguest() ) {
    error("Guests are not allowed to edit glossaries", $_SERVER["HTTP_REFERER"]);
}

if (! $glossary = get_record("glossary", "id", $cm->instance)) {
    error("Course module is incorrect");
}


if ($e) { // if entry is specified
    if (!$entry  = get_record("glossary_entries", "id", $e)) {
        error("Incorrect entry id");
    }
    $ineditperiod = ((time() - $entry->timecreated <  $CFG->maxeditingtime) || $glossary->editalways);
    if (!has_capability('mod/glossary:manageentries', $context) and !($entry->userid == $USER->id and ($ineditperiod and has_capability('mod/glossary:write', $context)))) {
        //expired edit time is the most probable cause here
        error(get_string('erredittimeexpired', 'glossary'), "view.php?id=$cm->id&amp;mode=entry&amp;hook=$e");
    }
} else { // new entry
    require_capability('mod/glossary:write', $context);
}

if ( $confirm ) {
    $form = data_submitted();
    trusttext_after_edit($form->text, $context);

    if ( !isset($form->usedynalink) ) {
        $form->usedynalink = 0;
    }
    if ( !isset($form->casesensitive) ) {
        $form->casesensitive = 0;
    }
    if ( !isset($form->fullmatch) ) {
        $form->fullmatch = 0;
    }
    $timenow = time();
    //$form->text = clean_text($form->text, $form->format);

    $newentry->course = $glossary->course;
    $newentry->glossaryid = $glossary->id;

    $newentry->concept = clean_text(trim($form->concept));
    $newentry->definition = $form->text;
    $newentry->format = $form->format;
    $newentry->usedynalink = $form->usedynalink;
    $newentry->casesensitive = $form->casesensitive;
    $newentry->fullmatch = $form->fullmatch;
    $newentry->timemodified = $timenow;
    $newentry->approved = 0;
    $newentry->aliases = "";
    if ( $glossary->defaultapproval or has_capability('mod/glossary:approve', $context) ) {
        $newentry->approved = 1;
    }

    $strglossary = get_string("modulename", "glossary");
    $strglossaries = get_string("modulenameplural", "glossary");
    $stredit = get_string("edit");

    if ($form->concept == '' or trim($form->text) == '' ) {
        $errors = get_string('fillfields','glossary');
        if ($usehtmleditor = can_use_richtext_editor()) {
            $defaultformat = FORMAT_HTML;
        } else {
            $defaultformat = FORMAT_MOODLE;
        }

        print_header_simple(format_string($glossary->name), "",
             "<a href=\"index.php?id=$course->id\">$strglossaries</a> ->
              <a href=\"view.php?id=$cm->id\">".format_string($glossary->name,true)."</a> -> $stredit", "form.text",
              "", true, "", navmenu($course, $cm));

        print_heading(format_string($glossary->name));

        /// Info box
        if ( $glossary->intro ) {
            print_simple_box(format_text($glossary->intro), 'center', '70%', '', 5, 'generalbox', 'intro');
        }
        echo '<br />';

        $tab = GLOSSARY_ADDENTRY_VIEW;
        include("tabs.html");

        include("edit.html");

        echo '</center>';

        glossary_print_tabbed_table_end();

        // Lets give IE more time to load the whole page
        // before trying to load the editor.
        if ($usehtmleditor) {
           use_html_editor("text");
        }

        print_footer($course);
        die;
    }

    if ($e) {
        //We are updating an entry, so we compare current session user with
        //existing entry user to avoid some potential problems if secureforms=off
        //Perhaps too much security? Anyway thanks to skodak (Bug 1823)
        $old = get_record('glossary_entries', 'id', $e);
        $ineditperiod = ((time() - $old->timecreated <  $CFG->maxeditingtime) || $glossary->editalways);
        if ( (!$ineditperiod  || $USER->id != $old->userid) and !has_capability('mod/glossary:manageentries', $context) and $e) {
            if ( $USER->id != $old->userid ) {
                error("You can't edit other people's entries!");
            } elseif (!$ineditperiod) {
                error("You can't edit this. Time expired!");
            }
            die;
        }

        $newentry->id = $e;

        $permissiongranted = 1;
        if ( !$glossary->allowduplicatedentries ) {
            if ($dupentries = get_records("glossary_entries","lower(concept)", moodle_strtolower($newentry->concept))) {
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
            if ($newfilename = glossary_add_attachment($newentry, 'attachment')) {
                $newentry->attachment = $newfilename;
            } else {
                unset($newentry->attachment);
            }

            if (update_record("glossary_entries", $newentry)) {
                add_to_log($course->id, "glossary", "update entry", 
                           "view.php?id=$cm->id&amp;mode=entry&amp;hook=$newentry->id", 
                           $newentry->id, $cm->id);
                $redirectmessage = get_string('entryupdated','glossary');
            } else {
                error("Could not update your glossary");
            }
        } else {
            error("Could not update this glossary entry because this concept already exist.");
        }
    } else {
        
        $newentry->userid = $USER->id;
        $newentry->timecreated = $timenow;
        $newentry->sourceglossaryid = 0;
        $newentry->teacherentry = has_capability('mod/glossary:manageentries', $context);

        $permissiongranted = 1;
        if ( !$glossary->allowduplicatedentries ) {
            if ($dupentries = get_record("glossary_entries","lower(concept)", moodle_strtolower($newentry->concept), "glossaryid", $glossary->id)) {
                $permissiongranted = 0;
            }
        }
        if ( $permissiongranted ) {
            if ($newentry->id = insert_record("glossary_entries", $newentry)) {
                $e = $newentry->id;
                $newentry->attachment = $_FILES["attachment"];
                if ($newfilename = glossary_add_attachment($newentry, 'attachment')) {
                    $newentry->attachment = $newfilename;
                } else {
                     unset($newentry->attachment);
                }
                set_field("glossary_entries", "attachment", $newfilename, "id", $newentry->id);
                add_to_log($course->id, "glossary", "add entry", 
                           "view.php?id=$cm->id&amp;mode=entry&amp;hook=$newentry->id", $newentry->id,$cm->id);
                $redirectmessage = get_string('entrysaved','glossary');
            } else {
                error("Could not insert this new entry");
            }
        } else {
            error("Could not insert this glossary entry because this concept already exist.");
        }
    }

    delete_records("glossary_entries_categories","entryid",$e);
    delete_records("glossary_alias","entryid",$e);

    if ( isset($form->categories) ) {
        $newcategory->entryid = $e;
        foreach ($form->categories as $category) {
            if ( $category > 0 ) {
                $newcategory->categoryid = $category;
                insert_record("glossary_entries_categories",$newcategory, false);
            } else {
                break;
            }
        }
    }
    if ( isset($form->aliases) ) {
        if ( $aliases = explode("\n",clean_text($form->aliases)) ) {
            foreach ($aliases as $alias) {
                $alias = trim($alias);
                if ($alias) {
                    unset($newalias);
                    $newalias->entryid = $e;
                    $newalias->alias = $alias;
                    insert_record("glossary_alias",$newalias, false);
                }
            }
        }
    }
    redirect("view.php?id=$cm->id&amp;mode=entry&amp;hook=$newentry->id", $redirectmessage);

} else {
    if ($e) {
        $form = get_record("glossary_entries", "id", $e);

        $newentry->id = $form->id;
        $newentry->concept = $form->concept;
        $newentry->definition = $form->definition;
        $newentry->format = $form->format;
        $newentry->timemodified = time();
        $newentry->approved = $glossary->defaultapproval or has_capability('mod/glossary:approve', $context);
        $newentry->usedynalink = $form->usedynalink;
        $newentry->casesensitive = $form->casesensitive;
        $newentry->fullmatch = $form->fullmatch;
        $newentry->aliases = "";
        $newentry->userid = $form->userid;
        $newentry->timecreated = $form->timecreated;


        if ( $aliases = get_records("glossary_alias","entryid",$e) ) {
            foreach ($aliases as $alias) {
                $newentry->aliases .= $alias->alias . "\n";
            }
        }
    }
}


//Fill and print the form.
//We check every field has a default values here!!
if (!isset($newentry->concept)) {
    $newentry->concept = "";
}
if (!isset($newentry->aliases)) {
    $newentry->aliases = "";
}
if (!isset($newentry->usedynalink)) {
    if (isset($CFG->glossary_linkentries)) {
        $newentry->usedynalink = $CFG->glossary_linkentries;
    } else {
        $newentry->usedynalink = 0;
    }
}
if (!isset($newentry->casesensitive)) {
    if (isset($CFG->glossary_casesensitive)) {
        $newentry->casesensitive = $CFG->glossary_casesensitive;
    } else {
        $newentry->casesensitive = 0;
    }
}
if (!isset($newentry->fullmatch)) {
    if (isset($CFG->glossary_fullmatch)) {
        $newentry->fullmatch = $CFG->glossary_fullmatch;
    } else {
        $newentry->fullmatch = 0;
    }
}
if (!isset($newentry->definition)) {
    $newentry->definition = "";
}
if (!isset($newentry->timecreated)) {
    $newentry->timecreated = 0;
}
if (!isset($newentry->userid)) {
    $newentry->userid = $USER->id;
}
$strglossary = get_string("modulename", "glossary");
$strglossaries = get_string("modulenameplural", "glossary");
$stredit = get_string("edit");

if ($usehtmleditor = can_use_richtext_editor()) {
    $defaultformat = FORMAT_HTML;
} else {
    $defaultformat = FORMAT_MOODLE;
}

print_header_simple(format_string($glossary->name), "",
             "<a href=\"index.php?id=$course->id\">$strglossaries</a> ->
              <a href=\"view.php?id=$cm->id\">".format_string($glossary->name,true)."</a> -> $stredit", "",
              "", true, "", navmenu($course, $cm));

$ineditperiod = ((time() - $newentry->timecreated <  $CFG->maxeditingtime) || $glossary->editalways);
if ( (!$ineditperiod  || $USER->id != $newentry->userid) and !has_capability('mod/glossary:manageentries', $context) and $e) {
    if ( $USER->id != $newentry->userid ) {
        error("You can't edit other people's entries!");
    } elseif (!$ineditperiod) {
        error("You can't edit this. Time expired!");
    }
    die;
}

    print_heading(format_string($glossary->name));

/// Info box

    if ( $glossary->intro ) {
        print_simple_box(format_text($glossary->intro), 'center', '70%', '', 5, 'generalbox', 'intro');
    }

    echo '<br />';

/// Tabbed browsing sections
$tab = GLOSSARY_ADDENTRY_VIEW;
include("tabs.html");

if (!$e) {
    require_capability('mod/glossary:write', $context);  
}

include("edit.html");

echo '</center>';

glossary_print_tabbed_table_end();

    // Lets give IE more time to load the whole page
    // before trying to load the editor.
    if ($usehtmleditor) {
       use_html_editor("text");
    }


print_footer($course);

?>
