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
 * @package    mod_workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/lib.php');     // we extend this library here
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

    /** error status of the {@link self::add_allocation()} */
    const ALLOCATION_EXISTS             = -9999;

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

    /** @var stdclass workshop record from database */
    public $dbrecord;

    /** @var cm_info course module record */
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

    /** @var bool optional feature: students perform peer assessment of others' work (deprecated, consider always enabled) */
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

     /** @var string list of allowed file types that are allowed to be embedded into submission */
    public $submissionfiletypes = null;

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

    /** @var bool automatically switch to the assessment phase after the submissions deadline */
    public $phaseswitchassessment;

    /** @var string conclusion text to be displayed at the end of the activity */
    public $conclusion;

    /** @var int format of the conclusion text */
    public $conclusionformat;

    /** @var int the mode of the overall feedback */
    public $overallfeedbackmode;

    /** @var int maximum number of overall feedback attachments */
    public $overallfeedbackfiles;

    /** @var string list of allowed file types that can be attached to the overall feedback */
    public $overallfeedbackfiletypes = null;

    /** @var int maximum size of one file attached to the overall feedback */
    public $overallfeedbackmaxbytes;

    /** @var int Should the submission form show the text field? */
    public $submissiontypetext;

    /** @var int Should the submission form show the file attachment field? */
    public $submissiontypefile;

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
     * @var array It gets initialised in init_initial_bar, and may have keys 'i_first' and 'i_last' depending on what is selected.
     */
    protected $initialbarprefs = [];

    /**
     * Initializes the workshop API instance using the data from DB
     *
     * Makes deep copy of all passed records properties.
     *
     * For unit testing only, $cm and $course may be set to null. This is so that
     * you can test without having any real database objects if you like. Not all
     * functions will work in this situation.
     *
     * @param stdClass $dbrecord Workshop instance data from {workshop} table
     * @param stdClass|cm_info $cm Course module record
     * @param stdClass $course Course record from {course} table
     * @param stdClass $context The context of the workshop instance
     */
    public function __construct(stdclass $dbrecord, $cm, $course, ?stdclass $context=null) {
        $this->dbrecord = $dbrecord;
        foreach ($this->dbrecord as $field => $value) {
            if (property_exists('workshop', $field)) {
                $this->{$field} = $value;
            }
        }

        $this->strategy = clean_param($this->strategy, PARAM_PLUGIN);
        $this->evaluation = clean_param($this->evaluation, PARAM_PLUGIN);

        if (is_null($cm) || is_null($course)) {
            throw new coding_exception('Must specify $cm and $course');
        }
        $this->course = $course;
        if ($cm instanceof cm_info) {
            $this->cm = $cm;
        } else {
            $modinfo = get_fast_modinfo($course);
            $this->cm = $modinfo->get_cm($cm->id);
        }
        if (is_null($context)) {
            $this->context = context_module::instance($this->cm->id);
        } else {
            $this->context = $context;
        }
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
        $installed = core_component::get_plugin_list('workshopallocation');
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
        $installed = core_component::get_plugin_list('workshopform');
        $forms = array();
        foreach ($installed as $strategy => $strategypath) {
            if (file_exists($strategypath . '/lib.php')) {
                $forms[$strategy] = get_string('pluginname', 'workshopform_' . $strategy);
            }
        }
        return $forms;
    }

    /**
     * Returns the list of available grading evaluation methods
     *
     * @return array of (string)name => (string)localized title
     */
    public static function available_evaluators_list() {
        $evals = array();
        foreach (core_component::get_plugin_list_with_file('workshopeval', 'lib.php', false) as $eval => $evalpath) {
            $evals[$eval] = get_string('pluginname', 'workshopeval_' . $eval);
        }
        return $evals;
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

    /**
     * Converts the argument into an array (list) of file extensions.
     *
     * The list can be separated by whitespace, end of lines, commas colons and semicolons.
     * Empty values are not returned. Values are converted to lowercase.
     * Duplicates are removed. Glob evaluation is not supported.
     *
     * @deprecated since Moodle 3.4 MDL-56486 - please use the {@link core_form\filetypes_util}
     * @param string|array $extensions list of file extensions
     * @return array of strings
     */
    public static function normalize_file_extensions($extensions) {

        debugging('The method workshop::normalize_file_extensions() is deprecated.
            Please use the methods provided by the \core_form\filetypes_util class.', DEBUG_DEVELOPER);

        if ($extensions === '') {
            return array();
        }

        if (!is_array($extensions)) {
            $extensions = preg_split('/[\s,;:"\']+/', $extensions, -1, PREG_SPLIT_NO_EMPTY);
        }

        foreach ($extensions as $i => $extension) {
            $extension = str_replace('*.', '', $extension);
            $extension = strtolower($extension);
            $extension = ltrim($extension, '.');
            $extension = trim($extension);
            $extensions[$i] = $extension;
        }

        foreach ($extensions as $i => $extension) {
            if (strpos($extension, '*') !== false or strpos($extension, '?') !== false) {
                unset($extensions[$i]);
            }
        }

        $extensions = array_filter($extensions, 'strlen');
        $extensions = array_keys(array_flip($extensions));

        foreach ($extensions as $i => $extension) {
            $extensions[$i] = '.'.$extension;
        }

        return $extensions;
    }

    /**
     * Cleans the user provided list of file extensions.
     *
     * @deprecated since Moodle 3.4 MDL-56486 - please use the {@link core_form\filetypes_util}
     * @param string $extensions
     * @return string
     */
    public static function clean_file_extensions($extensions) {

        debugging('The method workshop::clean_file_extensions() is deprecated.
            Please use the methods provided by the \core_form\filetypes_util class.', DEBUG_DEVELOPER);

        $extensions = self::normalize_file_extensions($extensions);

        foreach ($extensions as $i => $extension) {
            $extensions[$i] = ltrim($extension, '.');
        }

        return implode(', ', $extensions);
    }

    /**
     * Check given file types and return invalid/unknown ones.
     *
     * Empty allowlist is interpretted as "any extension is valid".
     *
     * @deprecated since Moodle 3.4 MDL-56486 - please use the {@link core_form\filetypes_util}
     * @param string|array $extensions list of file extensions
     * @param string|array $allowlist list of valid extensions
     * @return array list of invalid extensions not found in the allowlist
     */
    public static function invalid_file_extensions($extensions, $allowlist) {

        debugging('The method workshop::invalid_file_extensions() is deprecated.
            Please use the methods provided by the \core_form\filetypes_util class.', DEBUG_DEVELOPER);

        $extensions = self::normalize_file_extensions($extensions);
        $allowlist = self::normalize_file_extensions($allowlist);

        if (empty($extensions) or empty($allowlist)) {
            return array();
        }

        // Return those items from $extensions that are not present in $allowlist.
        return array_keys(array_diff_key(array_flip($extensions), array_flip($allowlist)));
    }

    /**
     * Is the file have allowed to be uploaded to the workshop?
     *
     * Empty allowlist is interpretted as "any file type is allowed" rather
     * than "no file can be uploaded".
     *
     * @deprecated since Moodle 3.4 MDL-56486 - please use the {@link core_form\filetypes_util}
     * @param string $filename the file name
     * @param string|array $allowlist list of allowed file extensions
     * @return false
     */
    public static function is_allowed_file_type($filename, $allowlist) {

        debugging('The method workshop::is_allowed_file_type() is deprecated.
            Please use the methods provided by the \core_form\filetypes_util class.', DEBUG_DEVELOPER);

        $allowlist = self::normalize_file_extensions($allowlist);

        if (empty($allowlist)) {
            return true;
        }

        $haystack = strrev(trim(strtolower($filename)));

        foreach ($allowlist as $extension) {
            if (strpos($haystack, strrev($extension)) === 0) {
                // The file name ends with the extension.
                return true;
            }
        }

        return false;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // Workshop API                                                               //
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * Fetches all enrolled users with the capability mod/workshop:submit in the current workshop
     *
     * The returned objects contain properties required by user_picture and are ordered by lastname, firstname.
     * Only users with the active enrolment are returned.
     *
     * @param bool $musthavesubmission if true, return only users who have already submitted
     * @param int $groupid 0 means ignore groups, any other value limits the result by group id
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set)
     * @param int $limitnum return a subset containing this number of records (optional, required if $limitfrom is set)
     * @return array array[userid] => stdClass
     */
    public function get_potential_authors($musthavesubmission=true, $groupid=0, $limitfrom=0, $limitnum=0) {
        return $this->get_users_with_capability(['mod/workshop:submit'], $musthavesubmission, $groupid, $limitfrom, $limitnum);
    }

    /**
     * Returns the total number of users that would be fetched by {@link self::get_potential_authors()}
     *
     * @param bool $musthavesubmission if true, count only users who have already submitted
     * @param int $groupid 0 means ignore groups, any other value limits the result by group id
     * @return int
     */
    public function count_potential_authors($musthavesubmission=true, $groupid=0) {
        return count($this->get_users_with_capability(['mod/workshop:submit'], $musthavesubmission, $groupid));
    }

    /**
     * Fetches all enrolled users with the capability mod/workshop:peerassess in the current workshop
     *
     * The returned objects contain properties required by user_picture and are ordered by lastname, firstname.
     * Only users with the active enrolment are returned.
     *
     * @param bool $musthavesubmission if true, return only users who have already submitted
     * @param int $groupid 0 means ignore groups, any other value limits the result by group id
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set)
     * @param int $limitnum return a subset containing this number of records (optional, required if $limitfrom is set)
     * @return array array[userid] => stdClass
     */
    public function get_potential_reviewers($musthavesubmission=false, $groupid=0, $limitfrom=0, $limitnum=0) {
        return $this->get_users_with_capability(['mod/workshop:peerassess'], $musthavesubmission, $groupid, $limitfrom, $limitnum);
    }

    /**
     * Returns the total number of users that would be fetched by {@link self::get_potential_reviewers()}
     *
     * @param bool $musthavesubmission if true, count only users who have already submitted
     * @param int $groupid 0 means ignore groups, any other value limits the result by group id
     * @return int
     */
    public function count_potential_reviewers($musthavesubmission=false, $groupid=0) {
        return count($this->get_users_with_capability(['mod/workshop:peerassess'], $musthavesubmission, $groupid));

    }

    /**
     * Fetches all enrolled users that are authors or reviewers (or both) in the current workshop
     *
     * The returned objects contain properties required by user_picture and are ordered by lastname, firstname.
     * Only users with the active enrolment are returned.
     *
     * @see self::get_potential_authors()
     * @see self::get_potential_reviewers()
     * @param bool $musthavesubmission if true, return only users who have already submitted
     * @param int $groupid 0 means ignore groups, any other value limits the result by group id
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set)
     * @param int $limitnum return a subset containing this number of records (optional, required if $limitfrom is set)
     * @return array array[userid] => stdClass
     */
    public function get_participants($musthavesubmission=false, $groupid=0, $limitfrom=0, $limitnum=0) {

        // Get any users who have either of these 2 capabilities on the activity.
        return $this->get_users_with_capability(['mod/workshop:submit', 'mod/workshop:peerassess'],
            $musthavesubmission, $groupid, $limitfrom, $limitnum);

    }

    /**
     * Returns the total number of records that would be returned by {@link self::get_participants()}
     *
     * @param bool $musthavesubmission if true, return only users who have already submitted
     * @param int $groupid 0 means ignore groups, any other value limits the result by group id
     * @return int
     */
    public function count_participants($musthavesubmission=false, $groupid=0) {
        return count($this->get_participants($musthavesubmission, $groupid));
    }

    /**
     * Returns the total number of participants in the workshop
     *
     * This is a convenience method that uses {@see count_enrolled_users()} to count all users
     * with the capabilities mod/workshop:submit and mod/workshop:peerassess.
     *
     * @param array $groupids If not empty, return only participants in the specified groups
     * @return int
     */
    public function count_all_participants(array $groupids = []): int {
        return count_enrolled_users(
            context: $this->context,
            withcapability: ['mod/workshop:submit', 'mod/workshop:peerassess'],
            groupids: $groupids,
            onlyactive: true,
        );
    }

    /**
     * Checks if the given user is an actively enrolled participant in the workshop
     *
     * @param int $userid, defaults to the current $USER
     * @return boolean
     */
    public function is_participant($userid=null) {

        global $USER;

        if (is_null($userid)) {
            $userid = $USER->id;
        }

        // Get the participants on this activity and see if the user exists in that array.
        $participants = $this->get_participants();
        return (array_key_exists($userid, $participants));

    }

    /**
     * Groups the given users by the group membership
     *
     * This takes the module grouping settings into account. If a grouping is
     * set, returns only groups withing the course module grouping. Always
     * returns group [0] with all the given users.
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
        if ($this->cm->groupingid) {
            // Group workshop set to specified grouping - only consider groups
            // within this grouping, and leave out users who aren't members of
            // this grouping.
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
     * Returns the total number of records that would be returned by {@link self::get_submissions()}
     *
     * @deprecated since Moodle 5.1
     * @todo Remove this method in Moodle 6.0 (MDL-86399).
     *
     * @param mixed $authorid int|array|'all' If set to [array of] integer, return submission[s] of the given user[s] only
     * @param int $groupid If non-zero, return only submissions by authors in the specified group
     * @return int number of records
     */
    #[\core\attribute\deprecated(
        replacement: 'count_all_submissions',
        since: '5.1',
        mdl: 'MDL-84809',
    )]
    public function count_submissions($authorid='all', $groupid=0) {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);

        $authorids = [];
        $groupids = [];

        if (is_array($authorid)) {
            $authorids = $authorid;
        } else if ($authorid !== 'all') {
            $authorids[] = $authorid;
        }

        if ($groupid) {
            $groupids[] = $groupid;
        }

        return $this->count_all_submissions($authorids, $groupids);
    }

    /**
     * Returns the total number of submissions in the workshop
     *
     * @param array $authorids If not empty, return only submissions by the given authors
     * @param array $groupids If not empty, return only submissions by authors in the specified groups
     * @return int Number of submissions
     */
    public function count_all_submissions(array $authorids = [], array $groupids = []): int {
        $db = \core\di::get(\moodle_database::class);

        $authorwhere = '';
        $authorparams = [];
        if ($authorids) {
            [$authorsql, $authorparams] = $db->get_in_or_equal($authorids, SQL_PARAMS_NAMED);
            $authorwhere = " AND authorid $authorsql";
        }

        $groupjoin = '';
        $groupwhere = '';
        $groupparams = [];
        if ($groupids) {
            $groupsqljoin = groups_get_members_join($groupids, 'u.id', $this->context);
            $groupjoin = $groupsqljoin->joins;
            $groupwhere = !empty($groupsqljoin->wheres) ? " AND {$groupsqljoin->wheres}" : '';
            $groupparams = $groupsqljoin->params;
        }

        $sql = "SELECT COUNT(ws.id)
                  FROM {workshop_submissions} ws
                  JOIN {user} u ON (ws.authorid = u.id)
                $groupjoin
                 WHERE ws.example = 0 AND ws.workshopid = :workshopid
                $authorwhere
                $groupwhere";

        return $db->count_records_sql(
            $sql,
            [
                'workshopid' => $this->id,
                ...$authorparams,
                ...$groupparams,
            ],
        );
    }

    /**
     * Returns the total number of assessments in the workshop.
     *
     * @deprecated since Moodle 5.1
     * @todo Remove this method in Moodle 6.0 (MDL-86399).
     *
     * @param bool $onlygraded If true, count only graded assessments
     * @param int|null $groupid If not null, return only assessments by reviewers in the specified group
     * @return int Number of assessments
     */
    #[\core\attribute\deprecated(
        replacement: 'count_all_assessments',
        since: '5.1',
        mdl: 'MDL-84809',
    )]
    public function count_assessments(bool $onlygraded = false, ?int $groupid = null): int {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);

        $groupids = [];

        if ($groupid) {
            $groupids[] = $groupid;
        }

        return $this->count_all_assessments($onlygraded, $groupids);
    }

    /**
     * Returns the total number of assessments in the workshop.
     *
     * @param bool $onlygraded If true, count only graded assessments
     * @param array $groupids If not empty, return only assessments by reviewers in the specified groups
     * @return int Number of assessments
     */
    public function count_all_assessments(bool $onlygraded = false, array $groupids = []): int {
        $db = \core\di::get(\moodle_database::class);

        $groupjoin = '';
        $groupwhere = '';
        $groupparams = [];
        if ($groupids) {
            $groupsqljoin = groups_get_members_join($groupids, 'u.id', $this->context);
            $groupjoin = $groupsqljoin->joins;
            $groupwhere = !empty($groupsqljoin->wheres) ? " AND {$groupsqljoin->wheres}" : '';
            $groupparams = $groupsqljoin->params;
        }

        $onlygradedwhere = '';
        if ($onlygraded) {
            $onlygradedwhere = " AND s.grade IS NOT NULL";
        }

        $sql = "SELECT COUNT(s.id)
                  FROM {workshop_assessments} s
                  JOIN {workshop_submissions} ws ON (s.submissionid = ws.id)
                  JOIN {user} u ON (ws.authorid = u.id)
                  JOIN {workshop} w ON (ws.workshopid = w.id)
                $groupjoin
                 WHERE w.id = :workshopid
                $onlygradedwhere
                $groupwhere";

        return $db->count_records_sql(
            $sql,
            [
                'workshopid' => $this->id,
                ...$groupparams,
            ],
        );
    }


    /**
     * Returns submissions from this workshop
     *
     * Fetches data from {workshop_submissions} and adds some useful information from other
     * tables. Does not return textual fields to prevent possible memory lack issues.
     *
     * @see self::count_all_submissions()
     * @param mixed $authorid int|array|'all' If set to [array of] integer, return submission[s] of the given user[s] only
     * @param int $groupid If non-zero, return only submissions by authors in the specified group
     * @param int $limitfrom Return a subset of records, starting at this point (optional)
     * @param int $limitnum Return a subset containing this many records in total (optional, required if $limitfrom is set)
     * @return array of records or an empty array
     */
    public function get_submissions($authorid='all', $groupid=0, $limitfrom=0, $limitnum=0) {
        global $DB;

        $userfieldsapi = \core_user\fields::for_userpic();
        $authorfields = $userfieldsapi->get_sql('u', false, 'author', 'authoridx', false)->selects;
        $gradeoverbyfields = $userfieldsapi->get_sql('t', false, 'over', 'gradeoverbyx', false)->selects;
        $params            = array('workshopid' => $this->id);
        $sql = "SELECT s.id, s.workshopid, s.example, s.authorid, s.timecreated, s.timemodified,
                       s.title, s.grade, s.gradeover, s.gradeoverby, s.published,
                       $authorfields, $gradeoverbyfields
                  FROM {workshop_submissions} s
                  JOIN {user} u ON (s.authorid = u.id)";
        if ($groupid) {
            $sql .= " JOIN {groups_members} gm ON (gm.userid = u.id AND gm.groupid = :groupid)";
            $params['groupid'] = $groupid;
        }
        $sql .= " LEFT JOIN {user} t ON (s.gradeoverby = t.id)
                 WHERE s.example = 0 AND s.workshopid = :workshopid";

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
        list($sort, $sortparams) = users_order_by_sql('u');
        $sql .= " ORDER BY $sort";

        return $DB->get_records_sql($sql, array_merge($params, $sortparams), $limitfrom, $limitnum);
    }

    /**
     * Returns submissions from this workshop that are viewable by the current user (except example submissions).
     *
     * @param mixed $authorid int|array If set to [array of] integer, return submission[s] of the given user[s] only
     * @param int $groupid If non-zero, return only submissions by authors in the specified group. 0 for all groups.
     * @param int $limitfrom Return a subset of records, starting at this point (optional)
     * @param int $limitnum Return a subset containing this many records in total (optional, required if $limitfrom is set)
     * @return array of records and the total submissions count
     * @since  Moodle 3.4
     */
    public function get_visible_submissions($authorid = 0, $groupid = 0, $limitfrom = 0, $limitnum = 0) {
        global $DB, $USER;

        $submissions = array();
        $select = "SELECT s.*";
        $selectcount = "SELECT COUNT(s.id)";
        $from = " FROM {workshop_submissions} s";
        $params = array('workshopid' => $this->id);

        // Check if the passed group (or all groups when groupid is 0) is visible by the current user.
        if (!groups_group_visible($groupid, $this->course, $this->cm)) {
            return array($submissions, 0);
        }

        if ($groupid) {
            $from .= " JOIN {groups_members} gm ON (gm.userid = s.authorid AND gm.groupid = :groupid)";
            $params['groupid'] = $groupid;
        }
        $where = " WHERE s.workshopid = :workshopid AND s.example = 0";

        if (!has_capability('mod/workshop:viewallsubmissions', $this->context)) {
            // Check published submissions.
            $workshopclosed = $this->phase == self::PHASE_CLOSED;
            $canviewpublished = has_capability('mod/workshop:viewpublishedsubmissions', $this->context);
            if ($workshopclosed && $canviewpublished) {
                $published = " OR s.published = 1";
            } else {
                $published = '';
            }

            // Always get submissions I did or I provided feedback to.
            $where .= " AND (s.authorid = :authorid OR s.gradeoverby = :graderid $published)";
            $params['authorid'] = $USER->id;
            $params['graderid'] = $USER->id;
        }

        // Now, user filtering.
        if (!empty($authorid)) {
            list($usql, $uparams) = $DB->get_in_or_equal($authorid, SQL_PARAMS_NAMED);
            $where .= " AND s.authorid $usql";
            $params = array_merge($params, $uparams);
        }

        $order = " ORDER BY s.timecreated";

        $totalcount = $DB->count_records_sql($selectcount.$from.$where, $params);
        if ($totalcount) {
            $submissions = $DB->get_records_sql($select.$from.$where.$order, $params, $limitfrom, $limitnum);
        }
        return array($submissions, $totalcount);
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
        $userfieldsapi = \core_user\fields::for_userpic();
        $authorfields = $userfieldsapi->get_sql('u', false, 'author', 'authoridx', false)->selects;
        $gradeoverbyfields = $userfieldsapi->get_sql('g', false, 'gradeoverby', 'gradeoverbyx', false)->selects;
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
        $userfieldsapi = \core_user\fields::for_userpic();
        $authorfields = $userfieldsapi->get_sql('u', false, 'author', 'authoridx', false)->selects;
        $gradeoverbyfields = $userfieldsapi->get_sql('g', false, 'gradeoverby', 'gradeoverbyx', false)->selects;
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

        $userfieldsapi = \core_user\fields::for_userpic();
        $authorfields = $userfieldsapi->get_sql('u', false, 'author', 'authoridx', false)->selects;
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

        $submission         = new workshop_submission($this, $record, $showauthor);
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

        $summary        = new workshop_submission_summary($this, $record, $showauthor);
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

        $example = new workshop_example_submission($this, $record);

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

        $summary = new workshop_example_submission_summary($this, $example);

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

        $assessment             = new workshop_assessment($this, $record, $options);
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

        $assessment             = new workshop_example_assessment($this, $record, $options);
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

        $assessment             = new workshop_example_reference_assessment($this, $record, $options);
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

        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'mod_workshop', 'submission_content', $submission->id);
        $fs->delete_area_files($this->context->id, 'mod_workshop', 'submission_attachment', $submission->id);

        $DB->delete_records('workshop_submissions', array('id' => $submission->id));

        // Event information.
        $params = array(
            'context' => $this->context,
            'courseid' => $this->course->id,
            'relateduserid' => $submission->authorid,
            'other' => array(
                'submissiontitle' => $submission->title
            )
        );
        $params['objectid'] = $submission->id;
        $event = \mod_workshop\event\submission_deleted::create($params);
        $event->add_record_snapshot('workshop', $this->dbrecord);
        $event->trigger();
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

        $userfieldsapi = \core_user\fields::for_userpic();
        $reviewerfields = $userfieldsapi->get_sql('reviewer', false, '', 'revieweridx', false)->selects;
        $authorfields = $userfieldsapi->get_sql('author', false, 'author', 'authorid', false)->selects;
        $overbyfields = $userfieldsapi->get_sql('overby', false, 'overby', 'gradinggradeoverbyx', false)->selects;
        list($sort, $params) = users_order_by_sql('reviewer');
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
              ORDER BY $sort";
        $params['workshopid'] = $this->id;

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

        $userfieldsapi = \core_user\fields::for_userpic();
        $reviewerfields = $userfieldsapi->get_sql('reviewer', false, 'reviewer', 'revieweridx', false)->selects;
        $authorfields = $userfieldsapi->get_sql('author', false, 'author', 'authorid', false)->selects;
        $overbyfields = $userfieldsapi->get_sql('overby', false, 'overby', 'gradinggradeoverbyx', false)->selects;
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

        $userfieldsapi = \core_user\fields::for_userpic();
        $reviewerfields = $userfieldsapi->get_sql('reviewer', false, 'reviewer', 'revieweridx', false)->selects;
        $authorfields = $userfieldsapi->get_sql('author', false, 'author', 'authorid', false)->selects;
        $overbyfields = $userfieldsapi->get_sql('overby', false, 'overby', 'gradinggradeoverbyx', false)->selects;
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

        $userfieldsapi = \core_user\fields::for_userpic();
        $reviewerfields = $userfieldsapi->get_sql('reviewer', false, 'reviewer', 'revieweridx', false)->selects;
        $overbyfields = $userfieldsapi->get_sql('overby', false, 'overby', 'gradinggradeoverbyx', false)->selects;
        list($sort, $params) = users_order_by_sql('reviewer');
        $sql = "SELECT a.*, s.title, $reviewerfields, $overbyfields
                  FROM {workshop_assessments} a
            INNER JOIN {user} reviewer ON (a.reviewerid = reviewer.id)
            INNER JOIN {workshop_submissions} s ON (a.submissionid = s.id)
             LEFT JOIN {user} overby ON (a.gradinggradeoverby = overby.id)
                 WHERE s.example = 0 AND s.id = :submissionid AND s.workshopid = :workshopid
              ORDER BY $sort";
        $params['submissionid'] = $submissionid;
        $params['workshopid']   = $this->id;

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

        $userfieldsapi = \core_user\fields::for_userpic();
        $reviewerfields = $userfieldsapi->get_sql('reviewer', false, 'reviewer', 'revieweridx', false)->selects;
        $authorfields = $userfieldsapi->get_sql('author', false, 'author', 'authorid', false)->selects;
        $overbyfields = $userfieldsapi->get_sql('overby', false, 'overby', 'gradinggradeoverbyx', false)->selects;
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
     * Get allocated assessments not graded yet by the given reviewer
     *
     * @see self::get_assessments_by_reviewer()
     * @param int $reviewerid the reviewer id
     * @param null|int|array $exclude optional assessment id (or list of them) to be excluded
     * @return array
     */
    public function get_pending_assessments_by_reviewer($reviewerid, $exclude = null) {

        $assessments = $this->get_assessments_by_reviewer($reviewerid);

        foreach ($assessments as $id => $assessment) {
            if (!is_null($assessment->grade)) {
                unset($assessments[$id]);
                continue;
            }
            if (!empty($exclude)) {
                if (is_array($exclude) and in_array($id, $exclude)) {
                    unset($assessments[$id]);
                    continue;
                } else if ($id == $exclude) {
                    unset($assessments[$id]);
                    continue;
                }
            }
        }

        return $assessments;
    }

    /**
     * Allocate a submission to a user for review
     *
     * @param stdClass $submission Submission object with at least id property
     * @param int $reviewerid User ID
     * @param int $weight of the new assessment, from 0 to 16
     * @param bool $bulk repeated inserts into DB expected
     * @return int ID of the new assessment or an error code {@link self::ALLOCATION_EXISTS} if the allocation already exists
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
        $assessment->feedbackauthorformat   = editors_get_preferred_format();
        $assessment->feedbackreviewerformat = editors_get_preferred_format();

        return $DB->insert_record('workshop_assessments', $assessment, true, $bulk);
    }

    /**
     * Delete assessment record or records.
     *
     * Removes associated records from the workshop_grades table, too.
     *
     * @param int|array $id assessment id or array of assessments ids
     * @todo Give grading strategy plugins a chance to clean up their data, too.
     * @return bool true
     */
    public function delete_assessment($id) {
        global $DB;

        if (empty($id)) {
            return true;
        }

        $fs = get_file_storage();

        if (is_array($id)) {
            $DB->delete_records_list('workshop_grades', 'assessmentid', $id);
            foreach ($id as $itemid) {
                $fs->delete_area_files($this->context->id, 'mod_workshop', 'overallfeedback_content', $itemid);
                $fs->delete_area_files($this->context->id, 'mod_workshop', 'overallfeedback_attachment', $itemid);
            }
            $DB->delete_records_list('workshop_assessments', 'id', $id);

        } else {
            $DB->delete_records('workshop_grades', array('assessmentid' => $id));
            $fs->delete_area_files($this->context->id, 'mod_workshop', 'overallfeedback_content', $id);
            $fs->delete_area_files($this->context->id, 'mod_workshop', 'overallfeedback_attachment', $id);
            $DB->delete_records('workshop_assessments', array('id' => $id));
        }

        return true;
    }

    /**
     * Returns instance of grading strategy class
     *
     * @return stdclass Instance of a grading strategy
     */
    public function grading_strategy_instance() {
        global $CFG;    // because we require other libs here

        if (is_null($this->strategyinstance)) {
            if (empty($this->strategy)) {
                throw new coding_exception('Unknown grading strategy');
            }
            $strategylib = __DIR__ . '/form/' . $this->strategy . '/lib.php';
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
     * Sets the current evaluation method to the given plugin.
     *
     * @param string $method the name of the workshopeval subplugin
     * @return bool true if successfully set
     * @throws coding_exception if attempting to set a non-installed evaluation method
     */
    public function set_grading_evaluation_method($method) {
        global $DB;

        $method = clean_param($method, PARAM_PLUGIN);
        $evaluationlib = __DIR__ . '/eval/' . $method . '/lib.php';

        if (is_readable($evaluationlib)) {
            $this->evaluationinstance = null;
            $this->evaluation = $method;
            $DB->set_field('workshop', 'evaluation', $method, array('id' => $this->id));
            return true;
        }

        throw new coding_exception('Attempt to set a non-existing evaluation method.');
    }

    /**
     * Returns instance of grading evaluation class
     *
     * @return stdclass Instance of a grading evaluation
     */
    public function grading_evaluation_instance() {
        global $CFG;    // because we require other libs here

        if (is_null($this->evaluationinstance)) {
            if (empty($this->evaluation)) {
                $this->evaluation = 'best';
            }
            $evaluationlib = __DIR__ . '/eval/' . $this->evaluation . '/lib.php';
            if (is_readable($evaluationlib)) {
                require_once($evaluationlib);
            } else {
                // Fall back in case the subplugin is not available.
                $this->evaluation = 'best';
                $evaluationlib = __DIR__ . '/eval/' . $this->evaluation . '/lib.php';
                if (is_readable($evaluationlib)) {
                    require_once($evaluationlib);
                } else {
                    // Fall back in case the subplugin is not available any more.
                    throw new coding_exception('Missing default grading evaluation library ' . $evaluationlib);
                }
            }
            $classname = 'workshop_' . $this->evaluation . '_evaluation';
            $this->evaluationinstance = new $classname($this);
            if (!in_array('workshop_evaluation', class_parents($this->evaluationinstance))) {
                throw new coding_exception($classname . ' does not extend workshop_evaluation class');
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

        $allocationlib = __DIR__ . '/allocation/' . $method . '/lib.php';
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
     * @deprecated since 2.7 Please use the provided event classes for logging actions.
     *
     * @param string $action to be logged
     * @param moodle_url $url absolute url as returned by {@see workshop::submission_url()} and friends
     * @param mixed $info additional info, usually id in a table
     * @param bool $return true to return the arguments for add_to_log.
     * @return void|array array of arguments for add_to_log if $return is true
     */
    public function log($action, ?moodle_url $url = null, $info = null, $return = false) {
        debugging('The log method is now deprecated, please use event classes instead', DEBUG_DEVELOPER);

        if (is_null($url)) {
            $url = $this->view_url();
        }

        if (is_null($info)) {
            $info = $this->id;
        }

        $logurl = $this->log_convert_url($url);
        $args = array($this->course->id, 'workshop', $action, $logurl, $info, $this->cm->id);
        if ($return) {
            return $args;
        }
        call_user_func_array('add_to_log', $args);
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
            // assessing is allowed in the assessment phase only, unless the user is a teacher
            // providing additional assessment during the evaluation phase
            if ($this->phase != self::PHASE_EVALUATION or !has_capability('mod/workshop:overridegrades', $this->context, $userid)) {
                return false;
            }
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
        $this->phase = $newphase;
        $eventdata = array(
            'objectid' => $this->id,
            'context' => $this->context,
            'other' => array(
                'workshopphase' => $this->phase
            )
        );
        $event = \mod_workshop\event\phase_switched::create($eventdata);
        $event->trigger();
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
     * @param int $groupid if non-zero, prepare the report for the given group only
     * @param int $page the current page (for the pagination)
     * @param int $perpage participants per page (for the pagination)
     * @param string $sortby lastname|firstname|submissiontitle|submissiongrade|gradinggrade
     * @param string $sorthow ASC|DESC
     * @return stdclass data for the renderer
     */
    public function prepare_grading_report_data($userid, $groupid, $page, $perpage, $sortby, $sorthow) {
        global $DB;

        $canviewall     = has_capability('mod/workshop:viewallassessments', $this->context, $userid);
        $isparticipant  = $this->is_participant($userid);

        if (!$canviewall and !$isparticipant) {
            // who the hell is this?
            return array();
        }

        if (!in_array($sortby, array('lastname', 'firstname', 'submissiontitle', 'submissionmodified',
                'submissiongrade', 'gradinggrade'))) {
            $sortby = 'lastname';
        }

        if (!($sorthow === 'ASC' or $sorthow === 'DESC')) {
            $sorthow = 'ASC';
        }

        // get the list of user ids to be displayed
        if ($canviewall) {
            $participants = $this->get_participants(false, $groupid);
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
            $userfieldsapi = \core_user\fields::for_userpic();
            $picturefields = $userfieldsapi->get_sql('u', false, '', 'userid', false)->selects;
            $sql = "SELECT $picturefields, s.title AS submissiontitle, s.timemodified AS submissionmodified,
                           s.grade AS submissiongrade, ag.gradinggrade
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
        $additionalnames = \core_user\fields::get_name_fields();
        foreach ($participants as $participant) {
            if (!isset($userinfo[$participant->userid])) {
                $userinfo[$participant->userid]            = new stdclass();
                $userinfo[$participant->userid]->id        = $participant->userid;
                $userinfo[$participant->userid]->picture   = $participant->picture;
                $userinfo[$participant->userid]->imagealt  = $participant->imagealt;
                $userinfo[$participant->userid]->email     = $participant->email;
                foreach ($additionalnames as $addname) {
                    $userinfo[$participant->userid]->$addname = $participant->$addname;
                }
            }
        }

        // load the submissions details
        $submissions = $this->get_submissions(array_keys($participants));

        // get the user details for all moderators (teachers) that have overridden a submission grade
        foreach ($submissions as $submission) {
            if (!isset($userinfo[$submission->gradeoverby])) {
                $userinfo[$submission->gradeoverby]            = new stdclass();
                $userinfo[$submission->gradeoverby]->id        = $submission->gradeoverby;
                $userinfo[$submission->gradeoverby]->picture   = $submission->overpicture;
                $userinfo[$submission->gradeoverby]->imagealt  = $submission->overimagealt;
                $userinfo[$submission->gradeoverby]->email     = $submission->overemail;
                foreach ($additionalnames as $addname) {
                    $temp = 'over' . $addname;
                    $userinfo[$submission->gradeoverby]->$addname = $submission->$temp;
                }
            }
        }

        // get the user details for all reviewers of the displayed participants
        $reviewers = array();

        if ($submissions) {
            list($submissionids, $params) = $DB->get_in_or_equal(array_keys($submissions), SQL_PARAMS_NAMED);
            list($sort, $sortparams) = users_order_by_sql('r');
            $userfieldsapi = \core_user\fields::for_userpic();
            $picturefields = $userfieldsapi->get_sql('r', false, '', 'reviewerid', false)->selects;
            $sql = "SELECT a.id AS assessmentid, a.submissionid, a.grade, a.gradinggrade, a.gradinggradeover, a.weight,
                           $picturefields, s.id AS submissionid, s.authorid
                      FROM {workshop_assessments} a
                      JOIN {user} r ON (a.reviewerid = r.id)
                      JOIN {workshop_submissions} s ON (a.submissionid = s.id AND s.example = 0)
                     WHERE a.submissionid $submissionids
                  ORDER BY a.weight DESC, $sort";
            $reviewers = $DB->get_records_sql($sql, array_merge($params, $sortparams));
            foreach ($reviewers as $reviewer) {
                if (!isset($userinfo[$reviewer->reviewerid])) {
                    $userinfo[$reviewer->reviewerid]            = new stdclass();
                    $userinfo[$reviewer->reviewerid]->id        = $reviewer->reviewerid;
                    $userinfo[$reviewer->reviewerid]->picture   = $reviewer->picture;
                    $userinfo[$reviewer->reviewerid]->imagealt  = $reviewer->imagealt;
                    $userinfo[$reviewer->reviewerid]->email     = $reviewer->email;
                    foreach ($additionalnames as $addname) {
                        $userinfo[$reviewer->reviewerid]->$addname = $reviewer->$addname;
                    }
                }
            }
        }

        // get the user details for all reviewees of the displayed participants
        $reviewees = array();
        if ($participants) {
            list($participantids, $params) = $DB->get_in_or_equal(array_keys($participants), SQL_PARAMS_NAMED);
            list($sort, $sortparams) = users_order_by_sql('e');
            $params['workshopid'] = $this->id;
            $userfieldsapi = \core_user\fields::for_userpic();
            $picturefields = $userfieldsapi->get_sql('e', false, '', 'authorid', false)->selects;
            $sql = "SELECT a.id AS assessmentid, a.submissionid, a.grade, a.gradinggrade, a.gradinggradeover, a.reviewerid, a.weight,
                           s.id AS submissionid, $picturefields
                      FROM {user} u
                      JOIN {workshop_assessments} a ON (a.reviewerid = u.id)
                      JOIN {workshop_submissions} s ON (a.submissionid = s.id AND s.example = 0)
                      JOIN {user} e ON (s.authorid = e.id)
                     WHERE u.id $participantids AND s.workshopid = :workshopid
                  ORDER BY a.weight DESC, $sort";
            $reviewees = $DB->get_records_sql($sql, array_merge($params, $sortparams));
            foreach ($reviewees as $reviewee) {
                if (!isset($userinfo[$reviewee->authorid])) {
                    $userinfo[$reviewee->authorid]            = new stdclass();
                    $userinfo[$reviewee->authorid]->id        = $reviewee->authorid;
                    $userinfo[$reviewee->authorid]->picture   = $reviewee->picture;
                    $userinfo[$reviewee->authorid]->imagealt  = $reviewee->imagealt;
                    $userinfo[$reviewee->authorid]->email     = $reviewee->email;
                    foreach ($additionalnames as $addname) {
                        $userinfo[$reviewee->authorid]->$addname = $reviewee->$addname;
                    }
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
     * @param mixed moodle_url|null $actionurl
     * @param stdClass $assessment
     * @param array $options editable, editableweight, overridablegradinggrade
     * @return workshop_feedbackreviewer_form
     */
    public function get_feedbackreviewer_form($actionurl, stdclass $assessment, $options=array()) {
        global $CFG;
        require_once(__DIR__ . '/feedbackreviewer_form.php');

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
     * @mixed moodle_url|null $actionurl
     * @param stdClass $submission
     * @param array $options editable
     * @return workshop_feedbackauthor_form
     */
    public function get_feedbackauthor_form($actionurl, stdclass $submission, $options=array()) {
        global $CFG;
        require_once(__DIR__ . '/feedbackauthor_form.php');

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

    /**
     * Returns the information about the user's grades as they are stored in the gradebook
     *
     * The submission grade is returned for users with the capability mod/workshop:submit and the
     * assessment grade is returned for users with the capability mod/workshop:peerassess. Unless the
     * user has the capability to view hidden grades, grades must be visible to be returned. Null
     * grades are not returned. If none grade is to be returned, this method returns false.
     *
     * @param int $userid the user's id
     * @return workshop_final_grades|false
     */
    public function get_gradebook_grades($userid) {
        global $CFG;
        require_once($CFG->libdir.'/gradelib.php');

        if (empty($userid)) {
            throw new coding_exception('User id expected, empty value given.');
        }

        // Read data via the Gradebook API
        $gradebook = grade_get_grades($this->course->id, 'mod', 'workshop', $this->id, $userid);

        $grades = new workshop_final_grades();

        if (has_capability('mod/workshop:submit', $this->context, $userid)) {
            if (!empty($gradebook->items[0]->grades)) {
                $submissiongrade = reset($gradebook->items[0]->grades);
                if (!is_null($submissiongrade->grade)) {
                    if (!$submissiongrade->hidden or has_capability('moodle/grade:viewhidden', $this->context, $userid)) {
                        $grades->submissiongrade = $submissiongrade;
                    }
                }
            }
        }

        if (has_capability('mod/workshop:peerassess', $this->context, $userid)) {
            if (!empty($gradebook->items[1]->grades)) {
                $assessmentgrade = reset($gradebook->items[1]->grades);
                if (!is_null($assessmentgrade->grade)) {
                    if (!$assessmentgrade->hidden or has_capability('moodle/grade:viewhidden', $this->context, $userid)) {
                        $grades->assessmentgrade = $assessmentgrade;
                    }
                }
            }
        }

        if (!is_null($grades->submissiongrade) or !is_null($grades->assessmentgrade)) {
            return $grades;
        }

        return false;
    }

    /**
     * Return the editor options for the submission content field.
     *
     * @return array
     */
    public function submission_content_options() {
        global $CFG;
        require_once($CFG->dirroot.'/repository/lib.php');

        return array(
            'trusttext' => true,
            'subdirs' => false,
            'maxfiles' => $this->nattachments,
            'maxbytes' => $this->maxbytes,
            'context' => $this->context,
            'return_types' => FILE_INTERNAL | FILE_EXTERNAL,
          );
    }

    /**
     * Return the filemanager options for the submission attachments field.
     *
     * @return array
     */
    public function submission_attachment_options() {
        global $CFG;
        require_once($CFG->dirroot.'/repository/lib.php');

        $options = array(
            'subdirs' => true,
            'maxfiles' => $this->nattachments,
            'maxbytes' => $this->maxbytes,
            'return_types' => FILE_INTERNAL | FILE_CONTROLLED_LINK,
        );

        $filetypesutil = new \core_form\filetypes_util();
        $options['accepted_types'] = $filetypesutil->normalize_file_types($this->submissionfiletypes);

        return $options;
    }

    /**
     * Return the editor options for the overall feedback for the author.
     *
     * @return array
     */
    public function overall_feedback_content_options() {
        global $CFG;
        require_once($CFG->dirroot.'/repository/lib.php');

        return array(
            'subdirs' => 0,
            'maxbytes' => $this->overallfeedbackmaxbytes,
            'maxfiles' => $this->overallfeedbackfiles,
            'changeformat' => 1,
            'context' => $this->context,
            'return_types' => FILE_INTERNAL,
        );
    }

    /**
     * Return the filemanager options for the overall feedback for the author.
     *
     * @return array
     */
    public function overall_feedback_attachment_options() {
        global $CFG;
        require_once($CFG->dirroot.'/repository/lib.php');

        $options = array(
            'subdirs' => 1,
            'maxbytes' => $this->overallfeedbackmaxbytes,
            'maxfiles' => $this->overallfeedbackfiles,
            'return_types' => FILE_INTERNAL | FILE_CONTROLLED_LINK,
        );

        $filetypesutil = new \core_form\filetypes_util();
        $options['accepted_types'] = $filetypesutil->normalize_file_types($this->overallfeedbackfiletypes);

        return $options;
    }

    /**
     * Performs the reset of this workshop instance.
     *
     * @param stdClass $data The actual course reset settings.
     * @return array List of results, each being array[(string)component, (string)item, (string)error]
     */
    public function reset_userdata(stdClass $data) {

        $componentstr = get_string('pluginname', 'workshop').': '.format_string($this->name);
        $status = array();

        if (!empty($data->reset_workshop_assessments) or !empty($data->reset_workshop_submissions)) {
            // Reset all data related to assessments, including assessments of
            // example submissions.
            $result = $this->reset_userdata_assessments($data);
            if ($result === true) {
                $status[] = array(
                    'component' => $componentstr,
                    'item' => get_string('resetassessments', 'mod_workshop'),
                    'error' => false,
                );
            } else {
                $status[] = array(
                    'component' => $componentstr,
                    'item' => get_string('resetassessments', 'mod_workshop'),
                    'error' => $result,
                );
            }
        }

        if (!empty($data->reset_workshop_submissions)) {
            // Reset all remaining data related to submissions.
            $result = $this->reset_userdata_submissions($data);
            if ($result === true) {
                $status[] = array(
                    'component' => $componentstr,
                    'item' => get_string('resetsubmissions', 'mod_workshop'),
                    'error' => false,
                );
            } else {
                $status[] = array(
                    'component' => $componentstr,
                    'item' => get_string('resetsubmissions', 'mod_workshop'),
                    'error' => $result,
                );
            }
        }

        if (!empty($data->reset_workshop_phase)) {
            // Do not use the {@link workshop::switch_phase()} here, we do not
            // want to trigger events.
            $this->reset_phase();
            $status[] = array(
                'component' => $componentstr,
                'item' => get_string('resetsubmissions', 'mod_workshop'),
                'error' => false,
            );
        }

        return $status;
    }

    /**
     * Check if the current user can access the other user's group.
     *
     * This is typically used for teacher roles that have permissions like
     * 'view all submissions'. Even with such a permission granted, we have to
     * check the workshop activity group mode.
     *
     * If the workshop is not in a group mode, or if it is in the visible group
     * mode, this method returns true. This is consistent with how the
     * {@link groups_get_activity_allowed_groups()} behaves.
     *
     * If the workshop is in a separate group mode, the current user has to
     * have the 'access all groups' permission, or share at least one
     * accessible group with the other user.
     *
     * @param int $otheruserid The ID of the other user, e.g. the author of a submission.
     * @return bool False if the current user cannot access the other user's group.
     */
    public function check_group_membership($otheruserid) {
        global $USER;

        if (groups_get_activity_groupmode($this->cm) != SEPARATEGROUPS) {
            // The workshop is not in a group mode, or it is in a visible group mode.
            return true;

        } else if (has_capability('moodle/site:accessallgroups', $this->context)) {
            // The current user can access all groups.
            return true;

        } else {
            $thisusersgroups = groups_get_all_groups($this->course->id, $USER->id, $this->cm->groupingid, 'g.id');
            $otherusersgroups = groups_get_all_groups($this->course->id, $otheruserid, $this->cm->groupingid, 'g.id');
            $commongroups = array_intersect_key($thisusersgroups, $otherusersgroups);

            if (empty($commongroups)) {
                // The current user has no group common with the other user.
                return false;

            } else {
                // The current user has a group common with the other user.
                return true;
            }
        }
    }

    /**
     * Check whether the given user has assessed all his required examples before submission.
     *
     * @param  int $userid the user to check
     * @return bool        false if there are examples missing assessment, true otherwise.
     * @since  Moodle 3.4
     */
    public function check_examples_assessed_before_submission($userid) {

        if ($this->useexamples and $this->examplesmode == self::EXAMPLES_BEFORE_SUBMISSION
            and !has_capability('mod/workshop:manageexamples', $this->context)) {

            // Check that all required examples have been assessed by the user.
            $examples = $this->get_examples_for_reviewer($userid);
            foreach ($examples as $exampleid => $example) {
                if (is_null($example->assessmentid)) {
                    $examples[$exampleid]->assessmentid = $this->add_allocation($example, $userid, 0);
                }
                if (is_null($example->grade)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Check that all required examples have been assessed by the given user.
     *
     * @param  stdClass $userid     the user (reviewer) to check
     * @return mixed bool|state     false and notice code if there are examples missing assessment, true otherwise.
     * @since  Moodle 3.4
     */
    public function check_examples_assessed_before_assessment($userid) {

        if ($this->useexamples and $this->examplesmode == self::EXAMPLES_BEFORE_ASSESSMENT
                and !has_capability('mod/workshop:manageexamples', $this->context)) {

            // The reviewer must have submitted their own submission.
            $reviewersubmission = $this->get_submission_by_author($userid);
            if (!$reviewersubmission) {
                // No money, no love.
                return array(false, 'exampleneedsubmission');
            } else {
                $examples = $this->get_examples_for_reviewer($userid);
                foreach ($examples as $exampleid => $example) {
                    if (is_null($example->grade)) {
                        return array(false, 'exampleneedassessed');
                    }
                }
            }
        }
        return array(true, null);
    }

    /**
     * Trigger module viewed event and set the module viewed for completion.
     *
     * @since  Moodle 3.4
     */
    public function set_module_viewed() {
        global $CFG;
        require_once($CFG->libdir . '/completionlib.php');

        // Mark viewed.
        $completion = new completion_info($this->course);
        $completion->set_module_viewed($this->cm);

        $eventdata = array();
        $eventdata['objectid'] = $this->id;
        $eventdata['context'] = $this->context;

        // Trigger module viewed event.
        $event = \mod_workshop\event\course_module_viewed::create($eventdata);
        $event->add_record_snapshot('course', $this->course);
        $event->add_record_snapshot('workshop', $this->dbrecord);
        $event->add_record_snapshot('course_modules', $this->cm);
        $event->trigger();
    }

    /**
     * Validates the submission form or WS data.
     *
     * @param  array $data the data to be validated
     * @return array       the validation errors (if any)
     * @since  Moodle 3.4
     */
    public function validate_submission_data($data) {
        global $DB, $USER;

        $errors = array();
        if (empty($data['id']) and empty($data['example'])) {
            // Make sure there is no submission saved meanwhile from another browser window.
            $sql = "SELECT COUNT(s.id)
                      FROM {workshop_submissions} s
                      JOIN {workshop} w ON (s.workshopid = w.id)
                      JOIN {course_modules} cm ON (w.id = cm.instance)
                      JOIN {modules} m ON (m.name = 'workshop' AND m.id = cm.module)
                     WHERE cm.id = ? AND s.authorid = ? AND s.example = 0";

            if ($DB->count_records_sql($sql, array($data['cmid'], $USER->id))) {
                $errors['title'] = get_string('err_multiplesubmissions', 'mod_workshop');
            }
        }
        // Get the workshop record by id or cmid, depending on whether we're creating or editing a submission.
        if (empty($data['workshopid'])) {
            $workshop = $DB->get_record_select('workshop', 'id = (SELECT instance FROM {course_modules} WHERE id = ?)',
                    [$data['cmid']]);
        } else {
            $workshop = $DB->get_record('workshop', ['id' => $data['workshopid']]);
        }

        if (isset($data['attachment_filemanager'])) {
            $getfiles = file_get_drafarea_files($data['attachment_filemanager']);
            $attachments = $getfiles->list;
        } else {
            $attachments = array();
        }

        if ($workshop->submissiontypefile == WORKSHOP_SUBMISSION_TYPE_REQUIRED) {
            if (empty($attachments)) {
                $errors['attachment_filemanager'] = get_string('err_required', 'form');
            }
        } else if ($workshop->submissiontypefile == WORKSHOP_SUBMISSION_TYPE_DISABLED && !empty($data['attachment_filemanager'])) {
            $errors['attachment_filemanager'] = get_string('submissiontypedisabled', 'mod_workshop');
        }

        if ($workshop->submissiontypetext == WORKSHOP_SUBMISSION_TYPE_REQUIRED && html_is_blank($data['content_editor']['text'])) {
            $errors['content_editor'] = get_string('err_required', 'form');
        } else if ($workshop->submissiontypetext == WORKSHOP_SUBMISSION_TYPE_DISABLED && !empty($data['content_editor']['text'])) {
            $errors['content_editor'] = get_string('submissiontypedisabled', 'mod_workshop');
        }

        // If neither type is explicitly required, one or the other must be submitted.
        if ($workshop->submissiontypetext != WORKSHOP_SUBMISSION_TYPE_REQUIRED
                && $workshop->submissiontypefile != WORKSHOP_SUBMISSION_TYPE_REQUIRED
                && empty($attachments) && html_is_blank($data['content_editor']['text'])) {
            $errors['content_editor'] = get_string('submissionrequiredcontent', 'mod_workshop');
            $errors['attachment_filemanager'] = get_string('submissionrequiredfile', 'mod_workshop');
        }

        return $errors;
    }

    /**
     * Adds or updates a submission.
     *
     * @param stdClass $submission The submissin data (via form or via WS).
     * @return the new or updated submission id.
     * @since  Moodle 3.4
     */
    public function edit_submission($submission) {
        global $USER, $DB;

        if ($submission->example == 0) {
            // This was used just for validation, it must be set to zero when dealing with normal submissions.
            unset($submission->example);
        } else {
            throw new coding_exception('Invalid submission form data value: example');
        }
        $timenow = time();
        if (is_null($submission->id)) {
            $submission->workshopid     = $this->id;
            $submission->example        = 0;
            $submission->authorid       = $USER->id;
            $submission->timecreated    = $timenow;
            $submission->feedbackauthorformat = editors_get_preferred_format();
        }
        $submission->timemodified       = $timenow;
        $submission->title              = trim($submission->title);
        $submission->content            = '';          // Updated later.
        $submission->contentformat      = FORMAT_HTML; // Updated later.
        $submission->contenttrust       = 0;           // Updated later.
        $submission->late               = 0x0;         // Bit mask.
        if (!empty($this->submissionend) and ($this->submissionend < time())) {
            $submission->late = $submission->late | 0x1;
        }
        if ($this->phase == self::PHASE_ASSESSMENT) {
            $submission->late = $submission->late | 0x2;
        }

        // Event information.
        $params = array(
            'context' => $this->context,
            'courseid' => $this->course->id,
            'other' => array(
                'submissiontitle' => $submission->title
            )
        );
        $logdata = null;
        if (is_null($submission->id)) {
            $submission->id = $DB->insert_record('workshop_submissions', $submission);
            $params['objectid'] = $submission->id;
            $event = \mod_workshop\event\submission_created::create($params);
            $event->trigger();
        } else {
            if (empty($submission->id) or empty($submission->id) or ($submission->id != $submission->id)) {
                throw new moodle_exception('err_submissionid', 'workshop');
            }
        }
        $params['objectid'] = $submission->id;

        // Save and relink embedded images and save attachments.
        if ($this->submissiontypetext != WORKSHOP_SUBMISSION_TYPE_DISABLED) {
            $submission = file_postupdate_standard_editor($submission, 'content', $this->submission_content_options(),
                    $this->context, 'mod_workshop', 'submission_content', $submission->id);
        }

        $submission = file_postupdate_standard_filemanager($submission, 'attachment', $this->submission_attachment_options(),
            $this->context, 'mod_workshop', 'submission_attachment', $submission->id);

        if (empty($submission->attachment)) {
            // Explicit cast to zero integer.
            $submission->attachment = 0;
        }
        // Store the updated values or re-save the new submission (re-saving needed because URLs are now rewritten).
        $DB->update_record('workshop_submissions', $submission);
        $event = \mod_workshop\event\submission_updated::create($params);
        $event->add_record_snapshot('workshop', $this->dbrecord);
        $event->trigger();

        // Send submitted content for plagiarism detection.
        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'mod_workshop', 'submission_attachment', $submission->id);

        $params['other']['content'] = $submission->content;
        $params['other']['pathnamehashes'] = array_keys($files);

        $event = \mod_workshop\event\assessable_uploaded::create($params);
        $event->trigger();

        return $submission->id;
    }

    /**
     * Helper method for validating if the current user can view the given assessment.
     *
     * @param  stdClass   $assessment assessment object
     * @param  stdClass   $submission submission object
     * @return void
     * @throws moodle_exception
     * @since  Moodle 3.4
     */
    public function check_view_assessment($assessment, $submission) {
        global $USER;

        $isauthor = $submission->authorid == $USER->id;
        $isreviewer = $assessment->reviewerid == $USER->id;
        $canviewallassessments  = has_capability('mod/workshop:viewallassessments', $this->context);
        $canviewallsubmissions  = has_capability('mod/workshop:viewallsubmissions', $this->context);

        $canviewallsubmissions = $canviewallsubmissions && $this->check_group_membership($submission->authorid);

        if (!$isreviewer and !$isauthor and !($canviewallassessments and $canviewallsubmissions)) {
            throw new \moodle_exception('nopermissions', 'error', $this->view_url(), 'view this assessment');
        }

        if ($isauthor and !$isreviewer and !$canviewallassessments and $this->phase != self::PHASE_CLOSED) {
            // Authors can see assessments of their work at the end of workshop only.
            throw new \moodle_exception('nopermissions', 'error', $this->view_url(),
                'view assessment of own work before workshop is closed');
        }
    }

    /**
     * Helper method for validating if the current user can edit the given assessment.
     *
     * @param  stdClass   $assessment assessment object
     * @param  stdClass   $submission submission object
     * @return void
     * @throws moodle_exception
     * @since  Moodle 3.4
     */
    public function check_edit_assessment($assessment, $submission) {
        global $USER;

        $this->check_view_assessment($assessment, $submission);
        // Further checks.
        $isreviewer = ($USER->id == $assessment->reviewerid);

        $assessmenteditable = $isreviewer && $this->assessing_allowed($USER->id);
        if (!$assessmenteditable) {
            throw new moodle_exception('nopermissions', 'error', '', 'edit assessments');
        }

        list($assessed, $notice) = $this->check_examples_assessed_before_assessment($assessment->reviewerid);
        if (!$assessed) {
            throw new moodle_exception($notice, 'mod_workshop');
        }
    }

    /**
     * Adds information to an allocated assessment (function used the first time a review is done or when updating an existing one).
     *
     * @param  stdClass $assessment the assessment
     * @param  stdClass $submission the submission
     * @param  stdClass $data       the assessment data to be added or Updated
     * @param  stdClass $strategy   the strategy instance
     * @return float|null           Raw percentual grade (0.00000 to 100.00000) for submission
     * @since  Moodle 3.4
     */
    public function edit_assessment($assessment, $submission, $data, $strategy) {
        global $DB;

        $cansetassessmentweight = has_capability('mod/workshop:allocate', $this->context);

        // Let the grading strategy subplugin save its data.
        $rawgrade = $strategy->save_assessment($assessment, $data);

        // Store the data managed by the workshop core.
        $coredata = (object)array('id' => $assessment->id);
        if (isset($data->feedbackauthor_editor)) {
            $coredata->feedbackauthor_editor = $data->feedbackauthor_editor;
            $coredata = file_postupdate_standard_editor($coredata, 'feedbackauthor', $this->overall_feedback_content_options(),
                $this->context, 'mod_workshop', 'overallfeedback_content', $assessment->id);
            unset($coredata->feedbackauthor_editor);
        }
        if (isset($data->feedbackauthorattachment_filemanager)) {
            $coredata->feedbackauthorattachment_filemanager = $data->feedbackauthorattachment_filemanager;
            $coredata = file_postupdate_standard_filemanager($coredata, 'feedbackauthorattachment',
                $this->overall_feedback_attachment_options(), $this->context, 'mod_workshop', 'overallfeedback_attachment',
                $assessment->id);
            unset($coredata->feedbackauthorattachment_filemanager);
            if (empty($coredata->feedbackauthorattachment)) {
                $coredata->feedbackauthorattachment = 0;
            }
        }
        if (isset($data->weight) and $cansetassessmentweight) {
            $coredata->weight = $data->weight;
        }
        // Update the assessment data if there is something other than just the 'id'.
        if (count((array)$coredata) > 1 ) {
            $DB->update_record('workshop_assessments', $coredata);
            $params = array(
                'relateduserid' => $submission->authorid,
                'objectid' => $assessment->id,
                'context' => $this->context,
                'other' => array(
                    'workshopid' => $this->id,
                    'submissionid' => $assessment->submissionid
                )
            );

            if (is_null($assessment->grade)) {
                // All workshop_assessments are created when allocations are made. The create event is of more use located here.
                $event = \mod_workshop\event\submission_assessed::create($params);
                $event->trigger();
            } else {
                $params['other']['grade'] = $assessment->grade;
                $event = \mod_workshop\event\submission_reassessed::create($params);
                $event->trigger();
            }
        }
        return $rawgrade;
    }

    /**
     * Evaluates an assessment.
     *
     * @param  stdClass $assessment the assessment
     * @param  stdClass $data       the assessment data to be updated
     * @param  bool $cansetassessmentweight   whether the user can change the assessment weight
     * @param  bool $canoverridegrades   whether the user can override the assessment grades
     * @return void
     * @since  Moodle 3.4
     */
    public function evaluate_assessment($assessment, $data, $cansetassessmentweight, $canoverridegrades) {
        global $DB, $USER;

        $data = file_postupdate_standard_editor($data, 'feedbackreviewer', array(), $this->context);
        $record = new stdclass();
        $record->id = $assessment->id;
        if ($cansetassessmentweight) {
            $record->weight = $data->weight;
        }
        if ($canoverridegrades) {
            $record->gradinggradeover = $this->raw_grade_value($data->gradinggradeover, $this->gradinggrade);
            $record->gradinggradeoverby = $USER->id;
            $record->feedbackreviewer = $data->feedbackreviewer;
            $record->feedbackreviewerformat = $data->feedbackreviewerformat;
        }
        $DB->update_record('workshop_assessments', $record);
    }

    /**
     * Trigger submission viewed event.
     *
     * @param stdClass $submission submission object
     * @since  Moodle 3.4
     */
    public function set_submission_viewed($submission) {
        $params = array(
            'objectid' => $submission->id,
            'context' => $this->context,
            'courseid' => $this->course->id,
            'relateduserid' => $submission->authorid,
            'other' => array(
                'workshopid' => $this->id
            )
        );

        $event = \mod_workshop\event\submission_viewed::create($params);
        $event->trigger();
    }

    /**
     * Evaluates a submission.
     *
     * @param  stdClass $submission the submission
     * @param  stdClass $data       the submission data to be updated
     * @param  bool $canpublish     whether the user can publish the submission
     * @param  bool $canoverride    whether the user can override the submission grade
     * @return void
     * @since  Moodle 3.4
     */
    public function evaluate_submission($submission, $data, $canpublish, $canoverride) {
        global $DB, $USER;

        $data = file_postupdate_standard_editor($data, 'feedbackauthor', array(), $this->context);
        $record = new stdclass();
        $record->id = $submission->id;
        if ($canoverride) {
            $record->gradeover = $this->raw_grade_value($data->gradeover, $this->grade);
            $record->gradeoverby = $USER->id;
            $record->feedbackauthor = $data->feedbackauthor;
            $record->feedbackauthorformat = $data->feedbackauthorformat;
        }
        if ($canpublish) {
            $record->published = !empty($data->published);
        }
        $DB->update_record('workshop_submissions', $record);
    }

    /**
     * Get the initial first name.
     *
     * @return string|null initial of first name we are currently filtering by.
     */
    public function get_initial_first(): ?string {
        if (empty($this->initialbarprefs['i_first'])) {
            return null;
        }

        return $this->initialbarprefs['i_first'];
    }

    /**
     * Get the initial last name.
     *
     * @return string|null initial of last name we are currently filtering by.
     */
    public function get_initial_last(): ?string {
        if (empty($this->initialbarprefs['i_last'])) {
            return null;
        }

        return $this->initialbarprefs['i_last'];
    }

    /**
     * Init method for initial bars.
     * @return void
     */
    public function init_initial_bar(): void {
        global $SESSION;
        if ($this->phase === self::PHASE_SETUP) {
            return;
        }

        $ifirst = optional_param('ifirst', null, PARAM_NOTAGS);
        $ilast = optional_param('ilast', null, PARAM_NOTAGS);

        if (empty($SESSION->mod_workshop->initialbarprefs['id-'.$this->context->id])) {
            $SESSION->mod_workshop = new stdClass();
            $SESSION->mod_workshop->initialbarprefs['id-'.$this->context->id] = [];
        }
        if (!empty($SESSION->mod_workshop->initialbarprefs['id-'.$this->context->id]['i_first'])) {
            $this->initialbarprefs['i_first'] = $SESSION->mod_workshop->initialbarprefs['id-'.$this->context->id]['i_first'];
        }
        if (!empty($SESSION->mod_workshop->initialbarprefs['id-'.$this->context->id]['i_last'])) {
            $this->initialbarprefs['i_last'] = $SESSION->mod_workshop->initialbarprefs['id-'.$this->context->id]['i_last'];
        }
        if (!is_null($ifirst)) {
            $this->initialbarprefs['i_first'] = $ifirst;
            $SESSION->mod_workshop->initialbarprefs['id-'.$this->context->id]['i_first'] = $ifirst;
        }

        if (!is_null($ilast)) {
            $this->initialbarprefs['i_last'] = $ilast;
            $SESSION->mod_workshop->initialbarprefs['id-'.$this->context->id]['i_last'] = $ilast;
        }
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
     * @param null|int $timegraded explicit timestamp of the aggregation, defaults to the current time
     * @return void
     */
    protected function aggregate_grading_grades_process(array $assessments, $timegraded = null) {
        global $DB;

        $reviewerid = null; // the id of the reviewer being processed
        $current    = null; // the gradinggrade currently saved in database
        $finalgrade = null; // the new grade to be calculated
        $agid       = null; // aggregation id
        $sumgrades  = 0;
        $count      = 0;

        if (is_null($timegraded)) {
            $timegraded = time();
        }

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

        // Event information.
        $params = array(
            'context' => $this->context,
            'courseid' => $this->course->id,
            'relateduserid' => $reviewerid
        );

        // check if the new final grade differs from the one stored in the database
        if (grade_floats_different($finalgrade, $current)) {
            $params['other'] = array(
                'currentgrade' => $current,
                'finalgrade' => $finalgrade
            );

            // we need to save new calculation into the database
            if (is_null($agid)) {
                // no aggregation record yet
                $record = new stdclass();
                $record->workshopid = $this->id;
                $record->userid = $reviewerid;
                $record->gradinggrade = $finalgrade;
                $record->timegraded = $timegraded;
                $record->id = $DB->insert_record('workshop_aggregations', $record);
                $params['objectid'] = $record->id;
                $event = \mod_workshop\event\assessment_evaluated::create($params);
                $event->trigger();
            } else {
                $record = new stdclass();
                $record->id = $agid;
                $record->gradinggrade = $finalgrade;
                $record->timegraded = $timegraded;
                $DB->update_record('workshop_aggregations', $record);
                $params['objectid'] = $agid;
                $event = \mod_workshop\event\assessment_reevaluated::create($params);
                $event->trigger();
            }
        }
    }

    /**
     * This is a replacement for the old get_users_with_capability_sql method.
     * The old method returned huge amounts of SQL for large courses, potentially 10s of thousands of lines
     * with hundreds of UNIONS, which caused some poor performance. This new method aims to streamline it all
     * into one query.
     *
     * Gets users with any of the specified capabilities on the workshop activity.
     *
     * The list is automatically restricted according to any availability restrictions
     * that apply to user lists (e.g. group, grouping restrictions).
     *
     * @param array $capabilities array of capability names (If the user has ANY of them, it returns true for that user)
     * @param bool $musthavesubmission if true, return only users who have already submitted
     * @param int $groupid 0 means ignore groups, any other value limits the result by group id
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set)
     * @param int $limitnum return a subset containing this number of records (optional, required if $limitfrom is set)
     * @return array of users
     */
    protected function get_users_with_capability(array $capabilities, bool $musthavesubmission, int $groupid,
            int $limitfrom = 0, int $limitnum = 0): array {

        global $CFG, $DB;

        $params = [];

        // This is from enrollib.php:get_enrolled_join(). It says it's better for caching to use round.
        $now = round(time(), -2);
        $userfieldsapi = \core_user\fields::for_name()->with_userpic();

        $sqlarray = [];
        $sqlarray['select'] = "SELECT DISTINCT u.id" . $userfieldsapi->get_sql('u')->selects;
        $sqlarray['from'] = "FROM {user} u";
        $sqlarray['join'] = [];
        $sqlarray['join'][] = "JOIN {user_enrolments} ue ON ue.userid = u.id";
        $sqlarray['join'][] = "JOIN {enrol} e ON e.id = ue.enrolid AND e.courseid = :courseid";
        $sqlarray['where'] = [];
        $sqlarray['where'][] = "WHERE u.deleted = 0";
        $sqlarray['where'][] = "AND u.id <> :guestid";
        $sqlarray['where'][] = "AND ue.status = :activestatus";
        $sqlarray['where'][] = "AND e.status = :enabledstatus";
        $sqlarray['where'][] = "AND ue.timestart < :timestart";
        $sqlarray['where'][] = "AND (ue.timeend > :timeend OR ue.timeend = 0)";

        // Apply sorting.
        [$sortby, $sortbyparams] = users_order_by_sql('u');
        $sqlarray['sort'] = "ORDER BY {$sortby}";

        // Apply filtering.
        [$filtersql, $filterparams] = $this->get_users_with_initial_filtering_sql_where('u');
        if ($filtersql) {
            $sqlarray['where'][] = "AND {$filtersql}";
            $params = array_merge($params, $filterparams);
        }

        $params['courseid'] = $this->context->get_course_context()->instanceid;
        $params['timestart'] = $now;
        $params['timeend'] = $now;
        $params['guestid'] = $CFG->siteguest;
        $params['activestatus'] = ENROL_USER_ACTIVE;
        $params['enabledstatus'] = ENROL_INSTANCE_ENABLED;

        // If we are filtering by users who have submitted.
        if ($musthavesubmission) {
            $sqlarray['join'][] = "JOIN {workshop_submissions} ws ON
                (ws.authorid = u.id AND ws.example = 0 AND ws.workshopid = :workshopid) ";
            $params['workshopid'] = $this->id;
        }

        // Are we searching one specific group?
        if ($groupid > 0) {
            $sqlarray['join'][] = "JOIN {groups_members} gm ON (gm.userid = u.id AND gm.groupid = :groupid)";
            $params['groupid'] = $groupid;
        } else if ($this->cm->groupingid) {
            // If not, is there a groupingid set on the activity?
            $sqlarray['join'][] = "JOIN {groupings_groups} gg ON gg.groupingid = :groupingid";
            $sqlarray['join'][] = "JOIN {groups_members} gm ON (gm.userid = u.id AND gm.groupid = gg.groupid)";
            $params['groupingid'] = $this->cm->groupingid;
        }

        // Is the activity restricted so only certain users can access it?
        $info = new \core_availability\info_module($this->cm);
        [$listsql, $listparams] = $info->get_user_list_sql(false);
        if ($listsql) {
            $sqlarray['join'][] = " JOIN ($listsql) restricted ON restricted.id = u.id";
            $params = array_merge($params, $listparams);
        }

        // Join the role assignments to only return users with the right capabilities.
        $capjoin = get_with_capability_join($this->context, $capabilities, 'u.id');
        $sqlarray['join'][] = $capjoin->joins;

        // Convert the sql array into a string.
        $sql = $this->build_sql($sqlarray);

        // We actually query the users here instead of returning SQL like it used to.
        return $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);

    }

    /**
     * Build SQL string from array of clauses
     *
     * @param array $sqlarray ['select' => '', 'from' => '', 'join' => [], 'where' => [], 'sort' => '']
     * @return string
     */
    protected function build_sql(array $sqlarray): string {

        $sql = "";
        foreach ($sqlarray as $el) {
            if (is_array($el)) {
                $sql .= implode(" ", $el) . " ";
            } else {
                $sql .= " {$el} ";
            }
        }

        return $sql;

    }

    /**
     * Returns SQL to fetch all enrolled users with the first name or last name.
     *
     * @param string $table Table alias for users table
     * @return array
     */
    protected function get_users_with_initial_filtering_sql_where(string $table): array {
        global $DB;
        $conditions = [];
        $params = [];
        $ifirst = $this->get_initial_first();
        $ilast = $this->get_initial_last();
        if ($ifirst) {
            $conditions[] = $DB->sql_like('LOWER(' . $table . '.firstname)', ':i_first' , false, false);
            $params['i_first'] = $DB->sql_like_escape($ifirst) . '%';
        }
        if ($ilast) {
            $conditions[] = $DB->sql_like('LOWER(' . $table . '.lastname)', ':i_last' , false, false);
            $params['i_last'] = $DB->sql_like_escape($ilast) . '%';
        }
        return [implode(" AND ", $conditions), $params];
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

    /**
     * Removes all user data related to assessments (including allocations).
     *
     * This includes assessments of example submissions as long as they are not
     * referential assessments.
     *
     * @param stdClass $data The actual course reset settings.
     * @return bool|string True on success, error message otherwise.
     */
    protected function reset_userdata_assessments(stdClass $data) {
        global $DB;

        $sql = "SELECT a.id
                  FROM {workshop_assessments} a
                  JOIN {workshop_submissions} s ON (a.submissionid = s.id)
                 WHERE s.workshopid = :workshopid
                       AND (s.example = 0 OR (s.example = 1 AND a.weight = 0))";

        $assessments = $DB->get_records_sql($sql, array('workshopid' => $this->id));
        $this->delete_assessment(array_keys($assessments));

        $DB->delete_records('workshop_aggregations', array('workshopid' => $this->id));

        return true;
    }

    /**
     * Removes all user data related to participants' submissions.
     *
     * @param stdClass $data The actual course reset settings.
     * @return bool|string True on success, error message otherwise.
     */
    protected function reset_userdata_submissions(stdClass $data) {
        global $DB;

        $submissions = $this->get_submissions();
        foreach ($submissions as $submission) {
            $this->delete_submission($submission);
        }

        return true;
    }

    /**
     * Hard set the workshop phase to the setup one.
     */
    protected function reset_phase() {
        global $DB;

        $DB->set_field('workshop', 'phase', self::PHASE_SETUP, array('id' => $this->id));
        $this->phase = self::PHASE_SETUP;
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
        if (has_capability('moodle/course:manageactivities', $workshop->context, $userid)) {
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
            if ($workshop->phaseswitchassessment) {
                $task = new stdClass();
                $allocator = $DB->get_record('workshopallocation_scheduled', array('workshopid' => $workshop->id));
                if (empty($allocator)) {
                    $task->completed = false;
                } else if ($allocator->enabled and is_null($allocator->resultstatus)) {
                    $task->completed = true;
                } else if ($workshop->submissionend > time()) {
                    $task->completed = null;
                } else {
                    $task->completed = false;
                }
                $task->title = get_string('setup', 'workshopallocation_scheduled');
                $task->link = $workshop->allocation_url('scheduled');
                $phase->tasks['allocatescheduled'] = $task;
            }
            $task = new stdclass();
            $task->title = get_string('allocate', 'workshop');
            $task->link = $workshop->allocation_url();
            $numofauthors = $workshop->count_potential_authors(false);
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
            // If submission deadline has passed and late submissions are allowed, only display 'latesubmissionsallowed' text to
            // users (students) who have not submitted and users(teachers, admins)  who can switch pahase..
            if (has_capability('mod/workshop:switchphase', $workshop->context, $userid) ||
                    (!$workshop->get_submission_by_author($userid) && $workshop->submissionend < time())) {
                $task = new stdclass();
                $task->title = get_string('latesubmissionsallowed', 'workshop');
                $task->completed = 'info';
                $phase->tasks['latesubmissionsallowed'] = $task;
            }
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
        if ($workshop->phase == workshop::PHASE_SUBMISSION and $workshop->phaseswitchassessment
                and has_capability('mod/workshop:switchphase', $workshop->context, $userid)) {
            $task = new stdClass();
            $task->title = get_string('switchphase30auto', 'mod_workshop', workshop::timestamp_formats($workshop->submissionend));
            $task->completed = 'info';
            $phase->tasks['autoswitchinfo'] = $task;
        }
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
            if ($numofpeers) {
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
            $expected = $workshop->count_potential_authors(false);
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

            $expected = $workshop->count_potential_reviewers(false);
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

        if (has_capability('moodle/course:manageactivities', $workshop->context, $userid)) {
            $task = new stdclass();
            $task->title = get_string('taskconclusion', 'workshop');
            $task->link = $workshop->updatemod_url();
            if (trim($workshop->conclusion)) {
                $task->completed = true;
            } elseif ($workshop->phase >= workshop::PHASE_EVALUATION) {
                $task->completed = false;
            }
            $phase->tasks['conclusion'] = $task;
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

        // Add phase switching actions.
        if (has_capability('mod/workshop:switchphase', $workshop->context, $userid)) {
            $nextphases = array(
                workshop::PHASE_SETUP => workshop::PHASE_SUBMISSION,
                workshop::PHASE_SUBMISSION => workshop::PHASE_ASSESSMENT,
                workshop::PHASE_ASSESSMENT => workshop::PHASE_EVALUATION,
                workshop::PHASE_EVALUATION => workshop::PHASE_CLOSED,
            );
            foreach ($this->phases as $phasecode => $phase) {
                if ($phase->active) {
                    if (isset($nextphases[$workshop->phase])) {
                        $task = new stdClass();
                        $task->title = get_string('switchphasenext', 'mod_workshop');
                        $task->link = $workshop->switchphase_url($nextphases[$workshop->phase]);
                        $task->details = '';
                        $task->completed = null;
                        $phase->tasks['switchtonextphase'] = $task;
                    }

                } else {
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

    /** @var workshop */
    protected $workshop;

    /**
     * Copies the properties of the given database record into properties of $this instance
     *
     * @param workshop $workshop
     * @param stdClass $submission full record
     * @param bool $showauthor show the author-related information
     * @param array $options additional properties
     */
    public function __construct(workshop $workshop, stdClass $submission, $showauthor = false) {

        $this->workshop = $workshop;

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
        $authorfields = explode(',', implode(',', \core_user\fields::get_picture_fields()));
        foreach ($authorfields as $field) {
            $prefixedusernamefield = 'author' . $field;
            unset($this->{$prefixedusernamefield});
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
    /** @var string */
    public $authorfirstnamephonetic;
    /** @var string */
    public $authorlastnamephonetic;
    /** @var string */
    public $authormiddlename;
    /** @var string */
    public $authoralternatename;
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
        'authorid', 'authorfirstname', 'authorlastname', 'authorfirstnamephonetic', 'authorlastnamephonetic',
        'authormiddlename', 'authoralternatename', 'authorpicture',
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
        'attachment', 'authorid', 'authorfirstname', 'authorlastname', 'authorfirstnamephonetic', 'authorlastnamephonetic',
        'authormiddlename', 'authoralternatename', 'authorpicture', 'authorimagealt', 'authoremail');
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

    /** @var workshop */
    public $workshop;

    /**
     * Copies the properties of the given database record into properties of $this instance
     *
     * The $options keys are: showreviewer, showauthor
     * @param workshop $workshop
     * @param stdClass $assessment full record
     * @param array $options additional properties
     */
    public function __construct(workshop $workshop, stdClass $record, array $options = array()) {

        $this->workshop = $workshop;
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

    /** @var string */
    public $feedbackauthor;

    /** @var int */
    public $feedbackauthorformat;

    /** @var int */
    public $feedbackauthorattachment;

    /** @var array */
    protected $fields = array('id', 'submissionid', 'weight', 'timecreated',
        'timemodified', 'grade', 'gradinggrade', 'gradinggradeover', 'feedbackauthor',
        'feedbackauthorformat', 'feedbackauthorattachment');

    /**
     * Format the overall feedback text content
     *
     * False is returned if the overall feedback feature is disabled. Null is returned
     * if the overall feedback content has not been found. Otherwise, string with
     * formatted feedback text is returned.
     *
     * @return string|bool|null
     */
    public function get_overall_feedback_content() {

        if ($this->workshop->overallfeedbackmode == 0) {
            return false;
        }

        if (trim($this->feedbackauthor) === '') {
            return null;
        }

        $content = file_rewrite_pluginfile_urls($this->feedbackauthor, 'pluginfile.php', $this->workshop->context->id,
            'mod_workshop', 'overallfeedback_content', $this->id);
        $content = format_text($content, $this->feedbackauthorformat,
            array('overflowdiv' => true, 'context' => $this->workshop->context));

        return $content;
    }

    /**
     * Prepares the list of overall feedback attachments
     *
     * Returns false if overall feedback attachments are not allowed. Otherwise returns
     * list of attachments (may be empty).
     *
     * @return bool|array of stdClass
     */
    public function get_overall_feedback_attachments() {

        if ($this->workshop->overallfeedbackmode == 0) {
            return false;
        }

        if ($this->workshop->overallfeedbackfiles == 0) {
            return false;
        }

        if (empty($this->feedbackauthorattachment)) {
            return array();
        }

        $attachments = array();
        $fs = get_file_storage();
        $files = $fs->get_area_files($this->workshop->context->id, 'mod_workshop', 'overallfeedback_attachment', $this->id);
        foreach ($files as $file) {
            if ($file->is_directory()) {
                continue;
            }
            $filepath = $file->get_filepath();
            $filename = $file->get_filename();
            $fileurl = moodle_url::make_pluginfile_url($this->workshop->context->id, 'mod_workshop',
                'overallfeedback_attachment', $this->id, $filepath, $filename, true);
            $previewurl = new moodle_url(moodle_url::make_pluginfile_url($this->workshop->context->id, 'mod_workshop',
                'overallfeedback_attachment', $this->id, $filepath, $filename, false), array('preview' => 'bigthumb'));
            $attachments[] = (object)array(
                'filepath' => $filepath,
                'filename' => $filename,
                'fileurl' => $fileurl,
                'previewurl' => $previewurl,
                'mimetype' => $file->get_mimetype(),

            );
        }

        return $attachments;
    }
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

    /**
     * Prepare the data to be exported to a external system via Web Services.
     *
     * This function applies extra capabilities checks.
     * @return stdClass the data ready for external systems
     */
    public function export_data_for_external() {
        $data = $this->get_data();
        $options = $this->get_options();

        foreach ($data->grades as $reportdata) {
            // If we are in submission phase ignore the following data.
            if ($options->workshopphase == workshop::PHASE_SUBMISSION) {
                unset($reportdata->submissiongrade);
                unset($reportdata->gradinggrade);
                unset($reportdata->submissiongradeover);
                unset($reportdata->submissiongradeoverby);
                unset($reportdata->submissionpublished);
                unset($reportdata->reviewedby);
                unset($reportdata->reviewerof);
                continue;
            }

            if (!$options->showsubmissiongrade) {
                unset($reportdata->submissiongrade);
                unset($reportdata->submissiongradeover);
            }

            if (!$options->showgradinggrade and $tr == 0) {
                unset($reportdata->gradinggrade);
            }

            if (!$options->showreviewernames) {
                foreach ($reportdata->reviewedby as $reviewedby) {
                    $reviewedby->userid = 0;
                }
            }

            if (!$options->showauthornames) {
                foreach ($reportdata->reviewerof as $reviewerof) {
                    $reviewerof->userid = 0;
                }
            }
        }

        return $data;
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


/**
 * Holds the final grades for the activity as are stored in the gradebook
 */
class workshop_final_grades implements renderable {

    /** @var object the info from the gradebook about the grade for submission */
    public $submissiongrade = null;

    /** @var object the infor from the gradebook about the grade for assessment */
    public $assessmentgrade = null;
}
