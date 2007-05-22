<?php // $Id$
/**
 * Functions used to show question editing interface
 *
 * TODO: currently the function question_list still provides controls specific
 *       to the quiz module. This needs to be generalised.
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
    $modrec->cmid = $modrec->id;
    
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
* Gets the default category in a course
*
* It returns the first category with no parent category. If no categories
* exist yet then one is created.
* @return object The default category
* @param integer $courseid  The id of the course whose default category is wanted
*/
function get_default_question_category($courseid) {
    // If it already exists, just return it.
    if ($category = get_records_select("question_categories", "course = '$courseid' AND parent = '0'", 'id', '*', '', 1)) {
        return reset($category);
    }

    // Otherwise, we need to make one
    $category = new stdClass;
    $category->name = get_string("default", "quiz");
    $category->info = get_string("defaultinfo", "quiz");
    $category->course = $courseid;
    $category->parent = 0;
    $category->sortorder = 999; // By default, all categories get this number, and are sorted alphabetically.
    $category->publish = 0;
    $category->stamp = make_unique_id_code();

    if (!$category->id = insert_record("question_categories", $category)) {
        notify("Error creating a default category!");
        return false;
    }
    return $category;
}

/**
 * prints a form to choose categories
 */
function question_category_form($course, $pageurl, $current, $recurse=1, $showhidden=false, $showquestiontext=false) {
    global $CFG;

/// Make sure the default category exists for this course
    get_default_question_category($course->id);

/// Get all the existing categories now
    $catmenu = question_category_options($course->id, true);

    $strcategory = get_string("category", "quiz");
    $strshow = get_string("show", "quiz");
    $streditcats = get_string("editcategories", "quiz");

    popup_form ("edit.php?".$pageurl->get_query_string()."&amp;category=", $catmenu, "catmenu", $current, "", "", "", false, "self", "<strong>$strcategory</strong>");

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
function question_list($course, $pageurl, $categoryid, $cm = null,
        $recurse=1, $page=0, $perpage=100, $showhidden=false, $sortorder='typename', $sortorderdecoded='qtype, name ASC',
        $showquestiontext = false) {
    global $QTYPE_MENU, $USER, $CFG, $THEME;
    
    $qtypemenu = $QTYPE_MENU;
    if ($rqp_types = get_records('question_rqp_types')) {
        foreach($rqp_types as $type) {
            $qtypemenu['rqp_'.$type->id] = $type->name;
        }
    }

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

    if (!$category = get_record('question_categories', 'id', $categoryid)) {
        notify('Category not found!');
        return;
    }
    $canedit = has_capability('moodle/question:manage', get_context_instance(CONTEXT_COURSE, $category->course));

    if ($cm AND $cm->modname == 'quiz') {
        $quizid = $cm->instance;
    } else {
        $quizid = 0;
    }
    
    echo '<div class="boxaligncenter">';
    $formatoptions = new stdClass;
    $formatoptions->noclean = true;
    echo format_text($category->info, FORMAT_MOODLE, $formatoptions, $course->id);

    echo '<table><tr>';

    // check if editing questions in this category is allowed
    if ($canedit) {
        echo '<td valign="top" align="right">';
        $returnurl = urlencode($pageurl->out());
        $questionurl = new moodle_url("$CFG->wwwroot/question/question.php", 
                                    array('returnurl' => $returnurl,
                                          'category' => $category->id));
        if ($cm!==null){
            $questionurl->param('cmid', $cm->id);
        }
        popup_form ($questionurl->out().'&amp;qtype=', $qtypemenu, "addquestion", "", "choose", "", "", false, "self", "<strong>$strcreatenewquestion</strong>");
        echo '</td><td valign="top" align="right">';
        helpbutton("questiontypes", $strcreatenewquestion, "quiz");
        echo '</td>';
    }
    else {
        echo '<td>';
        print_string("publishedit","quiz");
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

    echo '<form method="post" action="edit.php">';
    echo '<fieldset class="invisiblefieldset" style="display: block;">';
    echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    echo $pageurl->hidden_params_out(array('qsortorder'));
    echo '<table id="categoryquestions" style="width: 100%"><tr>';
    echo "<th style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">$straction</th>";
    
    $sortoptions = array('alpha' => get_string("sortalpha", "quiz"),
                         'typealpha' => get_string("sorttypealpha", "quiz"),
                         'age' => get_string("sortage", "quiz"));
    $orderselect  = choose_from_menu ($sortoptions, 'qsortorder', $sortorder, false, 'this.form.submit();', '0', true);
    $orderselect .= '<noscript><div><input type="submit" value="'.get_string("sortsubmit", "quiz").'" /></div></noscript>';
    echo "<th style=\"white-space:nowrap; text-align: left;\" class=\"header\" scope=\"col\">$strquestionname $orderselect</th>
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
        if ($nameclass) {
            $nameclass = 'class="' . $nameclass . '"';
        }
        if ($textclass) {
            $textclass = 'class="' . $textclass . '"';
        }
        
        echo "<tr>\n<td style=\"white-space:nowrap;\" $nameclass>\n";
        
        if (function_exists('module_specific_actions')) {
            echo module_specific_actions($pageurl, $question->id, $cm->id);
        }
       
        // preview
        link_to_popup_window('/question/preview.php?id=' . $question->id . '&amp;quizid=' . $quizid, 'questionpreview',
                "<img src=\"$CFG->pixpath/t/preview.gif\" class=\"iconsmall\" alt=\"$strpreview\" />",
                0, 0, $strpreview, QUESTION_PREVIEW_POPUP_OPTIONS);
        
        // edit, hide, delete question, using question capabilities, not quiz capabilieies
        if ($canedit) {
            $questionparams = (($cm !== null)? array('cmid' => $cm->id) : array()) +
                                (array('returnurl' => $pageurl->out(), 'id'=>$question->id));
            $questionurl = new moodle_url("$CFG->wwwroot/question/question.php", $questionparams);
            echo "<a title=\"$stredit\" href=\"".$questionurl->out()."\"><img
                    src=\"$CFG->pixpath/t/edit.gif\" alt=\"$stredit\" /></a>&nbsp;";
            // hide-feature
            if($question->hidden) {
                echo "<a title=\"$strrestore\" href=\"edit.php?".$pageurl->get_query_string()."&amp;unhide=$question->id&amp;sesskey=$USER->sesskey\"><img
                        src=\"$CFG->pixpath/t/restore.gif\" alt=\"$strrestore\" /></a>";
            } else {
                echo "<a title=\"$strdelete\" href=\"edit.php?".$pageurl->get_query_string()."&amp;deleteselected=$question->id&amp;q$question->id=1\"><img
                        src=\"$CFG->pixpath/t/delete.gif\" alt=\"$strdelete\" /></a>";
            }
        }
        echo "&nbsp;<input title=\"$strselect\" type=\"checkbox\" name=\"q$question->id\" value=\"1\" />";
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
                    $formatoptions, $course->id);
            echo "</td></tr>\n";
        }
    }
    echo "</table>\n";

    $paging = print_paging_bar($totalnumber, $page, $perpage, $pageurl, 'qpage',
            false, true);
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

    echo '<table class="quiz-edit-selected"><tr><td colspan="2">';
    echo '<a href="javascript:select_all_in(\'TABLE\', null, \'categoryquestions\');">'.$strselectall.'</a> /'.
     ' <a href="javascript:deselect_all_in(\'TABLE\', null, \'categoryquestions\');">'.$strselectnone.'</a>'.
     '</td><td align="right"><b>&nbsp;'.get_string('withselected', 'quiz').':</b></td></tr><tr><td>';

    if (function_exists('module_specific_buttons')) {
        echo module_specific_buttons($cm->id);
    }
    // print delete and move selected question
    if ($canedit) {
        echo '<input type="submit" name="deleteselected" value="'.$strdelete."\" /></td><td>\n";
        echo '<input type="submit" name="move" value="'.get_string('moveto', 'quiz')."\" />\n";
        question_category_select_menu($course->id, false, true, $category->id);
    }
    echo "</td></tr></table>";

    if (function_exists('module_specific_controls')) {
        echo module_specific_controls($totalnumber, $recurse, $category->id, $cm->id);
    }
    echo '</fieldset>';
    echo "</form>\n";
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
function question_showbank($pageurl, $cm, $page, $perpage, $sortorder, $sortorderdecoded, $cat, $recurse, $showhidden, $showquestiontext){  
    global $COURSE;  

/// Now, check for commands on this page and modify variables as necessary
    if (isset($_REQUEST['move']) and confirm_sesskey()) { /// Move selected questions to new category
        $tocategoryid = required_param('category', PARAM_INT);
        if (!$tocategory = get_record('question_categories', 'id', $tocategoryid)) {
            error('Invalid category');
        }
        if (!has_capability('moodle/question:managecategory', get_context_instance(CONTEXT_COURSE, $tocategory->course))){
            error(get_string('categorynoedit', 'quiz', $tocategory->name), $pageurl->out());
        }
        foreach ($_POST as $key => $value) {    // Parse input for question ids
            if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
                $key = $matches[1];
                if (!set_field('question', 'category', $tocategory->id, 'id', $key)) {
                    error('Could not update category field');
                }
            }
        }
    }

    if (isset($_REQUEST['deleteselected'])) { // delete selected questions from the category

        if (isset($_REQUEST['confirm']) and confirm_sesskey()) { // teacher has already confirmed the action
            $deleteselected = required_param('deleteselected');
            if ($_REQUEST['confirm'] == md5($deleteselected)) {
                if ($questionlist = explode(',', $deleteselected)) {
                    // for each question either hide it if it is in use or delete it
                    foreach ($questionlist as $questionid) {
                        if (record_exists('quiz_question_instances', 'question', $questionid) or
                            record_exists('question_states', 'originalquestion', $questionid)) {
                            if (!set_field('question', 'hidden', 1, 'id', $questionid)) {
                               error('Was not able to hide question');
                            }
                        } else {
                            delete_question($questionid);
                        }
                    }
                }
                echo '</td></tr>';
                echo '</table>';
                echo '</div>';
                redirect($pageurl->out());
            } else {
                error("Confirmation string was incorrect");
            }

        } else { // teacher still has to confirm
            // make a list of all the questions that are selected
            $rawquestions = $_REQUEST;
            $questionlist = '';  // comma separated list of ids of questions to be deleted
            $questionnames = ''; // string with names of questions separated by <br /> with
                                 // an asterix in front of those that are in use
            $inuse = false;      // set to true if at least one of the questions is in use
            foreach ($rawquestions as $key => $value) {    // Parse input for question ids
                if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
                    $key = $matches[1];                    $questionlist .= $key.',';
                    if (record_exists('quiz_question_instances', 'question', $key) or
                        record_exists('question_states', 'originalquestion', $key)) {
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
                        $pageurl->out_action(array('deleteselected'=>$questionlist, 'confirm'=>md5($questionlist))),
                        $pageurl->out_action());

            echo '</td></tr>';
            echo '</table>';
            print_footer($COURSE);
            exit;
        }
    }

    // Unhide a question
    if(isset($_REQUEST['unhide']) && confirm_sesskey()) {
        $unhide = required_param('unhide', PARAM_INT);
        if(!set_field('question', 'hidden', 0, 'id', $unhide)) {
            error("Failed to unhide the question.");
        }
        redirect($pageurl->out());
    }

    // starts with category selection form
    print_box_start('generalbox questionbank');
    print_heading(get_string('questionbank', 'question'), '', 2);
    question_category_form($COURSE, $pageurl, $cat, $recurse,
            $showhidden, $showquestiontext);
    
    // continues with list of questions
    question_list($COURSE, $pageurl, $cat, isset($cm) ? $cm : null,
            $recurse, $page, $perpage, $showhidden, $sortorder, $sortorderdecoded, 
            $showquestiontext);

    print_box_end();
}
/**
 * Common setup for all pages for editing questions.
 * @param boolean $requirecmid require cmid? default false
 * @param boolean $requirecourseid require courseid, if cmid is not given? default true
 * @return array $thispageurl, $courseid, $cmid, $cm, $module, $pagevars
 */
function question_edit_setup($requirecmid = false, $requirecourseid = true){
    global $COURSE;
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
        }
    }
    require_login($courseid, false);
    
    
    $pagevars['qpage'] = optional_param('qpage', -1, PARAM_INT);
    
    //pass 'cat' from page to page and when 'category' comes from a drop down menu
    //then we also reset the qpage so we go to page 1 of 
    //a new cat.
    $pagevars['cat'] = optional_param('cat', 0, PARAM_INT);
    if  ($category = optional_param('category', 0, PARAM_INT)){
        $pagevars['cat'] = $category;
        $pagevars['qpage'] = 0;
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
    

    if (empty($pagevars['cat']) or !count_records_select("question_categories", "id = '".$pagevars['cat']."' AND (course = '{$COURSE->id}' OR publish = '1')")) {
        $category = get_default_question_category($COURSE->id);
        $pagevars['cat'] = $category->id;
        $thispageurl->param('cat', $category->id);
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
    if ($pagevars['cpage'] != 1){
        $thispageurl->param('cpage', $pagevars['cpage']);
    }
    

    return array($thispageurl, $courseid, $cmid, $cm, $module, $pagevars);
}
?>
