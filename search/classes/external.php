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
 * Handles external (web service) function calls related to search.
 *
 * @package core_search
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search;

use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_user\external\user_summary_exporter;

/**
 * Handles external (web service) function calls related to search.
 *
 * @package core_search
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends \core_external\external_api {
    /**
     * Returns parameter types for get_relevant_users function.
     *
     * @return external_function_parameters Parameters
     */
    public static function get_relevant_users_parameters() {
        return new external_function_parameters([
            'query' => new external_value(
                PARAM_RAW,
                'Query string (full or partial user full name or other details)'
            ),
            'courseid' => new external_value(PARAM_INT, 'Course id (0 if none)'),
        ]);
    }

    /**
     * Returns result type for get_relevant_users function.
     *
     * @return external_description Result type
     */
    public static function get_relevant_users_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'User id'),
                'fullname' => new external_value(PARAM_RAW, 'Full name as text'),
                'profileimageurlsmall' => new external_value(PARAM_URL, 'URL to small profile image')
            ])
        );
    }

    /**
     * Searches for users given a query, taking into account the current user's permissions and
     * possibly a course to check within.
     *
     * @param string $query Query text
     * @param int $courseid Course id or 0 if no restriction
     * @return array Defined return structure
     */
    public static function get_relevant_users($query, $courseid) {
        global $CFG, $PAGE;

        // Validate parameter.
        [
            'query' => $query,
            'courseid' => $courseid,
        ] = self::validate_parameters(self::get_relevant_users_parameters(), [
            'query' => $query,
            'courseid' => $courseid,
        ]);

        // Validate the context (search page is always system context).
        $systemcontext = \context_system::instance();
        self::validate_context($systemcontext);

        // Get course object too.
        if ($courseid) {
            $coursecontext = \context_course::instance($courseid);
        } else {
            $coursecontext = null;
        }

        // If not logged in, can't see anyone when forceloginforprofiles is on.
        if (!empty($CFG->forceloginforprofiles)) {
            if (!isloggedin() || isguestuser()) {
                return [];
            }
        }

        $users = \core_user::search($query, $coursecontext);

        $result = [];
        foreach ($users as $user) {
            // Get a standard exported user object.
            $fulldetails = (new user_summary_exporter($user))->export($PAGE->get_renderer('core'));

            // To avoid leaking private data to students, only include the specific information we
            // are going to display (and not the email, idnumber, etc).
            $result[] = (object)['id' => $fulldetails->id, 'fullname' => $fulldetails->fullname,
                    'profileimageurlsmall' => $fulldetails->profileimageurlsmall];
        }
        return $result;
    }

    /**
     * get_results parameters.
     *
     * @since Moodle 3.2
     * @return external_function_parameters
     */
    public static function get_results_parameters() {
        return new external_function_parameters(
            array(
                'q' => new external_value(PARAM_NOTAGS, 'the search query'),
                'filters' => new external_single_structure(
                    array(
                        'title' => new external_value(PARAM_NOTAGS, 'result title', VALUE_OPTIONAL),
                        'areaids' => new external_multiple_structure(
                            new external_value(PARAM_RAW, 'areaid'), 'restrict results to these areas', VALUE_DEFAULT, []
                        ),
                        'courseids' => new external_multiple_structure(
                            new external_value(PARAM_INT, 'courseid'), 'restrict results to these courses', VALUE_DEFAULT, []
                        ),
                        'contextids' => new external_multiple_structure(
                            new external_value(PARAM_INT, 'contextid'), 'restrict results to these context', VALUE_DEFAULT, []
                        ),
                        'userids'  => new external_multiple_structure(
                            new external_value(PARAM_INT, 'userid'), 'restrict results to these users', VALUE_DEFAULT, []
                        ),
                        'groupids' => new external_multiple_structure(
                            new external_value(PARAM_INT, 'groupid'), 'restrict results to these groups', VALUE_DEFAULT, []
                        ),
                        'mycoursesonly' => new external_value(PARAM_BOOL, 'result title', VALUE_OPTIONAL),
                        'order' => new external_value(PARAM_ALPHA, 'result title', VALUE_OPTIONAL),
                        'timestart' => new external_value(PARAM_INT, 'result title', VALUE_DEFAULT, 0),
                        'timeend' => new external_value(PARAM_INT, 'result title', VALUE_DEFAULT, 0)
                    ), 'filters to apply', VALUE_OPTIONAL
                ),
                'page' => new external_value(PARAM_INT, 'results page number starting from 0, defaults to the first page',
                    VALUE_DEFAULT)
            )
        );
    }


    /*
     * Gets global search results based on the provided query and filters.
     *
     * @param string $q
     * @param array $filters
     * @param int $page
     * @return array
     */
    public static function get_results($q, $filters = [], $page = 0) {
        global $PAGE;

        $params = self::validate_parameters(self::get_results_parameters(), array(
            'q' => $q,
            'filters' => $filters,
            'page' => $page)
        );

        $system = \context_system::instance();
        \external_api::validate_context($system);

        require_capability('moodle/search:query', $system);

        if (\core_search\manager::is_global_search_enabled() === false) {
            throw new \moodle_exception('globalsearchdisabled', 'search');
        }

        $search = \core_search\manager::instance();

        $data = new \stdClass();
        $data->q = $params['q'];

        if (!empty($params['filters']['title'])) {
            $data->title = $params['filters']['title'];
        }

        if (!empty($params['filters']['areaids'])) {
            $data->areaids = $params['filters']['areaids'];
        }

        if (!empty($params['filters']['courseids'])) {
            $data->courseids = $params['filters']['courseids'];
        }

        if (!empty($params['filters']['contextids'])) {
            $data->contextids = $params['filters']['contextids'];
        }

        if (!empty($params['filters']['userids'])) {
            $data->userids = $params['filters']['userids'];
        }

        if (!empty($params['filters']['groupids'])) {
            $data->groupids = $params['filters']['groupids'];
        }

        if (!empty($params['filters']['timestart'])) {
            $data->timestart = $params['filters']['timestart'];
        }
        if (!empty($params['filters']['timeend'])) {
            $data->timeend = $params['filters']['timeend'];
        }

        $docs = $search->paged_search($data, $page);

        $return = [
            'totalcount' => $docs->totalcount,
            'warnings' => [],
            'results' => []
        ];

        // Convert results to simple data structures.
        if ($docs) {
            foreach ($docs->results as $doc) {
                $return['results'][] = $doc->export_doc($PAGE->get_renderer('core'));
            }
        }
        return $return;
    }

    /**
     * Returns description of method get_results.
     *
     * @return external_single_structure
     */
    public static function get_results_returns() {

        return new external_single_structure(
            array(
                'totalcount' => new external_value(PARAM_INT, 'Total number of results'),
                'results' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'itemid' => new external_value(PARAM_INT, 'unique id in the search area scope'),
                            'componentname' => new external_value(PARAM_ALPHANUMEXT, 'component name'),
                            'areaname' => new external_value(PARAM_ALPHANUMEXT, 'search area name'),
                            'courseurl' => new external_value(PARAM_URL, 'result course url'),
                            'coursefullname' => new external_value(PARAM_RAW, 'result course fullname'),
                            'timemodified' => new external_value(PARAM_INT, 'result modified time'),
                            'title' => new external_value(PARAM_RAW, 'result title'),
                            'docurl' => new external_value(PARAM_URL, 'result url'),
                            'content' => new external_value(PARAM_RAW, 'result contents', VALUE_OPTIONAL),
                            'contextid' => new external_value(PARAM_INT, 'result context id'),
                            'contexturl' => new external_value(PARAM_URL, 'result context url'),
                            'description1' => new external_value(PARAM_RAW, 'extra result contents, depends on the search area', VALUE_OPTIONAL),
                            'description2' => new external_value(PARAM_RAW, 'extra result contents, depends on the search area', VALUE_OPTIONAL),
                            'multiplefiles' => new external_value(PARAM_INT, 'whether multiple files are returned or not', VALUE_OPTIONAL),
                            'filenames' => new external_multiple_structure(
                                new external_value(PARAM_RAW, 'result file name', VALUE_OPTIONAL)
                                , 'result file names if present',
                                VALUE_OPTIONAL
                            ),
                            'filename' => new external_value(PARAM_RAW, 'result file name if present', VALUE_OPTIONAL),
                            'userid' => new external_value(PARAM_INT, 'user id', VALUE_OPTIONAL),
                            'userurl' => new external_value(PARAM_URL, 'user url', VALUE_OPTIONAL),
                            'userfullname' => new external_value(PARAM_RAW, 'user fullname', VALUE_OPTIONAL),
                            'textformat' => new external_value(PARAM_INT, 'text fields format, it is the same for all of them')
                        ), 'Search result'
                    ), 'Search results', VALUE_OPTIONAL
                )
            )
        );
    }
}
