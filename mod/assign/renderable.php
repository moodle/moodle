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

    /** @var stding $gradefordisplay the student grade rendered into a format suitable for display */
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
     */
    public function __construct($gradefordisplay,
                                $gradeddate,
                                $grader,
                                $feedbackplugins,
                                $grade,
                                $coursemoduleid,
                                $returnaction,
                                $returnparams,
                                $canviewfullnames) {
        $this->gradefordisplay = $gradefordisplay;
        $this->gradeddate = $gradeddate;
        $this->grader = $grader;
        $this->feedbackplugins = $feedbackplugins;
        $this->grade = $grade;
        $this->coursemoduleid = $coursemoduleid;
        $this->returnaction = $returnaction;
        $this->returnparams = $returnparams;
        $this->canviewfullnames = $canviewfullnames;
    }
}

/**
 * Renderable submission status
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_status implements renderable {
    /** @var int STUDENT_VIEW */
    const STUDENT_VIEW     = 10;
    /** @var int GRADER_VIEW */
    const GRADER_VIEW      = 20;

    /** @var int allowsubmissionsfromdate */
    public $allowsubmissionsfromdate = 0;
    /** @var bool alwaysshowdescription */
    public $alwaysshowdescription = false;
    /** @var stdClass the submission info (may be null) */
    public $submission = null;
    /** @var boolean teamsubmissionenabled - true or false */
    public $teamsubmissionenabled = false;
    /** @var stdClass teamsubmission the team submission info (may be null) */
    public $teamsubmission = null;
    /** @var stdClass submissiongroup the submission group info (may be null) */
    public $submissiongroup = null;
    /** @var array submissiongroupmemberswhoneedtosubmit list of users who still need to submit */
    public $submissiongroupmemberswhoneedtosubmit = array();
    /** @var bool submissionsenabled */
    public $submissionsenabled = false;
    /** @var bool locked */
    public $locked = false;
    /** @var bool graded */
    public $graded = false;
    /** @var int duedate */
    public $duedate = 0;
    /** @var int cutoffdate */
    public $cutoffdate = 0;
    /** @var array submissionplugins - the list of submission plugins */
    public $submissionplugins = array();
    /** @var string returnaction */
    public $returnaction = '';
    /** @var string returnparams */
    public $returnparams = array();
    /** @var int courseid */
    public $courseid = 0;
    /** @var int coursemoduleid */
    public $coursemoduleid = 0;
    /** @var int the view (STUDENT_VIEW OR GRADER_VIEW) */
    public $view = self::STUDENT_VIEW;
    /** @var bool canviewfullnames */
    public $canviewfullnames = false;
    /** @var bool canedit */
    public $canedit = false;
    /** @var bool cansubmit */
    public $cansubmit = false;
    /** @var int extensionduedate */
    public $extensionduedate = 0;
    /** @var context context */
    public $context = 0;
    /** @var bool blindmarking - Should we hide student identities from graders? */
    public $blindmarking = false;
    /** @var string gradingcontrollerpreview */
    public $gradingcontrollerpreview = '';
    /** @var string attemptreopenmethod */
    public $attemptreopenmethod = 'none';
    /** @var int maxattempts */
    public $maxattempts = -1;
    /** @var string gradingstatus */
    public $gradingstatus = '';
    /** @var bool preventsubmissionnotingroup */
    public $preventsubmissionnotingroup = 0;
    /** @var array usergroups */
    public $usergroups = array();


    /**
     * Constructor
     *
     * @param int $allowsubmissionsfromdate
     * @param bool $alwaysshowdescription
     * @param stdClass $submission
     * @param bool $teamsubmissionenabled
     * @param stdClass $teamsubmission
     * @param int $submissiongroup
     * @param array $submissiongroupmemberswhoneedtosubmit
     * @param bool $submissionsenabled
     * @param bool $locked
     * @param bool $graded
     * @param int $duedate
     * @param int $cutoffdate
     * @param array $submissionplugins
     * @param string $returnaction
     * @param array $returnparams
     * @param int $coursemoduleid
     * @param int $courseid
     * @param string $view
     * @param bool $canedit
     * @param bool $cansubmit
     * @param bool $canviewfullnames
     * @param int $extensionduedate - Any extension to the due date granted for this user
     * @param context $context - Any extension to the due date granted for this user
     * @param bool $blindmarking - Should we hide student identities from graders?
     * @param string $gradingcontrollerpreview
     * @param string $attemptreopenmethod - The method of reopening student attempts.
     * @param int $maxattempts - How many attempts can a student make?
     * @param string $gradingstatus - The submission status (ie. Graded, Not Released etc).
     * @param bool $preventsubmissionnotingroup - Prevent submission if user is not in a group
     * @param array $usergroups - Array containing all groups the user is assigned to
     */
    public function __construct($allowsubmissionsfromdate,
                                $alwaysshowdescription,
                                $submission,
                                $teamsubmissionenabled,
                                $teamsubmission,
                                $submissiongroup,
                                $submissiongroupmemberswhoneedtosubmit,
                                $submissionsenabled,
                                $locked,
                                $graded,
                                $duedate,
                                $cutoffdate,
                                $submissionplugins,
                                $returnaction,
                                $returnparams,
                                $coursemoduleid,
                                $courseid,
                                $view,
                                $canedit,
                                $cansubmit,
                                $canviewfullnames,
                                $extensionduedate,
                                $context,
                                $blindmarking,
                                $gradingcontrollerpreview,
                                $attemptreopenmethod,
                                $maxattempts,
                                $gradingstatus,
                                $preventsubmissionnotingroup,
                                $usergroups) {
        $this->allowsubmissionsfromdate = $allowsubmissionsfromdate;
        $this->alwaysshowdescription = $alwaysshowdescription;
        $this->submission = $submission;
        $this->teamsubmissionenabled = $teamsubmissionenabled;
        $this->teamsubmission = $teamsubmission;
        $this->submissiongroup = $submissiongroup;
        $this->submissiongroupmemberswhoneedtosubmit = $submissiongroupmemberswhoneedtosubmit;
        $this->submissionsenabled = $submissionsenabled;
        $this->locked = $locked;
        $this->graded = $graded;
        $this->duedate = $duedate;
        $this->cutoffdate = $cutoffdate;
        $this->submissionplugins = $submissionplugins;
        $this->returnaction = $returnaction;
        $this->returnparams = $returnparams;
        $this->coursemoduleid = $coursemoduleid;
        $this->courseid = $courseid;
        $this->view = $view;
        $this->canedit = $canedit;
        $this->cansubmit = $cansubmit;
        $this->canviewfullnames = $canviewfullnames;
        $this->extensionduedate = $extensionduedate;
        $this->context = $context;
        $this->blindmarking = $blindmarking;
        $this->gradingcontrollerpreview = $gradingcontrollerpreview;
        $this->attemptreopenmethod = $attemptreopenmethod;
        $this->maxattempts = $maxattempts;
        $this->gradingstatus = $gradingstatus;
        $this->preventsubmissionnotingroup = $preventsubmissionnotingroup;
        $this->usergroups = $usergroups;
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
 * Renderable header
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_header implements renderable {
    /** @var stdClass the assign record  */
    public $assign = null;
    /** @var mixed context|null the context record  */
    public $context = null;
    /** @var bool $showintro - show or hide the intro */
    public $showintro = false;
    /** @var int coursemoduleid - The course module id */
    public $coursemoduleid = 0;
    /** @var string $subpage optional subpage (extra level in the breadcrumbs) */
    public $subpage = '';
    /** @var string $preface optional preface (text to show before the heading) */
    public $preface = '';
    /** @var string $postfix optional postfix (text to show after the intro) */
    public $postfix = '';

    /**
     * Constructor
     *
     * @param stdClass $assign  - the assign database record
     * @param mixed $context context|null the course module context
     * @param bool $showintro  - show or hide the intro
     * @param int $coursemoduleid  - the course module id
     * @param string $subpage  - an optional sub page in the navigation
     * @param string $preface  - an optional preface to show before the heading
     */
    public function __construct(stdClass $assign,
                                $context,
                                $showintro,
                                $coursemoduleid,
                                $subpage='',
                                $preface='',
                                $postfix='') {
        $this->assign = $assign;
        $this->context = $context;
        $this->showintro = $showintro;
        $this->coursemoduleid = $coursemoduleid;
        $this->subpage = $subpage;
        $this->preface = $preface;
        $this->postfix = $postfix;
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
    /** @var int coursemoduleid - The assignment course module id */
    public $coursemoduleid = 0;
    /** @var boolean teamsubmission - Are team submissions enabled for this assignment */
    public $teamsubmission = false;
    /** @var boolean warnofungroupedusers - Do we need to warn people that there are users without groups */
    public $warnofungroupedusers = false;

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
     * @param int $coursemoduleid
     * @param int $submissionsneedgradingcount
     * @param bool $teamsubmission
     */
    public function __construct($participantcount,
                                $submissiondraftsenabled,
                                $submissiondraftscount,
                                $submissionsenabled,
                                $submissionssubmittedcount,
                                $cutoffdate,
                                $duedate,
                                $coursemoduleid,
                                $submissionsneedgradingcount,
                                $teamsubmission,
                                $warnofungroupedusers) {
        $this->participantcount = $participantcount;
        $this->submissiondraftsenabled = $submissiondraftsenabled;
        $this->submissiondraftscount = $submissiondraftscount;
        $this->submissionsenabled = $submissionsenabled;
        $this->submissionssubmittedcount = $submissionssubmittedcount;
        $this->duedate = $duedate;
        $this->cutoffdate = $cutoffdate;
        $this->coursemoduleid = $coursemoduleid;
        $this->submissionsneedgradingcount = $submissionsneedgradingcount;
        $this->teamsubmission = $teamsubmission;
        $this->warnofungroupedusers = $warnofungroupedusers;
    }
}

/**
 * Renderable course index summary
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
     * @param boolean $usesections - True if this course format uses sections
     * @param string $courseformatname - The id of this course format
     */
    public function __construct($usesections, $courseformatname) {
        $this->usesections = $usesections;
        $this->courseformatname = $courseformatname;
    }

    /**
     * Add a row of data to display on the course index page
     *
     * @param int $cmid - The course module id for generating a link
     * @param string $cmname - The course module name for generating a link
     * @param string $sectionname - The name of the course section (only if $usesections is true)
     * @param int $timedue - The due date for the assignment - may be 0 if no duedate
     * @param string $submissioninfo - A string with either the number of submitted assignments, or the
     *                                 status of the current users submission depending on capabilities.
     * @param string $gradeinfo - The current users grade if they have been graded and it is not hidden.
     */
    public function add_assign_info($cmid, $cmname, $sectionname, $timedue, $submissioninfo, $gradeinfo) {
        $this->assignments[] = array('cmid'=>$cmid,
                               'cmname'=>$cmname,
                               'sectionname'=>$sectionname,
                               'timedue'=>$timedue,
                               'submissioninfo'=>$submissioninfo,
                               'gradeinfo'=>$gradeinfo);
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
    /** @var string $context */
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
     */
    public function __construct(context $context, $sid, $filearea, $component) {
        global $CFG;
        $this->context = $context;
        list($context, $course, $cm) = get_context_info_array($context->id);
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

        $this->preprocess($this->dir, $filearea, $component);
    }

    /**
     * Preprocessing the file list to add the portfolio links if required.
     *
     * @param array $dir
     * @param string $filearea
     * @param string $component
     * @return void
     */
    public function preprocess($dir, $filearea, $component) {
        global $CFG;

        foreach ($dir['subdirs'] as $subdir) {
            $this->preprocess($subdir, $filearea, $component);
        }
        foreach ($dir['files'] as $file) {
            $file->portfoliobutton = '';
            if (!empty($CFG->enableportfolios)) {
                require_once($CFG->libdir . '/portfoliolib.php');
                $button = new portfolio_add_button();
                if (has_capability('mod/assign:exportownsubmission', $this->context)) {
                    $portfolioparams = array('cmid' => $this->cm->id, 'fileid' => $file->get_id());
                    $button->set_callback_options('assign_portfolio_caller',
                                                  $portfolioparams,
                                                  'mod_assign');
                    $button->set_format_by_file($file);
                    $file->portfoliobutton = $button->to_html(PORTFOLIO_ADD_ICON_LINK);
                }
            }
            $path = '/' .
                    $this->context->id .
                    '/' .
                    $component .
                    '/' .
                    $filearea .
                    '/' .
                    $file->get_itemid() .
                    $file->get_filepath() .
                    $file->get_filename();
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php", $path, true);
            $filename = $file->get_filename();
            $file->fileurl = html_writer::link($url, $filename, [
                    'target' => '_blank',
                ]);
        }
    }
}
