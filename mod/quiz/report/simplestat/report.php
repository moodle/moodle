<?PHP  // $Id$

/// Overview report just displays a big table of all the attempts

class quiz_report extends quiz_default_report {

    function display($quiz, $cm, $course) {     /// This function just displays the report

        global $CFG;

        $data = array();
        $questionorder = explode(',', $quiz->questions);

        /// For each person in the class, get their best attempt
        /// and create a table listing results for each person
        if ($users = get_course_students($course->id, "u.lastname ASC")) {
            foreach ($users as $user) {

                $data[$user->id]->name    = "$user->firstname $user->lastname";
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

        optional_variable($output, "");

    /// If spreadsheet is wanted, produce one
        if ($output = "xls") {
        }

    /// If a text file is wanted, produce one
        if ($output = "xls") {
        }

    /// Otherwise, display the table as HTML

        $count = count($questionorder);
        $total = array();
   
        echo "<table border=1 align=\"center\">";
        echo "<tr>";
        echo "<td>&nbsp;</td>";
        for ($i=1; $i<=$count; $i++) {
            $total[$i] = 0.0;
            echo "<th>$i</th>";
        }
        echo "</tr>";

        $datacount = 0;
        foreach ($data as $userid => $datum) {
            echo "<tr>";
            echo "<td><b>$datum->name</b></td>";
            if ($datum->grades) {
                $datacount++;
                foreach ($datum->grades as $key => $grade) {
                    echo "<td>$grade</td>";
                    $total[$key]+= $grade;
                }
            }
            echo "</tr>";
        }

        echo "<tr>";
        echo "<td>&nbsp;</td>";
        for ($i=1; $i<=$count; $i++) {
            $average = format_float($total[$i] / $datacount, 2);
            echo "<td>$average</td>";
        }
        echo "</tr>";

        echo "</table>";

        return true;
    }
}

?>
