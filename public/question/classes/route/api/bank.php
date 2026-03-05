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

namespace core_question\route\api;

use core\context\course;
use core\context\module;
use core\exception\required_capability_exception;
use core\param;
use core\router\parameters\query_course;
use core\router\parameters\query_coursemodule;
use core\router\require_login;
use core\router\route;
use core\router\schema\example;
use core\router\schema\objects\array_of_strings;
use core\router\schema\objects\array_of_things;
use core\router\schema\objects\schema_object;
use core\router\schema\parameters\path_parameter;
use core\router\schema\parameters\query_parameter;
use core\router\schema\response\content\json_media_type;
use core\router\schema\response\payload_response;
use core\router\schema\response\response;
use core_question\local\bank\formatted_bank;
use core_question\local\bank\question_bank_helper;
use core_question\local\bank\question_edit_contexts;
use core_question\local\bank\question_version_status;
use core_question\output\question_category_selector;
use core_question\question_category;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Web service functions related to question banks
 *
 * @package   core_question
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bank {
    /**
     * Return the total number of questions in each question bank for the given course.
     *
     * This will count all top-level questions (no subquestions) that are not hidden.
     *
     * @param int $courseid
     */
    #[route(
        path: '/bank/{courseid}/question_counts',
        method: ['GET'],
        pathtypes: [
            new path_parameter(
                name: 'courseid',
                type: param::INT,
                description: 'The course ID the fetch question counts for',
                required: true,
            ),
        ],
        requirelogin: new require_login(false, true, 'courseid'),
    )]
    public function question_counts(
        ServerRequestInterface $request,
        ResponseInterface $response,
        int $courseid,
    ): payload_response {
        global $DB;
        $coursecontext = course::instance($courseid);
        $capabilities = array_merge(question_edit_contexts::$caps['editq'], question_edit_contexts::$caps['categories']);

        if (!has_any_capability($capabilities, $coursecontext)) {
            throw new required_capability_exception(
                $coursecontext,
                reset($capabilities),
                'missingcapability',
                'question',
            );
        }

        $contextpathlike = $DB->sql_like('c.path', ':contextpath');

        // Get a count of all questions in each module context within this course, keyed by cmid.
        // Only include modules that have question category records, so we don't get a count for modules that don't use questions.
        // Return a count of 0 for those modules with no questions.
        // The double LEFT JOIN of question_versions ensures we only get the latest version for a question bank entry.
        $sql = "
            SELECT c.instanceid,
                   COUNT(
                       CASE
                           WHEN q.id IS NOT NULL THEN 1
                       END
                   ) AS count
              FROM {context} c
              JOIN {question_categories} qc ON qc.contextid = c.id
         LEFT JOIN {question_bank_entries} qbe ON qbe.questioncategoryid = qc.id
         LEFT JOIN {question_versions} qv ON qv.questionbankentryid = qbe.id
         LEFT JOIN {question_versions} qv1 ON qv1.questionbankentryid = qbe.id AND qv.version < qv1.version
         LEFT JOIN {question} q ON q.id = qv.questionid
             WHERE c.contextlevel = :module
                   AND {$contextpathlike}
                   AND (q.parent = '0' OR q.id IS NULL)
                   AND (qv1.questionbankentryid IS NULL OR q.id IS NULL)
          GROUP BY c.instanceid
        ";
        $params = [
            'hidden' => question_version_status::QUESTION_STATUS_HIDDEN,
            'module' => module::LEVEL,
            'contextpath' => "{$coursecontext->path}/%",
        ];
        $counts = $DB->get_records_sql_menu($sql, $params);
        return new payload_response(
            payload: [
                'counts' => $counts,
            ],
            request: $request,
            response: $response,
        );
    }

    /**
     * Return a list of formatted question banks matching the parameters.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param course $coursecontext The course context.
     * @param module $currentmodulecontext The module context.
     * @param question_bank_helper $helper Injected dependency.
     * @return payload_response A list of question banks with formatted names, and whether they are shared and recently used.
     */
    #[route(
        path: '/banks', // Resolves to /api/rest/v2/question/banks.
        queryparams: [
            new query_course(required: true),
            new query_coursemodule('currentmodule'),
            new query_parameter(name: 'includeshared', type: param::BOOL, default: true),
            new query_parameter(name: 'includerecent', type: param::BOOL, default: false),
        ],
        responses: [
            new response(
                statuscode: 200,
                description: 'OK',
                content: [
                    new json_media_type(
                        schema: new schema_object(
                            content: [
                                'banks' => new array_of_things(thingtype: formatted_bank::class),
                            ],
                        ),
                    ),
                ],
            ),
        ],
        requirelogin: new require_login(true, courseattributename: 'course'),
    )]
    public function banks(
        ServerRequestInterface $request,
        ResponseInterface $response,
        course $coursecontext,
        module $currentmodulecontext,
        question_bank_helper $helper,
    ): payload_response {
        $params = $request->getQueryParams();
        $banks = $helper::get_banks_for_course(
            $coursecontext,
            $currentmodulecontext,
            $params['includeshared'],
            $params['includerecent'],
        );
        return new payload_response(
            request: $request,
            response: $response,
            payload: ['banks' => $banks],
        );
    }

    #[route(
        path: '/categories', // Resolves to /api/rest/v2/question/categories.
        queryparams: [
            new query_coursemodule(),
        ],
        responses: [
            new response(
                statuscode: 200,
                description: 'OK',
                content: [
                    new json_media_type(
                        schema: new schema_object(
                            content: [
                                'context' => new array_of_strings(param::ALPHA, param::TEXT),
                                'categories' => new array_of_things(question_category::class),
                            ],
                        ),
                    ),
                ],
            ),
        ],
    )]
    /**
     * Return a list of question categories with names and info formatted for output.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param module $coursemodulecontext The module context.
     * @param question_category_selector $categoryselector Injected dependency.
     * @return payload_response The course module context id and name, and a list of question categories in that context.
     */
    public function categories(
        ServerRequestInterface $request,
        ResponseInterface $response,
        module $coursemodulecontext,
        question_category_selector $categoryselector,
    ): payload_response {
        require_login();
        $categories = $categoryselector->get_categories_for_contexts($coursemodulecontext->id, top: true);
        return new payload_response(
            request: $request,
            response: $response,
            payload: [
                'context' => [
                    'id' => $coursemodulecontext->id,
                    'name' => $coursemodulecontext->get_context_name(false),
                    'prefixedname' => $coursemodulecontext->get_context_name(),
                ],
                'categories' => $categories,
            ],
        );
    }
}
