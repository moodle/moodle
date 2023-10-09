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

namespace qbank_managecategories;

use core\output\datafilter;
use core_question\local\bank\condition;
use core_question\local\bank\view;

/**
 * This class controls from which category questions are listed.
 *
 * @package   qbank_managecategories
 * @copyright 2013 Ray Morris
 * @author    2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category_condition extends condition {
    /** @var \stdClass The course record. */
    protected $course;

    /** @var \stdClass The category record. */
    protected $category;

    /** @var array of contexts. */
    protected $contexts;

    /** @var string categoryID,contextID as used with question_bank_view->display(). */
    protected $cat;

    /** @var int The maximum displayed length of the category info. */
    public $maxinfolength;

    /** @var bool Include questions in subcategories of the specified category? */
    public $includesubcategories;

    /**
     * Constructor to initialize the category filter condition.
     *
     * @param view $qbank qbank view
     */
    public function __construct(view $qbank = null) {
        if (is_null($qbank)) {
            return;
        }
        $this->cat = $qbank->get_pagevars('cat');
        $this->contexts = $qbank->contexts->having_one_edit_tab_cap($qbank->get_pagevars('tabname'));
        $this->course = $qbank->course;

        [$categoryid, $contextid] = self::validate_category_param($this->cat);
        if (is_null($categoryid)) {
            return;
        }

        $this->category = self::get_category_record($categoryid, $contextid);

        parent::__construct($qbank);
        $this->includesubcategories = $this->filter['filteroptions']['includesubcategories'] ?? false;
    }

    /**
     * Return default category
     *
     * @return \stdClass default category
     */
    public function get_default_category(): \stdClass {
        if (empty($this->category)) {
            return question_get_default_category(\context_course::instance($this->course->id)->id);
        }

        return $this->category;
    }

    public static function get_condition_key() {
        return 'category';
    }

    /**
     * Returns course id.
     *
     * @return string Course id.
     */
    public function get_course_id() {
        return $this->course->id;
    }

    /**
     * Called by question_bank_view to display the GUI for selecting a category
     * @deprecated since Moodle 4.3 MDL-72321 - please do not use this function any more.
     * @todo Final deprecation on Moodle 4.7 MDL-78090
     */
    public function display_options() {
        debugging('Function display_options() is deprecated, please use filtering objects', DEBUG_DEVELOPER);
        global $PAGE;
        $displaydata = [];
        $catmenu = helper::question_category_options($this->contexts, true, 0,
                true, -1, false);
        $displaydata['categoryselect'] = \html_writer::select($catmenu, 'category', $this->cat, [],
                array('class' => 'searchoptions custom-select', 'id' => 'id_selectacategory'));
        $displaydata['categorydesc'] = '';
        if ($this->category) {
            $displaydata['categorydesc'] = $this->print_category_info($this->category);
        }
        return $PAGE->get_renderer('qbank_managecategories')->render_category_condition($displaydata);
    }

    /**
     * Displays the recursion checkbox GUI.
     * question_bank_view places this within the section that is hidden by default
     * @deprecated since Moodle 4.3 MDL-72321 - please do not use this function any more.
     * @todo Final deprecation on Moodle 4.7 MDL-78090
     */
    public function display_options_adv() {
        debugging('Function display_options_adv() is deprecated, please use filtering objects', DEBUG_DEVELOPER);
        global $PAGE;
        $displaydata = [];
        if ($this->recurse) {
            $displaydata['checked'] = 'checked';
        }
        return $PAGE->get_renderer('qbank_managecategories')->render_category_condition_advanced($displaydata);
    }

    /**
     * Display the drop down to select the category.
     *
     * @param array $contexts of contexts that can be accessed from here.
     * @param \moodle_url $pageurl the URL of this page.
     * @param string $current 'categoryID,contextID'.
     * @deprecated since Moodle 4.3
     * @todo Final deprecation on Moodle 4.7 MDL-78090
     */
    protected function display_category_form($contexts, $pageurl, $current) {
        debugging(
            'Function display_category_form() is deprecated, please use the core_question renderer instead.',
            DEBUG_DEVELOPER
        );
        echo \html_writer::start_div('choosecategory');
        $catmenu = question_category_options($contexts, true, 0, true, -1, false);
        echo \html_writer::label(get_string('selectacategory', 'question'), 'id_selectacategory', true, ["class" => "mr-1"]);
        echo \html_writer::select($catmenu, 'category', $current, [],
                array('class' => 'searchoptions custom-select', 'id' => 'id_selectacategory'));
        echo \html_writer::end_div() . "\n";
    }

    /**
     * Print the text if category id not available.
     * @deprecated since Moodle 4.3
     * @todo Final deprecation in Moodle 4.7 MDL-78090
     */
    public static function print_choose_category_message(): void {
        debugging(
            'Function print_choose_category_message() is deprecated, please use ' .
                'qbank_managecategories/choose_category template instead.',
            DEBUG_DEVELOPER
        );
        global $OUTPUT;
        echo $OUTPUT->render_from_template('qbank_managecategories/choose_category', []);
    }

    /**
     * Look up the category record based on category ID and context
     * @param string $categoryandcontext categoryID,contextID as used with question_bank_view->display()
     * @return \stdClass The category record
     * @deprecated since Moodle 4.3
     * @todo Final deprecation in Moodle 4.7 MDL-78090
     */
    public static function get_current_category($categoryandcontext) {
        debugging('Function get_current_category() is deprecated. Please do not use it anymore.', DEBUG_DEVELOPER);
        global $DB, $OUTPUT;
        [$categoryid, $contextid] = explode(',', $categoryandcontext);
        if (!$categoryid) {
            self::print_choose_category_message();
            return false;
        }

        if (!$category = $DB->get_record('question_categories', ['id' => $categoryid, 'contextid' => $contextid])) {
            echo $OUTPUT->box_start('generalbox questionbank');
            echo $OUTPUT->notification('Category not found!');
            echo $OUTPUT->box_end();
            return false;
        }

        return $category;
    }

    /**
     * Return category and context ID from compound parameter.
     *
     * @param string $categoryandcontext Comma-separated list of category and context IDs.
     * @return int[]|null[]
     */
    public static function validate_category_param(string $categoryandcontext): array {
        [$categoryid, $contextid] = explode(',', $categoryandcontext);
        if (!$categoryid) {
            return [null, null];
        }
        return [clean_param($categoryid, PARAM_INT), clean_param($contextid, PARAM_INT)];
    }

    /**
     * Fetch the question category record matching the provided category and context IDs.
     *
     * @param int $categoryid
     * @param int $contextid
     * @return \stdClass
     * @throws \dml_exception
     */
    public static function get_category_record($categoryid, $contextid): \stdClass {
        global $DB;
        return $DB->get_record('question_categories',
                ['id' => $categoryid, 'contextid' => $contextid],
                '*',
                MUST_EXIST);
    }

    /**
     * Print the category description
     * @param \stdClass $category the category information form the database.
     * @deprecated since Moodle 4.3 MDL-72321 - please do not use this function any more.
     * @todo Final deprecation on Moodle 4.7 MDL-78090
     */
    protected function print_category_info($category): string {
        debugging('Function print_category_info() is deprecated. Please do not use it anymore', DEBUG_DEVELOPER);
        $formatoptions = new \stdClass();
        $formatoptions->noclean = true;
        $formatoptions->overflowdiv = true;
        if (isset($this->maxinfolength)) {
            return shorten_text(format_text($category->info, $category->infoformat, $formatoptions, $this->course->id),
                    $this->maxinfolength);
        }

        return format_text($category->info, $category->infoformat, $formatoptions, $this->course->id);
    }

    public static function build_query_from_filter(array $filter): array {
        global $DB;
        $recursive = false;
        if (isset($filter['filteroptions']['includesubcategories'])) {
            $recursive = (bool)$filter['filteroptions']['includesubcategories'];
        }

        // Sub categories.
        if ($recursive) {
            $categories = $filter['values'];
            $categoriesandsubcategories = [];
            foreach ($categories as $categoryid) {
                $categoriesandsubcategories += question_categorylist($categoryid);
            }
        } else {
            $categoriesandsubcategories = $filter['values'];
        }

        $jointype = $filter['jointype'] ?? self::JOINTYPE_DEFAULT;
        $equal = !($jointype === datafilter::JOINTYPE_NONE);
        [$insql, $params] = $DB->get_in_or_equal($categoriesandsubcategories, SQL_PARAMS_NAMED, 'cat', $equal);
        $where = 'qbe.questioncategoryid ' . $insql;
        return [$where, $params];
    }

    public function get_title() {
        return get_string('category', 'core_question');
    }

    public function get_filter_class() {
        return 'qbank_managecategories/datafilter/filtertypes/categories';
    }

    public function allow_custom() {
        return false;
    }

    public function allow_multiple() {
        return false;
    }

    public function allow_empty() {
        return false;
    }

    public function get_join_list(): array {
        return [
                datafilter::JOINTYPE_ANY,
        ];
    }

    public function get_initial_values() {
        $catmenu = helper::question_category_options($this->contexts, true, 0, true, -1, false);
        $values = [];
        foreach ($catmenu as $menu) {
            foreach ($menu as $heading => $catlist) {
                $values[] = (object) [
                    // Add a list item for each question category context. This will serve as a "heading" within the list
                    // and we will use CSS to disable pointer events so it cannot be selected.
                    'value' => '',
                    'title' => $heading,
                    'disabled' => true,
                    'classes' => 'suggestions-heading',
                ];
                foreach ($catlist as $key => $value) {
                    $values[] = (object) [
                        // Remove contextid from value.
                        'value' => str_contains($key, ',') ? substr($key, 0, strpos($key, ',')) : $key,
                        'title' => $value,
                        'selected' => ($key === $this->cat),
                    ];
                }
            }
        }
        return $values;
    }

    public function get_filteroptions(): \stdClass {
        return (object)[
            'includesubcategories' => $this->includesubcategories,
        ];
    }

    public function is_required(): bool {
        return true;
    }
}
