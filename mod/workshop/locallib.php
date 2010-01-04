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

require_once(dirname(__FILE__).'/lib.php');      // we extend this library here

/**
 * Full-featured workshop API
 *
 * This wraps the workshop database record with a set of methods that are called
 * from the module itself. The class should be initialized right after you get
 * $workshop, $cm and $course records at the begining of the script.
 */
class workshop {

    /** return statuses of {@link add_allocation} to be passed to a workshop renderer method */
    const ALLOCATION_EXISTS = -1;
    const ALLOCATION_ERROR  = -2;

    /** the internal code of the workshop phases as are stored in the database */
    const PHASE_SETUP       = 10;
    const PHASE_SUBMISSION  = 20;
    const PHASE_ASSESSMENT  = 30;
    const PHASE_EVALUATION  = 40;
    const PHASE_CLOSED      = 50;

    /** @var stdClass course module record */
    public $cm = null;

    /** @var stdClass course record */
    public $course = null;

    /** @var stdClass the workshop instance context */
    public $context = null;

    /**
     * @var workshop_strategy grading strategy instance
     * Do not use directly, get the instance using {@link workshop::grading_strategy_instance()}
     */
    protected $strategyinstance = null;

    /** @var stdClass underlying database record */
    protected $dbrecord = null;

    /**
     * Initializes the workshop API instance using the data from DB
     *
     * Makes deep copy of all passed records properties. Replaces integer $course attribute
     * with a full database record (course should not be stored in instances table anyway).
     *
     * @param stdClass $dbrecord Workshop instance data from {workshop} table
     * @param stdClass $cm       Course module record as returned by {@link get_coursemodule_from_id()}
     * @param stdClass $course   Course record from {course} table
     */
    public function __construct(stdClass $dbrecord, stdClass $cm, stdClass $course) {
        $this->dbrecord = $dbrecord;
        $this->cm       = $cm;
        $this->course   = $course;
        $this->context  = get_context_instance(CONTEXT_MODULE, $this->cm->id);
    }

    /**
     * Magic method to retrieve the value of the underlying database record's field
     *
     * @throws coding_exception if the field does not exist
     * @param mixed $key the name of the database field
     * @return mixed|null the value of the field
     */
    public function __get($key) {
        if (!isset($this->dbrecord->{$key})) {
            // todo remove the comment here // throw new coding_exception('You are trying to get a non-existing property');
            return null;
        }
        return $this->dbrecord->{$key};
    }

    /**
     * Given a list of user ids, returns the filtered one containing just ids of users with own submission
     *
     * Example submissions are ignored.
     *
     * @param array $userids 
     * @return TODO
     */
    protected function users_with_submission(array $userids) {
        global $DB;

        $userswithsubmission = array();
        list($usql, $uparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $sql = "SELECT id,userid
                  FROM {workshop_submissions}
                 WHERE example = 0 AND workshopid = :workshopid AND userid $usql";
        $params = array('workshopid' => $this->id);
        $params = array_merge($params, $uparams);
        $submissions = $DB->get_records_sql($sql, $params);
        foreach ($submissions as $submission) {
            $userswithsubmission[$submission->userid] = null;
        }

        return $userswithsubmission;
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

        $users = get_users_by_capability($this->context, 'mod/workshop:submit',
                    'u.id, u.lastname, u.firstname', 'u.lastname,u.firstname', '', '', '', '', false, false, true);

        if ($musthavesubmission) {
            $users = array_intersect_key($users, $this->users_with_submission(array_keys($users)));
        }

        return $users;
    }

    /**
     * Fetches all users with the capability mod/workshop:peerassess in the current context
     *
     * The returned objects contain id, lastname and firstname properties and are ordered by lastname,firstname
     *
     * @param bool $musthavesubmission If true, return only users who have already submitted. All possible users otherwise.
     * @return array array[userid] => stdClass{->id ->lastname ->firstname}
     */
    public function get_peer_reviewers($musthavesubmission=false) {
        global $DB;

        $users = get_users_by_capability($this->context, 'mod/workshop:peerassess',
                    'u.id, u.lastname, u.firstname', 'u.lastname,u.firstname', '', '', '', '', false, false, true);

        if ($musthavesubmission) {
            // users without their own submission can not be reviewers
            $users = array_intersect_key($users, $this->users_with_submission(array_keys($users)));
        }

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

        if ('all' === $examples) {
            // no additional conditions
        } elseif ($examples === true) {
            $sql .= ' AND example = 1';
        } elseif ($examples === false) {
            $sql .= ' AND example = 0';
        } else {
            throw new coding_exception('Illegal parameter value: $examples may be false|true|"all"');
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

        $users = get_users_by_capability($this->context, array('mod/workshop:submit', 'mod/workshop:peerassess'),
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
            return self::ALLOCATION_EXISTS;
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
                throw new coding_exception('the grading subplugin must contain library ' . $strategylib);
            }
            $classname = 'workshop_' . $this->strategy . '_strategy';
            $this->strategyinstance = new $classname($this);
            if (!in_array('workshop_strategy', class_implements($this->strategyinstance))) {
                throw new coding_exception($classname . ' does not implement workshop_strategy interface');
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
        $installed = get_plugin_list('workshopallocation');
        $forms = array();
        foreach ($installed as $allocation => $allocationpath) {
            if (file_exists($allocationpath . '/allocator.php')) {
                $forms[$allocation] = get_string('pluginname', 'workshopallocation_' . $allocation);
            }
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
     * @return stdClass {@link moodle_url} the URL of the page to view own submission
     */
    public function submission_url() {
        global $CFG;
        return new moodle_url($CFG->wwwroot . '/mod/workshop/submission.php', array('cmid' => $this->cm->id));
    }

    /**
     * @return stdClass {@link moodle_url} the URL of the mod_edit form
     */
    public function updatemod_url() {
        global $CFG;
        return new moodle_url($CFG->wwwroot . '/course/modedit.php', array('update' => $this->cm->id, 'return' => 1));
    }

    public function allocation_url() {
        global $CFG;
        return new moodle_url($CFG->wwwroot . '/mod/workshop/allocation.php', array('cmid' => $this->cm->id));
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
     * Are users allowed to create/edit their submissions?
     *
     * TODO: this depends on the workshop phase, phase deadlines, submitting after deadlines possibility
     *
     * @return bool
     */
    public function submitting_allowed() {
        return true;
    }

    /**
     * Returns the localized name of the grading strategy method to be displayed to the users
     *
     * @return string
     */
    public function strategy_name() {
        return get_string('pluginname', 'workshopgrading_' . $this->strategy);
    }

    /**
     * Prepare an individual workshop plan for the given user.
     *
     * @param mixed $userid 
     * @return TODO
     */
    public function prepare_user_plan($userid) {
        global $DB;

        $phases = array();

        // Prepare tasks for the setup phase
        $phase = new stdClass();
        $phase->title = get_string('phasesetup', 'workshop');
        $phase->tasks = array();
        if (has_capability('moodle/course:manageactivities', $this->context, $userid)) {
            $task = new stdClass();
            $task->title = get_string('taskintro', 'workshop');
            $task->link = $this->updatemod_url();
            $task->completed = !(trim(strip_tags($this->intro)) == '');
            $phase->tasks['intro'] = $task;
        }
        if (has_capability('mod/workshop:editdimensions', $this->context, $userid)) {
            $task = new stdClass();
            $task->title = get_string('editassessmentform', 'workshop');
            $task->link = $this->editform_url();
            if ($this->assessment_form_ready()) {
                $task->completed = true;
            } elseif ($this->phase > self::PHASE_SETUP) {
                $task->completed = false;
            }
            $phase->tasks['editform'] = $task;
        }
        if (has_capability('moodle/course:manageactivities', $this->context, $userid)) {
            $task = new stdClass();
            $task->title = get_string('taskinstructauthors', 'workshop');
            $task->link = $this->updatemod_url();
            if (trim(strip_tags($this->instructauthors))) {
                $task->completed = true;
            } elseif ($this->phase >= self::PHASE_SUBMISSION) {
                $task->completed = false;
            }
            $phase->tasks['instructauthors'] = $task;
        }
        if (empty($phase->tasks) and $this->phase == self::PHASE_SETUP) {
            // if we are in the setup phase and there is no task (typical for students), let us
            // display some explanation what is going on
            $task = new stdClass();
            $task->title = get_string('undersetup', 'workshop');
            $task->completed = 'info';
            $phase->tasks['setupinfo'] = $task;
        }
        $phases[self::PHASE_SETUP] = $phase;

        // Prepare tasks for the submission phase
        $phase = new stdClass();
        $phase->title = get_string('phasesubmission', 'workshop');
        $phase->tasks = array();
        if (has_capability('mod/workshop:submit', $this->context, $userid)) {
            $task = new stdClass();
            $task->title = get_string('tasksubmit', 'workshop');
            $task->link = $this->submission_url();
            if ($DB->record_exists('workshop_submissions', array('workshopid'=>$this->id, 'example'=>0, 'userid'=>$userid))) {
                $task->completed = true;
            } elseif ($this->phase >= self::PHASE_ASSESSMENT) {
                $task->completed = false;
            } else {
                $task->completed = null;    // still has a chance to submit
            }
            $phase->tasks['submit'] = $task;
        }
        if (has_capability('moodle/course:manageactivities', $this->context, $userid)) {
            $task = new stdClass();
            $task->title = get_string('taskinstructreviewers', 'workshop');
            $task->link = $this->updatemod_url();
            if (trim(strip_tags($this->instructreviewers))) {
                $task->completed = true;
            } elseif ($this->phase >= self::PHASE_ASSESSMENT) {
                $task->completed = false;
            }
            $phase->tasks['instructreviewers'] = $task;
        }
        $phases[self::PHASE_SUBMISSION] = $phase;
        if (has_capability('mod/workshop:allocate', $this->context, $userid)) {
            $task = new stdClass();
            $task->title = get_string('allocate', 'workshop');
            $task->link = $this->allocation_url();
            $rs = $this->get_allocations_recordset();
            $allocations = array(); // 'submissionid' => isallocated
            foreach ($rs as $allocation) {
                if (!isset($allocations[$allocation->submissionid])) {
                    $allocations[$allocation->submissionid] = false;
                }
                if (!empty($allocation->reviewerid)) {
                    $allocations[$allocation->submissionid] = true;
                }
            }
            $numofsubmissions = count($allocations);
            $numofallocated   = count(array_filter($allocations));
            $rs->close();
            if ($numofsubmissions == 0) {
                $task->completed = null;
            } elseif ($numofsubmissions == $numofallocated) {
                $task->completed = true;
            } elseif ($this->phase > self::PHASE_SUBMISSION) {
                $task->completed = false;
            } else {
                $task->completed = null;    // still has a chance to allocate
            }
            $a = new stdClass();
            $a->total = $numofsubmissions;
            $a->done  = $numofallocated;
            $task->details = get_string('allocatedetails', 'workshop', $a);
            unset($a);
            $phase->tasks['submit'] = $task;
        }

        // Prepare tasks for the peer-assessment phase (includes eventual self-assessments)
        $phase = new stdClass();
        $phase->title = get_string('phaseassessment', 'workshop');
        $phase->tasks = array();
        $phase->isreviewer = has_capability('mod/workshop:peerassess', $this->context, $userid);
        $phase->assessments = $this->get_assessments($userid); // todo make sure this does not contain assessment of examples
        $numofpeers     = 0;    // number of allocated peer-assessments
        $numofpeerstodo = 0;    // number of peer-assessments to do
        $numofself      = 0;    // number of allocated self-assessments - should be 0 or 1
        $numofselftodo  = 0;    // number of self-assessments to do - should be 0 or 1
        foreach ($phase->assessments as $a) {
            if ($a->authorid == $userid) {
                $numofself++;
                if (is_null($a->grade)) {
                    $numofselftodo++;
                }
            } else {
                $numofpeers++;
                if (is_null($a->grade)) {
                    $numofpeerstodo++;
                }
            }
        }
        unset($a);
        if ($numofpeers) {
            $task = new stdClass();
            $task->completed = ($numofpeerstodo == 0);
            $a = new stdClass();
            $a->total = $numofpeers;
            $a->todo  = $numofpeerstodo;
            $task->title = get_string('taskassesspeers', 'workshop');
            $task->details = get_string('taskassesspeersdetails', 'workshop', $a);
            unset($a);
            $phase->tasks['assesspeers'] = $task;
        }
        if ($numofself) {
            $task = new stdClass();
            $task->completed = ($numofselftodo == 0);
            $task->title = get_string('taskassessself', 'workshop');
            $phase->tasks['assessself'] = $task;
        }
        $phases[self::PHASE_ASSESSMENT] = $phase;

        // Prepare tasks for the grading evaluation phase - todo
        $phase = new stdClass();
        $phase->title = get_string('phaseevaluation', 'workshop');
        $phase->tasks = array();
        $phases[self::PHASE_EVALUATION] = $phase;

        // Prepare tasks for the "workshop closed" phase - todo
        $phase = new stdClass();
        $phase->title = get_string('phaseclosed', 'workshop');
        $phase->tasks = array();
        $phases[self::PHASE_CLOSED] = $phase;

        // Polish data, set default values if not done explicitly
        foreach ($phases as $phasecode => $phase) {
            $phase->title       = isset($phase->title)      ? $phase->title     : '';
            $phase->tasks       = isset($phase->tasks)      ? $phase->tasks     : array();
            if ($phasecode == $this->phase) {
                $phase->active = true;
            } else {
                $phase->active = false;
            }

            foreach ($phase->tasks as $taskcode => $task) {
                $task->title        = isset($task->title)       ? $task->title      : '';
                $task->link         = isset($task->link)        ? $task->link       : null;
                $task->details      = isset($task->details)     ? $task->details    : '';
                $task->completed    = isset($task->completed)   ? $task->completed  : null;
            }
        }
        return $phases;
    }

    /**
     * Has the assessment form been defined?
     *
     * @return bool
     */
    public function assessment_form_ready() {
        return $this->grading_strategy_instance()->form_ready();
    }

}
