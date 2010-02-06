<?PHP

// This page prints a review of a particular quiz attempt
    require_once("../../config.php");
    require_once("lib.php");

    $id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
    $hp = optional_param('hp', 0, PARAM_INT); // hotpot ID
    $attempt = required_param('attempt', PARAM_INT); // A particular attempt ID for review

    if ($id) {
        $PAGE->set_url('/mod/hotpot/review.php', array('id'=>$id,'attempt'=>$attempt));
        if (! $cm = get_coursemodule_from_id('hotpot', $id)) {
            print_error('invalidcoursemodule');
        }
        if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
            print_error('coursemisconf');
        }
        if (! $hotpot = $DB->get_record("hotpot", array("id"=>$cm->instance))) {
            print_error('invalidcoursemodule');
        }
    } else {
        $PAGE->set_url('/mod/hotpot/review.php', array('hp'=>$hp,'attempt'=>$attempt));
        if (! $hotpot = $DB->get_record("hotpot", array("id"=>$hp))) {
            print_error('invalidcoursemodule');
        }
        if (! $course = $DB->get_record("course", array("id"=>$hotpot->course))) {
            print_error('coursemisconf');
        }
        if (! $cm = get_coursemodule_from_instance("hotpot", $hotpot->id, $course->id)) {
            print_error('invalidcoursemodule');
        }
    }
    if (! $attempt = $DB->get_record("hotpot_attempts", array("id"=>$attempt))) {
        print_error('invalidattemptid', 'hotpot');
    }

    require_login($course, true, $cm);

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if (!has_capability('mod/hotpot:viewreport',$context)) {
        if (!$hotpot->review) {
            print_error("noreview", "quiz");
        }
        //if (time() < $hotpot->timeclose) {
        //  print_error("noreviewuntil", "quiz", '', userdate($hotpot->timeclose));
        //}
        if ($attempt->userid != $USER->id) {
            print_error('notyourattempt', 'hotpot');
        }
    }
    add_to_log($course->id, "hotpot", "review", "review.php?id=$cm->id&attempt=$attempt->id", "$hotpot->id", "$cm->id");
// Print the page header
    $strmodulenameplural = get_string("modulenameplural", "hotpot");
    $strmodulename  = get_string("modulename", "hotpot");
    // print header

    $PAGE->requires->js('/lib/overlib/overlib.js', true);
    $PAGE->requires->js('/lib/overlib/overlib_cssstyle.js', true);
    $PAGE->set_title(format_string($course->shortname) . ": $hotpot->name");
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
    print '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib
    echo $OUTPUT->heading($hotpot->name);
    hotpot_print_attempt_summary($hotpot, $attempt);
    hotpot_print_review_buttons($course, $hotpot, $attempt, $context);
    $action = has_capability('mod/hotpot:viewreport',$context) ? optional_param('action', '', PARAM_ALPHA) : '';
    if ($action) {
        $xml = $DB->get_field('hotpot_details', 'details', array('attempt'=>$attempt->id));
        print '<hr>';
        switch ($action) {
            case 'showxmltree':
                print '<pre id="contents">';
                $xml_tree = new hotpot_xml_tree($xml, "['hpjsresult']['#']");
                print_r ($xml_tree->xml_value('fields'));
                print '</pre>';
                break;
            case 'showxmlsource':
                print htmlspecialchars($xml);
                break;
            default:
                print "Action '$action' not recognized";
        }
        print '<hr>';
    } else {
        hotpot_print_attempt_details($hotpot, $attempt);
    }
    hotpot_print_review_buttons($course, $hotpot, $attempt, $context);
    echo $OUTPUT->footer();
///////////////////////////
//    functions
///////////////////////////
function hotpot_print_attempt_summary(&$hotpot, &$attempt) {
    // start table
    global $OUTPUT;
    echo $OUTPUT->box_start("generalbox boxaligncenter boxwidthwide");
    print '<table width="100%" border="1" valign="top" align="center" cellpadding="2" cellspacing="2" class="generaltable">'."\n";
    // add attempt properties
    $fields = array('attempt', 'score', 'penalties', 'status', 'timetaken', 'timerecorded');
    foreach ($fields as $field) {
        switch ($field) {
            case 'score':
                $value = hotpot_format_score($attempt);
                break;
            case 'status':
                $value = hotpot_format_status($attempt);
                break;
            case 'timerecorded':
                $value = empty($attempt->timefinish) ? '-' : userdate($attempt->timefinish);
                break;
            case 'timetaken':
                $value = empty($attempt->timefinish) ? '-' : format_time($attempt->timefinish - $attempt->timestart);
                break;
            default:
                $value = isset($attempt->$field) ? $attempt->$field : NULL;
        }
        if (isset($value)) {
            switch ($field) {
                case 'status':
                case 'timerecorded':
                    $name = get_string('report'.$field, 'hotpot');
                    break;
                case 'penalties':
                    $name = get_string('penalties', 'hotpot');
                    break;
                default:
                    $name = get_string($field, 'quiz');
            }
            print '<tr><th align="right" width="100" class="generaltableheader" scope="row">'.$name.':</th><td class="generaltablecell">'.$value.'</td></tr>';
        }
    }
    // finish table
    print '</table>';
    echo $OUTPUT->box_end();
}
function hotpot_print_review_buttons(&$course, &$hotpot, &$attempt, $context) {
    global $DB, $OUTPUT;

    print "\n".'<table border="0" align="center" cellpadding="2" cellspacing="2" class="generaltable">';
    print "\n<tr>\n".'<td align="center">';
    echo $OUTPUT->single_button(new moodle_url("report.php", array('hp'=>$hotpot->id)), get_string('continue'));
    if (has_capability('mod/hotpot:viewreport',$context) && $DB->record_exists('hotpot_details', array('attempt'=>$attempt->id))) {
        print "</td>\n".'<td align="center">';
        echo $OUTPUT->single_button(new moodle_url("review.php", array('hp'=>$hotpot->id, 'attempt'=>$attempt->id, 'action'=>'showxmlsource')), get_string('showxmlsource', 'hotpot'));
        print "</td>\n".'<td align="center">';
        echo $OUTPUT->single_button(new moodle_url("review.php", array('hp'=>$hotpot->id,'attempt'=>$attempt->id, 'action'=>'showxmltree')), get_string('showxmltree', 'hotpot'));
        $colspan = 3;
    } else {
        $colspan = 1;
    }
    print "</td>\n</tr>\n";
    print '<tr><td colspan="'.$colspan.'">';
    echo $OUTPUT->spacer(array('height'=>4, 'width'=>1)); // should be done with CSS instead
    print "</td></tr>\n";
    print "</table>\n";
}
function hotpot_print_attempt_details(&$hotpot, &$attempt) {
    global $DB, $OUTPUT;

    // define fields to print
    $textfields = array('correct', 'ignored', 'wrong');
    $numfields = array('score', 'weighting', 'hints', 'clues', 'checks');
    $fields = array_merge($textfields, $numfields);
    $q = array(); // questions
    $f = array(); // fields
    foreach ($fields as $field) {
        $name = get_string($field, 'hotpot');
        $f[$field] = array('count'=>0, 'name'=>$name);
    }
    // get questions and responses for this attempt
    $questions = $DB->get_records('hotpot_questions', array('hotpot'=>$hotpot->id), 'id');
    $responses = $DB->get_records('hotpot_responses', array('attempt'=>$attempt->id), 'id');
    if ($questions && $responses) {
        foreach ($responses as $response) {
            $id = $response->question;
            foreach ($fields as $field) {
                if (!isset($f[$field])) {
                    $name = get_string($field, 'hotpot');
                    $f[$field] = array('count'=>0, 'name'=>$name);
                }
                if (isset($response->$field)) {
                    $f[$field]['count']++;
                    if (!isset($q[$id])) {
                        $name = hotpot_get_question_name($questions[$id]);
                        $q[$id] = array('name'=>$name);
                    }
                    $q[$id][$field] = $response->$field;
                }
            }
        }
    }
    // count the number of columns required in the table
    $colspan = 0;
    foreach ($numfields as $field) {
        if ($f[$field]['count']) {
            $colspan += 2;
        }
    }
    $colspan = max(2, $colspan);
    // start table of questions and responses
    echo $OUTPUT->box_start("generalbox boxaligncenter boxwidthwide");
    print '<table width="100%" border="1" valign="top" align="center" cellpadding="2" cellspacing="2" class="generaltable">'."\n";
    if (empty($q)) {
        print '<tr><td align="center" class="generaltablecell"><b>'.get_string("noresponses", "hotpot")."</b></td></tr>\n";
    } else {
        // flag to ensure separators are only printed before the 2nd and subsequent questions
        $printseparator = false;
        foreach ($q as $i=>$question) {
            // flag to ensure questions are only printed when there is at least one response
            $printedquestion = false;
            // add rows of text fields
            foreach ($textfields as $field) {
                if (isset($question[$field])) {
                    $text = hotpot_strings($question[$field]);
                    if (trim($text)) {
                        // print question if necessary
                        if (!$printedquestion) {
                            if ($printseparator) {
                                print '<tr><td colspan="'.$colspan.'"><div class="tabledivider"></div></td></tr>'."\n";
                            }
                            $printseparator = true;
                            print '<tr><td colspan="'.$colspan.'" class="generaltablecell"><b>'.$question['name'].'</b></td></tr>'."\n";
                            $printedquestion = true;
                        }
                        // print response
                        print '<tr><th align="right" width="100" class="generaltableheader" scope="row">'.$f[$field]['name'].':</th><td colspan="'.($colspan-1).'" class="generaltablecell">'.$text.'</td></tr>'."\n";
                    }
                }
            }
            // add row of numeric fields
            print '<tr>';
            foreach ($numfields as $field) {
                if ($f[$field]['count']) {
                    // print question if necessary
                    if (!$printedquestion) {
                        print '<td colspan="'.$colspan.'" class="generaltablecell"><b>'.$question['name']."</b></td></tr>\n<tr>";
                        $printedquestion = true;
                    }
                    // print numeric response
                    $value = isset($question[$field]) ? $question[$field] : '-';
                    print '<th align="right" width="100" class="generaltableheader" scope="row">'.$f[$field]['name'].':</th><td class="generaltablecell">'.$value.'</td>';
                }
            }
            print "</tr>\n";
        } // foreach $q
    }
    // finish table
    print "</table>\n";
    echo $OUTPUT->box_end();
}

