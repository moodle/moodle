<?PHP  // $Id$

/// Overview report just displays a big table of all the attempts

class quiz_report extends quiz_default_report {

    function display($quiz, $cm, $course) {     /// This function just displays the report

        global $CFG;
        global $download;

        optional_variable($download, "");

    /// Check to see if groups are being used in this quiz
        if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
            $currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id&mode=simplestat");
        } else {
            $currentgroup = false;
        }

        if ($currentgroup) {
            $users = get_group_students($currentgroup, "u.lastname ASC");
        } else {
            $users = get_course_students($course->id, "u.lastname ASC");
        }

        $data = array();
        $questionorder = explode(',', $quiz->questions);

        $count = 0;
        foreach ($questionorder as $questionid) {
            $count++;
            $question[$count] = get_record("quiz_questions", "id", $questionid);
        }

    /// For each person in the class, get their best attempt
    /// and create a table listing results for each person
        if ($users) {
            foreach ($users as $user) {

                $data[$user->id]->firstname = $user->firstname;
                $data[$user->id]->lastname = $user->lastname;
                $data[$user->id]->grades = array(); // by default

                if (!$attempts = quiz_get_user_attempts($quiz->id, $user->id)) {
                    continue;
                }
                if (!$bestattempt = quiz_calculate_best_attempt($quiz, $attempts)) {
                    continue;
                }
                if (!$questions = quiz_get_attempt_responses($bestattempt, $quiz)) {
                    continue;
                }
                quiz_remove_unwanted_questions($questions, $quiz);

                if (!$results = quiz_grade_attempt_results($quiz, $questions)) {
                    error("Could not re-grade this quiz attempt!");
                }

                $count = 0;
                foreach ($questionorder as $questionid) {
                    $count++;
                    $data[$user->id]->grades[$count] = $results->grades[$questionid];
                }
            }
        }

        $count = count($questionorder);
        $total = array();
        $average = array();
        for ($i=1; $i<=$count; $i++) {
            $total[$i] = 0.0;
            $average[$i] = 0.0;
        }

        $datacount = 0;
        foreach ($data as $userid => $datum) {
            if ($datum->grades) {
                $datacount++;
                foreach ($datum->grades as $key => $grade) {
                    $total[$key]+= $grade;
                }
            }
        }

        if ($datacount) {
            foreach ($total as $key => $sum) {
                $average[$key] = format_float($sum/$datacount, 2);
            }
        }

    /// If spreadsheet is wanted, produce one
        if ($download == "xls") {
            require_once("$CFG->libdir/excel/Worksheet.php");
            require_once("$CFG->libdir/excel/Workbook.php");
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$course->shortname ".$quiz->name.".xls" );
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
            header("Pragma: public");

            $workbook = new Workbook("-");
            // Creating the first worksheet
            $myxls = &$workbook->add_worksheet('Simple Quiz Statistics');

        /// Print names of all the fields
            $myxls->write_string(0,0,$quiz->name);
            $myxls->set_column(0,0,25);
                
            $myxls->set_column(1,$count,9);
            for ($i=1; $i<=$count; $i++) {
                $myxls->write_string(0,$i,$i);
            }
        
        /// Print all the user data

            $row=1;
            foreach ($data as $userid => $datum) {
                $myxls->write_string($row,0,fullname($datum));
                for ($i=1; $i<=$count; $i++) {
                    if (isset($datum->grades[$i])) {
                        $myxls->write_number($row,$i,$datum->grades[$i]);
                    }
                }
                $row++;
            }

        /// Print all the averages
            for ($i=1; $i<=$count; $i++) {
                $myxls->write_number($row,$i,$average[$i]);
            }

            $formatot =& $workbook->add_format();
            // format number 10 is percent, two digit
            $formatot->set_num_format(10);
        /// Print all the averages as percentages
            $row++;
            $myxls->write_string($row,0,"%");
            for ($i=1; $i<=$count; $i++) {
//                $percent = format_float($average[$i] * 100);
//                $myxls->write_text($row,$i,"$percent%");
                $myxls->write_number($row,$i,$average[$i],$formatot);
            }

            $workbook->close();
        
            exit;
        }
    

    /// If a text file is wanted, produce one
        if ($download == "txt") {
        /// Print header to force download
    
            header("Content-Type: application/download\n"); 
            header("Content-Disposition: attachment; filename=$course->shortname ".$quiz->name.".txt");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
            header("Pragma: public");


        /// Print names of all the fields
    
            echo "$quiz->name";
            for ($i=1; $i<=$count; $i++) {
                echo "\t$i";
            }
            echo "\n";
        
        /// Print all the user data

            foreach ($data as $userid => $datum) {
                echo fullname($datum);
                for ($i=1; $i<=$count; $i++) {
                    echo "\t";
                    if (isset($datum->grades[$i])) {
                        echo $datum->grades[$i];
                    }
                }
                echo "\n";
            }

        /// Print all the averages
            echo "\t";
            for ($i=1; $i<=$count; $i++) {
                echo "\t".$average[$i];
            }
            echo "\n";

        /// Print all the averages as percentages
            echo "\t%";
            for ($i=1; $i<=$count; $i++) {
                $percent = format_float($average[$i] * 100);
                echo "\t$percent";
            }
            echo "\n";
        
            exit;
        }




    /// Otherwise, display the table as HTML

        echo "<table border=1 align=\"center\">";
        echo "<tr>";
        echo "<td>&nbsp;</td>";
        for ($i=1; $i<=$count; $i++) {
            $title = '';
            if (!empty($question[$i]->questiontext)) {
                $title = strip_tags($question[$i]->questiontext);
            }
            echo "<th title=\"$title\">$i</th>";
        }
        echo "</tr>";

        foreach ($data as $userid => $datum) {
            echo "<tr>";
            echo "<td><b>".fullname($datum)."</b></td>";
            if ($datum->grades) {
                foreach ($datum->grades as $key => $grade) {
                    if (isset($grade)) {
                        echo "<td>$grade</td>";
                    } else {
                        echo "<td>&nbsp;</td>";
                    }
                }
            }
            echo "</tr>";
        }

        echo "<tr>";
        echo "<td>&nbsp;</td>";
        for ($i=1; $i<=$count; $i++) {
            echo "<td>".$average[$i]."</td>";
        }
        echo "</tr>";

        echo "</table>";

        echo "<br />";
        echo "<table border=0 align=center><tr>";
        echo "<td>";
        unset($options);
        $options["id"] = "$cm->id";
        $options["mode"] = "simplestat";
        $options["noheader"] = "yes";
        $options["download"] = "xls";
        print_single_button("report.php", $options, get_string("downloadexcel"));
        echo "<td>";
        $options["download"] = "txt";
        print_single_button("report.php", $options, get_string("downloadtext"));
        echo "</table>";
    

        return true;
    }
}

?>
