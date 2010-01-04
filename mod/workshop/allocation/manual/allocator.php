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
 * Allows user to allocate the submissions manually
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(__FILE__)) . '/lib.php');                  // interface definition
require_once(dirname(dirname(dirname(__FILE__))) . '/locallib.php');    // workshop internal API

/**
 * Allows users to allocate submissions for review manually
 */
class workshop_manual_allocator implements workshop_allocator {

    /** constants that are used to pass status messages between init() and ui() */
    const MSG_ADDED         = 1;
    const MSG_NOSUBMISSION  = 2;
    const MSG_EXISTS        = 3;
    const MSG_CONFIRM_DEL   = 4;
    const MSG_DELETED       = 5;
    const MSG_DELETE_ERROR  = 6;

    /** @var workshop instance */
    protected $workshop;

    /**
     * @param workshop $workshop Workshop API object
     */
    public function __construct(workshop $workshop) {
        $this->workshop = $workshop;
    }

    /**
     * Allocate submissions as requested by user
     */
    public function init() {
        global $PAGE;

        $mode = optional_param('mode', 'display', PARAM_ALPHA);

        switch ($mode) {
        case 'new':
            if (!confirm_sesskey()) {
                throw new moodle_exception('confirmsesskeybad');
            }
            $reviewerid = required_param('by', PARAM_INT);
            $authorid   = required_param('of', PARAM_INT);
            $m          = array();  // message object to be passed to the next page
            $submission = $this->workshop->get_submission_by_author($authorid);
            if (!$submission) {
                // nothing submitted by the given user
                $m[] = self::MSG_NOSUBMISSION;
                $m[] = $authorid;

            } else {
                // ok, we have the submission
                $res = $this->workshop->add_allocation($submission, $reviewerid);
                if ($res == workshop::ALLOCATION_EXISTS) {
                    $m[] = self::MSG_EXISTS;
                    $m[] = $submission->userid;
                    $m[] = $reviewerid;
                } else {
                    $m[] = self::MSG_ADDED;
                    $m[] = $submission->userid;
                    $m[] = $reviewerid;
                }
            }
            $m = implode('-', $m);  // serialize message object to be passed via URL
            redirect($PAGE->url->out(false, array('m' => $m), false));
            break;
        case 'del':
            if (!confirm_sesskey()) {
                throw new moodle_exception('confirmsesskeybad');
            }
            $assessmentid   = required_param('what', PARAM_INT);
            $confirmed      = optional_param('confirm', 0, PARAM_INT);
            $assessment     = $this->workshop->get_assessment_by_id($assessmentid);
            if ($assessment) {
                if (!$confirmed) {
                    $m[] = self::MSG_CONFIRM_DEL;
                    $m[] = $assessment->id;
                    $m[] = $assessment->authorid;
                    $m[] = $assessment->reviewerid;
                    if (is_null($assessment->grade)) {
                        $m[] = 0;
                    } else {
                        $m[] = 1;
                    }
                } else {
                    if($this->workshop->delete_assessment($assessment->id)) {
                        $m[] = self::MSG_DELETED;
                        $m[] = $assessment->authorid;
                        $m[] = $assessment->reviewerid;
                    } else {
                        $m[] = self::MSG_DELETE_ERROR;
                        $m[] = $assessment->authorid;
                        $m[] = $assessment->reviewerid;
                    }
                }
                $m = implode('-', $m);  // serialize message object to be passed via URL
                redirect($PAGE->url->out(false, array('m' => $m), false));
            }
            break;
        }
    }

    /**
     * Prints user interface - current allocation and a form to edit it
     */
    public function ui() {
        global $PAGE;
        global $CFG;    // bacause we include other libs here
        global $OUTPUT;

        $hlauthorid     = -1;           // highlight this author
        $hlreviewerid   = -1;           // highlight this reviewer
        $msg            = new stdClass(); // message to render

        $m  = optional_param('m', '', PARAM_ALPHANUMEXT);   // message stdClass
        if ($m) {
            $m = explode('-', $m);  // unserialize
            switch ($m[0]) {
            case self::MSG_ADDED:
                $hlauthorid     = $m[1];
                $hlreviewerid   = $m[2];
                $msg->text      = get_string('allocationadded', 'workshopallocation_manual');
                $msg->sty       = 'ok';
                break;
            case self::MSG_EXISTS:
                $hlauthorid     = $m[1];
                $hlreviewerid   = $m[2];
                $msg->text      = get_string('allocationexists', 'workshopallocation_manual');
                $msg->sty       = 'info';
                break;
            case self::MSG_NOSUBMISSION:
                $hlauthorid     = $m[1];
                $msg->text      = get_string('nosubmissionfound', 'workshop');
                $msg->sty       = 'error';
                break;
            case self::MSG_CONFIRM_DEL:
                $hlauthorid     = $m[2];
                $hlreviewerid   = $m[3];
                if ($m[4] == 0) {
                    $msg->text  = get_string('areyousuretodeallocate', 'workshopallocation_manual');
                    $msg->sty   = 'info';
                } else {
                    $msg->text  = get_string('areyousuretodeallocategraded', 'workshopallocation_manual');
                    $msg->sty   = 'error';
                }
                break;
            case self::MSG_DELETED:
                $hlauthorid     = $m[1];
                $hlreviewerid   = $m[2];
                $msg->text      = get_string('assessmentdeleted', 'workshop');
                $msg->sty       = 'ok';
                break;
            case self::MSG_DELETE_ERROR:
                $hlauthorid     = $m[1];
                $hlreviewerid   = $m[2];
                $msg->text      = get_string('assessmentnotdeleted', 'workshop');
                $msg->sty       = 'error';
                break;
            }
            if ($m[0] == self::MSG_CONFIRM_DEL) {
                $form = new html_form();
                $form->url = new moodle_url($PAGE->url, array('mode' => 'del', 'what' => $m[1], 'confirm' => 1));
                $form->button = new html_button();
                $form->button->text = get_string('iamsure', 'workshop');
                $form->method = 'post';
                $msg->extra = $OUTPUT->button($form);
            }
        }

        $peers = array();
        $rs = $this->workshop->get_allocations_recordset();
        foreach ($rs as $allocation) {
            $currentuserid = $allocation->authorid;
            if (!isset($peers[$currentuserid])) {
                $peers[$currentuserid]                   = new stdClass();
                $peers[$currentuserid]->id               = $allocation->authorid;
                $peers[$currentuserid]->firstname        = $allocation->authorfirstname;
                $peers[$currentuserid]->lastname         = $allocation->authorlastname;
                $peers[$currentuserid]->picture          = $allocation->authorpicture;
                $peers[$currentuserid]->imagealt         = $allocation->authorimagealt;
                $peers[$currentuserid]->submissionid     = $allocation->submissionid;
                $peers[$currentuserid]->submissiontitle  = $allocation->submissiontitle;
                $peers[$currentuserid]->submissiongrade  = $allocation->submissiongrade;
                $peers[$currentuserid]->reviewedby       = array(); // users who are reviewing this user's submission
                $peers[$currentuserid]->reviewerof       = array(); // users whom submission is being reviewed by this user
            }
            if (!empty($allocation->reviewerid)) {
                // example: "submission of user with id 45 is reviewed by user with id 87 in the assessment record 12"
                $peers[$currentuserid]->reviewedby[$allocation->reviewerid] = $allocation->assessmentid;
            }
        }
        $rs->close();

        foreach ($peers as $author) {
            foreach ($author->reviewedby as $reviewerid => $assessmentid) {
                if (isset($peers[$reviewerid])) {
                    // example: "user with id 87 is reviewer of the work submitted by user id 45 in the assessment record 12"
                    $peers[$reviewerid]->reviewerof[$author->id] = $assessmentid;
                }
            }
        }

        // We have all data. Let it pass to the renderer and return the output
        // Here, we do not use neither the core renderer nor the workshop one but use an own one
        require_once(dirname(__FILE__) . '/renderer.php');
        $uioutput = $PAGE->theme->get_renderer('workshopallocation_manual', $PAGE);
        return $uioutput->display_allocations($this->workshop, $peers, $hlauthorid, $hlreviewerid, $msg);
    }

}
