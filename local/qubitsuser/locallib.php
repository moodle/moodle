<?php
require_once($CFG->dirroot.'/enrol/locallib.php');
require_once($CFG->dirroot.'/user/lib.php');

class qbcourse_enrolment_manager extends course_enrolment_manager{

    /**
     * Gets an array of the users that can be enrolled in this course.
     *
     * @global moodle_database $DB
     * @param int $enrolid
     * @param string $search
     * @param bool $searchanywhere
     * @param int $page Defaults to 0
     * @param int $perpage Defaults to 25
     * @param int $addedenrollment Defaults to 0
     * @param bool $returnexactcount Return the exact total users using count_record or not.
     * @return array with two or three elements:
     *      int totalusers Number users matching the search. (This element only exist if $returnexactcount was set to true)
     *      array users List of user objects returned by the query.
     *      boolean moreusers True if there are still more users, otherwise is False.
     * @throws dml_exception
     */
    public function get_potential_users($enrolid, $search = '', $searchanywhere = false, $page = 0, $perpage = 25,
            $addedenrollment = 0, $returnexactcount = false, $cohortusers = array()) {
        global $DB;

        [$ufields, $joins, $params, $wherecondition] = $this->get_basic_search_conditions($search, $searchanywhere);

        $useridcndstr = " AND u.id in (0)";
        if(count($cohortusers) > 0){
            $strcohortusers = implode(",", $cohortusers);
            $useridcndstr = " AND u.id in ($strcohortusers) ";
        }

        $fields      = 'SELECT '.$ufields;
        $countfields = 'SELECT COUNT(1)';
        $sql = " FROM {user} u
                      $joins
            LEFT JOIN {user_enrolments} ue ON (ue.userid = u.id AND ue.enrolid = :enrolid)
                WHERE $wherecondition $useridcndstr 
                      AND ue.id IS NULL ";
        $params['enrolid'] = $enrolid;

        return $this->execute_search_queries($search, $fields, $countfields, $sql, $params, $page, $perpage, $addedenrollment,
                $returnexactcount);
    }

    /**
     * Helper method used by {@link get_potential_users()} and {@link search_other_users()}.
     *
     * @param string $search the search string, if any.
     * @param string $fields the first bit of the SQL when returning some users.
     * @param string $countfields fhe first bit of the SQL when counting the users.
     * @param string $sql the bulk of the SQL statement.
     * @param array $params query parameters.
     * @param int $page which page number of the results to show.
     * @param int $perpage number of users per page.
     * @param int $addedenrollment number of users added to enrollment.
     * @param bool $returnexactcount Return the exact total users using count_record or not.
     * @return array with two or three elements:
     *      int totalusers Number users matching the search. (This element only exist if $returnexactcount was set to true)
     *      array users List of user objects returned by the query.
     *      boolean moreusers True if there are still more users, otherwise is False.
     * @throws dml_exception
     */
    protected function execute_search_queries($search, $fields, $countfields, $sql, array $params, $page, $perpage,
            $addedenrollment = 0, $returnexactcount = false) {
        global $DB, $CFG;

        list($sort, $sortparams) = users_order_by_sql('u', $search, $this->get_context());
        $order = ' ORDER BY ' . $sort;

        $totalusers = 0;
        $moreusers = false;
        $results = [];

        $availableusers = $DB->get_records_sql($fields . $sql . $order,
                array_merge($params, $sortparams), ($page * $perpage) - $addedenrollment, $perpage + 1);
        if ($availableusers) {
            $totalusers = count($availableusers);
            $moreusers = $totalusers > $perpage;

            if ($moreusers) {
                // We need to discard the last record.
                array_pop($availableusers);
            }

            if ($returnexactcount && $moreusers) {
                // There is more data. We need to do the exact count.
                $totalusers = $DB->count_records_sql($countfields . $sql, $params);
            }
        }

        $results['users'] = $availableusers;
        $results['moreusers'] = $moreusers;

        if ($returnexactcount) {
            // Include totalusers in result if $returnexactcount flag is true.
            $results['totalusers'] = $totalusers;
        }

        return $results;
    }

}