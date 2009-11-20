<?php   // $Id$

    require_once("../../config.php");
    require_once("lib.php");
    require_once("$CFG->dirroot/course/lib.php");
    global $CFG, $USER;

    $id = required_param('id', PARAM_INT);    // Course Module ID

    $step     = optional_param('step', 0, PARAM_INT);
    $dest     = optional_param('dest', 'current', PARAM_ALPHA);   // current | new
    $file     = optional_param('file', '', PARAM_FILE);         // file to import
    $catsincl = optional_param('catsincl', 0, PARAM_INT);         // Import Categories too?

    $mode     = optional_param('mode', 'letter', PARAM_ALPHA );
    $hook     = optional_param('hook', 'ALL', PARAM_ALPHANUM);

    if (! $cm = get_coursemodule_from_id('glossary', $id)) {
        error("Course Module ID was incorrect");
    }
    
    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    if (! $glossary = get_record("glossary", "id", $cm->instance)) {
        error("Course module is incorrect");
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

    $navigation = build_navigation($strimportentries, $cm);
    print_header_simple(format_string($glossary->name), "", $navigation,
        "", "", true, update_module_button($cm->id, $course->id, $strglossary),
        navmenu($course, $cm));

    print_heading($strimportentries);

    if ( !$step ) {
        print_box_start('glossarydisplay generalbox');
        include("import.html");
        print_box_end();

        print_footer($course);
        exit;
    }

    require_sesskey();
    $form = data_submitted();
    $file = $_FILES["file"];

    require_once($CFG->dirroot.'/lib/uploadlib.php');
    $um = new upload_manager('file',false,false,$course,false,0);

    if (!$um->preprocess_files()) {
        print_box_start('glossarydisplay generalbox');
        print_continue('import.php?id='.$id);
        print_box_end();

        print_footer();
        die();
    }

    if ($xml = glossary_read_imported_file($file['tmp_name']) ) {

        $importedentries = 0;
        $importedcats    = 0;
        $entriesrejected = 0;
        $rejections      = '';
        if ($dest == 'new') {
            // If the user chose to create a new glossary
            $xmlglossary = $xml['GLOSSARY']['#']['INFO'][0]['#'];

            if ( $xmlglossary['NAME'][0]['#'] ) {
                unset($glossary);
                $glossary->name = addslashes($xmlglossary['NAME'][0]['#']);
                $glossary->course = $course->id;
                $glossary->globalglossary = addslashes($xmlglossary['GLOBALGLOSSARY'][0]['#']);
                $glossary->intro = addslashes($xmlglossary['INTRO'][0]['#']);
                $glossary->showspecial = addslashes($xmlglossary['SHOWSPECIAL'][0]['#']);
                $glossary->showalphabet = addslashes($xmlglossary['SHOWALPHABET'][0]['#']);
                $glossary->showall = addslashes($xmlglossary['SHOWALL'][0]['#']);
                $glossary->timecreated = time();
                $glossary->timemodified = time();

                // Setting the default values if no values were passed
                if ( isset($xmlglossary['ENTBYPAGE'][0]['#']) ) {
                    $glossary->entbypage = addslashes($xmlglossary['ENTBYPAGE'][0]['#']);
                } else {
                    $glossary->entbypage = $CFG->glossary_entbypage;
                }
                if ( isset($xmlglossary['ALLOWDUPLICATEDENTRIES'][0]['#']) ) {
                    $glossary->allowduplicatedentries = addslashes($xmlglossary['ALLOWDUPLICATEDENTRIES'][0]['#']);
                } else {
                    $glossary->allowduplicatedentries = $CFG->glossary_dupentries;
                }
                if ( isset($xmlglossary['DISPLAYFORMAT'][0]['#']) ) {
                    $glossary->displayformat = addslashes($xmlglossary['DISPLAYFORMAT'][0]['#']);
                } else {
                    $glossary->displayformat = 2;
                }
                if ( isset($xmlglossary['ALLOWCOMMENTS'][0]['#']) ) {
                    $glossary->allowcomments = addslashes($xmlglossary['ALLOWCOMMENTS'][0]['#']);
                } else {
                    $glossary->allowcomments = $CFG->glossary_allowcomments;
                }
                if ( isset($xmlglossary['USEDYNALINK'][0]['#']) ) {
                    $glossary->usedynalink = addslashes($xmlglossary['USEDYNALINK'][0]['#']);
                } else {
                    $glossary->usedynalink = $CFG->glossary_linkentries;
                }
                if ( isset($xmlglossary['DEFAULTAPPROVAL'][0]['#']) ) {
                    $glossary->defaultapproval = addslashes($xmlglossary['DEFAULTAPPROVAL'][0]['#']);
                } else {
                    $glossary->defaultapproval = $CFG->glossary_defaultapproval;
                }

                // Include new glossary and return the new ID
                if ( !$glossary->id = glossary_add_instance($glossary) ) {
                    notify("Error while trying to create the new glossary.");
                    echo '</center>';
                    glossary_print_tabbed_table_end();
                    print_footer($course);
                    exit;
                } else {
                    //The instance has been created, so lets do course_modules
                    //and course_sections
                    $mod->groupmode = $course->groupmode;  /// Default groupmode the same as course

                    $mod->instance = $glossary->id;
                    // course_modules and course_sections each contain a reference
                    // to each other, so we have to update one of them twice.

                    if (! $currmodule = get_record("modules", "name", 'glossary')) {
                        error("Glossary module doesn't exist");
                    }
                    $mod->module = $currmodule->id;
                    $mod->course = $course->id;
                    $mod->modulename = 'glossary';
                    $mod->section = 0;

                    if (! $mod->coursemodule = add_course_module($mod) ) {
                        error("Could not add a new course module");
                    }

                    if (! $sectionid = add_mod_to_section($mod) ) {
                        error("Could not add the new course module to that section");
                    }
                    //We get the section's visible field status
                    $visible = get_field("course_sections","visible","id",$sectionid);

                    if (! set_field("course_modules", "visible", $visible, "id", $mod->coursemodule)) {
                        error("Could not update the course module with the correct visibility");
                    }

                    if (! set_field("course_modules", "section", $sectionid, "id", $mod->coursemodule)) {
                        error("Could not update the course module with the correct section");
                    }
                    add_to_log($course->id, "course", "add mod",
                               "../mod/$mod->modulename/view.php?id=$mod->coursemodule",
                               "$mod->modulename $mod->instance");
                    add_to_log($course->id, $mod->modulename, "add",
                               "view.php?id=$mod->coursemodule",
                               "$mod->instance", $mod->coursemodule);

                    rebuild_course_cache($course->id);

                    print_simple_box(get_string("newglossarycreated","glossary"),"center","70%");
                    echo '<p>';
                }
            } else {
                notify("Error while trying to create the new glossary.");
                print_footer($course);
                exit;
            }
        }

        $xmlentries = $xml['GLOSSARY']['#']['INFO'][0]['#']['ENTRIES'][0]['#']['ENTRY'];
        for($i = 0; $i < sizeof($xmlentries); $i++) {
            // Inserting the entries
            $xmlentry = $xmlentries[$i];
            unset($newentry);
            $newentry->concept = trim(addslashes($xmlentry['#']['CONCEPT'][0]['#']));
            $newentry->definition = trusttext_strip(addslashes($xmlentry['#']['DEFINITION'][0]['#']));
            if ( isset($xmlentry['#']['CASESENSITIVE'][0]['#']) ) {
                $newentry->casesensitive    = addslashes($xmlentry['#']['CASESENSITIVE'][0]['#']);
            } else {
                $newentry->casesensitive      = $CFG->glossary_casesensitive;
            }

            $permissiongranted = 1;
            if ( $newentry->concept and $newentry->definition ) {
                if ( !$glossary->allowduplicatedentries ) {
                    // checking if the entry is valid (checking if it is duplicated when should not be)
                    if ( $newentry->casesensitive ) {
                        $dupentry = get_record("glossary_entries","concept",$newentry->concept,"glossaryid",$glossary->id);
                    } else {
                        $dupentry = get_record("glossary_entries","lower(concept)",moodle_strtolower($newentry->concept),"glossaryid",$glossary->id);
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
                $newentry->format           = addslashes($xmlentry['#']['FORMAT'][0]['#']);
                $newentry->timecreated      = time();
                $newentry->timemodified     = time();

                // Setting the default values if no values were passed
                if ( isset($xmlentry['#']['USEDYNALINK'][0]['#']) ) {
                    $newentry->usedynalink      = addslashes($xmlentry['#']['USEDYNALINK'][0]['#']);
                } else {
                    $newentry->usedynalink      = $CFG->glossary_linkentries;
                }
                if ( isset($xmlentry['#']['FULLMATCH'][0]['#']) ) {
                    $newentry->fullmatch        = addslashes($xmlentry['#']['FULLMATCH'][0]['#']);
                } else {
                    $newentry->fullmatch      = $CFG->glossary_fullmatch;
                }

                if ( $newentry->id = insert_record("glossary_entries",$newentry) )  {
                    $importedentries++;

                    $xmlaliases = @$xmlentry['#']['ALIASES'][0]['#']['ALIAS']; // ignore missing ALIASES
                    for($k = 0; $k < sizeof($xmlaliases); $k++) {
                    /// Importing aliases
                        $xmlalias = $xmlaliases[$k];
                        $aliasname = $xmlalias['#']['NAME'][0]['#'];

                        if (!empty($aliasname)) {
                            unset($newalias);
                            $newalias->entryid = $newentry->id;
                            $newalias->alias = trim(addslashes($aliasname));
                            $newalias->id = insert_record("glossary_alias",$newalias);
                        }
                    }

                    if ( $catsincl ) {
                        // If the categories must be imported...
                        $xmlcats = @$xmlentry['#']['CATEGORIES'][0]['#']['CATEGORY']; // ignore missing CATEGORIES
                        for($k = 0; $k < sizeof($xmlcats); $k++) {
                            $xmlcat = $xmlcats[$k];
                            unset($newcat);

                            $newcat->name = addslashes($xmlcat['#']['NAME'][0]['#']);
                            $newcat->usedynalink = addslashes($xmlcat['#']['USEDYNALINK'][0]['#']);
                            if ( !$category = get_record("glossary_categories","glossaryid",$glossary->id,"name",$newcat->name) ) {
                                // Create the category if it does not exist
                                unset($category);
                                $category->name = $newcat->name;
                                $category->glossaryid = $glossary->id;
                                if ( !$category->id = insert_record("glossary_categories",$category)) {
                                    // add to exception report (can't insert category)
                                    $rejections .= "<tr><td>&nbsp;<strong>" . get_string("category","glossary") . ":</strong>$newcat->name</td>" .
                                                   "<td>" . get_string("cantinsertcat","glossary"). "</td></tr>";
                                } else {
                                    $importedcats++;
                                }
                            }
                            if ( $category ) {
                                // inserting the new relation
                                unset($entrycat);
                                $entrycat->entryid    = $newentry->id;
                                $entrycat->categoryid = $category->id;
                                if ( !insert_record("glossary_entries_categories",$entrycat) ) {
                                    // add to exception report (can't insert relation)
                                    $rejections .= "<tr><td>&nbsp;<strong>" . get_string("category","glossary") . ":</strong>$newcat->name</td>" .
                                                   "<td>" . get_string("cantinsertrel","glossary"). "</td></tr>";
                                }
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
        print_box_start('glossarydisplay generalbox');
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
            echo '<table class="glossaryimportexport">';
            echo '<tr><td align="center" colspan="2" width="100%"><strong>' . get_string("rejectionrpt","glossary") . '</strong></tr>';
            echo $rejections;
            echo '</table><hr />';
        }
    /// Print continue button, based on results
        if ($importedentries) {
            print_continue('view.php?id='.$id);
        } else {
            print_continue('import.php?id='.$id);
        }
        print_box_end();
    } else {
        notice(get_string('errorparsingxml', 'glossary'));
    }

/// Finish the page
    print_footer($course);

?>
