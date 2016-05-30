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
 * This file contains a renderer for the assignment class
 *
 * @package   assignfeedback_file
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * A renderable summary of the zip import
 *
 * @package assignfeedback_file
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assignfeedback_file_import_summary implements renderable {
    /** @var int $cmid Course module id for constructing navigation links */
    public $cmid = 0;
    /** @var int $userswithnewfeedback The number of users who have received new feedback */
    public $userswithnewfeedback = 0;
    /** @var int $feedbackfilesadded The number of new feedback files */
    public $feedbackfilesadded = 0;
    /** @var int $feedbackfilesupdated The number of updated feedback files */
    public $feedbackfilesupdated = 0;

    /**
     * Constructor for this renderable class
     *
     * @param int $cmid - The course module id for navigation
     * @param int $userswithnewfeedback - The number of users with new feedback
     * @param int $feedbackfilesadded - The number of feedback files added
     * @param int $feedbackfilesupdated - The number of feedback files updated
     */
    public function __construct($cmid, $userswithnewfeedback, $feedbackfilesadded, $feedbackfilesupdated) {
        $this->cmid = $cmid;
        $this->userswithnewfeedback = $userswithnewfeedback;
        $this->feedbackfilesadded = $feedbackfilesadded;
        $this->feedbackfilesupdated = $feedbackfilesupdated;
    }
}
