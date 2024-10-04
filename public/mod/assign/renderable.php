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
 * This file contains the definition for the renderable classes for the assignment
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \mod_assign\output\assign_submission_status;

defined('MOODLE_INTERNAL') || die();

/**
 * This class wraps the submit for grading confirmation page
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submit_for_grading_page implements renderable {
    /** @var array $notifications is a list of notification messages returned from the plugins */
    public $notifications = array();
    /** @var int $coursemoduleid */
    public $coursemoduleid = 0;
    /** @var moodleform $confirmform */
    public $confirmform = null;

    /**
     * Constructor
     * @param string $notifications - Any mesages to display
     * @param int $coursemoduleid
     * @param moodleform $confirmform
     */
    public function __construct($notifications, $coursemoduleid, $confirmform) {
        $this->notifications = $notifications;
        $this->coursemoduleid = $coursemoduleid;
        $this->confirmform = $confirmform;
    }

}

/**
 * Implements a renderable message notification
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_gradingmessage implements renderable {
    /** @var string $heading is the heading to display to the user */
    public $heading = '';
    /** @var string $message is the message to display to the user */
    public $message = '';
    /** @var int $coursemoduleid */
    public $coursemoduleid = 0;
    /** @var int $gradingerror should be set true if there was a problem grading */
    public $gradingerror = null;
    /** @var int the grading page. */
    public $page;

    /**
     * Constructor
     * @param string $heading This is the heading to display
     * @param string $message This is the message to display
     * @param bool $gradingerror Set to true to display the message as an error.
     * @param int $coursemoduleid
     * @param int $page This is the current quick grading page
     */
    public function __construct($heading, $message, $coursemoduleid, $gradingerror = false, $page = null) {
        $this->heading = $heading;
        $this->message = $message;
        $this->coursemoduleid = $coursemoduleid;
        $this->gradingerror = $gradingerror;
        $this->page = $page;
    }

}

/**
 * Implements a renderable grading options form
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_form implements renderable {
    /** @var moodleform $form is the edit submission form */
    public $form = null;
    /** @var string $classname is the name of the class to assign to the container */
    public $classname = '';
    /** @var string $jsinitfunction is an optional js function to add to the page requires */
    public $jsinitfunction = '';

    /**
     * Constructor
     * @param string $classname This is the class name for the container div
     * @param moodleform $form This is the moodleform
     * @param string $jsinitfunction This is an optional js function to add to the page requires
     */
    public function __construct($classname, moodleform $form, $jsinitfunction = '') {
        $this->classname = $classname;
        $this->form = $form;
        $this->jsinitfunction = $jsinitfunction;
    }

}

/**
 * Implements a renderable user summary
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_user_summary implements renderable {
    /** @var stdClass $user suitable for rendering with user_picture and fullname(). */
    public $user = null;
    /** @var int $courseid */
    public $courseid;
    /** @var bool $viewfullnames */
    public $viewfullnames = false;
    /** @var bool $blindmarking */
    public $blindmarking = false;
    /** @var int $uniqueidforuser */
    public $uniqueidforuser;
    /** @var array $extrauserfields */
    public $extrauserfields;
    /** @var bool $suspendeduser */
    public $suspendeduser;

    /**
     * Constructor
     * @param stdClass $user
     * @param int $courseid
     * @param bool $viewfullnames
     * @param bool $blindmarking
     * @param int $uniqueidforuser
     * @param array $extrauserfields
     * @param bool $suspendeduser
     */
    public function __construct(stdClass $user,
                                $courseid,
                                $viewfullnames,
                                $blindmarking,
                                $uniqueidforuser,
                                $extrauserfields,
                                $suspendeduser = false) {
        $this->user = $user;
        $this->courseid = $courseid;
        $this->viewfullnames = $viewfullnames;
        $this->blindmarking = $blindmarking;
        $this->uniqueidforuser = $uniqueidforuser;
        $this->extrauserfields = $extrauserfields;
        $this->suspendeduser = $suspendeduser;
    }
}

/**
 * Implements a renderable feedback plugin feedback
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_feedback_plugin_feedback implements renderable {
    /** @var int SUMMARY */
    const SUMMARY                = 10;
    /** @var int FULL */
    const FULL                   = 20;

    /** @var assign_submission_plugin $plugin */
    public $plugin = null;
    /** @var stdClass $grade */
    public $grade = null;
    /** @var string $view */
    public $view = self::SUMMARY;
    /** @var int $coursemoduleid */
    public $coursemoduleid = 0;
    /** @var string returnaction The action to take you back to the current page */
    public $returnaction = '';
    /** @var array returnparams The params to take you back to the current page */
    public $returnparams = array();

    /**
     * Feedback for a single plugin
     *
     * @param assign_feedback_plugin $plugin
     * @param stdClass $grade
     * @param string $view one of feedback_plugin::SUMMARY or feedback_plugin::FULL
     * @param int $coursemoduleid
     * @param string $returnaction The action required to return to this page
     * @param array $returnparams The params required to return to this page
     */
    public function __construct(assign_feedback_plugin $plugin,
                                stdClass $grade,
                                $view,
                                $coursemoduleid,
                                $returnaction,
                                $returnparams) {
        $this->plugin = $plugin;
        $this->grade = $grade;
        $this->view = $view;
        $this->coursemoduleid = $coursemoduleid;
        $this->returnaction = $returnaction;
        $this->returnparams = $returnparams;
    }

}

/**
 * Implements a renderable submission plugin submission
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_plugin_submission implements renderable {
    /** @var int SUMMARY */
    const SUMMARY                = 10;
    /** @var int FULL */
    const FULL                   = 20;

    /** @var assign_submission_plugin $plugin */
    public $plugin = null;
    /** @var stdClass $submission */
    public $submission = null;
    /** @var string $view */
    public $view = self::SUMMARY;
    /** @var int $coursemoduleid */
    public $coursemoduleid = 0;
    /** @var string returnaction The action to take you back to the current page */
    public $returnaction = '';
    /** @var array returnparams The params to take you back to the current page */
    public $returnparams = array();

    /**
     * Constructor
     * @param assign_submission_plugin $plugin
     * @param stdClass $submission
     * @param string $view one of submission_plugin::SUMMARY, submission_plugin::FULL
     * @param int $coursemoduleid - the course module id
     * @param string $returnaction The action to return to the current page
     * @param array $returnparams The params to return to the current page
     */
    public function __construct(assign_submission_plugin $plugin,
                                stdClass $submission,
                                $view,
                                $coursemoduleid,
                                $returnaction,
                                $returnparams) {
        $this->plugin = $plugin;
        $this->submission = $submission;
        $this->view = $view;
        $this->coursemoduleid = $coursemoduleid;
        $this->returnaction = $returnaction;
        $this->returnparams = $returnparams;
    }
}

/**
 * Renderable feedback status
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_feedback_status implements renderable {

    /** @var string $gradefordisplay the student grade rendered into a format suitable for display */
    public $gradefordisplay = '';
    /** @var mixed the graded date (may be null) */
    public $gradeddate = 0;
    /** @var mixed the grader (may be null) */
    public $grader = null;
    /** @var array feedbackplugins - array of feedback plugins */
    public $feedbackplugins = array();
    /** @var stdClass assign_grade record */
    public $grade = null;
    /** @var int coursemoduleid */
    public $coursemoduleid = 0;
    /** @var string returnaction */
    public $returnaction = '';
    /** @var array returnparams */
    public $returnparams = array();
    /** @var bool canviewfullnames */
    public $canviewfullnames = false;
    /** @var string gradingcontrollergrade The grade information rendered by a grade controller */
    public $gradingcontrollergrade;
    /** @var array information for the given plugins. */
    public $plugins = [];

    /**
     * Constructor
     * @param string $gradefordisplay
     * @param mixed $gradeddate
     * @param mixed $grader
     * @param array $feedbackplugins
     * @param mixed $grade
     * @param int $coursemoduleid
     * @param string $returnaction The action required to return to this page
     * @param array $returnparams The list of params required to return to this page
     * @param bool $canviewfullnames
     * @param string $gradingcontrollergrade The grade information rendered by a grade controller
     */
    public function __construct($gradefordisplay,
                                $gradeddate,
                                $grader,
                                $feedbackplugins,
                                $grade,
                                $coursemoduleid,
                                $returnaction,
                                $returnparams,
                                $canviewfullnames,
                                $gradingcontrollergrade = '') {
        $this->gradefordisplay = $gradefordisplay;
        $this->gradeddate = $gradeddate;
        $this->grader = $grader;
        $this->feedbackplugins = $feedbackplugins;
        $this->grade = $grade;
        $this->coursemoduleid = $coursemoduleid;
        $this->returnaction = $returnaction;
        $this->returnparams = $returnparams;
        $this->canviewfullnames = $canviewfullnames;
        $this->gradingcontrollergrade = $gradingcontrollergrade;
    }
}

/**
 * Renderable submission status
 * @package   mod_assign
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_status_compact extends assign_submission_status implements renderable {
    // Compact view of the submission status. Not in a table etc.
}

/**
 * Used to output the attempt history for a particular assignment.
 *
 * @package mod_assign
 * @copyright 2012 Davo Smith, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_attempt_history implements renderable {

    /** @var array submissions - The list of previous attempts */
    public $submissions = array();
    /** @var array grades - The grades for the previous attempts */
    public $grades = array();
    /** @var array submissionplugins - The list of submission plugins to render the previous attempts */
    public $submissionplugins = array();
    /** @var array feedbackplugins - The list of feedback plugins to render the previous attempts */
    public $feedbackplugins = array();
    /** @var int coursemoduleid - The cmid for the assignment */
    public $coursemoduleid = 0;
    /** @var string returnaction - The action for the next page. */
    public $returnaction = '';
    /** @var string returnparams - The params for the next page. */
    public $returnparams = array();
    /** @var bool cangrade - Does this user have grade capability? */
    public $cangrade = false;
    /** @var string useridlistid - Id of the useridlist stored in cache, this plus rownum determines the userid */
    public $useridlistid = 0;
    /** @var int rownum - The rownum of the user in the useridlistid - this plus useridlistid determines the userid */
    public $rownum = 0;

    /**
     * Constructor
     *
     * @param array $submissions
     * @param array $grades
     * @param array $submissionplugins
     * @param array $feedbackplugins
     * @param int $coursemoduleid
     * @param string $returnaction
     * @param array $returnparams
     * @param bool $cangrade
     * @param int $useridlistid
     * @param int $rownum
     */
    public function __construct($submissions,
                                $grades,
                                $submissionplugins,
                                $feedbackplugins,
                                $coursemoduleid,
                                $returnaction,
                                $returnparams,
                                $cangrade,
                                $useridlistid,
                                $rownum) {
        $this->submissions = $submissions;
        $this->grades = $grades;
        $this->submissionplugins = $submissionplugins;
        $this->feedbackplugins = $feedbackplugins;
        $this->coursemoduleid = $coursemoduleid;
        $this->returnaction = $returnaction;
        $this->returnparams = $returnparams;
        $this->cangrade = $cangrade;
        $this->useridlistid = $useridlistid;
        $this->rownum = $rownum;
    }
}

/**
 * Used to output the attempt history chooser for a particular assignment.
 *
 * @package mod_assign
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_attempt_history_chooser implements renderable, templatable {

    /** @var array submissions - The list of previous attempts */
    public $submissions = array();
    /** @var array grades - The grades for the previous attempts */
    public $grades = array();
    /** @var int coursemoduleid - The cmid for the assignment */
    public $coursemoduleid = 0;
    /** @var int userid - The current userid */
    public $userid = 0;
    /** @var int submission count */
    public $submissioncount;

    /**
     * Constructor
     *
     * @param array $submissions
     * @param array $grades
     * @param int $coursemoduleid
     * @param int $userid
     */
    public function __construct($submissions,
                                $grades,
                                $coursemoduleid,
                                $userid) {
        $this->submissions = $submissions;
        $this->grades = $grades;
        $this->coursemoduleid = $coursemoduleid;
        $this->userid = $userid;
    }

    /**
     * Function to export the renderer data in a format that is suitable for a
     * mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        // Show newest to oldest.
        $export = (object) $this;
        $export->submissions = array_reverse($export->submissions);
        $export->submissioncount = count($export->submissions);

        foreach ($export->submissions as $i => $submission) {
            $grade = null;
            foreach ($export->grades as $onegrade) {
                if ($onegrade->attemptnumber == $submission->attemptnumber) {
                    $submission->grade = $onegrade;
                    break;
                }
            }
            if (!$submission) {
                $submission = new stdClass();
            }

            $editbtn = '';

            if ($submission->timemodified) {
                $submissionsummary = userdate($submission->timemodified);
            } else {
                $submissionsummary = get_string('nosubmission', 'assign');
            }

            $attemptsummaryparams = array('attemptnumber' => $submission->attemptnumber + 1,
                                          'submissionsummary' => $submissionsummary);
            $submission->attemptsummary = get_string('attemptheading', 'assign', $attemptsummaryparams);
            $submission->statussummary = get_string('submissionstatus_' . $submission->status, 'assign');

        }

        return $export;
    }
}

/**
 * Renderable header related to an individual subplugin
 * @package   mod_assign
 * @copyright 2014 Henning Bostelmann
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_plugin_header implements renderable {
    /** @var assign_plugin $plugin */
    public $plugin = null;

    /**
     * Header for a single plugin
     *
     * @param assign_plugin $plugin
     */
    public function __construct(assign_plugin $plugin) {
        $this->plugin = $plugin;
    }
}

/**
 * Renderable grading summary
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_grading_summary implements renderable {
    /** @var int participantcount - The number of users who can submit to this assignment */
    public $participantcount = 0;
    /** @var bool submissiondraftsenabled - Allow submission drafts */
    public $submissiondraftsenabled = false;
    /** @var int submissiondraftscount - The number of submissions in draft status */
    public $submissiondraftscount = 0;
    /** @var bool submissionsenabled - Allow submissions */
    public $submissionsenabled = false;
    /** @var int submissionssubmittedcount - The number of submissions in submitted status */
    public $submissionssubmittedcount = 0;
    /** @var int submissionsneedgradingcount - The number of submissions that need grading */
    public $submissionsneedgradingcount = 0;
    /** @var int duedate - The assignment due date (if one is set) */
    public $duedate = 0;
    /** @var int cutoffdate - The assignment cut off date (if one is set) */
    public $cutoffdate = 0;
    /** @var int timelimit - The assignment time limit (if one is set) */
    public $timelimit = 0;
    /** @var int coursemoduleid - The assignment course module id */
    public $coursemoduleid = 0;
    /** @var boolean teamsubmission - Are team submissions enabled for this assignment */
    public $teamsubmission = false;
    /** @var boolean warnofungroupedusers - Do we need to warn people that there are users without groups */
    public $warnofungroupedusers = false;
    /** @var boolean relativedatesmode - Is the course a relative dates mode course or not */
    public $courserelativedatesmode = false;
    /** @var int coursestartdate - start date of the course as a unix timestamp*/
    public $coursestartdate;
    /** @var boolean cangrade - Can the current user grade students? */
    public $cangrade = false;
    /** @var boolean isvisible - Is the assignment's context module visible to students? */
    public $isvisible = true;
    /** @var cm_info $cm - The course module object. */
    public $cm = null;

    /** @var string no warning needed about group submissions */
    const WARN_GROUPS_NO = false;
    /** @var string warn about group submissions, as groups are required */
    const WARN_GROUPS_REQUIRED = 'warnrequired';
    /** @var string warn about group submissions, as some will submit as 'Default group' */
    const WARN_GROUPS_OPTIONAL = 'warnoptional';

    /**
     * constructor
     *
     * @param int $participantcount
     * @param bool $submissiondraftsenabled
     * @param int $submissiondraftscount
     * @param bool $submissionsenabled
     * @param int $submissionssubmittedcount
     * @param int $cutoffdate
     * @param int $duedate
     * @param int $timelimit
     * @param int $coursemoduleid
     * @param int $submissionsneedgradingcount
     * @param bool $teamsubmission
     * @param string $warnofungroupedusers
     * @param bool $courserelativedatesmode true if the course is using relative dates, false otherwise.
     * @param int $coursestartdate unix timestamp representation of the course start date.
     * @param bool $cangrade
     * @param bool $isvisible
     * @param cm_info|null $cm The course module object.
     */
    public function __construct($participantcount,
                                $submissiondraftsenabled,
                                $submissiondraftscount,
                                $submissionsenabled,
                                $submissionssubmittedcount,
                                $cutoffdate,
                                $duedate,
                                $timelimit,
                                $coursemoduleid,
                                $submissionsneedgradingcount,
                                $teamsubmission,
                                $warnofungroupedusers,
                                $courserelativedatesmode,
                                $coursestartdate,
                                $cangrade = true,
                                $isvisible = true,
                                ?cm_info $cm = null) {
        $this->participantcount = $participantcount;
        $this->submissiondraftsenabled = $submissiondraftsenabled;
        $this->submissiondraftscount = $submissiondraftscount;
        $this->submissionsenabled = $submissionsenabled;
        $this->submissionssubmittedcount = $submissionssubmittedcount;
        $this->duedate = $duedate;
        $this->cutoffdate = $cutoffdate;
        $this->timelimit = $timelimit;
        $this->coursemoduleid = $coursemoduleid;
        $this->submissionsneedgradingcount = $submissionsneedgradingcount;
        $this->teamsubmission = $teamsubmission;
        $this->warnofungroupedusers = $warnofungroupedusers;
        $this->courserelativedatesmode = $courserelativedatesmode;
        $this->coursestartdate = $coursestartdate;
        $this->cangrade = $cangrade;
        $this->isvisible = $isvisible;
        $this->cm = $cm;
    }
}

/**
 * Renderable course index summary
 *
 * @deprecated since Moodle 5.0 (MDL-83888).
 * @todo MDL-84429 Final deprecation in Moodle 6.0.
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_course_index_summary implements renderable {
    /** @var array assignments - A list of course module info and submission counts or statuses */
    public $assignments = array();
    /** @var boolean usesections - Does this course format support sections? */
    public $usesections = false;
    /** @var string courseformat - The current course format name */
    public $courseformatname = '';

    /**
     * constructor
     *
     * @deprecated since Moodle 5.0 (MDL-83888).
     * @todo MDL-84429 Final deprecation in Moodle 6.0.
     * @param boolean $usesections - True if this course format uses sections
     * @param string $courseformatname - The id of this course format
     */
    #[\core\attribute\deprecated(
        since: '5.0',
        mdl: 'MDL-83888',
        reason: 'The assign_course_index_summary class is not used anymore.',
    )]
    public function __construct($usesections, $courseformatname) {
        \core\deprecation::emit_deprecation_if_present([$this, __FUNCTION__]);
        $this->usesections = $usesections;
        $this->courseformatname = $courseformatname;
    }

    /**
     * Add a row of data to display on the course index page
     *
     * @deprecated since Moodle 5.0 (MDL-83888).
     * @todo MDL-84429 Final deprecation in Moodle 6.0.
     * @param int $cmid - The course module id for generating a link
     * @param string $cmname - The course module name for generating a link
     * @param string $sectionname - The name of the course section (only if $usesections is true)
     * @param int $timedue - The due date for the assignment - may be 0 if no duedate
     * @param string $submissioninfo - A string with either the number of submitted assignments, or the
     *                                 status of the current users submission depending on capabilities.
     * @param string $gradeinfo - The current users grade if they have been graded and it is not hidden.
     * @param bool cangrade - Does this user have grade capability?
     */
    #[\core\attribute\deprecated(
        since: '5.0',
        mdl: 'MDL-83888',
        reason: 'The assign_course_index_summary class is not used anymore.',
    )]
    public function add_assign_info($cmid, $cmname, $sectionname, $timedue, $submissioninfo, $gradeinfo, $cangrade = false) {
        \core\deprecation::emit_deprecation_if_present([$this, __FUNCTION__]);
        $this->assignments[] = ['cmid' => $cmid,
                               'cmname' => $cmname,
                               'sectionname' => $sectionname,
                               'timedue' => $timedue,
                               'submissioninfo' => $submissioninfo,
                               'gradeinfo' => $gradeinfo,
                               'cangrade' => $cangrade];
    }


}


/**
 * An assign file class that extends rendererable class and is used by the assign module.
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_files implements renderable {
    /** @var context $context */
    public $context;
    /** @var array $dir as returned by {@see file_storage::get_area_tree()}. */
    public $dir;
    /** @var MoodleQuickForm $portfolioform */
    public $portfolioform;
    /** @var stdClass $cm course module */
    public $cm;
    /** @var stdClass $course */
    public $course;

    /**
     * The constructor
     *
     * @param context $context
     * @param int $sid
     * @param string $filearea
     * @param string $component
     * @param stdClass $course
     * @param stdClass $cm
     */
    public function __construct(context $context, $sid, $filearea, $component, $course = null, $cm = null) {
        global $CFG;
        if (empty($course) || empty($cm)) {
            list($context, $course, $cm) = get_context_info_array($context->id);
        }

        $this->context = $context;
        $this->cm = $cm;
        $this->course = $course;
        $fs = get_file_storage();
        $this->dir = $fs->get_area_tree($this->context->id, $component, $filearea, $sid);

        $files = $fs->get_area_files($this->context->id,
                                     $component,
                                     $filearea,
                                     $sid,
                                     'timemodified',
                                     false);

        if (!empty($CFG->enableportfolios)) {
            require_once($CFG->libdir . '/portfoliolib.php');
            if (count($files) >= 1 && !empty($sid) &&
                    has_capability('mod/assign:exportownsubmission', $this->context)) {
                $button = new portfolio_add_button();
                $callbackparams = array('cmid' => $this->cm->id,
                                        'sid' => $sid,
                                        'area' => $filearea,
                                        'component' => $component);
                $button->set_callback_options('assign_portfolio_caller',
                                              $callbackparams,
                                              'mod_assign');
                $button->reset_formats();
                $this->portfolioform = $button->to_html(PORTFOLIO_ADD_TEXT_LINK);
            }
        }
    }

    /**
     * Preprocessing the file list to add the portfolio links if required.
     *
     * @param array $dir
     * @param string $filearea
     * @param string $component
     * @deprecated since Moodle 4.3
     */
    public function preprocess($dir, $filearea, $component) {
        // Nothing to do here any more.
        debugging('The preprocess method has been deprecated since Moodle 4.3.', DEBUG_DEVELOPER);
    }

    /**
     * Get the modified time of the specified file.
     * @param stored_file $file
     * @return string
     */
    public function get_modified_time(stored_file $file): string {
        return userdate(
            $file->get_timemodified(),
            get_string('strftimedatetime', 'langconfig'),
        );
    }

    /**
     * Get the URL used to view the file.
     *
     * @param stored_file
     * @return moodle_url
     */
    public function get_file_url(stored_file $file): moodle_url {
        return \moodle_url::make_pluginfile_url(
            $this->context->id,
            $file->get_component(),
            $file->get_filearea(),
            $file->get_itemid(),
            $file->get_filepath(),
            $file->get_filename(),
            true,
        );
    }


}
