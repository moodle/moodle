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
 * This file contains the definition for the renderable classes for the submissions
 *
 * @package mod_panoptosubmission
 * @copyright Panopto 2023
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Renderable grading summary.
 *
 * @package   mod_panoptosubmission
 * @copyright Panopto 2023
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class panoptosubmission_grading_summary implements renderable {
    /** @var int participantcount - The number of users who can submit to this submission */
    public $participantcount = 0;
    /** @var bool submissionsenabled - Allow submissions */
    public $submissionsenabled = false;
    /** @var int submissionssubmittedcount - The number of submissions in submitted status */
    public $submissionssubmittedcount = 0;
    /** @var int submissionsneedgradingcount - The number of submissions that need grading */
    public $submissionsneedgradingcount = 0;
    /** @var int duedate - The submission due date (if one is set) */
    public $duedate = 0;
    /** @var int cutoffdate - The submission cut off date (if one is set) */
    public $cutoffdate = 0;
    /** @var int timelimit - The submission time limit (if one is set) */
    public $timelimit = 0;
    /** @var int coursemoduleid - The submission course module id */
    public $coursemoduleid = 0;
    /** @var bool relativedatesmode - Is the course a relative dates mode course or not */
    public $courserelativedatesmode = false;
    /** @var int coursestartdate - start date of the course as a unix timestamp*/
    public $coursestartdate;
    /** @var bool isvisible - Is the submission's context module visible to students? */
    public $isvisible = true;

    /**
     * constructor
     *
     * @param int $participantcount
     * @param bool $submissionsenabled
     * @param int $submissionssubmittedcount
     * @param int $cutoffdate
     * @param int $duedate
     * @param int $timelimit
     * @param int $coursemoduleid
     * @param int $submissionsneedgradingcount
     * @param bool $courserelativedatesmode true if the course is using relative dates, false otherwise.
     * @param int $coursestartdate unix timestamp representation of the course start date.
     * @param bool $isvisible
     */
    public function __construct($participantcount,
                                $submissionsenabled,
                                $submissionssubmittedcount,
                                $cutoffdate,
                                $duedate,
                                $timelimit,
                                $coursemoduleid,
                                $submissionsneedgradingcount,
                                $courserelativedatesmode,
                                $coursestartdate,
                                $isvisible = true) {
        $this->participantcount = $participantcount;
        $this->submissionsenabled = $submissionsenabled;
        $this->submissionssubmittedcount = $submissionssubmittedcount;
        $this->duedate = $duedate;
        $this->cutoffdate = $cutoffdate;
        $this->timelimit = $timelimit;
        $this->coursemoduleid = $coursemoduleid;
        $this->submissionsneedgradingcount = $submissionsneedgradingcount;
        $this->courserelativedatesmode = $courserelativedatesmode;
        $this->coursestartdate = $coursestartdate;
        $this->isvisible = $isvisible;
    }
}
