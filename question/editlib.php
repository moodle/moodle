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
 * Functions used to show question editing interface
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 *//** */

require_once($CFG->libdir.'/questionlib.php');

define('DEFAULT_QUESTIONS_PER_PAGE', 20);

function get_module_from_cmid($cmid) {
    global $CFG, $DB;
    if (!$cmrec = $DB->get_record_sql("SELECT cm.*, md.name as modname
                               FROM {course_modules} cm,
                                    {modules} md
                               WHERE cm.id = ? AND
                                     md.id = cm.module", array($cmid))){
        print_error('invalidcoursemodule');
    } elseif (!$modrec =$DB->get_record($cmrec->modname, array('id' => $cmrec->instance))) {
        print_error('invalidcoursemodule');
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

    global $QTYPES, $DB;

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
    list ($usql, $params) = $DB->get_in_or_equal(explode(',', $categorylist));
    if ($questions = $DB->get_records_select("question","category $usql $npsql", $params, "qtype, name ASC")) {

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
    global $DB;
    return 1 == $DB->count_records_sql("
            SELECT count(*)
              FROM {question_categories} c1,
                   {question_categories} c2
             WHERE c2.id = ?
               AND c1.contextid = c2.contextid
               AND c1.parent = 0 AND c2.parent = 0", array($categoryid));
}

/**
 * Check whether this user is allowed to delete this category.
 *
 * @param integer $todelete a category id.
 */
function question_can_delete_cat($todelete) {
    global $DB;
    if (question_is_only_toplevel_category_in_context($todelete)) {
        print_error('cannotdeletecate', 'question');
    } else {
        $contextid = $DB->get_field('question_categories', 'contextid', array('id' => $todelete));
        require_capability('moodle/question:managecategory', get_context_instance_by_id($contextid));
    }
}

abstract class question_bank_column_base {
    protected $qbank;

    /**
     * Constructor.
     * @param $qbank the question_bank_view we are helping to render.
     */
    public function __construct(question_bank_view $qbank) {
        $this->qbank = $qbank;
        $this->init();
    }

    /**
     * A chance for subclasses to initialise themselves, for example to load lang strings,
     * without having to override the constructor.
     */
    protected function init() {
    }

    /**
     * Output the column header cell.
     * @param integer $currentsort 0 for none. 1 for normal sort, -1 for reverse sort.
     */
    public function display_header() {
        echo '<th class=header ' . $this->get_name() . '" scope="col">';
        $sortable = $this->is_sortable();
        $name = $this->get_name();
        $title = $this->get_title();
        if (is_array($sortable)) {
            if ($title) {
                echo '<div class="title">' . $title . '</div>';
            }
            $links = array();
            foreach ($sortable as $subsort => $details) {
                $links[] = $this->make_sort_link($name . '_' . $subsort, $details['title'], !empty($details['reverse']));
            }
            echo implode(' / ', $links);
        } else if ($sortable) {
            echo $this->make_sort_link($name, $this->qbank->get_sort($name), $title);
        } else {
            echo $title;
        }
        echo "</th>\n";
    }

    /**
     * Title for this column. Not used if is_sortable returns an array.
     * @param object $question the row from the $question table, augmented with extra information.
     * @param string $rowclasses CSS class names that should be applied to this row of output.
     */
    protected function get_title() {
        return '';
    }

    /**
     * Get a link that changes the sort order, and indicates the current sort state.
     * @param $name internal name used for this type of sorting.
     * @param $currentsort the current sort order -1, 0, 1 for descending, none, ascending.
     * @param $title the link text.
     * @param $defaultreverse whether the default sort order for this column is descending, rather than ascending.
     * @return string HTML fragment.
     */
    protected function make_sort_link($name, $currentsort, $title, $defaultreverse = false) {
        $newsortreverse = $defaultreverse;
        if ($currentsort) {
            $newsortreverse = $currentsort > 0;
        }
        if ($newsortreverse) {
            $tip = get_string('sortbyxreverse', '', $title);
        } else {
            $tip = get_string('sortbyx', '', $title);
        }
        echo '<a href="' . $this->qbank->new_sort_url($name, $newsortreverse) . '" title="' . $tip . '">';
        echo $title;
        if ($currentsort) {
            echo $this->get_sort_icon($currentsort < 0);
        }
        echo '</a>';
    }

    /**
     * Get an icon representing the corrent sort state.
     * @param $reverse sort is descending, not ascending.
     * @return string HTML image tag.
     */
    protected function get_sort_icon($reverse) {
        global $CFG;
        if ($reverse) {
            return ' <img src="' . $CFG->pixpath . '/t/up.gif" alt="' . get_string('desc') . '" />';
        } else {
            return ' <img src="' . $CFG->pixpath . '/t/down.gif" alt="' . get_string('asc') . '" />';
        }
    }

    /**
     * Output this column.
     * @param object $question the row from the $question table, augmented with extra information.
     * @param string $rowclasses CSS class names that should be applied to this row of output.
     */
    public function display($question, $rowclasses) {
        $this->display_start($question, $rowclasses);
        $this->display_content($question, $rowclasses);
        $this->display_end($question, $rowclasses);
    }

    protected function display_start($question, $rowclasses) {
        echo '<td class="' . $this->get_name() . '">';
    }

    /**
     * @param object $question the row from the $question table, augmented with extra information.
     * @return string internal name for this column. Used as a CSS class name, and to store information about the current sort.
     */
    abstract protected function get_name();

    /**
     * Output the contents of this column.
     * @param object $question the row from the $question table, augmented with extra information.
     * @param string $rowclasses CSS class names that should be applied to this row of output.
     */
    abstract protected function display_content($question, $rowclasses);

    protected function display_end($question, $rowclasses) {
        echo "</td>\n";
    }

    /**
     * Return an array 'table_alias' => 'JOIN clause' to bring in any data that
     * this column required.
     *
     * The return values for all the columns will be checked. It is OK if two
     * columns join in the same table with the same alias and identical JOIN clauses.
     * If to columns try to use the same alias with different joins, you get an error.
     * The only table included by default is the question table, which is aliased to 'q'.
     *
     * @return array 'table_alias' => 'JOIN clause'
     */
    public function get_extra_joins() {
        return array();
    }

    /**
     * @return array fields required. use table alias 'q' for the question table, or one of the
     * ones from get_extra_joins. Every field requested must specify a table prefix.
     */
    public function get_required_fields() {
        return array();
    }

    /**
     * Can this column be sorted on? You can return either:
     *  + false for no (the default),
     *  + a field name, if sorting this column corresponds to sorting on that datbase field.
     *  + an array of subnames to sort on as follows
     *  return array(
     *      'firstname' => array('field' => 'uc.firstname', 'title' => get_string('firstname')),
     *      'lastname' => array('field' => 'uc.lastname', 'field' => get_string('lastname')),
     *  );
     * As well as field, and field, you can also add 'revers' => 1 if you want the default sort
     * order to be DESC.
     * @return mixed as above.
     */
    public function is_sortable() {
        return false;
    }

    /**
     * Helper method for building sort clauses.
     * @param boolean $reverse whether the normal direction should be reversed.
     * @param string $normaldir 'ASC' or 'DESC'
     * @return string 'ASC' or 'DESC'
     */
    protected function sortorder($reverse) {
        if ($reverse) {
            return ' DESC';
        } else {
            return ' ASC';
        }
    }

    /**
     * @param $reverse Whether to sort in the reverse of the default sort order.
     * @param $subsort if is_sortable returns an array of subnames, then this will be
     *      one of those. Otherwise will be empty.
     * @return string some SQL to go in the order by clause.
     */
    public function sort_expression($reverse, $subsort) {
        $sortable = $this->is_sortable();
        if (is_array($sortable)) {
            if (array_key_exists($subsort, $sortable)) {
                return $sortable[$sortable]['field'] . $this->sortorder($reverse, !empty($sortable[$sortable]['reverse']));
            } else {
                throw new coding_exception('Unexpected $subsort type: ' . $subsort);
            }
        } else if ($sortable) {
            return $sortable . $this->sortorder($reverse);
        } else {
            throw new coding_exception('sort_expression called on a non-sortable column.');
        }
    }
}

/**
 * A column with a checkbox for each question with name q{questionid}.
 */
class question_bank_checkbox_column extends question_bank_column_base {
    protected $strselect;

    public function init() {
        $this->strselect = get_string('select', 'quiz');
    }

    protected function get_name() {
        return 'checkbox';
    }

    protected function get_title() {
        return '<input type="checkbox" disabled="disabled" />';
    }

    protected function display_content($question, $rowclasses) {
        echo '<input title="' . $this->strselect . '" type="checkbox" name="q' .
                $question->id . '" id="checkq' . $question->id . '" value="1" />';
    }

    public function get_required_fields() {
        return array('q.id');
    }
}

/**
 * A column type for the name of the question type.
 */
class question_bank_question_type_column extends question_bank_column_base {
    protected function get_name() {
        return 'qtype';
    }

    protected function get_title() {
        return get_string('qtypeveryshort', 'question');
    }

    protected function display_content($question, $rowclasses) {
        echo print_question_icon($question);
    }

    public function get_required_fields() {
        return array('q.qtype');
    }

    public function is_sortable() {
        return 'q.qtype';
    }
}

/**
 * A column type for the name of the question name.
 */
class question_bank_question_name_column extends question_bank_column_base {
    protected function get_name() {
        return 'questionname';
    }

    protected function get_title() {
        return get_string('question');
    }

    protected function display_content($question, $rowclasses) {
        echo format_string($question->name);
    }

    public function get_required_fields() {
        return array('q.name');
    }

    public function is_sortable() {
        return 'q.name';
    }
}

/**
 * A column type for the name of the question creator.
 */
class question_bank_creator_name_column extends question_bank_column_base {
    protected function get_name() {
        return 'creatorname';
    }

    protected function get_title() {
        return get_string('createdby', 'question');
    }

    protected function display_content($question, $rowclasses) {
        if (!empty($question->creatorfirstname) && !empty($question->creatorlastname)) {
            $u = new stdClass;
            $u->firstname = $question->creatorfirstname;
            $u->lastname = $question->creatorlastname;
            echo fullname($u);
        }
    }

    public function get_extra_joins() {
        return array('uc' => 'LEFT JOIN {user} uc ON uc.id = q.createdby');
    }

    public function get_required_fields() {
        return array('uc.firstname AS creatorfirstname', 'uc.lastname AS creatorlastname');
    }

    public function is_sortable() {
        return array(
            'firstname' => array('field' => 'uc.firstname', 'title' => get_string('firstname')),
            'lastname' => array('field' => 'uc.lastname', 'title' => get_string('lastname')),
        );
    }
}

/**
 * A column type for the name of the question last modifier.
 */
class question_bank_modifier_name_column extends question_bank_column_base {
    protected function get_name() {
        return 'modifiername';
    }

    protected function get_title() {
        return get_string('lastmodifiedby', 'question');
    }

    protected function display_content($question, $rowclasses) {
        if (!empty($question->modifierfirstname) && !empty($question->modifierlastname)) {
            $u = new stdClass;
            $u->firstname = $question->modifierfirstname;
            $u->lastname = $question->modifierlastname;
            echo fullname($u);
        }
    }

    public function get_extra_joins() {
        return array('um' => 'LEFT JOIN {user} um ON um.id = q.modifiedby');
    }

    public function get_required_fields() {
        return array('um.firstname AS modifierfirstname', 'um.lastname AS modifierlastname');
    }

    public function is_sortable() {
        return array(
            'firstname' => array('field' => 'um.firstname', 'title' => get_string('firstname')),
            'lastname' => array('field' => 'um.lastname', 'title' => get_string('lastname')),
        );
    }
}

/**
 * A base class for actions that are an icon that lets you manipulate the question in some way.
 */
abstract class question_bank_action_column_base extends question_bank_column_base {

    protected function get_title() {
        return '&#160;';
    }

    protected function print_icon($icon, $title, $url) {
        global $CFG;
        echo '<a title="' . $title . '" href="' . $url . '">
                <img src="' . $CFG->pixpath . '/t/' . $icon . '" class="iconsmall" alt="' . $title . '" /></a>';
    }

    public function get_required_fields() {
        return array('q.id');
    }
}

class question_bank_edit_action_column extends question_bank_action_column_base {
    protected $stredit;
    protected $strview;

    public function init() {
        parent::init();
        $this->stredit = get_string('edit');
        $this->strview = get_string('view');
    }

    protected function get_name() {
        return 'editaction';
    }

    protected function display_content($question, $rowclasses) {
        if (question_has_capability_on($question, 'edit') ||
                question_has_capability_on($question, 'move')) {
            $this->print_icon('edit', $this->stredit, $this->qbank->edit_question_url($question->id));
        } else {
            $this->print_icon('info', $this->strview, $this->qbank->edit_question_url($question->id));
        }
    }
}

class question_bank_preview_action_column extends question_bank_action_column_base {
    protected $strpreview;

    public function init() {
        parent::init();
        $this->stredit = get_string('preview');
    }

    protected function get_name() {
        return 'previewaction';
    }

    protected function display_content($question, $rowclasses) {
        if (question_has_capability_on($question, 'use')) {
            link_to_popup_window($this->qbank->preview_question_url($question->id), 'questionpreview',
                    ' <img src="' . $CFG->pixpath . '/t/preview.gif" class="iconsmall" alt="' . $this->strpreview . '" />',
                    0, 0, $this->strpreview, QUESTION_PREVIEW_POPUP_OPTIONS);
        }
    }

    public function get_required_fields() {
        return array('q.id');
    }
}

class question_bank_move_action_column extends question_bank_action_column_base {
    protected $strmove;

    public function init() {
        parent::init();
        $this->strmove = get_string('move');
    }

    protected function get_name() {
        return 'editaction';
    }

    protected function display_content($question, $rowclasses) {
        if (question_has_capability_on($question, 'move')) {
            $this->print_icon('move', $this->strmove, $this->qbank->edit_question_url($question->id));
        }
    }
}

/**
 * action to delete (or hide) a question, or restore a previously hidden question.
 */
class question_bank_delete_action_column extends question_bank_action_column_base {
    protected $strdelete;
    protected $strrestore;

    public function init() {
        parent::init();
        $this->strdelete = get_string('delete');
        $this->strrestore = get_string('restore');
    }

    protected function get_name() {
        return 'deleteaction';
    }

    protected function display_content($question, $rowclasses) {
        if (question_has_capability_on($question, 'edit')) {
            if ($question->hidden) {
                $this->print_icon('restore', $this->strrestore, $this->qbank->base_url()->out(false, array('unhide' => $question->id)));
            } else {
                $this->print_icon('restore', $this->strrestore,
                        $this->qbank->base_url()->out(false, array('deleteselected' => $question->id, 'q' . $question->id => 1)));
            }
        }
    }

    public function get_required_fields() {
        return array('q.id', 'q.hidden');
    }
}

/**
 * This class prints a view of the question bank, including
 *  + Some controls to allow users to to select what is displayed.
 *  + A list of questions as a table.
 *  + Further controls to do things with the questions.
 *
 * This class gives a basic view, and provides plenty of hooks where subclasses
 * can override parts of the display.
 *
 * The list of questions presented as a table is generated by creating a list of
 * question_bank_column objects, one for each 'column' to be displayed. These
 * manage
 *  + outputting the contents of that column, given a $question object, but also
 *  + generating the right fragments of SQL to ensure the necessary data is present,
 *    and sorted in the right order.
 *  + outputting table headers.
 */
class question_bank_view {
    protected $baseurl;
    protected $editquestionurl;
    protected $quizorcourseid;
    protected $contexts;
    protected $cm;

    public function __construct($contexts, $pageurl, $cm = null) {
        global $CFG, $COURSE;

        $this->contexts = $contexts;
        $this->baseurl = $pageurl;
        $this->cm = $cm;

        if (!empty($cm) && $cm->modname == 'quiz') {
            $this->quizorcourseid = '&amp;quizid=' . $cm->instance;
        } else {
            $this->quizorcourseid = '&amp;courseid=' .$COURSE->id;
        }

        // Create the url of the new question page to forward to.
        $this->editquestionurl = new moodle_url("$CFG->wwwroot/question/question.php",
                array('returnurl' => $pageurl->out()));
        if ($cm !== null){
            $this->editquestionurl->param('cmid', $cm->id);
        } else {
            $this->editquestionurl->param('courseid', $COURSE->id);
        }
    }

    public function base_url($questionid) {
        return $baseurl;
    }

    public function edit_question_url($questionid) {
        return $this->editquestionurl->out(false, array('id' => $questionid));
    }

    public function preview_question_url($questionid) {
        global $CFG;
        return $CFG->wwwroot . '/question/preview.php?id=' . $question->id . $this->quizorcourseid;
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
     */
    public function display($tabname, $page, $perpage, $sortorder,
            $sortorderdecoded, $cat, $recurse, $showhidden, $showquestiontext){
        global $COURSE, $DB;

        if ($this->process_actions_needing_ui()) {
            return;
        }

        // Category selection form
        print_box_start('generalbox questionbank');
        print_heading(get_string('questionbank', 'question'), '', 2);

        $this->display_category_form($this->contexts->having_one_edit_tab_cap($tabname),
                $this->baseurl, $cat, $recurse, $showhidden, $showquestiontext);

        // continues with list of questions
        $this->display_question_list($this->contexts->having_one_edit_tab_cap($tabname), $this->baseurl, $cat, $this->cm,
                $recurse, $page, $perpage, $showhidden, $sortorder, $sortorderdecoded, $showquestiontext,
                $this->contexts->having_cap('moodle/question:add'));

        print_box_end();
    }

    /**
     * prints a form to choose categories
     */
    protected function display_category_form($contexts, $pageurl, $current, $recurse=1,
            $showhidden=false, $showquestiontext=false) {
        global $CFG;

    /// Get all the existing categories now
        $catmenu = question_category_options($contexts, false, 0, true);

        $strcategory = get_string('category', 'quiz');
        $strshow = get_string('show', 'quiz');
        $streditcats = get_string('editcategories', 'quiz');

        popup_form('edit.php?'.$pageurl->get_query_string().'&amp;category=',
                $catmenu, 'catmenu', $current, '', '', '', false, 'self',
                "<strong>$strcategory</strong>");

        echo '<form method="get" action="edit.php" id="displayoptions">';
        echo "<fieldset class='invisiblefieldset'>";
        echo $pageurl->hidden_params_out(array('recurse', 'showhidden', 'showquestiontext'));
        $this->display_category_form_checkbox('recurse', $recurse);
        $this->display_category_form_checkbox('showhidden', $showhidden);
        $this->display_category_form_checkbox('showquestiontext', $showquestiontext);
        echo '<noscript><div class="centerpara"><input type="submit" value="'. get_string('go') .'" />';
        echo '</div></noscript></fieldset></form>';
    }

    /**
     * Private funciton to help the preceeding function.
     */
    protected function display_category_form_checkbox($name, $checked) {
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
    protected function display_question_list($contexts, $pageurl, $categoryandcontext,
            $cm = null, $recurse=1, $page=0, $perpage=100, $showhidden=false,
            $sortorder='typename', $sortorderdecoded='qtype, name ASC',
            $showquestiontext = false, $addcontexts = array()) {
        global $CFG, $COURSE, $DB;

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
        $strquestion = get_string("question", "quiz");
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

        // Create the url of the new question page to forward to.
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
        echo "</div>\n";

        echo '<div class="createnewquestion">';
        if ($canadd) {
            popup_form($questionurl->out(false, array('category' => $category->id)).
                    '&amp;qtype=', $qtypemenu, "addquestion", "", "choose", "",
                    "", false, "self", "<strong>$strcreatenewquestion</strong>");
            helpbutton("questiontypes", $strcreatenewquestion, "quiz");
        } else {
            print_string('nopermissionadd', 'question');
        }
        echo '</div>';

        $categorylist = ($recurse) ? question_categorylist($category->id) : $category->id;
        $categorylist_array =  explode(',', $categorylist);

        $showhidden = $showhidden ? '' : " AND hidden = '0'";

        list($usql, $params) = $DB->get_in_or_equal($categorylist_array);
        if (!$totalnumber = $DB->count_records_select('question',
                "category $usql AND parent = '0' $showhidden", $params)) {
            echo '<div class="categoryquestionscontainer noquestionsincategory">';
            print_string('noquestions', 'quiz');
            echo '</div>';
            return;
        }

        if (!$questions = $DB->get_records_select('question',
                "category $usql AND parent = '0' $showhidden", $params, $sortorderdecoded,
                '*', $page*$perpage, $perpage)) {

            // There are no questions on the requested page.
            $page = 0;
            if (!$questions = $DB->get_records_select('question',
                    "category $usql AND parent = '0' $showhidden", $params, $sortorderdecoded,
                    '*', 0, $perpage)) {
                // There are no questions at all
                echo '<div class="categoryquestionscontainer noquestionsincategory">';
                print_string('noquestions', 'quiz');
                echo '</div>';
                return;
            }
        }

        echo '<div class="categorysortopotionscontainer">';
        $this->display_question_sort_options($pageurl, $sortorder);
        echo '</div>';

        echo '<div class="categorypagingbarcontainer">';
        print_paging_bar($totalnumber, $page, $perpage, $pageurl, 'qpage');
        echo '</div>';

        echo '<form method="post" action="edit.php">';
        echo '<fieldset class="invisiblefieldset" style="display: block;">';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        echo $pageurl->hidden_params_out();
        echo '<div class="categoryquestionscontainer">';
        echo '<table id="categoryquestions" style="width: 100%"><colgroup><col id="qaction"></col><col id="qname"></col><col id="qextraactions"></col></colgroup><tr>';
        echo "<th style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">$straction</th>";

        echo "<th style=\"white-space:nowrap; text-align: left;\" class=\"header\" scope=\"col\">$strquestion</th>";
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

            $canuseq = question_has_capability_on($question, 'use', $question->category);
            if (function_exists('module_specific_actions')) {
                echo module_specific_actions($pageurl, $question->id, $cm->id, $canuseq);
            }

            if ($caneditall || $canmoveall || $canuseall){
                echo "<input title=\"$strselect\" type=\"checkbox\" name=\"q$question->id\" id=\"checkq$question->id\" value=\"1\" />";
            }
            echo "</td>\n";

            echo "<td $nameclass><div>";
            print_question_icon($question);
            echo format_string($question->name);
            echo "</div></td>\n";

            echo "<td>\n";

            // edit, hide, delete question, using question capabilities, not quiz capabilities
            if (question_has_capability_on($question, 'edit', $question->category) ||
                    question_has_capability_on($question, 'move', $question->category)) {
                echo "<a title=\"$stredit\" href=\"".$questionurl->out(false, array('id'=>$question->id))."\">
                        <img src=\"$CFG->pixpath/t/edit.gif\" alt=\"$stredit\" /></a>";
            } elseif (question_has_capability_on($question, 'view', $question->category)) {
                echo "<a title=\"$strview\" href=\"".$questionurl->out(false, array('id'=>$question->id))."\">
                        <img src=\"$CFG->pixpath/i/info.gif\" alt=\"$strview\" /></a>";
            }

            // preview
            if ($canuseq) {
                $quizorcourseid = $quizid?('&amp;quizid=' . $quizid):('&amp;courseid=' .$COURSE->id);
                link_to_popup_window('/question/preview.php?id=' . $question->id .
                        $quizorcourseid, 'questionpreview',
                        " <img src=\"$CFG->pixpath/t/preview.gif\" class=\"iconsmall\" alt=\"$strpreview\" />",
                        0, 0, $strpreview, QUESTION_PREVIEW_POPUP_OPTIONS);
            }

            if (question_has_capability_on($question, 'move', $question->category) && question_has_capability_on($question, 'view', $question->category)) {
                echo "<a title=\"$strmove\" href=\"".$questionurl->out(false, array('id'=>$question->id, 'movecontext'=>1))."\">
                        <img src=\"$CFG->pixpath/t/move.gif\" alt=\"$strmove\" /></a>";
            }

            if (question_has_capability_on($question, 'edit', $question->category)) {
                // hide-feature
                if($question->hidden) {
                    echo "<a title=\"$strrestore\" href=\"edit.php?".$pageurl->get_query_string()."&amp;unhide=$question->id&amp;sesskey=".sesskey()."\">
                            <img src=\"$CFG->pixpath/t/restore.gif\" alt=\"$strrestore\" /></a>";
                } else {
                    echo "<a title=\"$strdelete\" href=\"edit.php?".$pageurl->get_query_string()."&amp;deleteselected=$question->id&amp;q$question->id=1\">
                            <img src=\"$CFG->pixpath/t/delete.gif\" alt=\"$strdelete\" /></a>";
                }
            }
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
        echo "</table></div>\n";

        echo '<div class="categorypagingbarcontainer pagingbottom">';
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
                echo module_specific_buttons($cm->id);
            }

            // print delete and move selected question
            if ($caneditall) {
                echo '<input type="submit" name="deleteselected" value="' . $strdelete . "\" />\n";
            }

            if ($canmoveall && count($addcontexts)) {
                echo '<input type="submit" name="move" value="'.get_string('moveto', 'quiz')."\" />\n";
                question_category_select_menu($addcontexts, false, 0, "$category->id,$category->contextid");
            }

            if (function_exists('module_specific_controls') && $canuseall) {
                $modulespecific = module_specific_controls($totalnumber, $recurse, $category, $cm->id);
                if(!empty($modulespecific)){
                    echo "<hr />$modulespecific";
                }
            }
        }
        echo "</div>\n";

        echo '</fieldset>';
        echo "</form>\n";
    }

    protected function display_question_sort_options($pageurl, $sortorder){
        //sort options
        $html = "<div class=\"mdl-align questionsortoptions\">";
        // POST method should only be used for parameters that change data
        // or if POST method has to be used, the user must be redirected immediately to
        // non-POSTed page to not break the back button
        $html .= '<form method="get" action="edit.php">';
        $html .= '<fieldset class="invisiblefieldset" style="display: block;">';
        $html .= '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        $html .= $pageurl->hidden_params_out(array('qsortorder'));
        //choose_from_menu concatenates the form name with
        //"menu" so the label is for menuqsortorder
        $sortoptions = array('alpha' => get_string("qname", "quiz"),
                             'typealpha' => get_string("qtypename", "quiz"),
                             'age' => get_string("age", "quiz"));
        $a =  choose_from_menu($sortoptions, 'qsortorder', $sortorder, false, 'this.form.submit();', '0', true);
        $html .= '<label for="menuqsortorder">'.get_string('sortquestionsbyx', 'quiz', $a).'</label>';
        $html .=  '<noscript><div><input type="submit" value="'.get_string("sortsubmit", "quiz").'" /></div></noscript>';
        $html .= '</fieldset>';
        $html .= "</form>\n";
        $html .= "</div>\n";
        echo $html;
    }

    public function process_actions() {
        global $CFG, $COURSE, $DB;
        /// Now, check for commands on this page and modify variables as necessary
        if (optional_param('move', false, PARAM_BOOL) and confirm_sesskey()) { /// Move selected questions to new category
            $category = required_param('category', PARAM_SEQUENCE);
            list($tocategoryid, $contextid) = explode(',', $category);
            if (! $tocategory = $DB->get_record('question_categories', array('id' => $tocategoryid, 'contextid' => $contextid))) {
                print_error('cannotfindcate', 'question');
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
                list($usql, $params) = $DB->get_in_or_equal($questionids);
                $sql = "SELECT q.*, c.contextid FROM {question} q, {question_categories} c WHERE q.id $usql AND c.id = q.category";
                if (!$questions = $DB->get_records_sql($sql, $params)){
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
                            if ($DB->record_exists('quiz_question_instances', array('question' => $questionid))) {
                                if (!$DB->set_field('question', 'hidden', 1, array('id' => $questionid))) {
                                    question_require_capability_on($questionid, 'edit');
                                    print_error('cannothidequestion', 'question');
                                }
                            } else {
                                delete_question($questionid);
                            }
                        }
                    }
                    redirect($pageurl->out());
                } else {
                    print_error('invalidconfirm', 'question');
                }
            }
        }

        // Unhide a question
        if(($unhide = optional_param('unhide', '', PARAM_INT)) and confirm_sesskey()) {
            question_require_capability_on($unhide, 'edit');
            if(!$DB->set_field('question', 'hidden', 0, array('id', $unhide))) {
                print_error('cannotunhidequestion', 'question');
            }
            redirect($pageurl->out());
        }
    }

    public function process_actions_needing_ui() {
        if (optional_param('deleteselected', false, PARAM_BOOL)) {
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
                    if ($DB->record_exists('quiz_question_instances', array('question' => $key))) {
                        $questionnames .= '* ';
                        $inuse = true;
                    }
                    $questionnames .= $DB->get_field('question', 'name', array('id' => $key)) . '<br />';
                }
            }
            if (!$questionlist) { // no questions were selected
                redirect($this->baseurl->out());
            }
            $questionlist = rtrim($questionlist, ',');

            // Add an explanation about questions in use
            if ($inuse) {
                $questionnames .= '<br />'.get_string('questionsinuse', 'quiz');
            }
            notice_yesno(get_string("deletequestionscheck", "quiz", $questionnames),
                        $pageurl->out_action(array('deleteselected'=>$questionlist, 'confirm'=>md5($questionlist))),
                        $pageurl->out_action());

            return true;
        }
    }
}

/**
 * Common setup for all pages for editing questions.
 * @param string $edittab code for this edit tab
 * @param boolean $requirecmid require cmid? default false
 * @param boolean $requirecourseid require courseid, if cmid is not given? default true
 * @return array $thispageurl, $contexts, $cmid, $cm, $module, $pagevars
 */
function question_edit_setup($edittab, $requirecmid = false, $requirecourseid = true){
    global $COURSE, $QUESTION_EDITTABCAPS, $DB;

    //$thispageurl is used to construct urls for all question edit pages we link to from this page. It contains an array
    //of parameters that are passed from page to page.
    $thispageurl = new moodle_url();
    $thispageurl->remove_params(); // We are going to explicity add back everything important - this avoids unwanted params from being retained.

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
        if (!$catparts[0] || (FALSE !== array_search($catparts[1], $contextlistarr)) ||
                !$DB->count_records_select("question_categories", "id = ? AND contextid = ?", array($catparts[0], $catparts[1]))) {
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
    global $DB;
    if (!is_object($contextorid)){
        $context = get_context_instance_by_id($contextorid);
    } else {
        $context = $contextorid;
    }
    if ($context && ($context->contextlevel == CONTEXT_COURSE)) {
        require_login($context->instanceid);
    } else if ($context && ($context->contextlevel == CONTEXT_MODULE)) {
        if ($cm = $DB->get_record('course_modules',array('id' =>$context->instanceid))) {
            if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
                print_error('invalidcourseid');
            }
            require_course_login($course, true, $cm);

        } else {
            print_error('invalidcoursemodule');
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
