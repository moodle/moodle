<?php // $Id$

require_once('../../config.php');
require_once('lib.php');
require_once('edit_form.php');

global $CFG, $USER;

$id = required_param('id', PARAM_INT);                // Course Module ID
$e  = optional_param('e', 0, PARAM_INT);              // EntryID
$confirm = optional_param('confirm',0, PARAM_INT);    // proceed. Edit the edtry

$mode = optional_param('mode', '', PARAM_ALPHA);      // categories if by category?
$hook = optional_param('hook', '', PARAM_ALPHANUM);   // CategoryID

if (! $cm = get_coursemodule_from_id('glossary', $id)) {
    print_error('invalidcoursemodule');
}

$context = get_context_instance(CONTEXT_MODULE, $cm->id);

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

require_login($course->id, false, $cm);

if ( isguest() ) {
    print_error('guestnoedit', 'glossary', $_SERVER["HTTP_REFERER"]);
}

if (! $glossary = $DB->get_record("glossary", array("id"=>$cm->instance))) {
    print_error('invalidid', 'glossary');
}


if ($e) { // if entry is specified
    if (!$entry  = $DB->get_record("glossary_entries", array("id"=>$e))) {
        print_error('invalidentry');
    }
    $ineditperiod = ((time() - $entry->timecreated <  $CFG->maxeditingtime) || $glossary->editalways);
    if (!has_capability('mod/glossary:manageentries', $context) and !($entry->userid == $USER->id and ($ineditperiod and has_capability('mod/glossary:write', $context)))) {
        //expired edit time is the most probable cause here
        print_error('erredittimeexpired', 'glossary', 'view.php?id=$cm->id&amp;mode=entry&amp;hook=$e');
    }
} else { // new entry
    require_capability('mod/glossary:write', $context);
}

$mform = new mod_glossary_entry_form(null, compact('cm', 'glossary', 'hook', 'mode', 'e', 'context'));
if ($mform->is_cancelled()){
    if ($e){
        redirect("view.php?id=$cm->id&amp;mode=entry&amp;hook=$e");
    } else {
        redirect("view.php?id=$cm->id");
    }

} elseif ($fromform = $mform->get_data(false)) {
    trusttext_after_edit($fromform->definition, $context);

    if ( !isset($fromform->usedynalink) ) {
        $fromform->usedynalink = 0;
    }
    if ( !isset($fromform->casesensitive) ) {
        $fromform->casesensitive = 0;
    }
    if ( !isset($fromform->fullmatch) ) {
        $fromform->fullmatch = 0;
    }
    $timenow = time();

    $todb = new object();
    $todb->course = $glossary->course;
    $todb->glossaryid = $glossary->id;

    $todb->concept = trim($fromform->concept);
    $todb->definition = $fromform->definition;
    $todb->format = $fromform->format;
    $todb->usedynalink = $fromform->usedynalink;
    $todb->casesensitive = $fromform->casesensitive;
    $todb->fullmatch = $fromform->fullmatch;
    $todb->timemodified = $timenow;
    $todb->approved = 0;
    $todb->aliases = "";
    if ( $glossary->defaultapproval or has_capability('mod/glossary:approve', $context) ) {
        $todb->approved = 1;
    }

    if ($e) {
        $todb->id = $e;
        $dir = glossary_file_area_name($todb);
        if ($mform->save_files($dir) and $newfilename = $mform->get_new_filename()) {
            $todb->attachment = $newfilename;
        }

        if ($DB->update_record('glossary_entries', $todb)) {
            add_to_log($course->id, "glossary", "update entry",
                       "view.php?id=$cm->id&amp;mode=entry&amp;hook=$todb->id",
                       $todb->id, $cm->id);
        } else {
            print_error('cantupdateglossary', 'glossary');
        }
    } else {

        $todb->userid = $USER->id;
        $todb->timecreated = $timenow;
        $todb->sourceglossaryid = 0;
        $todb->teacherentry = has_capability('mod/glossary:manageentries', $context);


        if ($todb->id = $DB->insert_record("glossary_entries", $todb)) {
            $e = $todb->id;
            $dir = glossary_file_area_name($todb);
            if ($mform->save_files($dir) and $newfilename = $mform->get_new_filename()) {
                $DB->set_field("glossary_entries", "attachment", $newfilename, array("id"=>$todb->id));
            }
            add_to_log($course->id, "glossary", "add entry",
                       "view.php?id=$cm->id&amp;mode=entry&amp;hook=$todb->id", $todb->id,$cm->id);
        } else {
            print_error('cantinsertent', 'glossary');
        }

    }

    $DB->delete_records("glossary_entries_categories", array("entryid"=>$e));
    $DB->delete_records("glossary_alias", array("entryid"=>$e));

    if (empty($fromform->notcategorised) && isset($fromform->categories)) {
        $newcategory->entryid = $e;
        foreach ($fromform->categories as $category) {
            if ( $category > 0 ) {
                $newcategory->categoryid = $category;
                $DB->insert_record("glossary_entries_categories", $newcategory, false);
            } else {
                break;
            }
        }
    }
    if ( isset($fromform->aliases) ) {
        if ( $aliases = explode("\n", $fromform->aliases) ) {
            foreach ($aliases as $alias) {
                $alias = trim($alias);
                if ($alias) {
                    $newalias = new object();
                    $newalias->entryid = $e;
                    $newalias->alias = $alias;
                    $DB->insert_record("glossary_alias", $newalias, false);
                }
            }
        }
    }
    redirect("view.php?id=$cm->id&amp;mode=entry&amp;hook=$todb->id");

} else {
    if ($e) {
        $fromdb = $DB->get_record("glossary_entries", array("id"=>$e));

        $toform = new object();

        if ($categoriesarr = $DB->get_records_menu("glossary_entries_categories", array("entryid"=>$e), '', 'id, categoryid')){
            $toform->categories = array_values($categoriesarr);
        } else {
            $toform->categories = array(0);
        }
        $toform->concept = $fromdb->concept;
        $toform->definition = $fromdb->definition;
        $toform->format = $fromdb->format;
        trusttext_prepare_edit($toform->definition, $toform->format, can_use_html_editor(), $context);
        $toform->approved = $glossary->defaultapproval or has_capability('mod/glossary:approve', $context);
        $toform->usedynalink = $fromdb->usedynalink;
        $toform->casesensitive = $fromdb->casesensitive;
        $toform->fullmatch = $fromdb->fullmatch;
        $toform->aliases = '';
        $ineditperiod = ((time() - $fromdb->timecreated <  $CFG->maxeditingtime) || $glossary->editalways);
        if ((!$ineditperiod  || $USER->id != $fromdb->userid) and !has_capability('mod/glossary:manageentries', $context)) {
            if ( $USER->id != $fromdb->userid ) {
                print_error('errcannoteditothers', 'glossary');
            } elseif (!$ineditperiod) {
                print_error('erredittimeexpired', 'glossary');
            }
            die;
        }

        if ( $aliases = $DB->get_records_menu("glossary_alias", array("entryid"=>$e), '', 'id, alias') ) {
            $toform->aliases = implode("\n", $aliases) . "\n";
        }
        $mform->set_data($toform);
    }
}

$stredit = empty($e) ? get_string('addentry', 'glossary') : get_string("edit");
$navigation = build_navigation($stredit, $cm);
print_header_simple(format_string($glossary->name), "", $navigation, "",
              "", true, "", navmenu($course, $cm));

print_heading(format_string($glossary->name));

/// Info box

///if ( $glossary->intro ) {
///    print_simple_box(format_text($glossary->intro), 'center', '70%', '', 5, 'generalbox', 'intro');
///}

/// Tabbed browsing sections
///$tab = GLOSSARY_ADDENTRY_VIEW;
///include("tabs.php");

if (!$e) {
    require_capability('mod/glossary:write', $context);
}

$mform->display();

///glossary_print_tabbed_table_end();


print_footer($course);

?>
