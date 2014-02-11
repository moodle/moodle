<?php
// This gets included into lib.php

//TODO: Reduce code repetition between this and workshop_manual_allocator

class workshop_teammode_manual_allocator extends workshop_manual_allocator {
    
    public function init() {
        global $PAGE, $SESSION;

        $mode = optional_param('mode', 'display', PARAM_ALPHA);
        
        $result = new workshop_allocation_result($this);

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
                    $m[] = $submission->authorid;
                    $m[] = $reviewerid;
                } else {
                    $m[] = self::MSG_ADDED;
                    $m[] = $submission->authorid;
                    $m[] = $reviewerid;
                }
            }
            $m = implode('-', $m);  // serialize message object to be passed via URL
            redirect($PAGE->url->out(false, array('m' => $m)));
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
                redirect($PAGE->url->out(false, array('m' => $m)));
            }
            break;
        }

        

        if(!empty($SESSION->workshop_upload_messages)) {
            $messages = $SESSION->workshop_upload_messages;
            unset($SESSION->workshop_upload_messages);
            $failed = false;
            foreach($messages as $m) {
                list($level, $message) = explode("::",$m);
                if ($level == "error") $failed = true;
                $result->log($message, $level);
            }
            if ($failed) {
                $result->set_status(workshop_allocation_result::STATUS_FAILED);
            } else {
                $result->set_status(workshop_allocation_result::STATUS_EXECUTED);
            }
        } else {
            $result->set_status(workshop_allocation_result::STATUS_VOID);
        }
        
        return $result;
    }

    /**
     * Prints user interface - current allocation and a form to edit it
     */
    public function ui() {
        global $PAGE, $DB;

        $output     = $PAGE->get_renderer('workshopallocation_manual');

        $pagingvar  = 'page';
        $page       = optional_param($pagingvar, 0, PARAM_INT);
        $perpage    = 10;   // todo let the user modify this

        $hlauthorid     = -1;           // highlight this author
        $hlreviewerid   = -1;           // highlight this reviewer

        $message        = new workshop_message();

        $m  = optional_param('m', '', PARAM_ALPHANUMEXT);   // message code
        if ($m) {
            $m = explode('-', $m);
            switch ($m[0]) {
            case self::MSG_ADDED:
                $hlauthorid     = $m[1];
                $hlreviewerid   = $m[2];
                $message        = new workshop_message(get_string('allocationadded', 'workshopallocation_manual'),
                    workshop_message::TYPE_OK);
                break;
            case self::MSG_EXISTS:
                $hlauthorid     = $m[1];
                $hlreviewerid   = $m[2];
                $message        = new workshop_message(get_string('allocationexists', 'workshopallocation_manual'),
                    workshop_message::TYPE_INFO);
                break;
            case self::MSG_NOSUBMISSION:
                $hlauthorid     = $m[1];
                $message        = new workshop_message(get_string('nosubmissionfound', 'workshop'),
                    workshop_message::TYPE_ERROR);
                break;
            case self::MSG_CONFIRM_DEL:
                $hlauthorid     = $m[2];
                $hlreviewerid   = $m[3];
                if ($m[4] == 0) {
                    $message    = new workshop_message(get_string('areyousuretodeallocate', 'workshopallocation_manual'),
                        workshop_message::TYPE_INFO);
                } else {
                    $message    = new workshop_message(get_string('areyousuretodeallocategraded', 'workshopallocation_manual'),
                        workshop_message::TYPE_ERROR);
                }
                $url = new moodle_url($PAGE->url, array('mode' => 'del', 'what' => $m[1], 'confirm' => 1, 'sesskey' => sesskey()));
                $label = get_string('iamsure', 'workshop');
                $message->set_action($url, $label);
                break;
            case self::MSG_DELETED:
                $hlauthorid     = $m[1];
                $hlreviewerid   = $m[2];
                $message        = new workshop_message(get_string('assessmentdeleted', 'workshop'),
                    workshop_message::TYPE_OK);
                break;
            case self::MSG_DELETE_ERROR:
                $hlauthorid     = $m[1];
                $hlreviewerid   = $m[2];
                $message        = new workshop_message(get_string('assessmentnotdeleted', 'workshop'),
                    workshop_message::TYPE_ERROR);
                break;
            }
        }

        // fetch the list of ids of all workshop participants - this may get really long so fetch just id
        $participants = get_users_by_capability($PAGE->context, array('mod/workshop:submit', 'mod/workshop:peerassess'),
                                            'u.id', 'u.lastname,u.firstname,u.id', '', '', '', '', false, false, true);

		// TEAMMODE :: Morgan Harris
		// this introduces a new variable, $gradeitems, that replaces $participants in some cases
		// basically in team mode you get a list of *groups* not people
		
        list($insql, $params) = $DB->get_in_or_equal(array_keys($participants));
        
        $groupingsql = '';
        $params2 = array();
        if($this->workshop->cm->groupingid) {
            $groupinggroups = groups_get_all_groups($this->workshop->cm->course, 0, $this->workshop->cm->groupingid, 'g.id');
            list($groupingsql, $params2) = $DB->get_in_or_equal(array_keys($groupinggroups));
            $groupingsql = " AND g.id $groupingsql";
        }
        
		$sql = <<<SQL
SELECT g.id, g.name
FROM {groups} g
JOIN {groups_members} m ON m.groupid = g.id
WHERE g.courseid = {$this->workshop->cm->course} AND m.userid $insql $groupingsql
GROUP BY g.id, g.name
ORDER BY g.name
SQL;

		$rslt = $DB->get_records_sql($sql, array_merge($params, $params2));
			
		$gradeitems = $rslt;

        $numofparticipants = count($gradeitems);  // we will need later for the pagination

        if ($hlauthorid > 0 and $hlreviewerid > 0) {
            // display just those two users
            // todo: figure out a sensible way to GROUPMOD this
            $participants = array_intersect_key($participants, array($hlauthorid => null, $hlreviewerid => null));
            $button = $output->single_button($PAGE->url, get_string('showallparticipants', 'workshopallocation_manual'), 'get');
        } else {
            // slice the list of participants according to the current page
            $gradeitems = array_slice($gradeitems, $page * $perpage, $perpage, true);
            $button = '';
        }

        // this will hold the information needed to display user names and pictures
        $userinfo = $DB->get_records_list('user', 'id', array_keys($participants), '', user_picture::fields());

        // load the participants' submissions
	    $submissions = $this->workshop->get_submissions_grouped();
        
        foreach ($submissions as $submission) {
            if (!isset($userinfo[$submission->authorid])) {
                $userinfo[$submission->authorid]            = new stdclass();
                $userinfo[$submission->authorid]->id        = $submission->authorid;
                $userinfo[$submission->authorid]->firstname = $submission->authorfirstname;
                $userinfo[$submission->authorid]->lastname  = $submission->authorlastname;
                $userinfo[$submission->authorid]->picture   = $submission->authorpicture;
                $userinfo[$submission->authorid]->imagealt  = $submission->authorimagealt;
                $userinfo[$submission->authorid]->email     = $submission->authoremail;
            }
        }

        // get current reviewers
        $reviewers = array();
        if ($submissions) {
			$keys = array_keys( $submissions );
            list($submissionids, $params) = $DB->get_in_or_equal($keys, SQL_PARAMS_NAMED);
            $sql = "SELECT a.id AS assessmentid, a.submissionid,
                           r.id AS reviewerid, r.lastname, r.firstname, r.picture, r.imagealt, r.email,
                           s.id AS submissionid, s.authorid
                      FROM {workshop_assessments} a
                      JOIN {user} r ON (a.reviewerid = r.id)
                      JOIN {workshop_submissions} s ON (a.submissionid = s.id)
                     WHERE a.submissionid $submissionids";
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

        // the information about the allocations
        $allocations = array();

        foreach ($gradeitems as $participant) {
            $allocations[$participant->id] = new stdClass();
        	$allocations[$participant->id]->groupid = $participant->id;
        	$allocations[$participant->id]->group = $participant;
            $allocations[$participant->id]->submissionid = null;
            $allocations[$participant->id]->reviewedby = array();
            $allocations[$participant->id]->reviewerof = array();
            
        }
        unset($participants);

		//as we're iterating over this list, we also need to check if all the names are unique for our upload script
		$allgroupnames = array();

        foreach ($submissions as $submission) {
	        $id = $submission->group->id;
            $allocations[$id]->submissionid = $submission->id;
            $allocations[$id]->submissiontitle = $submission->title;
            $allocations[$id]->submissiongrade = $submission->grade;
            $allocations[$id]->userid = $submission->authorid;
			$allgroupnames[$id] = $submission->group->name;
        }
		
		$duplicategroupnames = array_unique(array_diff_assoc($allgroupnames,array_unique($allgroupnames)));
        
        foreach($reviewers as $reviewer) {
			$id = $submissions[$reviewer->submissionid];
            $allocations[$id->group->id]->reviewedby[$reviewer->reviewerid] = $reviewer->assessmentid;
        }
        unset($reviewers);

		unset($submissions);
        
        foreach($userinfo as $k => $u) {
	        $userinfo[$k]->groups = groups_get_all_groups($this->workshop->cm->course, $u->id, $this->workshop->cm->groupingid, 'g.id');
        }

        // prepare data to be rendered
        $data                   = new workshopallocation_teammode_manual_allocations();
        $data->allocations      = $allocations;
        $data->gradeitems		= $gradeitems;
        $data->userinfo         = $userinfo;
		$data->groupduplicates  = $duplicategroupnames;
        $data->authors          = $this->workshop->get_potential_authors();
        $data->reviewers        = $this->workshop->get_potential_reviewers();
        $data->hlauthorid       = $hlauthorid;
        $data->hlreviewerid     = $hlreviewerid;
        $data->selfassessment   = $this->workshop->useselfassessment;
        $data->gradeitems		= $gradeitems;

        // prepare paging bar
        $pagingbar              = new paging_bar($numofparticipants, $page, $perpage, $PAGE->url, $pagingvar);
        $pagingbarout           = $output->render($pagingbar);

        return $pagingbarout . $output->render($message) . $output->render($data) . $button . $pagingbarout;
    }

    /**
     * Delete all data related to a given workshop module instance
     *
     * This plugin does not store any data.
     *
     * @see workshop_delete_instance()
     * @param int $workshopid id of the workshop module instance being deleted
     * @return void
     */
    public static function delete_instance($workshopid) {
        return;
    }
    
    public static function teammode_class() {
        return null;
    }
    
}

/**
 * Contains all information needed to render current allocations and the allocator UI
 *
 * @see workshop_manual_allocator::ui()
 */
class workshopallocation_teammode_manual_allocations implements renderable {
    public $allocations;
    public $gradeitems;
    public $userinfo;
	public $groupduplicates;
    public $authors;
    public $reviewers;
    public $hlauthorid;
    public $hlreviewerid;
    public $selfassessment;
}