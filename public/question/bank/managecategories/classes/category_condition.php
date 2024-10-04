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

    /** @var \stdClass The course_modules record. */
    protected \stdClass $cm;

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
    public function __construct(?view $qbank = null) {
        if (is_null($qbank)) {
            return;
        }
        $this->cat = $qbank->get_pagevars('cat');
        $this->contexts = array_filter($qbank->contexts->having_one_edit_tab_cap($qbank->get_pagevars('tabname')),
            static fn($context) => $context->contextlevel === CONTEXT_MODULE
        );
        $this->course = $qbank->course;
        $this->cm = $qbank->cm;

        [$categoryid, $contextid] = self::validate_category_param($this->cat);
        if (is_null($categoryid)) {
            return;
        }

        $this->category = self::get_category_record($categoryid, $contextid);

        parent::__construct($qbank);
        if (isset($this->filter['filteroptions']['includesubcategories'])) {
            set_user_preference('qbank_managecategories_includesubcategories_filter_default',
                $this->filter['filteroptions']['includesubcategories']);
        }
        $this->includesubcategories = $this->filter['filteroptions']['includesubcategories'] ??
            get_user_preferences('qbank_managecategories_includesubcategories_filter_default', false);
    }

    /**
     * Return default category
     *
     * @return \stdClass default category
     */
    public function get_default_category(): \stdClass {
        if (empty($this->category)) {
            return question_get_default_category(\context_module::instance($this->cm->id)->id, true);
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
     * @deprecated since Moodle 4.3 MDL-72321 - please do not use this function any more.
     */
    #[\core\attribute\deprecated('filtering objects', since: '4.3', mdl: 'MDL-72321', final: true)]
    public function display_options() {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
    }

    /**
     * @deprecated since Moodle 4.3 MDL-72321 - please do not use this function any more.
     */
    #[\core\attribute\deprecated('foobar::blah()', since: '4.3', mdl: 'MDL-72321', final: true)]
    public function display_options_adv() {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
    }

    /**
     * @deprecated since Moodle 4.3
     */
    #[\core\attribute\deprecated('core_question renderer', since: '4.3', mdl: 'MDL-72321', final: true)]
    protected function display_category_form($contexts, $pageurl, $current) {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
    }

    /**
     * @deprecated since Moodle 4.3
     */
    #[\core\attribute\deprecated('qbank_managecategories/choose_category template', since: '4.3', mdl: 'MDL-72321', final: true)]
    public static function print_choose_category_message(): void {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
    }

    /**
     * @deprecated since Moodle 4.3
     */
    #[\core\attribute\deprecated(null, since: '4.3', mdl: 'MDL-72321', final: true)]
    public static function get_current_category($categoryandcontext) {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
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
     * @deprecated since Moodle 4.3 MDL-72321 - please do not use this function any more.
     */
    #[\core\attribute\deprecated(null, since: '4.3', mdl: 'MDL-72321', final: true)]
    protected function print_category_info($category): string {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
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

    #[\Override]
    public function filter_invalid_values(array $filterconditions): array {

        global $DB;

        $defaultcatid = explode(',', $filterconditions['cat'])[0];

        [$insql, $inparams] = $DB->get_in_or_equal($filterconditions['filter']['category']['values']);
        $categories = $DB->get_records_select('question_categories', "id {$insql}",
            $inparams, null, 'id');
        $categoryids = array_keys($categories);

        foreach ($filterconditions['filter']['category']['values'] as $key => $catid) {

            // Check that the category still exists, and if not, remove it from the conditions.
            if (!in_array($catid, $categoryids)) {
                unset($filterconditions['filter']['category']['values'][$key]);
            }

        }

        // If we now don't have any valid categories, use the default loaded from the page.
        if (count($filterconditions['filter']['category']['values']) === 0) {
            $filterconditions['filter']['category']['values'] = [$defaultcatid];
        }

        return $filterconditions;

    }

}
