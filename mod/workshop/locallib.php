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
 * Library of internal classes and functions for module workshop
 *
 * All the workshop specific functions, needed to implement the module
 * logic, should go to here. Instead of having bunch of function named
 * workshop_something() taking the workshop instance as the first
 * parameter, we use a class workshop_api that provides all methods.
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/lib.php'); // we extend this library here

define('WORKSHOP_ALLOCATION_EXISTS',        -1);    // return status of {@link add_allocation}

/**
 * Full-featured workshop API
 *
 * This extends the module base API and adds the internal methods that are called
 * from the module itself. The class should be initialized right after you get
 * $workshop and $cm records at the begining of the script.
 */
class workshop_api extends workshop {

    /** grading strategy instance */
    protected $strategy_api=null;

    /**
     * Initialize the object using the data from DB
     *
     * @param object $instance  The instance data row from {workshop} table
     * @param object $md        Course module record
     */
    public function __construct($instance, $cm) {

        parent::__construct($instance, $cm);
    }

    /**
     * Fetches all users with the capability mod/workshop:submit in the current context
     *
     * Static variables used to cache the results. The returned objects contain id, lastname
     * and firstname properties and are ordered by lastname,firstname
     *
     * @param bool $musthavesubmission If true, return only users who have already submitted. All possible authors otherwise.
     * @return array array[userid] => stdClass{->id ->lastname ->firstname}
     */
    public function get_peer_authors($musthavesubmission=true) {
        global $DB;
        static $users               = null;
        static $userswithsubmission = null;

        if (is_null($users)) {
            $context = get_context_instance(CONTEXT_MODULE, $this->cm->id);
            $users = get_users_by_capability($context, 'mod/workshop:submit',
                        'u.id, u.lastname, u.firstname', 'u.lastname,u.firstname', '', '', '', '', false, false, true);
        }

        if ($musthavesubmission && is_null($userswithsubmission)) {
            $userswithsubmission = $DB->get_records_list('workshop_submissions', 'userid', array_keys($users),'', 'userid');
            $userswithsubmission = array_intersect_key($users, $userswithsubmission);
        }

        if ($musthavesubmission) {
            return $userswithsubmission;
        } else {
            return $users;
        }
    }

    /**
     * Returns all users with the capability mod/workshop:submit sorted by groups
     *
     * This takes the module grouping settings into account. If "Available for group members only"
     * is set, returns only groups withing the course module grouping.
     *
     * @param bool $musthavesubmission If true, return only users who have already submitted. All possible authors otherwise.
     * @return array array[groupid][userid] => stdClass{->id ->lastname ->firstname}
     */
    public function get_peer_authors_by_group($musthavesubmission=true) {
        global $DB;

        $authors    = $this->get_peer_authors($musthavesubmission);
        $gauthors   = array();  // grouped authors to be returned
        if ($this->cm->groupmembersonly) {
            // Available for group members only - the workshop is available only
            // to users assigned to groups within the selected grouping, or to
            // any group if no grouping is selected.
            $groupingid = $this->cm->groupingid;
            // All authors that are members of at least one group will be
            // added into a virtual group id 0
            $gauthors[0] = array();
        } else {
            $groupingid = 0;
            // there is no need to be member of a group so $gauthors[0] will contain
            // all authors with a submission
            $gauthors[0] = $authors;
        }
        $gmemberships = groups_get_all_groups($this->cm->course, array_keys($authors), $groupingid,
                            'gm.id,gm.groupid,gm.userid');
        foreach ($gmemberships as $gmembership) {
            if (!isset($gauthors[$gmembership->groupid])) {
                $gauthors[$gmembership->groupid] = array();
            }
            $gauthors[$gmembership->groupid][$gmembership->userid]  = $authors[$gmembership->userid];
            $gauthors[0][$gmembership->userid]                      = $authors[$gmembership->userid];
        }
        return $gauthors;
    }

    /**
     * Fetches all users with the capability mod/workshop:peerassess in the current context
     *
     * Static variable used to cache the results. The returned objects contain id, lastname
     * and firstname properties and are ordered by lastname,firstname
     *
     * @param bool $musthavesubmission If true, return only users who have already submitted. All possible users otherwise.
     * @return array array[userid] => stdClass{->id ->lastname ->firstname}
     */
    public function get_peer_reviewers($musthavesubmission=false) {
        global $DB;
        static $users               = null;
        static $userswithsubmission = null;

        if (is_null($users)) {
            $context = get_context_instance(CONTEXT_MODULE, $this->cm->id);
            $users = get_users_by_capability($context, 'mod/workshop:peerassess',
                        'u.id, u.lastname, u.firstname', 'u.lastname,u.firstname', '', '', '', '', false, false, true);
            if ($musthavesubmission && is_null($userswithsubmission)) {
                // users without their own submission can not be reviewers
                $userswithsubmission = $DB->get_records_list('workshop_submissions', 'userid', array_keys($users),'', 'userid');
                $userswithsubmission = array_intersect_key($users, $userswithsubmission);
            }
        }
        if ($musthavesubmission) {
            return $userswithsubmission;
        } else {
            return $users;
        }
    }

    /**
     * Returns all users with the capability mod/workshop:peerassess sorted by groups
     *
     * This takes the module grouping settings into account. If "Available for group members only"
     * is set, returns only groups withing the course module grouping.
     *
     * @param bool $musthavesubmission If true, return only users who have already submitted. All possible users otherwise.
     * @return array array[groupid][userid] => stdClass{->id ->lastname ->firstname}
     */
    public function get_peer_reviewers_by_group($musthavesubmission=false) {
        global $DB;

        $reviewers  = $this->get_peer_reviewers($musthavesubmission);
        $greviewers = array();  // grouped reviewers to be returned
        if ($this->cm->groupmembersonly) {
            // Available for group members only - the workshop is available only
            // to users assigned to groups within the selected grouping, or to
            // any group if no grouping is selected.
            $groupingid = $this->cm->groupingid;
            // All reviewers that are members of at least one group will be
            // added into a virtual group id 0
            $greviewers[0] = array();
        } else {
            $groupingid = 0;
            // there is no need to be member of a group so $greviewers[0] will contain
            // all reviewers
            $greviewers[0] = $reviewers;
        }
        $gmemberships = groups_get_all_groups($this->cm->course, array_keys($reviewers), $groupingid,
                            'gm.id,gm.groupid,gm.userid');
        foreach ($gmemberships as $gmembership) {
            if (!isset($greviewers[$gmembership->groupid])) {
                $greviewers[$gmembership->groupid] = array();
            }
            $greviewers[$gmembership->groupid][$gmembership->userid] = $reviewers[$gmembership->userid];
            $greviewers[0][$gmembership->userid] = $reviewers[$gmembership->userid];
        }
        return $greviewers;
    }

    /**
     * Returns submissions from this workshop
     *
     * Fetches data from {workshop_submissions} and adds some useful information from other
     * tables.
     *
     * @param mixed $userid int|array|'all' If set to [array of] integer, return submission[s] of the given user[s] only
     * @param mixed $examples false|true|'all' Only regular submissions, only examples, all submissions
     * @todo unittest
     * @return object moodle_recordset
     */
    public function get_submissions_recordset($userid='all', $examples=false) {
        global $DB;

        $sql = 'SELECT s.*, u.lastname AS authorlastname, u.firstname AS authorfirstname
                FROM {workshop_submissions} s
                JOIN {user} u ON (s.userid = u.id)
                WHERE s.workshopid = ?';
        $params[0] = $this->id;

        if ($examples === false) {
            $sql .= ' AND example = 0';
        }
        if ($examples === true) {
            $sql .= ' AND example = 1';
        }
        if (is_int($userid)) {
            $sql .= ' AND userid = ?';
            $params = array_merge($params, array($userid));
        }
        if (is_array($userid)) {
            list($usql, $uparams) = $DB->get_in_or_equal($userid);
            $sql .= ' AND userid ' . $usql;
            $params = array_merge($params, $uparams);
        }

        return $DB->get_recordset_sql($sql, $params);
    }

    /**
     * Returns a submission submitted by the given author or authors.
     *
     * This is intended for regular workshop participants, not for example submissions by teachers.
     * If an array of authors is provided, returns array of stripped submission records so they do not
     * include text fields (to prevent possible memory-lack issues).
     *
     * @param mixed $id integer|array author ID or IDs
     * @return mixed false if not found, object if $id is int, array if $id is array
     */
    public function get_submission_by_author($id) {
        if (empty($id)) {
            return false;
        }
        $rs = $this->get_submissions_recordset($id, false);
        if (is_int($id)) {
            $submission = $rs->current();
            $rs->close();
            if (empty($submission->id)) {
                return false;
            } else {
                return $submission;
            }
        } elseif (is_array($id)) {
            $submissions = array();
            foreach ($rs as $submission) {
                $submissions[$submission->id] = new stdClass();
                foreach ($submission as $property => $value) {
                    // we do not want text fields here to prevent possible memory issues
                    if (in_array($property, array('id', 'workshopid', 'example', 'userid', 'authorlastname', 'authorfirstname',
                            'timecreated', 'timemodified', 'grade', 'gradeover', 'gradeoverby', 'gradinggrade'))) {
                        $submissions[$submission->id]->{$property} = $value;
                    }
                }
            }
            return $submissions;
        } else {
            throw new moodle_workshop_exception($this, 'wrongparameter');
        }
    }

    /**
     * Returns the list of assessments with some data added
     *
     * Fetches data from {workshop_assessments} and adds some useful information from other
     * tables.
      *
     * @param mixed $reviewerid 'all'|int|array User ID of the reviewer
     * @param mixed $id         'all'|int Assessment ID
     * @return object moodle_recordset
     */
    public function get_assessments_recordset($reviewerid='all', $id='all') {
        global $DB;

        $sql = 'SELECT  a.*,
                        reviewer.id AS reviewerid,reviewer.firstname AS reviewerfirstname,reviewer.lastname as reviewerlastname,
                        s.title,
                        author.id AS authorid, author.firstname AS authorfirstname,author.lastname as authorlastname
                FROM {workshop_assessments} a
                INNER JOIN {user} reviewer ON (a.userid = reviewer.id)
                INNER JOIN {workshop_submissions} s ON (a.submissionid = s.id)
                INNER JOIN {user} author ON (s.userid = author.id)
                WHERE s.workshopid = ?';
        $params = array($this->id);
        if (is_int($reviewerid)) {
            $sql .= ' AND reviewerid = ?';
            $params = array_merge($params, array($reviewerid));
        }
        if (is_array($reviewerid)) {
            list($usql, $uparams) = $DB->get_in_or_equal($reviewerid);
            $sql .= ' AND reviewerid ' . $usql;
            $params = array_merge($params, $uparams);
        }
        if (is_int($id)) {
            $sql .= ' AND a.id = ?';
            $params = array_merge($params, array($id));
        }

        return $DB->get_recordset_sql($sql, $params);
    }

    /**
     * Returns the list of assessments with some data added
     *
     * Fetches data from {workshop_assessments} and adds some useful information from other
     * tables. The returned objects are lightweight version of those returned by get_assessments_recordset(),
     * mainly they do not contain text fields.
     *
     * @param mixed $reviewerid 'all'|int|array User ID of the reviewer
     * @return array [assessmentid] => assessment object
     * @see workshop_api::get_assessments_recordset() for the structure of returned objects
     */
    public function get_assessments($reviewerid='all') {
        $rs = $this->get_assessments_recordset($reviewerid, 'all');
        $assessments = array();
        foreach ($rs as $assessment) {
            // copy selected properties into the array to be returned. This is here mainly in order not
            // to include text comments.
            $assessments[$assessment->id] = new stdClass;
            foreach ($assessment as $property => $value) {
                if (in_array($property, array('id', 'submissionid', 'userid', 'timecreated', 'timemodified',
                        'timeagreed', 'grade', 'gradinggrade', 'gradinggradeover', 'gradinggradeoverby',
                        'reviewerid', 'reviewerfirstname', 'reviewerlastname', 'title', 'authorid',
                        'authorfirstname', 'authorlastname'))) {
                    $assessments[$assessment->id]->{$property} = $value;
                }
            }
        }
        $rs->close();
        return $assessments;
    }

    /**
     * Get the information about the given assessment
     *
     * @param int $id Assessment ID
     * @see workshop_api::get_assessments_recordset() for the structure of data returned
     * @return mixed false if not found, object otherwise
     */
    public function get_assessment_by_id($id) {
        $rs         = $this->get_assessments_recordset('all', $id);
        $assessment = $rs->current();
        $rs->close();
        if (empty($assessment->id)) {
            return false;
        } else {
            return $assessment;
        }
    }

    /**
     * Get the information about all assessments assigned to the given reviewer
     *
     * @param int $id Reviewer ID
     * @see workshop_api::get_assessments_recordset() for the structure of data returned
     * @return array array of objects
     */
    public function get_assessments_by_reviewer($id) {
        $rs = $this->get_assessments_recordset($id);
        $assessments = array();
        foreach ($rs as $assessment) {
            $assessments[$assessment->id] = $assessment;
        }
        $rs->close();
        return $assessment;
    }

    /**
     * Returns the list of allocations in the workshop
     *
     * This returns the list of all users who can submit their work or review submissions (or both
     * which is the common case). So basically this is to return list of all students participating
     * in the workshop. For every participant, it adds information about their submission and their
     * reviews.
     *
     * The returned structure is recordset of objects with following properties:
     * [authorid] [authorfirstname] [authorlastname] [authorpicture] [authorimagealt]
     * [submissionid] [submissiontitle] [submissiongrade] [assessmentid]
     * [timeallocated] [reviewerid] [reviewerfirstname] [reviewerlastname]
     * [reviewerpicture] [reviewerimagealt]
     *
     * This should be refactored when capability handling proposed by Petr is implemented so that
     * we can check capabilities directly in SQL joins.
     * Note that the returned recordset includes participants without submission as well as those
     * without any review allocated yet.
     *
     * @return object moodle_recordset
     */
    public function get_allocations_recordset() {
        global $DB;
        static $users=null;

        if (is_null($users)) {
            $context = get_context_instance(CONTEXT_MODULE, $this->cm->id);
            $users = get_users_by_capability($context, array('mod/workshop:submit', 'mod/workshop:peerassess'),
                        'u.id', 'u.lastname,u.firstname', '', '', '', '', false, false, true);
        }

        list($usql, $params) = $DB->get_in_or_equal(array_keys($users));
        $params[] = $this->id;

        $sql = 'SELECT  author.id AS authorid, author.firstname AS authorfirstname, author.lastname AS authorlastname,
                        author.picture AS authorpicture, author.imagealt AS authorimagealt,
                        s.id AS submissionid, s.title AS submissiontitle, s.grade AS submissiongrade,
                        a.id AS assessmentid, a.timecreated AS timeallocated, a.userid AS reviewerid,
                        reviewer.firstname AS reviewerfirstname, reviewer.lastname AS reviewerlastname,
                        reviewer.picture as reviewerpicture, reviewer.imagealt AS reviewerimagealt
                FROM {user} author
                    LEFT JOIN {workshop_submissions} s ON (s.userid = author.id)
                    LEFT JOIN {workshop_assessments} a ON (s.id = a.submissionid)
                    LEFT JOIN {user} reviewer ON (a.userid = reviewer.id)
                WHERE author.id ' . $usql . ' AND (s.workshopid = ? OR s.workshopid IS NULL)
                ORDER BY author.lastname,author.firstname,reviewer.lastname,reviewer.firstname';
        return $DB->get_recordset_sql($sql, $params);
    }

    /**
     * Allocate a submission to a user for review
     *
     * @param object $submission Submission record
     * @param int $reviewerid User ID
     * @param bool $bulk repeated inserts into DB expected
     * @return int ID of the new assessment or an error code
     */
    public function add_allocation(stdClass $submission, $reviewerid, $bulk=false) {
        global $DB;

        if ($DB->record_exists('workshop_assessments', array('submissionid' => $submission->id, 'userid' => $reviewerid))) {
            return WORKSHOP_ALLOCATION_EXISTS;
        }

        $now = time();
        $assessment = new stdClass();
        $assessment->submissionid   = $submission->id;
        $assessment->userid         = $reviewerid;
        $assessment->timecreated    = $now;
        $assessment->timemodified   = $now;

        return $DB->insert_record('workshop_assessments', $assessment, true, $bulk);
    }

    /**
     * Delete assessment record or records
     *
     * @param mixed $id int|array assessment id or array of assessments ids
     * @return bool false if $id not a valid parameter, true otherwise
     */
    public function delete_assessment($id) {
        global $DB;

        // todo remove all given grades from workshop_grades;

        if (is_numeric($id)) {
            return $DB->delete_records('workshop_assessments', array('id' => $id));
        }
        if (is_array($id)) {
            return $DB->delete_records_list('workshop_assessments', 'id', $id);
        }
        return false;
    }

    /**
     * Returns instance of grading strategy class
     *
     * @param object $workshop Workshop record
     * @return object Instance of a grading strategy
     */
    public function grading_strategy_instance() {
        if (!($this->strategy === clean_param($workshop->strategy, PARAM_ALPHA))) {
            throw new moodle_workshop_exception($this, 'invalidstrategyname');
        }

        if (is_null($this->strategy_api)) {
            $strategylib = dirname(__FILE__) . '/grading/' . $workshop->strategy . '/strategy.php';
            if (is_readable($strategylib)) {
                require_once($strategylib);
            } else {
                throw new moodle_workshop_exception($this, 'missingstrategy');
            }
            $classname = 'workshop_' . $workshop->strategy . '_strategy';
            $this->strategy_api = new $classname($this);
            if (!in_array('workshop_strategy', class_implements($this->strategy_api))) {
                throw new moodle_workshop_exception($this, 'strategynotimplemented');
            }
        }

        return $this->strategy_api;
    }

    /**
     * Return list of available allocation methods
     *
     * @return array Array ['string' => 'string'] of localized allocation method names
     */
    public function installed_allocators() {
        $installed = get_list_of_plugins('mod/workshop/allocation');
        $forms = array();
        foreach ($installed as $allocation) {
            $forms[$allocation] = get_string('allocation' . $allocation, 'workshop');
        }
        // usability - make sure that manual allocation appears the first
        if (isset($forms['manual'])) {
            $m = array('manual' => $forms['manual']);
            unset($forms['manual']);
            $forms = array_merge($m, $forms);
        }
        return $forms;
    }

    /**
     * Returns instance of submissions allocator
     *
     * @param object $method The name of the allocation method, must be PARAM_ALPHA
     * @return object Instance of submissions allocator
     */
    public function allocator_instance($method) {
        $allocationlib = dirname(__FILE__) . '/allocation/' . $method . '/allocator.php';
        if (is_readable($allocationlib)) {
            require_once($allocationlib);
        } else {
            throw new moodle_workshop_exception($this, 'missingallocator');
        }
        $classname = 'workshop_' . $method . '_allocator';
        return new $classname($this);
    }

}

/**
 * Class for workshop exceptions. Just saves a couple of arguments of the
 * constructor for a moodle_exception.
 *
 * @param object $workshop Should be workshop or its subclass
 * @param string $errorcode
 * @param mixed $a Object/variable to pass to get_string
 * @param string $link URL to continue after the error notice
 * @param $debuginfo
 */
class moodle_workshop_exception extends moodle_exception {

    function __construct($workshop, $errorcode, $a = NULL, $link = '', $debuginfo = null) {
        global $CFG;

        if (!$link) {
            $link = $CFG->wwwroot . '/mod/workshop/view.php?a=' . $workshop->id;
        }
        if ('confirmsesskeybad' == $errorcode) {
            $module = '';
        } else {
            $module = 'workshop';
        }
        parent::__construct($errorcode, $module, $link, $a, $debuginfo);
    }
}

