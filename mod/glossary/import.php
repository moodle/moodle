<?php

require_once("../../config.php");
require_once("lib.php");
require_once("$CFG->dirroot/course/lib.php");

$id = required_param('id', PARAM_INT);    // Course Module ID

$step     = optional_param('step', 0, PARAM_INT);
$dest     = optional_param('dest', 'current', PARAM_ALPHA);   // current | new
$file     = optional_param('file', '', PARAM_FILE);         // file to import
$catsincl = optional_param('catsincl', 0, PARAM_INT);         // Import Categories too?

$mode     = optional_param('mode', 'letter', PARAM_ALPHA );
$hook     = optional_param('hook', 'ALL', PARAM_ALPHANUM);
$file     = optional_param('file', 0, PARAM_INT); // xml file

$url = new moodle_url('/mod/glossary/import.php', array('id'=>$id));
if ($step !== 0) {
    $url->param('step', $step);
}
if ($dest !== 'current') {
    $url->param('dest', $dest);
}
if ($file !== '') {
    $url->param('file', $file);
}
if ($catsincl !== 0) {
    $url->param('catsincl', $catsincl);
}
if ($mode !== 'letter') {
    $url->param('mode', $mode);
}
if ($hook !== 'ALL') {
    $url->param('hook', $hook);
}
$PAGE->set_url($url);

if (! $cm = get_coursemodule_from_id('glossary', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

if (! $glossary = $DB->get_record("glossary", array("id"=>$cm->instance))) {
    print_error('invalidid', 'glossary');
}

require_login($course->id, false, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/glossary:import', $context);

if ($dest != 'new' and $dest != 'current') {
    $dest = 'current';
}
$strglossaries = get_string("modulenameplural", "glossary");
$strglossary = get_string("modulename", "glossary");
$strallcategories = get_string("allcategories", "glossary");
$straddentry = get_string("addentry", "glossary");
$strnoentries = get_string("noentries", "glossary");
$strsearchconcept = get_string("searchconcept", "glossary");
$strsearchindefinition = get_string("searchindefinition", "glossary");
$strsearch = get_string("search");
$strimportentries = get_string('importentriesfromxml', 'glossary');

$PAGE->set_url('/mod/glossary/import.php', array('id'=>$cm->id, 'mode'=>$mode, 'hook'=>$hook));
$PAGE->navbar->add($strimportentries);
$PAGE->set_title(format_string($glossary->name));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($strimportentries);

if ( !$step ) {
    // display upload form
    echo $OUTPUT->box_start('glossarydisplay generalbox');
    include("import.html");
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
}

require_sesskey();
$form = data_submitted();
$result = true;
if (empty($file)) {
    $result = false;
} else {
    $fs = get_file_storage();
    $usercontext = get_context_instance(CONTEXT_USER, $USER->id);
    $draftfiles = $fs->get_area_files($usercontext->id, 'user_draft', $file, 'id', false);
    if (count($draftfiles)<1) {
        $result = false;
    } else {
        $xmlfile = array_pop($draftfiles);
    }
}

if (!$result) {
    echo $OUTPUT->box_start('glossarydisplay generalbox');
    echo $OUTPUT->continue_button('import.php?id='.$id);
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    die();
}

if ($xml = glossary_read_imported_file($xmlfile->get_content())) {
    $xmlfile->delete();

    $importedentries = 0;
    $importedcats    = 0;
    $entriesrejected = 0;
    $rejections      = '';
    if ($dest == 'new') {
        // If the user chose to create a new glossary
        $xmlglossary = $xml['GLOSSARY']['#']['INFO'][0]['#'];

        if ( $xmlglossary['NAME'][0]['#'] ) {
            unset($glossary);
            $glossary->name = ($xmlglossary['NAME'][0]['#']);
            $glossary->course = $course->id;
            $glossary->globalglossary = ($xmlglossary['GLOBALGLOSSARY'][0]['#']);
            $glossary->intro = ($xmlglossary['INTRO'][0]['#']);
            $glossary->showspecial = ($xmlglossary['SHOWSPECIAL'][0]['#']);
            $glossary->showalphabet = ($xmlglossary['SHOWALPHABET'][0]['#']);
            $glossary->showall = ($xmlglossary['SHOWALL'][0]['#']);
            $glossary->timecreated = time();
            $glossary->timemodified = time();

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

            // Include new glossary and return the new ID
            if ( !$glossary->id = glossary_add_instance($glossary) ) {
                echo $OUTPUT->notification("Error while trying to create the new glossary.");
                echo '</center>';
                glossary_print_tabbed_table_end();
                echo $OUTPUT->footer();
                exit;
            } else {
                //The instance has been created, so lets do course_modules
                //and course_sections
                $mod->groupmode = $course->groupmode;  /// Default groupmode the same as course

                $mod->instance = $glossary->id;
                // course_modules and course_sections each contain a reference
                // to each other, so we have to update one of them twice.

                if (! $currmodule = $DB->get_record("modules", array("name"=>'glossary'))) {
                    print_error('modulenotexist', 'debug', '', 'Glossary');
                }
                $mod->module = $currmodule->id;
                $mod->course = $course->id;
                $mod->modulename = 'glossary';
                $mod->section = 0;

                if (! $mod->coursemodule = add_course_module($mod) ) {
                    print_error('cannotaddcoursemodule');
                }

                if (! $sectionid = add_mod_to_section($mod) ) {
                    print_error('cannotaddcoursemoduletosection');
                }
                //We get the section's visible field status
                $visible = $DB->get_field("course_sections", "visible", array("id"=>$sectionid));

                $DB->set_field("course_modules", "visible", $visible, array("id"=>$mod->coursemodule));
                $DB->set_field("course_modules", "section", $sectionid, array("id"=>$mod->coursemodule));

                add_to_log($course->id, "course", "add mod",
                           "../mod/$mod->modulename/view.php?id=$mod->coursemodule",
                           "$mod->modulename $mod->instance");
                add_to_log($course->id, $mod->modulename, "add",
                           "view.php?id=$mod->coursemodule",
                           "$mod->instance", $mod->coursemodule);

                rebuild_course_cache($course->id);

                echo $OUTPUT->box(get_string("newglossarycreated","glossary"),'generalbox boxaligncenter boxwidthnormal');
                echo '<p>';
            }
        } else {
            echo $OUTPUT->notification("Error while trying to create the new glossary.");
            echo $OUTPUT->footer();
            exit;
        }
    }

    $xmlentries = $xml['GLOSSARY']['#']['INFO'][0]['#']['ENTRIES'][0]['#']['ENTRY'];
    for($i = 0; $i < sizeof($xmlentries); $i++) {
        // Inserting the entries
        $xmlentry = $xmlentries[$i];
        unset($newentry);
        $newentry->concept = trim($xmlentry['#']['CONCEPT'][0]['#']);
        $newentry->definition = trusttext_strip($xmlentry['#']['DEFINITION'][0]['#']);
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
                    $dupentry = $DB->get_record("glossary_entries", array("concept"=>$newentry->concept, "glossaryid"=>$glossary->id));
                } else {
                    $dupentry = $DB->get_record("glossary_entries", array("lower(concept)"=>moodle_strtolower($newentry->concept), "glossaryid"=>$glossary->id));
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
            $newentry->format           = $xmlentry['#']['FORMAT'][0]['#'];
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

            if ( $newentry->id = $DB->insert_record("glossary_entries",$newentry) )  {
                $importedentries++;

                $xmlaliases = @$xmlentry['#']['ALIASES'][0]['#']['ALIAS']; // ignore missing ALIASES
                for($k = 0; $k < sizeof($xmlaliases); $k++) {
                /// Importing aliases
                    $xmlalias = $xmlaliases[$k];
                    $aliasname = $xmlalias['#']['NAME'][0]['#'];

                    if (!empty($aliasname)) {
                        $newalias = new object();
                        $newalias->entryid = $newentry->id;
                        $newalias->alias = trim($aliasname);
                        $newalias->id = $DB->insert_record("glossary_alias",$newalias);
                    }
                }

                if ( $catsincl ) {
                    // If the categories must be imported...
                    $xmlcats = @$xmlentry['#']['CATEGORIES'][0]['#']['CATEGORY']; // ignore missing CATEGORIES
                    for($k = 0; $k < sizeof($xmlcats); $k++) {
                        $xmlcat = $xmlcats[$k];

                        $newcat = new object();
                        $newcat->name = $xmlcat['#']['NAME'][0]['#'];
                        $newcat->usedynalink = $xmlcat['#']['USEDYNALINK'][0]['#'];
                        if ( !$category = $DB->get_record("glossary_categories", array("glossaryid"=>$glossary->id,"name"=>$newcat->name))) {
                            // Create the category if it does not exist
                            $category = new object();
                            $category->name = $newcat->name;
                            $category->glossaryid = $glossary->id;
                            $category->id = $DB->insert_record("glossary_categories",$category);
                            $importedcats++;
                        }
                        if ( $category ) {
                            // inserting the new relation
                            $entrycat = new object();
                            $entrycat->entryid    = $newentry->id;
                            $entrycat->categoryid = $category->id;
                            $DB->insert_record("glossary_entries_categories",$entrycat);
                        }
                    }
                }
            } else {
                $entriesrejected++;
                // add to exception report (can't insert new record)
                $rejections .= "<tr><td>$newentry->concept</td>" .
                               "<td>" . get_string("cantinsertrec","glossary"). "</td></tr>";
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
    if ( $catsincl ) {
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

