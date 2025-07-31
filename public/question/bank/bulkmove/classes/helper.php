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

namespace qbank_bulkmove;

/**
 * Bulk move helper.
 *
 * @package    qbank_bulkmove
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Bulk move questions to a category.
     *
     * @param string $movequestionselected comma separated string of questions to be moved.
     * @param \stdClass $tocategory the category where the questions will be moved to.
     */
    public static function bulk_move_questions(string $movequestionselected, \stdClass $tocategory): void {
        global $DB, $CFG;
        require_once($CFG->libdir .'/questionlib.php');
        if ($questionids = explode(',', $movequestionselected)) {
            [$usql, $params] = $DB->get_in_or_equal($questionids);
            $sql = "SELECT q.*, c.contextid
                      FROM {question} q
                      JOIN {question_versions} qv ON qv.questionid = q.id
                      JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                      JOIN {question_categories} c ON c.id = qbe.questioncategoryid
                     WHERE q.id
                     {$usql}";
            $questions = $DB->get_records_sql($sql, $params);
            foreach ($questions as $question) {
                question_require_capability_on($question, 'move');
            }
            question_move_questions_to_category($questionids, $tocategory->id);
        }
    }

    /**
     * Get the display data for the move form.
     *
     * @param array $addcontexts the array of contexts to be considered in order to render the category select menu.
     * @param \moodle_url $moveurl the url where the move script will point to.
     * @param \moodle_url $returnurl return url in case the form is cancelled.
     * @return array the data to be rendered in the mustache where it contains the dropdown, move url and return url.
     * @deprecated since Moodle 5.0.
     * @todo MDL-82413 Final deprecation in Moodle 6.0.
     */
    #[\core\attribute\deprecated(
        replacement: 'replaced by a modal and webservice.
        See qbank_bulkmove/modal_question_bank_bulkmove and core_question_external\move_questions',
        since: '5.0',
        mdl: 'MDL-71378'
    )]
    public static function get_displaydata(array $addcontexts, \moodle_url $moveurl, \moodle_url $returnurl): array {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
        $displaydata = [];
        $displaydata ['categorydropdown'] = \qbank_managecategories\helper::question_category_select_menu($addcontexts,
            false, 0, '', -1, true);
        $displaydata ['moveurl'] = $moveurl;
        $displaydata['returnurl'] = $returnurl;
        return $displaydata;
    }

    /**
     * Process the question came from the form post.
     *
     * @param array $rawquestions raw questions came as a part of post.
     * @return array question ids got from the post are processed and structured in an array.
     */
    public static function process_question_ids(array $rawquestions): array {
        $questionids = [];
        $questionlist = '';
        foreach ($rawquestions as $key => $notused) {
            // Parse input for question ids.
            if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
                $key = $matches[1];
                $questionids[] = $key;
            }
        }
        if (!empty($questionids)) {
            $questionlist = implode(',', $questionids);
        }
        return [$questionids, $questionlist];
    }
}
