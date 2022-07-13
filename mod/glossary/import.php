<?php

require_once("../../config.php");
require_once("lib.php");
require_once("$CFG->dirroot/course/lib.php");
require_once("$CFG->dirroot/course/modlib.php");
require_once('import_form.php');

$id = required_param('id', PARAM_INT);    // Course Module ID

$mode     = optional_param('mode', 'letter', PARAM_ALPHA );
$hook     = optional_param('hook', 'ALL', PARAM_ALPHANUM);

$url = new moodle_url('/mod/glossary/import.php', array('id'=>$id));
if ($mode !== 'letter') {
    $url->param('mode', $mode);
}
if ($hook !== 'ALL') {
    $url->param('hook', $hook);
}
$PAGE->set_url($url);

if (! $cm = get_coursemodule_from_id('glossary', $id)) {
    throw new \moodle_exception('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    throw new \moodle_exception('coursemisconf');
}

if (! $glossary = $DB->get_record("glossary", array("id"=>$cm->instance))) {
    throw new \moodle_exception('invalidid', 'glossary');
}

require_login($course, false, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/glossary:import', $context);

$strglossaries = get_string("modulenameplural", "glossary");
$strglossary = get_string("modulename", "glossary");
$strallcategories = get_string("allcategories", "glossary");
$straddentry = get_string("addentry", "glossary");
$strnoentries = get_string("noentries", "glossary");
$strsearchindefinition = get_string("searchindefinition", "glossary");
$strsearch = get_string("search");
$strimportentries = get_string('importentriesfromxml', 'glossary');

$PAGE->navbar->add($strimportentries);
$PAGE->set_title($glossary->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_secondary_active_tab('modulepage');
$PAGE->activityheader->disable();

$form = new mod_glossary_import_form('');
if ($form->is_cancelled()) {
    redirect(new moodle_url('view.php', ['id' => $id]));
}

echo $OUTPUT->header();
echo $OUTPUT->heading($strimportentries);

if ( !$data = $form->get_data() ) {
    echo $OUTPUT->box_start('glossarydisplay generalbox');
    // display upload form
    $data = new stdClass();
    $data->id = $id;
    $form->set_data($data);
    $form->display();
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
}

$result = $form->get_file_content('file');

if (empty($result)) {
    echo $OUTPUT->box_start('glossarydisplay generalbox');
    echo $OUTPUT->continue_button('import.php?id='.$id);
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    die();
}

// Large exports are likely to take their time and memory.
core_php_time_limit::raise();
raise_memory_limit(MEMORY_EXTRA);

if ($xml = glossary_read_imported_file($result)) {
    $importedentries = 0;
    $importedcats    = 0;
    $entriesrejected = 0;
    $rejections      = '';
    $glossarycontext = $context;

    if ($data->dest == 'newglossary') {
        // If the user chose to create a new glossary
        $xmlglossary = $xml['GLOSSARY']['#']['INFO'][0]['#'];

        if ( $xmlglossary['NAME'][0]['#'] ) {
            $glossary = new stdClass();
            $glossary->modulename = 'glossary';
            $glossary->module = $cm->module;
            $glossary->name = ($xmlglossary['NAME'][0]['#']);
            $glossary->globalglossary = ($xmlglossary['GLOBALGLOSSARY'][0]['#']);
            $glossary->intro = ($xmlglossary['INTRO'][0]['#']);
            $glossary->introformat = isset($xmlglossary['INTROFORMAT'][0]['#']) ? $xmlglossary['INTROFORMAT'][0]['#'] : FORMAT_MOODLE;
            $glossary->showspecial = ($xmlglossary['SHOWSPECIAL'][0]['#']);
            $glossary->showalphabet = ($xmlglossary['SHOWALPHABET'][0]['#']);
            $glossary->showall = ($xmlglossary['SHOWALL'][0]['#']);
            $glossary->cmidnumber = null;

            // Setting the default values if no values were passed
            if ( isset($xmlglossary['ENTBYPAGE'][0]['#']) ) {
                $glossary->entbypage = ($xmlglossary['ENTBYPAGE'][0]['#']);
            } else {
                $glossary->entbypage = $CFG->glossary_entbypage;
            }
            if ( isset($xmlglossary['ALLOWDUPLICATEDENTRIES'][0]['#']) ) {
                $glossary->allowduplicatedentries = ($xmlglossary['ALLOWDUPLICATEDENTRIES'][0]['#']);
            } else {
                $glossary->allowduplicatedentries = $CFG->glossary_dupentries;
            }
            if ( isset($xmlglossary['DISPLAYFORMAT'][0]['#']) ) {
                $glossary->displayformat = ($xmlglossary['DISPLAYFORMAT'][0]['#']);
            } else {
                $glossary->displayformat = 2;
            }
            if ( isset($xmlglossary['ALLOWCOMMENTS'][0]['#']) ) {
                $glossary->allowcomments = ($xmlglossary['ALLOWCOMMENTS'][0]['#']);
            } else {
                $glossary->allowcomments = $CFG->glossary_allowcomments;
            }
            if ( isset($xmlglossary['USEDYNALINK'][0]['#']) ) {
                $glossary->usedynalink = ($xmlglossary['USEDYNALINK'][0]['#']);
            } else {
                $glossary->usedynalink = $CFG->glossary_linkentries;
            }
            if ( isset($xmlglossary['DEFAULTAPPROVAL'][0]['#']) ) {
                $glossary->defaultapproval = ($xmlglossary['DEFAULTAPPROVAL'][0]['#']);
            } else {
                $glossary->defaultapproval = $CFG->glossary_defaultapproval;
            }

            // These fields were not included in export, assume zero.
            $glossary->assessed = 0;
            $glossary->availability = null;

            // Check if we're creating the new glossary on the front page or inside a course.
            if ($cm->course == SITEID) {
                // On the front page, activities are in section 1.
                $glossary->section = 1;
            } else {
                // Inside a course, add to the general section (0).
                $glossary->section = 0;
            }
            // New glossary is always visible.
            $glossary->visible = 1;
            $glossary->visibleoncoursepage = 1;

            // Include new glossary and return the new ID
            if ( !($glossary = add_moduleinfo($glossary, $course)) ) {
                echo $OUTPUT->notification("Error while trying to create the new glossary.");
                glossary_print_tabbed_table_end();
                echo $OUTPUT->footer();
                exit;
            } else {
                $glossarycontext = context_module::instance($glossary->coursemodule);
                glossary_xml_import_files($xmlglossary, 'INTROFILES', $glossarycontext->id, 'intro', 0);
                echo $OUTPUT->box(get_string("newglossarycreated","glossary"),'generalbox boxaligncenter boxwidthnormal');
            }
        } else {
            echo $OUTPUT->notification("Error while trying to create the new glossary.");
            echo $OUTPUT->footer();
            exit;
        }
    }

    $xmlentries = $xml['GLOSSARY']['#']['INFO'][0]['#']['ENTRIES'][0]['#']['ENTRY'];
    $sizeofxmlentries = is_array($xmlentries) ? count($xmlentries) : 0;
    for($i = 0; $i < $sizeofxmlentries; $i++) {
        // Inserting the entries
        $xmlentry = $xmlentries[$i];
        $newentry = new stdClass();
        $newentry->concept = trim($xmlentry['#']['CONCEPT'][0]['#']);
        $definition = $xmlentry['#']['DEFINITION'][0]['#'];
        if (!is_string($definition)) {
            throw new \moodle_exception('errorparsingxml', 'glossary');
        }
        $newentry->definition = trusttext_strip($definition);
        if ( isset($xmlentry['#']['CASESENSITIVE'][0]['#']) ) {
            $newentry->casesensitive = $xmlentry['#']['CASESENSITIVE'][0]['#'];
        } else {
            $newentry->casesensitive = $CFG->glossary_casesensitive;
        }

        $permissiongranted = 1;
        if ( $newentry->concept and $newentry->definition ) {
            if ( !$glossary->allowduplicatedentries ) {
                // checking if the entry is valid (checking if it is duplicated when should not be)
                if ( $newentry->casesensitive ) {
                    $dupentry = $DB->record_exists_select('glossary_entries',
                                    'glossaryid = :glossaryid AND concept = :concept', array(
                                        'glossaryid' => $glossary->id,
                                        'concept'    => $newentry->concept));
                } else {
                    $dupentry = $DB->record_exists_select('glossary_entries',
                                    'glossaryid = :glossaryid AND LOWER(concept) = :concept', array(
                                        'glossaryid' => $glossary->id,
                                        'concept'    => core_text::strtolower($newentry->concept)));
                }
                if ($dupentry) {
                    $permissiongranted = 0;
                }
            }
        } else {
            $permissiongranted = 0;
        }
        if ($permissiongranted) {
            $newentry->glossaryid       = $glossary->id;
            $newentry->sourceglossaryid = 0;
            $newentry->approved         = 1;
            $newentry->userid           = $USER->id;
            $newentry->teacherentry     = 1;
            $newentry->definitionformat = $xmlentry['#']['FORMAT'][0]['#'];
            $newentry->timecreated      = time();
            $newentry->timemodified     = time();

            // Setting the default values if no values were passed
            if ( isset($xmlentry['#']['USEDYNALINK'][0]['#']) ) {
                $newentry->usedynalink      = $xmlentry['#']['USEDYNALINK'][0]['#'];
            } else {
                $newentry->usedynalink      = $CFG->glossary_linkentries;
            }
            if ( isset($xmlentry['#']['FULLMATCH'][0]['#']) ) {
                $newentry->fullmatch        = $xmlentry['#']['FULLMATCH'][0]['#'];
            } else {
                $newentry->fullmatch      = $CFG->glossary_fullmatch;
            }

            $newentry->id = $DB->insert_record("glossary_entries",$newentry);
            $importedentries++;

            $xmlaliases = @$xmlentry['#']['ALIASES'][0]['#']['ALIAS']; // ignore missing ALIASES
            $sizeofxmlaliases = is_array($xmlaliases) ? count($xmlaliases) : 0;
            for($k = 0; $k < $sizeofxmlaliases; $k++) {
            /// Importing aliases
                $xmlalias = $xmlaliases[$k];
                $aliasname = $xmlalias['#']['NAME'][0]['#'];

                if (!empty($aliasname)) {
                    $newalias = new stdClass();
                    $newalias->entryid = $newentry->id;
                    $newalias->alias = trim($aliasname);
                    $newalias->id = $DB->insert_record("glossary_alias",$newalias);
                }
            }

            if (!empty($data->catsincl)) {
                // If the categories must be imported...
                $xmlcats = @$xmlentry['#']['CATEGORIES'][0]['#']['CATEGORY']; // ignore missing CATEGORIES
                $sizeofxmlcats = is_array($xmlcats) ? count($xmlcats) : 0;
                for($k = 0; $k < $sizeofxmlcats; $k++) {
                    $xmlcat = $xmlcats[$k];

                    $newcat = new stdClass();
                    $newcat->name = $xmlcat['#']['NAME'][0]['#'];
                    $newcat->usedynalink = $xmlcat['#']['USEDYNALINK'][0]['#'];
                    if ( !$category = $DB->get_record("glossary_categories", array("glossaryid"=>$glossary->id,"name"=>$newcat->name))) {
                        // Create the category if it does not exist
                        $category = new stdClass();
                        $category->name = $newcat->name;
                        $category->glossaryid = $glossary->id;
                        $category->id = $DB->insert_record("glossary_categories",$category);
                        $importedcats++;
                    }
                    if ( $category ) {
                        // inserting the new relation
                        $entrycat = new stdClass();
                        $entrycat->entryid    = $newentry->id;
                        $entrycat->categoryid = $category->id;
                        $DB->insert_record("glossary_entries_categories",$entrycat);
                    }
                }
            }

            // Import files embedded in the entry text.
            glossary_xml_import_files($xmlentry['#'], 'ENTRYFILES', $glossarycontext->id, 'entry', $newentry->id);

            // Import files attached to the entry.
            if (glossary_xml_import_files($xmlentry['#'], 'ATTACHMENTFILES', $glossarycontext->id, 'attachment', $newentry->id)) {
                $DB->update_record("glossary_entries", array('id' => $newentry->id, 'attachment' => '1'));
            }

            // Import tags associated with the entry.
            if (core_tag_tag::is_enabled('mod_glossary', 'glossary_entries')) {
                $xmltags = @$xmlentry['#']['TAGS'][0]['#']['TAG']; // Ignore missing TAGS.
                $sizeofxmltags = is_array($xmltags) ? count($xmltags) : 0;
                for ($k = 0; $k < $sizeofxmltags; $k++) {
                    // Importing tags.
                    $tag = $xmltags[$k]['#'];
                    if (!empty($tag)) {
                        core_tag_tag::add_item_tag('mod_glossary', 'glossary_entries', $newentry->id, $glossarycontext, $tag);
                    }
                }
            }

        } else {
            $entriesrejected++;
            if ( $newentry->concept and $newentry->definition ) {
                // add to exception report (duplicated entry))
                $rejections .= "<tr><td>$newentry->concept</td>" .
                               "<td>" . get_string("duplicateentry","glossary"). "</td></tr>";
            } else {
                // add to exception report (no concept or definition found))
                $rejections .= "<tr><td>---</td>" .
                               "<td>" . get_string("noconceptfound","glossary"). "</td></tr>";
            }
        }
    }

    // Reset caches.
    \mod_glossary\local\concept_cache::reset_glossary($glossary);

    // processed entries
    echo $OUTPUT->box_start('glossarydisplay generalbox');
    echo '<table class="glossaryimportexport">';
    echo '<tr>';
    echo '<td width="50%" align="right">';
    echo get_string("totalentries","glossary");
    echo ':</td>';
    echo '<td width="50%" align="left">';
    echo $importedentries + $entriesrejected;
    echo '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td width="50%" align="right">';
    echo get_string("importedentries","glossary");
    echo ':</td>';
    echo '<td width="50%" align="left">';
    echo $importedentries;
    if ( $entriesrejected ) {
        echo ' <small>(' . get_string("rejectedentries","glossary") . ": $entriesrejected)</small>";
    }
    echo '</td>';
    echo '</tr>';
    if (!empty($data->catsincl)) {
        echo '<tr>';
        echo '<td width="50%" align="right">';
        echo get_string("importedcategories","glossary");
        echo ':</td>';
        echo '<td width="50%">';
        echo $importedcats;
        echo '</td>';
        echo '</tr>';
    }
    echo '</table><hr />';

    // rejected entries
    if ($rejections) {
        echo $OUTPUT->heading(get_string("rejectionrpt","glossary"), 4);
        echo '<table class="glossaryimportexport">';
        echo $rejections;
        echo '</table><hr />';
    }
/// Print continue button, based on results
    if ($importedentries) {
        echo $OUTPUT->continue_button('view.php?id='.$id);
    } else {
        echo $OUTPUT->continue_button('import.php?id='.$id);
    }
    echo $OUTPUT->box_end();
} else {
    echo $OUTPUT->box_start('glossarydisplay generalbox');
    echo get_string('errorparsingxml', 'glossary');
    echo $OUTPUT->continue_button('import.php?id='.$id);
    echo $OUTPUT->box_end();
}

/// Finish the page
echo $OUTPUT->footer();
