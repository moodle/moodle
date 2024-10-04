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
     * @param stdClass|null $cm (optional) activity settings.
     * @param array $params the parameters required to initialize the api.
     * @param array $extraparams any extra parameters need to initialized if the api is extended, it will be passed to js.
     * @throws \moodle_exception
     */
    public function __construct(
        question_edit_contexts $contexts,
        moodle_url $pageurl,
        stdClass $course,
        ?stdClass $cm = null,
        array $params = [],
        array $extraparams = [],
    ) {
        if ($cm === null) {
            debugging('$cm is now a required field', DEBUG_DEVELOPER);
        }

        $this->entryid = $extraparams['entryid'];
        $this->basereturnurl = new \moodle_url($extraparams['returnurl']);
        parent::__construct($contexts, $pageurl, $course, $cm, $params, $extraparams);
    }

    protected function init_question_actions(): void {
        parent::init_question_actions();
        unset($this->questionactions['qbank_history\history_action']);
    }

    protected function wanted_columns(): array {
        $this->requiredcolumns = [];
        $questionbankcolumns = $this->get_question_bank_plugins();
        foreach ($questionbankcolumns as $classobject) {
            if (empty($classobject)) {
                continue;
            }
            $this->requiredcolumns[$classobject->get_column_name()] = $classobject;
        }

        return $this->requiredcolumns;
    }

    /**
     * @deprecated since Moodle 4.3 MDL-72321
     */
    #[\core\attribute\deprecated('filtering objects', since: '4.3', mdl: 'MDL-72321', final: true)]
    protected function display_advanced_search_form($advancedsearch): void {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
    }

    public function allow_add_questions(): bool {
        // As we dont want to create questions in this page.
        return false;
    }

    #[\Override]
    protected function default_sort(): array {
        return ['qbank_history__version_number_column' => SORT_ASC];
    }

    protected function build_query(): void {
        // Get the required tables and fields.
        [$fields, $joins] = $this->get_component_requirements(array_merge($this->requiredcolumns, $this->questionactions));

        // Build the order by clause.
        $sorts = [];
        foreach ($this->sort as $sortname => $sortorder) {
            list($colname, $subsort) = $this->parse_subsort($sortname);
            $sorts[] = $this->requiredcolumns[$colname]->sort_expression($sortorder == SORT_DESC, $subsort);
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
        global $PAGE, $DB, $OUTPUT;
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
        if ($latestquestiondata) {
            $historydata = [
                'questionname' => $latestquestiondata->name,
                'returnurl' => $this->basereturnurl,
                'questionicon' => print_question_icon($latestquestiondata)
            ];
            // Header for the page before the actual form from the api.
            echo $PAGE->get_renderer('qbank_history')->render_history_header($historydata);
        } else {
            // Continue when all the question versions are deleted.
            echo $OUTPUT->notification(get_string('allquestionversionsdeleted', 'qbank_history'), 'notifysuccess');
            echo $OUTPUT->continue_button($this->basereturnurl);
        }
    }

    public function is_listing_specific_versions(): bool {
        return true;
    }

    /**
     * Override wanted_filters so that we apply the filters provided by the URL, but don't display the filter UI.
     *
     * @return void
     */
    public function wanted_filters(): void {
        $this->display_question_bank_header();
        // Add search conditions.
        $this->add_standard_search_conditions();
    }

}
