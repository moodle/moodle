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
 * Code for the submissions allocation support is defined here
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Allocators are responsible for assigning submissions to reviewers for assessments
 *
 * The task of the allocator is to assign the correct number of submissions to reviewers 
 * for assessment. Several allocation methods are expected and they can be combined. For
 * example, teacher can allocate several submissions manually (by 'manual' allocator) and
 * then let the other submissions being allocated randomly (by 'random' allocator).
 * Allocation is actually done by creating an initial assessment record in the
 * workshop_assessments table.
 */
interface workshop_allocator {
    
    /**
     * Initialize the allocator and eventually process submitted data
     *
     * This method is called soon after the allocator is constructed and before any output 
     * is generated. Therefore is may process any data submitted and do other tasks.
     * It should not generate any output
     *
     * @throws moodle_workshop_exception
     * @return void
     */
    public function init();


    /**
     * Returns HTML to be displayed as the user interface
     *
     * If a form is part of the UI, the caller should have call $PAGE->set_url(...)
     * 
     * @access public
     * @return string HTML to be displayed
     */
    public function ui();

}


/**
 * Return list of available allocation methods
 *
 * @access public
 * @return array Array ['string' => 'string'] of localized allocation method names
 */
function workshop_installed_allocators() {

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
 * @param object $workshop Workshop record
 * @param object $method The name of the allocation method, must be PARAM_ALPHA
 * @return object Instance of submissions allocator
 */
function workshop_allocator_instance(workshop $workshop, $method) {

    $allocationlib = dirname(__FILE__) . '/' . $method . '/allocator.php';
    if (is_readable($allocationlib)) {
        require_once($allocationlib);
    } else {
        throw new moodle_exception('missingallocator', 'workshop');
    }
    $classname = 'workshop_' . $method . '_allocator';
    return new $classname($workshop);
}


/**
 * Returns the list of submissions and assessments allocated to them in the given workshop
 *
 * Submissions without allocated assessment are returned too, having assessment attributes null.
 * This also fetches all other associated information (like details about the author and reviewer)
 * needed to produce allocation reports.
 * The returned structure is recordset of objects with following properties:
 * [submissionid] [submissiontitle] [authorid] [authorfirstname] 
 * [authorlastname] [authorpicture] [authorimagealt] [assessmentid] 
 * [timeallocated] [reviewerid] [reviewerfirstname] [reviewerlastname] 
 * [reviewerpicture] [reviewerimagealt]
 *
 * @param object $workshop The workshop object
 * @return object Recordset of allocations
 */
function workshop_get_allocations(workshop $workshop) {
    global $DB;

    $sql = 'SELECT s.id AS submissionid, s.title AS submissiontitle, s.userid AS authorid, 
                    author.firstname AS authorfirstname, author.lastname AS authorlastname, 
                    author.picture AS authorpicture, author.imagealt AS authorimagealt,
                    a.id AS assessmentid, a.timecreated AS timeallocated, a.userid AS reviewerid, 
                    reviewer.firstname AS reviewerfirstname, reviewer.lastname AS reviewerlastname,
                    reviewer.picture as reviewerpicture, reviewer.imagealt AS reviewerimagealt
            FROM {workshop_submissions} s
                LEFT JOIN {workshop_assessments} a ON (s.id = a.submissionid)
                LEFT JOIN {user} author ON (s.userid = author.id)
                LEFT JOIN {user} reviewer ON (a.userid = reviewer.id)
            WHERE s.workshopid = ?
            ORDER BY author.lastname,author.firstname,reviewer.lastname,reviewer.firstname';
    return $DB->get_recordset_sql($sql, array($workshop->id));
}


/**
 * Allocate a submission to a user for review
 * 
 * @param object $workshop Workshop record
 * @param object $submission Submission record
 * @param int $reviewerid User ID
 * @access public
 * @return int ID of the new assessment or an error code
 */
function workshop_add_allocation(workshop $workshop, stdClass $submission, $reviewerid) {
    global $DB;

    if ($DB->record_exists('workshop_assessments', array('submissionid' => $submission->id, 'userid' => $reviewerid))) {
        return WORKSHOP_ALLOCATION_EXISTS;
    }

    $now = time();
    $assessment = new stdClass();
    $assessment->submissionid = $submission->id;
    $assessment->userid         = $reviewerid;
    $assessment->timecreated    = $now;
    $assessment->timemodified   = $now;

    return $DB->insert_record('workshop_assessments', $assessment);
}

