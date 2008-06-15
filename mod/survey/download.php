<?php // $Id$

    require ("../../config.php");

// Check that all the parameters have been provided.

    $id    = required_param('id', PARAM_INT);    // Course Module ID
    $type  = optional_param('type', 'xls', PARAM_ALPHA);
    $group = optional_param('group', 0, PARAM_INT);

    if (! $cm = $DB->get_record("course_modules", array("id"=>$id))) {
        print_error('invalidcoursemodule');
    }

    if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
        print_error('coursemisconf');
    }

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    require_login($course->id, false, $cm);
    require_capability('mod/survey:download', $context) ;

    if (! $survey = $DB->get_record("survey", array("id"=>$cm->instance))) {
        print_error('invalidsurveyid', 'survey');
    }

    add_to_log($course->id, "survey", "download", "download.php?id=$cm->id&amp;type=$type", "$survey->id", $cm->id);

/// Check to see if groups are being used in this survey

    $groupmode = groups_get_activity_groupmode($cm);   // Groups are being used

    if ($groupmode and $group) {
        $users = get_users_by_capability($context, 'mod/survey:participate', '', '', '', '', $group, null, false);
    } else {
        $users = get_users_by_capability($context, 'mod/survey:participate', '', '', '', '', '', null, false);
        $group = false;
    }

// Get all the questions and their proper order

    $questions = $DB->get_records_list("survey_questions", "id", explode(',', $survey->questions));
    $order = explode(",", $survey->questions);

    $virtualscales = false;
    foreach ($order as $key => $qid) {  // Do we have virtual scales?
        $question = $questions[$qid];
        if ($question->type < 0) {
            $virtualscales = true;
            break;
        }
    }

    $fullorderlist = array();
    foreach ($order as $key => $qid) {    // build up list of actual questions
        $question = $questions[$qid];

        if ($question->multi) {
            $addlist = $question->multi;
        } else {
            $addlist = $qid;
        }

        if ($virtualscales && ($question->type < 0)) {        // only use them
            $fullorderlist[] = $addlist;

        } else if (!$virtualscales && ($question->type >= 0)){   // ignore them
            $fullorderlist[] = $addlist;
        }
    }

    $fullquestions = $DB->get_records_list("survey_questions", "id", $fullorderlist);

//  Question type of multi-questions overrides the type of single questions
    foreach ($order as $key => $qid) {
        $question = $questions[$qid];

        if ($question->multi) {
            $subs = explode(",", $question->multi);
            while (list ($skey, $sqid) = each ($subs)) {
                $fullquestions["$sqid"]->type = $question->type;
            }
        }
    }

    $questions = $fullquestions;

//  Translate all the question texts

    foreach ($questions as $key => $question) {
        $questions[$key]->text = get_string($question->text, "survey");
    }


// Get and collate all the results in one big array

    if (! $aaa = $DB->get_records("survey_answers", array("survey"=>$survey->id), "time ASC")) {
        print_error('cannotfindanswer', 'survey');
    }

    foreach ($aaa as $a) {
        if (!$group or isset($users[$a->userid])) {
            if (empty($results[$a->userid])) { // init new array
                $results[$a->userid]["time"] = $a->time;
                foreach ($fullorderlist as $qid) {
                    $results[$a->userid][$qid]["answer1"] = "";
                    $results[$a->userid][$qid]["answer2"] = "";
                }
            }
            $results[$a->userid][$a->question]["answer1"] = $a->answer1;
            $results[$a->userid][$a->question]["answer2"] = $a->answer2;
        }
    }

// Output the file as a valid ODS spreadsheet if required

    if ($type == "ods") {
        require_once("$CFG->libdir/odslib.class.php");

    /// Calculate file name
        $downloadfilename = clean_filename("$course->shortname ".strip_tags(format_string($survey->name,true))).'.ods';
    /// Creating a workbook
        $workbook = new MoodleODSWorkbook("-");
    /// Sending HTTP headers
        $workbook->send($downloadfilename);
    /// Creating the first worksheet
        $myxls =& $workbook->add_worksheet(substr(strip_tags(format_string($survey->name,true)), 0, 31));

        $header = array("surveyid","surveyname","userid","firstname","lastname","email","idnumber","time", "notes");
        $col=0;
        foreach ($header as $item) {
            $myxls->write_string(0,$col++,$item);
        }
        foreach ($order as $key => $qid) {
            $question = $questions[$qid];
            if ($question->type == "0" || $question->type == "1" || $question->type == "3" || $question->type == "-1")  {
                $myxls->write_string(0,$col++,"$question->text");
            }
            if ($question->type == "2" || $question->type == "3")  {
                $myxls->write_string(0,$col++,"$question->text (preferred)");
            }
        }

//      $date = $workbook->addformat();
//      $date->set_num_format('mmmm-d-yyyy h:mm:ss AM/PM'); // ?? adjust the settings to reflect the PHP format below

        $row = 0;
        foreach ($results as $user => $rest) {
            $col = 0;
            $row++;
            if (! $u = $DB->get_record("user", array("id"=>$user))) {
                print_error('invaliduserid');
            }
            if ($n = $DB->get_record("survey_analysis", array("survey"=>$survey->id, "userid"=>$user))) {
                $notes = $n->notes;
            } else {
                $notes = "No notes made";
            }
            $myxls->write_string($row,$col++,$survey->id);
            $myxls->write_string($row,$col++,strip_tags(format_text($survey->name,true)));
            $myxls->write_string($row,$col++,$user);
            $myxls->write_string($row,$col++,$u->firstname);
            $myxls->write_string($row,$col++,$u->lastname);
            $myxls->write_string($row,$col++,$u->email);
            $myxls->write_string($row,$col++,$u->idnumber);
            $myxls->write_string($row,$col++, userdate($results[$user]["time"], "%d-%b-%Y %I:%M:%S %p") );
//          $myxls->write_number($row,$col++,$results[$user]["time"],$date);
            $myxls->write_string($row,$col++,$notes);

            foreach ($order as $key => $qid) {
                $question = $questions[$qid];
                if ($question->type == "0" || $question->type == "1" || $question->type == "3" || $question->type == "-1")  {
                    $myxls->write_string($row,$col++, $results[$user][$qid]["answer1"] );
                }
                if ($question->type == "2" || $question->type == "3")  {
                    $myxls->write_string($row, $col++, $results[$user][$qid]["answer2"] );
                }
            }
        }
        $workbook->close();

        exit;
    }

// Output the file as a valid Excel spreadsheet if required

    if ($type == "xls") {
        require_once("$CFG->libdir/excellib.class.php");

    /// Calculate file name
        $downloadfilename = clean_filename("$course->shortname ".strip_tags(format_string($survey->name,true))).'.xls';
    /// Creating a workbook
        $workbook = new MoodleExcelWorkbook("-");
    /// Sending HTTP headers
        $workbook->send($downloadfilename);
    /// Creating the first worksheet
        $myxls =& $workbook->add_worksheet(substr(strip_tags(format_string($survey->name,true)), 0, 31));

        $header = array("surveyid","surveyname","userid","firstname","lastname","email","idnumber","time", "notes");
        $col=0;
        foreach ($header as $item) {
            $myxls->write_string(0,$col++,$item);
        }
        foreach ($order as $key => $qid) {
            $question = $questions[$qid];
            if ($question->type == "0" || $question->type == "1" || $question->type == "3" || $question->type == "-1")  {
                $myxls->write_string(0,$col++,"$question->text");
            }
            if ($question->type == "2" || $question->type == "3")  {
                $myxls->write_string(0,$col++,"$question->text (preferred)");
            }
        }

//      $date = $workbook->addformat();
//      $date->set_num_format('mmmm-d-yyyy h:mm:ss AM/PM'); // ?? adjust the settings to reflect the PHP format below

        $row = 0;
        foreach ($results as $user => $rest) {
            $col = 0;
            $row++;
            if (! $u = $DB->get_record("user", array("id"=>$user))) {
                print_error('invaliduserid');
            }
            if ($n = $DB->get_record("survey_analysis", array("survey"=>$survey->id, "userid"=>$user))) {
                $notes = $n->notes;
            } else {
                $notes = "No notes made";
            }
            $myxls->write_string($row,$col++,$survey->id);
            $myxls->write_string($row,$col++,strip_tags(format_text($survey->name,true)));
            $myxls->write_string($row,$col++,$user);
            $myxls->write_string($row,$col++,$u->firstname);
            $myxls->write_string($row,$col++,$u->lastname);
            $myxls->write_string($row,$col++,$u->email);
            $myxls->write_string($row,$col++,$u->idnumber);
            $myxls->write_string($row,$col++, userdate($results[$user]["time"], "%d-%b-%Y %I:%M:%S %p") );
//          $myxls->write_number($row,$col++,$results[$user]["time"],$date);
            $myxls->write_string($row,$col++,$notes);

            foreach ($order as $key => $qid) {
                $question = $questions[$qid];
                if ($question->type == "0" || $question->type == "1" || $question->type == "3" || $question->type == "-1")  {
                    $myxls->write_string($row,$col++, $results[$user][$qid]["answer1"] );
                }
                if ($question->type == "2" || $question->type == "3")  {
                    $myxls->write_string($row, $col++, $results[$user][$qid]["answer2"] );
                }
            }
        }
        $workbook->close();

        exit;
    }

// Otherwise, return the text file.

// Print header to force download

    header("Content-Type: application/download\n");

    $downloadfilename = clean_filename("$course->shortname ".strip_tags(format_string($survey->name,true)));
    header("Content-Disposition: attachment; filename=\"$downloadfilename.txt\"");

// Print names of all the fields

    echo "surveyid    surveyname    userid    firstname    lastname    email    idnumber    time    ";
    foreach ($order as $key => $qid) {
        $question = $questions[$qid];
        if ($question->type == "0" || $question->type == "1" || $question->type == "3" || $question->type == "-1")  {
            echo "$question->text    ";
        }
        if ($question->type == "2" || $question->type == "3")  {
             echo "$question->text (preferred)    ";
        }
    }
    echo "\n";

// Print all the lines of data.

    foreach ($results as $user => $rest) {
        if (! $u = $DB->get_record("user", array("id"=>$user))) {
            print_error('invaliduserid');
        }
        echo $survey->id."\t";
        echo strip_tags(format_string($survey->name,true))."\t";
        echo $user."\t";
        echo $u->firstname."\t";
        echo $u->lastname."\t";
        echo $u->email."\t";
        echo $u->idnumber."\t";
        echo userdate($results[$user]["time"], "%d-%b-%Y %I:%M:%S %p")."\t";

        foreach ($order as $key => $qid) {
            $question = $questions[$qid];
            if ($question->type == "0" || $question->type == "1" || $question->type == "3" || $question->type == "-1")  {
                echo $results[$user][$qid]["answer1"]."    ";
            }
            if ($question->type == "2" || $question->type == "3")  {
                echo $results[$user][$qid]["answer2"]."    ";
            }
        }
        echo "\n";
    }
    exit;


?>
