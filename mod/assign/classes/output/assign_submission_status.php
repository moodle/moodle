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
 * This file contains the definition for the renderable assign submission status.
 *
 * @package   mod_assign
 * @copyright 2020 Matt Porritt <mattp@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_assign\output;

/**
 * This file contains the definition for the renderable assign submission status.
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_status implements \renderable {
    /** @var int STUDENT_VIEW */
    const STUDENT_VIEW     = 10;
    /** @var int GRADER_VIEW */
    const GRADER_VIEW      = 20;

    /** @var int allowsubmissionsfromdate */
    public $allowsubmissionsfromdate = 0;
    /** @var bool alwaysshowdescription */
    public $alwaysshowdescription = false;
    /** @var mixed the submission info (may be null or an integer) */
    public $submission = null;
    /** @var boolean teamsubmissionenabled - true or false */
    public $teamsubmissionenabled = false;
    /** @var \stdClass teamsubmission the team submission info (may be null) */
    public $teamsubmission = null;
    /** @var mixed submissiongroup the submission group info (may be null) */
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
    /** @var \context context */
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
    /** @var int The time limit for the assignment */
    public $timelimit = 0;
    /** @var bool */
    public $caneditowner;

    /**
     * Constructor
     *
     * @param int $allowsubmissionsfromdate
     * @param bool $alwaysshowdescription
     * @param mixed $submission
     * @param bool $teamsubmissionenabled
     * @param \stdClass $teamsubmission
     * @param mixed $submissiongroup
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
     * @param int $extensionduedate Any extension to the due date granted for this user.
     * @param \context $context Any extension to the due date granted for this user.
     * @param bool $blindmarking Should we hide student identities from graders?
     * @param string $gradingcontrollerpreview
     * @param string $attemptreopenmethod The method of reopening student attempts.
     * @param int $maxattempts How many attempts can a student make?
     * @param string $gradingstatus The submission status (ie. Graded, Not Released etc).
     * @param bool $preventsubmissionnotingroup Prevent submission if user is not in a group.
     * @param array $usergroups Array containing all groups the user is assigned to.
     * @param int $timelimit The time limit for the assignment.
     */
    public function __construct(
        $allowsubmissionsfromdate,
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
        $usergroups,
        $timelimit
    ) {
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
        $this->timelimit = $timelimit;
    }
}
