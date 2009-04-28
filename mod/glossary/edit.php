<?php // $Id$

require_once('../../config.php');
require_once('lib.php');
require_once('edit_form.php');

$cmid = required_param('cmid', PARAM_INT);            // Course Module ID
$id   = optional_param('id', 0, PARAM_INT);           // EntryID

if (!$cm = get_coursemodule_from_id('glossary', $cmid)) {
    print_error('invalidcoursemodule');
}

if (!$course = $DB->get_record('course', array('id'=>$cm->course))) {
    print_error('coursemisconf');
}

require_login($course, false, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);

if (isguestuser()) {
    print_error('guestnoedit', 'glossary', "$CFG->wwwroot/mod/glossary/view.php?id=$cmid");
}

if (!$glossary = $DB->get_record('glossary', array('id'=>$cm->instance))) {
    print_error('invalidid', 'glossary');
}

$mform = new mod_glossary_entry_form(null, compact('cm', 'glossary'));

if ($id) { // if entry is specified
    if (!$entry = $DB->get_record('glossary_entries', array('id'=>$id, 'glossaryid'=>$glossary->id))) {
        print_error('invalidentry');
    }

    $ineditperiod = ((time() - $entry->timecreated <  $CFG->maxeditingtime) || $glossary->editalways);
    if (!has_capability('mod/glossary:manageentries', $context) and !($entry->userid == $USER->id and ($ineditperiod and has_capability('mod/glossary:write', $context)))) {
        if ($USER->id != $fromdb->userid) {
            print_error('errcannoteditothers', 'glossary', "view.php?id=$cm->id&amp;mode=entry&amp;hook=$id");
        } elseif (!$ineditperiod) {
            print_error('erredittimeexpired', 'glossary', "view.php?id=$cm->id&amp;mode=entry&amp;hook=$id");
        }
    }

    // clean up text before edit if needed
    $entry = trusttext_pre_edit($entry, 'definition', $context);

    //prepare extra data
    if ($aliases = $DB->get_records_menu("glossary_alias", array("entryid"=>$id), '', 'id, alias')) {
        $entry->aliases = implode("\n", $aliases) . "\n";
    }
    if ($categoriesarr = $DB->get_records_menu("glossary_entries_categories", array('entryid'=>$id), '', 'id, categoryid')) {
        // TODO: this fetches cats from both main and secondary glossary :-(
        $entry->categories = array_values($categoriesarr);
    }

} else { // new entry
    require_capability('mod/glossary:write', $context);
    $entry = new object();
    $entry->id               = null;
    $entry->definition       = '';
    $entry->definitionformat = FORMAT_HTML; // TODO: better default value
}

$entry->cmid = $cm->id;

$draftid_editor = file_get_submitted_draft_itemid('entry');
$currenttext = file_prepare_draft_area($draftid_editor, $context->id, 'glossary_entry', $entry->id, true, $entry->definition);
$entry->entry = array('text'=>$currenttext, 'format'=>$entry->definitionformat, 'itemid'=>$draftid_editor);

$draftitemid = file_get_submitted_draft_itemid('attachments');
file_prepare_draft_area($draftitemid, $context->id, 'glossary_attachment', $entry->id , false);
$entry->attachments = $draftitemid;

// set form initial data
$mform->set_data($entry);


if ($mform->is_cancelled()){
    if ($id){
        redirect("view.php?id=$cm->id&amp;mode=entry&amp;hook=$id");
    } else {
        redirect("view.php?id=$cm->id");
    }

} else if ($data = $mform->get_data()) {
    $timenow = time();

    if (empty($entry->id)) {
        $entry->glossaryid       = $glossary->id;
        $entry->timecreated      = $timenow;
        $entry->userid           = $USER->id;
        $entry->timecreated      = $timenow;
        $entry->sourceglossaryid = 0;
        $entry->teacherentry     = has_capability('mod/glossary:manageentries', $context);
    }

    $entry->concept          = trim($data->concept);
    $entry->definition       = '';          // updated later
    $entry->definitionformat = FORMAT_HTML; // updated later
    $entry->definitiontrust  = trusttext_trusted($context);
    $entry->timemodified     = $timenow;
    $entry->approved         = 0;
    $entry->usedynalink      = isset($data->usedynalink) ?   $data->usedynalink : 0;
    $entry->casesensitive    = isset($data->casesensitive) ? $data->casesensitive : 0;
    $entry->fullmatch        = isset($data->fullmatch) ?     $data->fullmatch : 0;

    if ($glossary->defaultapproval or has_capability('mod/glossary:approve', $context)) {
        $entry->approved = 1;
    }

    if (empty($entry->id)) {
        //new entry
        $entry->id = $DB->insert_record('glossary_entries', $entry);
        add_to_log($course->id, "glossary", "add entry",
                   "view.php?id=$cm->id&amp;mode=entry&amp;hook=$entry->id", $entry->id, $cm->id);

    } else {
        //existing entry
        $DB->update_record('glossary_entries', $entry);
        add_to_log($course->id, "glossary", "update entry",
                   "view.php?id=$cm->id&amp;mode=entry&amp;hook=$entry->id",
                   $entry->id, $cm->id);
    }

    // save and relink embedded images
    $entry->definitionformat = $data->entry['format'];
    $entry->definition       = file_save_draft_area_files($draftid_editor, $context->id, 'glossary_entry', $entry->id, array('subdirs'=>true), $data->entry['text']);

    // save attachments
    $info = file_get_draft_area_info($draftitemid);
    $entry->attachment = ($info['filecount']>0) ? '1' : '';
    file_save_draft_area_files($draftitemid, $context->id, 'glossary_attachment', $entry->id);

    // store the final values
    $DB->update_record('glossary_entries', $entry);

    //refetch complete entry
    $entry = $DB->get_record('glossary_entries', array('id'=>$entry->id));

    // update entry categories
    $DB->delete_records('glossary_entries_categories', array('entryid'=>$entry->id));
    // TODO: this deletes cats from both both main and secondary glossary :-(
    if (!empty($data->categories) and array_search(0, $data->categories) === false) {
        foreach ($data->categories as $catid) {
            $newcategory = new object();
            $newcategory->entryid    = $entry->id;
            $newcategory->categoryid = $catid;
            $DB->insert_record('glossary_entries_categories', $newcategory, false);
        }
    }

    // update aliases
    $DB->delete_records('glossary_alias', array('entryid'=>$entry->id));
    $aliases = trim($data->aliases);
    if ($aliases !== '') {
        $aliases = explode("\n", $aliases);
        foreach ($aliases as $alias) {
            $alias = trim($alias);
            if ($alias !== '') {
                $newalias = new object();
                $newalias->entryid = $entry->id;
                $newalias->alias   = $alias;
                $DB->insert_record('glossary_alias', $newalias, false);
            }
        }
    }

    redirect("view.php?id=$cm->id&amp;mode=entry&amp;hook=$entry->id");
}

$stredit = empty($entry->id) ? get_string('addentry', 'glossary') : get_string('edit');

$navigation = build_navigation($stredit, $cm);
print_header_simple(format_string($glossary->name), "", $navigation, "",
              "", true, "", navmenu($course, $cm));

print_heading(format_string($glossary->name));

$mform->display();

print_footer($course);

?>
