<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * A search class to control from which category questions are listed.
 *
 * @package   core_question
 * @copyright 2013 Ray Morris
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\bank\search;
defined('MOODLE_INTERNAL') || die();

/**
 *  This class controls from which category questions are listed.
 *
 * @copyright 2013 Ray Morris
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category_condition extends condition {
    /** @var \stdClass The course record. */
    protected $course;

    /** @var \stdClass The category record. */
    protected $category;

    /** @var array of contexts. */
    protected $contexts;

    /** @var bool Whether to include questions from sub-categories. */
    protected $recurse;

    /** @var string SQL fragment to add to the where clause. */
    protected $where;

    /** @var array query param used in where. */
    protected $params;

    /** @var string categoryID,contextID as used with question_bank_view->display(). */
    protected $cat;

    /** @var int The maximum displayed length of the category info. */
    protected $maxinfolength;

    /**
     * Constructor
     * @param string     $cat           categoryID,contextID as used with question_bank_view->display()
     * @param bool       $recurse       Whether to include questions from sub-categories
     * @param array      $contexts      Context objects as used by question_category_options()
     * @param \moodle_url $baseurl       The URL the form is submitted to
     * @param \stdClass   $course        Course record
     * @param integer    $maxinfolength The maximum displayed length of the category info.
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

    public function where() {
        return  $this->where;
    }

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
        echo \html_writer::start_div();
        echo \html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'recurse',
                                               'value' => 0, 'id' => 'recurse_off'));
        echo \html_writer::checkbox('recurse', '1', $this->recurse, get_string('includesubcategories', 'question'),
                                       array('id' => 'recurse_on', 'class' => 'searchoptions'));
        echo \html_writer::end_div() . "\n";
    }

    /**
     * Display the drop down to select the category.
     *
     * @param array $contexts of contexts that can be accessed from here.
     * @param \moodle_url $pageurl the URL of this page.
     * @param string $current 'categoryID,contextID'.
     */
    protected function display_category_form($contexts, $pageurl, $current) {
        global $OUTPUT;

        echo \html_writer::start_div('choosecategory');
        $catmenu = question_category_options($contexts, false, 0, true);
        echo \html_writer::label(get_string('selectacategory', 'question'), 'id_selectacategory');
        echo \html_writer::select($catmenu, 'category', $current, array(), array('class' => 'searchoptions custom-select', 'id' => 'id_selectacategory'));
        echo \html_writer::end_div() . "\n";
    }

    /**
     * Look up the category record based on cateogry ID and context
     * @param string $categoryandcontext categoryID,contextID as used with question_bank_view->display()
     * @return \stdClass The category record
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
     * @param stdClass $category the category information form the database.
     */
    protected function print_category_info($category) {
        $formatoptions = new \stdClass();
        $formatoptions->noclean = true;
        $formatoptions->overflowdiv = true;
        echo \html_writer::start_div('boxaligncenter categoryinfo');
        if (isset($this->maxinfolength)) {
            echo shorten_text(format_text($category->info, $category->infoformat, $formatoptions, $this->course->id),
                                     $this->maxinfolength);
        } else {
            echo format_text($category->info, $category->infoformat, $formatoptions, $this->course->id);
        }
        echo \html_writer::end_div() . "\n";
    }
}
