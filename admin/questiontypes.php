<?PHP // $Id$
      // Allows the admin to manage questiontypes
      // This page is adapted from modules.php

      // This page is not yet in service, the whole plug-in architecture
      // for question types is still under construction.

    require_once('../config.php');

    $show    = optional_param('show', '', PARAM_SAFEDIR);
    $hide    = optional_param('hide', '', PARAM_SAFEDIR);
    $delete  = optional_param('delete', '', PARAM_SAFEDIR);
    $confirm = optional_param('confirm', '', PARAM_BOOL);

    require_login();

    if (!isadmin()) {
        error("Only administrators can use this page!");
    }

    if (!$site = get_site()) {
        error("Site isn't defined!");
    }


/// Print headings

    $stradministration = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strdelete = get_string("delete");
    $strversion = get_string("version");
    $strhide = get_string("hide");
    $strshow = get_string("show");
    $strsettings = get_string("settings");
    $strquestions = get_string("questions");
    $strquestiontype = get_string("questiontype", 'quiz');

    print_header("$site->shortname: $strquestions", "$site->fullname",
                 "<a href=\"index.php\">$stradministration</a> -> ".
                 "<a href=\"configure.php\">$strconfiguration</a> -> $strquestions");

    print_heading($strquestions);


/// If data submitted, then process and store.

    if (!empty($hide) and confirm_sesskey()) {
        if (!$qtype = get_record("question_types", "name", $hide)) {
            error("Question type doesn't exist!");
        }
        set_field("question_types", "visible", "0", "id", $qtype->id);            // Hide question type
    }

    if (!empty($show) and confirm_sesskey()) {
        if (!$qtype = get_record("question_types", "name", $show)) {
            error("Question type doesn't exist!");
        }
        set_field("question_types", "visible", "1", "id", $qtype->id);            // Show question type
    }

    if (!empty($delete) and confirm_sesskey()) {

        $strqtypename = get_string("qtypename", "qtype_$delete");

        if (!$confirm) {
            notice_yesno(get_string("qtypedeleteconfirm", "admin", $strqtypename),
                         "questiontypes.php?delete=$delete&amp;confirm=1&amp;sesskey=$USER->sesskey",
                         "questiontypes.php");
            print_footer();
            exit;

        } else {  // Delete everything!!

            if ($delete == "random") {
                error("You can not delete the random question type!!");
            }

            if (!$qtype = get_record("question_types", "name", $delete)) {
                error("Question type doesn't exist!");
            }

            // OK, first delete all the questions
            if ($questions = get_records("quiz_questions", "qtype", $qtype->id)) {
                foreach ($questions as $question) {
                    if (! quiz_delete_question($coursemod->id, $coursemod->section)) {
                        notify("Could not delete the $strqtypename with id = $question->id");
                    }
                }
            }

            // And the qtype entry itself
            if (!delete_records("question_types", "name", $qtype->name)) {
                notify("Error occurred while deleting the $strqtypename record from question_types table");
            }

            // Then the tables themselves

            if ($tables = $db->Metatables()) {
                $prefix = $CFG->prefix.$qtype->name;
                foreach ($tables as $table) {
                    if (strpos($table, $prefix) === 0) {
                        if (!execute_sql("DROP TABLE $table", false)) {
                            notify("ERROR: while trying to drop table $table");
                        }
                    }
                }
            }

            $a->qtype = $strqtypename;
            $a->directory = "$CFG->dirroot/qtype/$delete";
            notice(get_string("qtypedeletefiles", "", $a), "questiontypes.php");
        }
    }

/// Get and sort the existing questiontypes

    if (!$qtypes = get_records("question_types")) {
        error("No question types found!!");        // Should never happen
    }

    foreach ($qtypes as $qtype) {
        $strqtypename = get_string("qtypename", "qtype_$qtype->name");
        $qtypebyname[$strqtypename] = $qtype;
    }
    ksort($qtypebyname);

/// Print the table of all questiontypes

    $table->head  = array ($strquestiontype, $strquestions, $strversion, "$strhide/$strshow", $strdelete, $strsettings);
    $table->align = array ("left", "right", "left", "center", "center", "center");
    $table->wrap  = array ("nowrap", "", "", "", "","");
    $table->size  = array ("100%", "10", "10", "10", "10","12");
    $table->width = "100";

    foreach ($qtypebyname as $qtypename => $qtype) {

        $icon = "<img src=\"$CFG->dirroot/mod/quiz/questiontypes/$qtype->name/icon.gif\" hspace=\"10\" height=\"16\" width=\"16\" border=\"0\" alt=\"\" />";

        if (file_exists("$CFG->dirroot/mod/quiz/questiontypes/$qtype->name/config.html")) {
            $settings = "<a href=\"questiontype.php?qtype=$qtype->name\">$strsettings</a>";
        } else {
            $settings = "";
        }

        $count = count_records('quiz_questions', 'qtype', $qtype->id);

        $delete = $count ? '' : "<a href=\"questiontypes.php?delete=$qtype->name&amp;sesskey=$USER->sesskey\">$strdelete</a>";

        if ($qtype->visible) {
            $visible = "<a href=\"questiontypes.php?hide=$qtype->name&amp;sesskey=$USER->sesskey\" title=\"$strhide\">".
                       "<img src=\"$CFG->pixpath/i/hide.gif\" align=\"middle\" height=\"16\" width=\"16\" border=\"0\" alt=\"\" /></a>";
            $class = "";
        } else {
            $visible = "<a href=\"questiontypes.php?show=$qtype->name&amp;sesskey=$USER->sesskey\" title=\"$strshow\">".
                       "<img src=\"$CFG->pixpath/i/show.gif\" align=\"middle\" height=\"16\" width=\"16\" border=\"0\" alt=\"\" /></a>";
            $class = "class=\"dimmed_text\"";
        }
        if ($qtype->name == "random") {
            $delete = "";
            $visible = "";
            $class = "";
        }
        $table->data[] = array ("<span $class>$icon $qtypename</span>", $count, $qtype->version, $visible, $delete, $settings);
    }
    print_table($table);

    echo "<br /><br />";

    print_footer();

?>
