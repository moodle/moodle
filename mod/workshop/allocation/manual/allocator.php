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
 * These constants are used to pass status messages between init() and ui()
 */
define('WORKSHOP_ALLOCATION_MANUAL_MSG_ADDED',          1);
define('WORKSHOP_ALLOCATION_MANUAL_MSG_NOSUBMISSION',   2);
define('WORKSHOP_ALLOCATION_MANUAL_MSG_EXISTS',         3);
define('WORKSHOP_ALLOCATION_MANUAL_MSG_WOSUBMISSION',   4);
define('WORKSHOP_ALLOCATION_MANUAL_MSG_CONFIRM_DEL',    5);
define('WORKSHOP_ALLOCATION_MANUAL_MSG_DELETED',        6);


/**
 * Allows users to allocate submissions for review manually
 */
class workshop_manual_allocator implements workshop_allocator {

    /** workshop instance */
    protected $workshop;


    /**
     * @param stdClass $workshop Workshop record
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
                throw new moodle_workshop_exception($this->workshop, 'confirmsesskeybad');
            }
            $reviewerid = required_param('by', PARAM_INT);
            $authorid   = required_param('of', PARAM_INT);
            $m          = array();  // message object to be passed to the next page
            $rs         = $this->workshop->get_submissions($authorid);
            $submission = $rs->current();
            $rs->close();
            if (!$submission) {
                // nothing submitted by the given user
                $m[] = WORKSHOP_ALLOCATION_MANUAL_MSG_NOSUBMISSION;
                $m[] = $authorid;
                
            } else {
                // ok, we have the submission
                $res = $this->workshop->add_allocation($submission, $reviewerid);
                if ($res == WORKSHOP_ALLOCATION_EXISTS) {
                    $m[] = WORKSHOP_ALLOCATION_MANUAL_MSG_EXISTS;
                    $m[] = $submission->userid;
                    $m[] = $reviewerid;
                } elseif ($res == WORKSHOP_ALLOCATION_WOSUBMISSION) {
                    $m[] = WORKSHOP_ALLOCATION_MANUAL_MSG_WOSUBMISSION;
                    $m[] = $submission->userid;
                    $m[] = $reviewerid;
                } else {
                    $m[] = WORKSHOP_ALLOCATION_MANUAL_MSG_ADDED;
                    $m[] = $submission->userid;
                    $m[] = $reviewerid;
                }
            }
            $m = implode('-', $m);  // serialize message object to be passed via URL
            redirect($PAGE->url->out(false, array('m' => $m), false));
            break;
        case 'del':
            if (!confirm_sesskey()) {
                throw new moodle_workshop_exception($this->workshop, 'confirmsesskeybad');
            }
            $assessmentid   = required_param('what', PARAM_INT);
            $confirmed      = optional_param('confirm', 0, PARAM_INT);
            $rs             = $this->workshop->get_assessments('all', $assessmentid);
            $assessment     = $rs->current();
            $rs->close();
            if ($assessment) {
                if (!$confirmed) {
                    $m[] = WORKSHOP_ALLOCATION_MANUAL_MSG_CONFIRM_DEL;
                    $m[] = $assessment->id;
                    $m[] = $assessment->authorid;
                    $m[] = $assessment->reviewerid;
                    if (is_null($assessment->grade)) {
                        $m[] = 0;
                    } else {
                        $m[] = 1;
                    }
                } else {
                    $res = $this->workshop->delete_assessment($assessment->id);
                    $m[] = WORKSHOP_ALLOCATION_MANUAL_MSG_DELETED;
                    $m[] = $assessment->authorid;
                    $m[] = $assessment->reviewerid;
                }
                $m = implode('-', $m);  // serialize message object to be passed via URL
                redirect($PAGE->url->out(false, array('m' => $m), false));
            }
            break;
        }

        // if we stay on this page, set the environment
        $PAGE->requires->css('mod/workshop/allocation/manual/ui.css');
    }


    /**
     * Prints user interface - current allocation and a form to edit it
     */
    public function ui() {
        global $PAGE;

        $o              = '';   // output buffer
        $hlauthorid     = -1;   // highlight this author
        $hlreviewerid   = -1;   // highlight this reviewer
        $msg            = '';   // msg text
        $sty            = '';   // msg style
        $m = optional_param('m', '', PARAM_ALPHANUMEXT);   // message object

        if ($m) {
            $m = explode('-', $m);  // unserialize
            switch ($m[0]) {
            case WORKSHOP_ALLOCATION_MANUAL_MSG_ADDED:
                $hlauthorid     = $m[1];
                $hlreviewerid   = $m[2];
                $msg            = get_string('allocationadded', 'workshop');
                $sty            = 'ok';
                break;
            case WORKSHOP_ALLOCATION_MANUAL_MSG_EXISTS:
                $hlauthorid     = $m[1];
                $hlreviewerid   = $m[2];
                $msg            = get_string('allocationexists', 'workshop');
                $sty            = 'info';
                break;
            case WORKSHOP_ALLOCATION_MANUAL_MSG_NOSUBMISSION:
                $hlauthorid     = $m[1];
                $msg            = get_string('nosubmissionfound', 'workshop');
                $sty            = 'error';
                break;
            case WORKSHOP_ALLOCATION_MANUAL_MSG_WOSUBMISSION:
                $hlauthorid     = $m[1];
                $hlreviewerid   = $m[2];
                $msg            = get_string('cantassesswosubmission', 'workshop');
                $sty            = 'error';
                break;
            case WORKSHOP_ALLOCATION_MANUAL_MSG_CONFIRM_DEL:
                $hlauthorid     = $m[2];
                $hlreviewerid   = $m[3];
                if ($m[4] == 0) {
                    $msg            = get_string('areyousuretodeallocate', 'workshop');
                    $sty            = 'info';
                } else {
                    $msg            = get_string('areyousuretodeallocategraded', 'workshop');
                    $sty            = 'error';
                }
                break;
            case WORKSHOP_ALLOCATION_MANUAL_MSG_DELETED:
                $hlauthorid     = $m[1];
                $hlreviewerid   = $m[2];
                $msg            = get_string('assessmentdeleted', 'workshop');
                $sty            = 'ok';
                break;
            }
            $o .= '<div id="message" class="' . $sty . '">';
            $o .= '  <span>' . $msg . '</span>';
            $o .= '  <div id="message-close"><a href="' . $PAGE->url->out() . '">' . 
                                                get_string('messageclose', 'workshop') . '</a></div>';
            if ($m[0] == WORKSHOP_ALLOCATION_MANUAL_MSG_CONFIRM_DEL) {
                $handler = $PAGE->url->out_action();
                $o .= print_single_button($handler, array('mode' => 'del', 'what' => $m[1], 'confirm' => 1),
                                get_string('iamsure', 'workshop'), 'post', '', true);
            }
            $o .= '</div>';
        }

        $peer = array(); // singular chosen due to readibility
        $rs = $this->workshop->get_allocations();
        foreach ($rs as $allocation) {
            $currentuserid = $allocation->authorid;
            if (!isset($peer[$currentuserid])) {
                $peer[$currentuserid]                   = new stdClass();
                $peer[$currentuserid]->id               = $allocation->authorid;
                $peer[$currentuserid]->firstname        = $allocation->authorfirstname;
                $peer[$currentuserid]->lastname         = $allocation->authorlastname;
                $peer[$currentuserid]->picture          = $allocation->authorpicture;
                $peer[$currentuserid]->imagealt         = $allocation->authorimagealt;
                $peer[$currentuserid]->avatar           = print_user_picture($peer[$currentuserid],
                                                                            $this->workshop->course, null, 16, true);
                $peer[$currentuserid]->submissionid     = $allocation->submissionid;
                $peer[$currentuserid]->submissiontitle  = $allocation->submissiontitle;
                $peer[$currentuserid]->submissiongrade  = $allocation->submissiongrade;
                $peer[$currentuserid]->reviewedby       = array(); // users who are reviewing this user's submission
                $peer[$currentuserid]->reviewerof       = array(); // users whom submission is being reviewed by this user
            }
            if (!empty($allocation->reviewerid)) {
                // example: "submission of user with id 45 is reviewed by user with id 87 in the assessment record 12"
                $peer[$currentuserid]->reviewedby[$allocation->reviewerid] = $allocation->assessmentid;
            }
        }
        $rs->close();

        foreach ($peer as $author) {
            foreach ($author->reviewedby as $reviewerid => $assessmentid) {
                // example: "user with id 87 is reviewer of the work submitted by user id 45 in the assessment record 12"
                if (isset($peer[$reviewerid])) {
                    $peer[$reviewerid]->reviewerof[$author->id] = $assessmentid;
                }
            }
        }

        if (empty($peer)) {
            $o .= '<div id="message" class="info">' . get_string('nosubmissions', 'workshop') . '</div>';
        } else {
            $o .= '<table class="allocations">' . "\n";
            $o .= '<thead><tr>';
            $o .= '<th>' . get_string('participantreviewedby', 'workshop') . '</th>';
            $o .= '<th>' . get_string('participant', 'workshop') . '</th>';
            $o .= '<th>' . get_string('participantrevierof', 'workshop') . '</th>';
            $o .= '</thead><tbody>';
            $counter = 0;
            foreach ($peer as $user) {
                $o .= '<tr class="r' . $counter % 2 . '">' . "\n";

                if ($user->id == $hlauthorid) {
                    $highlight=' highlight';
                } else {
                    $highlight='';
                }
                $o .= '<td class="reviewedby' . $highlight . '">' . "\n";
                if (is_null($user->submissionid)) {
                    $o .= '<span class="info">' . "\n";
                    $o .= get_string('nothingtoreview', 'workshop');
                    $o .= '</span>' . "\n";
                } else {
                    $handler = $PAGE->url->out_action() . '&amp;mode=new&amp;of=' . $user->id . '&amp;by=';
                    $o .= popup_form($handler, $this->available_reviewers($user->id), 'addreviewof' . $user->id, '',
                             get_string('chooseuser', 'workshop'), '', '', true, 'self', get_string('addreviewer', 'workshop'));
                }
                $o .= '<ul>' . "\n";
                foreach ($user->reviewedby as $reviewerid => $assessmentid) {
                    $o .= '<li>';
                    $o .= print_user_picture($peer[$reviewerid], $this->workshop->course, null, 16, true);
                    $o .= fullname($peer[$reviewerid]);

                    // delete
                    $handler = $PAGE->url->out_action(array('mode' => 'del', 'what' => $assessmentid));
                    $o .= '<a class="action" href="' . $handler . '"> X </a>'; // todo icon and link title

                    $o .= '</li>';
                }
                $o .= '</ul>' . "\n";

                $o .= '</td>' . "\n";
                $o .= '<td class="peer">' . "\n";
                $o .= print_user_picture($user, $this->workshop->course, null, 35, true);
                $o .= fullname($user);
                $o .= '<div class="submission">' . "\n";
                if (is_null($user->submissionid)) {
                    $o .= '<span class="info">' . get_string('nosubmissionfound', 'workshop');
                } else {
                    $o .= '<div class="title"><a href="#">' . s($user->submissiontitle) . '</a></div>';
                    if (is_null($user->submissiongrade)) {
                        $o .= '<div class="grade missing">' . get_string('nogradeyet', 'workshop') . '</div>';
                    } else {
                        $o .= '<div class="grade">' . s($user->submissiongrade) . '</div>'; // todo calculate
                    }
                }
                $o .= '</div>' . "\n";
                $o .= '</td>' . "\n";

                if ($user->id == $hlreviewerid) {
                    $highlight=' highlight';
                } else {
                    $highlight='';
                }
                $o .= '<td class="reviewerof' . $highlight . '">' . "\n";
                if (!($this->workshop->assesswosubmission) && is_null($user->submissionid)) {
                    $o .= '<span class="info">' . "\n";
                    $o .= get_string('cantassesswosubmission', 'workshop');
                    $o .= '</span>' . "\n";
                } else {
                    $handler = $PAGE->url->out_action() . '&mode=new&amp;by=' . $user->id . '&amp;of=';
                    $o .= popup_form($handler, $this->available_reviewees($user->id), 'addreviewby' . $user->id, '',
                             get_string('chooseuser', 'workshop'), '', '', true, 'self', get_string('addreviewee', 'workshop'));
                    $o .= '<ul>' . "\n";
                    foreach ($user->reviewerof as $authorid => $assessmentid) {
                        $o .= '<li>';
                        $o .= print_user_picture($peer[$authorid], $this->workshop->course, null, 16, true);
                        $o .= fullname($peer[$authorid]);

                        // delete
                        $handler = $PAGE->url->out_action(array('mode' => 'del', 'what' => $assessmentid));
                        $o .= '<a class="action" href="' . $handler . '"> X </a>'; // todo icon and link title

                        $o .= '</li>';
                    }   
                    $o .= '</ul>' . "\n";
                }
                $o .= '</td>' . "\n";
                $o .= '</tr>' . "\n";
                $counter++;
            }
            $o .= '</tbody></table>' . "\n";
        }
        return $o;
    }


    /**
     * Return a list of reviewers that can review a submission
     *
     * @param int $authorid User ID of the submission author
     * @return array Select options
     */
    protected function available_reviewers($authorid) {

        $users = $this->workshop->get_peer_reviewers();
        $options = array();
        foreach ($users as $user) {
            $options[$user->id] = fullname($user);
        }
        if (0 == $this->workshop->useselfassessment) {
            // students can not review their own submissions in this workshop
            if (isset($options[$authorid])) {
                unset($options[$authorid]);
            }
        }

        return $options;
    }


    /**
     * Return a list of reviewees whom work can be reviewed by a given user
     *
     * @param int $reviewerid User ID of the reviewer
     * @return array Select options
     */
    protected function available_reviewees($reviewerid) {

        $rs = $this->workshop->get_submissions();
        $options = array();
        foreach ($rs as $submission) {
            $options[$submission->userid] = fullname((object)array('firstname' => $submission->authorfirstname, 
                                                                   'lastname' =>  $submission->authorlastname));
        }
        $rs->close();
        if (0 == $this->workshop->useselfassessment) {
            // students can not be reviewed by themselves in this workshop
            if (isset($options[$reviewerid])) {
                unset($options[$reviewerid]);
            }
        }

        return $options;
    }

}

