<?PHP  // $Id: report.php,
//  created from the above 2003/11/20 by Tom Robb tom@robb.net
// Version 2.5  Modified 2004/01/18
//  Further errorsin percentage calculations corrected

/// This report shows the specific responses made by each student for each question.

class quiz_report extends quiz_default_report {

    function display($quiz, $cm, $course) {     /// This function just displays the report

    global $CFG;
    global $download, $quests,$qtally,$table_colcount,$max_choices, $analysis,$qs_in_order,$total_user_count,$match_number, $thismin,$thismax,$myxls,$match_qs,$formatbc,$workbook,$strquestion,$showtext,$debug;
    optional_variable($download, "");
    optional_variable($debug, "");
//$debug = 1;


    $strindivresp = get_string('indivresp', 'quiz');
    $strname = get_string('name', 'quiz');
    $strgrade = get_string('grade');
    $stritemanal = get_string('itemanal', 'quiz');
    $strcorrresp = get_string('corrresp', 'quiz');
    $strnoresponse = get_string('noresponse', 'quiz');
    $strpercentcorrect = get_string('percentcorrect', 'quiz');
    $strlistitems = get_string('listitems', 'quiz');
    $strquestion = get_string('question', 'quiz');
    $strwithsummary = get_string('withsummary', 'quiz');
    $strdiscrimination = get_string('discrimination', 'quiz');

    //Get the question ids
    //$showtext causes M/C text to whos in top table.  This could be made into a user toggle if we want to complicate matters
    $showtext = 1;
    $containsMCTF = 0;  //used to toggle title in final listing
    $thisquizid = $quiz->id;
    $qs_in_order =qr_getqs($thisquizid);
    $qcount = 0;
    $max_choices = 0;  //for printing tallies we need to know how many rows to print
    $table_colcount = 0;
    foreach ($qs_in_order as $qid){
        $table_colcount++;
        //Get the question type and text and append to object
        if ($question_data = get_records_select("quiz_questions",$select="id='$qid'","","qtype,questiontext")) {
            foreach($question_data as $thiskey => $thisq){
                $quests[$qid]["qtype"] =  $thiskey;
                $quests[$qid]["qtext"] =  $question_data[$thiskey]->questiontext;
            }
        }
        if($quests[$qid]['qtype'] == 3 or $quests[$qid]['qtype'] == 2){ $containsMCTF = 1;}
        if($quests[$qid]['qtype'] == 5){
            //for MATCH items we need to know how many items there are
            $thismatch = get_record("quiz_match","question","$qid");
            $temparray = explode(",",$thismatch->subquestions);
            $match_number[$qid] = count($temparray);
            $match_start[$qid] = $temparray[0];
            $table_colcount = $table_colcount + $match_number[$qid] - 1;
        }
        $choice_data = get_records_select("quiz_answers",$select="question='$qid'","","id as cid,answer,fraction");
        if($quests[$qid]['qtype'] == 8){
              $thismin[$qid] = get_field("quiz_numerical","min","question","$qid");
              $thismax[$qid] = get_field("quiz_numerical","max","question","$qid");
              $quests[$qid]["correct"] = $thismin[$qid] ."< $choice_data->answer >" .  $thismax[$qid];
        }
        if($quests[$qid]['qtype'] >3) {continue;}
        //only get choices here if type is SHORTANSWER,TRUEFALSE or MULTICHOICE
        //Get the choices for each question and add to object['choice'] each choicd ID and text
        $choice_count=0;
        foreach($choice_data as $thiscid=>$thischoice){
            $choice_count++;
            $quests[$qid]["choice"]["$thiscid"]["answer"] =  $thischoice->answer;
            $quests[$qid]["choice"]["$thiscid"]["choiceno"]  =  $choice_count;
            //if the fraction = 1, then set this choice number as the correct answer
            if ($thischoice->fraction == 1){
                //append answer if more than one
                if($quests[$qid]["correct"]){
                    $quests[$qid]["correct"] .= "," . $thischoice->answer;
                } else {
                    if($quests[$qid]['qtype'] == 3) {
                        $quests[$qid]["correct"] = $choice_count;
                    } else {
                        $quests[$qid]["correct"] = $thischoice->answer;
                    }
                }
            }
         }
    }
    if($debug and !$download){
        print("<h3>Quests</h3>");
        print_object($quests);
    }
    $user_resps = qr_quiz_responses($thisquizid);
//    //print_object($user_resps);
    foreach($user_resps as $thiskey => $thisresp){
        $userdata[$thisresp->userid][$thisresp->attemptno]['response'][$thisresp->question]=s($thisresp->answer);
        $userdata[$thisresp->userid][$thisresp->attemptno]['grade']=$thisresp->sumgrades;
        $userdata[$thisresp->userid][$thisresp->attemptno]['name']=fullname($thisresp);
        $userdata[$thisresp->userid][$thisresp->attemptno]['attemptid']=$thisresp->aid;
    }
    if($debug and !$download){
    print("<h3>User Data</h3>");
    print_object($userdata);
    }
    //now go through $userdata and create tally by user, attempt, question storing both response and if correct
    $reportline = 0;
    foreach($userdata as $thisuser){
        foreach($thisuser as $thiskey=>$thisattempt){
//            //print_object($thisattempt);
            $reportline++;
            $data_tally[$reportline][$thisattempt['attemptid']][] = $thisattempt['name'];
            $data_tally[$reportline][$thisattempt['attemptid']][] =round(($thisattempt['grade']/$quiz->sumgrades)*100,0); 
            //now for each question, record response as it should be printed and whether right, wrong or skipped
            //SHORTASNSWER the answer as in $userdata; TF or MULTI need response looked by from cid from $quests
            //MATCH needs elaborate processing
            //We need to go through the responses in the order Qs presented thus the use of $qs_in_order not just $thisattempt
            foreach ($qs_in_order as $qid){
                $thisanswer = $thisattempt['response'][$qid];
                if($quests[$qid]['qtype']==5) {
                    //for MATCH processing.  Treat each match couplet as an item for $data_tally
                    //builds an array of all questions and answers for match questions
                    $quiz_matches = qr_match_array($qid);
                    $matchsets = explode(",",$thisanswer);
                    //sort needed so that same items line up vertically
                    sort($matchsets);
                    $matchcnt = 0;
                    foreach($matchsets as $thisset){
                        $matchcnt++;
                        $nowpair = explode("-",$thisset);
                        $phrasepair[0] = $quiz_matches[$nowpair[0]][Q];
                        $phrasepair[1] = $quiz_matches[$nowpair[1]][A];
                        //$match_answers keeps the correct answers for use in Response Analysis
                        //This will operate redundantly for each user but better than setting up separate routine to run once(?)
                        $match_answers[$qid][$nowpair[0]] = $phrasepair[1];
                        $match_qs[$qid][$nowpair[0]] = $phrasepair[0];
                        $rid = $nowpair[1];
                        $qtally[$qid][$nowpair[0]][$nowpair[1]]['tally']++;
                        $qtally[$qid][$nowpair[0]][$nowpair[1]]['answer'] = $phrasepair[1];
                        if ($quiz_matches[$nowpair[0]] == $quiz_matches[$nowpair[1]]) {
                            $pairdata['score'] = 1;
                            $qtally[$qid][$nowpair[0]]['correct']++;
                        } else {
                            $pairdata['score'] = 0;
                        }
                        $pairdata['data'] = $phrasepair;
                        $pairdata['qtype'] = 5;
                        $pairdata['qid'] = $qid;
                        $data_tally[$reportline][$thisattempt['attemptid']][] = $pairdata;
                     }
                } elseif ($quests[$qid]['qtype']==8) {
                    $thisdata = qr_answer_lookup($qid,$thisanswer);
                    $data_tally[$reportline][$thisattempt['attemptid']][] = $thisdata;
                } else {
                    $thisdata = qr_answer_lookup($qid,$thisanswer);
                    //$thisdata returns couplet of display string and right/wrong
                    if(!$thisdata['data']) {$thisdata['data'] = "--";}
                    if($thisdata) {
                        $data_tally[$reportline][$thisattempt['attemptid']][] = $thisdata;
                    }
                }
            }
        }
    }
    $total_user_count = $reportline;
    //prepare headers (must do now because $table_colcount calculated here
    if($debug and !$download){
    print("<h3>Data Tally</h3>");
    print_object($data_tally);
    }

    //Create a list of all attempts with their scores for item analysis
    //Also create $data2 that has attempt id as key
    foreach ($data_tally as $thistally){
        foreach($thistally as $this_aid=>$thisattempt){
            //this is the attempt id and the score
            $data2[$this_aid] = $thisattempt;
            $scores[$this_aid] = $thisattempt[1];
        }
    }
    arsort($scores);
    //now go through scores from top to bottom and from $data2 accumulate number correct for top 1/3 and bottom 1/3 of scorers
    $totscores = count($scores);
    $numb_to_analyze = floor($totscores/3);
    $skipval = $numb_to_analyze + 1;
    $first_lowval = $totscores - $numb_to_analyze +1;
    $count_scores = 0;
    $tempscores = array();
    $top_scores = array_pad($tempscores,$table_colcount+1,0);
    $bott_scores = array_pad($tempscores,$table_colcount+1,0);
    foreach($scores as $aid=>$score){
        $count_scores++;
        if ($count_scores < $skipval){
            //array items 0 & 1 contain user name & tot score, not item data
            $i = 2;
            while($data2[$aid][$i]){
                //let this array start from 1
                if ($data2[$aid][$i]['score'] == 1){
                    $top_scores[$i-1]++;
                }
                $i++;
            }
        } elseif ($count_scores >= $first_lowval) {
            $i = 2;
            while($data2[$aid][$i]){
                //let this array start from 1
                if ($data2[$aid][$i]['score'] == 1){
                    $bott_scores[$i-1]++;
                }
                $i++;
            }
        } else {
            continue;
        }
    }
    
    if($debug and !$download){
    print("<h3>Scores</h3>");
    print_object($scores);
    print("<h3>Top Scores</h3>");
    print_object($top_scores);
    print("<h3>Bottom Scores</h3>");
    print_object($bott_scores);
    }
    
    //Create here an array with the response analysis data for use with both screen display & Excel
    //  2 dimensional array has as many cells across as items + title, as many down as $max_choices
    // plus one row [0] for correct items
    // Populate array first with "--" in each cell
    $analysis[] = "--";
    $analysis0 = array_pad($analysis,$table_colcount+1,"--");
    for ($i = 1; $i <= $max_choices+1; $i++){
        $analysis[$i] = $analysis0;
    }

    $pct_correct = qr_make_footers();

    if($debug and !$download){
    print("<h3>Footers</h3>");
    print_object($pct_correct);
    }

    //put the correct values in $analysis
    for ($i = 1; $i<= $max_choices;$i++){
        //prepare answer tallies
        //2 columns already spoken for
        $current_column = 0;
        foreach ($qs_in_order as $qid){
            $current_column++;
            switch ($quests[$qid]['qtype']) {
                case 1:
                    if(!$sa_tally[$qid]){
                        $sa_tally[$qid] = qr_make_satally($qid,$current_column);
                    }
                    break;
                case 2:
                    if(!$tf_tally[$qid]){
                        $tf_tally[$qid] = qr_make_tftally($qid,$current_column);
                    }
                    break;
                case 3:
                  if($qtally[$qid][$i]){
                        $analysis[$i][$current_column] = $qtally[$qid][$i];
                    }
                    break;
                case 8:
                    if(!$num_tally[$qid]){
                        $num_tally[$qid] = qr_make_numtally($qid,$current_column);
                    }
                    break;
                case 5:
                //Make the inverted array if not already made
                    if(! $match_tally[$qid]){
                        $match_tally[$qid] = qr_make_matchtally($qid);
                    }
                    $match_end = $match_start[$qid] + $match_number[$qid] -1;
                    $colcounter = 0;
                    for ($j = $match_start[$qid];$j <= $match_end;$j++){
                        $colcounter++;
                        if($match_tally[$qid][$i][$colcounter]) {
                            $tallytext = $match_tally[$qid][$i][$colcounter]['answer'];
                            $tallycount = $match_tally[$qid][$i][$colcounter]['tally'];
                            //Two slashes used to represent location of a break since one slash might appear in data
                            $analysis[$i][$current_column + $colcounter-1] = $tallytext . "//" . $tallycount ;
                        }
                    }
                    $current_column += $match_number[$qid] -1;
                    break;
                default:
                
                break;
            }
        }
    }

    if($debug and !$download){
    print("<h3>Analysis</h3>");
    print_object($analysis);
    }
    /// If spreadsheet is wanted, produce one
    if ($download == "xls") {
        require_once("$CFG->libdir/excel/Worksheet.php");
        require_once("$CFG->libdir/excel/Workbook.php");
        header("Content-type: application/vnd.ms-excel");
        $downloadfilename = clean_filename("$course->shortname $quiz->name");
        header("Content-Disposition: attachment; filename=$downloadfilename.xls");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");
        $workbook = new Workbook("-");
        // Creating the first worksheet
        $myxls = &$workbook->add_worksheet('Responses for each student');

        /// format types
        $format =& $workbook->add_format();
        $format->set_bold(0);
        $formaty =& $workbook->add_format();
        $formaty->set_bg_color('yellow'); 
        $formatyc =& $workbook->add_format();
        $formatyc->set_bg_color('yellow'); //bold text on yellow bg
        $formatyc->set_bold(1);
        $formatyc->set_align('center');
        $formatc =& $workbook->add_format();
        $formatc->set_align('center');
        $formatb =& $workbook->add_format();
        $formatb->set_bold(1);
        $formatbc =& $workbook->add_format();
        $formatbc->set_bold(1);
        $formatbc->set_align('center');
        $formatbpct =& $workbook->add_format();
        $formatbpct->set_bold(1);
        $formatbpct->set_num_format('0.0%');
        $formatbrt =& $workbook->add_format();
        $formatbrt->set_bold(1);
        $formatbrt->set_align('right');
        $formatred =& $workbook->add_format();
        $formatred->set_bold(1);
        $formatred->set_color('red');
        $formatred->set_align('center');
        $formatblue =& $workbook->add_format();
        $formatblue->set_bold(1);
        $formatblue->set_color('blue');
        $formatblue->set_align('center');
        $myxls->write_string(0,0,$quiz->name);
        $myxls->set_column(0,0,25);
        $row=2;
        $qcount=0;
//        $myxls->write_string(0,2,$choiceMax,$formatyc);
        //You might expect the headers to be written at this point, but we are saving it till last
        $highest_Q_no = 0;
        $totcolcount = $table_colcount+2;
        $myxls->write_string(0,2,"Responses of Individuals to Each Item",$formatyc);
//        qr_xls_headers("Name","Grade");
        //This should have been a function but there is an 'incurable' error:
        //  "Call to a member function on a non-object"  It is repeated below for the 2nd worksheet with
        //  only minor variations
        $nm = "Name";$Item="Grade";
        $row = 1;
        $col = 0;
        $qcount = 0;
        $myxls->write_string($row,$col,$nm,$formatbc);
        if($Item == "Grade") {
            $col++;
            $myxls->write_string($row,$col,$Item,$formatbc);
        }
        foreach($qs_in_order as $qid){
            $qcount++;
            $col++;
            if($quests[$qid]['qtype'] == 5) {
                $i = 0;
                foreach ($match_qs[$qid] as $nowq){
                    $i++;
                    $qm = "Q-$qcount M-$i";
                    $myxls->write_string($row,$col,$qm,$formatbc);
                    $myxls->write_string(2,$col,$nowq,$formatyc);
                    $col++;
                }
                $col--;
            } else {
                $myxls->write_string($row,$col,"Q-$qcount",$formatbc);
            }
        }

        //now print the lines of answers
        
        $row = 2;
        foreach ($data_tally as $thisuserno=>$thisuser){
            foreach($thisuser as $thisattemptno=>$thisattempt){
                $row++;
                foreach($thisattempt as $thisitemkey=>$thisitem) {
                    //$thisitemkeys 1 & 2 are name and total score
                    //There needs to be a 3-way branch, keys0 & 1 just print $thisitem
                    //else if $thisitem['qtype'] = 5, then processing for MATCH is needed
                    //else the data to be printed is in $thisitem['data'] and 
                    //$thisitem['score'] == 1 shows that the item was correct
                    if ($thisitem['score'] < 1) {$thiscolor = $formatred;} else {$thiscolor = $formatblue;}
                    $col++;
                    if ($thisitemkey == 0){
                        $col = 0;
                        $myxls->write_string($row,$col,$thisitem,$formatb);
                    } elseif ($thisitemkey == 1){
                        $myxls->write_number($row,$col,$thisitem,$formatb);
                    } elseif ($thisitemkey['qtype'] == 2){
                        $myxls->write_string($row,$col,$thisitem['data']['answer'],$thiscolor);
                    } elseif ($thisitem['qtype'] == 5) {
                        if ($thisitem['score'] == 1) {$thiscolor = $formatblue;} else {$thiscolor = $formatred;}
                        if(!$thisitem['data'][1]){$thisitem['data'][1]="(No Response)";}
                        $myxls->write_string($row,$col,$thisitem['data'][1],$thiscolor);
                    } else {
                        $myxls->write_string($row,$col,$thisitem['data'],$thiscolor);
                    }
                }
            }
        }

        $myxls = &$workbook->add_worksheet('Item Response Analysis');
        $sheettitle = "Item Response Analysis";
        $myxls->write_string(0,0,$sheettitle,$formatb);
        $itemcount = 0;
        $nm = "Question";
        $row = 1;
        $col = 0;
        $qcount = 0;
        $myxls->write_string($row,$col,$nm,$formatbc);
        foreach($qs_in_order as $qid){
            $qcount++;
            $col++;
            if($quests[$qid]['qtype'] == 5) {
                $i = 0;
                foreach ($match_qs[$qid] as $nowq){
                $i++;
                $qm = "Q-$qcount M-$i";
                $myxls->write_string($row,$col,$qm,$formatbc);
                $myxls->write_string($row+1,$col,$nowq,$formatbc);
                $col++;
                }
                $col--;
            } else {
                $myxls->write_string($row,$col,"Q-$qcount",$formatbc);
            }
        }

//           Now write tally data
        $row = $row+2;
        $myxls->write_string($row,1,"Correct Response:",$formatbc);
        $col=1;
        foreach ($qs_in_order as $qid){
            $col++;
            if ($quests[$qid]['qtype'] == 5) {
                foreach($match_answers[$qid] as $thisans){
                $myxls->write_string($row,1,$thisans,$formatbc);
                $col++;
                }
                $col--;
            } else {
                $myxls->write_string($row,1,$quests[$qid]['correct'],$formatbc);
            }
        }
        //display a row for each possible multiple choice with $max_choices being highest row,$table_colcount is the width
        for ($i = 1; $i<= $max_choices;$i++){
            $label="M/C #$i";
            $myxls->write_string($row,0,$label,$formatbrt);
            //display answer tallies
            for ($j = 1; $j <= $table_colcount; $j++){
                //substitute "<br>" for a "//"
                $nowdata = $analysis[$i][$j];
                if($slashpos = strpos($nowdata,"//")){
                $text = substr($nowdata,0,$slashpos);
                $value = substr($nowdata,$slashpos+2);
                $myxls->write_string($row,$j,$text,$formatc);
                $myxls->write_string($row+1,$j,$value,$formatbc);
                } else {
                $myxls->write_string($row,$j,$nowdata,$formatc);
                }
            }
            $row = $row+2;
        }

        //Output the total percent correct
        $row++;

        $myxls->write_string($row,0,"Percent Correct:",$formatbrt);
        for ($i = 1; $i<= $table_colcount;$i++){
            $myxls->write_number($row,$i,$pct_correct[$i]/100,$formatbpct);
        }

    //Finally display the itemanalysis
        $row++;
        $myxls->write_string($row,0,"Discrimination Index",$formatbc);
        $myxls->write_string($row+1,0,"Top third",$formatbc);
        $myxls->write_string($row+2,0,"Bottom third",$formatbc);
        for ($i = 1; $i<= $table_colcount;$i++){
            if($bott_scores[$i] > 0) {
                $val = round(($top_scores[$i]/$bott_scores[$i]),1);
            } elseif ($top_scores[$i] ){
                $val = 10;
            } else {
                $val = 0;
            }
            $myxls->write_string($row,$i,$val,$formatbc);
            $myxls->write_string($row+1,$i,$top_scores[$i],$formatbc);
            $myxls->write_string($row+2,$i,$bott_scores[$i],$formatbc);
        }


           //Print the questions with responses on a new worksheet
        $myxls = &$workbook->add_worksheet('Questions and Responses');
        $sheettitle = "Questions and Responses";
        $myxls->write_string(0,0,$sheettitle,$formatb);
        $itemcount = 0;
        //Now printout the questions (and M/C answers if $containsMC

        $qcount = 0;
        $row = 1;
        foreach ($qs_in_order as $qid){
            if ($quests[$qid]['qtype']==5) { $itemcount = $itemcount + $match_number[$qid];} else {$itemcount++;}
            $row++;
            $qcount++;
            $label = "Q-$qcount";
            $myxls->write_string($row,0,$label,$formatb);
            $myxls->write_string($row,1,$quests[$qid]['qtext'],$formatb);
            if($quests[$qid]['qtype']==3){
                $nowchoices = $quests[$qid]['choice'];
                foreach($nowchoices as $thischoice){
                    $cno = $thischoice['choiceno'];
                    $row++;
                    $label = "A-$thischoice[choiceno]";
                    $nowstat =  $analysis[$cno][$itemcount];
                    $pct = qr_make_pct($nowstat,$total_user_count)/100;
                    $myxls->write_number($row,1,$nowstat,$formatb);
                    $myxls->write_number($row,2,$pct,$formatbpct);
                    $myxls->write_string($row,3,$thischoice[answer],$formatb);
                }
            }
            if($quests[$qid]['qtype']==2){
                //"True" responses
                $row++;
                $nowstat =  $analysis[1][$itemcount];
                $nowresp = substr($nowstat,5);
                $pct = qr_make_pct($nowresp,$total_user_count)/100;
                $myxls->write_number($row,1,$nowresp,$formatb);
                $myxls->write_number($row,2,$pct,$formatbpct);
                $myxls->write_string($row,3,'True',$formatb);
                //"False" responses
                $row++;
                $nowstat =  $analysis[2][$itemcount];
                $nowresp = substr($nowstat,6);
                $pct = qr_make_pct($nowresp,$total_user_count)/100;
                $myxls->write_number($row,1,$nowresp,$formatb);
                $myxls->write_number($row,2,$pct,$formatbpct);
                $myxls->write_string($row,3,'False',$formatb);
            }
        }

        $workbook->close();
        exit;
    }

        ////////---------------------------
        /// If a text file is wanted, produce one
        if ($download == "txt") {
        /// Print header to force download

        header("Content-Type: application/download\n"); 
        $downloadfilename = clean_filename("$course->shortname $quiz->name");
        header("Content-Disposition: attachment; filename=\"$downloadfilename.txt\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");

        /// Print names of all the fields

        echo "$quiz->name";
        echo "\n";
        
        /// Print all the user data
        $colcount = count($question_ids);
        foreach ($data_tally as $thisuserno=>$thisuser){
            foreach($thisuser as $thisattemptno=>$thisattempt){
                foreach($thisattempt as $thisitemkey=>$thisitem) {
                if ($thisitem['score'] < 1) {$mark = "";} else {$mark = "*";}
                //First two items are name & grade
                if ($thisitemkey < 2){
                    echo $thisitem . "\t";
                } elseif ($thisitemkey['qtype'] == 2){
                   echo $thisitem['data']['answer'] . "\t";
                } elseif ($thisitem['qtype'] == 5) {
                    if ($thisitem['score'] == 1) {$mark = "*";} else {$mark="";}
                    if(!$thisitem['data'][1]){$thisitem['data'][1]="(No Response)";}
                    echo "{$thisitem['data'][0]} -- $mark{$thisitem['data'][1]}\t";
                } else {
                    echo "$mark{$thisitem['data']}\t";
                }
                }
            }
            echo " \n";
        }
        echo " \n";
        echo "* Asterisk indicates correct response";
        echo " \n";
        //Question numbers
        for  ($i = 1;$i <= $colcount;$i++) {
            echo  "Q-$i\t";
        }
        echo " \n";
        //Q numbers
        foreach($qs_in_order as $qid){
            $qcount++;
            if($quests[$qid]['qtype'] == 5) {
                $i = 0;
                foreach ($match_qs[$qid] as $nowq){
                $i++;
                echo  "Q-$qcount M-$i\t";
                }
            } else {
                echo  "Q-$qcount\t";
            }
        }
        echo  " \n";
        //Repeat for q answers
        foreach($qs_in_order as $qid){
            $qcount++;
            if($quests[$qid]['qtype'] == 5) {
                foreach ($match_qs[$qid] as $nowq){
                echo $nowq . "\t";
                }
            } else {
                echo  "\t";
            }
        }
        echo  " \n";

        for ($i = 1; $i<= $max_choices;$i++){
            echo  "M/C #$i\t";
            //display answer tallies
            for ($j = 1; $j <= $table_colcount; $j++){
                //substitute "<br>" for a "//"
                $nowdata = $analysis[$i][$j];
                if (strpos($nowdata,"//")>0) {
                $nowdata = str_replace("//"," : ",$nowdata);
                }
                echo $nowdata . "\t";
            }
            echo  " \n";
        }
        exit;
        }
        ////////--------------------------- If it falls through both of the $download choices, print on screen
    //Print user responses
    print ("<table border=1 align=center width=95% cellpadding=2 cellspacing=0>\n");
    $totcolcount = $table_colcount+2;
    print("<tr><th colspan=$totcolcount>$strindivresp</th></tr>");
    qr_print_headers($data_tally,"$strname","$strgrade");
    //now print the lines of answers
    foreach ($data_tally as $thisuserno=>$thisuser){
        foreach($thisuser as $thisattemptno=>$thisattempt){
            print("<tr>");
            foreach($thisattempt as $thisitemkey=>$thisitem) {
            //$thisitemkeys 1 & 2 are name and total score
            //There needs to be a 3-way branch, keys0 & 1 just print $thisitem
            //else if $thisitem['qtype'] = 5, then processing for MATCH is needed
            //else the data to be printed is in $thisitem['data'] and $thisitem['score'] == 1 shows that the item was correct
                if ($thisitem['score'] < 1) {$thiscolor = "ff0000";} else {$thiscolor = "000000";}
                if ($thisitemkey == 0){
                    print("<th align='left'>$thisitem&nbsp;</th>");
                } elseif ($thisitemkey == 1){
                    print("<td align='right'>&nbsp;$thisitem%&nbsp;&nbsp;</td>");
                } elseif ($thisitemkey['qtype'] == 2){
                    print("<td>&nbsp;&nbsp;$thisitem[data][answer]&nbsp;&nbsp;</td>");
                } elseif ($thisitem['qtype'] == 5) {
                    if ($thisitem['score'] == 1) {$thiscolor = "blue";}
                    if(!$thisitem['data'][1]){$thisitem['data'][1]="($strnoresponse)";}
                    print("<td align=center><font size=-2>{$thisitem['data'][0]}<br><font color='$thiscolor'>{$thisitem['data'][1]}</font></font></td>");
                } elseif  ($thisitem['qtype'] == 3) {
                    if ($showtext) {
                        print("<td align=center><font color='$thiscolor' size=-2>&nbsp;&nbsp;{$thisitem['data']}&nbsp;&nbsp;</font></td>");
                    } else {
                        print("<td align=center><font color='$thiscolor'>&nbsp;&nbsp;{$thisitem['data']}&nbsp;&nbsp;</font></td>");
                    }
                } else {
                    print("<td align=center><font color='$thiscolor'>&nbsp;&nbsp;{$thisitem['data']}&nbsp;&nbsp;</font></td>");
                }
            }
        }
        print("</tr>\n");
    }
    print("</table><p>\n");
        
    if($debug and !$download){
    print("<h3>Qtally</h3>");
    print_object($qtally);
    }
    //print tally of how many selected each choice
    print ("<p><table width=95% border=1 align=center cellpadding=2 cellspacing=0>\n");
    print("<tr><th colspan=$totcolcount>$stritemanal</th></tr>");
    qr_print_headers($data_tally,"Item","&nbsp;");
    //display row with correct answers
    print("<tr><th colspan=2 align=right>$strcorrresp:</th>");
    foreach ($qs_in_order as $qid){
        if ($quests[$qid]['qtype'] == 5) {
            foreach($match_answers[$qid] as $thisans){
                print("<th align='center'>&nbsp;$thisans&nbsp;</th>");
            }
        } else {
            print("<th align='center'>&nbsp;{$quests[$qid]['correct']}&nbsp;</th>");
        }
    }
    print("</tr>\n");

    //display a row for each possible multiple choice with $max_choices being highest row,$table_colcount is the width
    for ($i = 1; $i<= $max_choices;$i++){
        print("<tr valign=top><th colspan=2 align=right>&nbsp;M/C #$i</td>");
        //display answer tallies
        for ($j = 1; $j <= $table_colcount; $j++){
           //substitute "<br>" for a "//"
           $nowdata = $analysis[$i][$j];
           if (strpos($nowdata,"//")>0) {$nowdata = str_replace("//","<br>",$nowdata);}
           print("<td align='center'>&nbsp;$nowdata</td>");
        
        }
    }
    print("</tr>\n");
    //Display the total percent correct
    print("<tr valign=top><th align=right colspan=2>$strpercentcorrect:</th>");

    for ($i = 0; $i< $table_colcount;$i++){
        print ("<th>{$pct_correct[$i]}</th> ");
    }
    print("</tr>\n");
    //Finally display the itemanalysis
    print("<tr valign=top valign='middle'><th align=right colspan=2>");
    helpbutton("discrimination", "", "quiz");
    print(" $strdiscrimination:</th>");
    for ($i = 1; $i<= $table_colcount;$i++){
        if($bott_scores[$i] > 0) {
            $val = round(($top_scores[$i]/$bott_scores[$i]),1);
        } elseif ($top_scores[$i] ){
            $val = 10;
        } else {
            $val = 0;
        }
        print ("<th valign='middle'><font size=-1>$val ({$top_scores[$i]}/{$bott_scores[$i]})</font></th> ");
    }
    print("</tr>\n");
    print("</table>\n");

    //Now printout the questions (and M/C answers if $containsMC

    print ("<p><table width=95% border=1 align=center cellpadding=2 cellspacing=0>\n");
    if ($containsMCTF){$ws = " ". $strwithsummary;} else {$ws = "";}
    print("<tr><th colspan=3>QUIZ: $quiz->name&nbsp;&nbsp; -- &nbsp;&nbsp;$strlistitems$ws</th></tr>\n");
    $qcount = 0;
    $itemcount = 0; //needed since matching Qs adds additional columns of data in $analysis
    foreach ($qs_in_order as $qid){
        $qcount++;
        if ($quests[$qid]['qtype']==5) { $itemcount = $itemcount + $match_number[$qid];} else {$itemcount++;}
        print("<tr valign=top><th  width='10%'>Q-$qcount</th><td colspan=2>{$quests[$qid]['qtext']}</td></tr>\n");
        if($quests[$qid]['qtype']==3){
            $nowchoices = $quests[$qid]['choice'];
            foreach($nowchoices as $thischoice){
                $cno = $thischoice['choiceno'];
                $nowstat =  $analysis[$cno][$itemcount];
                $pct_cor = qr_make_pct($nowstat,$total_user_count);
                print("<tr valign=top><td align='right' width='10%'>$nowstat ($pct_cor%)&nbsp;</td>");
                print("<td width='5%' align='center'><b>A-$cno</b></td><td>{$thischoice['answer']}</td></tr>\n");
            }
        }
        if($quests[$qid]['qtype']==2){
            //"True" responses
            $nowstat =  $analysis[1][$itemcount];
            $colpos = strpos($nowstat,":");
            $nowresp = substr($nowstat,$colpos+1);
            $pct_cor = qr_make_pct($nowresp,$total_user_count);
            print("<tr valign=top><td align='right'>$nowresp ($pct_cor%)&nbsp;</td>");
            print("<td colspan=2 align=left>True</td></tr>\n");
            //"False" responses
            $nowstat =  $analysis[2][$itemcount];
            $colpos = strpos($nowstat,":");
            $nowresp = substr($nowstat,$colpos+1);
            $pct_cor = qr_make_pct($nowresp,$total_user_count);
            print("<tr valign=top><td align='right'>$nowresp ($pct_cor%)&nbsp;</td>");
            print("<td colspan=2 align=left>False</td></tr>\n");
        }
    }
    print("</table>\n");
    
    echo "<br />\n";
    echo "<table border=0 align=center><tr>\n";
    echo "<td>";
    unset($options);
    $options["id"] = "$cm->id";
    $options["mode"] = "fullstat";
    $options["noheader"] = "yes";
    $options["download"] = "xls";
    print_single_button("report.php", $options, get_string("downloadexcel"));
    echo "<td>";
    $options["download"] = "txt";
    print_single_button("report.php", $options, get_string("downloadtext"));
    echo "</table>";
////////---------------------------
    return true;
    }
}
////just functions below here----------------------------------------------

function qr_quiz_responses($quiz) {
// Given any quiz number, get all responses and place in
// $response object
    global $CFG;
   
   $resp_recs =get_records_sql("SELECT r.id as rid, r.attempt, r.answer, r.question, a.attempt as attemptno, a.id as aid, a.quiz, a.userid, a.sumgrades, u.id as uid, u.lastname, u.firstname FROM {$CFG->prefix}quiz_responses r, {$CFG->prefix}quiz_attempts a, {$CFG->prefix}user u WHERE a.id = r.attempt AND a.quiz = '$quiz' AND a.userid = u.id ORDER BY u.lastname ASC, u.firstname ASC, r.id ASC");
    return $resp_recs;
}

function qr_make_satally($qid,$col){
    global $analysis, $qtally,$max_choices;
    $this_sa = $qtally[$qid]['response'];
    $rowcnt = 0;
    if ($this_sa){
        foreach($this_sa as $thistext =>$thistally){
            $rowcnt++;
            $analysis[$rowcnt][$col] = $thistext . "//" . $thistally;
        }
    }
    if ($rowcnt > $max_choices) {$max_choices = $rowcnt;}
    return 1;
}

function qr_make_tftally($qid,$col){
    global $analysis, $qtally;
    $this_tf = $qtally[$qid];
    foreach($this_tf as $thiskey=>$tallycnt){
        if ($thiskey == "True") {
            $analysis[1][$col] = "True: " .  $tallycnt;
        } else if ($thiskey == "False") {
            $analysis[2][$col] = "False: " . $tallycnt;
        }
    }
    return 1;
}

function qr_make_numtally($qid,$col){
    global $analysis, $qtally;
    $this_num = $qtally[$qid];
    $rowcnt = 0;
    if ($this_num){
        foreach($this_num['response'] as $thisans=>$thistally){
            if($thisans){
                $rowcnt++;
                $analysis[$rowcnt][$col] = $thisans . "//" . $thistally;
            }
        }
    }
    if ($rowcnt > $max_choices) {$max_choices = $rowcnt;}
    return 1;
}

function qr_make_matchtally($qid){
    //The MATCH items need to be inverted so that the 1st of each match can be printed in the first row, then the second, etc.
    global $qtally;
    $itemcntA = 0;
    foreach ($qtally[$qid] as $thiskey=>$thisitem){
        if($thiskey != "correct"){
            $itemcntA++;
            $itemcntB = 0;
            if (gettype($thisitem) == "array"){
                foreach ($thisitem as $thisrid=>$thisans){
                    if (!$thisans['answer']){continue;}
                    $itemcntB++;
                    $inverted[$itemcntB][$itemcntA]['answer'] = $thisans['answer'];
                    $inverted[$itemcntB][$itemcntA]['tally'] = $thisans['tally'];
                }
            }
        }
    }
return $inverted;
}

function qr_print_headers($data_tally,$nm,$gd){
    global  $qs_in_order,$qtally,$quests,$total_user_count,$match_number,$strquestion;
    $qcount = 0;
    if($nm == "Item") {
        print("<tr><th colspan=2 align=right>$strquestion:</th>");
    } else {
        print("<tr><th>$nm</th><th width='5%'>$gd</th>");
    }
    foreach($qs_in_order as $qid){
        $qcount++;
        if($quests[$qid]['qtype'] == 5) {
            $colcount = $match_number[$qid];
        } else {
            $colcount = 1;
        }
        print("<th colspan=$colcount>Q-$qcount</th>");
    }
    print("</tr>\n");
}

function qr_make_footers(){
    //Create the percent correct for the footer
    global  $qs_in_order,$qtally,$quests,$total_user_count;
    foreach($qs_in_order as $qid){
        if($quests[$qid]['qtype'] == 5) {
            foreach ($qtally[$qid] as $thisitem){
                $this_correct = $thisitem['correct'];
                $footers[] = qr_make_pct($this_correct,$total_user_count);
            }
        } else {
            $this_correct = $qtally[$qid]['correct'];
            $footers[] = qr_make_pct($this_correct,$total_user_count);
        }
    }
    return $footers;
}

function qr_make_pct($this_correct,$totusers){
    global  $qs_in_order,$qtally,$quests,$total_user_count;
    if($this_correct>0 and $totusers > 0){
        $pct_cor =(floor(($this_correct/$totusers)*1000)/10);
    } else {
        $pct_cor = 0;
    }
    return $pct_cor ;
}

function qr_answer_lookup($qid,$thisanswer){
    //For each type of question, this needs to determine answer string to report and whether right or wrong
    global $quests,$qtally,$max_choices,$thismin,$thismax,$showtext;
    $thistype = $quests[$qid]['qtype'];
    $returndata['data'] = "--";
    $returndata['score'] = 0;
    $returndata['qtype'] = $thistype;
    $returndata['qid'] = $qid;
    $qtally[$qid]['qtype'] = $thistype;
    if ($thisanswer){
        switch ($thistype) {
            case 1:  //SHORTANSWER
                $returndata['data'] = $thisanswer;
                $qtally[$qid]['response'][$thisanswer]++;
                //convert all to lowercase to allow for mismatching cases to be correct
                if (strpos(strtolower($quests[$qid]['correct']),trim(strtolower($thisanswer))) >-1){
                    $qtally[$qid]['correct']++;
                    $returndata['score'] = 1;
                }
                break;
            case 2:  //TRUEFALSE
                $returndata['data'] = $quests[$qid]['choice'][$thisanswer]['answer'];
                $qtally[$qid][$quests[$qid]['choice'][$thisanswer]['answer']]++;
                if ($quests[$qid]['correct']==$quests[$qid]['choice'][$thisanswer]['answer']){
                    $returndata['score'] = 1;
                    $qtally[$qid]['correct']++;
                }
                break;
            case 3:  //MULTICHOICE
                $thischoiceno = $quests[$qid]['choice'][$thisanswer]['choiceno'];
                if ($showtext){
                    $returndata['data'] = $quests[$qid]['choice'][$thisanswer]['answer'];
                } else {
                    $returndata['data'] = $thischoiceno;
                }
//                if($max_choices < $returndata['data']) {$max_choices = $returndata['data'];}
                if ($max_choices < $thischoiceno) {$max_choices = $thischoiceno;}
                $qtally[$qid][$quests[$qid]['choice'][$thisanswer]['choiceno']]++;
                if (strtolower($quests[$qid]['correct'])==strtolower($quests[$qid]['choice'][$thisanswer]['choiceno'])){
                    $returndata['score'] = 1;
                    $qtally[$qid]['correct']++;
                }
                break;
            case 8:  //NUMERICAL
                $returndata['data'] = $thisanswer;
//                $returndata['data'] = $thismin . "<" . $thisanswer . ">" . $thismax;
                $qtally[$qid]['response'][$thisanswer]++;
                if ($thisanswer >= $thismin[$qid] and $thisanswer <= $thismax[$qid]){
                    $qtally[$qid]['correct']++;
                    $returndata['score'] = 1;
                }
                break;
        }
    }
    return $returndata;
}

function qr_getqs($quiz){
// Returns a list of question numbers for a specific quiz
 if (!$questions = get_record("quiz","id",$quiz)) {
        notify("Could not find any questions for quiz $quiz");
        return false;
    }
    $qlist = array();
    $qlist = explode(",",$questions->questions);
    return $qlist;
}

function qr_match_array($nowQ){
    //builds an array of all questions and answers for match questions in the quiz for use in qr_match_table
    global $quiz_matches,$quiz_match_hdrs;
//make an array of all Q & As for match questions
//format:  $quiz_matches['quiz_match_sub_id'][Q or A]
    $allmatch = get_records("quiz_match_sub","question",$nowQ);
//    //print_object($allmatch);
    $hdrcnt=0;
    foreach($allmatch as $thismatchitemno =>$thismatchitem){
        $hdrcnt++;
        $quiz_matches[$thismatchitemno]["Q"] = $thismatchitem->questiontext;
        $quiz_matches[$thismatchitemno]["A"] = $thismatchitem->answertext;
    }
    //needed so that we know how many column headers to create
    $quiz_match_hdrs[$nowQ]=$hdrcnt;
    return $quiz_matches;
}

function qr_match_table($resplist){
    global $quiz_matches;
    $tbl = "\n<table border=0 cellspacing=0 cellpadding=2 align=center><tr valign='middle' align='center'>";
    $resp_array = explode(",",$resplist);
    $q_cnt=0;
    foreach ($resp_array as $resp_pair){
        $q_cnt++;
        $tbl = $tbl ."<td><font size-1> $q_cnt</font></td>";
    }
    $tbl = $tbl . "</tr>\n<tr valign=middle>";
    foreach ($resp_array as $resp_pair){
        $resp_QA = explode("-",$resp_pair);
        if ($resp_QA[0] == $resp_QA[1]){
          $qa = "<b> <font size=-2>{$quiz_matches[$resp_QA[0]]['Q']}</font>&nbsp;- <font color='blue' size=-2>{$quiz_matches[$resp_QA[1]]['A']}</font></b>";
        } else{
          $qa = "<b><font size=-2> {$quiz_matches[$resp_QA[0]]['Q']}</font>&nbsp;- <font color='red' size=-2> {$quiz_matches[$resp_QA[1]]['A']}</font></b>";
        }
//        $qa = $resp_QA[0]  . "=" . $resp_QA[1] ;
        $tbl = $tbl . "<td>$qa</td>";
    }
    $tbl = $tbl . "</tr>\n</table>\n";
    return $tbl;
}
?>
