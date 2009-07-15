<?php // $Id$
/**
 * Functions used to show question editing interface
 *
 *
 * @author Martin Dougiamas and many others. This has recently been extensively
 *         rewritten by members of the Serving Mathematics project
 *         {@link http://maths.york.ac.uk/serving_maths}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 */

require_once($CFG->libdir.'/questionlib.php');

define('DEFAULT_QUESTIONS_PER_PAGE', 20);

function get_module_from_cmid($cmid){
    global $CFG;
    if (!$cmrec = get_record_sql("SELECT cm.*, md.name as modname
                               FROM {$CFG->prefix}course_modules cm,
                                    {$CFG->prefix}modules md
                               WHERE cm.id = '$cmid' AND
                                     md.id = cm.module")){
        error('cmunknown');
    } elseif (!$modrec =get_record($cmrec->modname, 'id', $cmrec->instance)) {
        error('cmunknown');
    }
    $modrec->instance = $modrec->id;
    $modrec->cmid = $cmrec->id;
    $cmrec->name = $modrec->name;

    return array($modrec, $cmrec);
}
/**
* Function to read all questions for category into big array
*
* @param int $category category number
* @param bool $noparent if true only questions with NO parent will be selected
* @param bool $recurse include subdirectories
* @param bool $export set true if this is called by questionbank export
* @author added by Howard Miller June 2004
*/
function get_questions_category( $category, $noparent=false, $recurse=true, $export=true ) {

    global $QTYPES;

    // questions will be added to an array
    $qresults = array();

    // build sql bit for $noparent
    $npsql = '';
    if ($noparent) {
      $npsql = " and parent='0' ";
    }

    // get (list) of categories
    if ($recurse) {
        $categorylist = question_categorylist( $category->id );
    }
    else {
        $categorylist = $category->id;
    }

    // get the list of questions for the category
    if ($questions = get_records_select("question","category IN ($categorylist) $npsql", "qtype, name ASC")) {

        // iterate through questions, getting stuff we need
        foreach($questions as $question) {
            $questiontype = $QTYPES[$question->qtype];
            $question->export_process = $export;
            $questiontype->get_question_options( $question );
            $qresults[] = $question;
        }
    }

    return $qresults;
}

/**
 * @param integer $categoryid a category id.
 * @return boolean whether this is the only top-level category in a context.
 */
function question_is_only_toplevel_category_in_context($categoryid) {
    global $CFG;
    return 1 == count_records_sql("
            SELECT count(*)
              FROM {$CFG->prefix}question_categories c1,
                   {$CFG->prefix}question_categories c2
             WHERE c2.id = $categoryid
               AND c1.contextid = c2.contextid
               AND c1.parent = 0 AND c2.parent = 0");
}

/**
 * Check whether this user is allowed to delete this category.
 *
 * @param integer $todelete a category id.
 */
function question_can_delete_cat($todelete) {
    if (question_is_only_toplevel_category_in_context($todelete)) {
        error('You can\'t delete that category it is the default category for this context.');
    } else {
        $contextid = get_field('question_categories', 'contextid', 'id', $todelete);
        require_capability('moodle/question:managecategory', get_context_instance_by_id($contextid));
    }
}
/**
 * prints a form to choose categories
 */
function question_category_form($contexts, $pageurl, $current, $recurse=1, $showhidden=false, $showquestiontext=false) {
    global $CFG;


/// Get all the existing categories now
    $catmenu = question_category_options($contexts, false, 0, true);

    $strcategory = get_string('category', 'quiz');
    $strshow = get_string('show', 'quiz');
    $streditcats = get_string('editcategories', 'quiz');

    popup_form ('edit.php?'.$pageurl->get_query_string().'&amp;category=', $catmenu, 'catmenu', $current, '', '', '', false, 'self', "<strong>$strcategory</strong>");

    echo '<form method="get" action="edit.php" id="displayoptions">';
    echo "<fieldset class='invisiblefieldset'>";
    echo $pageurl->hidden_params_out(array('recurse', 'showhidden', 'showquestiontext'));
    question_category_form_checkbox('recurse', $recurse);
    question_category_form_checkbox('showhidden', $showhidden);
    question_category_form_checkbox('showquestiontext', $showquestiontext);
    echo '<noscript><div class="centerpara"><input type="submit" value="'. get_string('go') .'" />';
    echo '</div></noscript></fieldset></form>';
}

/**
 * Private funciton to help the preceeding function.
 */
function question_category_form_checkbox($name, $checked) {
    echo '<div><input type="hidden" id="' . $name . '_off" name="' . $name . '" value="0" />';
    echo '<input type="checkbox" id="' . $name . '_on" name="' . $name . '" value="1"';
    if ($checked) {
        echo ' checked="checked"';
    }
    echo ' onchange="getElementById(\'displayoptions\').submit(); return true;" />';
    echo '<label for="' . $name . '_on">';
    print_string($name, 'quiz');
    echo "</label></div>\n";
}

/**
* Prints the table of questions in a category with interactions
*
* @param object $course   The course object
* @param int $categoryid  The id of the question category to be displayed
* @param int $cm      The course module record if we are in the context of a particular module, 0 otherwise
* @param int $recurse     This is 1 if subcategories should be included, 0 otherwise
* @param int $page        The number of the page to be displayed
* @param int $perpage     Number of questions to show per page
* @param boolean $showhidden   True if also hidden questions should be displayed
* @param boolean $showquestiontext whether the text of each question should be shown in the list
*/
function question_list($contexts, $pageurl, $categoryandcontext, $cm = null,
        $recurse=1, $page=0, $perpage=100, $showhidden=false, $sortorder='typename', $sortorderdecoded='qtype, name ASC',
        $showquestiontext = false, $addcontexts = array()) {
    global $USER, $CFG, $THEME, $COURSE;

    $lastchangedid=optional_param('lastchanged',0,PARAM_INT);
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

    if (!$category = get_record('question_categories', 'id', $categoryid, 'contextid', $contextid)) {
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
    echo '<div class="boxaligncenter">';
    $formatoptions = new stdClass;
    $formatoptions->noclean = true;
    echo format_text($category->info, FORMAT_MOODLE, $formatoptions, $COURSE->id);

    echo '<table><tr>';

    if ($canadd) {
        echo '<td valign="top" align="right">';
        popup_form ($questionurl->out(false, array('category' => $category->id)).'&amp;qtype=', $qtypemenu, "addquestion", "", "choose", "", "", false, "self", "<strong>$strcreatenewquestion</strong>");
        echo '</td><td valign="top" align="right">';
        helpbutton("questiontypes", $strcreatenewquestion, "quiz");
        echo '</td>';
    }
    else {
        echo '<td>';
        print_string('nopermissionadd', 'question');
        echo '</td>';
    }

    echo '</tr></table>';
    echo '</div>';

    $categorylist = ($recurse) ? question_categorylist($category->id) : $category->id;

    // hide-feature
    $showhidden = $showhidden ? '' : " AND hidden = '0'";

    if (!$totalnumber = count_records_select('question', "category IN ($categorylist) AND parent = '0' $showhidden")) {
        echo "<p style=\"text-align:center;\">";
        print_string("noquestions", "quiz");
        echo "</p>";
        return;
    }

    if (!$questions = get_records_select('question', "category IN ($categorylist) AND parent = '0' $showhidden", $sortorderdecoded, '*', $page*$perpage, $perpage)) {
        // There are no questions on the requested page.
        $page = 0;
        if (!$questions = get_records_select('question', "category IN ($categorylist) AND parent = '0' $showhidden", $sortorderdecoded, '*', 0, $perpage)) {
            // There are no questions at all
            echo "<p style=\"text-align:center;\">";
            print_string("noquestions", "quiz");
            echo "</p>";
            return;
        }
    }

    print_paging_bar($totalnumber, $page, $perpage, $pageurl, 'qpage');

    echo question_sort_options($pageurl, $sortorder);


    echo '<form method="post" action="edit.php">';
    echo '<fieldset class="invisiblefieldset" style="display: block;">';
    echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    echo $pageurl->hidden_params_out();
    echo '<table id="categoryquestions" style="width: 100%"><tr>';
    echo "<th style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">$straction</th>";

    echo "<th style=\"white-space:nowrap; text-align: left;\" class=\"header\" scope=\"col\">$strquestionname</th>
    <th style=\"white-space:nowrap; text-align: right;\" class=\"header\" scope=\"col\">$strtype</th>";
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
        if ($question->id==$lastchangedid) {
            $nameclass='highlight';
        }
        if ($nameclass) {
            $nameclass = 'class="' . $nameclass . '"';
        }
        if ($textclass) {
            $textclass = 'class="' . $textclass . '"';
        }

        echo "<tr>\n<td style=\"white-space:nowrap;\" $nameclass>\n";

        $canuseq = question_has_capability_on($question, 'use', $question->category);
        if (function_exists('module_specific_actions')) {
            echo module_specific_actions($pageurl, $question->id, $cm->id, $canuseq);
        }

        // preview
        if ($canuseq) {
            $quizorcourseid = $quizid?('&amp;quizid=' . $quizid):('&amp;courseid=' .$COURSE->id);
            link_to_popup_window('/question/preview.php?id=' . $question->id . $quizorcourseid, 'questionpreview',
                    "<img src=\"$CFG->pixpath/t/preview.gif\" class=\"iconsmall\" alt=\"$strpreview\" />",
                    0, 0, $strpreview, QUESTION_PREVIEW_POPUP_OPTIONS);
        }
        // edit, hide, delete question, using question capabilities, not quiz capabilieies
        if (question_has_capability_on($question, 'edit', $question->category) || question_has_capability_on($question, 'move', $question->category)) {
            echo "<a title=\"$stredit\" href=\"".$questionurl->out(false, array('id'=>$question->id))."\"><img
                    src=\"$CFG->pixpath/t/edit.gif\" alt=\"$stredit\" /></a>&nbsp;";
        } elseif (question_has_capability_on($question, 'view', $question->category)){
            echo "<a title=\"$strview\" href=\"".$questionurl->out(false, array('id'=>$question->id))."\"><img
                    src=\"$CFG->pixpath/i/info.gif\" alt=\"$strview\" /></a>&nbsp;";
        }

        if (question_has_capability_on($question, 'move', $question->category) && question_has_capability_on($question, 'view', $question->category)) {
            echo "<a title=\"$strmove\" href=\"".$questionurl->out(false, array('id'=>$question->id, 'movecontext'=>1))."\"><img
                    src=\"$CFG->pixpath/t/move.gif\" alt=\"$strmove\" /></a>&nbsp;";
        }

        if (question_has_capability_on($question, 'edit', $question->category)) {
            // hide-feature
            if($question->hidden) {
                echo "<a title=\"$strrestore\" href=\"edit.php?".$pageurl->get_query_string()."&amp;unhide=$question->id&amp;sesskey=$USER->sesskey\"><img
                        src=\"$CFG->pixpath/t/restore.gif\" alt=\"$strrestore\" /></a>";
            } else {
                echo "<a title=\"$strdelete\" href=\"edit.php?".$pageurl->get_query_string()."&amp;deleteselected=$question->id&amp;q$question->id=1\"><img
                        src=\"$CFG->pixpath/t/delete.gif\" alt=\"$strdelete\" /></a>";
            }
        }
        if ($caneditall || $canmoveall || $canuseall){
            echo "&nbsp;<input title=\"$strselect\" type=\"checkbox\" name=\"q$question->id\" value=\"1\" />";
        }
        echo "</td>\n";

        echo "<td $nameclass>" . format_string($question->name) . "</td>\n";
        echo "<td $nameclass style='text-align: right'>\n";
        print_question_icon($question);
        echo "</td>\n";
        echo "</tr>\n";
        if($showquestiontext){
            echo '<tr><td colspan="3" ' . $textclass . '>';
            $formatoptions = new stdClass;
            $formatoptions->noclean = true;
            $formatoptions->para = false;
            echo format_text($question->questiontext, $question->questiontextformat,
                    $formatoptions, $COURSE->id);
            echo "</td></tr>\n";
        }
    }
    echo "</table>\n";

    $paging = print_paging_bar($totalnumber, $page, $perpage, $pageurl, 'qpage', false, true);
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

    if ($caneditall || $canmoveall || $canuseall){
        echo '<a href="javascript:select_all_in(\'TABLE\',null,\'categoryquestions\');">'.$strselectall.'</a> /'.
         ' <a href="javascript:deselect_all_in(\'TABLE\',null,\'categoryquestions\');">'.$strselectnone.'</a>';
        echo '<br />';
        echo '<strong>&nbsp;'.get_string('withselected', 'quiz').':</strong><br />';

        if (function_exists('module_specific_buttons')) {
            echo module_specific_buttons($cm->id);
        }
        // print delete and move selected question
        if ($caneditall) {
            echo '<input type="submit" name="deleteselected" value="'.$strdelete."\" />\n";
        }
        if ($canmoveall && count($addcontexts)) {
            echo '<input type="submit" name="move" value="'.get_string('moveto', 'quiz')."\" />\n";
            question_category_select_menu($addcontexts, false, 0, "$category->id,$category->contextid");
        }

        if (function_exists('module_specific_controls') && $canuseall) {
            echo module_specific_controls($totalnumber, $recurse, $category, $cm->id);
        }
    }
    echo '</fieldset>';
    echo "</form>\n";
}
function question_sort_options($pageurl, $sortorder){
    global $USER;
    //sort options
    $html = "<div class=\"mdl-align\">";
    $html .= '<form method="post" action="edit.php">';
    $html .= '<fieldset class="invisiblefieldset" style="display: block;">';
    $html .= '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    $html .= $pageurl->hidden_params_out(array('qsortorder'));
    $sortoptions = array('alpha' => get_string("sortalpha", "quiz"),
                         'typealpha' => get_string("sorttypealpha", "quiz"),
                         'age' => get_string("sortage", "quiz"));
    $html .=  choose_from_menu ($sortoptions, 'qsortorder', $sortorder, false, 'this.form.submit();', '0', true);
    $html .=  '<noscript><div><input type="submit" value="'.get_string("sortsubmit", "quiz").'" /></div></noscript>';
    $html .= '</fieldset>';
    $html .= "</form>\n";
    $html .= "</div>\n";
    return $html;
}

function question_showbank_actions($pageurl, $cm){
    global $CFG, $COURSE;
    /// Now, check for commands on this page and modify variables as necessary
    if (optional_param('move', false, PARAM_BOOL) and confirm_sesskey()) { /// Move selected questions to new category
        $category = required_param('category', PARAM_SEQUENCE);
        list($tocategoryid, $contextid) = explode(',', $category);
        if (! $tocategory = get_record('question_categories', 'id', $tocategoryid, 'contextid', $contextid)) {
            error('Could not find category record');
        }
        $tocontext = get_context_instance_by_id($contextid);
        require_capability('moodle/question:add', $tocontext);
        $rawdata = (array) data_submitted();
        $questionids = array();
        foreach ($rawdata as $key => $value) {    // Parse input for question ids
            if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
                $key = $matches[1];
                $questionids[] = $key;
            }
        }
        if ($questionids){
            $questionidlist = join($questionids, ',');
            $sql = "SELECT q.*, c.contextid FROM {$CFG->prefix}question q, {$CFG->prefix}question_categories c WHERE q.id IN ($questionidlist) AND c.id = q.category";
            if (!$questions = get_records_sql($sql)){
                print_error('questiondoesnotexist', 'question', $pageurl->out());
            }
            $checkforfiles = false;
            foreach ($questions as $question){
                //check capabilities
                question_require_capability_on($question, 'move');
                $fromcontext = get_context_instance_by_id($question->contextid);
                if (get_filesdir_from_context($fromcontext) != get_filesdir_from_context($tocontext)){
                    $checkforfiles = true;
                }
            }
            $returnurl = $pageurl->out(false, array('category'=>"$tocategoryid,$contextid"));
            if (!$checkforfiles){
                if (!question_move_questions_to_category(implode(',', $questionids), $tocategory->id)) {
                    print_error('errormovingquestions', 'question', $returnurl, $questionids);
                }
                redirect($returnurl);
            } else {
                $movecontexturl  = new moodle_url($CFG->wwwroot.'/question/contextmoveq.php',
                                                array('returnurl' => $returnurl,
                                                        'ids'=>$questionidlist,
                                                        'tocatid'=> $tocategoryid));
                if ($cm){
                    $movecontexturl->param('cmid', $cm->id);
                } else {
                    $movecontexturl->param('courseid', $COURSE->id);
                }
                redirect($movecontexturl->out());
            }
        }
    }

    if (optional_param('deleteselected', false, PARAM_BOOL)) { // delete selected questions from the category
        if (($confirm = optional_param('confirm', '', PARAM_ALPHANUM)) and confirm_sesskey()) { // teacher has already confirmed the action
            $deleteselected = required_param('deleteselected');
            if ($confirm == md5($deleteselected)) {
                if ($questionlist = explode(',', $deleteselected)) {
                    // for each question either hide it if it is in use or delete it
                    foreach ($questionlist as $questionid) {
                        question_require_capability_on($questionid, 'edit');
                        if (record_exists('quiz_question_instances', 'question', $questionid)) {
                            if (!set_field('question', 'hidden', 1, 'id', $questionid)) {
                                question_require_capability_on($questionid, 'edit');
                                error('Was not able to hide question');
                            }
                        } else {
                            delete_question($questionid);
                        }
                    }
                }
                redirect($pageurl->out());
            } else {
                error("Confirmation string was incorrect");
            }
        }
    }

    // Unhide a question
    if(($unhide = optional_param('unhide', '', PARAM_INT)) and confirm_sesskey()) {
        question_require_capability_on($unhide, 'edit');
        if(!set_field('question', 'hidden', 0, 'id', $unhide)) {
            error("Failed to unhide the question.");
        }
        redirect($pageurl->out());
    }
}

/**
 * Shows the question bank editing interface.
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
 *         {@link http://maths.york.ac.uk/serving_maths}
 * @param moodle_url $pageurl object representing this pages url.
 */
function question_showbank($tabname, $contexts, $pageurl, $cm, $page, $perpage, $sortorder, $sortorderdecoded, $cat, $recurse, $showhidden, $showquestiontext){
    global $COURSE;

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
                $questionnames .= get_field('question', 'name', 'id', $key).'<br />';
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
                    $pageurl->out_action(),
                    $pageurl->out(true),
                    array('deleteselected'=>$questionlist, 'confirm'=>md5($questionlist)),
                    $pageurl->params(), 'post', 'get');

        echo '</td></tr>';
        echo '</table>';
        print_footer($COURSE);
        exit;
    }


    // starts with category selection form
    print_box_start('generalbox questionbank');
    print_heading(get_string('questionbank', 'question'), '', 2);
    question_category_form($contexts->having_one_edit_tab_cap($tabname), $pageurl, $cat, $recurse, $showhidden, $showquestiontext);

    // continues with list of questions
    question_list($contexts->having_one_edit_tab_cap($tabname), $pageurl, $cat, isset($cm) ? $cm : null,
            $recurse, $page, $perpage, $showhidden, $sortorder, $sortorderdecoded, $showquestiontext,
            $contexts->having_cap('moodle/question:add'));

    print_box_end();
}
/**
 * Common setup for all pages for editing questions.
 * @param string $edittab code for this edit tab
 * @param boolean $requirecmid require cmid? default false
 * @param boolean $requirecourseid require courseid, if cmid is not given? default true
 * @return array $thispageurl, $contexts, $cmid, $cm, $module, $pagevars
 */
function question_edit_setup($edittab, $requirecmid = false, $requirecourseid = true){
    global $COURSE, $QUESTION_EDITTABCAPS;

    //$thispageurl is used to construct urls for all question edit pages we link to from this page. It contains an array
    //of parameters that are passed from page to page.
    $thispageurl = new moodle_url();
    if ($requirecmid){
        $cmid =required_param('cmid', PARAM_INT);
    } else {
        $cmid = optional_param('cmid', 0, PARAM_INT);
    }
    if ($cmid){
        list($module, $cm) = get_module_from_cmid($cmid);
        $courseid = $cm->course;
        $thispageurl->params(compact('cmid'));
        require_login($courseid, false, $cm);
        $thiscontext = get_context_instance(CONTEXT_MODULE, $cmid);
    } else {
        $module = null;
        $cm = null;
        if ($requirecourseid){
            $courseid  = required_param('courseid', PARAM_INT);
        } else {
            $courseid  = optional_param('courseid', 0, PARAM_INT);
        }
        if ($courseid){
            $thispageurl->params(compact('courseid'));
            require_login($courseid, false);
            $thiscontext = get_context_instance(CONTEXT_COURSE, $courseid);
        } else {
            $thiscontext = null;
        }
    }

    if ($thiscontext){
        $contexts = new question_edit_contexts($thiscontext);
        $contexts->require_one_edit_tab_cap($edittab);

    } else {
        $contexts = null;
    }



    $pagevars['qpage'] = optional_param('qpage', -1, PARAM_INT);

    //pass 'cat' from page to page and when 'category' comes from a drop down menu
    //then we also reset the qpage so we go to page 1 of
    //a new cat.
    $pagevars['cat'] = optional_param('cat', 0, PARAM_SEQUENCE);// if empty will be set up later
    if  ($category = optional_param('category', 0, PARAM_SEQUENCE)){
        if ($pagevars['cat'] != $category){ // is this a move to a new category?
            $pagevars['cat'] = $category;
            $pagevars['qpage'] = 0;
        }
    }
    if ($pagevars['cat']){
        $thispageurl->param('cat', $pagevars['cat']);
    }
    if ($pagevars['qpage'] > -1) {
        $thispageurl->param('qpage', $pagevars['qpage']);
    } else {
        $pagevars['qpage'] = 0;
    }

    $pagevars['qperpage'] = optional_param('qperpage', -1, PARAM_INT);
    if ($pagevars['qperpage'] > -1) {
        $thispageurl->param('qperpage', $pagevars['qperpage']);
    } else {
        $pagevars['qperpage'] = DEFAULT_QUESTIONS_PER_PAGE;
    }

    $sortoptions = array('alpha' => 'name, qtype ASC',
                          'typealpha' => 'qtype, name ASC',
                          'age' => 'id ASC');

    if ($sortorder = optional_param('qsortorder', '', PARAM_ALPHA)) {
        $pagevars['qsortorderdecoded'] = $sortoptions[$sortorder];
        $pagevars['qsortorder'] = $sortorder;
        $thispageurl->param('qsortorder', $sortorder);
    } else {
        $pagevars['qsortorderdecoded'] = $sortoptions['typealpha'];
        $pagevars['qsortorder'] = 'typealpha';
    }

    $defaultcategory = question_make_default_categories($contexts->all());

    $contextlistarr = array();
    foreach ($contexts->having_one_edit_tab_cap($edittab) as $context){
        $contextlistarr[] = "'$context->id'";
    }
    $contextlist = join($contextlistarr, ' ,');
    if (!empty($pagevars['cat'])){
        $catparts = explode(',', $pagevars['cat']);
        if (!$catparts[0] || (FALSE !== array_search($catparts[1], $contextlistarr)) || !count_records_select("question_categories", "id = '".$catparts[0]."' AND contextid = $catparts[1]")) {
            print_error('invalidcategory', 'quiz');
        }
    } else {
        $category = $defaultcategory;
        $pagevars['cat'] = "$category->id,$category->contextid";
    }

    if(($recurse = optional_param('recurse', -1, PARAM_BOOL)) != -1) {
        $pagevars['recurse'] = $recurse;
        $thispageurl->param('recurse', $recurse);
    } else {
        $pagevars['recurse'] = 1;
    }

    if(($showhidden = optional_param('showhidden', -1, PARAM_BOOL)) != -1) {
        $pagevars['showhidden'] = $showhidden;
        $thispageurl->param('showhidden', $showhidden);
    } else {
        $pagevars['showhidden'] = 0;
    }

    if(($showquestiontext = optional_param('showquestiontext', -1, PARAM_BOOL)) != -1) {
        $pagevars['showquestiontext'] = $showquestiontext;
        $thispageurl->param('showquestiontext', $showquestiontext);
    } else {
        $pagevars['showquestiontext'] = 0;
    }

    //category list page
    $pagevars['cpage'] = optional_param('cpage', 1, PARAM_INT);
    if ($pagevars['cpage'] < 1) {
        $pagevars['cpage'] = 1;
    }
    if ($pagevars['cpage'] != 1){
        $thispageurl->param('cpage', $pagevars['cpage']);
    }


    return array($thispageurl, $contexts, $cmid, $cm, $module, $pagevars);
}
class question_edit_contexts{
    var $allcontexts;
    /**
     * @param current context
     */
    function question_edit_contexts($thiscontext){
        $pcontextids = get_parent_contexts($thiscontext);
        $contexts = array($thiscontext);
        foreach ($pcontextids as $pcontextid){
            $contexts[] = get_context_instance_by_id($pcontextid);
        }
        $this->allcontexts = $contexts;
    }
    /**
     * @return array all parent contexts
     */
    function all(){
        return $this->allcontexts;
    }
    /**
     * @return object lowest context which must be either the module or course context
     */
    function lowest(){
        return $this->allcontexts[0];
    }
    /**
     * @param string $cap capability
     * @return array parent contexts having capability, zero based index
     */
    function having_cap($cap){
        $contextswithcap = array();
        foreach ($this->allcontexts as $context){
            if (has_capability($cap, $context)){
                $contextswithcap[] = $context;
            }
        }
        return $contextswithcap;
    }
    /**
     * @param array $caps capabilities
     * @return array parent contexts having at least one of $caps, zero based index
     */
    function having_one_cap($caps){
        $contextswithacap = array();
        foreach ($this->allcontexts as $context){
            foreach ($caps as $cap){
                if (has_capability($cap, $context)){
                    $contextswithacap[] = $context;
                    break; //done with caps loop
                }
            }
        }
        return $contextswithacap;
    }
    /**
     * @param string $tabname edit tab name
     * @return array parent contexts having at least one of $caps, zero based index
     */
    function having_one_edit_tab_cap($tabname){
        global $QUESTION_EDITTABCAPS;
        return $this->having_one_cap($QUESTION_EDITTABCAPS[$tabname]);
    }
    /**
     * Has at least one parent context got the cap $cap?
     *
     * @param string $cap capability
     * @return boolean
     */
    function have_cap($cap){
        return (count($this->having_cap($cap)));
    }

    /**
     * Has at least one parent context got one of the caps $caps?
     *
     * @param string $cap capability
     * @return boolean
     */
    function have_one_cap($caps){
        foreach ($caps as $cap){
            if ($this->have_cap($cap)){
                return true;
            }
        }
        return false;
    }
    /**
     * Has at least one parent context got one of the caps for actions on $tabname
     *
     * @param string $tabname edit tab name
     * @return boolean
     */
    function have_one_edit_tab_cap($tabname){
        global $QUESTION_EDITTABCAPS;
        return $this->have_one_cap($QUESTION_EDITTABCAPS[$tabname]);
    }
    /**
     * Throw error if at least one parent context hasn't got the cap $cap
     *
     * @param string $cap capability
     */
    function require_cap($cap){
        if (!$this->have_cap($cap)){
            print_error('nopermissions', '', '', $cap);
        }
    }
    /**
     * Throw error if at least one parent context hasn't got one of the caps $caps
     *
     * @param array $cap capabilities
     */
     function require_one_cap($caps){
        if (!$this->have_one_cap($caps)){
            $capsstring = join($caps, ', ');
            print_error('nopermissions', '', '', $capsstring);
        }
    }
    /**
     * Throw error if at least one parent context hasn't got one of the caps $caps
     *
     * @param string $tabname edit tab name
     */
     function require_one_edit_tab_cap($tabname){
        if (!$this->have_one_edit_tab_cap($tabname)){
            print_error('nopermissions', '', '', 'access question edit tab '.$tabname);
        }
    }
}

//capabilities for each page of edit tab.
//this determines which contexts' categories are available. At least one
//page is displayed if user has one of the capability on at least one context
$QUESTION_EDITTABCAPS = array(
                            'editq' => array('moodle/question:add',
                                'moodle/question:editmine',
                                'moodle/question:editall',
                                'moodle/question:viewmine',
                                'moodle/question:viewall',
                                'moodle/question:usemine',
                                'moodle/question:useall',
                                'moodle/question:movemine',
                                'moodle/question:moveall'),
                            'questions'=>array('moodle/question:add',
                                'moodle/question:editmine',
                                'moodle/question:editall',
                                'moodle/question:viewmine',
                                'moodle/question:viewall',
                                'moodle/question:movemine',
                                'moodle/question:moveall'),
                           'categories'=>array('moodle/question:managecategory'),
                           'import'=>array('moodle/question:add'),
                           'export'=>array('moodle/question:viewall', 'moodle/question:viewmine'));



/**
 * Make sure user is logged in as required in this context.
 */
function require_login_in_context($contextorid = null){
    if (!is_object($contextorid)){
        $context = get_context_instance_by_id($contextorid);
    } else {
        $context = $contextorid;
    }
    if ($context && ($context->contextlevel == CONTEXT_COURSE)) {
        require_login($context->instanceid);
    } else if ($context && ($context->contextlevel == CONTEXT_MODULE)) {
        if ($cm = get_record('course_modules','id',$context->instanceid)) {
            if (!$course = get_record('course', 'id', $cm->course)) {
                error('Incorrect course.');
            }
            require_course_login($course, true, $cm);

        } else {
            error('Incorrect course module id.');
        }
    } else if ($context && ($context->contextlevel == CONTEXT_SYSTEM)) {
        if (!empty($CFG->forcelogin)) {
            require_login();
        }

    } else {
        require_login();
    }
}
?>
