<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas and others                //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Functions used by edit.php to edit quizzes
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 *//** */

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

    if ($quiz->questions) {
        list($usql, $params) = $DB->get_in_or_equal(explode(',', $quiz->questions));
        $questions = $DB->get_records_sql("SELECT q.*,c.contextid
                              FROM {question} q,
                                   {question_categories} c
                             WHERE q.id $usql
                               AND q.category = c.id", $params);
    } else {
        $questions = array();
    }

    $layout=quiz_clean_layout($quiz->questions);
    $order = explode(',', $layout);
    $lastindex = count($order)-1;

    if ($hasattempts) {
        $disabled='disabled="disabled"';
        $movedisabled='';
        $pagingdisabled='';
    } else {
        $disabled='';
        $movedisabled='';
        $pagingdisabled='';
    }
    if ($quiz->shufflequestions) {
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
        'onclick="return confirm(\''.
        get_string("areyousuredeleteselected","quiz").'\');" value="'.
        get_string("deleteselected").'"  '.$disabled.' /></div>';

    $a = '<input name="moveselectedonpagetop" type="text" size="2" '.
        $pagingdisabled.' />';

    $reordercontrols2top='<div class="moveselectedonpage">'.
        get_string("moveselectedonpage","quiz", $a) .
        '<input type="submit" name="savechanges" value="'.
        $strmove.'"  '.$pagingdisabled.' />'.'
        <br /><input type="submit" name="savechanges" value="'.
        $strreorderquestions.'"  '.$movedisabled.' /></div>';
    $reordercontrols2bottom='<div class="moveselectedonpage">'.
        '<input type="submit" name="savechanges" value="'.
        $strreorderquestions.'"  '.$movedisabled.' /><br />'.
        get_string("moveselectedonpage","quiz",$a) .
        '<input type="submit" name="savechanges" value="'.
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
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';

        echo $reordercontrolstop;
    }

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
    <fieldset class="invisiblefieldset" style="display: block;">
    <label for="<?php echo "inputq$qnum" ?>"><?php echo $strgrade; ?></label>:<br />
    <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
    <?php echo $pageurl->hidden_params_out(); ?>
    <input type="hidden" name="savechanges" value="save" />
        <?php
            echo '<input type="text" name="q'.$qnum.'" id="inputq'.$qnum.'" size="' . ($quiz->decimalpoints + 2) . '"
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
                $newquestioninfo->newrandomcategory=$newrandomcategory;
                if (! $newcategory = $DB->get_record('question_categories',
                        array('id'=>$newrandomcategory))) {
                    $newquestioninfo->newrandomcategory=false;
                }
            }else{
                $newquestioninfo->newrandomcategory=false;
            }
        } else {
            $newquestioninfo->newrandomcategory=false;
        }
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
    echo " ".get_string("xfromcategory",'quiz',get_string('random','quiz'))."</div>";

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
 * A column type for the add this question to the quiz.
 */
class question_bank_add_to_quiz_action_column extends question_bank_action_column_base {
    protected $stradd;

    public function init() {
        parent::init();
        $this->stradd = get_string('addtoquiz', 'quiz');
    }

    public function get_name() {
        return 'addtoquizaction';
    }

    protected function display_content($question, $rowclasses) {
        // for RTL languages: switch right and left arrows
        if (right_to_left()) {
            $movearrow = 'removeright.gif';
        } else {
            $movearrow = 'moveleft.gif';
        }
        $this->print_icon($movearrow, $this->stradd, $this->qbank->add_to_quiz_url($question->id));
    }

    public function get_required_fields() {
        return array('q.id');
    }
}

/**
 * A column type for the name followed by the start of the question text.
 */
class question_bank_question_name_text_column extends question_bank_question_name_column {
    public function get_name() {
        return 'questionnametext';
    }

    protected function display_content($question, $rowclasses) {
        echo '<div>';
        $labelfor = $this->label_for($question);
        if ($labelfor) {
            echo '<label for="' . $labelfor . '">';
        }
        echo quiz_question_tostring($question, false, true, true);
        if ($labelfor) {
            echo '</label>';
        }
        echo '</div>';
    }

    public function get_required_fields() {
        $fields = parent::get_required_fields();
        $fields[] = 'q.questiontext';
        $fields[] = 'q.questiontextformat';
        return $fields;
    }
}

/**
 * Subclass to customise the view of the question bank for the quiz editing screen.
 */
class quiz_question_bank_view extends question_bank_view {
    protected $quizhasattempts = false;

    protected function know_field_types() {
        $types = parent::know_field_types();
        $types[] = new question_bank_add_to_quiz_action_column($this);
        $types[] = new question_bank_question_name_text_column($this);
        return $types;
    }

    protected function wanted_columns() {
        return array('addtoquizaction', 'checkbox', 'qtype', 'questionnametext', 'editaction', 'previewaction');
    }

    /**
     * Let the question bank display know whether the quiz has been attempted,
     * hence whether some bits of UI, like the add this question to the quiz icon,
     * should be displayed.
     * @param boolean $quizhasattempts whether the quiz has attempts.
     */
    public function set_quiz_has_attempts($quizhasattempts) {
        $this->quizhasattempts = $quizhasattempts;
        if ($quizhasattempts && isset($this->visiblecolumns['addtoquizaction'])) {
            unset($this->visiblecolumns['addtoquizaction']);
        }
    }

    public function preview_question_url($questionid) {
        global $CFG;
        return $CFG->wwwroot . '/question/preview.php?id=' . $questionid . $this->quizorcourseid;
    }

    public function add_to_quiz_url($questionid) {
        global $CFG;
        return $CFG->wwwroot . '/mod/quiz/edit.php?' . $this->baseurl->get_query_string() .
                '&amp;addquestion=' . $questionid . '&amp;sesskey=' . sesskey();
    }

    public function display($tabname, $page, $perpage, $sortorder,
            $sortorderdecoded, $cat, $recurse, $showhidden, $showquestiontext){

        if ($this->process_actions_needing_ui()) {
            return;
        }

        // Display the current category.
        if (!$category = $this->get_current_category($cat)) {
            return;
        }
        $this->print_category_info($category);

        print_box_start('generalbox questionbank');

        $this->display_category_form($this->contexts->having_one_edit_tab_cap($tabname),
                $this->baseurl, $cat);

        // continues with list of questions
        $this->display_question_list($this->contexts->having_one_edit_tab_cap($tabname), $this->baseurl, $cat, $this->cm,
                $recurse, $page, $perpage, $showhidden, $sortorder, $sortorderdecoded, $showquestiontext,
                $this->contexts->having_cap('moodle/question:add'));

        $this->display_options($recurse, $showhidden, $showquestiontext);
        print_box_end();
    }

    protected function print_choose_category_message($categoryandcontext) {
        print_box_start('generalbox questionbank');
        $this->display_category_form($this->contexts->having_one_edit_tab_cap('edit'), $this->baseurl, $categoryandcontext);
        echo "<p style=\"text-align:center;\"><b>";
        print_string("selectcategoryabove", "quiz");
        echo "</b></p>";
        print_box_end();
    }

    protected function print_category_info($category) {
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $strcategory = get_string('category', 'quiz');
        echo '<div class="categoryinfo"><div class="categorynamefieldcontainer">'.
                $strcategory;
        echo ': <span class="categorynamefield">';
        echo shorten_text(strip_tags(format_text($category->name, FORMAT_MOODLE,
                $formatoptions, $this->course->id)),60);
        echo '</span></div><div class="categoryinfofieldcontainer"><span class="categoryinfofield">';
        echo shorten_text(strip_tags(format_text($category->info, FORMAT_MOODLE,
                $formatoptions, $this->course->id)),200);
        echo '</span></div></div>';
    }

    protected function display_options($recurse = 1, $showhidden = false, $showquestiontext = false) {
        echo '<form method="get" action="edit.php" id="displayoptions">';
        echo "<fieldset class='invisiblefieldset'>";
        echo $this->baseurl->hidden_params_out(array('recurse', 'showhidden', 'showquestiontext'));
        $this->display_category_form_checkbox('recurse', get_string('recurse', 'quiz'));
        $this->display_category_form_checkbox('showhidden', get_string('showhidden', 'quiz'));
        echo '<noscript><div class="centerpara"><input type="submit" value="'. get_string('go') .'" />';
        echo '</div></noscript></fieldset></form>';
    }
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
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />";
    echo $pageurl->hidden_params_out();
    $a='<input type="text" id="inputmaxgrade" name="maxgrade" size="' . ($quiz->decimalpoints + 2) . '" tabindex="'.($tabindex)
         .'" value="'.quiz_format_grade($quiz, $quiz->grade).'" />';
    echo '<label for="inputmaxgrade">'.get_string("maximumgradex",'',$a)."</label>";
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
 */
function quiz_print_status_bar($quiz){
    global $CFG;
    $numberofquestions=quiz_number_of_questions_in_quiz($quiz->questions);
    ?><div class="statusdisplay"><span class="totalpoints">
    <?php echo get_string('totalpointsx', 'quiz', quiz_format_grade($quiz, $quiz->sumgrades)) ?></span>
    | <span class="numberofquestions">
    <?php
    echo get_string("numquestionsx","quiz",$numberofquestions);
    ?></span>
    <?php

// Current status of the quiz, with open an close dates as a tool tip.
    $available = true;
    $dates = array();
    $timenow = time();
    if ($quiz->timeopen > 0) {
        if ($timenow > $quiz->timeopen) {
            $dates[] = get_string('quizopenedon', 'quiz', userdate($quiz->timeopen));
        } else {
            $dates[] = get_string('quizwillopen', 'quiz', userdate($quiz->timeopen));
            $available = false;
        }
    }
    if ($quiz->timeclose > 0) {
        if ($timenow > $quiz->timeclose) {
            $dates[] = get_string('quizclosed', 'quiz', userdate($quiz->timeclose));
            $available = false;
        } else {
            $dates[] = get_string('quizcloseson', 'quiz', userdate($quiz->timeclose));
        }
    }
    if (empty($dates)) {
        $dates[] = get_string('alwaysavailable', 'quiz');
    }
    $dates = implode('. ', $dates);
    echo ' | <span class="quizopeningstatus" title="' . $dates . '">';
    if ($available) {
        print_string('quizisopen', 'quiz');
    } else {
        print_string('quizisclosed', 'quiz');
    }
    echo '</span>';

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
    if($quiz->shufflequestions){
        $shuffleqs= get_string("yes");
    }
    else{
        $shuffleqs= get_string("no");
    }
    echo get_string("shufflequestionsx",'quiz',$shuffleqs);
    echo " | ";
    $questionsperpagebool = ($quiz->questionsperpage < 1) ? 0 : 1;
    if($questionsperpagebool){
        $valquestionsparpage=$quiz->questionsperpage;
    }else{
        $valquestionsparpage=get_string("unlimited");
    }
    print_string("questionsperpagex","quiz",$valquestionsparpage);

    ?>
    </div>
    <?php
}

?>