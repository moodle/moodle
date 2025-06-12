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

namespace core_question\output;

use core\context;
use core\output\renderable;
use core\output\renderer_base;
use core\output\templatable;
use core_question\local\bank\question_version_status;

/**
 * A select menu of question categories.
 *
 *
 * @package   core_question
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_category_selector implements renderable, templatable {

    /**
     * Constructor.
     *
     * @param array $contexts
     * @param bool $top
     * @param string $currentcat
     * @param string $selected
     * @param int $nochildrenof
     * @param bool $autocomplete
     */
    public function __construct(
        /** @var array The module contexts for the question banks to show category options for. */
        protected array $contexts = [],
        /** @var bool If true, include top categories for each context in the options. */
        protected bool $top = false,
        /** @var string The current category, to exclude from the list. */
        protected $currentcat = 0,
        /** @var string The value of the initially selected option. */
        protected string $selected = "",
        /** @var int If this matches the ID of a category in the list, don't include its children. */
        protected int $nochildrenof = -1,
        /** @var bool If true, return options as a flattened array suitable for a list of autocomplete suggestions. */
        protected bool $autocomplete = false,
    ) {
    }

    /**
     * Get all the category objects, including a count of the number of questions in that category,
     * for all the categories in the lists $contexts.
     *
     * @param string $contexts comma separated list of contextids
     * @param string $sortorder used as the ORDER BY clause in the select statement.
     * @param bool $top Whether to return the top categories or not.
     * @param int $showallversions 1 to show all versions not only the latest.
     * @return array of category objects.
     * @throws \dml_exception
     */
    public function get_categories_for_contexts(
        string $contexts,
        string $sortorder = 'parent, sortorder, name ASC',
        bool $top = false,
        int $showallversions = 0,
    ): array {
        global $DB;

        $contextids = explode(',', $contexts);
        foreach ($contextids as $contextid) {
            $context = context::instance_by_id($contextid);
            if ($context->contextlevel === CONTEXT_MODULE) {
                $validcontexts[] = $contextid;
            }
        }
        if (empty($validcontexts)) {
            return [];
        }

        [$insql, $inparams] = $DB->get_in_or_equal($validcontexts);

        $topwhere = $top ? '' : 'AND c.parent <> 0';
        $statuscondition = "AND (qv.status = '" . question_version_status::QUESTION_STATUS_READY . "' " .
            " OR qv.status = '" . question_version_status::QUESTION_STATUS_DRAFT . "' )";
        $substatuscondition = "AND v.status <> '"  . question_version_status::QUESTION_STATUS_HIDDEN . "' ";
        $sql = "SELECT c.*,
                    (SELECT COUNT(1)
                       FROM {question} q
                       JOIN {question_versions} qv ON qv.questionid = q.id
                       JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                      WHERE q.parent = '0'
                        $statuscondition
                            AND c.id = qbe.questioncategoryid
                            AND ({$showallversions} = 1
                                OR (qv.version = (SELECT MAX(v.version)
                                                    FROM {question_versions} v
                                                    JOIN {question_bank_entries} be ON be.id = v.questionbankentryid
                                                   WHERE be.id = qbe.id $substatuscondition)
                                   )
                                )
                            ) AS questioncount
                  FROM {question_categories} c
                 WHERE c.contextid {$insql} {$topwhere}
              ORDER BY {$sortorder}";

        return $DB->get_records_sql($sql, $inparams);
    }

    /**
     * Output an array of question categories.
     *
     * @param array $contexts The list of contexts.
     * @param bool $top Whether to return the top categories or not.
     * @param int $currentcat The current category, to exclude from the list.
     * @param bool $popupform Return each question bank's group in an additional nested array.
     * @param int $nochildrenof Don't include children of this category
     * @param bool $escapecontextnames Whether the returned name of the thing is to be HTML escaped or not.
     * @return array
     * @throws \coding_exception|\dml_exception
     */
    public function question_category_options(
        array $contexts,
        bool $top = false,
        int $currentcat = 0,
        bool $popupform = false,
        int $nochildrenof = -1,
        bool $escapecontextnames = true,
    ): array {
        global $CFG;
        $pcontexts = [];
        foreach ($contexts as $context) {
            if ($context->contextlevel !== CONTEXT_MODULE) {
                continue;
            }
            $pcontexts[] = $context->id;
        }
        $contextslist = join(', ', $pcontexts);

        $categories = $this->get_categories_for_contexts($contextslist, 'parent, sortorder, name ASC', $top);

        if ($top) {
            $categories = $this->question_fix_top_names($categories);
        }

        $categories = $this->question_add_context_in_key($categories);
        $categories = $this->add_indented_names($categories, $nochildrenof);

        // Sort cats out into different contexts.
        $categoriesarray = [];
        foreach ($pcontexts as $contextid) {
            $context = context::instance_by_id($contextid);
            $contextstring = $context->get_context_name(true, true, $escapecontextnames);
            foreach ($categories as $category) {
                if ($category->contextid == $contextid) {
                    $cid = $category->id;
                    if ("{$currentcat},{$contextid}" != $cid || $currentcat == 0) {
                        $a = new \stdClass();
                        $a->name = format_string(
                            $category->indentedname,
                            true,
                            ['context' => $context]
                        );
                        if ($category->idnumber !== null && $category->idnumber !== '') {
                            $a->idnumber = s($category->idnumber);
                        }
                        if (!empty($category->questioncount)) {
                            $a->questioncount = $category->questioncount;
                        }
                        if (isset($a->idnumber) && isset($a->questioncount)) {
                            $formattedname = get_string('categorynamewithidnumberandcount', 'question', $a);
                        } else if (isset($a->idnumber)) {
                            $formattedname = get_string('categorynamewithidnumber', 'question', $a);
                        } else if (isset($a->questioncount)) {
                            $formattedname = get_string('categorynamewithcount', 'question', $a);
                        } else {
                            $formattedname = $a->name;
                        }
                        $categoriesarray[$contextstring][$cid] = $formattedname;
                    }
                }
            }
        }
        if ($popupform) {
            $popupcats = [];
            foreach ($categoriesarray as $contextstring => $optgroup) {
                $group = [];
                foreach ($optgroup as $key => $value) {
                    $key = str_replace($CFG->wwwroot, '', $key);
                    $group[$key] = $value;
                }
                $popupcats[] = [$contextstring => $group];
            }
            return $popupcats;
        } else {
            return $categoriesarray;
        }
    }

    /**
     * Add context in categories key.
     *
     * @param array $categories The list of categories, keyed by ID.
     * @return array The list with the context id added to each key, id, and parent attribute.
     */
    public function question_add_context_in_key(array $categories): array {
        $newcatarray = [];
        foreach ($categories as $id => $category) {
            $category->parent = "$category->parent,$category->contextid";
            $category->id = "$category->id,$category->contextid";
            $newcatarray["$id,$category->contextid"] = $category;
        }
        return $newcatarray;
    }

    /**
     * Finds top categories in the given categories hierarchy and replace their name with a proper localised string.
     *
     * @param array $categories An array of question categories.
     * @param bool $escape Whether the returned name of the thing is to be HTML escaped or not.
     * @return array The same question category list given to the function, with the top category names being translated.
     * @throws \coding_exception
     */
    public function question_fix_top_names(array $categories, bool $escape = true): array {

        foreach ($categories as $id => $category) {
            if ($category->parent == 0) {
                $context = context::instance_by_id($category->contextid);
                $categories[$id]->name = get_string('topfor', 'question', $context->get_context_name(false, false, $escape));
            }
        }

        return $categories;
    }

    /**
     * Format categories into an indented list reflecting the tree structure.
     *
     * @param array $categories An array of category objects, keyed by ID.
     * @param int $nochildrenof If the category with this ID is in the list, don't include its children.
     * @return array The formatted list of categories.
     */
    public function add_indented_names(array $categories, int $nochildrenof = -1): array {

        // Add an array to each category to hold the child category ids. This array
        // will be removed again by flatten_category_tree(). It should not be used
        // outside these two functions.
        foreach (array_keys($categories) as $id) {
            $categories[$id]->childids = [];
        }

        // Build the tree structure, and record which categories are top-level.
        // We have to be careful, because the categories array may include published
        // categories from other courses, but not their parents.
        $toplevelcategoryids = [];
        foreach (array_keys($categories) as $id) {
            if (
                !empty($categories[$id]->parent) &&
                array_key_exists($categories[$id]->parent, $categories)
            ) {
                $categories[$categories[$id]->parent]->childids[] = $id;
            } else {
                $toplevelcategoryids[] = $id;
            }
        }

        // Flatten the tree to and add the indents.
        $newcategories = [];
        foreach ($toplevelcategoryids as $id) {
            $newcategories = $newcategories + $this->flatten_category_tree(
                    $categories,
                    $id,
                    0,
                    $nochildrenof,
                );
        }

        return $newcategories;
    }

    /**
     * Only for the use of add_indented_names().
     *
     * Recursively adds an indentedname field to each category, starting with the category
     * with id $id, and dealing with that category and all its children, and
     * return a new array, with those categories in the right order.
     *
     * @param array $categories an array of categories which has had childids
     *          fields added by flatten_category_tree(). Passed by reference for
     *          performance only. It is not modfied.
     * @param int $id the category to start the indenting process from.
     * @param int $depth the indent depth. Used in recursive calls.
     * @param int $nochildrenof If the category with this ID is in the list, don't recur to its children.
     * @return array a new array of categories, in the right order for the tree.
     */
    public function flatten_category_tree(array &$categories, $id, int $depth = 0, int $nochildrenof = -1): array {

        // Indent the name of this category.
        $newcategories = [];
        $newcategories[$id] = $categories[$id];
        $newcategories[$id]->indentedname = str_repeat('&nbsp;&nbsp;&nbsp;', $depth) .
            $categories[$id]->name;

        // Recursively indent the children.
        foreach ($categories[$id]->childids as $childid) {
            [$childcategory, ] = explode(',', $childid);
            if ($childcategory != $nochildrenof) {
                $newcategories = $newcategories + self::flatten_category_tree(
                        $categories,
                        $childid,
                        $depth + 1,
                        $nochildrenof,
                    );
            }
        }

        // Remove the childids array that were temporarily added.
        unset($newcategories[$id]->childids);

        return $newcategories;
    }

    /**
     * Context for the question_category_selector.mustache template.
     *
     * @param renderer_base $output
     * @return array[] [
     *  'banks' => a 2-D array of question banks and categories, for a plain select list with optgroups.
     *  'categories' => A flat list of categories, with bank names and disabled entries, for enhancing with an Autocomplete.
     * ]
     */
    public function export_for_template(renderer_base $output): array {
        $categoriesarray = $this->question_category_options(
            $this->contexts,
            $this->top,
            $this->currentcat,
            false,
            $this->nochildrenof,
            false,
        );

        $bankoptgroups = [];
        $categoryoptions = [];
        if ($this->autocomplete) {
            foreach ($categoriesarray as $bankname => $categories) {
                $categoryoptions[] = [
                    'label' => $bankname,
                    'value' => 0,
                    'disabled' => true,
                ];
                foreach ($categories as $idcontext => $category) {
                    $categoryoptions[] = [
                        'label' => $category,
                        'value' => $idcontext,
                        'selected' => $this->selected == $idcontext,
                    ];
                }
            }
            if (empty($selected) && isset($categoryoptions[1])) {
                // Default to selecting the first category option.
                $categoryoptions[1]['selected'] = 1;
            }
        } else {
            foreach ($categoriesarray as $bankname => $categories) {
                $bankoptgroups[] = [
                    'bankname' => $bankname,
                    'categories' => array_map(
                        fn($idcontext, $category) => [
                            'idcontext' => $idcontext,
                            'category' => $category,
                            'selected' => $this->selected == $idcontext,
                        ],
                        array_keys($categories),
                        $categories,
                    ),
                ];
            }
            if (empty($selected) && isset($bankoptgroups[0]['categories'][0])) {
                // Default to selecting the first category option.
                $bankoptgroups[0]['categories'][0]['selected'] = 1;
            }
        }

        return [
            'banks' => $bankoptgroups,
            'categories' => $categoryoptions,
        ];
    }
}
