<?php  // $Id$
////////////////////////////////////////////////////////////////////
/// Class for importing course test manager questions.            //
///                                                               //
///                                                               //
////////////////////////////////////////////////////////////////////

// Based on format.php, included by ../../import.php
/**
 * @package questionbank
 * @subpackage importexport
 */

require_once($CFG->dirroot.'/lib/uploadlib.php');

class qformat_coursetestmanager extends qformat_default {

    function provide_import() {
        return true;
    }

    function importpreprocess($category) {
        $this->category = $category;  // Important
        return true;
    }

    function importprocess($filename) {
        global $CFG, $USER, $strimportquestions,$form,$question_category,$category,$COURSE,
            $hostname, $mdapath, $mdbpath;
        if ((PHP_OS == "Linux") and isset($hostname)) {
            $hostname = trim($hostname);
            // test the ODBC socket server connection
            // if failure, unset hostname and set hostname_access_error
            $question_categories = $this->getquestioncategories($mdbpath, $mdapath, $hostname);
            if (!$question_categories) {
                $hostname_access_error = $hostname . " ";
                unset($hostname);
            } else  {
                $hostname_access_error = 0;
            }
        }

        if ((PHP_OS == "Linux") and !isset($hostname)) {
            // copy the file to a semi-permanent location
            if (! $basedir = make_upload_directory("$COURSE->id")) {
                error("The site administrator needs to fix the file permissions for the data directory");
            }
            if (!isset($hostname_access_error)) {
                $bname=basename($filename);
                $cleanfilename = clean_filename($bname);
                if ($cleanfilename) {
                    $newfile = "$basedir/$cleanfilename";
                    if (move_uploaded_file($filename, $newfile)) {
                        chmod($newfile, 0666);
                        clam_log_upload($newfile,$COURSE);
                    } else {
                        notify(get_string("uploadproblem", "", $filename));
                    }
                }
                $filename = $newfile;
            }
            print_heading_with_help($strimportquestions, "import", "quiz");
            print_simple_box_start("center");
            if ($hostname_access_error) { notify("couldn't connect to ODBC Socket Server on " . $hostname_access_error); }
            echo "<form method=\"post\" action=\"import.php\">";
            echo '<fieldset class="invisiblefieldset">';
            echo "<table cellpadding=\"5\">";

            echo "<tr><td align=\"right\">";
            echo "What is the hostname or IP address of the ODBC Socket Server:</td><td>";
            echo " <input name=\"hostname\" type=\"text\" size=\"50\" value=\"".stripslashes($hostname_access_error)."\" />";
            echo " <input name=\"filename\" type=\"hidden\" value=\"".$filename."\" />";
            echo " <input name=\"category\" type=\"hidden\" value=\"".$category->id."\" />";
            echo " <input name=\"format\" type=\"hidden\" value=\"".$form->format."\" />";
            echo "</td><td>&nbsp;</td></tr>";
            echo "<tr><td align=\"right\">";
            echo "What is the location of the database (.mdb file) on the Socket Server:</td><td>";
            echo " <input name=\"mdbpath\" type=\"text\" size=\"50\" value=\"".stripslashes($mdbpath)."\" />";
            echo "</td><td>&nbsp;</td></tr>";
            echo "<tr><td align=\"right\">";
            echo "What is the location of the system database (System.mda file) on the Socket Server:</td><td>";
            echo " <input name=\"mdapath\" type=\"text\" size=\"50\" value=\"".stripslashes($mdapath)."\" />";
            echo "</td><td>&nbsp;</td></tr>";
            echo "<tr><td>&nbsp;</td><td>";
            echo " <input type=\"submit\" name=\"save\" value=\"Connect to Server\" />";
            echo "</td></tr>";
            echo "</table>";
            echo '</fieldset>';
            echo "</form>";
            print_simple_box_end();
            print_footer($COURSE);
            exit;
        }

        // we get here if running windows or after connect to ODBC socket server on linux
//
// this generates the page to choose categories of questions to import
//
        if (!isset($question_category)) {

            if (PHP_OS == "WINNT") {
            // copy the file to a semi-permanent location
                if (! $basedir = make_upload_directory("$COURSE->id")) {
                    error("The site administrator needs to fix the file permissions for the data directory");
                }
                $bname=basename($filename);
                $cleanfilename = clean_filename($bname);
                if ($cleanfilename) {
                    $newfile = "$basedir/$cleanfilename";
                    if (move_uploaded_file($filename, $newfile)) {
                        chmod($newfile, 0666);
                        clam_log_upload($newfile,$COURSE);
                    } else {
                        notify(get_string("uploadproblem", "", $filename));
                    }
                }
                $filename = $newfile;
            }
            // end of file copy

            // don't have to do this on linux, since it's alreay been done in the test above
            if (PHP_OS == "WINNT") {
                $question_categories = $this->getquestioncategories($filename);
            }
            // print the intermediary form
            if (!$categories = question_category_options($COURSE->id, true)) {
                error("No categories!");
            }
            print_heading_with_help($strimportquestions, "import", "quiz");
            print_simple_box_start("center");
            echo "<form method=\"post\" action=\"import.php\">";
            echo '<fieldset class="invisiblefieldset">';
            echo "<table cellpadding=\"5\">";
            echo "<tr><td align=\"right\">";
            echo "Choose a category of questions to import:</td><td>";
            asort($question_categories);
            choose_from_menu($question_categories, "question_category","All Categories","All Categories", "", "allcategories");
            echo " <input name=\"filename\" type=\"hidden\" value=\"".$filename."\" />";
            echo " <input name=\"category\" type=\"hidden\" value=\"".$category->id."\" />";
            echo " <input name=\"format\" type=\"hidden\" value=\"".$form->format."\" />";
            if (PHP_OS == "Linux") {
                echo " <input name=\"hostname\" type=\"hidden\" value=\"".stripslashes(trim($hostname))."\" />";
                echo " <input name=\"mdbpath\" type=\"hidden\" value=\"".stripslashes($mdbpath)."\" />";
                echo " <input name=\"mdapath\" type=\"hidden\" value=\"".stripslashes($mdapath)."\" />";
            }
            echo "</td><td>&nbsp;</td>";
            echo "</tr><tr><td>&nbsp;</td><td>";
            echo " <input type=\"submit\" name=\"save\" value=\"Import Questions\" />";
            echo "</td></tr>";
            echo "</table>";
            echo '</fieldset>';
            echo "</form>";
            print_simple_box_end();
            print_footer($COURSE);
            exit;
        }
//
// this is the main import section
//
        notify("Importing questions");
        if (PHP_OS == "Linux") {
            $hostname = trim($hostname);
            $records = $this->getquestions($mdbpath,$question_category,$mdapath, $hostname);
        } else {
            $records = $this->getquestions($filename,$question_category);
        }
        foreach ($records as $qrec) {
            $question = $this->defaultquestion();
            if ($qrec[9] != "") {
                $question->image = $qrec[9];
            }
//  0   Selected
//  1   PracticeTestOK?
//  2   QuestionText
//  3   QuestionType
//  4   Option1Text
//  5   Option2Text
//  6   Option3Text
//  7   Option4Text
//  8   CorrectAnswer
//  9   Graphic
//  10  Module
//  11  ChapterNumber
//  12  PageNumber
            $ref = "Answer can be found in chapter ". $qrec[11] . ", page " . $qrec[12] . ".";
            switch ($qrec[3]) {
                case 1:
                    $question->qtype = MULTICHOICE; // MULTICHOICE, SHORTANSWER, TRUEFALSE
        //          echo "<pre>";echo htmlspecialchars($qrec[2]); echo "</pre>";
                    $question->questiontext = addslashes(trim($qrec[2]));
        //          echo "<pre>";echo $question->questiontext; echo "</pre>";
                    $question->name = preg_replace("/<br />/", "", $question->questiontext);
                    $question->single = 1;  // Only one answer is allowed -- used for multiple choicers
                    $fractionset = 0;
                    for ($i=4;$i<=7;$i++) {
                        if ($qrec[$i] != "") {
                            $question->answer[$i-3]=addslashes($qrec[$i]);
                            if ($qrec[8] == $i-3) {  // if this is the index of CorrectAnswer
                                $question->fraction[$i-3] = 1;
                                $fractionset = 1;
                            } else {
                                $question->fraction[$i-3] = 0;
                            }
                            $question->feedback[$i-3] = (($qrec[8] == $i-3)?"Correct. ":"Incorrect. ") . $ref;
                        }
                    }
                    if ($fractionset == 0) { 
                        $question->fraction[1] = 1; 
                    }
                break;
                case 2:  // TRUE FALSE
                    $question->qtype = TRUEFALSE;
                    $question->questiontext = addslashes(trim($qrec[2]));
                    $question->name = preg_replace("/<br />/", "", $question->questiontext);
                    // for TF, $question->answer should be 1 for true, 0 for false
                    if ($qrec[8] == "T") { 
                        $question->answer =1;
                    } else { 
                        $question->answer = 0; 
                    }
                      // for TF, use $question->feedbacktrue and feedbackfalse
                    $question->feedbacktrue = (($qrec[8] =="T")?"Correct. ":"Incorrect. ") . $ref;
                    $question->feedbackfalse = (($qrec[8] =="F")?"Correct. ":"Incorrect. ") . $ref;
                break;
                case 3:
                    $question->qtype = SHORTANSWER;
                    $question->questiontext = addslashes(trim($qrec[2]));
        //          echo "<pre>";echo $question->questiontext; echo "</pre>";
                    $question->name = preg_replace("/<br />/", "", $question->questiontext);
                    $question->usecase=0;  // Ignore case -- for SHORT ANSWER questions
                    $answers = explode("~", $qrec[8]);
                    $question->answer[0]=" ";
                    $question->fraction[0]=1;
                    for ($i=0;$i<count($answers);$i++) {
                        $question->answer[$i] = addslashes(trim($answers[$i]));
                        $question->feedback[$i] = $ref;
                        $question->fraction[$i] = 1; // 1 for 100%, 0 for none or somewhere in between
                    }
                break;
                case 4:
                    $question = 0;
                    notify("Cannot use essay questions - skipping question ". $qrec[2] . " " . $ref);
                break;
                default:
                    $question = 0;
                    notify("Misformatted Record.  Question Skipped.");
                break;
            }
            if ($question) { 
                $questions[] = $question; 
            }
        }
        $count = 0;
        // process all the questions
        if (PHP_OS == "WINNT") {
            $filename = str_replace("\\\\","\\",$filename);
            $filename = str_replace("/","\\",$filename);
        }
        foreach ($questions as $question) {   // Process and store each question
            $count++;
            echo "<hr /><p><b>$count</b>. ".stripslashes($question->questiontext)."</p>";
            $question->category = $this->category->id;
            $question->stamp = make_unique_id_code();  // Set the unique code (not to be changed)
            $question->createdby = $USER->id;
            $question->timecreated = time();
            if (!$question->id = insert_record("question", $question)) {
                error("Could not insert new question!");
            }
            $this->questionids[] = $question->id;
            // Now to save all the answers and type-specific options
            $result = save_question_options($question);
            if (!empty($result->error)) {
                notify($result->error);
                $this->deletedatabase($filename);
                return false;
            }
            if (!empty($result->notice)) {
                notify($result->notice);
                $this->deletedatabase($filename);
                return true;
            }
            // Give the question a unique version stamp determined by question_hash()
            set_field('question', 'version', question_hash($question), 'id', $question->id);
        }
        $this->deletedatabase($filename);
        return true;
    }

    function importpostprocess() {
        return true;
    }

    function deletedatabase($filename) {
        if (! $this->fulldelete($filename)) {
            echo "<br />Error: Could not delete: $filename";
            return false;
        }
        return true;
    }

    function getquestions($filename, $category, $mdapath="", $hostname="") {
        if (($category == "allcategories") or ($category == "")) {
            $sql = "SELECT * FROM TBQuestions";
        } else {
            $sql = "SELECT * FROM TBQuestions where module = '".$category."'";
        }
        if (PHP_OS == "WINNT") {
            $ldb =& $this->connect_win($filename);
            $qset = $ldb->Execute("$sql");
            if ( !$qset->EOF ) {
                $records = $qset->GetAssoc(true);
            } else {
                $this->err("There were no records in the database.",$dsn);
                $ldb->Close();
                return false;
            }
            $ldb->Close();
        } else  { // if PHP_OS == WINNT
            // we have a linux installation
            $result = $this->query_linux($sql,$filename, $mdapath,$hostname);
            if ( count($result) > 0 ) {
                // get rid of the ID field in the first column.
                for($i=0;$i<count($result);$i++) {
                    foreach (array_keys($result[$i]) as $j) {
                        $records[$i][$j-1] = $result[$i][$j];
                    }
                }
            } else {
                $this->err("There were no records in the database.",$dsn);
                $ldb->Close();
                return false;
            }
                // xml test and connect
        }  // PHP_OS TEST
            return $records;
    }

    function getquestioncategories($filename, $mdapath="", $hostname="") {
        global $CFG, $result;
        $sql = "SELECT Distinct module FROM TBQuestions";
        if (PHP_OS == "WINNT") {
            $ldb =& $this->connect_win($filename);
            $qset = $ldb->Execute("$sql");
            if ( !$qset->EOF ) {
                $records = $qset->GetArray(true);
                foreach ($records as $record) {
                    $categories[$record[0]] = $record[0];
                }
            } else { // if recordcount
                $this->err("There were no records in the database.",$dsn);
                $ldb->Close();
                return false;
            }
            $ldb->Close();
        } else  { // if PHP_OS == WINNT
            // we have a linux installation
            $result = $this->query_linux($sql, $filename, $mdapath, $hostname);
            for($i=0;$i<count($result);$i++) {
                $categories[$result[$i][0]] = $result[$i][0];
            }
        }  // PHP_OS TEST
        return $categories;
    }

    function query_linux($sql, $mdbpath, $mdapath, $hostname) {
        global $result;
        include_once("odbcsocketserver.class.php");
        // set up socket server object to connect to remote host
        $oTest = new ODBCSocketServer;
        //Set the Hostname, port, and connection string
        $oTest->sHostName = $hostname;
        $oTest->nPort = 9628;
//      $oTest->sConnectionString="DRIVER=Microsoft Access Driver (*.mdb);SystemDB=C:\CTM\System.mda;DBQ=C:\CTM\of2K3\ctm.mdb;UID=Assess;PWD=VBMango;";
        $oTest->sConnectionString="DRIVER=Microsoft Access Driver (*.mdb);SystemDB=".
        $mdapath.";DBQ=".$mdbpath.";UID=Assess;PWD=VBMango;";
        // send and receive XML communication
        $qResult = $oTest->ExecSQL($sql);
        // set up XML parser to read the results
        $xml_parser = xml_parser_create("US-ASCII");
        xml_set_element_handler($xml_parser, "quiz_xmlstart", "quiz_xmlend");
        xml_set_character_data_handler($xml_parser, "quiz_xmldata");
        // parse the XML and get back the result set array
        if (!xml_parse($xml_parser, $qResult)) {
            $this->err("XML error: ".xml_error_string(xml_get_error_code($xml_parser))
              ." at line ".xml_get_current_line_number($xml_parser),$oTest->sConnectionString);
            return false;
        } else  {
//          echo("Successful XML parse.  ");
            // prepare the array for use in the pull-down
/*          echo "<br />count of rows is ". count ($result);
                    echo "<pre>\n";
                    $qResult = HtmlSpecialChars($qResult);
                    echo $qResult;
                    echo "\n</pre>";
*/
            xml_parser_free($xml_parser);
//  $sResult = HtmlSpecialChars($qResult);
    //echo("<pre>");
//  echo($sResult);
//  echo("</pre>");

            return $result;
        }
    }

    function connect_win($filename) {
        global $CFG, $systemdb;
        // first, verify the location of System.mda
        if (!isset($systemdb)) {
            $systemdb=$this->findfile("System.mda");
        }
        if (! $systemdb) {
            $this->err("The system database System.mda cannot be found.  Check that you've uploaded it to the course.",$dsn);
            die;
        }

        $ldb = &ADONewConnection('access');
        $dsn="DRIVER=Microsoft Access Driver (*.mdb);SystemDB=".$systemdb.";DBQ=".$filename.";UID=Assess;PWD=VBMango;";
        $dbconnected = $ldb->Connect($dsn);
        if (! $dbconnected) {
            $this->err("Moodle could not connect to the database.",$dsn);
            die;
        }
        return $ldb;
    }

    function err($message, $dsn) {
        echo "<font color=\"#990000\">";
        echo "<p>Error: $message</p>";
        echo "<p>ODBC File DSN: $dsn<br />";
        echo "</font>";
    }

    function fulldelete($location) {
        if (is_dir($location)) {
            $currdir = opendir($location);
            while (false !== ($file = readdir($currdir))) {
                if ($file <> ".." && $file <> ".") {
                    $fullfile = $location."/".$file;
                    if (is_dir($fullfile)) {
                        if (!fulldelete($fullfile)) {
                            return false;
                        }
                    } else {
                        if (!unlink($fullfile)) {
                            return false;
                        }
                    }
                }
            }
            closedir($currdir);
            if (! rmdir($location)) {
                return false;
            }

        } else {
            if (!unlink($location)) {
                return false;
            }
        }
        return true;
    }


    function findfile($filename) {
        global $CFG;
        $dirs = $this->getcoursedirs();
        $dirs[] = $CFG->dirroot."\mod\quiz\format";
        foreach ($dirs as $dir) {
            $file = $dir . "\System.mda";
            // look for System.mda
            if (is_file($file)) return $file;
        }
        return false;
    }

    function getcoursedirs() {
        global $CFG;
        // for every course in the system, find the root of the data directory
        $courses = get_records_sql("select distinct id,fullname from ".$CFG->prefix."course");
        $dirs = array();
        if ($courses) {
            foreach ($courses as $course) {
                $dir = $CFG->dataroot . "/" . $course->id;
                if (is_dir($dir)) { 
                    $dirs[] = $dir; 
                }
            }
        }
        return $dirs;
    }
} // END OF CLASS

    //Handler for starting elements
    function quiz_xmlstart($parser, $name, $attribs) {
        global $result,$row, $col, $incolumn;
        $name = strtolower($name);
        switch ($name) {
            case "row":
                $col=0;break;
            case "column":
                $incolumn = 1;break;
            case "error":
            break;
            case "result":
                $row = 0; break;
        } // switch
    }

    //handler for the end of elements
    function quiz_xmlend($parser, $name) {
        global $result, $row, $col, $incolumn;
        $name = strtolower($name);
        switch ($name) {
            case "row":
                $row++;break;
            case "column":
                $incolumn = 0;
                $col++;
            break;
            case "error":
            break;
            case "result":
            break;
        } // switch
    }  // function

    //handler for character data
    function quiz_xmldata($parser, $data) {
        global $result, $row, $col, $incolumn;
        if ($incolumn) { 
            $result[$row][$col] = $result[$row][$col] . $data;
        }
    }

?>
