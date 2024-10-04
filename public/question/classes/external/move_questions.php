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

namespace core_question\external;

use core\context;
use core\exception\moodle_exception;
use core\notification;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\restricted_context_exception;
use core_question\local\bank\filter_condition_manager;
use moodle_url;

/**
 * API for moving questions from one question bank category to another.
 *
 * @package    core_question
 * @copyright  2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author     Simon Adams <simon.adams@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class move_questions extends external_api {

    /**
     *  Declare the method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'newcontextid' => new external_value(PARAM_INT, 'Contextid of the target question bank'),
                'newcategoryid' => new external_value(PARAM_INT, 'ID of the target question category'),
                'questionids' => new external_value(PARAM_SEQUENCE, 'Comma separated list of question ids to move'),
                'returnurl' => new external_value(PARAM_LOCALURL,
                    desc: 'A URL to add/update the filter param with the new category',
                    default: ''
                ),
            ]
        );
    }

    /**
     *  Define the webservice response.
     *
     * @return external_value
     */
    public static function execute_returns(): external_value {
        return new external_value(PARAM_URL, 'Modified URL with filter param containing the new question category', VALUE_OPTIONAL);
    }

    /**
     * Move questions to a new question category.
     * Optionally provide a url to add/update it with the filter param containing the new category.
     *
     * @param int $newcontextid of the target question bank
     * @param int $newcategoryid of the target category
     * @param string $questionids comma separated list of question ids to move
     * @param string $returnurlstring optional, provide this to have the filter url param added/updated to reflect the new category
     * @return null|string if $returnurlstring was provided then an updated url which filters to the new category
     */
    public static function execute(
        int $newcontextid,
        int $newcategoryid,
        string $questionids,
        string $returnurlstring = ''
    ): ?string {
        global $DB;

        [
            'newcontextid' => $newcontextid,
            'newcategoryid' => $newcategoryid,
            'questionids' => $questionids,
            'returnurl' => $returnurlstring,
        ] = self::validate_parameters(self::execute_parameters(), [
            'newcontextid' => $newcontextid,
            'newcategoryid' => $newcategoryid,
            'questionids' => $questionids,
            'returnurl' => $returnurlstring,
        ]);

        $newcontext = context::instance_by_id($newcontextid);
        self::validate_context($newcontext);

        \core_question\local\bank\helper::require_plugin_enabled('qbank_bulkmove');

        $contexts = new \core_question\local\bank\question_edit_contexts($newcontext);
        $contexts->require_cap('moodle/question:add');

        if (!$targetcategory = $DB->get_record('question_categories', ['id' => $newcategoryid, 'contextid' => $newcontextid])) {
            throw new \moodle_exception('cannotfindcate', 'question');
        }

        \qbank_bulkmove\helper::bulk_move_questions($questionids, $targetcategory);
        notification::success(get_string('questionsmoved', 'qbank_bulkmove'));

        if ($returnurlstring) {
            $returnurl = new moodle_url($returnurlstring);
            $returnurl->param('cmid', $newcontext->instanceid);
            $returnurl->param('cat', "{$newcategoryid},{$newcontextid}");
            $returnurl->remove_params('category');
            // We can only highlight 1 question, so only highlight if we're moving a single question.
            $qids = explode(',', $questionids);
            if (count($qids) === 1) {
                $returnurl->param('lastchanged', reset($qids));
            } else {
                $returnurl->remove_params('lastchanged');
            };
            $filter = $returnurl->param('filter');
            if ($filter) {
                $returnfilters = filter_condition_manager::update_filter_param_to_category(
                    $filter,
                    $newcategoryid,
                );
            } else {
                $returnfilters = json_encode(
                    filter_condition_manager::get_default_filter("{$newcategoryid},{$newcontextid}"),
                    JSON_THROW_ON_ERROR
                );
            }

            $returnurl->param('filter', $returnfilters);
            return $returnurl->out(false);
        }
    }
}
