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

namespace qbank_history;

use core_question\local\bank\question_edit_contexts;
use core_question\local\bank\view;
use moodle_url;
use stdClass;

/**
 * Custom view class for the history page.
 *
 * @package    qbank_history
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_history_view extends view {

    /**
     * Entry id to get the versions
     *
     * @var int $entryid
     */
    protected $entryid;

    /**
     * Base url for the return.
     *
     * @var \moodle_url $basereturnurl
     */
    protected $basereturnurl;

    /**
     * Constructor for the history.
     * @param question_edit_contexts $contexts the contexts of api call
     * @param moodle_url $pageurl url of the page
     * @param stdClass $course course settings
     * @param int $entryid quiz settings
     * @param string $returnurl url to return to
     */
    public function __construct(question_edit_contexts $contexts, moodle_url $pageurl, stdClass $course, int $entryid,
                                string $returnurl) {
        parent::__construct($contexts, $pageurl, $course);
        $this->entryid = $entryid;
        $this->basereturnurl = new \moodle_url($returnurl);
    }

    protected function wanted_columns(): array {
        $this->requiredcolumns = [];
        $excludefeatures = [
            'question_usage_column',
            'history_action_column'
        ];
        $questionbankcolumns = $this->get_question_bank_plugins();
        foreach ($questionbankcolumns as $classobject) {
            if (empty($classobject) || in_array($classobject->get_column_name(), $excludefeatures)) {
                continue;
            }
            $this->requiredcolumns[$classobject->get_column_name()] = $classobject;
        }

        return $this->requiredcolumns;
    }

    public function wanted_filters($cat, $tagids, $showhidden, $recurse, $editcontexts, $showquestiontext): void {
        $categorydata = explode(',', $cat);
        $contextid = $categorydata[1];
        $catcontext = \context::instance_by_id($contextid);
        $thiscontext = $this->get_most_specific_context();
        $this->display_question_bank_header();

        // Display tag filter if usetags setting is enabled/enablefilters is true.
        if ($this->enablefilters) {
            if (is_array($this->customfilterobjects)) {
                foreach ($this->customfilterobjects as $filterobjects) {
                    $this->searchconditions[] = $filterobjects;
                }
            } else {
                if (get_config('core', 'usetags')) {
                    array_unshift($this->searchconditions,
                            new \core_question\bank\search\tag_condition([$catcontext, $thiscontext], $tagids));
                }

                array_unshift($this->searchconditions, new \core_question\bank\search\hidden_condition(!$showhidden));
            }
        }
        $this->display_options_form($showquestiontext);
    }

    protected function display_advanced_search_form($advancedsearch): void {
        foreach ($advancedsearch as $searchcondition) {
            echo $searchcondition->display_options_adv();
        }
    }

    protected function create_new_question_form($category, $canadd): void {
        // As we dont want to create questions in this page.
    }

    /**
     * Default sort for question data.
     * @return array
     */
    protected function default_sort(): array {
        $defaultsort = [];
        if (class_exists('\\qbank_viewcreator\\creator_name_column')) {
            $sort = 'qbank_viewcreator\creator_name_column-timecreated';
        }
        $defaultsort[$sort] = 1;

        return $defaultsort;
    }

    protected function build_query(): void {
        // Get the required tables and fields.
        $joins = [];
        $fields = ['qv.status', 'qv.version', 'qv.id as versionid', 'qbe.id as questionbankentryid'];
        if (!empty($this->requiredcolumns)) {
            foreach ($this->requiredcolumns as $column) {
                $extrajoins = $column->get_extra_joins();
                foreach ($extrajoins as $prefix => $join) {
                    if (isset($joins[$prefix]) && $joins[$prefix] != $join) {
                        throw new \coding_exception('Join ' . $join . ' conflicts with previous join ' . $joins[$prefix]);
                    }
                    $joins[$prefix] = $join;
                }
                $fields = array_merge($fields, $column->get_required_fields());
            }
        }
        $fields = array_unique($fields);

        // Build the order by clause.
        $sorts = [];
        foreach ($this->sort as $sort => $order) {
            list($colname, $subsort) = $this->parse_subsort($sort);
            $sorts[] = $this->requiredcolumns[$colname]->sort_expression($order < 0, $subsort);
        }

        // Build the where clause.
        $entryid = "qbe.id = $this->entryid";
        // Changes done here to get the questions only for the passed entryid.
        $tests = ['q.parent = 0', $entryid];
        $this->sqlparams = [];
        foreach ($this->searchconditions as $searchcondition) {
            if ($searchcondition->where()) {
                $tests[] = '((' . $searchcondition->where() .'))';
            }
            if ($searchcondition->params()) {
                $this->sqlparams = array_merge($this->sqlparams, $searchcondition->params());
            }
        }
        // Build the SQL.
        $sql = ' FROM {question} q ' . implode(' ', $joins);
        $sql .= ' WHERE ' . implode(' AND ', $tests);
        $this->countsql = 'SELECT count(1)' . $sql;
        $this->loadsql = 'SELECT ' . implode(', ', $fields) . $sql . ' ORDER BY ' . implode(', ', $sorts);
    }

    /**
     * Display the header for the question bank in the history page to include question name and type.
     */
    public function display_question_bank_header(): void {
        global $PAGE, $DB;
        $sql = 'SELECT q.*
                 FROM {question} q
                 JOIN {question_versions} qv ON qv.questionid = q.id
                 JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                WHERE qv.version  = (SELECT MAX(v.version)
                                       FROM {question_versions} v
                                       JOIN {question_bank_entries} be
                                         ON be.id = v.questionbankentryid
                                      WHERE be.id = qbe.id)
                  AND qbe.id = ?';
        $latestquestiondata = $DB->get_record_sql($sql, [$this->entryid]);
        $historydata = [
            'questionname' => $latestquestiondata->name,
            'returnurl' => $this->basereturnurl,
            'questionicon' => print_question_icon($latestquestiondata)
        ];
        // Header for the page before the actual form from the api.
        echo $PAGE->get_renderer('qbank_history')->render_history_header($historydata);
    }

    public function is_listing_specific_versions(): bool {
        return true;
    }

}
