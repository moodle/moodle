<?php
/**
 *  This class controls from which category questions are listed.
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  2013 Tim Hunt, Ray Morris and others {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_question_bank_search_condition_category extends core_question_bank_search_condition {
    protected $category;
    protected $recurse;
    protected $where;
    protected $params;
    protected $cat;

    /**
     * Constructor
     * @param string     $cat           categoryID,contextID as used with question_bank_view->display()
     * @param boolean    $recurse       Whether to include questions from sub-categories
     * @param array      $contexts      Context objects as used by question_category_options()
     * @param moodle_url $baseurl       The URL the form is submitted to
     * @param stdClass   $course        Course record
     * @param integer    $maxinfolength The maximum displayed length of the category info
     */
    public function __construct($cat = null, $recurse = false, $contexts, $baseurl, $course, $maxinfolength = null) {
        $this->cat = $cat;
        $this->recurse = $recurse;
        $this->contexts = $contexts;
        $this->baseurl = $baseurl;
        $this->course = $course;
        $this->init();
        $this->maxinfolength = $maxinfolength;
    }

    /**
     * Initialize the object so it will be ready to return where() and params()
     */
    private function init() {
        global $DB;
        if (!$this->category = $this->get_current_category($this->cat)) {
            return;
        }
        if ($this->recurse) {
            $categoryids = question_categorylist($this->category->id);
        } else {
            $categoryids = array($this->category->id);
        }
        list($catidtest, $this->params) = $DB->get_in_or_equal($categoryids, SQL_PARAMS_NAMED, 'cat');
        $this->where = 'q.category ' . $catidtest;
    }

    /**
     * @returns string SQL fragment to be ANDed to the where clause to select which category of questions to display
     */
    public function where() {
        return  $this->where;
    }

    /**
     * @returns array Parameters to be bound to the SQL query to select which category of questions to display
     */
    public function params() {
        return $this->params;
    }

    /**
     * Called by question_bank_view to display the GUI for selecting a category
     */
    public function display_options() {
        $this->display_category_form($this->contexts, $this->baseurl, $this->cat);
        $this->print_category_info($this->category);
    }

    /**
     * Displays the recursion checkbox GUI.
     * question_bank_view places this within the section that is hidden by default
     */
    public function display_options_adv() {
        echo '<div>';
        echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'recurse',
                                               'value' => 0, 'id' => 'recurse_off'));
        echo html_writer::checkbox('recurse', '1', $this->recurse, get_string('includesubcategories', 'question'),
                                       array('id' => 'recurse_on', 'class' => 'searchoptions'));
        echo "</div>\n";

    }

    /**
     * Display the drop down to select the category
     */
    protected function display_category_form($contexts, $pageurl, $current) {
        global $OUTPUT;

        echo '<div class="choosecategory">';
        $catmenu = question_category_options($contexts, false, 0, true);
        $select = new single_select($this->baseurl, 'category', $catmenu, $current, null, 'catmenu');
        $select->set_label(get_string('selectacategory', 'question'));
        echo $OUTPUT->render($select);
        echo "</div>\n";

    }

    /**
     * Look up the category record based on cateogry ID and context
     * @param string $categoryandcontext categoryID,contextID as used with question_bank_view->display()
     * @return stdClass The category record
     */
    protected function get_current_category($categoryandcontext) {
        global $DB, $OUTPUT;
        list($categoryid, $contextid) = explode(',', $categoryandcontext);
        if (!$categoryid) {
            $this->print_choose_category_message($categoryandcontext);
            return false;
        }

        if (!$category = $DB->get_record('question_categories',
                array('id' => $categoryid, 'contextid' => $contextid))) {
            echo $OUTPUT->box_start('generalbox questionbank');
            echo $OUTPUT->notification('Category not found!');
            echo $OUTPUT->box_end();
            return false;
        }

        return $category;
    }

    /**
     * Print the category description
     */
    protected function print_category_info($category) {
        $formatoptions = new stdClass();
        $formatoptions->noclean = true;
        $formatoptions->overflowdiv = true;
        echo '<div class="boxaligncenter categoryinfo">';
        if (isset($this->maxinfolength)) {
            echo shorten_text(format_text($category->info, $category->infoformat, $formatoptions, $this->course->id),
                                     $this->maxinfolength);
        } else {
            echo format_text($category->info, $category->infoformat, $formatoptions, $this->course->id);
        }
        echo "</div>\n";
    }

}

