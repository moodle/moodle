<?PHP // $Id$

    require ("../../config.php");

// Check that all the parameters have been provided.

    require_variable($id);    // Course Module ID
    optional_variable($type, "xls");

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("Sorry, only teachers can see this.");
    }

    if (! $survey = get_record("survey", "id", $cm->instance)) {
        error("Survey ID was incorrect");
    }

    add_to_log("Downloaded report as $type:  $survey->name", $course->id);


// Get all the questions and their proper order

    $questions = get_records_sql("SELECT * FROM survey_questions WHERE id in ($survey->questions)");
    $order = explode(",", $survey->questions);

    foreach ($order as $key => $qid) {  // Do we have virtual scales?
        $question = $questions[$qid];
        if ($question->type < 0) { 
            $virtualscales = true;
            break;
        }
    }

    $fullorderlist = "";
    foreach ($order as $key => $qid) {    // build up list of actual questions
        $question = $questions[$qid];

        if (!(empty($fullorderlist))) {
            $fullorderlist .= ",";
        }

        if ($question->multi) {
            $addlist = $question->multi;
        } else {
            $addlist = $qid;
        }
        
        if ($virtualscales && ($question->type < 0)) {        // only use them
            $fullorderlist .= $addlist;

        } else if (!$virtualscales && ($question->type >= 0)){   // ignore them
            $fullorderlist .= $addlist;
        }
    }

    $fullquestions = get_records_sql("SELECT * FROM survey_questions WHERE id in ($fullorderlist)");

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

    $order     = explode(",", $fullorderlist);
    $questions = $fullquestions;


// Get and collate all the results in one big array

    if (! $aaa = get_records("survey_answers", "survey", "$survey->id", "time ASC")) {
        error("There are no answers for this survey yet.");
    }
   
    foreach ($aaa as $a) {
        if (!$results["$a->user"]) { // init new array
            $results["$a->user"]["time"] = $a->time;
            foreach ($order as $key => $qid) {
                $results["$a->user"]["$qid"]["answer1"] = "";
                $results["$a->user"]["$qid"]["answer2"] = "";
            }
        }
        $results["$a->user"]["$a->question"]["answer1"] = $a->answer1;
        $results["$a->user"]["$a->question"]["answer2"] = $a->answer2;
    }


// Output the file as a valid Excel spreadsheet if required

    if ($type == "xls") {
        include( "$CFG->libdir/psxlsgen.php" );


        $myxls = new PhpSimpleXlsGen();
        $myxls->totalcol = count($order) + 100;
        $header = array("surveyid","surveyname","userid","firstname","lastname","email","idnumber","time", "notes");
        $myxls->ChangePos(0,0);
        foreach ($header as $item) {
            $myxls->InsertText($item);
        }
        foreach ($order as $key => $qid) {
            $question = $questions["$qid"];
            if ($question->type == "0" || $question->type == "1" || $question->type == "3" || $question->type == "-1")  {
                $myxls->InsertText("$question->text");
            }
            if ($question->type == "2" || $question->type == "3")  {
                $myxls->InsertText("$question->text (preferred)");
            }
        }

        $i = 0;
        foreach ($results as $user => $rest) {
            $i++;
            $myxls->ChangePos($i,0);
            if (! $u = get_record("user", "id", $user)) {
                error("Error finding student # $user");
            }
            if ($n = get_record_sql("SELECT * FROM survey_analysis WHERE survey='$survey->id' AND user='$user'")) {
                $notes = $n->notes;
            } else {
                $notes = "No notes made";
            }
            $myxls->InsertText($survey->id);
            $myxls->InsertText($survey->name);
            $myxls->InsertText($user);
            $myxls->InsertText($u->firstname);
            $myxls->InsertText($u->lastname);
            $myxls->InsertText($u->email);
            $myxls->InsertText($u->idnumber);
            $myxls->InsertText( date("d-M-Y h:i:s A", $results["$user"]["time"]) );
            $myxls->InsertText($notes);
    
            foreach ($order as $key => $qid) {
                $question = $questions["$qid"];
                if ($question->type == "0" || $question->type == "1" || $question->type == "3" || $question->type == "-1")  {
                    $myxls->InsertText( $results["$user"]["$qid"]["answer1"] );
                }
                if ($question->type == "2" || $question->type == "3")  {
                    $myxls->InsertText( $results["$user"]["$qid"]["answer2"] );
                }
            }
        }
        $myxls->SendFile("surveyreport");

        exit;
    }

// Otherwise, return the text file.

// Print header to force download

    header("Content-Type: application/download\n"); 
    header("Content-Disposition: attachment; filename=\"$survey->name results.txt\"");

// Print names of all the fields

    echo "surveyid    surveyname    userid    firstname    lastname    email    idnumber    time    ";
    foreach ($order as $key => $qid) {
        $question = $questions["$qid"];
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
        if (! $u = get_record_sql("SELECT firstname,lastname,email,idnumber FROM user WHERE id = '$user'")) {
            error("Error finding student # $user");
        }
        echo $survey->id."    ";
        echo $survey->name."    ";
        echo $user."    ";
        echo $u->firstname."    ";
        echo $u->lastname."    ";
        echo $u->email."    ";
        echo $u->idnumber."    ";
        echo date("d-M-Y h:i:s A", $results["$user"]["time"])."    ";

        foreach ($order as $key => $qid) {
            $question = $questions["$qid"];
            if ($question->type == "0" || $question->type == "1" || $question->type == "3" || $question->type == "-1")  {
                echo $results["$user"]["$qid"]["answer1"]."    ";
            }
            if ($question->type == "2" || $question->type == "3")  {
                echo $results["$user"]["$qid"]["answer2"]."    ";
            }
        }
        echo "\n";
    }
    exit;


?>

