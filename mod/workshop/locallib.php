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
 * parameter, we use a class workshop that provides all methods.
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
 * This wraps the workshop database record with a set of methods that are called
 * from the module itself. The class should be initialized right after you get
 * $workshop, $cm and $course records at the begining of the script.
 */
class workshop {

    /** @var stdClass course module record */
    public $cm = null;

    /** @var stdClass course record */
    public $course = null;

    /** grading strategy instance */
    private $strategyinstance = null;

    /**
     * Initializes the workshop API instance using the data from DB
     *
     * Makes deep copy of all passed records properties. Replaces integer $course attribute
     * with a full database record (course should not be stored in instances table anyway).
     *
     * @param stdClass $instance Workshop instance data from {workshop} table
     * @param stdClass $cm       Course module record as returned by {@link get_coursemodule_from_id()}
     * @param stdClass $course   Course record from {course} table
     */
    public function __construct(stdClass $instance, stdClass $cm, stdClass $course) {
        foreach ($instance as $key => $val) {
            if (is_object($val) || (is_array($val))) {
                // this should not happen if the $instance is really just the record returned by $DB
                $this->{$key} = unserialize(serialize($val));   // makes deep copy of referenced variables
            } else {
                $this->{$key} = $val;
            }
        }
        $this->cm     = unserialize(serialize($cm));
        $this->course = unserialize(serialize($course));
    }

    /**
     * Fetches all users with the capability mod/workshop:submit in the current context
     *
     * The returned objects contain id, lastname and firstname properties and are ordered by lastname,firstname
     *
     * @param bool $musthavesubmission If true, return only users who have already submitted. All possible authors otherwise.
     * @return array array[userid] => stdClass{->id ->lastname ->firstname}
     */
    public function get_peer_authors($musthavesubmission=true) {
        global $DB;

        $context = get_context_instance(CONTEXT_MODULE, $this->cm->id);
        $users = get_users_by_capability($context, 'mod/workshop:submit',
                    'u.id, u.lastname, u.firstname', 'u.lastname,u.firstname', '', '', '', '', false, false, true);

        if ($musthavesubmission) {
            $userswithsubmission = array();
            $submissions = $DB->get_records_list('workshop_submissions', 'userid', array_keys($users),'', 'id,userid');
            foreach ($submissions as $submission) {
                $userswithsubmission[$submission->userid] = null;
            }
            $userswithsubmission = array_intersect_key($users, $userswithsubmission);
        }

        if ($musthavesubmission) {
            return $userswithsubmission;
        } else {
            return $users;
        }
    }

    /**
     * Fetches all users with the capability mod/workshop:peerassess in the current context
     *
     * Static variable used to cache the results. The returned objects contain id, lastname
     * and firstname properties and are ordered by lastname,firstname
     *
     * @param bool $musthavesubmission If true, return only users who have already submitted. All possible users otherwise.
     * @see get_super_reviewers()
     * @return array array[userid] => stdClass{->id ->lastname ->firstname}
     */
    public function get_peer_reviewers($musthavesubmission=false) {
        global $DB;

        $context = get_context_instance(CONTEXT_MODULE, $this->cm->id);
        $users = get_users_by_capability($context, 'mod/workshop:peerassess',
                    'u.id, u.lastname, u.firstname', 'u.lastname,u.firstname', '', '', '', '', false, false, true);

        if ($musthavesubmission) {
            // users without their own submission can not be reviewers
            $submissions = $DB->get_records_list('workshop_submissions', 'userid', array_keys($users),'', 'id,userid');
            foreach ($submissions as $submission) {
                $userswithsubmission[$submission->userid] = null;
            }
            $userswithsubmission = array_intersect_key($users, $userswithsubmission);
        }

        if ($musthavesubmission) {
            return $userswithsubmission;
        } else {
            return $users;
        }
    }

    /**
     * Fetches all users with the capability mod/workshop:assessallsubmissions in the current context
     *
     * Static variable used to cache the results. The returned objects contain id, lastname
     * and firstname properties and are ordered by lastname,firstname
     *
     * @param bool $musthavesubmission If true, return only users who have already submitted. All possible users otherwise.
     * @see get_peer_reviewers()
     * @return array array[userid] => stdClass{->id ->lastname ->firstname}
     */
    public function get_super_reviewers() {
        global $DB;

        $context = get_context_instance(CONTEXT_MODULE, $this->cm->id);
        $users = get_users_by_capability($context, 'mod/workshop:assessallsubmissions',
                    'u.id, u.lastname, u.firstname', 'u.lastname,u.firstname', '', '', '', '', false, false, true);

        return $users;
    }

    /**
     * Groups the given users by the group membership
     *
     * This takes the module grouping settings into account. If "Available for group members only"
     * is set, returns only groups withing the course module grouping. Always returns group [0] with
     * all the given users.
     *
     * @param array $users array[userid] => stdClass{->id ->lastname ->firstname}
     * @return array array[groupid][userid] => stdClass{->id ->lastname ->firstname}
     */
    public function get_grouped($users) {
        global $DB;
        global $CFG;

        $grouped = array();  // grouped users to be returned
        if (empty($users)) {
            return $grouped;
        }
        if (!empty($CFG->enablegroupings) and $this->cm->groupmembersonly) {
            // Available for group members only - the workshop is available only
            // to users assigned to groups within the selected grouping, or to
            // any group if no grouping is selected.
            $groupingid = $this->cm->groupingid;
            // All users that are members of at least one group will be
            // added into a virtual group id 0
            $grouped[0] = array();
        } else {
            $groupingid = 0;
            // there is no need to be member of a group so $grouped[0] will contain
            // all users
            $grouped[0] = $users;
        }
        $gmemberships = groups_get_all_groups($this->cm->course, array_keys($users), $groupingid,
                            'gm.id,gm.groupid,gm.userid');
        foreach ($gmemberships as $gmembership) {
            if (!isset($grouped[$gmembership->groupid])) {
                $grouped[$gmembership->groupid] = array();
            }
            $grouped[$gmembership->groupid][$gmembership->userid] = $users[$gmembership->userid];
            $grouped[0][$gmembership->userid] = $users[$gmembership->userid];
        }
        return $grouped;
    }

    /**
     * Returns submissions from this workshop
     *
     * Fetches data from {workshop_submissions} and adds some useful information from other
     * tables.
     *
     * @param mixed $userid int|array|'all' If set to [array of] integer, return submission[s] of the given user[s] only
     * @param mixed $examples false|true|'all' Only regular submissions, only examples, all submissions
     * @return stdClass moodle_recordset
     */
    public function get_submissions_recordset($userid='all', $examples=false) {
        global $DB;

        $sql = 'SELECT s.*, u.lastname AS authorlastname, u.firstname AS authorfirstname
                  FROM {workshop_submissions} s
            INNER JOIN {user} u ON (s.userid = u.id)
                 WHERE s.workshopid = :workshopid';
        $params = array('workshopid' => $this->id);

        if ($examples === true) {
            $sql .= ' AND example = 1';
        } else {
            $sql .= ' AND example = 0';
        }

        if ('all' === $userid) {
            // no additional conditions
        } elseif (is_array($userid)) {
            list($usql, $uparams) = $DB->get_in_or_equal($userid, SQL_PARAMS_NAMED);
            $sql .= " AND userid $usql";
            $params = array_merge($params, $uparams);
        } else {
            $sql .= ' AND userid = :userid';
            $params['userid'] = $userid;
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
     * @return mixed false if not found, stdClass if $id is int, array if $id is array
     */
    public function get_submission_by_author($id) {
        if (empty($id)) {
            return false;
        }
        $rs = $this->get_submissions_recordset($id, false);
        if (is_array($id)) {
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
            $submission = $rs->current();
            $rs->close();
            if (empty($submission->id)) {
                return false;
            } else {
                return $submission;
            }
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
     * @return stdClass moodle_recordset
     */
    public function get_assessments_recordset($reviewerid='all', $id='all') {
        global $DB;

        $sql = 'SELECT a.*,
                       reviewer.id AS reviewerid,reviewer.firstname AS reviewerfirstname,reviewer.lastname as reviewerlastname,
                       s.title,
                       author.id AS authorid, author.firstname AS authorfirstname,author.lastname as authorlastname
                  FROM {workshop_assessments} a
            INNER JOIN {user} reviewer ON (a.userid = reviewer.id)
            INNER JOIN {workshop_submissions} s ON (a.submissionid = s.id)
            INNER JOIN {user} author ON (s.userid = author.id)
                 WHERE s.workshopid = :workshopid';
        $params = array('workshopid' => $this->id);

        if ('all' === $reviewerid) {
            // no additional conditions
        } elseif (is_array($reviewerid)) {
            list($usql, $uparams) = $DB->get_in_or_equal($reviewerid, SQL_PARAMS_NAMED);
            $sql .= " AND reviewer.id $usql";
            $params = array_merge($params, $uparams);
        } else {
            $sql .= ' AND reviewer.id = :reviewerid';
            $params['reviewerid'] = $reviewerid;
        }

        if ('all' === $id) {
            // no additional conditions
        } else {
            $sql .= ' AND a.id = :assessmentid';
            $params['assessmentid'] = $id;
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
     * @param mixed $id         'all'|int Assessment ID
     * @return array [assessmentid] => assessment stdClass
     * @see workshop::get_assessments_recordset() for the structure of returned objects
     */
    public function get_assessments($reviewerid='all', $id='all') {
        $rs = $this->get_assessments_recordset($reviewerid, $id);
        $assessments = array();
        foreach ($rs as $assessment) {
            // copy selected properties into the array to be returned. This is here mainly in order not
            // to include text comments.
            $assessments[$assessment->id] = new stdClass();
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
     * @see workshop::get_assessments_recordset() for the structure of data returned
     * @return mixed false if not found, stdClass otherwise
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
     * @see workshop::get_assessments_recordset() for the structure of data returned
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
     * TODO This should be refactored when capability handling proposed by Petr is implemented so that
     * we can check capabilities directly in SQL joins.
     * Note that the returned recordset includes participants without submission as well as those
     * without any review allocated yet.
     *
     * @return stdClass moodle_recordset
     */
    public function get_allocations_recordset() {
        global $DB;

        $context = get_context_instance(CONTEXT_MODULE, $this->cm->id);
        $users = get_users_by_capability($context, array('mod/workshop:submit', 'mod/workshop:peerassess'),
                    'u.id', 'u.lastname,u.firstname', '', '', '', '', false, false, true);

        list($usql, $params) = $DB->get_in_or_equal(array_keys($users), SQL_PARAMS_NAMED);
        $params['workshopid'] = $this->id;

        $sql = "SELECT author.id AS authorid, author.firstname AS authorfirstname, author.lastname AS authorlastname,
                       author.picture AS authorpicture, author.imagealt AS authorimagealt,
                       s.id AS submissionid, s.title AS submissiontitle, s.grade AS submissiongrade,
                       a.id AS assessmentid, a.timecreated AS timeallocated, a.userid AS reviewerid,
                       reviewer.firstname AS reviewerfirstname, reviewer.lastname AS reviewerlastname,
                       reviewer.picture as reviewerpicture, reviewer.imagealt AS reviewerimagealt
                  FROM {user} author
             LEFT JOIN {workshop_submissions} s ON (s.userid = author.id)
             LEFT JOIN {workshop_assessments} a ON (s.id = a.submissionid)
             LEFT JOIN {user} reviewer ON (a.userid = reviewer.id)
                 WHERE author.id $usql AND s.workshopid = :workshopid
              ORDER BY author.lastname,author.firstname,reviewer.lastname,reviewer.firstname";
        
        return $DB->get_recordset_sql($sql, $params);
    }

    /**
     * Allocate a submission to a user for review
     *
     * @param stdClass $submission Submission record
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

        if (is_array($id)) {
            return $DB->delete_records_list('workshop_assessments', 'id', $id);
        } else {
            return $DB->delete_records('workshop_assessments', array('id' => $id));
        }
    }

    /**
     * Returns instance of grading strategy class
     *
     * @return stdClass Instance of a grading strategy
     */
    public function grading_strategy_instance() {
        global $CFG;    // because we require other libs here

        if (is_null($this->strategyinstance)) {
            $strategylib = dirname(__FILE__) . '/grading/' . $this->strategy . '/strategy.php';
            if (is_readable($strategylib)) {
                require_once($strategylib);
            } else {
                throw new moodle_exception('missingstrategy', 'workshop');
            }
            $classname = 'workshop_' . $this->strategy . '_strategy';
            $this->strategyinstance = new $classname($this);
            if (!in_array('workshop_strategy', class_implements($this->strategyinstance))) {
                throw new moodle_exception('strategynotimplemented', 'workshop');
            }
        }
        return $this->strategyinstance;
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
     * @param stdClass $method The name of the allocation method, must be PARAM_ALPHA
     * @return stdClass Instance of submissions allocator
     */
    public function allocator_instance($method) {
        global $CFG;    // because we require other libs here

        $allocationlib = dirname(__FILE__) . '/allocation/' . $method . '/allocator.php';
        if (is_readable($allocationlib)) {
            require_once($allocationlib);
        } else {
            throw new coding_exception('Unable to find allocator.php');
        }
        $classname = 'workshop_' . $method . '_allocator';
        return new $classname($this);
    }

    /**
     * @return stdClass {@link moodle_url} the URL of this workshop's view page
     */
    public function view_url() {
        global $CFG;
        return new moodle_url($CFG->wwwroot . '/mod/workshop/view.php', array('id' => $this->cm->id));
    }

    /**
     * @return stdClass {@link moodle_url} the URL of the page for editing this workshop's grading form
     */
    public function editform_url() {
        global $CFG;
        return new moodle_url($CFG->wwwroot . '/mod/workshop/editform.php', array('cmid' => $this->cm->id));
    }

    /**
     * @return stdClass {@link moodle_url} the URL of the page for previewing this workshop's grading form
     */
    public function previewform_url() {
        global $CFG;
        return new moodle_url($CFG->wwwroot . '/mod/workshop/assessment.php', array('preview' => $this->cm->id));
    }

    /**
     * @param int $assessmentid The ID of assessment record
     * @return stdClass {@link moodle_url} the URL of the assessment page
     */
    public function assess_url($assessmentid) {
        global $CFG;
        return new moodle_url($CFG->wwwroot . '/mod/workshop/assessment.php', array('asid' => $assessmentid));
    }

    /**
     * Returns an object containing all data to display the user's full name and picture
     *
     * @param int $id optional user id, defaults to the current user
     * @return stdClass containing properties lastname, firstname, picture and imagealt
     */
    public function user_info($id=null) {
        global $USER, $DB;

        if (is_null($id) || ($id == $USER->id)) {
            return $USER;
        } else {
            return $DB->get_record('user', array('id' => $id), 'id,lastname,firstname,picture,imagealt', MUST_EXIST);
        }
    }

    /**
     * Are users alloed to create/edit their submissions?
     *
     * TODO: this depends on the workshop phase, phase deadlines, submitting after deadlines possibility
     *
     * @return bool
     */
    public function submitting_allowed() {
        return true;
    }

}
