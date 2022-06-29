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

namespace mod_quiz\question\bank\filter;

use core_question\local\bank\question_version_status;

/**
 * A custom filter condition helper for quiz to select question categories.
 *
 * This is required as quiz will only use ready questions and the count should show according to that.
 *
 * @package    mod_quiz
 * @category   question
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_category_condition_helper extends \qbank_managecategories\helper {

    public static function question_category_options(array $contexts, bool $top = false, int $currentcat = 0,
        bool $popupform = false, int $nochildrenof = -1,
        bool $escapecontextnames = true): array {
        global $CFG;
        $pcontexts = [];
        foreach ($contexts as $context) {
            $pcontexts[] = $context->id;
        }
        $contextslist = join(', ', $pcontexts);

        $categories = self::get_categories_for_contexts($contextslist, 'parent, sortorder, name ASC', $top);

        if ($top) {
            $categories = self::question_fix_top_names($categories);
        }

        $categories = self::question_add_context_in_key($categories);
        $categories = self::add_indented_names($categories, $nochildrenof);

        // Sort cats out into different contexts.
        $categoriesarray = [];
        foreach ($pcontexts as $contextid) {
            $context = \context::instance_by_id($contextid);
            $contextstring = $context->get_context_name(true, true, $escapecontextnames);
            foreach ($categories as $category) {
                if ($category->contextid == $contextid) {
                    $cid = $category->id;
                    if ($currentcat != $cid || $currentcat == 0) {
                        $a = new \stdClass;
                        $a->name = format_string($category->indentedname, true,
                            ['context' => $context]);
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

    public static function get_categories_for_contexts($contexts, string $sortorder = 'parent, sortorder, name ASC',
        bool $top = false, int $showallversions = 0): array {
        global $DB;
        $topwhere = $top ? '' : 'AND c.parent <> 0';
        $statuscondition = "AND qv.status = '". question_version_status::QUESTION_STATUS_READY . "' ";

        $sql = "SELECT c.*,
                    (SELECT COUNT(1)
                       FROM {question} q
                       JOIN {question_versions} qv ON qv.questionid = q.id
                       JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                      WHERE q.parent = '0'
                        $statuscondition
                            AND c.id = qbe.questioncategoryid
                            AND ($showallversions = 1
                                OR (qv.version = (SELECT MAX(v.version)
                                                    FROM {question_versions} v
                                                    JOIN {question_bank_entries} be ON be.id = v.questionbankentryid
                                                   WHERE be.id = qbe.id)
                                   )
                                )
                            ) AS questioncount
                  FROM {question_categories} c
                 WHERE c.contextid IN ($contexts) $topwhere
              ORDER BY $sortorder";

        return $DB->get_records_sql($sql);
    }
}
