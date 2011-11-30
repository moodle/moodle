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
 * @package    mod
 * @subpackage workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/lib.php');     // we extend this library here
require_once($CFG->libdir . '/gradelib.php');   // we use some rounding and comparing routines here
require_once($CFG->libdir . '/filelib.php');

/**
 * Full-featured workshop API
 *
 * This wraps the workshop database record with a set of methods that are called
 * from the module itself. The class should be initialized right after you get
 * $workshop, $cm and $course records at the begining of the script.
 */
class workshop {

    /** return statuses of {@link add_allocation} to be passed to a workshop renderer method */
    const ALLOCATION_EXISTS             = -9999;
    const ALLOCATION_ERROR              = -9998;

    /** the internal code of the workshop phases as are stored in the database */
    const PHASE_SETUP                   = 10;
    const PHASE_SUBMISSION              = 20;
    const PHASE_ASSESSMENT              = 30;
    const PHASE_EVALUATION              = 40;
    const PHASE_CLOSED                  = 50;

    /** the internal code of the examples modes as are stored in the database */
    const EXAMPLES_VOLUNTARY            = 0;
    const EXAMPLES_BEFORE_SUBMISSION    = 1;
    const EXAMPLES_BEFORE_ASSESSMENT    = 2;

    /** @var stdclass course module record */
    public $cm;

    /** @var stdclass course record */
    public $course;

    /** @var stdclass context object */
    public $context;

    /** @var int workshop instance identifier */
    public $id;

    /** @var string workshop activity name */
    public $name;

    /** @var string introduction or description of the activity */
    public $intro;

    /** @var int format of the {@link $intro} */
    public $introformat;

    /** @var string instructions for the submission phase */
    public $instructauthors;

    /** @var int format of the {@link $instructauthors} */
    public $instructauthorsformat;

    /** @var string instructions for the assessment phase */
    public $instructreviewers;

    /** @var int format of the {@link $instructreviewers} */
    public $instructreviewersformat;

    /** @var int timestamp of when the module was modified */
    public $timemodified;

    /** @var int current phase of workshop, for example {@link workshop::PHASE_SETUP} */
    public $phase;

    /** @var bool optional feature: students practise evaluating on example submissions from teacher */
    public $useexamples;

    /** @var bool optional feature: students perform peer assessment of others' work */
    public $usepeerassessment;

    /** @var bool optional feature: students perform self assessment of their own work */
    public $useselfassessment;

    /** @var float number (10, 5) unsigned, the maximum grade for submission */
    public $grade;

    /** @var float number (10, 5) unsigned, the maximum grade for assessment */
    public $gradinggrade;

    /** @var string type of the current grading strategy used in this workshop, for example 'accumulative' */
    public $strategy;

    /** @var string the name of the evaluation plugin to use for grading grades calculation */
    public $evaluation;

    /** @var int number of digits that should be shown after the decimal point when displaying grades */
    public $gradedecimals;

    /** @var int number of allowed submission attachments and the files embedded into submission */
    public $nattachments;

    /** @var bool allow submitting the work after the deadline */
    public $latesubmissions;

    /** @var int maximum size of the one attached file in bytes */
    public $maxbytes;

    /** @var int mode of example submissions support, for example {@link workshop::EXAMPLES_VOLUNTARY} */
    public $examplesmode;

    /** @var int if greater than 0 then the submission is not allowed before this timestamp */
    public $submissionstart;

    /** @var int if greater than 0 then the submission is not allowed after this timestamp */
    public $submissionend;

    /** @var int if greater than 0 then the peer assessment is not allowed before this timestamp */
    public $assessmentstart;

    /** @var int if greater than 0 then the peer assessment is not allowed after this timestamp */
    public $assessmentend;

    /**
     * @var workshop_strategy grading strategy instance
     * Do not use directly, get the instance using {@link workshop::grading_strategy_instance()}
     */
    protected $strategyinstance = null;

    /**
     * @var workshop_evaluation grading evaluation instance
     * Do not use directly, get the instance using {@link workshop::grading_evaluation_instance()}
     */
    protected $evaluationinstance = null;

    /**
     * Initializes the workshop API instance using the data from DB
     *
     * Makes deep copy of all passed records properties. Replaces integer $course attribute
     * with a full database record (course should not be stored in instances table anyway).
     *
     * @param stdClass $dbrecord Workshop instance data from {workshop} table
     * @param stdClass $cm       Course module record as returned by {@link get_coursemodule_from_id()}
     * @param stdClass $course   Course record from {course} table
     * @param stdClass $context  The context of the workshop instance
     */
    public function __construct(stdclass $dbrecord, stdclass $cm, stdclass $course, stdclass $context=null) {
        foreach ($dbrecord as $field => $value) {
            if (property_exists('workshop', $field)) {
                $this->{$field} = $value;
            }
        }
        $this->cm           = $cm;
        $this->course       = $course;
        if (is_null($context)) {
            $this->context = get_context_instance(CONTEXT_MODULE, $this->cm->id);
        } else {
            $this->context = $context;
        }
        $this->evaluation   = 'best';   // todo make this configurable although we have no alternatives yet
    }

    ////////////////////////////////////////////////////////////////////////////////
    // Static methods                                                             //
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * Return list of available allocation methods
     *
     * @return array Array ['string' => 'string'] of localized allocation method names
     */
    public static function installed_allocators() {
        $installed = get_plugin_list('workshopallocation');
        $forms = array();
        foreach ($installed as $allocation => $allocationpath) {
            if (file_exists($allocationpath . '/lib.php')) {
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
     * Returns an array of options for the editors that are used for submitting and assessing instructions
     *
     * @param stdClass $context
     * @uses EDITOR_UNLIMITED_FILES hard-coded value for the 'maxfiles' option
     * @return array
     */
    public static function instruction_editors_options(stdclass $context) {
        return array('subdirs' => 1, 'maxbytes' => 0, 'maxfiles' => -1,
                     'changeformat' => 1, 'context' => $context, 'noclean' => 1, 'trusttext' => 0);
    }

    /**
     * Given the percent and the total, returns the number
     *
     * @param float $percent from 0 to 100
     * @param float $total   the 100% value
     * @return float
     */
    public static function percent_to_value($percent, $total) {
        if ($percent < 0 or $percent > 100) {
            throw new coding_exception('The percent can not be less than 0 or higher than 100');
        }

        return $total * $percent / 100;
    }

    /**
     * Returns an array of numeric values that can be used as maximum grades
     *
     * @return array Array of integers
     */
    public static function available_maxgrades_list() {
        $grades = array();
        for ($i=100; $i>=0; $i--) {
            $grades[$i] = $i;
        }
        return $grades;
    }

    /**
     * Returns the localized list of supported examples modes
     *
     * @return array
     */
    public static function available_example_modes_list() {
        $options = array();
        $options[self::EXAMPLES_VOLUNTARY]         = get_string('examplesvoluntary', 'workshop');
        $options[self::EXAMPLES_BEFORE_SUBMISSION] = get_string('examplesbeforesubmission', 'workshop');
        $options[self::EXAMPLES_BEFORE_ASSESSMENT] = get_string('examplesbeforeassessment', 'workshop');
        return $options;
    }

    /**
     * Returns the list of available grading strategy methods
     *
     * @return array ['string' => 'string']
     */
    public static function available_strategies_list() {
        $installed = get_plugin_list('workshopform');
        $forms = array();
        foreach ($installed as $strategy => $strategypath) {
            if (file_exists($strategypath . '/lib.php')) {
                $forms[$strategy] = get_string('pluginname', 'workshopform_' . $strategy);
            }
        }
        return $forms;
    }

    /**
     * Return an array of possible values of assessment dimension weight
     *
     * @return array of integers 0, 1, 2, ..., 16
     */
    public static function available_dimension_weights_list() {
        $weights = array();
        for ($i=16; $i>=0; $i--) {
            $weights[$i] = $i;
        }
        return $weights;
    }

    /**
     * Return an array of possible values of assessment weight
     *
     * Note there is no real reason why the maximum value here is 16. It used to be 10 in
     * workshop 1.x and I just decided to use the same number as in the maximum weight of
     * a single assessment dimension.
     * The value looks reasonable, though. Teachers who would want to assign themselves
     * higher weight probably do not want peer assessment really...
     *
     * @return array of integers 0, 1, 2, ..., 16
     */
    public static function available_assessment_weights_list() {
        $weights = array();
        for ($i=16; $i>=0; $i--) {
            $weights[$i] = $i;
        }
        return $weights;
    }

    /**
     * Helper function returning the greatest common divisor
     *
     * @param int $a
     * @param int $b
     * @return int
     */
    public static function gcd($a, $b) {
        return ($b == 0) ? ($a):(self::gcd($b, $a % $b));
    }

    /**
     * Helper function returning the least common multiple
     *
     * @param int $a
     * @param int $b
     * @return int
     */
    public static function lcm($a, $b) {
        return ($a / self::gcd($a,$b)) * $b;
    }

    /**
     * Returns an object suitable for strings containing dates/times
     *
     * The returned object contains properties date, datefullshort, datetime, ... containing the given
     * timestamp formatted using strftimedate, strftimedatefullshort, strftimedatetime, ... from the
     * current lang's langconfig.php
     * This allows translators and administrators customize the date/time format.
     *
     * @param int $timestamp the timestamp in UTC
     * @return stdclass
     */
    public static function timestamp_formats($timestamp) {
        $formats = array('date', 'datefullshort', 'dateshort', 'datetime',
                'datetimeshort', 'daydate', 'daydatetime', 'dayshort', 'daytime',
                'monthyear', 'recent', 'recentfull', 'time');
        $a = new stdclass();
        foreach ($formats as $format) {
            $a->{$format} = userdate($timestamp, get_string('strftime'.$format, 'langconfig'));
        }
        $day = userdate($timestamp, '%Y%m%d', 99, false);
        $today = userdate(time(), '%Y%m%d', 99, false);
        $tomorrow = userdate(time() + DAYSECS, '%Y%m%d', 99, false);
        $yesterday = userdate(time() - DAYSECS, '%Y%m%d', 99, false);
        $distance = (int)round(abs(time() - $timestamp) / DAYSECS);
        if ($day == $today) {
            $a->distanceday = get_string('daystoday', 'workshop');
        } elseif ($day == $yesterday) {
            $a->distanceday = get_string('daysyesterday', 'workshop');
        } elseif ($day < $today) {
            $a->distanceday = get_string('daysago', 'workshop', $distance);
        } elseif ($day == $tomorrow) {
            $a->distanceday = get_string('daystomorrow', 'workshop');
        } elseif ($day > $today) {
            $a->distanceday = get_string('daysleft', 'workshop', $distance);
        }
        return $a;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // Workshop API                                                               //
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * Fetches all users with the capability mod/workshop:submit in the current context
     *
     * The returned objects contain id, lastname and firstname properties and are ordered by lastname,firstname
     *
     * @todo handle with limits and groups
     * @param bool $musthavesubmission If true, return only users who have already submitted. All possible authors otherwise.
     * @return array array[userid] => stdclass{->id ->lastname ->firstname}
     */
    public function get_potential_authors($musthavesubmission=true) {
        $users = get_users_by_capability($this->context, 'mod/workshop:submit',
                    'u.id,u.lastname,u.firstname', 'u.lastname,u.firstname,u.id', '', '', '', '', false, false, true);
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
     * @todo handle with limits and groups
     * @param bool $musthavesubmission If true, return only users who have already submitted. All possible users otherwise.
     * @return array array[userid] => stdclass{->id ->lastname ->firstname}
     */
    public function get_potential_reviewers($musthavesubmission=false) {
        $users = get_users_by_capability($this->context, 'mod/workshop:peerassess',
                    'u.id, u.lastname, u.firstname', 'u.lastname,u.firstname,u.id', '', '', '', '', false, false, true);
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
     * @param array $users array[userid] => stdclass{->id ->lastname ->firstname}
     * @return array array[groupid][userid] => stdclass{->id ->lastname ->firstname}
     */
    public function get_grouped($users) {
        global $DB;
        global $CFG;

        $grouped = array();  // grouped users to be returned
        if (empty($users)) {
            return $grouped;
        }
        if (!empty($CFG->enablegroupmembersonly) and $this->cm->groupmembersonly) {
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
     * Returns the list of all allocations (i.e. assigned assessments) in the workshop
     *
     * Assessments of example submissions are ignored
     *
     * @return array
     */
    public function get_allocations() {
        global $DB;

        $sql = 'SELECT a.id, a.submissionid, a.reviewerid, s.authorid
                  FROM {workshop_assessments} a
            INNER JOIN {workshop_submissions} s ON (a.submissionid = s.id)
                 WHERE s.example = 0 AND s.workshopid = :workshopid';
        $params = array('workshopid' => $this->id);

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Returns submissions from this workshop
     *
     * Fetches data from {workshop_submissions} and adds some useful information from other
     * tables. Does not return textual fields to prevent possible memory lack issues.
     *
     * @param mixed $authorid int|array|'all' If set to [array of] integer, return submission[s] of the given user[s] only
     * @return array of records or an empty array
     */
    public function get_submissions($authorid='all') {
        global $DB;

        $authorfields      = user_picture::fields('u', null, 'authoridx', 'author');
        $gradeoverbyfields = user_picture::fields('t', null, 'gradeoverbyx', 'over');
        $sql = "SELECT s.id, s.workshopid, s.example, s.authorid, s.timecreated, s.timemodified,
                       s.title, s.grade, s.gradeover, s.gradeoverby, s.published,
                       $authorfields, $gradeoverbyfields
                  FROM {workshop_submissions} s
            INNER JOIN {user} u ON (s.authorid = u.id)
             LEFT JOIN {user} t ON (s.gradeoverby = t.id)
                 WHERE s.example = 0 AND s.workshopid = :workshopid";
        $params = array('workshopid' => $this->id);

        if ('all' === $authorid) {
            // no additional conditions
        } elseif (!empty($authorid)) {
            list($usql, $uparams) = $DB->get_in_or_equal($authorid, SQL_PARAMS_NAMED);
            $sql .= " AND authorid $usql";
            $params = array_merge($params, $uparams);
        } else {
            // $authorid is empty
            return array();
        }
        $sql .= " ORDER BY u.lastname, u.firstname";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Returns a submission record with the author's data
     *
     * @param int $id submission id
     * @return stdclass
     */
    public function get_submission_by_id($id) {
        global $DB;

        // we intentionally check the workshopid here, too, so the workshop can't touch submissions
        // from other instances
        $authorfields      = user_picture::fields('u', null, 'authoridx', 'author');
        $gradeoverbyfields = user_picture::fields('g', null, 'gradeoverbyx', 'gradeoverby');
        $sql = "SELECT s.*, $authorfields, $gradeoverbyfields
                  FROM {workshop_submissions} s
            INNER JOIN {user} u ON (s.authorid = u.id)
             LEFT JOIN {user} g ON (s.gradeoverby = g.id)
                 WHERE s.example = 0 AND s.workshopid = :workshopid AND s.id = :id";
        $params = array('workshopid' => $this->id, 'id' => $id);
        return $DB->get_record_sql($sql, $params, MUST_EXIST);
    }

    /**
     * Returns a submission submitted by the given author
     *
     * @param int $id author id
     * @return stdclass|false
     */
    public function get_submission_by_author($authorid) {
        global $DB;

        if (empty($authorid)) {
            return false;
        }
        $authorfields      = user_picture::fields('u', null, 'authoridx', 'author');
        $gradeoverbyfields = user_picture::fields('g', null, 'gradeoverbyx', 'gradeoverby');
        $sql = "SELECT s.*, $authorfields, $gradeoverbyfields
                  FROM {workshop_submissions} s
            INNER JOIN {user} u ON (s.authorid = u.id)
             LEFT JOIN {user} g ON (s.gradeoverby = g.id)
                 WHERE s.example = 0 AND s.workshopid = :workshopid AND s.authorid = :authorid";
        $params = array('workshopid' => $this->id, 'authorid' => $authorid);
        return $DB->get_record_sql($sql, $params);
    }

    /**
     * Returns published submissions with their authors data
     *
     * @return array of stdclass
     */
    public function get_published_submissions($orderby='finalgrade DESC') {
        global $DB;

        $authorfields = user_picture::fields('u', null, 'authoridx', 'author');
        $sql = "SELECT s.id, s.authorid, s.timecreated, s.timemodified,
                       s.title, s.grade, s.gradeover, COALESCE(s.gradeover,s.grade) AS finalgrade,
                       $authorfields
                  FROM {workshop_submissions} s
            INNER JOIN {user} u ON (s.authorid = u.id)
                 WHERE s.example = 0 AND s.workshopid = :workshopid AND s.published = 1
              ORDER BY $orderby";
        $params = array('workshopid' => $this->id);
        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Returns full record of the given example submission
     *
     * @param int $id example submission od
     * @return object
     */
    public function get_example_by_id($id) {
        global $DB;
        return $DB->get_record('workshop_submissions',
                array('id' => $id, 'workshopid' => $this->id, 'example' => 1), '*', MUST_EXIST);
    }

    /**
     * Returns the list of example submissions in this workshop with reference assessments attached
     *
     * @return array of objects or an empty array
     * @see workshop::prepare_example_summary()
     */
    public function get_examples_for_manager() {
        global $DB;

        $sql = 'SELECT s.id, s.title,
                       a.id AS assessmentid, a.grade, a.gradinggrade
                  FROM {workshop_submissions} s
             LEFT JOIN {workshop_assessments} a ON (a.submissionid = s.id AND a.weight = 1)
                 WHERE s.example = 1 AND s.workshopid = :workshopid
              ORDER BY s.title';
        return $DB->get_records_sql($sql, array('workshopid' => $this->id));
    }

    /**
     * Returns the list of all example submissions in this workshop with the information of assessments done by the given user
     *
     * @param int $reviewerid user id
     * @return array of objects, indexed by example submission id
     * @see workshop::prepare_example_summary()
     */
    public function get_examples_for_reviewer($reviewerid) {
        global $DB;

        if (empty($reviewerid)) {
            return false;
        }
        $sql = 'SELECT s.id, s.title,
                       a.id AS assessmentid, a.grade, a.gradinggrade
                  FROM {workshop_submissions} s
             LEFT JOIN {workshop_assessments} a ON (a.submissionid = s.id AND a.reviewerid = :reviewerid AND a.weight = 0)
                 WHERE s.example = 1 AND s.workshopid = :workshopid
              ORDER BY s.title';
        return $DB->get_records_sql($sql, array('workshopid' => $this->id, 'reviewerid' => $reviewerid));
    }

    /**
     * Prepares renderable submission component
     *
     * @param stdClass $record required by {@see workshop_submission}
     * @param bool $showauthor show the author-related information
     * @return workshop_submission
     */
    public function prepare_submission(stdClass $record, $showauthor = false) {

        $submission         = new workshop_submission($record, $showauthor);
        $submission->url    = $this->submission_url($record->id);

        return $submission;
    }

    /**
     * Prepares renderable submission summary component
     *
     * @param stdClass $record required by {@see workshop_submission_summary}
     * @param bool $showauthor show the author-related information
     * @return workshop_submission_summary
     */
    public function prepare_submission_summary(stdClass $record, $showauthor = false) {

        $summary        = new workshop_submission_summary($record, $showauthor);
        $summary->url   = $this->submission_url($record->id);

        return $summary;
    }

    /**
     * Prepares renderable example submission component
     *
     * @param stdClass $record required by {@see workshop_example_submission}
     * @return workshop_example_submission
     */
    public function prepare_example_submission(stdClass $record) {

        $example = new workshop_example_submission($record);

        return $example;
    }

    /**
     * Prepares renderable example submission summary component
     *
     * If the example is editable, the caller must set the 'editable' flag explicitly.
     *
     * @param stdClass $example as returned by {@link workshop::get_examples_for_manager()} or {@link workshop::get_examples_for_reviewer()}
     * @return workshop_example_submission_summary to be rendered
     */
    public function prepare_example_summary(stdClass $example) {

        $summary = new workshop_example_submission_summary($example);

        if (is_null($example->grade)) {
            $summary->status = 'notgraded';
            $summary->assesslabel = get_string('assess', 'workshop');
        } else {
            $summary->status = 'graded';
            $summary->assesslabel = get_string('reassess', 'workshop');
        }

        $summary->gradeinfo           = new stdclass();
        $summary->gradeinfo->received = $this->real_grade($example->grade);
        $summary->gradeinfo->max      = $this->real_grade(100);

        $summary->url       = new moodle_url($this->exsubmission_url($example->id));
        $summary->editurl   = new moodle_url($this->exsubmission_url($example->id), array('edit' => 'on'));
        $summary->assessurl = new moodle_url($this->exsubmission_url($example->id), array('assess' => 'on', 'sesskey' => sesskey()));

        return $summary;
    }

    /**
     * Prepares renderable assessment component
     *
     * The $options array supports the following keys:
     * showauthor - should the author user info be available for the renderer
     * showreviewer - should the reviewer user info be available for the renderer
     * showform - show the assessment form if it is available
     * showweight - should the assessment weight be available for the renderer
     *
     * @param stdClass $record as returned by eg {@link self::get_assessment_by_id()}
     * @param workshop_assessment_form|null $form as returned by {@link workshop_strategy::get_assessment_form()}
     * @param array $options
     * @return workshop_assessment
     */
    public function prepare_assessment(stdClass $record, $form, array $options = array()) {

        $assessment             = new workshop_assessment($record, $options);
        $assessment->url        = $this->assess_url($record->id);
        $assessment->maxgrade   = $this->real_grade(100);

        if (!empty($options['showform']) and !($form instanceof workshop_assessment_form)) {
            debugging('Not a valid instance of workshop_assessment_form supplied', DEBUG_DEVELOPER);
        }

        if (!empty($options['showform']) and ($form instanceof workshop_assessment_form)) {
            $assessment->form = $form;
        }

        if (empty($options['showweight'])) {
            $assessment->weight = null;
        }

        if (!is_null($record->grade)) {
            $assessment->realgrade = $this->real_grade($record->grade);
        }

        return $assessment;
    }

    /**
     * Prepares renderable example submission's assessment component
     *
     * The $options array supports the following keys:
     * showauthor - should the author user info be available for the renderer
     * showreviewer - should the reviewer user info be available for the renderer
     * showform - show the assessment form if it is available
     *
     * @param stdClass $record as returned by eg {@link self::get_assessment_by_id()}
     * @param workshop_assessment_form|null $form as returned by {@link workshop_strategy::get_assessment_form()}
     * @param array $options
     * @return workshop_example_assessment
     */
    public function prepare_example_assessment(stdClass $record, $form = null, array $options = array()) {

        $assessment             = new workshop_example_assessment($record, $options);
        $assessment->url        = $this->exassess_url($record->id);
        $assessment->maxgrade   = $this->real_grade(100);

        if (!empty($options['showform']) and !($form instanceof workshop_assessment_form)) {
            debugging('Not a valid instance of workshop_assessment_form supplied', DEBUG_DEVELOPER);
        }

        if (!empty($options['showform']) and ($form instanceof workshop_assessment_form)) {
            $assessment->form = $form;
        }

        if (!is_null($record->grade)) {
            $assessment->realgrade = $this->real_grade($record->grade);
        }

        $assessment->weight = null;

        return $assessment;
    }

    /**
     * Prepares renderable example submission's reference assessment component
     *
     * The $options array supports the following keys:
     * showauthor - should the author user info be available for the renderer
     * showreviewer - should the reviewer user info be available for the renderer
     * showform - show the assessment form if it is available
     *
     * @param stdClass $record as returned by eg {@link self::get_assessment_by_id()}
     * @param workshop_assessment_form|null $form as returned by {@link workshop_strategy::get_assessment_form()}
     * @param array $options
     * @return workshop_example_reference_assessment
     */
    public function prepare_example_reference_assessment(stdClass $record, $form = null, array $options = array()) {

        $assessment             = new workshop_example_reference_assessment($record, $options);
        $assessment->maxgrade   = $this->real_grade(100);

        if (!empty($options['showform']) and !($form instanceof workshop_assessment_form)) {
            debugging('Not a valid instance of workshop_assessment_form supplied', DEBUG_DEVELOPER);
        }

        if (!empty($options['showform']) and ($form instanceof workshop_assessment_form)) {
            $assessment->form = $form;
        }

        if (!is_null($record->grade)) {
            $assessment->realgrade = $this->real_grade($record->grade);
        }

        $assessment->weight = null;

        return $assessment;
    }

    /**
     * Removes the submission and all relevant data
     *
     * @param stdClass $submission record to delete
     * @return void
     */
    public function delete_submission(stdclass $submission) {
        global $DB;
        $assessments = $DB->get_records('workshop_assessments', array('submissionid' => $submission->id), '', 'id');
        $this->delete_assessment(array_keys($assessments));
        $DB->delete_records('workshop_submissions', array('id' => $submission->id));
    }

    /**
     * Returns the list of all assessments in the workshop with some data added
     *
     * Fetches data from {workshop_assessments} and adds some useful information from other
     * tables. The returned object does not contain textual fields (i.e. comments) to prevent memory
     * lack issues.
     *
     * @return array [assessmentid] => assessment stdclass
     */
    public function get_all_assessments() {
        global $DB;

        $reviewerfields = user_picture::fields('reviewer', null, 'revieweridx', 'reviewer');
        $authorfields   = user_picture::fields('author', null, 'authorid', 'author');
        $overbyfields   = user_picture::fields('overby', null, 'gradinggradeoverbyx', 'overby');
        $sql = "SELECT a.id, a.submissionid, a.reviewerid, a.timecreated, a.timemodified,
                       a.grade, a.gradinggrade, a.gradinggradeover, a.gradinggradeoverby,
                       $reviewerfields, $authorfields, $overbyfields,
                       s.title
                  FROM {workshop_assessments} a
            INNER JOIN {user} reviewer ON (a.reviewerid = reviewer.id)
            INNER JOIN {workshop_submissions} s ON (a.submissionid = s.id)
            INNER JOIN {user} author ON (s.authorid = author.id)
             LEFT JOIN {user} overby ON (a.gradinggradeoverby = overby.id)
                 WHERE s.workshopid = :workshopid AND s.example = 0
              ORDER BY reviewer.lastname, reviewer.firstname";
        $params = array('workshopid' => $this->id);

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get the complete information about the given assessment
     *
     * @param int $id Assessment ID
     * @return stdclass
     */
    public function get_assessment_by_id($id) {
        global $DB;

        $reviewerfields = user_picture::fields('reviewer', null, 'revieweridx', 'reviewer');
        $authorfields   = user_picture::fields('author', null, 'authorid', 'author');
        $overbyfields   = user_picture::fields('overby', null, 'gradinggradeoverbyx', 'overby');
        $sql = "SELECT a.*, s.title, $reviewerfields, $authorfields, $overbyfields
                  FROM {workshop_assessments} a
            INNER JOIN {user} reviewer ON (a.reviewerid = reviewer.id)
            INNER JOIN {workshop_submissions} s ON (a.submissionid = s.id)
            INNER JOIN {user} author ON (s.authorid = author.id)
             LEFT JOIN {user} overby ON (a.gradinggradeoverby = overby.id)
                 WHERE a.id = :id AND s.workshopid = :workshopid";
        $params = array('id' => $id, 'workshopid' => $this->id);

        return $DB->get_record_sql($sql, $params, MUST_EXIST);
    }

    /**
     * Get the complete information about the user's assessment of the given submission
     *
     * @param int $sid submission ID
     * @param int $uid user ID of the reviewer
     * @return false|stdclass false if not found, stdclass otherwise
     */
    public function get_assessment_of_submission_by_user($submissionid, $reviewerid) {
        global $DB;

        $reviewerfields = user_picture::fields('reviewer', null, 'revieweridx', 'reviewer');
        $authorfields   = user_picture::fields('author', null, 'authorid', 'author');
        $overbyfields   = user_picture::fields('overby', null, 'gradinggradeoverbyx', 'overby');
        $sql = "SELECT a.*, s.title, $reviewerfields, $authorfields, $overbyfields
                  FROM {workshop_assessments} a
            INNER JOIN {user} reviewer ON (a.reviewerid = reviewer.id)
            INNER JOIN {workshop_submissions} s ON (a.submissionid = s.id AND s.example = 0)
            INNER JOIN {user} author ON (s.authorid = author.id)
             LEFT JOIN {user} overby ON (a.gradinggradeoverby = overby.id)
                 WHERE s.id = :sid AND reviewer.id = :rid AND s.workshopid = :workshopid";
        $params = array('sid' => $submissionid, 'rid' => $reviewerid, 'workshopid' => $this->id);

        return $DB->get_record_sql($sql, $params, IGNORE_MISSING);
    }

    /**
     * Get the complete information about all assessments of the given submission
     *
     * @param int $submissionid
     * @return array
     */
    public function get_assessments_of_submission($submissionid) {
        global $DB;

        $reviewerfields = user_picture::fields('reviewer', null, 'revieweridx', 'reviewer');
        $overbyfields   = user_picture::fields('overby', null, 'gradinggradeoverbyx', 'overby');
        $sql = "SELECT a.*, s.title, $reviewerfields, $overbyfields
                  FROM {workshop_assessments} a
            INNER JOIN {user} reviewer ON (a.reviewerid = reviewer.id)
            INNER JOIN {workshop_submissions} s ON (a.submissionid = s.id)
             LEFT JOIN {user} overby ON (a.gradinggradeoverby = overby.id)
                 WHERE s.example = 0 AND s.id = :submissionid AND s.workshopid = :workshopid
              ORDER BY reviewer.lastname, reviewer.firstname, reviewer.id";
        $params = array('submissionid' => $submissionid, 'workshopid' => $this->id);

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get the complete information about all assessments allocated to the given reviewer
     *
     * @param int $reviewerid
     * @return array
     */
    public function get_assessments_by_reviewer($reviewerid) {
        global $DB;

        $reviewerfields = user_picture::fields('reviewer', null, 'revieweridx', 'reviewer');
        $authorfields   = user_picture::fields('author', null, 'authorid', 'author');
        $overbyfields   = user_picture::fields('overby', null, 'gradinggradeoverbyx', 'overby');
        $sql = "SELECT a.*, $reviewerfields, $authorfields, $overbyfields,
                       s.id AS submissionid, s.title AS submissiontitle, s.timecreated AS submissioncreated,
                       s.timemodified AS submissionmodified
                  FROM {workshop_assessments} a
            INNER JOIN {user} reviewer ON (a.reviewerid = reviewer.id)
            INNER JOIN {workshop_submissions} s ON (a.submissionid = s.id)
            INNER JOIN {user} author ON (s.authorid = author.id)
             LEFT JOIN {user} overby ON (a.gradinggradeoverby = overby.id)
                 WHERE s.example = 0 AND reviewer.id = :reviewerid AND s.workshopid = :workshopid";
        $params = array('reviewerid' => $reviewerid, 'workshopid' => $this->id);

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Allocate a submission to a user for review
     *
     * @param stdClass $submission Submission object with at least id property
     * @param int $reviewerid User ID
     * @param int $weight of the new assessment, from 0 to 16
     * @param bool $bulk repeated inserts into DB expected
     * @return int ID of the new assessment or an error code
     */
    public function add_allocation(stdclass $submission, $reviewerid, $weight=1, $bulk=false) {
        global $DB;

        if ($DB->record_exists('workshop_assessments', array('submissionid' => $submission->id, 'reviewerid' => $reviewerid))) {
            return self::ALLOCATION_EXISTS;
        }

        $weight = (int)$weight;
        if ($weight < 0) {
            $weight = 0;
        }
        if ($weight > 16) {
            $weight = 16;
        }

        $now = time();
        $assessment = new stdclass();
        $assessment->submissionid           = $submission->id;
        $assessment->reviewerid             = $reviewerid;
        $assessment->timecreated            = $now;         // do not set timemodified here
        $assessment->weight                 = $weight;
        $assessment->generalcommentformat   = editors_get_preferred_format();
        $assessment->feedbackreviewerformat = editors_get_preferred_format();

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
     * @return stdclass Instance of a grading strategy
     */
    public function grading_strategy_instance() {
        global $CFG;    // because we require other libs here

        if (is_null($this->strategyinstance)) {
            $strategylib = dirname(__FILE__) . '/form/' . $this->strategy . '/lib.php';
            if (is_readable($strategylib)) {
                require_once($strategylib);
            } else {
                throw new coding_exception('the grading forms subplugin must contain library ' . $strategylib);
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
     * Returns instance of grading evaluation class
     *
     * @return stdclass Instance of a grading evaluation
     */
    public function grading_evaluation_instance() {
        global $CFG;    // because we require other libs here

        if (is_null($this->evaluationinstance)) {
            $evaluationlib = dirname(__FILE__) . '/eval/' . $this->evaluation . '/lib.php';
            if (is_readable($evaluationlib)) {
                require_once($evaluationlib);
            } else {
                throw new coding_exception('the grading evaluation subplugin must contain library ' . $evaluationlib);
            }
            $classname = 'workshop_' . $this->evaluation . '_evaluation';
            $this->evaluationinstance = new $classname($this);
            if (!in_array('workshop_evaluation', class_implements($this->evaluationinstance))) {
                throw new coding_exception($classname . ' does not implement workshop_evaluation interface');
            }
        }
        return $this->evaluationinstance;
    }

    /**
     * Returns instance of submissions allocator
     *
     * @param string $method The name of the allocation method, must be PARAM_ALPHA
     * @return stdclass Instance of submissions allocator
     */
    public function allocator_instance($method) {
        global $CFG;    // because we require other libs here

        $allocationlib = dirname(__FILE__) . '/allocation/' . $method . '/lib.php';
        if (is_readable($allocationlib)) {
            require_once($allocationlib);
        } else {
            throw new coding_exception('Unable to find the allocation library ' . $allocationlib);
        }
        $classname = 'workshop_' . $method . '_allocator';
        return new $classname($this);
    }

    /**
     * @return moodle_url of this workshop's view page
     */
    public function view_url() {
        global $CFG;
        return new moodle_url('/mod/workshop/view.php', array('id' => $this->cm->id));
    }

    /**
     * @return moodle_url of the page for editing this workshop's grading form
     */
    public function editform_url() {
        global $CFG;
        return new moodle_url('/mod/workshop/editform.php', array('cmid' => $this->cm->id));
    }

    /**
     * @return moodle_url of the page for previewing this workshop's grading form
     */
    public function previewform_url() {
        global $CFG;
        return new moodle_url('/mod/workshop/editformpreview.php', array('cmid' => $this->cm->id));
    }

    /**
     * @param int $assessmentid The ID of assessment record
     * @return moodle_url of the assessment page
     */
    public function assess_url($assessmentid) {
        global $CFG;
        $assessmentid = clean_param($assessmentid, PARAM_INT);
        return new moodle_url('/mod/workshop/assessment.php', array('asid' => $assessmentid));
    }

    /**
     * @param int $assessmentid The ID of assessment record
     * @return moodle_url of the example assessment page
     */
    public function exassess_url($assessmentid) {
        global $CFG;
        $assessmentid = clean_param($assessmentid, PARAM_INT);
        return new moodle_url('/mod/workshop/exassessment.php', array('asid' => $assessmentid));
    }

    /**
     * @return moodle_url of the page to view a submission, defaults to the own one
     */
    public function submission_url($id=null) {
        global $CFG;
        return new moodle_url('/mod/workshop/submission.php', array('cmid' => $this->cm->id, 'id' => $id));
    }

    /**
     * @param int $id example submission id
     * @return moodle_url of the page to view an example submission
     */
    public function exsubmission_url($id) {
        global $CFG;
        return new moodle_url('/mod/workshop/exsubmission.php', array('cmid' => $this->cm->id, 'id' => $id));
    }

    /**
     * @param int $sid submission id
     * @param array $aid of int assessment ids
     * @return moodle_url of the page to compare assessments of the given submission
     */
    public function compare_url($sid, array $aids) {
        global $CFG;

        $url = new moodle_url('/mod/workshop/compare.php', array('cmid' => $this->cm->id, 'sid' => $sid));
        $i = 0;
        foreach ($aids as $aid) {
            $url->param("aid{$i}", $aid);
            $i++;
        }
        return $url;
    }

    /**
     * @param int $sid submission id
     * @param int $aid assessment id
     * @return moodle_url of the page to compare the reference assessments of the given example submission
     */
    public function excompare_url($sid, $aid) {
        global $CFG;
        return new moodle_url('/mod/workshop/excompare.php', array('cmid' => $this->cm->id, 'sid' => $sid, 'aid' => $aid));
    }

    /**
     * @return moodle_url of the mod_edit form
     */
    public function updatemod_url() {
        global $CFG;
        return new moodle_url('/course/modedit.php', array('update' => $this->cm->id, 'return' => 1));
    }

    /**
     * @param string $method allocation method
     * @return moodle_url to the allocation page
     */
    public function allocation_url($method=null) {
        global $CFG;
        $params = array('cmid' => $this->cm->id);
        if (!empty($method)) {
            $params['method'] = $method;
        }
        return new moodle_url('/mod/workshop/allocation.php', $params);
    }

    /**
     * @param int $phasecode The internal phase code
     * @return moodle_url of the script to change the current phase to $phasecode
     */
    public function switchphase_url($phasecode) {
        global $CFG;
        $phasecode = clean_param($phasecode, PARAM_INT);
        return new moodle_url('/mod/workshop/switchphase.php', array('cmid' => $this->cm->id, 'phase' => $phasecode));
    }

    /**
     * @return moodle_url to the aggregation page
     */
    public function aggregate_url() {
        global $CFG;
        return new moodle_url('/mod/workshop/aggregate.php', array('cmid' => $this->cm->id));
    }

    /**
     * @return moodle_url of this workshop's toolbox page
     */
    public function toolbox_url($tool) {
        global $CFG;
        return new moodle_url('/mod/workshop/toolbox.php', array('id' => $this->cm->id, 'tool' => $tool));
    }

    /**
     * Workshop wrapper around {@see add_to_log()}
     *
     * @param string $action to be logged
     * @param moodle_url $url absolute url as returned by {@see workshop::submission_url()} and friends
     * @param mixed $info additional info, usually id in a table
     */
    public function log($action, moodle_url $url = null, $info = null) {

        if (is_null($url)) {
            $url = $this->view_url();
        }

        if (is_null($info)) {
            $info = $this->id;
        }

        $logurl = $this->log_convert_url($url);
        add_to_log($this->course->id, 'workshop', $action, $logurl, $info, $this->cm->id);
    }

    /**
     * Is the given user allowed to create their submission?
     *
     * @param int $userid
     * @return bool
     */
    public function creating_submission_allowed($userid) {

        $now = time();
        $ignoredeadlines = has_capability('mod/workshop:ignoredeadlines', $this->context, $userid);

        if ($this->latesubmissions) {
            if ($this->phase != self::PHASE_SUBMISSION and $this->phase != self::PHASE_ASSESSMENT) {
                // late submissions are allowed in the submission and assessment phase only
                return false;
            }
            if (!$ignoredeadlines and !empty($this->submissionstart) and $this->submissionstart > $now) {
                // late submissions are not allowed before the submission start
                return false;
            }
            return true;

        } else {
            if ($this->phase != self::PHASE_SUBMISSION) {
                // submissions are allowed during the submission phase only
                return false;
            }
            if (!$ignoredeadlines and !empty($this->submissionstart) and $this->submissionstart > $now) {
                // if enabled, submitting is not allowed before the date/time defined in the mod_form
                return false;
            }
            if (!$ignoredeadlines and !empty($this->submissionend) and $now > $this->submissionend ) {
                // if enabled, submitting is not allowed after the date/time defined in the mod_form unless late submission is allowed
                return false;
            }
            return true;
        }
    }

    /**
     * Is the given user allowed to modify their existing submission?
     *
     * @param int $userid
     * @return bool
     */
    public function modifying_submission_allowed($userid) {

        $now = time();
        $ignoredeadlines = has_capability('mod/workshop:ignoredeadlines', $this->context, $userid);

        if ($this->phase != self::PHASE_SUBMISSION) {
            // submissions can be edited during the submission phase only
            return false;
        }
        if (!$ignoredeadlines and !empty($this->submissionstart) and $this->submissionstart > $now) {
            // if enabled, re-submitting is not allowed before the date/time defined in the mod_form
            return false;
        }
        if (!$ignoredeadlines and !empty($this->submissionend) and $now > $this->submissionend) {
            // if enabled, re-submitting is not allowed after the date/time defined in the mod_form even if late submission is allowed
            return false;
        }
        return true;
    }

    /**
     * Is the given reviewer allowed to create/edit their assessments?
     *
     * @param int $userid
     * @return bool
     */
    public function assessing_allowed($userid) {

        if ($this->phase != self::PHASE_ASSESSMENT) {
            // assessing is not allowed but in the assessment phase
            return false;
        }

        $now = time();
        $ignoredeadlines = has_capability('mod/workshop:ignoredeadlines', $this->context, $userid);

        if (!$ignoredeadlines and !empty($this->assessmentstart) and $this->assessmentstart > $now) {
            // if enabled, assessing is not allowed before the date/time defined in the mod_form
            return false;
        }
        if (!$ignoredeadlines and !empty($this->assessmentend) and $now > $this->assessmentend) {
            // if enabled, assessing is not allowed after the date/time defined in the mod_form
            return false;
        }
        // here we go, assessing is allowed
        return true;
    }

    /**
     * Are reviewers allowed to create/edit their assessments of the example submissions?
     *
     * Returns null if example submissions are not enabled in this workshop. Otherwise returns
     * true or false. Note this does not check other conditions like the number of already
     * assessed examples, examples mode etc.
     *
     * @return null|bool
     */
    public function assessing_examples_allowed() {
        if (empty($this->useexamples)) {
            return null;
        }
        if (self::EXAMPLES_VOLUNTARY == $this->examplesmode) {
            return true;
        }
        if (self::EXAMPLES_BEFORE_SUBMISSION == $this->examplesmode and self::PHASE_SUBMISSION == $this->phase) {
            return true;
        }
        if (self::EXAMPLES_BEFORE_ASSESSMENT == $this->examplesmode and self::PHASE_ASSESSMENT == $this->phase) {
            return true;
        }
        return false;
    }

    /**
     * Are the peer-reviews available to the authors?
     *
     * @return bool
     */
    public function assessments_available() {
        return $this->phase == self::PHASE_CLOSED;
    }

    /**
     * Switch to a new workshop phase
     *
     * Modifies the underlying database record. You should terminate the script shortly after calling this.
     *
     * @param int $newphase new phase code
     * @return bool true if success, false otherwise
     */
    public function switch_phase($newphase) {
        global $DB;

        $known = $this->available_phases_list();
        if (!isset($known[$newphase])) {
            return false;
        }

        if (self::PHASE_CLOSED == $newphase) {
            // push the grades into the gradebook
            $workshop = new stdclass();
            foreach ($this as $property => $value) {
                $workshop->{$property} = $value;
            }
            $workshop->course     = $this->course->id;
            $workshop->cmidnumber = $this->cm->id;
            $workshop->modname    = 'workshop';
            workshop_update_grades($workshop);
        }

        $DB->set_field('workshop', 'phase', $newphase, array('id' => $this->id));
        return true;
    }

    /**
     * Saves a raw grade for submission as calculated from the assessment form fields
     *
     * @param array $assessmentid assessment record id, must exists
     * @param mixed $grade        raw percentual grade from 0.00000 to 100.00000
     * @return false|float        the saved grade
     */
    public function set_peer_grade($assessmentid, $grade) {
        global $DB;

        if (is_null($grade)) {
            return false;
        }
        $data = new stdclass();
        $data->id = $assessmentid;
        $data->grade = $grade;
        $data->timemodified = time();
        $DB->update_record('workshop_assessments', $data);
        return $grade;
    }

    /**
     * Prepares data object with all workshop grades to be rendered
     *
     * @param int $userid the user we are preparing the report for
     * @param mixed $groups single group or array of groups - only show users who are in one of these group(s). Defaults to all
     * @param int $page the current page (for the pagination)
     * @param int $perpage participants per page (for the pagination)
     * @param string $sortby lastname|firstname|submissiontitle|submissiongrade|gradinggrade
     * @param string $sorthow ASC|DESC
     * @return stdclass data for the renderer
     */
    public function prepare_grading_report_data($userid, $groups, $page, $perpage, $sortby, $sorthow) {
        global $DB;

        $canviewall     = has_capability('mod/workshop:viewallassessments', $this->context, $userid);
        $isparticipant  = has_any_capability(array('mod/workshop:submit', 'mod/workshop:peerassess'), $this->context, $userid);

        if (!$canviewall and !$isparticipant) {
            // who the hell is this?
            return array();
        }

        if (!in_array($sortby, array('lastname','firstname','submissiontitle','submissiongrade','gradinggrade'))) {
            $sortby = 'lastname';
        }

        if (!($sorthow === 'ASC' or $sorthow === 'DESC')) {
            $sorthow = 'ASC';
        }

        // get the list of user ids to be displayed
        if ($canviewall) {
            // fetch the list of ids of all workshop participants - this may get really long so fetch just id
            $participants = get_users_by_capability($this->context, array('mod/workshop:submit', 'mod/workshop:peerassess'),
                    'u.id', '', '', '', $groups, '', false, false, true);
        } else {
            // this is an ordinary workshop participant (aka student) - display the report just for him/her
            $participants = array($userid => (object)array('id' => $userid));
        }

        // we will need to know the number of all records later for the pagination purposes
        $numofparticipants = count($participants);

        if ($numofparticipants > 0) {
            // load all fields which can be used for sorting and paginate the records
            list($participantids, $params) = $DB->get_in_or_equal(array_keys($participants), SQL_PARAMS_NAMED);
            $params['workshopid1'] = $this->id;
            $params['workshopid2'] = $this->id;
            $sqlsort = array();
            $sqlsortfields = array($sortby => $sorthow) + array('lastname' => 'ASC', 'firstname' => 'ASC', 'u.id' => 'ASC');
            foreach ($sqlsortfields as $sqlsortfieldname => $sqlsortfieldhow) {
                $sqlsort[] = $sqlsortfieldname . ' ' . $sqlsortfieldhow;
            }
            $sqlsort = implode(',', $sqlsort);
            $sql = "SELECT u.id AS userid,u.firstname,u.lastname,u.picture,u.imagealt,u.email,
                           s.title AS submissiontitle, s.grade AS submissiongrade, ag.gradinggrade
                      FROM {user} u
                 LEFT JOIN {workshop_submissions} s ON (s.authorid = u.id AND s.workshopid = :workshopid1 AND s.example = 0)
                 LEFT JOIN {workshop_aggregations} ag ON (ag.userid = u.id AND ag.workshopid = :workshopid2)
                     WHERE u.id $participantids
                  ORDER BY $sqlsort";
            $participants = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);
        } else {
            $participants = array();
        }

        // this will hold the information needed to display user names and pictures
        $userinfo = array();

        // get the user details for all participants to display
        foreach ($participants as $participant) {
            if (!isset($userinfo[$participant->userid])) {
                $userinfo[$participant->userid]            = new stdclass();
                $userinfo[$participant->userid]->id        = $participant->userid;
                $userinfo[$participant->userid]->firstname = $participant->firstname;
                $userinfo[$participant->userid]->lastname  = $participant->lastname;
                $userinfo[$participant->userid]->picture   = $participant->picture;
                $userinfo[$participant->userid]->imagealt  = $participant->imagealt;
                $userinfo[$participant->userid]->email     = $participant->email;
            }
        }

        // load the submissions details
        $submissions = $this->get_submissions(array_keys($participants));

        // get the user details for all moderators (teachers) that have overridden a submission grade
        foreach ($submissions as $submission) {
            if (!isset($userinfo[$submission->gradeoverby])) {
                $userinfo[$submission->gradeoverby]            = new stdclass();
                $userinfo[$submission->gradeoverby]->id        = $submission->gradeoverby;
                $userinfo[$submission->gradeoverby]->firstname = $submission->overfirstname;
                $userinfo[$submission->gradeoverby]->lastname  = $submission->overlastname;
                $userinfo[$submission->gradeoverby]->picture   = $submission->overpicture;
                $userinfo[$submission->gradeoverby]->imagealt  = $submission->overimagealt;
                $userinfo[$submission->gradeoverby]->email     = $submission->overemail;
            }
        }

        // get the user details for all reviewers of the displayed participants
        $reviewers = array();
        if ($submissions) {
            list($submissionids, $params) = $DB->get_in_or_equal(array_keys($submissions), SQL_PARAMS_NAMED);
            $sql = "SELECT a.id AS assessmentid, a.submissionid, a.grade, a.gradinggrade, a.gradinggradeover, a.weight,
                           r.id AS reviewerid, r.lastname, r.firstname, r.picture, r.imagealt, r.email,
                           s.id AS submissionid, s.authorid
                      FROM {workshop_assessments} a
                      JOIN {user} r ON (a.reviewerid = r.id)
                      JOIN {workshop_submissions} s ON (a.submissionid = s.id AND s.example = 0)
                     WHERE a.submissionid $submissionids
                  ORDER BY a.weight DESC, r.lastname, r.firstname";
            $reviewers = $DB->get_records_sql($sql, $params);
            foreach ($reviewers as $reviewer) {
                if (!isset($userinfo[$reviewer->reviewerid])) {
                    $userinfo[$reviewer->reviewerid]            = new stdclass();
                    $userinfo[$reviewer->reviewerid]->id        = $reviewer->reviewerid;
                    $userinfo[$reviewer->reviewerid]->firstname = $reviewer->firstname;
                    $userinfo[$reviewer->reviewerid]->lastname  = $reviewer->lastname;
                    $userinfo[$reviewer->reviewerid]->picture   = $reviewer->picture;
                    $userinfo[$reviewer->reviewerid]->imagealt  = $reviewer->imagealt;
                    $userinfo[$reviewer->reviewerid]->email     = $reviewer->email;
                }
            }
        }

        // get the user details for all reviewees of the displayed participants
        $reviewees = array();
        if ($participants) {
            list($participantids, $params) = $DB->get_in_or_equal(array_keys($participants), SQL_PARAMS_NAMED);
            $params['workshopid'] = $this->id;
            $sql = "SELECT a.id AS assessmentid, a.submissionid, a.grade, a.gradinggrade, a.gradinggradeover, a.reviewerid, a.weight,
                           s.id AS submissionid,
                           e.id AS authorid, e.lastname, e.firstname, e.picture, e.imagealt, e.email
                      FROM {user} u
                      JOIN {workshop_assessments} a ON (a.reviewerid = u.id)
                      JOIN {workshop_submissions} s ON (a.submissionid = s.id AND s.example = 0)
                      JOIN {user} e ON (s.authorid = e.id)
                     WHERE u.id $participantids AND s.workshopid = :workshopid
                  ORDER BY a.weight DESC, e.lastname, e.firstname";
            $reviewees = $DB->get_records_sql($sql, $params);
            foreach ($reviewees as $reviewee) {
                if (!isset($userinfo[$reviewee->authorid])) {
                    $userinfo[$reviewee->authorid]            = new stdclass();
                    $userinfo[$reviewee->authorid]->id        = $reviewee->authorid;
                    $userinfo[$reviewee->authorid]->firstname = $reviewee->firstname;
                    $userinfo[$reviewee->authorid]->lastname  = $reviewee->lastname;
                    $userinfo[$reviewee->authorid]->picture   = $reviewee->picture;
                    $userinfo[$reviewee->authorid]->imagealt  = $reviewee->imagealt;
                    $userinfo[$reviewee->authorid]->email     = $reviewee->email;
                }
            }
        }

        // finally populate the object to be rendered
        $grades = $participants;

        foreach ($participants as $participant) {
            // set up default (null) values
            $grades[$participant->userid]->submissionid = null;
            $grades[$participant->userid]->submissiontitle = null;
            $grades[$participant->userid]->submissiongrade = null;
            $grades[$participant->userid]->submissiongradeover = null;
            $grades[$participant->userid]->submissiongradeoverby = null;
            $grades[$participant->userid]->submissionpublished = null;
            $grades[$participant->userid]->reviewedby = array();
            $grades[$participant->userid]->reviewerof = array();
        }
        unset($participants);
        unset($participant);

        foreach ($submissions as $submission) {
            $grades[$submission->authorid]->submissionid = $submission->id;
            $grades[$submission->authorid]->submissiontitle = $submission->title;
            $grades[$submission->authorid]->submissiongrade = $this->real_grade($submission->grade);
            $grades[$submission->authorid]->submissiongradeover = $this->real_grade($submission->gradeover);
            $grades[$submission->authorid]->submissiongradeoverby = $submission->gradeoverby;
            $grades[$submission->authorid]->submissionpublished = $submission->published;
        }
        unset($submissions);
        unset($submission);

        foreach($reviewers as $reviewer) {
            $info = new stdclass();
            $info->userid = $reviewer->reviewerid;
            $info->assessmentid = $reviewer->assessmentid;
            $info->submissionid = $reviewer->submissionid;
            $info->grade = $this->real_grade($reviewer->grade);
            $info->gradinggrade = $this->real_grading_grade($reviewer->gradinggrade);
            $info->gradinggradeover = $this->real_grading_grade($reviewer->gradinggradeover);
            $info->weight = $reviewer->weight;
            $grades[$reviewer->authorid]->reviewedby[$reviewer->reviewerid] = $info;
        }
        unset($reviewers);
        unset($reviewer);

        foreach($reviewees as $reviewee) {
            $info = new stdclass();
            $info->userid = $reviewee->authorid;
            $info->assessmentid = $reviewee->assessmentid;
            $info->submissionid = $reviewee->submissionid;
            $info->grade = $this->real_grade($reviewee->grade);
            $info->gradinggrade = $this->real_grading_grade($reviewee->gradinggrade);
            $info->gradinggradeover = $this->real_grading_grade($reviewee->gradinggradeover);
            $info->weight = $reviewee->weight;
            $grades[$reviewee->reviewerid]->reviewerof[$reviewee->authorid] = $info;
        }
        unset($reviewees);
        unset($reviewee);

        foreach ($grades as $grade) {
            $grade->gradinggrade = $this->real_grading_grade($grade->gradinggrade);
        }

        $data = new stdclass();
        $data->grades = $grades;
        $data->userinfo = $userinfo;
        $data->totalcount = $numofparticipants;
        $data->maxgrade = $this->real_grade(100);
        $data->maxgradinggrade = $this->real_grading_grade(100);
        return $data;
    }

    /**
     * Calculates the real value of a grade
     *
     * @param float $value percentual value from 0 to 100
     * @param float $max   the maximal grade
     * @return string
     */
    public function real_grade_value($value, $max) {
        $localized = true;
        if (is_null($value) or $value === '') {
            return null;
        } elseif ($max == 0) {
            return 0;
        } else {
            return format_float($max * $value / 100, $this->gradedecimals, $localized);
        }
    }

    /**
     * Calculates the raw (percentual) value from a real grade
     *
     * This is used in cases when a user wants to give a grade such as 12 of 20 and we need to save
     * this value in a raw percentual form into DB
     * @param float $value given grade
     * @param float $max   the maximal grade
     * @return float       suitable to be stored as numeric(10,5)
     */
    public function raw_grade_value($value, $max) {
        if (is_null($value) or $value === '') {
            return null;
        }
        if ($max == 0 or $value < 0) {
            return 0;
        }
        $p = $value / $max * 100;
        if ($p > 100) {
            return $max;
        }
        return grade_floatval($p);
    }

    /**
     * Calculates the real value of grade for submission
     *
     * @param float $value percentual value from 0 to 100
     * @return string
     */
    public function real_grade($value) {
        return $this->real_grade_value($value, $this->grade);
    }

    /**
     * Calculates the real value of grade for assessment
     *
     * @param float $value percentual value from 0 to 100
     * @return string
     */
    public function real_grading_grade($value) {
        return $this->real_grade_value($value, $this->gradinggrade);
    }

    /**
     * Sets the given grades and received grading grades to null
     *
     * This does not clear the information about how the peers filled the assessment forms, but
     * clears the calculated grades in workshop_assessments. Therefore reviewers have to re-assess
     * the allocated submissions.
     *
     * @return void
     */
    public function clear_assessments() {
        global $DB;

        $submissions = $this->get_submissions();
        if (empty($submissions)) {
            // no money, no love
            return;
        }
        $submissions = array_keys($submissions);
        list($sql, $params) = $DB->get_in_or_equal($submissions, SQL_PARAMS_NAMED);
        $sql = "submissionid $sql";
        $DB->set_field_select('workshop_assessments', 'grade', null, $sql, $params);
        $DB->set_field_select('workshop_assessments', 'gradinggrade', null, $sql, $params);
    }

    /**
     * Sets the grades for submission to null
     *
     * @param null|int|array $restrict If null, update all authors, otherwise update just grades for the given author(s)
     * @return void
     */
    public function clear_submission_grades($restrict=null) {
        global $DB;

        $sql = "workshopid = :workshopid AND example = 0";
        $params = array('workshopid' => $this->id);

        if (is_null($restrict)) {
            // update all users - no more conditions
        } elseif (!empty($restrict)) {
            list($usql, $uparams) = $DB->get_in_or_equal($restrict, SQL_PARAMS_NAMED);
            $sql .= " AND authorid $usql";
            $params = array_merge($params, $uparams);
        } else {
            throw new coding_exception('Empty value is not a valid parameter here');
        }

        $DB->set_field_select('workshop_submissions', 'grade', null, $sql, $params);
    }

    /**
     * Calculates grades for submission for the given participant(s) and updates it in the database
     *
     * @param null|int|array $restrict If null, update all authors, otherwise update just grades for the given author(s)
     * @return void
     */
    public function aggregate_submission_grades($restrict=null) {
        global $DB;

        // fetch a recordset with all assessments to process
        $sql = 'SELECT s.id AS submissionid, s.grade AS submissiongrade,
                       a.weight, a.grade
                  FROM {workshop_submissions} s
             LEFT JOIN {workshop_assessments} a ON (a.submissionid = s.id)
                 WHERE s.example=0 AND s.workshopid=:workshopid'; // to be cont.
        $params = array('workshopid' => $this->id);

        if (is_null($restrict)) {
            // update all users - no more conditions
        } elseif (!empty($restrict)) {
            list($usql, $uparams) = $DB->get_in_or_equal($restrict, SQL_PARAMS_NAMED);
            $sql .= " AND s.authorid $usql";
            $params = array_merge($params, $uparams);
        } else {
            throw new coding_exception('Empty value is not a valid parameter here');
        }

        $sql .= ' ORDER BY s.id'; // this is important for bulk processing

        $rs         = $DB->get_recordset_sql($sql, $params);
        $batch      = array();    // will contain a set of all assessments of a single submission
        $previous   = null;       // a previous record in the recordset

        foreach ($rs as $current) {
            if (is_null($previous)) {
                // we are processing the very first record in the recordset
                $previous   = $current;
            }
            if ($current->submissionid == $previous->submissionid) {
                // we are still processing the current submission
                $batch[] = $current;
            } else {
                // process all the assessments of a sigle submission
                $this->aggregate_submission_grades_process($batch);
                // and then start to process another submission
                $batch      = array($current);
                $previous   = $current;
            }
        }
        // do not forget to process the last batch!
        $this->aggregate_submission_grades_process($batch);
        $rs->close();
    }

    /**
     * Sets the aggregated grades for assessment to null
     *
     * @param null|int|array $restrict If null, update all reviewers, otherwise update just grades for the given reviewer(s)
     * @return void
     */
    public function clear_grading_grades($restrict=null) {
        global $DB;

        $sql = "workshopid = :workshopid";
        $params = array('workshopid' => $this->id);

        if (is_null($restrict)) {
            // update all users - no more conditions
        } elseif (!empty($restrict)) {
            list($usql, $uparams) = $DB->get_in_or_equal($restrict, SQL_PARAMS_NAMED);
            $sql .= " AND userid $usql";
            $params = array_merge($params, $uparams);
        } else {
            throw new coding_exception('Empty value is not a valid parameter here');
        }

        $DB->set_field_select('workshop_aggregations', 'gradinggrade', null, $sql, $params);
    }

    /**
     * Calculates grades for assessment for the given participant(s)
     *
     * Grade for assessment is calculated as a simple mean of all grading grades calculated by the grading evaluator.
     * The assessment weight is not taken into account here.
     *
     * @param null|int|array $restrict If null, update all reviewers, otherwise update just grades for the given reviewer(s)
     * @return void
     */
    public function aggregate_grading_grades($restrict=null) {
        global $DB;

        // fetch a recordset with all assessments to process
        $sql = 'SELECT a.reviewerid, a.gradinggrade, a.gradinggradeover,
                       ag.id AS aggregationid, ag.gradinggrade AS aggregatedgrade
                  FROM {workshop_assessments} a
            INNER JOIN {workshop_submissions} s ON (a.submissionid = s.id)
             LEFT JOIN {workshop_aggregations} ag ON (ag.userid = a.reviewerid AND ag.workshopid = s.workshopid)
                 WHERE s.example=0 AND s.workshopid=:workshopid'; // to be cont.
        $params = array('workshopid' => $this->id);

        if (is_null($restrict)) {
            // update all users - no more conditions
        } elseif (!empty($restrict)) {
            list($usql, $uparams) = $DB->get_in_or_equal($restrict, SQL_PARAMS_NAMED);
            $sql .= " AND a.reviewerid $usql";
            $params = array_merge($params, $uparams);
        } else {
            throw new coding_exception('Empty value is not a valid parameter here');
        }

        $sql .= ' ORDER BY a.reviewerid'; // this is important for bulk processing

        $rs         = $DB->get_recordset_sql($sql, $params);
        $batch      = array();    // will contain a set of all assessments of a single submission
        $previous   = null;       // a previous record in the recordset

        foreach ($rs as $current) {
            if (is_null($previous)) {
                // we are processing the very first record in the recordset
                $previous   = $current;
            }
            if ($current->reviewerid == $previous->reviewerid) {
                // we are still processing the current reviewer
                $batch[] = $current;
            } else {
                // process all the assessments of a sigle submission
                $this->aggregate_grading_grades_process($batch);
                // and then start to process another reviewer
                $batch      = array($current);
                $previous   = $current;
            }
        }
        // do not forget to process the last batch!
        $this->aggregate_grading_grades_process($batch);
        $rs->close();
    }

    /**
     * Returns the mform the teachers use to put a feedback for the reviewer
     *
     * @param moodle_url $actionurl
     * @param stdClass $assessment
     * @param array $options editable, editableweight, overridablegradinggrade
     * @return workshop_feedbackreviewer_form
     */
    public function get_feedbackreviewer_form(moodle_url $actionurl, stdclass $assessment, $options=array()) {
        global $CFG;
        require_once(dirname(__FILE__) . '/feedbackreviewer_form.php');

        $current = new stdclass();
        $current->asid                      = $assessment->id;
        $current->weight                    = $assessment->weight;
        $current->gradinggrade              = $this->real_grading_grade($assessment->gradinggrade);
        $current->gradinggradeover          = $this->real_grading_grade($assessment->gradinggradeover);
        $current->feedbackreviewer          = $assessment->feedbackreviewer;
        $current->feedbackreviewerformat    = $assessment->feedbackreviewerformat;
        if (is_null($current->gradinggrade)) {
            $current->gradinggrade = get_string('nullgrade', 'workshop');
        }
        if (!isset($options['editable'])) {
            $editable = true;   // by default
        } else {
            $editable = (bool)$options['editable'];
        }

        // prepare wysiwyg editor
        $current = file_prepare_standard_editor($current, 'feedbackreviewer', array());

        return new workshop_feedbackreviewer_form($actionurl,
                array('workshop' => $this, 'current' => $current, 'editoropts' => array(), 'options' => $options),
                'post', '', null, $editable);
    }

    /**
     * Returns the mform the teachers use to put a feedback for the author on their submission
     *
     * @param moodle_url $actionurl
     * @param stdClass $submission
     * @param array $options editable
     * @return workshop_feedbackauthor_form
     */
    public function get_feedbackauthor_form(moodle_url $actionurl, stdclass $submission, $options=array()) {
        global $CFG;
        require_once(dirname(__FILE__) . '/feedbackauthor_form.php');

        $current = new stdclass();
        $current->submissionid          = $submission->id;
        $current->published             = $submission->published;
        $current->grade                 = $this->real_grade($submission->grade);
        $current->gradeover             = $this->real_grade($submission->gradeover);
        $current->feedbackauthor        = $submission->feedbackauthor;
        $current->feedbackauthorformat  = $submission->feedbackauthorformat;
        if (is_null($current->grade)) {
            $current->grade = get_string('nullgrade', 'workshop');
        }
        if (!isset($options['editable'])) {
            $editable = true;   // by default
        } else {
            $editable = (bool)$options['editable'];
        }

        // prepare wysiwyg editor
        $current = file_prepare_standard_editor($current, 'feedbackauthor', array());

        return new workshop_feedbackauthor_form($actionurl,
                array('workshop' => $this, 'current' => $current, 'editoropts' => array(), 'options' => $options),
                'post', '', null, $editable);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // Internal methods (implementation details)                                  //
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * Given an array of all assessments of a single submission, calculates the final grade for this submission
     *
     * This calculates the weighted mean of the passed assessment grades. If, however, the submission grade
     * was overridden by a teacher, the gradeover value is returned and the rest of grades are ignored.
     *
     * @param array $assessments of stdclass(->submissionid ->submissiongrade ->gradeover ->weight ->grade)
     * @return void
     */
    protected function aggregate_submission_grades_process(array $assessments) {
        global $DB;

        $submissionid   = null; // the id of the submission being processed
        $current        = null; // the grade currently saved in database
        $finalgrade     = null; // the new grade to be calculated
        $sumgrades      = 0;
        $sumweights     = 0;

        foreach ($assessments as $assessment) {
            if (is_null($submissionid)) {
                // the id is the same in all records, fetch it during the first loop cycle
                $submissionid = $assessment->submissionid;
            }
            if (is_null($current)) {
                // the currently saved grade is the same in all records, fetch it during the first loop cycle
                $current = $assessment->submissiongrade;
            }
            if (is_null($assessment->grade)) {
                // this was not assessed yet
                continue;
            }
            if ($assessment->weight == 0) {
                // this does not influence the calculation
                continue;
            }
            $sumgrades  += $assessment->grade * $assessment->weight;
            $sumweights += $assessment->weight;
        }
        if ($sumweights > 0 and is_null($finalgrade)) {
            $finalgrade = grade_floatval($sumgrades / $sumweights);
        }
        // check if the new final grade differs from the one stored in the database
        if (grade_floats_different($finalgrade, $current)) {
            // we need to save new calculation into the database
            $record = new stdclass();
            $record->id = $submissionid;
            $record->grade = $finalgrade;
            $record->timegraded = time();
            $DB->update_record('workshop_submissions', $record);
        }
    }

    /**
     * Given an array of all assessments done by a single reviewer, calculates the final grading grade
     *
     * This calculates the simple mean of the passed grading grades. If, however, the grading grade
     * was overridden by a teacher, the gradinggradeover value is returned and the rest of grades are ignored.
     *
     * @param array $assessments of stdclass(->reviewerid ->gradinggrade ->gradinggradeover ->aggregationid ->aggregatedgrade)
     * @return void
     */
    protected function aggregate_grading_grades_process(array $assessments) {
        global $DB;

        $reviewerid = null; // the id of the reviewer being processed
        $current    = null; // the gradinggrade currently saved in database
        $finalgrade = null; // the new grade to be calculated
        $agid       = null; // aggregation id
        $sumgrades  = 0;
        $count      = 0;

        foreach ($assessments as $assessment) {
            if (is_null($reviewerid)) {
                // the id is the same in all records, fetch it during the first loop cycle
                $reviewerid = $assessment->reviewerid;
            }
            if (is_null($agid)) {
                // the id is the same in all records, fetch it during the first loop cycle
                $agid = $assessment->aggregationid;
            }
            if (is_null($current)) {
                // the currently saved grade is the same in all records, fetch it during the first loop cycle
                $current = $assessment->aggregatedgrade;
            }
            if (!is_null($assessment->gradinggradeover)) {
                // the grading grade for this assessment is overridden by a teacher
                $sumgrades += $assessment->gradinggradeover;
                $count++;
            } else {
                if (!is_null($assessment->gradinggrade)) {
                    $sumgrades += $assessment->gradinggrade;
                    $count++;
                }
            }
        }
        if ($count > 0) {
            $finalgrade = grade_floatval($sumgrades / $count);
        }
        // check if the new final grade differs from the one stored in the database
        if (grade_floats_different($finalgrade, $current)) {
            // we need to save new calculation into the database
            if (is_null($agid)) {
                // no aggregation record yet
                $record = new stdclass();
                $record->workshopid = $this->id;
                $record->userid = $reviewerid;
                $record->gradinggrade = $finalgrade;
                $record->timegraded = time();
                $DB->insert_record('workshop_aggregations', $record);
            } else {
                $record = new stdclass();
                $record->id = $agid;
                $record->gradinggrade = $finalgrade;
                $record->timegraded = time();
                $DB->update_record('workshop_aggregations', $record);
            }
        }
    }

    /**
     * Given a list of user ids, returns the filtered one containing just ids of users with own submission
     *
     * Example submissions are ignored.
     *
     * @param array $userids
     * @return array
     */
    protected function users_with_submission(array $userids) {
        global $DB;

        if (empty($userids)) {
            return array();
        }
        $userswithsubmission = array();
        list($usql, $uparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $sql = "SELECT id,authorid
                  FROM {workshop_submissions}
                 WHERE example = 0 AND workshopid = :workshopid AND authorid $usql";
        $params = array('workshopid' => $this->id);
        $params = array_merge($params, $uparams);
        $submissions = $DB->get_records_sql($sql, $params);
        foreach ($submissions as $submission) {
            $userswithsubmission[$submission->authorid] = true;
        }

        return $userswithsubmission;
    }

    /**
     * @return array of available workshop phases
     */
    protected function available_phases_list() {
        return array(
            self::PHASE_SETUP       => true,
            self::PHASE_SUBMISSION  => true,
            self::PHASE_ASSESSMENT  => true,
            self::PHASE_EVALUATION  => true,
            self::PHASE_CLOSED      => true,
        );
    }

    /**
     * Converts absolute URL to relative URL needed by {@see add_to_log()}
     *
     * @param moodle_url $url absolute URL
     * @return string
     */
    protected function log_convert_url(moodle_url $fullurl) {
        static $baseurl;

        if (!isset($baseurl)) {
            $baseurl = new moodle_url('/mod/workshop/');
            $baseurl = $baseurl->out();
        }

        return substr($fullurl->out(), strlen($baseurl));
    }
}

////////////////////////////////////////////////////////////////////////////////
// Renderable components
////////////////////////////////////////////////////////////////////////////////

/**
 * Represents the user planner tool
 *
 * Planner contains list of phases. Each phase contains list of tasks. Task is a simple object with
 * title, link and completed (true/false/null logic).
 */
class workshop_user_plan implements renderable {

    /** @var int id of the user this plan is for */
    public $userid;
    /** @var workshop */
    public $workshop;
    /** @var array of (stdclass)tasks */
    public $phases = array();
    /** @var null|array of example submissions to be assessed by the planner owner */
    protected $examples = null;

    /**
     * Prepare an individual workshop plan for the given user.
     *
     * @param workshop $workshop instance
     * @param int $userid whom the plan is prepared for
     */
    public function __construct(workshop $workshop, $userid) {
        global $DB;

        $this->workshop = $workshop;
        $this->userid   = $userid;

        //---------------------------------------------------------
        // * SETUP | submission | assessment | evaluation | closed
        //---------------------------------------------------------
        $phase = new stdclass();
        $phase->title = get_string('phasesetup', 'workshop');
        $phase->tasks = array();
        if (has_capability('moodle/course:manageactivities', $workshop->context, $userid)) {
            $task = new stdclass();
            $task->title = get_string('taskintro', 'workshop');
            $task->link = $workshop->updatemod_url();
            $task->completed = !(trim($workshop->intro) == '');
            $phase->tasks['intro'] = $task;
        }
        if (has_capability('moodle/course:manageactivities', $workshop->context, $userid)) {
            $task = new stdclass();
            $task->title = get_string('taskinstructauthors', 'workshop');
            $task->link = $workshop->updatemod_url();
            $task->completed = !(trim($workshop->instructauthors) == '');
            $phase->tasks['instructauthors'] = $task;
        }
        if (has_capability('mod/workshop:editdimensions', $workshop->context, $userid)) {
            $task = new stdclass();
            $task->title = get_string('editassessmentform', 'workshop');
            $task->link = $workshop->editform_url();
            if ($workshop->grading_strategy_instance()->form_ready()) {
                $task->completed = true;
            } elseif ($workshop->phase > workshop::PHASE_SETUP) {
                $task->completed = false;
            }
            $phase->tasks['editform'] = $task;
        }
        if ($workshop->useexamples and has_capability('mod/workshop:manageexamples', $workshop->context, $userid)) {
            $task = new stdclass();
            $task->title = get_string('prepareexamples', 'workshop');
            if ($DB->count_records('workshop_submissions', array('example' => 1, 'workshopid' => $workshop->id)) > 0) {
                $task->completed = true;
            } elseif ($workshop->phase > workshop::PHASE_SETUP) {
                $task->completed = false;
            }
            $phase->tasks['prepareexamples'] = $task;
        }
        if (empty($phase->tasks) and $workshop->phase == workshop::PHASE_SETUP) {
            // if we are in the setup phase and there is no task (typical for students), let us
            // display some explanation what is going on
            $task = new stdclass();
            $task->title = get_string('undersetup', 'workshop');
            $task->completed = 'info';
            $phase->tasks['setupinfo'] = $task;
        }
        $this->phases[workshop::PHASE_SETUP] = $phase;

        //---------------------------------------------------------
        // setup | * SUBMISSION | assessment | evaluation | closed
        //---------------------------------------------------------
        $phase = new stdclass();
        $phase->title = get_string('phasesubmission', 'workshop');
        $phase->tasks = array();
        if (($workshop->usepeerassessment or $workshop->useselfassessment)
             and has_capability('moodle/course:manageactivities', $workshop->context, $userid)) {
            $task = new stdclass();
            $task->title = get_string('taskinstructreviewers', 'workshop');
            $task->link = $workshop->updatemod_url();
            if (trim($workshop->instructreviewers)) {
                $task->completed = true;
            } elseif ($workshop->phase >= workshop::PHASE_ASSESSMENT) {
                $task->completed = false;
            }
            $phase->tasks['instructreviewers'] = $task;
        }
        if ($workshop->useexamples and $workshop->examplesmode == workshop::EXAMPLES_BEFORE_SUBMISSION
                and has_capability('mod/workshop:submit', $workshop->context, $userid, false)
                    and !has_capability('mod/workshop:manageexamples', $workshop->context, $userid)) {
            $task = new stdclass();
            $task->title = get_string('exampleassesstask', 'workshop');
            $examples = $this->get_examples();
            $a = new stdclass();
            $a->expected = count($examples);
            $a->assessed = 0;
            foreach ($examples as $exampleid => $example) {
                if (!is_null($example->grade)) {
                    $a->assessed++;
                }
            }
            $task->details = get_string('exampleassesstaskdetails', 'workshop', $a);
            if ($a->assessed == $a->expected) {
                $task->completed = true;
            } elseif ($workshop->phase >= workshop::PHASE_ASSESSMENT) {
                $task->completed = false;
            }
            $phase->tasks['examples'] = $task;
        }
        if (has_capability('mod/workshop:submit', $workshop->context, $userid, false)) {
            $task = new stdclass();
            $task->title = get_string('tasksubmit', 'workshop');
            $task->link = $workshop->submission_url();
            if ($DB->record_exists('workshop_submissions', array('workshopid'=>$workshop->id, 'example'=>0, 'authorid'=>$userid))) {
                $task->completed = true;
            } elseif ($workshop->phase >= workshop::PHASE_ASSESSMENT) {
                $task->completed = false;
            } else {
                $task->completed = null;    // still has a chance to submit
            }
            $phase->tasks['submit'] = $task;
        }
        if (has_capability('mod/workshop:allocate', $workshop->context, $userid)) {
            $task = new stdclass();
            $task->title = get_string('allocate', 'workshop');
            $task->link = $workshop->allocation_url();
            $numofauthors = count(get_users_by_capability($workshop->context, 'mod/workshop:submit', 'u.id', '', '', '',
                    '', '', false, true));
            $numofsubmissions = $DB->count_records('workshop_submissions', array('workshopid'=>$workshop->id, 'example'=>0));
            $sql = 'SELECT COUNT(s.id) AS nonallocated
                      FROM {workshop_submissions} s
                 LEFT JOIN {workshop_assessments} a ON (a.submissionid=s.id)
                     WHERE s.workshopid = :workshopid AND s.example=0 AND a.submissionid IS NULL';
            $params['workshopid'] = $workshop->id;
            $numnonallocated = $DB->count_records_sql($sql, $params);
            if ($numofsubmissions == 0) {
                $task->completed = null;
            } elseif ($numnonallocated == 0) {
                $task->completed = true;
            } elseif ($workshop->phase > workshop::PHASE_SUBMISSION) {
                $task->completed = false;
            } else {
                $task->completed = null;    // still has a chance to allocate
            }
            $a = new stdclass();
            $a->expected    = $numofauthors;
            $a->submitted   = $numofsubmissions;
            $a->allocate    = $numnonallocated;
            $task->details  = get_string('allocatedetails', 'workshop', $a);
            unset($a);
            $phase->tasks['allocate'] = $task;

            if ($numofsubmissions < $numofauthors and $workshop->phase >= workshop::PHASE_SUBMISSION) {
                $task = new stdclass();
                $task->title = get_string('someuserswosubmission', 'workshop');
                $task->completed = 'info';
                $phase->tasks['allocateinfo'] = $task;
            }
        }
        if ($workshop->submissionstart) {
            $task = new stdclass();
            $task->title = get_string('submissionstartdatetime', 'workshop', workshop::timestamp_formats($workshop->submissionstart));
            $task->completed = 'info';
            $phase->tasks['submissionstartdatetime'] = $task;
        }
        if ($workshop->submissionend) {
            $task = new stdclass();
            $task->title = get_string('submissionenddatetime', 'workshop', workshop::timestamp_formats($workshop->submissionend));
            $task->completed = 'info';
            $phase->tasks['submissionenddatetime'] = $task;
        }
        if (($workshop->submissionstart < time()) and $workshop->latesubmissions) {
            $task = new stdclass();
            $task->title = get_string('latesubmissionsallowed', 'workshop');
            $task->completed = 'info';
            $phase->tasks['latesubmissionsallowed'] = $task;
        }
        if (isset($phase->tasks['submissionstartdatetime']) or isset($phase->tasks['submissionenddatetime'])) {
            if (has_capability('mod/workshop:ignoredeadlines', $workshop->context, $userid)) {
                $task = new stdclass();
                $task->title = get_string('deadlinesignored', 'workshop');
                $task->completed = 'info';
                $phase->tasks['deadlinesignored'] = $task;
            }
        }
        $this->phases[workshop::PHASE_SUBMISSION] = $phase;

        //---------------------------------------------------------
        // setup | submission | * ASSESSMENT | evaluation | closed
        //---------------------------------------------------------
        $phase = new stdclass();
        $phase->title = get_string('phaseassessment', 'workshop');
        $phase->tasks = array();
        $phase->isreviewer = has_capability('mod/workshop:peerassess', $workshop->context, $userid);
        if ($workshop->useexamples and $workshop->examplesmode == workshop::EXAMPLES_BEFORE_ASSESSMENT
                and $phase->isreviewer and !has_capability('mod/workshop:manageexamples', $workshop->context, $userid)) {
            $task = new stdclass();
            $task->title = get_string('exampleassesstask', 'workshop');
            $examples = $workshop->get_examples_for_reviewer($userid);
            $a = new stdclass();
            $a->expected = count($examples);
            $a->assessed = 0;
            foreach ($examples as $exampleid => $example) {
                if (!is_null($example->grade)) {
                    $a->assessed++;
                }
            }
            $task->details = get_string('exampleassesstaskdetails', 'workshop', $a);
            if ($a->assessed == $a->expected) {
                $task->completed = true;
            } elseif ($workshop->phase > workshop::PHASE_ASSESSMENT) {
                $task->completed = false;
            }
            $phase->tasks['examples'] = $task;
        }
        if (empty($phase->tasks['examples']) or !empty($phase->tasks['examples']->completed)) {
            $phase->assessments = $workshop->get_assessments_by_reviewer($userid);
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
            if ($workshop->usepeerassessment and $numofpeers) {
                $task = new stdclass();
                if ($numofpeerstodo == 0) {
                    $task->completed = true;
                } elseif ($workshop->phase > workshop::PHASE_ASSESSMENT) {
                    $task->completed = false;
                }
                $a = new stdclass();
                $a->total = $numofpeers;
                $a->todo  = $numofpeerstodo;
                $task->title = get_string('taskassesspeers', 'workshop');
                $task->details = get_string('taskassesspeersdetails', 'workshop', $a);
                unset($a);
                $phase->tasks['assesspeers'] = $task;
            }
            if ($workshop->useselfassessment and $numofself) {
                $task = new stdclass();
                if ($numofselftodo == 0) {
                    $task->completed = true;
                } elseif ($workshop->phase > workshop::PHASE_ASSESSMENT) {
                    $task->completed = false;
                }
                $task->title = get_string('taskassessself', 'workshop');
                $phase->tasks['assessself'] = $task;
            }
        }
        if ($workshop->assessmentstart) {
            $task = new stdclass();
            $task->title = get_string('assessmentstartdatetime', 'workshop', workshop::timestamp_formats($workshop->assessmentstart));
            $task->completed = 'info';
            $phase->tasks['assessmentstartdatetime'] = $task;
        }
        if ($workshop->assessmentend) {
            $task = new stdclass();
            $task->title = get_string('assessmentenddatetime', 'workshop', workshop::timestamp_formats($workshop->assessmentend));
            $task->completed = 'info';
            $phase->tasks['assessmentenddatetime'] = $task;
        }
        if (isset($phase->tasks['assessmentstartdatetime']) or isset($phase->tasks['assessmentenddatetime'])) {
            if (has_capability('mod/workshop:ignoredeadlines', $workshop->context, $userid)) {
                $task = new stdclass();
                $task->title = get_string('deadlinesignored', 'workshop');
                $task->completed = 'info';
                $phase->tasks['deadlinesignored'] = $task;
            }
        }
        $this->phases[workshop::PHASE_ASSESSMENT] = $phase;

        //---------------------------------------------------------
        // setup | submission | assessment | * EVALUATION | closed
        //---------------------------------------------------------
        $phase = new stdclass();
        $phase->title = get_string('phaseevaluation', 'workshop');
        $phase->tasks = array();
        if (has_capability('mod/workshop:overridegrades', $workshop->context)) {
            $expected = count($workshop->get_potential_authors(false));
            $calculated = $DB->count_records_select('workshop_submissions',
                    'workshopid = ? AND (grade IS NOT NULL OR gradeover IS NOT NULL)', array($workshop->id));
            $task = new stdclass();
            $task->title = get_string('calculatesubmissiongrades', 'workshop');
            $a = new stdclass();
            $a->expected    = $expected;
            $a->calculated  = $calculated;
            $task->details  = get_string('calculatesubmissiongradesdetails', 'workshop', $a);
            if ($calculated >= $expected) {
                $task->completed = true;
            } elseif ($workshop->phase > workshop::PHASE_EVALUATION) {
                $task->completed = false;
            }
            $phase->tasks['calculatesubmissiongrade'] = $task;

            $expected = count($workshop->get_potential_reviewers(false));
            $calculated = $DB->count_records_select('workshop_aggregations',
                    'workshopid = ? AND gradinggrade IS NOT NULL', array($workshop->id));
            $task = new stdclass();
            $task->title = get_string('calculategradinggrades', 'workshop');
            $a = new stdclass();
            $a->expected    = $expected;
            $a->calculated  = $calculated;
            $task->details  = get_string('calculategradinggradesdetails', 'workshop', $a);
            if ($calculated >= $expected) {
                $task->completed = true;
            } elseif ($workshop->phase > workshop::PHASE_EVALUATION) {
                $task->completed = false;
            }
            $phase->tasks['calculategradinggrade'] = $task;

        } elseif ($workshop->phase == workshop::PHASE_EVALUATION) {
            $task = new stdclass();
            $task->title = get_string('evaluategradeswait', 'workshop');
            $task->completed = 'info';
            $phase->tasks['evaluateinfo'] = $task;
        }
        $this->phases[workshop::PHASE_EVALUATION] = $phase;

        //---------------------------------------------------------
        // setup | submission | assessment | evaluation | * CLOSED
        //---------------------------------------------------------
        $phase = new stdclass();
        $phase->title = get_string('phaseclosed', 'workshop');
        $phase->tasks = array();
        $this->phases[workshop::PHASE_CLOSED] = $phase;

        // Polish data, set default values if not done explicitly
        foreach ($this->phases as $phasecode => $phase) {
            $phase->title       = isset($phase->title)      ? $phase->title     : '';
            $phase->tasks       = isset($phase->tasks)      ? $phase->tasks     : array();
            if ($phasecode == $workshop->phase) {
                $phase->active = true;
            } else {
                $phase->active = false;
            }
            if (!isset($phase->actions)) {
                $phase->actions = array();
            }

            foreach ($phase->tasks as $taskcode => $task) {
                $task->title        = isset($task->title)       ? $task->title      : '';
                $task->link         = isset($task->link)        ? $task->link       : null;
                $task->details      = isset($task->details)     ? $task->details    : '';
                $task->completed    = isset($task->completed)   ? $task->completed  : null;
            }
        }

        // Add phase switching actions
        if (has_capability('mod/workshop:switchphase', $workshop->context, $userid)) {
            foreach ($this->phases as $phasecode => $phase) {
                if (! $phase->active) {
                    $action = new stdclass();
                    $action->type = 'switchphase';
                    $action->url  = $workshop->switchphase_url($phasecode);
                    $phase->actions[] = $action;
                }
            }
        }
    }

    /**
     * Returns example submissions to be assessed by the owner of the planner
     *
     * This is here to cache the DB query because the same list is needed later in view.php
     *
     * @see workshop::get_examples_for_reviewer() for the format of returned value
     * @return array
     */
    public function get_examples() {
        if (is_null($this->examples)) {
            $this->examples = $this->workshop->get_examples_for_reviewer($this->userid);
        }
        return $this->examples;
    }
}

/**
 * Common base class for submissions and example submissions rendering
 *
 * Subclasses of this class convert raw submission record from
 * workshop_submissions table (as returned by {@see workshop::get_submission_by_id()}
 * for example) into renderable objects.
 */
abstract class workshop_submission_base {

    /** @var bool is the submission anonymous (i.e. contains author information) */
    protected $anonymous;

    /* @var array of columns from workshop_submissions that are assigned as properties */
    protected $fields = array();

    /**
     * Copies the properties of the given database record into properties of $this instance
     *
     * @param stdClass $submission full record
     * @param bool $showauthor show the author-related information
     * @param array $options additional properties
     */
    public function __construct(stdClass $submission, $showauthor = false) {

        foreach ($this->fields as $field) {
            if (!property_exists($submission, $field)) {
                throw new coding_exception('Submission record must provide public property ' . $field);
            }
            if (!property_exists($this, $field)) {
                throw new coding_exception('Renderable component must accept public property ' . $field);
            }
            $this->{$field} = $submission->{$field};
        }

        if ($showauthor) {
            $this->anonymous = false;
        } else {
            $this->anonymize();
        }
    }

    /**
     * Unsets all author-related properties so that the renderer does not have access to them
     *
     * Usually this is called by the contructor but can be called explicitely, too.
     */
    public function anonymize() {
        foreach (array('authorid', 'authorfirstname', 'authorlastname',
               'authorpicture', 'authorimagealt', 'authoremail') as $field) {
            unset($this->{$field});
        }
        $this->anonymous = true;
    }

    /**
     * Does the submission object contain author-related information?
     *
     * @return null|boolean
     */
    public function is_anonymous() {
        return $this->anonymous;
    }
}

/**
 * Renderable object containing a basic set of information needed to display the submission summary
 *
 * @see workshop_renderer::render_workshop_submission_summary
 */
class workshop_submission_summary extends workshop_submission_base implements renderable {

    /** @var int */
    public $id;
    /** @var string */
    public $title;
    /** @var string graded|notgraded */
    public $status;
    /** @var int */
    public $timecreated;
    /** @var int */
    public $timemodified;
    /** @var int */
    public $authorid;
    /** @var string */
    public $authorfirstname;
    /** @var string */
    public $authorlastname;
    /** @var int */
    public $authorpicture;
    /** @var string */
    public $authorimagealt;
    /** @var string */
    public $authoremail;
    /** @var moodle_url to display submission */
    public $url;

    /**
     * @var array of columns from workshop_submissions that are assigned as properties
     * of instances of this class
     */
    protected $fields = array(
        'id', 'title', 'timecreated', 'timemodified',
        'authorid', 'authorfirstname', 'authorlastname', 'authorpicture',
        'authorimagealt', 'authoremail');
}

/**
 * Renderable object containing all the information needed to display the submission
 *
 * @see workshop_renderer::render_workshop_submission()
 */
class workshop_submission extends workshop_submission_summary implements renderable {

    /** @var string */
    public $content;
    /** @var int */
    public $contentformat;
    /** @var bool */
    public $contenttrust;
    /** @var array */
    public $attachment;

    /**
     * @var array of columns from workshop_submissions that are assigned as properties
     * of instances of this class
     */
    protected $fields = array(
        'id', 'title', 'timecreated', 'timemodified', 'content', 'contentformat', 'contenttrust',
        'attachment', 'authorid', 'authorfirstname', 'authorlastname', 'authorpicture',
        'authorimagealt', 'authoremail');
}

/**
 * Renderable object containing a basic set of information needed to display the example submission summary
 *
 * @see workshop::prepare_example_summary()
 * @see workshop_renderer::render_workshop_example_submission_summary()
 */
class workshop_example_submission_summary extends workshop_submission_base implements renderable {

    /** @var int */
    public $id;
    /** @var string */
    public $title;
    /** @var string graded|notgraded */
    public $status;
    /** @var stdClass */
    public $gradeinfo;
    /** @var moodle_url */
    public $url;
    /** @var moodle_url */
    public $editurl;
    /** @var string */
    public $assesslabel;
    /** @var moodle_url */
    public $assessurl;
    /** @var bool must be set explicitly by the caller */
    public $editable = false;

    /**
     * @var array of columns from workshop_submissions that are assigned as properties
     * of instances of this class
     */
    protected $fields = array('id', 'title');

    /**
     * Example submissions are always anonymous
     *
     * @return true
     */
    public function is_anonymous() {
        return true;
    }
}

/**
 * Renderable object containing all the information needed to display the example submission
 *
 * @see workshop_renderer::render_workshop_example_submission()
 */
class workshop_example_submission extends workshop_example_submission_summary implements renderable {

    /** @var string */
    public $content;
    /** @var int */
    public $contentformat;
    /** @var bool */
    public $contenttrust;
    /** @var array */
    public $attachment;

    /**
     * @var array of columns from workshop_submissions that are assigned as properties
     * of instances of this class
     */
    protected $fields = array('id', 'title', 'content', 'contentformat', 'contenttrust', 'attachment');
}


/**
 * Common base class for assessments rendering
 *
 * Subclasses of this class convert raw assessment record from
 * workshop_assessments table (as returned by {@see workshop::get_assessment_by_id()}
 * for example) into renderable objects.
 */
abstract class workshop_assessment_base {

    /** @var string the optional title of the assessment */
    public $title = '';

    /** @var workshop_assessment_form $form as returned by {@link workshop_strategy::get_assessment_form()} */
    public $form;

    /** @var moodle_url */
    public $url;

    /** @var float|null the real received grade */
    public $realgrade = null;

    /** @var float the real maximum grade */
    public $maxgrade;

    /** @var stdClass|null reviewer user info */
    public $reviewer = null;

    /** @var stdClass|null assessed submission's author user info */
    public $author = null;

    /** @var array of actions */
    public $actions = array();

    /* @var array of columns that are assigned as properties */
    protected $fields = array();

    /**
     * Copies the properties of the given database record into properties of $this instance
     *
     * The $options keys are: showreviewer, showauthor
     * @param stdClass $assessment full record
     * @param array $options additional properties
     */
    public function __construct(stdClass $record, array $options = array()) {

        $this->validate_raw_record($record);

        foreach ($this->fields as $field) {
            if (!property_exists($record, $field)) {
                throw new coding_exception('Assessment record must provide public property ' . $field);
            }
            if (!property_exists($this, $field)) {
                throw new coding_exception('Renderable component must accept public property ' . $field);
            }
            $this->{$field} = $record->{$field};
        }

        if (!empty($options['showreviewer'])) {
            $this->reviewer = user_picture::unalias($record, null, 'revieweridx', 'reviewer');
        }

        if (!empty($options['showauthor'])) {
            $this->author = user_picture::unalias($record, null, 'authorid', 'author');
        }
    }

    /**
     * Adds a new action
     *
     * @param moodle_url $url action URL
     * @param string $label action label
     * @param string $method get|post
     */
    public function add_action(moodle_url $url, $label, $method = 'get') {

        $action = new stdClass();
        $action->url = $url;
        $action->label = $label;
        $action->method = $method;

        $this->actions[] = $action;
    }

    /**
     * Makes sure that we can cook the renderable component from the passed raw database record
     *
     * @param stdClass $assessment full assessment record
     * @throws coding_exception if the caller passed unexpected data
     */
    protected function validate_raw_record(stdClass $record) {
        // nothing to do here
    }
}


/**
 * Represents a rendarable full assessment
 */
class workshop_assessment extends workshop_assessment_base implements renderable {

    /** @var int */
    public $id;

    /** @var int */
    public $submissionid;

    /** @var int */
    public $weight;

    /** @var int */
    public $timecreated;

    /** @var int */
    public $timemodified;

    /** @var float */
    public $grade;

    /** @var float */
    public $gradinggrade;

    /** @var float */
    public $gradinggradeover;

    /** @var array */
    protected $fields = array('id', 'submissionid', 'weight', 'timecreated',
        'timemodified', 'grade', 'gradinggrade', 'gradinggradeover');
}


/**
 * Represents a renderable training assessment of an example submission
 */
class workshop_example_assessment extends workshop_assessment implements renderable {

    /**
     * @see parent::validate_raw_record()
     */
    protected function validate_raw_record(stdClass $record) {
        if ($record->weight != 0) {
            throw new coding_exception('Invalid weight of example submission assessment');
        }
        parent::validate_raw_record($record);
    }
}


/**
 * Represents a renderable reference assessment of an example submission
 */
class workshop_example_reference_assessment extends workshop_assessment implements renderable {

    /**
     * @see parent::validate_raw_record()
     */
    protected function validate_raw_record(stdClass $record) {
        if ($record->weight != 1) {
            throw new coding_exception('Invalid weight of the reference example submission assessment');
        }
        parent::validate_raw_record($record);
    }
}


/**
 * Renderable message to be displayed to the user
 *
 * Message can contain an optional action link with a label that is supposed to be rendered
 * as a button or a link.
 *
 * @see workshop::renderer::render_workshop_message()
 */
class workshop_message implements renderable {

    const TYPE_INFO     = 10;
    const TYPE_OK       = 20;
    const TYPE_ERROR    = 30;

    /** @var string */
    protected $text = '';
    /** @var int */
    protected $type = self::TYPE_INFO;
    /** @var moodle_url */
    protected $actionurl = null;
    /** @var string */
    protected $actionlabel = '';

    /**
     * @param string $text short text to be displayed
     * @param string $type optional message type info|ok|error
     */
    public function __construct($text = null, $type = self::TYPE_INFO) {
        $this->set_text($text);
        $this->set_type($type);
    }

    /**
     * Sets the message text
     *
     * @param string $text short text to be displayed
     */
    public function set_text($text) {
        $this->text = $text;
    }

    /**
     * Sets the message type
     *
     * @param int $type
     */
    public function set_type($type = self::TYPE_INFO) {
        if (in_array($type, array(self::TYPE_OK, self::TYPE_ERROR, self::TYPE_INFO))) {
            $this->type = $type;
        } else {
            throw new coding_exception('Unknown message type.');
        }
    }

    /**
     * Sets the optional message action
     *
     * @param moodle_url $url to follow on action
     * @param string $label action label
     */
    public function set_action(moodle_url $url, $label) {
        $this->actionurl    = $url;
        $this->actionlabel  = $label;
    }

    /**
     * Returns message text with HTML tags quoted
     *
     * @return string
     */
    public function get_message() {
        return s($this->text);
    }

    /**
     * Returns message type
     *
     * @return int
     */
    public function get_type() {
        return $this->type;
    }

    /**
     * Returns action URL
     *
     * @return moodle_url|null
     */
    public function get_action_url() {
        return $this->actionurl;
    }

    /**
     * Returns action label
     *
     * @return string
     */
    public function get_action_label() {
        return $this->actionlabel;
    }
}

/**
 * Renderable output of submissions allocation process
 */
class workshop_allocation_init_result implements renderable {

    /** @var workshop_message */
    protected $message;
    /** @var array of steps */
    protected $info = array();
    /** @var moodle_url */
    protected $continue;

    /**
     * Supplied argument can be either integer status code or an array of string messages. Messages
     * in a array can have optional prefix or prefixes, using '::' as delimiter. Prefixes determine
     * the type of the message and may influence its visualisation.
     *
     * @param mixed $result int|array returned by {@see workshop_allocator::init()}
     * @param moodle_url to continue
     */
    public function __construct($result, moodle_url $continue) {

        if ($result === workshop::ALLOCATION_ERROR) {
            $this->message = new workshop_message(get_string('allocationerror', 'workshop'), workshop_message::TYPE_ERROR);
        } else {
            $this->message = new workshop_message(get_string('allocationdone', 'workshop'), workshop_message::TYPE_OK);
            if (is_array($result)) {
                $this->info = $result;
            }
        }

        $this->continue = $continue;
    }

    /**
     * @return workshop_message instance to render
     */
    public function get_message() {
        return $this->message;
    }

    /**
     * @return array of strings with allocation process details
     */
    public function get_info() {
        return $this->info;
    }

    /**
     * @return moodle_url where the user shoudl continue
     */
    public function get_continue_url() {
        return $this->continue;
    }
}

/**
 * Renderable component containing all the data needed to display the grading report
 */
class workshop_grading_report implements renderable {

    /** @var stdClass returned by {@see workshop::prepare_grading_report_data()} */
    protected $data;
    /** @var stdClass rendering options */
    protected $options;

    /**
     * Grades in $data must be already rounded to the set number of decimals or must be null
     * (in which later case, the [mod_workshop,nullgrade] string shall be displayed)
     *
     * @param stdClass $data prepared by {@link workshop::prepare_grading_report_data()}
     * @param stdClass $options display options (showauthornames, showreviewernames, sortby, sorthow, showsubmissiongrade, showgradinggrade)
     */
    public function __construct(stdClass $data, stdClass $options) {
        $this->data     = $data;
        $this->options  = $options;
    }

    /**
     * @return stdClass grading report data
     */
    public function get_data() {
        return $this->data;
    }

    /**
     * @return stdClass rendering options
     */
    public function get_options() {
        return $this->options;
    }
}


/**
 * Base class for renderable feedback for author and feedback for reviewer
 */
abstract class workshop_feedback {

    /** @var stdClass the user info */
    protected $provider = null;

    /** @var string the feedback text */
    protected $content = null;

    /** @var int format of the feedback text */
    protected $format = null;

    /**
     * @return stdClass the user info
     */
    public function get_provider() {

        if (is_null($this->provider)) {
            throw new coding_exception('Feedback provider not set');
        }

        return $this->provider;
    }

    /**
     * @return string the feedback text
     */
    public function get_content() {

        if (is_null($this->content)) {
            throw new coding_exception('Feedback content not set');
        }

        return $this->content;
    }

    /**
     * @return int format of the feedback text
     */
    public function get_format() {

        if (is_null($this->format)) {
            throw new coding_exception('Feedback text format not set');
        }

        return $this->format;
    }
}


/**
 * Renderable feedback for the author of submission
 */
class workshop_feedback_author extends workshop_feedback implements renderable {

    /**
     * Extracts feedback from the given submission record
     *
     * @param stdClass $submission record as returned by {@see self::get_submission_by_id()}
     */
    public function __construct(stdClass $submission) {

        $this->provider = user_picture::unalias($submission, null, 'gradeoverbyx', 'gradeoverby');
        $this->content  = $submission->feedbackauthor;
        $this->format   = $submission->feedbackauthorformat;
    }
}


/**
 * Renderable feedback for the reviewer
 */
class workshop_feedback_reviewer extends workshop_feedback implements renderable {

    /**
     * Extracts feedback from the given assessment record
     *
     * @param stdClass $assessment record as returned by eg {@see self::get_assessment_by_id()}
     */
    public function __construct(stdClass $assessment) {

        $this->provider = user_picture::unalias($assessment, null, 'gradinggradeoverbyx', 'overby');
        $this->content  = $assessment->feedbackreviewer;
        $this->format   = $assessment->feedbackreviewerformat;
    }
}
