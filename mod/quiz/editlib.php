<?php // $Id$
/**
 * Functions used by edit.php to edit quizzes
 *
 * @author Martin Dougiamas and many others. This has recently been extensively
 *         rewritten by members of the Serving Mathematics project
 *         {@link http://maths.york.ac.uk/serving_maths}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

require_once("locallib.php");

/**
* Delete a question from a quiz
*
* Deletes a question or a pagebreak from a quiz by updating $quiz
* as well as the quiz, quiz_question_instances
* @return boolean         false if the question was not in the quiz
* @param int $id          The id of the question to be deleted
* @param object $quiz  The extended quiz object as used by edit.php
*                         This is updated by this function
*/
function quiz_delete_quiz_question($id, &$quiz) {
    global $DB;
    // TODO: For the sake of safety check that this question can be deleted
    // safely, i.e., that it is not already in use.
    $questions = explode(",", $quiz->questions);

    // only do something if this question exists
    if (!isset($questions[$id])) {
        return false;
    }

    $question = $questions[$id];
    unset($questions[$id]);
    $quiz->questions = implode(",", $questions);
    // save new questionlist in database
    if (!$DB->set_field('quiz', 'questions', $quiz->questions, array('id' => $quiz->instance))) {
        print_error('cannotsavequestion', 'quiz');
    }
    $DB->delete_records('quiz_question_instances', array('quiz' => $quiz->instance, 'question'=> $question));
    return true;
}


/**
* Add a question to a quiz
*
* Adds a question to a quiz by updating $quiz as well as the
* quiz and quiz_question_instances tables. It also adds a page break
* if required.
* @return boolean         false if the question was already in the quiz
* @param int $id          The id of the question to be added
* @param object $quiz  The extended quiz object as used by edit.php
*                         This is updated by this function
* @param int $page  Which page in quiz to add the question on; if 0 (default), add at the end
*/
function quiz_add_quiz_question($id, &$quiz, $page=0) {
    global $DB;
    $questions = explode(",", $quiz->questions);
    if (in_array($id, $questions)) {
        return false;
    }

    // remove ending page break if it is not needed
    if ($breaks = array_keys($questions, 0)) {
        // determine location of the last two page breaks
        $end = end($breaks);
        $last = prev($breaks);
        $last = $last ? $last : -1;
        if (!$quiz->questionsperpage or
                (($end - $last -1) < $quiz->questionsperpage)) {
            array_pop($questions);
        }
    }
    if(is_int($page) && $page >= 1){
        $numofpages=quiz_number_of_pages($quiz->questions);
        if ($numofpages<$page){
            //the page specified does not exist in quiz
            $page=0;
        }else{
            // add ending page break - the following logic requires doing
            //this at this point
            $questions[] = 0;
            $currentpage=1;
            $addnow=false;
            foreach ($questions as $question){
                if($question==0){
                    $currentpage++;
                    //The current page is the one after the one we want to add on,
                    //so we add the question before adding the current page.
                    if ($currentpage==$page+1){
                        $questions_new[]=$id;
                    }
                }
                $questions_new[]=$question;
            }
            $questions=$questions_new;
        }
    }
    if ($page==0){
        // add question
        $questions[] = $id;
        // add ending page break
        $questions[] = 0;
    }

    // Save new questionslist in database
    $quiz->questions = implode(",", $questions);
    if (!$DB->set_field('quiz', 'questions', $quiz->questions, array('id' => $quiz->id))) {
        print_error('cannotsavequestion', 'quiz');
    }

    // update question grades
    $questionrecord = $DB->get_record('question', array('id' => $id));
    $quiz->grades[$id]
            = $questionrecord->defaultgrade;
    quiz_update_question_instance($quiz->grades[$id], $id, $quiz->instance);

    return true;
}

/**
* Save changes to question instance
*
* Saves changes to the question grades in the quiz_question_instances table.
* It does not update 'sumgrades' in the quiz table.
* @return boolean         Indicates success or failure.
* @param integer grade    The maximal grade for the question
* @param integer $questionid  The id of the question
* @param integer $quizid  The id of the quiz to update / add the instances for.
*/
function quiz_update_question_instance($grade, $questionid, $quizid) {
    global $DB;
    if ($instance = $DB->get_record('quiz_question_instances', array('quiz' => $quizid, 'question' => $questionid))) {
        $instance->grade = $grade;
        return $DB->update_record('quiz_question_instances', $instance);
    } else {
        unset($instance);
        $instance->quiz = $quizid;
        $instance->question = $questionid;
        $instance->grade = $grade;
        return $DB->insert_record("quiz_question_instances", $instance);
    }
}

/**
* Prints a list of quiz questions for the edit.php main view for edit
* ($reordertool=false) and order and paging ($reordertool=true) tabs
*
* @return int sum of maximum grades
* @param object $quiz This is not the standard quiz object used elsewhere but
*     it contains the quiz layout in $quiz->questions and the grades in
*     $quiz->grades
* @param object $pageurl The url of the current page with the parameters required 
*     for links returning to the current page, as a moodle_url object
* @param boolean $allowdelete Indicates whether the delete icons should be displayed
* @param boolean $reordertool  Indicates whether the reorder tool should be displayed
* @param boolean $quiz_qbanktool  Indicates whether the question bank should be displayed
* @param boolean $hasattempts  Indicates whether the quiz has attempts
*/
function quiz_print_question_list($quiz, $pageurl, $allowdelete=true,
        $reordertool=false, $quiz_qbanktool=false,
        $hasattempts=false) {
    global $USER, $CFG, $QTYPES, $DB;
    $strorder = get_string("order");
    $strquestionname = get_string("questionname", "quiz");
    $strgrade = get_string("grade");
    $strremove = get_string('remove', 'quiz');
    $stredit = get_string("edit");
    $strview = get_string("view");
    $straction = get_string("action");
    $strmove = get_string("move");
    $strmoveup = get_string("moveup");
    $strmovedown = get_string("movedown");
    $strsave=get_string('save',"quiz");
    $strreorderquestions=get_string("reorderquestions","quiz");

    $strselectall = get_string("selectall", "quiz");
    $strselectnone = get_string("selectnone", "quiz");
    $strtype = get_string("type", "quiz");
    $strpreview = get_string("preview", "quiz");

    /*if (!$quiz->questions) {
        echo "<p class=\"quizquestionlistcontrols\">";
        print_string("noquestions", "quiz");
        echo "</p>";
        return 0;
    }*/
    if($quiz->questions){
        list($usql, $params) = $DB->get_in_or_equal(explode(',', $quiz->questions));
        $questions = $DB->get_records_sql("SELECT q.*,c.contextid
                              FROM {question} q,
                                   {question_categories} c
                             WHERE q.id $usql
                               AND q.category = c.id", $params);
    }
    // if user only has empty pages we have to keep that: out with OR (!$questions)
    if (!$quiz->questions) {
        $pagecount=1;
        echo  '<div class="quizpage"><span class="pagetitle">Page&nbsp;'.
                $pagecount.'</span><div class="pagecontent"><div class="pagestatus">';
        print_string("noquestionsinquiz", "quiz");
        echo '</div>';
        if(!$reordertool){
            quiz_print_pagecontrols($quiz, $pageurl, $pagecount,$hasattempts);
        }
        echo "</div></div></div>";
        //this is how it worked before summer 2008
        //OR !$questions
        if(!isset($questions)){
            return 0;
        }
    }


    $layout=quiz_clean_layout($quiz->questions);
    $order = explode(',', $layout);
    $lastindex = count($order)-1;

    if ($hasattempts){
        $disabled='disabled="disabled"';
        $movedisabled='';
        $pagingdisabled='';
    }else{
        $disabled='';
        $movedisabled='';
        $pagingdisabled='';
    }
    if($quiz->shufflequestions){
        $movedisabled='disabled="disabled"';
    }
    if($quiz->questionsperpage){
        $pagingdisabled='disabled="disabled"';
    }


    $reordercontrolssetdefaultsubmit='<div style="display:none;">'.
        '<input type="submit" name="savechanges" value="'.
        $strreorderquestions.'" '.$movedisabled.' /></div>';
    $reordercontrols1='<div class="addnewpagesafterselected">'.
        '<input type="submit" name="addnewpagesafterselected" value="'.
        get_string("addnewpagesafterselected","quiz").'"  '.
        $pagingdisabled.' /></div>';
    $reordercontrols1.='<div class="quizdeleteselected">'.
        '<input type="submit" name="quizdeleteselected" '.
        'onclick=\'return confirm("'.
        get_string("areyousuredeleteselected","quiz").'")\'; value="'.
        get_string("deleteselected").'"  '.$disabled.' /></div>';

    $reordercontrols2top='<div class="moveselectedonpage">'.
        get_string("moveselectedonpage","quiz") .
        ': <input name="moveselectedonpagetop" type="text" size="2" '.
        $pagingdisabled.'  />'.
        '<input type="submit" name="savechanges" value="'.
        $strmove.'"  '.$pagingdisabled.' />'.'
        <br /><input type="submit" name="savechanges" value="'.
        $strreorderquestions.'"  '.$movedisabled.' /></div>';
    $reordercontrols2bottom='<div class="moveselectedonpage">'.
        '<input type="submit" name="savechanges" value="'.
        $strreorderquestions.'"  '.$movedisabled.' /><br />'.
        get_string("moveselectedonpage","quiz") .
        ': <input name="moveselectedonpagebottom" type="text" size="2"  '.
        $pagingdisabled.' />'.'<input type="submit" name="savechanges" value="'.
        $strmove.'"  '.$pagingdisabled.' /> '.'</div>';

    $reordercontrols3='<a href="javascript:select_all_in(\'FORM\',null,'.
            '\'quizquestions\');">'.
            $strselectall.'</a> /';
    $reordercontrols3.=    ' <a href="javascript:deselect_all_in(\'FORM\','.
            'null,\'quizquestions\');">'.
            $strselectnone.'</a>';

    $reordercontrolstop='<div class="reordercontrols">'.
            $reordercontrolssetdefaultsubmit.
            $reordercontrols1.$reordercontrols2top.$reordercontrols3."</div>";
    $reordercontrolsbottom='<div class="reordercontrols">'.
            $reordercontrolssetdefaultsubmit.
            $reordercontrols2bottom.$reordercontrols1.$reordercontrols3."</div>";

    if($reordertool){

        echo '<form method="post" action="edit.php" id="quizquestions"><div>';

        echo $pageurl->hidden_params_out();
        echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';

        echo $reordercontrolstop;
    }

    /* // Tim: is this a separate case from the above "no questions in quiz"? Should it still be done?:
    if(!$quiz->questions){
        $pagecount=1;
        echo  '<div class="quizpage"><span class="pagetitle">Page&nbsp;'.
                $pagecount.'</span><div class="pagecontent">';
        print_string("noquestions", "quiz");
        quiz_print_pagecontrols($quiz, $pageurl, $pagecount);
        echo "</div></div>";

    }*/

    //the current question ordinal (no descriptions)
    $qno = 1;
    //the current question (includes questions and descriptions)
    $questioncount=0;
    //the ordinal of current element in the layout
    //(includes page breaks, questions and descriptions)
    $count = 0;
    //the current page number in iteration
    $pagecount = 0;

    $sumgrade = 0;

    $pageopen=false;


    $returnurl = $pageurl->out();
    $questiontotalcount=count($order);

    foreach ($order as $i => $qnum) {

        $reordercheckbox='';
        $reordercheckboxlabel='';
        $reordercheckboxlabelclose='';

        if ($qnum and empty($questions[$qnum])) {
            continue;
        }
        // If the questiontype is missing change the question type
        if ($qnum and !array_key_exists($questions[$qnum]->qtype, $QTYPES)) {
            $questions[$qnum]->qtype = 'missingtype';
        }
        $deletex="delete.gif";
        if($qnum!=0 OR ($qnum==0&&!$pageopen)){
            //this is either a question or a page break after another
            //        (no page is currently open)
            if(!$pageopen){
                //if no page is open, start display of a page
                $pagecount++;
                echo  '<div class="quizpage"><span class="pagetitle">'.
                        get_string('page').'&nbsp;'.$pagecount.
                        '</span><div class="pagecontent">';
                $pageopen=true;
            }
            if($qnum==0 && $i<$questiontotalcount){
                //this is a consequent 0 (signaling empty page), tell
                //        the user the page is empty
                echo '<div class="pagestatus">';
                print_string("noquestionsonpage", "quiz");
                echo '</div>';
                if ($allowdelete && !$quiz->questionsperpage) { // remove from quiz, not question delete.
                    echo '<div class="quizpagedelete">';
                    echo "<a title=\"".get_string("removeemptypage","quiz")."\" href=\"".
                            $pageurl->out_action(array('deleteemptypage'=>$i)).
                            "\"><img src=\"$CFG->pixpath/t/delete.gif\" ".
                            "class=\"iconsmall\"".
                            " alt=\"$strremove\" /></a>";
                    echo '</div>';
                }
            }

            if($qnum!=0){
                $question = $questions[$qnum];
                $questionparams = array('returnurl' => $returnurl,
                        'cmid'=>$quiz->cmid, 'id' => $question->id);
                $questionurl = new moodle_url("$CFG->wwwroot/question/question.php",
                        $questionparams);
                $questioncount++;
                //this is an actual question


                /* Display question start */
?>
<div class="question">
    <div class="questioncontainer">
        <div class="qnum">
        <?php
            $reordercheckbox="";
            $reordercheckboxlabel='';
            $reordercheckboxlabelclose='';
            if($reordertool){
                $reordercheckbox='<input type="checkbox" name="s'.$question->id.
                    '" id="s'.$question->id.'" />';
                $reordercheckboxlabel='<label for="s'.$question->id.'">';
                $reordercheckboxlabelclose='</label>';
            }
            if (!$quiz->shufflequestions) {
                // Print and increment question number
                $questioncountstring="";
                if ($questioncount>999 OR ($reordertool && $questioncount>99)){
                    $questioncountstring=
                            "$reordercheckboxlabel<small>$questioncount</small>".
                            $reordercheckboxlabelclose.$reordercheckbox;
                }else{
                    $questioncountstring=$reordercheckboxlabel.$questioncount.
                            $reordercheckboxlabelclose.$reordercheckbox;
                }
                echo $questioncountstring;
                $qno += $question->length;
            } else {
                echo "$reordercheckboxlabel * $reordercheckboxlabelclose".
                        " $reordercheckbox";
            }

            ?>
        </div>
        <div class="content">
            <div class="questioncontrols">
                <?php
            if ($count != 0) {
                if(!$hasattempts){
                    $upbuttonclass="";
                    if (!($count < $lastindex-1)) {
                        $upbuttonclass="upwithoutdown";
                    }
                    echo "<a title=\"$strmoveup\" href=\"".
                            $pageurl->out_action(array('up'=>$count))."\"><img
                             src=\"$CFG->pixpath/t/up.gif\" class=\"iconsmall
                            $upbuttonclass\" alt=\"$strmoveup\" /></a>";
                }

            }
            if ($count < $lastindex-1) {
                if(!$hasattempts){
                    echo "<a title=\"$strmovedown\" href=\"".
                            $pageurl->out_action(array('down'=>$count))."\"><img
                            src=\"$CFG->pixpath/t/down.gif\" class=\"iconsmall\"".
                            " alt=\"$strmovedown\" /></a>";
                }
            }else{
            }
            if ($allowdelete && question_has_capability_on($question, 'use',
                    $question->category)) { // remove from quiz, not question delete.
                if(!$hasattempts){
                    echo "<a title=\"$strremove\" href=\"".
                            $pageurl->out_action(array('delete'=>$count))."\">
                            <img src=\"$CFG->pixpath/t/delete.gif\" ".
                            "class=\"iconsmall\" alt=\"$strremove\" /></a>";
                }
            }
                ?>
            </div><?php
            if ($question->qtype != 'description' && !$reordertool) {
                ?>
<div class="points">
<form method="post" action="edit.php"><div>
    <label for="<?php echo "inputq$qnum" ?>"><?php echo $strgrade; ?></label>:<br />
    <fieldset class="invisiblefieldset" style="display: block;">
    <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey ?>" />
    <?php echo $pageurl->hidden_params_out(); ?>
    <input type="hidden" name="savechanges" value="save" />
        <?php
            echo '<input type="text" name="q'.$qnum.'" size="' . ($quiz->decimalpoints + 2) . '"
                    value="'.(0 + $quiz->grades[$qnum]).
                    '" tabindex="'.($lastindex+$qno).'" />';
            ?>
        <input type="submit" class="pointssubmitbutton" value="<?php echo $strsave; ?>" />
    </fieldset>
<?php if(strcmp($question->qtype,'random')===0){
    echo '<a href="'.$questionurl->out().'" class="configurerandomquestion">'.get_string("configurerandomquestion","quiz").'</a>';
}
            
?>
</div>
</form>

            </div>
<?php
            }else if ($reordertool) {
                if ($qnum) {
                ?>
<div class="qorder">
        <?php
                    echo '<input type="text" name="o'.$i.'" size="2" value="'.
                            (10*$count+10).
                             '" tabindex="'.($lastindex+$qno).
                             '" '.$movedisabled.' />';
        ?>
<!--         <input type="submit" class="pointssubmitbutton" value="<?php
        echo $strsave; ?>" /> -->
</div>
<?php
                }
            }
?>
            <div class="questioncontentcontainer">
 <?php
            //strcmp returns 0 if equal
            if (strcmp($question->qtype,'random')===0){ // it is a random question
                if(!$reordertool){
                    quiz_print_randomquestion($question, $pageurl, $quiz,
                            $quiz_qbanktool);
                }else{
                    quiz_print_randomquestion_reordertool($question,
                            $pageurl, $quiz);
                }
            }else{ // it is a single question
                if(!$reordertool){
                    quiz_print_singlequestion($question, $questionurl, $quiz);
                }else{
                    quiz_print_singlequestion_reordertool($question,
                            $questionurl, $quiz);
                }
            }
                ?>
            </div>
        </div>
    </div>
</div>

    <?php
            /* Display question end */
                $count++;
                $sumgrade += $quiz->grades[$qnum];

            }
        }
        //a page break: end the existing page.
        if($qnum == 0){
            if($pageopen){
                if(!$reordertool){
                    quiz_print_pagecontrols($quiz, $pageurl, $pagecount,
                            $hasattempts);
                }else if ($i<$questiontotalcount-1){
                    //do not include the last page break for reordering
                    //to avoid creating a new extra page in the end
                    echo '<input type="hidden" name="o'.$i.'" size="2" value="'.
                            (10*$count+10).'" />';
                }
                echo "</div></div>";

                if(!$reordertool){
                    echo "<div class=\"addpage\">";
                    print_single_button($pageurl->out(true),
                            array("cmid"=>$quiz->cmid,
                                    "courseid"=>$quiz->course,
                                    "addpage"=>$count,
                                    "sesskey"=>sesskey()),
                            get_string("addpagehere","quiz"),
                             'get',
                             '_self',
                            false,
                            '',
                            $hasattempts);
                    echo "</div>";
                }
                $pagecount;
                $pageopen=false;
                $count++;
            }
        }

    }
    if($reordertool){
        echo $reordercontrolsbottom;
        echo '</div></form>';
    }



    return $sumgrade;
}

/**
 * Print all the controls for adding questions directly into the
 * specific page in the edit tab of edit.php
 *
 * @param unknown_type $quiz
 * @param unknown_type $pageurl
 * @param unknown_type $page
 * @param unknown_type $hasattempts
 */
function quiz_print_pagecontrols($quiz,$pageurl,$page, $hasattempts){
    global $CFG;
    $strcreatenewquestion=get_string("createnewquestion",'quiz');
    $strselectquestiontype=get_string("selectquestiontype",'quiz');
    echo '<div class="pagecontrols">';
    // get the current context
    $thiscontext = get_context_instance(CONTEXT_COURSE, $quiz->course);
    $contexts = new question_edit_contexts($thiscontext);
    // get default category and turn its infor into a string that works in an url
    $defaultcategory = question_make_default_categories($contexts->all());
    $categorystring = "$defaultcategory->id,$defaultcategory->contextid";
    //create the url the question page will return to
    $returnurl_addtoquiz=new moodle_url($pageurl->out(true),
            array("addonpage"=>$page));
    //create the url of the new question page to forward to. return url is given
    //as a parameter and automatically urlencoded.
    $newquestionparams = array('returnurl' => $returnurl_addtoquiz->out(false),
            'cmid'=>$quiz->cmid, "appendqnumstring"=>"addquestion", "category"=>$categorystring);
    $newquestionurl_object = new moodle_url("$CFG->wwwroot/question/question.php",
            $newquestionparams);
    $newquestionurl=$newquestionurl_object->out(false);
    echo get_string("addquestion","quiz").": ";
    if ($hasattempts) {
        $disabled = 'disabled="disabled"';
    } else {
        $disabled = '';
    }
    popup_form ($newquestionurl.'&amp;qtype=',
                question_type_menu(),
                "addquestion_$page",
                "",
                $strselectquestiontype,
                "",
                "",
                false,
                "self",
                "",
                null,
                $strcreatenewquestion, $hasattempts);
    helpbutton("questiontypes", $strcreatenewquestion, "quiz");
    echo '<div class="adddescription">';
    print_single_button($CFG->wwwroot."/question/question.php",
            array("cmid"=>$quiz->cmid,
                  "courseid"=>$quiz->course,
                  "returnurl"=>$returnurl_addtoquiz->out(false),
                  "appendqnumstring"=>"addquestion",
                  "category"=>$categorystring,
                  "qtype"=>"description"),
            get_string("adddescriptionlabel","quiz"),'get', '_self', false, '',
                    $hasattempts);
    echo "\n</div>";
    ?>
    <div class="addrandomquestion">
    <div class="singlebutton">
        <form class="randomquestionform" action="<?php echo $CFG->wwwroot; ?>/mod/quiz/addrandom.php" method="get">
            <div>
                <input type="hidden" class="addonpage_formelement" name="addonpage_form" value="<?php echo $page; ?>" />
                <input type="hidden" name="cmid" value="<?php echo $quiz->cmid; ?>" />
                <input type="hidden" name="courseid" value="<?php echo $quiz->course; ?>" />
                <input type="hidden" name="returnurl" value="<?php echo urlencode($pageurl->out(true)); ?>" />
                 <input type="submit" id="addrandomdialoglaunch_<?php echo $page; ?>" value="<?php echo get_string("addrandomquestion","quiz"); ?>" <?php echo " $disabled"; ?> />
                 <!--<a href="#"  id="addrandomdialoglaunch_<?php echo $page; ?>">laa</a>-->
                 <?php helpbutton('random', get_string('random', 'quiz'), 'quiz', true, false, '');
                  ?>
            </div>
        </form>
    </div>
    </div>
    <?php
    echo "\n</div>";
}
/**
 * Process submitted form data to create a new category for a random question
 * This is used by edit.php and addrandom.php
 * cmid
 *
 * @param object $qcobject
 * @return object an object with properties newrandomcategory and addonpage if operation successful.
 *      if operation failed, returns false.
 */
function quiz_process_randomquestion_formdata(&$qcobject){
    global $CFG,$DB;
    $newrandomcategory=0;
    $addonpage=0;
    $newquestioninfo=false;
    if ($qcobject->catform_rand->is_cancelled()){
        return false;
    }elseif ($catformdata = $qcobject->catform_rand->get_data()) {
        $newquestioninfo=new stdClass;
        $addonpage=$catformdata->addonpage;
        $newquestioninfo->addonpage=$catformdata->addonpage;
        if (!$catformdata->id) {//new category
            $newrandomcategory=$qcobject->add_category($catformdata->parent,
                    $catformdata->name, $catformdata->info,true);
            if(!is_null($newrandomcategory)){
                if (! $newcategory = $DB->get_record('question_categories',
                        array('id'=>$newrandomcategory))) {
                    return false;
                }
            }else{
                return false;
            }
        } else {
            return false;
        }
        $newquestioninfo->newrandomcategory=$newrandomcategory;
    }
    return($newquestioninfo);
}


/**
 * Print a simple question list of the questions in a question bank category.
 * Used for random question display in the edit tab of edit.php
 */
function quiz_simple_question_list($pageurl, $categorylist, $numbertoshow=3,
        $showhidden=false, $sortorderdecoded='qtype, name ASC',
        $showquestiontext = true){
    global $DB;

    // hide-feature
    $showhidden = $showhidden ? '' : " AND hidden = '0'";
    $categorylist_array =  explode(',', $categorylist);
    list($usql, $params) = $DB->get_in_or_equal($categorylist_array);

    if (!$questions = $DB->get_records_select('question',
            "category $usql AND parent = '0' $showhidden",
            $params, $sortorderdecoded, '*', 0, $numbertoshow)) {
        // There are no questions on the requested page.
        $page = 0;
        if (!$questions = $DB->get_records_select('question',
                "category $usql AND parent = '0' $showhidden",
                $params, $sortorderdecoded, '*', 0, $numbertoshow)) {
            // There are no questions at all
            return;
        }
    }
    foreach ($questions as $question) {
        echo "<li>";
        quiz_question_tostring($question,true, $showquestiontext, false);
        echo "</li>";
    }
}
/**
 * Print a given single question in quiz for the edit tab of edit.php.
 * Meant to be used from quiz_print_question_list()
 * 
 * @param object $question A question object from the database questions table
 * @param object $questionurl The url of the question editing page as a moodle_url object
 * @param object $quiz The quiz in the context of which the question is being displayed
 * 
 */
function quiz_print_singlequestion(&$question, &$questionurl, &$quiz){
    $stredit = get_string("edit");
    $strview = get_string("view");

    global $COURSE,$QTYPES,$CFG;
    ?>
    <div class="singlequestion">
            <?php
            $formatoptions = new stdClass;
            $formatoptions->noclean = false;
            $formatoptions->para = false;
            $formatoptions->newlines = false;
            if (question_has_capability_on($question, 'edit', $question->category)
                    || question_has_capability_on($question, 'move',
                    $question->category)) {
               echo "<a title=\"$stredit\" href=\"".$questionurl->out()."\">".
                        quiz_question_tostring($question,false).
                        '<span class="editicon">'.
                        "<img src=\"$CFG->pixpath/t/edit.gif\" alt=\"".
                        get_string("edit")."\" /></span>".
                        "</a>";
            }
            elseif (question_has_capability_on($question, 'view',
                    $question->category)){
               echo "<a title=\"$strview\" href=\"".
                           $questionurl->out(false, array('id'=>$question->id))."\">".
                        quiz_question_tostring($question,false).
                        '<span class="editicon">'.
                        "<img src=\"$CFG->pixpath/i/info.gif\" ".
                        "alt=\"$strview\" /></span>".
                        "</a>";
            }else{
                quiz_question_tostring($question,false,true,false);
            }
            echo '<span class="questiontype">';
            $namestr = $QTYPES[$question->qtype]->menu_name();
            print_question_icon($question);
            echo " $namestr</span>";
            echo '<span class="questionpreview">'.
                    quiz_question_preview_button($quiz, $question).'</span>';
            ?>
    </div><?php
}
/**
 * Print a given random question in quiz for the edit tab of edit.php.
 * Meant to be used from quiz_print_question_list()
 *  
 * @param object $question A question object from the database questions table
 * @param object $questionurl The url of the question editing page as a moodle_url object
 * @param object $quiz The quiz in the context of which the question is being displayed
 * @param boolean $quiz_qbanktool Indicate to this function if the question bank window open 
 */
function quiz_print_randomquestion(&$question, &$pageurl, &$quiz,$quiz_qbanktool){
    global $DB, $THEME;
    check_theme_arrows();
    echo '<div class="quiz_randomquestion">';

    if (!$category = $DB->get_record('question_categories', array('id' => $question->category))) {
        notify('Random question category not found!');
        return;
    }

    echo '<div class="randomquestionfromcategory">';
    print_question_icon($question);
    print_string('random','quiz');
    echo " ".get_string("fromcategory",'quiz').":</div>";

    $a = new stdClass;
    $a->arrow = $THEME->rarrow;
    $strshowcategorycontents=get_string('showcategorycontents','quiz', $a);
    
    echo '<div class="randomquestioncategory">';
    echo '<a href="'.
         $pageurl->out(false,array("qbanktool"=>1,
         "cat"=>$category->id.','.$category->contextid)).
         '" title="'.$strshowcategorycontents.'">'.$category->name.'</a>';
    echo '<span class="questionpreview">'.
        quiz_question_preview_button($quiz, $question).
        '</span>';
    

    echo "</div>";

    $questioncount=$DB->count_records_select('question',
            "category IN ($category->id) AND parent = '0' ");

    echo '<div class="randomquestionqlist">';
    $randomquestionlistsize=3;
    if(!$questioncount){
        //No questions in category, give an error plus instructions
        //error
        echo '<span class="error">';
        print_string("noquestionsnotinuse", "quiz");
        echo '</span>';
        echo '<br />';

        //create link to open question bank
        $linkcategorycontents=' <a href="'.
            $pageurl->out(false,array("qbanktool"=>1,
            "cat"=>$category->id.','.$category->contextid)).
             '">'.$strshowcategorycontents.'</a>';

        // embed the link into the string with instructions
        $a = new stdClass;
        $a->catname = '<strong>' . $category->name . '</strong>';
        $a->link =  $linkcategorycontents;
        echo get_string('addnewquestionsqbank','quiz', $a);

    }else{
        //Category has questions, list a sample of them
        echo "<ul>";
        quiz_simple_question_list($pageurl, $question->category,
                $randomquestionlistsize);
        echo '<li class="totalquestionsinrandomqcategory">';
        if ($questioncount>$randomquestionlistsize){
            echo "... ";
        }
        
        $a = new stdClass;
        $a->arrow = $THEME->rarrow;
        $strshowcategorycontents=get_string("showcategorycontents","quiz",$a);
        print_string("totalquestionsinrandomqcategory","quiz",$questioncount);

        echo ' <a href="'.
         $pageurl->out(false,array("qbanktool"=>1,"cat"=>$category->id.','.$category->contextid)).
         '">'.$strshowcategorycontents.'</a>';
        
        echo "</li>";
        echo "</ul>";
    }

    echo "</div>";

    echo '<div class="randomquestioncategorycount">';
    echo "</div>";

    echo "</div>";

}

/**
 * Print a given single question in quiz for the reordertool tab of edit.php.
 * Meant to be used from quiz_print_question_list()
 * 
 * @param object $question A question object from the database questions table
 * @param object $questionurl The url of the question editing page as a moodle_url object
 * @param object $quiz The quiz in the context of which the question is being displayed
 */
function quiz_print_singlequestion_reordertool(&$question, &$questionurl, &$quiz){
    $stredit = get_string("edit");
    $strview = get_string("view");

    global $COURSE,$QTYPES, $CFG;
    $reordercheckboxlabel='<label for="s'.$question->id.'">';
    $reordercheckboxlabelclose='</label>';

    ?>
    <div class="singlequestion">
            <?php
            $formatoptions = new stdClass;
            $formatoptions->noclean = false;
            $formatoptions->para = false;
            $formatoptions->newlines = false;
            echo $reordercheckboxlabel;
            print_question_icon($question);
            echo "$reordercheckboxlabelclose ";
            $questiontext=strip_tags(format_text($question->questiontext,
                    FORMAT_MOODLE,$formatoptions, $COURSE->id));
            $editstring="";
            if (question_has_capability_on($question, 'edit', $question->category) || question_has_capability_on($question, 'move', $question->category)) {
                echo "$reordercheckboxlabel ".
                        quiz_question_tostring($question,false).
                        $reordercheckboxlabelclose;
                $editstring="<a title=\"$stredit\" href=\"".
                        $questionurl->out(false, array('id'=>$question->id)).
                        "\"><img src=\"$CFG->pixpath/t/edit.gif\" alt=\"".
                        $stredit."\" /></a>";
            } elseif (question_has_capability_on($question, 'view',
                    $question->category)){
                echo "$reordercheckboxlabel".
                        quiz_question_tostring($question,false).
                        "$reordercheckboxlabelclose";
                $editstring="<a title=\"$strview\" href=\"".$questionurl->out(false,
                        array('id'=>$question->id))."\">$questionstring <img
                        src=\"$CFG->pixpath/i/info.gif\" alt=\"$strview\" /></a>";
            }else{
                echo "$reordercheckboxlabel".
                quiz_question_tostring($question,false).
                        "$reordercheckboxlabelclose";
            }
            echo '<span class="questionpreview">'.$editstring.
                    quiz_question_preview_button($quiz, $question, false).
                    '</span>';
            ?>
    </div><?php
}
/**
 * Print a given random question in quiz for the reordertool tab of edit.php.
 * Meant to be used from quiz_print_question_list()
 * 
 * @param object $question A question object from the database questions table
 * @param object $questionurl The url of the question editing page as a moodle_url object
 * @param object $quiz The quiz in the context of which the question is being displayed
 */

function quiz_print_randomquestion_reordertool(&$question, &$pageurl, &$quiz){
    global $CFG,$DB;
    $stredit = get_string("edit");
    $strview = get_string("view");

    echo '<div class="quiz_randomquestion">';

    if (!$category = $DB->get_record('question_categories', array('id' => $question->category))) {
        notify('Random question category not found!');
        return;
    }
    echo '<div class="randomquestionfromcategory">';
    $url=$pageurl->out(false,array("qbanktool"=>1, "cat"=>$category->id.','.
            $category->contextid));
    $reordercheckboxlabel='<label for="s'.$question->id.'">';
    $reordercheckboxlabelclose='</label>';

    echo $reordercheckboxlabel;
    print_question_icon($question);
    $questioncount=$DB->count_records_select('question',
            "category IN ($category->id) AND parent = '0' ");
    $randomquestionlistsize=3;

    if(!$questioncount){
        echo '<span class="error">';
        print_string("empty", "quiz");
        echo '</span> ';
    }

    print_string('random','quiz');
    echo ": $reordercheckboxlabelclose</div>";

    echo '<div class="randomquestioncategory">';
    echo '<!--<a href="'.
            $pageurl->out(false,array("qbanktool"=>1, "cat"=>$category->id.','.
            $category->contextid)).
            '">-->'.$reordercheckboxlabel.$category->name.
            $reordercheckboxlabelclose.'<!--</a>-->';
    echo '<span class="questionpreview">';
    echo quiz_question_preview_button($quiz, $question,false);
    
    echo '</span>';
    echo "</div>";


    echo '<div class="randomquestioncategorycount">';
    echo "</div>";

    echo "</div>";

}
/**
 * Creates a textual representation of a question for display.
 * 
 * @param object $question A question object from the database questions table
 * @param boolean $showicon If true, show the question's icon with the question. False by default.
 * @param boolean $showquestiontext If true (default), show question text after question name. 
 *       If false, show only question name.
 * @param boolean $return If true (default), return the output. If false, print it. 
 */

function quiz_question_tostring(&$question,$showicon=false,$showquestiontext=true, $return=true){
        global $COURSE;
        $result="";
        $result.='<span class="questionname">';
        if($showicon){
            $result.=print_question_icon($question,true);
            echo " ";
        }
        $result.=shorten_text(format_string($question->name),200).'</span>';
        if($showquestiontext){
            $formatoptions = new stdClass;
            $formatoptions->noclean = true;
            $formatoptions->para = false;
            $questiontext=strip_tags(format_text($question->questiontext,
                    $question->questiontextformat,
                    $formatoptions, $COURSE->id));
            $questiontext=shorten_text($questiontext,200);
            $result.='<span class="questiontext">';
            if(!empty($questiontext)){
                $result.=$questiontext;
            }else{
                $result.='<span class="error">';
                $result.= get_string("questiontextisempty","quiz");
                $result.='</span>';
            }
            $result.='</span>';
        }
        if($return){
            return $result;
        }else{
            echo $result;
        }
}





/**
 * Shows the question bank editing interface.
 * A changed copy of the function at question/editlib.php; to be refactored.
 *
 * The function also processes a number of actions:
 *
 * Actions affecting the question pool:
 * move           Moves a question to a different category
 * deleteselected Deletes the selected questions from the category
 * Other actions:
 * category      Chooses the category
 * displayoptions Sets display options
 *
 * @author Martin Dougiamas and many others. This has recently been extensively
 *         rewritten by Gustav Delius and other members of the Serving Mathematics project
 *         {@link http://maths.york.ac.uk/serving_maths}. Partially 
 *         rewritten by Olli Savolainen as a part of the Quiz UI Redesign 
 *         project in Summer 2008
 *         {@link http://docs.moodle.org/en/Development:Quiz_UI_redesign}.
 * @param moodle_url $pageurl object representing this pages url.
 */
function quiz_question_showbank($tabname, $contexts, $pageurl, $cm,
        $page, $perpage, $sortorder, $sortorderdecoded, $cat, $recurse,
        $showhidden, $showquestiontext, $cmoptions){
    global $COURSE,$DB;

    if (optional_param('deleteselected', false, PARAM_BOOL)){ // teacher still has to confirm
        // make a list of all the questions that are selected
        $rawquestions = $_REQUEST; // This code is called by both POST forms and GET links, so cannot use data_submitted.
        $questionlist = '';  // comma separated list of ids of questions to be deleted
        $questionnames = ''; // string with names of questions separated by <br /> with
                             // an asterix in front of those that are in use
        $inuse = false;      // set to true if at least one of the questions is in use
        foreach ($rawquestions as $key => $value) {    // Parse input for question ids
            if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
                $key = $matches[1];
                $questionlist .= $key.',';
                question_require_capability_on($key, 'edit');
                if (record_exists('quiz_question_instances', 'question', $key)) {
                    $questionnames .= '* ';
                    $inuse = true;
                }
                $questionnames .= get_field('question', 'name', 'id', $key).
                        '<br />';
            }
        }
        if (!$questionlist) { // no questions were selected
            redirect($pageurl->out());
        }
        $questionlist = rtrim($questionlist, ',');

        // Add an explanation about questions in use
        if ($inuse) {
            $questionnames .= '<br />'.get_string('questionsinuse', 'quiz');
        }
        notice_yesno(get_string("deletequestionscheck", "quiz", $questionnames),
                    $pageurl->out_action(array('deleteselected'=>$questionlist,
                            'confirm'=>md5($questionlist))),
                    $pageurl->out_action());
    }else{
        //actual question bank
        // starts with category selection form
        list($categoryid, $contextid)=  explode(',', $cat);

        if (!$categoryid) {
            print_box_start('generalbox questionbank');
            quiz_question_category_form($contexts->having_one_edit_tab_cap($tabname), $pageurl, $cat, $recurse, $showhidden, $showquestiontext);
            echo "<p style=\"text-align:center;\"><b>";
            print_string("selectcategoryabove", "quiz");
            echo "</b></p>";
            print_box_end();
            return;
        }

        if (!$category = $DB->get_record('question_categories',
                array('id' => $categoryid, 'contextid' => $contextid))) {
                    print_box_start('generalbox questionbank');
            notify('Category not found!');
            print_box_end();
            return;
        }
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $strcategory = get_string('category', 'quiz');
        echo '<div class="categoryinfo"><div class="categorynamefieldcontainer">'.
                $strcategory;
        echo ': <span class="categorynamefield">';
        echo shorten_text(strip_tags(format_text($category->name, FORMAT_MOODLE,
                $formatoptions, $COURSE->id)),60);
        echo '</span></div><div class="categoryinfofieldcontainer"><span class="categoryinfofield">';
        echo shorten_text(strip_tags(format_text($category->info, FORMAT_MOODLE,
                $formatoptions, $COURSE->id)),200);
        echo '</span></div></div>';

        print_box_start('generalbox questionbank');

        quiz_question_category_form($contexts->having_one_edit_tab_cap($tabname),
                $pageurl, $cat, $recurse, $showhidden, $showquestiontext);
        // continues with list of questions

        quiz_question_list($contexts->having_one_edit_tab_cap($tabname),
                $pageurl,
                $cat,
                isset($cm) ? $cm : null,
                $recurse,
                $page,
                $perpage,
                $showhidden,
                $sortorder,
                $sortorderdecoded,
                $showquestiontext,
                $contexts->having_cap('moodle/question:add'),
                $cmoptions);

        echo '<hr/><form method="get" action="edit.php" id="displayoptions">';
        echo "<fieldset class='invisiblefieldset'>";
        echo $pageurl->hidden_params_out(array('recurse', 'showhidden',
                'showquestiontext'));
        question_category_form_checkbox('recurse', $recurse);
        question_category_form_checkbox('showhidden', $showhidden);
        echo '<noscript><div class="centerpara"><input type="submit" value="'.
                get_string('go') .'" />';
        echo '</div></noscript></fieldset></form>';

        print_box_end();
    }

}
/**
 * prints a form to choose categories
 * A changed copy of the function at question/editlib.php; to be refactored.
 *
 */
function quiz_question_category_form($contexts, $pageurl, $current, $recurse=1,
        $showhidden=false, $showquestiontext=false) {
    global $CFG;

/// Get all the existing categories now
    $catmenu = question_category_options($contexts, false, 0, true);

    $strcategory = get_string('category', 'quiz');
    $strselectcategory = get_string('selectcategory', 'quiz');
    $strshow = get_string('show', 'quiz');
    $streditcats = get_string('editcategories', 'quiz');

    popup_form ('edit.php?'.$pageurl->get_query_string().'&amp;category=',
            $catmenu, 'catmenu', $current, '', '', '', false, 'self',
            $strselectcategory.":");
}



/**
* Prints the table of questions in a category with interactions
* A changed copy of the function at question/editlib.php; to be refactored.
*
* @param object $course   The course object
* @param int $categoryid  The id of the question category to be displayed
* @param int $cm      The course module record if we are in the context of a particular module, 0 otherwise
* @param int $recurse     This is 1 if subcategories should be included, 0 otherwise
* @param int $page        The number of the page to be displayed
* @param int $perpage     Number of questions to show per page
* @param boolean $showhidden   True if also hidden questions should be displayed
* @param boolean $showquestiontext whether the text of each question should be shown in the list
* @param object $cmoptions Options to be passed on to the callbacks called from this function
*/
function quiz_question_list($contexts, $pageurl, $categoryandcontext,
        $cm = null, $recurse=1, $page=0, $perpage=100, $showhidden=false,
        $sortorder='typename', $sortorderdecoded='qtype, name ASC',
        $showquestiontext = false, $addcontexts = array(), $cmoptions) {
    global $USER, $CFG, $THEME, $COURSE, $DB;
    list($categoryid, $contextid)=  explode(',', $categoryandcontext);

    $qtypemenu = question_type_menu();

    $strcategory = get_string("category", "quiz");
    $strquestion = get_string("question", "quiz");
    $straddquestions = get_string("addquestions", "quiz");
    $strimportquestions = get_string("importquestions", "quiz");
    $strexportquestions = get_string("exportquestions", "quiz");
    $strnoquestions = get_string("noquestions", "quiz");
    $strselect = get_string("select", "quiz");
    $strselectall = get_string("selectall", "quiz");
    $strselectnone = get_string("selectnone", "quiz");
    $strcreatenewquestion = get_string("createnewquestion", "quiz");
    $strquestionname = get_string("questionname", "quiz");
    $strdelete = get_string("delete");
    $stredit = get_string("edit");
    $strmove = get_string('moveqtoanothercontext', 'question');
    $strview = get_string("view");
    $straction = get_string("action");
    $strrestore = get_string('restore');

    $strtype = get_string("type", "quiz");
    $strcreatemultiple = get_string("createmultiple", "quiz");
    $strpreview = get_string("preview","quiz");

    if (!$categoryid) {
        echo "<p style=\"text-align:center;\"><b>";
        print_string("selectcategoryabove", "quiz");
        echo "</b></p>";
        return;
    }

    if (!$category = $DB->get_record('question_categories',
            array('id' => $categoryid, 'contextid' => $contextid))) {
        notify('Category not found!');
        return;
    }

    $catcontext = get_context_instance_by_id($contextid);
    $canadd = has_capability('moodle/question:add', $catcontext);
    //check for capabilities on all questions in category, will also apply to sub cats.
    $caneditall =has_capability('moodle/question:editall', $catcontext);
    $canuseall =has_capability('moodle/question:useall', $catcontext);
    $canmoveall =has_capability('moodle/question:moveall', $catcontext);

    if ($cm AND $cm->modname == 'quiz') {
        $quizid = $cm->instance;
    } else {
        $quizid = 0;
    }
    //create the url of the new question page to forward to. return url is given
    //as a parameter and automatically urlencoded.



    $returnurl = $pageurl->out();
    $questionurl = new moodle_url("$CFG->wwwroot/question/question.php",
                                array('returnurl' => $returnurl));
    if ($cm!==null){
        $questionurl->param('cmid', $cm->id);
    } else {
        $questionurl->param('courseid', $COURSE->id);
    }
    $questionmoveurl = new moodle_url("$CFG->wwwroot/question/contextmoveq.php",
                                array('returnurl' => $returnurl));
    if ($cm!==null){
        $questionmoveurl->param('cmid', $cm->id);
    } else {
        $questionmoveurl->param('courseid', $COURSE->id);
    }


    $categorylist = ($recurse) ? question_categorylist($category->id) : $category->id;

    // hide-feature
    $showhidden = $showhidden ? '' : " AND hidden = '0'";
    echo '<div class="createnewquestion">';
    if ($canadd) {
        popup_form ($questionurl->out(false, array('category' => $category->id)).
                '&amp;qtype=', $qtypemenu, "addquestion_$page", "", "choose", "",
                "", false, "self", "<strong>$strcreatenewquestion</strong>");
        helpbutton("questiontypes", $strcreatenewquestion, "quiz");
    }
    else {
        print_string('nopermissionadd', 'question');
    }
    echo '</div>';


        $categorylist_array =  explode(',', $categorylist);

    list($usql, $params) = $DB->get_in_or_equal($categorylist_array);
    if (!$totalnumber = $DB->count_records_select('question',
            "category $usql AND parent = '0' $showhidden", $params)) {
        echo '<div class="categoryquestionscontainer noquestionsincategory">';
        print_string("noquestions", "quiz");
        echo "</div>";
        return;
    }

    if (!$questions = $DB->get_records_select('question',
                "category $usql AND parent = '0' $showhidden", $params, $sortorderdecoded,
                '*', $page*$perpage, $perpage)) {

        // There are no questions on the requested page.
        $page = 0;
        if (!$questionsatall = $DB->get_records_select('question',
                "category $usql AND parent = '0' $showhidden", $params, $sortorderdecoded,
                '*', 0, $perpage)) {
            // There are no questions at all
            echo '<div class="categoryquestionscontainer noquestionsincategory">';
            print_string("noquestions", "quiz");
            echo "</div>";
            return;
        }
    }



    echo '<div class="categorysortopotionscontainer">';
    echo question_sort_options($pageurl, $sortorder);
    echo '</div>';
    echo '<div class="categorypagingbarcontainer">';
    print_paging_bar($totalnumber, $page, $perpage, $pageurl, 'qpage');
    echo '</div>';

    echo '<form method="post" action="edit.php">';
    echo '<fieldset class="invisiblefieldset" style="display: block;">';
    echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    echo $pageurl->hidden_params_out();
    echo '<div class="categoryquestionscontainer">';
    echo '<table id="categoryquestions" style="width: 100%"><colgroup><col id="qaction"></col><col id="qname"></col><col id="qextraactions"></col></colgroup><tr>';
    echo "<th style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">$straction</th>";

    echo "<th style=\"white-space:nowrap; text-align: left;\" class=\"header\" scope=\"col\">$strquestionname</th>";
    echo "<th style=\"white-space:nowrap; text-align: left;\" class=\"header\" scope=\"col\"></th>";
    echo "</tr>\n";
    foreach ($questions as $question) {
        $nameclass = '';
        $textclass = '';
        if ($question->hidden) {
            $nameclass = 'dimmed_text';
            $textclass = 'dimmed_text';
        }
        if ($showquestiontext) {
            $nameclass .= ' header';
        }
        if ($nameclass) {
            $nameclass = 'class="' . $nameclass . '"';
        }
        if ($textclass) {
            $textclass = 'class="' . $textclass . '"';
        }

        echo "<tr>\n<td style=\"white-space:nowrap;\" $nameclass>\n";

        $canuseq = question_has_capability_on($question, 'use',
                $question->category);
        if (function_exists('module_specific_actions')) {
            echo module_specific_actions($pageurl, $question->id, $cm->id,
            $canuseq,$cmoptions);
        }

        if ($caneditall || $canmoveall || $canuseall){
            echo "<input title=\"$strselect\" type=\"checkbox\" name=\"q$question->id\" id=\"checkq$question->id\" value=\"1\" />";
        }
        echo "</td>\n";

        echo "<td $nameclass><div>";
        $questionstring=quiz_question_tostring($question,false,true,true);
        echo "<label for=\"checkq$question->id\">";
        print_question_icon($question);
        echo " $questionstring</label>";



        echo "</div></td>\n";
        echo "<td>";
        // edit, hide, delete question, using question capabilities, not quiz capabilieies
        if (question_has_capability_on($question, 'edit', $question->category) ||
                question_has_capability_on($question, 'move',
                $question->category)) {
            echo "<a title=\"$stredit\" href=\"".$questionurl->out(false,
                    array('id'=>$question->id))."\"> <img
                    src=\"$CFG->pixpath/t/edit.gif\" alt=\"$stredit\" /></a>";
        } elseif (question_has_capability_on($question, 'view',
                $question->category)){

            echo "<a title=\"$strview\" href=\"".$questionurl->out(false,
                    array('id'=>$question->id))."\"><img
                    src=\"$CFG->pixpath/i/info.gif\" alt=\"$strview\" /></a>";
        }
        // preview
        if ($canuseq) {
            $quizorcourseid = $quizid?('&amp;quizid=' . $quizid):('&amp;courseid=' .$COURSE->id);
            link_to_popup_window('/question/preview.php?id=' . $question->id .
                    $quizorcourseid, 'questionpreview',
                    "<img src=\"$CFG->pixpath/t/preview.gif\" class=\"iconsmall\" alt=\"$strpreview\" />",
                    0, 0, $strpreview, QUESTION_PREVIEW_POPUP_OPTIONS);
        }
        echo "</td>";

        echo "</tr>\n";
        if($showquestiontext){
            echo '<tr><td colspan="3" ' . $textclass . '>';
            $formatoptions = new stdClass;
            $formatoptions->noclean = true;
            $formatoptions->para = false;
            echo format_text($question->questiontext,
                    $question->questiontextformat,
                    $formatoptions, $COURSE->id);
            echo "</td></tr>\n";
        }
    }
    echo "</table></div>\n";




    echo '<div class="categorypagingbarcontainer pagingbottom">';
    $paging = print_paging_bar($totalnumber, $page, $perpage,
            $pageurl, 'qpage', false, true);
    if ($totalnumber > DEFAULT_QUESTIONS_PER_PAGE) {
        if ($perpage == DEFAULT_QUESTIONS_PER_PAGE) {
            $showall = '<a href="edit.php?'.$pageurl->get_query_string(array('qperpage'=>1000)).'">'.get_string('showall', 'moodle', $totalnumber).'</a>';
        } else {
            $showall = '<a href="edit.php?'.$pageurl->get_query_string(array('qperpage'=>DEFAULT_QUESTIONS_PER_PAGE)).'">'.get_string('showperpage', 'moodle', DEFAULT_QUESTIONS_PER_PAGE).'</a>';
        }
        if ($paging) {
            $paging = substr($paging, 0, strrpos($paging, '</div>'));
            $paging .= "<br />$showall</div>";
        } else {
            $paging = "<div class='paging'>$showall</div>";
        }
    }
    echo $paging;
    echo '</div>';
    echo '<div class="categoryselectallcontainer">';
    if ($caneditall || $canmoveall || $canuseall){
        echo '<a href="javascript:select_all_in(\'TABLE\',null,\'categoryquestions\');">'.$strselectall.'</a> /'.
         ' <a href="javascript:deselect_all_in(\'TABLE\',null,\'categoryquestions\');">'.$strselectnone.'</a>';
        echo '<br />';
    }
    echo "</div>\n";
    echo '<div class="modulespecificbuttonscontainer">';
    if ($caneditall || $canmoveall || $canuseall){
        echo '<strong>&nbsp;'.get_string('withselected', 'quiz').':</strong><br />';
        if (function_exists('module_specific_buttons')) {
            echo module_specific_buttons($cm->id,$cmoptions);
        }
        // print delete and move selected question
        if ($caneditall) {
            echo '<input type="submit" name="deleteselected" value="'.
                    $strdelete."\" />\n";
        }
        if (function_exists('module_specific_controls') && $canuseall) {
            $modulespecific=module_specific_controls($totalnumber, $recurse, $category,
                    $cm->id,$cmoptions);
            if(!empty($modulespecific)){
                echo "<hr />$modulespecific";
            }
        }
    }
    echo "</div>\n";

    echo '</fieldset>';
    echo "</form>\n";
}


/**
 * Add an arbitrary element to array at a specified index, pushing the rest
 * back.
 *
 * @param array $array The array to operate on
 * @param mixed $value The element to add
 * @param integer $at The position at which to add the element
 * @return array
 */
function array_add_at($array,$value,$at){
    $beginpart=array_slice($array, 0,$at);
    $endpart=array_slice($array, $at, (count($array)-$at) );
    $beginpart[]=$value;
    $result=array_merge($beginpart,$endpart);
    return $result;
}
/**
 * Prints the form for setting a quiz' overall grade
 * 
 * @param object $quiz The quiz object of the quiz in question
 * @param object $pageurl The url of the current page with the parameters required 
 *     for links returning to the current page, as a moodle_url object
 * @param integer $tabindex The tabindex to start from for the form elements created
 * @return integer The tabindex from which the calling page can continue, that is,
 *      the last value used +1.
 */
function quiz_print_grading_form($quiz, $pageurl, $tabindex){
    global $USER;
    $strsave=get_string('save',"quiz");
    echo "<form method=\"post\" action=\"edit.php\"><div>";
    echo '<fieldset class="invisiblefieldset" style="display: block;">';
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />";
    echo $pageurl->hidden_params_out();
    echo '<label for="inputmaxgrade">'.get_string("maximumgrade")."</label>: ";
    echo '<input type="text" id="inputmaxgrade" name="maxgrade" size="' . ($quiz->decimalpoints + 2) . '" tabindex="'.($tabindex)
         .'" value="'.quiz_format_grade($quiz, $quiz->grade).'" />';
    echo '<input type="hidden" name="savechanges" value="save" />';
    echo '<input type="submit" value="'.$strsave.'" />';
    helpbutton("maxgrade", get_string("maximumgrade"), "quiz");
    echo '</fieldset>';
    echo "</div></form>\n";
    return $tabindex+1;
}
/**
 * Print the status bar
 *
 * @param object $quiz The quiz object of the quiz in question
 * @param integer $sumgrades The sum of the grades of the quiz to display
 */

function quiz_print_status_bar($quiz,$sumgrades){
    global $CFG;
    $numberofquestions=quiz_number_of_questions_in_quiz($quiz->questions);
    ?><div class="statusdisplay"><span class="totalpoints">
    <?php echo get_string("totalpoints","quiz") ?>:</span>
    <?php echo $sumgrades; ?>
    | <span class="numberofquestions">
    <?php
    echo get_string("questions","quiz").": $numberofquestions"
    ?></span>
    | <span class="quizopeningstatus">
    <?php
    $accessrule = new open_close_date_access_rule(new quiz($quiz, NULL, NULL, false), time());
    $accessrule->print_timing_information();
    ?></span><?php
    // If questions are shuffled, notify the user about the
    // question order not making much sense

    $updateurl=new moodle_url("$CFG->wwwroot/course/mod.php",
            array("return"=>"true","update"=>$quiz->cmid, "sesskey"=>sesskey()));
    echo '<br /><strong><a href="'.$updateurl->out().'">';
    print_string('updatethis', '', get_string('modulename', 'quiz'));
    echo '</a>:</strong> ';
    if($quiz->shufflequestions){
        echo "*";
    }
    echo get_string("shufflequestions",'quiz').": ";
    if($quiz->shufflequestions){
        echo get_string("yes");
    }
    else{
        echo get_string("no");
    }
    echo " | ";
    print_string("questionsperpage","quiz");
    $questionsperpagebool = ($quiz->questionsperpage < 1) ? 0 : 1;
    if($questionsperpagebool){
        echo ": $quiz->questionsperpage";
    }else{
        echo ": ".get_string("unlimited");
    }

    ?>
    </div>
    <?php
}

?>