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
 * Allocates the submissions randomly
 *
 * @package    workshopallocation_random
 * @subpackage mod_workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;    // access to global variables during unit test

require_once(__DIR__ . '/../lib.php');            // interface definition
require_once(__DIR__ . '/../../locallib.php');    // workshop internal API
require_once(__DIR__ . '/settings_form.php');     // settings form

/**
 * Allocates the submissions randomly
 */
class workshop_random_allocator implements workshop_allocator {

    /** constants used to pass status messages between init() and ui() */
    const MSG_SUCCESS       = 1;

    /** workshop instance */
    protected $workshop;

    /** mform with settings */
    protected $mform;

    /**
     * @param workshop $workshop Workshop API object
     */
    public function __construct(workshop $workshop) {
        $this->workshop = $workshop;
    }

    /**
     * Allocate submissions as requested by user
     *
     * @return workshop_allocation_result
     */
    public function init() {
        global $PAGE;

        $result = new workshop_allocation_result($this);
        $customdata = array();
        $customdata['workshop'] = $this->workshop;
        $this->mform = new workshop_random_allocator_form($PAGE->url, $customdata);
        if ($this->mform->is_cancelled()) {
            redirect($this->workshop->view_url());
        } else if ($settings = $this->mform->get_data()) {
            $settings = workshop_random_allocator_setting::instance_from_object($settings);
            $this->execute($settings, $result);
            return $result;
        } else {
            // this branch is executed if the form is submitted but the data
            // doesn't validate and the form should be redisplayed
            // or on the first display of the form.
            $result->set_status(workshop_allocation_result::STATUS_VOID);
            return $result;
        }
    }

    /**
     * Executes the allocation based on the given settings
     *
     * @param workshop_random_allocator_setting $setting
     * @param workshop_allocation_result allocation result logger
     */
    public function execute(workshop_random_allocator_setting $settings, workshop_allocation_result $result) {

        $authors        = $this->workshop->get_potential_authors();
        $authors        = $this->workshop->get_grouped($authors);
        $reviewers      = $this->workshop->get_potential_reviewers(!$settings->assesswosubmission);
        $reviewers      = $this->workshop->get_grouped($reviewers);
        $assessments    = $this->workshop->get_all_assessments();
        $newallocations = array();      // array of array(reviewer => reviewee)

        if ($settings->numofreviews) {
            if ($settings->removecurrent) {
                // behave as if there were no current assessments
                $curassessments = array();
            } else {
                $curassessments = $assessments;
            }
            $options                     = array();
            $options['numofreviews']     = $settings->numofreviews;
            $options['numper']           = $settings->numper;
            $options['excludesamegroup'] = $settings->excludesamegroup;
            $randomallocations  = $this->random_allocation($authors, $reviewers, $curassessments, $result, $options);
            $newallocations     = array_merge($newallocations, $randomallocations);
            $result->log(get_string('numofrandomlyallocatedsubmissions', 'workshopallocation_random', count($randomallocations)));
            unset($randomallocations);
        }
        if ($settings->addselfassessment) {
            $selfallocations    = $this->self_allocation($authors, $reviewers, $assessments);
            $newallocations     = array_merge($newallocations, $selfallocations);
            $result->log(get_string('numofselfallocatedsubmissions', 'workshopallocation_random', count($selfallocations)));
            unset($selfallocations);
        }
        if (empty($newallocations)) {
            $result->log(get_string('noallocationtoadd', 'workshopallocation_random'), 'info');
        } else {
            $newnonexistingallocations = $newallocations;
            $this->filter_current_assessments($newnonexistingallocations, $assessments);
            $this->add_new_allocations($newnonexistingallocations, $authors, $reviewers);
            $allreviewers = $reviewers[0];
            $allreviewersreloaded = false;
            foreach ($newallocations as $newallocation) {
                list($reviewerid, $authorid) = each($newallocation);
                $a = new stdClass();
                if (isset($allreviewers[$reviewerid])) {
                    $a->reviewername = fullname($allreviewers[$reviewerid]);
                } else {
                    // this may happen if $settings->assesswosubmission is false but the reviewer
                    // of the re-used assessment has not submitted anything. let us reload
                    // the list of reviewers name including those without their submission
                    if (!$allreviewersreloaded) {
                        $allreviewers = $this->workshop->get_potential_reviewers(false);
                        $allreviewersreloaded = true;
                    }
                    if (isset($allreviewers[$reviewerid])) {
                        $a->reviewername = fullname($allreviewers[$reviewerid]);
                    } else {
                        // this should not happen usually unless the list of participants was changed
                        // in between two cycles of allocations
                        $a->reviewername = '#'.$reviewerid;
                    }
                }
                if (isset($authors[0][$authorid])) {
                    $a->authorname = fullname($authors[0][$authorid]);
                } else {
                    $a->authorname = '#'.$authorid;
                }
                if (in_array($newallocation, $newnonexistingallocations)) {
                    $result->log(get_string('allocationaddeddetail', 'workshopallocation_random', $a), 'ok', 1);
                } else {
                    $result->log(get_string('allocationreuseddetail', 'workshopallocation_random', $a), 'ok', 1);
                }
            }
        }
        if ($settings->removecurrent) {
            $delassessments = $this->get_unkept_assessments($assessments, $newallocations, $settings->addselfassessment);
            // random allocator should not be able to delete assessments that have already been graded
            // by reviewer
            $result->log(get_string('numofdeallocatedassessment', 'workshopallocation_random', count($delassessments)), 'info');
            foreach ($delassessments as $delassessmentkey => $delassessmentid) {
                $a = new stdclass();
                $a->authorname      = fullname((object)array(
                        'lastname'  => $assessments[$delassessmentid]->authorlastname,
                        'firstname' => $assessments[$delassessmentid]->authorfirstname));
                $a->reviewername    = fullname((object)array(
                        'lastname'  => $assessments[$delassessmentid]->reviewerlastname,
                        'firstname' => $assessments[$delassessmentid]->reviewerfirstname));
                if (!is_null($assessments[$delassessmentid]->grade)) {
                    $result->log(get_string('allocationdeallocategraded', 'workshopallocation_random', $a), 'error', 1);
                    unset($delassessments[$delassessmentkey]);
                } else {
                    $result->log(get_string('assessmentdeleteddetail', 'workshopallocation_random', $a), 'info', 1);
                }
            }
            $this->workshop->delete_assessment($delassessments);
        }
        $result->set_status(workshop_allocation_result::STATUS_EXECUTED);
    }

    /**
     * Returns the HTML code to print the user interface
     */
    public function ui() {
        global $PAGE;

        $output = $PAGE->get_renderer('mod_workshop');

        $m = optional_param('m', null, PARAM_INT);  // status message code
        $message = new workshop_message();
        if ($m == self::MSG_SUCCESS) {
            $message->set_text(get_string('randomallocationdone', 'workshopallocation_random'));
            $message->set_type(workshop_message::TYPE_OK);
        }

        $out  = $output->container_start('random-allocator');
        $out .= $output->render($message);
        // the nasty hack follows to bypass the sad fact that moodle quickforms do not allow to actually
        // return the HTML content, just to display it
        ob_start();
        $this->mform->display();
        $out .= ob_get_contents();
        ob_end_clean();

        // if there are some not-grouped participant in a group mode, warn the user
        $gmode = groups_get_activity_groupmode($this->workshop->cm, $this->workshop->course);
        if (VISIBLEGROUPS == $gmode or SEPARATEGROUPS == $gmode) {
            $users = $this->workshop->get_potential_authors() + $this->workshop->get_potential_reviewers();
            $users = $this->workshop->get_grouped($users);
            if (isset($users[0])) {
                $nogroupusers = $users[0];
                foreach ($users as $groupid => $groupusers) {
                    if ($groupid == 0) {
                        continue;
                    }
                    foreach ($groupusers as $groupuserid => $groupuser) {
                        unset($nogroupusers[$groupuserid]);
                    }
                }
                if (!empty($nogroupusers)) {
                    $list = array();
                    foreach ($nogroupusers as $nogroupuser) {
                        $list[] = fullname($nogroupuser);
                    }
                    $a = implode(', ', $list);
                    $out .= $output->box(get_string('nogroupusers', 'workshopallocation_random', $a), 'generalbox warning nogroupusers');
                }
            }
        }

        // TODO $out .= $output->heading(get_string('stats', 'workshopallocation_random'));

        $out .= $output->container_end();

        return $out;
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

    /**
     * Return an array of possible numbers of reviews to be done
     *
     * Should contain numbers 1, 2, 3, ... 10 and possibly others up to a reasonable value
     *
     * @return array of integers
     */
    public static function available_numofreviews_list() {
        $options = array();
        $options[30] = 30;
        $options[20] = 20;
        $options[15] = 15;
        for ($i = 10; $i >= 0; $i--) {
            $options[$i] = $i;
        }
        return $options;
    }

    /**
     * Allocates submissions to their authors for review
     *
     * If the submission has already been allocated, it is skipped. If the author is not found among
     * reviewers, the submission is not assigned.
     *
     * @param array $authors grouped of {@see workshop::get_potential_authors()}
     * @param array $reviewers grouped by {@see workshop::get_potential_reviewers()}
     * @param array $assessments as returned by {@see workshop::get_all_assessments()}
     * @return array of new allocations to be created, array of array(reviewerid => authorid)
     */
    protected function self_allocation($authors=array(), $reviewers=array(), $assessments=array()) {
        if (!isset($authors[0]) || !isset($reviewers[0])) {
            // no authors or no reviewers
            return array();
        }
        $alreadyallocated = array();
        foreach ($assessments as $assessment) {
            if ($assessment->authorid == $assessment->reviewerid) {
                $alreadyallocated[$assessment->authorid] = 1;
            }
        }
        $add = array(); // list of new allocations to be created
        foreach ($authors[0] as $authorid => $author) {
            // for all authors in all groups
            if (isset($reviewers[0][$authorid])) {
                // if the author can be reviewer
                if (!isset($alreadyallocated[$authorid])) {
                    // and the allocation does not exist yet, then
                    $add[] = array($authorid => $authorid);
                }
            }
        }
        return $add;
    }

    /**
     * Creates new assessment records
     *
     * @param array $newallocations pairs 'reviewerid' => 'authorid'
     * @param array $dataauthors    authors by group, group [0] contains all authors
     * @param array $datareviewers  reviewers by group, group [0] contains all reviewers
     * @return bool
     */
    protected function add_new_allocations(array $newallocations, array $dataauthors, array $datareviewers) {
        global $DB;

        $newallocations = $this->get_unique_allocations($newallocations);
        $authorids      = $this->get_author_ids($newallocations);
        $submissions    = $this->workshop->get_submissions($authorids);
        $submissions    = $this->index_submissions_by_authors($submissions);
        foreach ($newallocations as $newallocation) {
            list($reviewerid, $authorid) = each($newallocation);
            if (!isset($submissions[$authorid])) {
                throw new moodle_exception('unabletoallocateauthorwithoutsubmission', 'workshop');
            }
            $submission = $submissions[$authorid];
            $status = $this->workshop->add_allocation($submission, $reviewerid, 1, true);   // todo configurable weight?
            if (workshop::ALLOCATION_EXISTS == $status) {
                debugging('newallocations array contains existing allocation, this should not happen');
            }
        }
    }

    /**
     * Flips the structure of submission so it is indexed by authorid attribute
     *
     * It is the caller's responsibility to make sure the submissions are not teacher
     * examples so no user is the author of more submissions.
     *
     * @param string $submissions array indexed by submission id
     * @return array indexed by author id
     */
    protected function index_submissions_by_authors($submissions) {
        $byauthor = array();
        if (is_array($submissions)) {
            foreach ($submissions as $submissionid => $submission) {
                if (isset($byauthor[$submission->authorid])) {
                    throw new moodle_exception('moresubmissionsbyauthor', 'workshop');
                }
                $byauthor[$submission->authorid] = $submission;
            }
        }
        return $byauthor;
    }

    /**
     * Extracts unique list of authors' IDs from the structure of new allocations
     *
     * @param array $newallocations of pairs 'reviewerid' => 'authorid'
     * @return array of authorids
     */
    protected function get_author_ids($newallocations) {
        $authors = array();
        foreach ($newallocations as $newallocation) {
            $authorid = reset($newallocation);
            if (!in_array($authorid, $authors)) {
                $authors[] = $authorid;
            }
        }
        return $authors;
    }

    /**
     * Removes duplicate allocations
     *
     * @param mixed $newallocations array of 'reviewerid' => 'authorid' pairs
     * @return array
     */
    protected function get_unique_allocations($newallocations) {
        return array_merge(array_map('unserialize', array_unique(array_map('serialize', $newallocations))));
    }

    /**
     * Returns the list of assessments to remove
     *
     * If user selects "removecurrentallocations", we should remove all current assessment records
     * and insert new ones. But this would needlessly waste table ids. Instead, let us find only those
     * assessments that have not been re-allocated in this run of allocation. So, the once-allocated
     * submissions are kept with their original id.
     *
     * @param array $assessments         list of current assessments
     * @param mixed $newallocations      array of 'reviewerid' => 'authorid' pairs
     * @param bool  $keepselfassessments do not remove already allocated self assessments
     * @return array of assessments ids to be removed
     */
    protected function get_unkept_assessments($assessments, $newallocations, $keepselfassessments) {
        $keepids = array(); // keep these assessments
        foreach ($assessments as $assessmentid => $assessment) {
            $aaid = $assessment->authorid;
            $arid = $assessment->reviewerid;
            if (($keepselfassessments) && ($aaid == $arid)) {
                $keepids[$assessmentid] = null;
                continue;
            }
            foreach ($newallocations as $newallocation) {
                list($nrid, $naid) = each($newallocation);
                if (array($arid, $aaid) == array($nrid, $naid)) {
                    // re-allocation found - let us continue with the next assessment
                    $keepids[$assessmentid] = null;
                    continue 2;
                }
            }
        }
        return array_keys(array_diff_key($assessments, $keepids));
    }

    /**
     * Allocates submission reviews randomly
     *
     * The algorithm of this function has been described at http://moodle.org/mod/forum/discuss.php?d=128473
     * Please see the PDF attached to the post before you study the implementation. The goal of the function
     * is to connect each "circle" (circles are representing either authors or reviewers) with a required
     * number of "squares" (the other type than circles are).
     *
     * The passed $options array must provide keys:
     *      (int)numofreviews - number of reviews to be allocated to each circle
     *      (int)numper - what user type the circles represent.
     *      (bool)excludesamegroup - whether to prevent peer submissions from the same group in visible group mode
     *
     * @param array    $authors      structure of grouped authors
     * @param array    $reviewers    structure of grouped reviewers
     * @param array    $assessments  currently assigned assessments to be kept
     * @param workshop_allocation_result $result allocation result logger
     * @param array    $options      allocation options
     * @return array                 array of (reviewerid => authorid) pairs
     */
    protected function random_allocation($authors, $reviewers, $assessments, $result, array $options) {
        if (empty($authors) || empty($reviewers)) {
            // nothing to be done
            return array();
        }

        $numofreviews = $options['numofreviews'];
        $numper       = $options['numper'];

        if (workshop_random_allocator_setting::NUMPER_SUBMISSION == $numper) {
            // circles are authors, squares are reviewers
            $result->log(get_string('resultnumperauthor', 'workshopallocation_random', $numofreviews), 'info');
            $allcircles = $authors;
            $allsquares = $reviewers;
            // get current workload
            list($circlelinks, $squarelinks) = $this->convert_assessments_to_links($assessments);
        } elseif (workshop_random_allocator_setting::NUMPER_REVIEWER == $numper) {
            // circles are reviewers, squares are authors
            $result->log(get_string('resultnumperreviewer', 'workshopallocation_random', $numofreviews), 'info');
            $allcircles = $reviewers;
            $allsquares = $authors;
            // get current workload
            list($squarelinks, $circlelinks) = $this->convert_assessments_to_links($assessments);
        } else {
            throw new moodle_exception('unknownusertypepassed', 'workshop');
        }
        // get the users that are not in any group. in visible groups mode, these users are exluded
        // from allocation by this method
        // $nogroupcircles is array (int)$userid => undefined
        if (isset($allcircles[0])) {
            $nogroupcircles = array_flip(array_keys($allcircles[0]));
        } else {
            $nogroupcircles = array();
        }
        foreach ($allcircles as $circlegroupid => $circles) {
            if ($circlegroupid == 0) {
                continue;
            }
            foreach ($circles as $circleid => $circle) {
                unset($nogroupcircles[$circleid]);
            }
        }
        // $result->log('circle links = ' . json_encode($circlelinks), 'debug');
        // $result->log('square links = ' . json_encode($squarelinks), 'debug');
        $squareworkload         = array();  // individual workload indexed by squareid
        $squaregroupsworkload   = array();    // group workload indexed by squaregroupid
        foreach ($allsquares as $squaregroupid => $squares) {
            $squaregroupsworkload[$squaregroupid] = 0;
            foreach ($squares as $squareid => $square) {
                if (!isset($squarelinks[$squareid])) {
                    $squarelinks[$squareid] = array();
                }
                $squareworkload[$squareid] = count($squarelinks[$squareid]);
                $squaregroupsworkload[$squaregroupid] += $squareworkload[$squareid];
            }
            $squaregroupsworkload[$squaregroupid] /= count($squares);
        }
        unset($squaregroupsworkload[0]);    // [0] is not real group, it contains all users
        // $result->log('square workload = ' . json_encode($squareworkload), 'debug');
        // $result->log('square group workload = ' . json_encode($squaregroupsworkload), 'debug');
        $gmode = groups_get_activity_groupmode($this->workshop->cm, $this->workshop->course);
        if (SEPARATEGROUPS == $gmode) {
            // shuffle all groups but [0] which means "all users"
            $circlegroups = array_keys(array_diff_key($allcircles, array(0 => null)));
            shuffle($circlegroups);
        } else {
            // all users will be processed at once
            $circlegroups = array(0);
        }
        // $result->log('circle groups = ' . json_encode($circlegroups), 'debug');
        foreach ($circlegroups as $circlegroupid) {
            $result->log('processing circle group id ' . $circlegroupid, 'debug');
            $circles = $allcircles[$circlegroupid];
            // iterate over all circles in the group until the requested number of links per circle exists
            // or it is not possible to fulfill that requirment
            // during the first iteration, we try to make sure that at least one circlelink exists. during the
            // second iteration, we try to allocate two, etc.
            for ($requiredreviews = 1; $requiredreviews <= $numofreviews; $requiredreviews++) {
                $this->shuffle_assoc($circles);
                $result->log('iteration ' . $requiredreviews, 'debug');
                foreach ($circles as $circleid => $circle) {
                    if (VISIBLEGROUPS == $gmode and isset($nogroupcircles[$circleid])) {
                        $result->log('skipping circle id ' . $circleid, 'debug');
                        continue;
                    }
                    $result->log('processing circle id ' . $circleid, 'debug');
                    if (!isset($circlelinks[$circleid])) {
                        $circlelinks[$circleid] = array();
                    }
                    $keeptrying     = true;     // is there a chance to find a square for this circle?
                    $failedgroups   = array();  // array of groupids where the square should be chosen from (because
                                                // of their group workload) but it was not possible (for example there
                                                // was the only square and it had been already connected
                    while ($keeptrying && (count($circlelinks[$circleid]) < $requiredreviews)) {
                        // firstly, choose a group to pick the square from
                        if (NOGROUPS == $gmode) {
                            if (in_array(0, $failedgroups)) {
                                $keeptrying = false;
                                $result->log(get_string('resultnomorepeers', 'workshopallocation_random'), 'error', 1);
                                break;
                            }
                            $targetgroup = 0;
                        } elseif (SEPARATEGROUPS == $gmode) {
                            if (in_array($circlegroupid, $failedgroups)) {
                                $keeptrying = false;
                                $result->log(get_string('resultnomorepeersingroup', 'workshopallocation_random'), 'error', 1);
                                break;
                            }
                            $targetgroup = $circlegroupid;
                        } elseif (VISIBLEGROUPS == $gmode) {
                            $trygroups = array_diff_key($squaregroupsworkload, array(0 => null));   // all but [0]
                            $trygroups = array_diff_key($trygroups, array_flip($failedgroups));     // without previous failures
                            if ($options['excludesamegroup']) {
                                // exclude groups the circle is member of
                                $excludegroups = array();
                                foreach (array_diff_key($allcircles, array(0 => null)) as $exgroupid => $exgroupmembers) {
                                    if (array_key_exists($circleid, $exgroupmembers)) {
                                        $excludegroups[$exgroupid] = null;
                                    }
                                }
                                $trygroups = array_diff_key($trygroups, $excludegroups);
                            }
                            $targetgroup = $this->get_element_with_lowest_workload($trygroups);
                        }
                        if ($targetgroup === false) {
                            $keeptrying = false;
                            $result->log(get_string('resultnotenoughpeers', 'workshopallocation_random'), 'error', 1);
                            break;
                        }
                        $result->log('next square should be from group id ' . $targetgroup, 'debug', 1);
                        // now, choose a square from the target group
                        $trysquares = array_intersect_key($squareworkload, $allsquares[$targetgroup]);
                        // $result->log('individual workloads in this group are ' . json_encode($trysquares), 'debug', 1);
                        unset($trysquares[$circleid]);  // can't allocate to self
                        $trysquares = array_diff_key($trysquares, array_flip($circlelinks[$circleid])); // can't re-allocate the same
                        $targetsquare = $this->get_element_with_lowest_workload($trysquares);
                        if (false === $targetsquare) {
                            $result->log('unable to find an available square. trying another group', 'debug', 1);
                            $failedgroups[] = $targetgroup;
                            continue;
                        }
                        $result->log('target square = ' . $targetsquare, 'debug', 1);
                        // ok - we have found the square
                        $circlelinks[$circleid][]       = $targetsquare;
                        $squarelinks[$targetsquare][]   = $circleid;
                        $squareworkload[$targetsquare]++;
                        $result->log('increasing square workload to ' . $squareworkload[$targetsquare], 'debug', 1);
                        if ($targetgroup) {
                            // recalculate the group workload
                            $squaregroupsworkload[$targetgroup] = 0;
                            foreach ($allsquares[$targetgroup] as $squareid => $square) {
                                $squaregroupsworkload[$targetgroup] += $squareworkload[$squareid];
                            }
                            $squaregroupsworkload[$targetgroup] /= count($allsquares[$targetgroup]);
                            $result->log('increasing group workload to ' . $squaregroupsworkload[$targetgroup], 'debug', 1);
                        }
                    } // end of processing this circle
                } // end of one iteration of processing circles in the group
            } // end of all iterations over circles in the group
        } // end of processing circle groups
        $returned = array();
        if (workshop_random_allocator_setting::NUMPER_SUBMISSION == $numper) {
            // circles are authors, squares are reviewers
            foreach ($circlelinks as $circleid => $squares) {
                foreach ($squares as $squareid) {
                    $returned[] = array($squareid => $circleid);
                }
            }
        }
        if (workshop_random_allocator_setting::NUMPER_REVIEWER == $numper) {
            // circles are reviewers, squares are authors
            foreach ($circlelinks as $circleid => $squares) {
                foreach ($squares as $squareid) {
                    $returned[] = array($circleid => $squareid);
                }
            }
        }
        return $returned;
    }

    /**
     * Extracts the information about reviews from the authors' and reviewers' perspectives
     *
     * @param array $assessments array of assessments as returned by {@link workshop::get_all_assessments()}
     * @return array of two arrays
     */
    protected function convert_assessments_to_links($assessments) {
        $authorlinks    = array(); // [authorid]    => array(reviewerid, reviewerid, ...)
        $reviewerlinks  = array(); // [reviewerid]  => array(authorid, authorid, ...)
        foreach ($assessments as $assessment) {
            if (!isset($authorlinks[$assessment->authorid])) {
                $authorlinks[$assessment->authorid] = array();
            }
            if (!isset($reviewerlinks[$assessment->reviewerid])) {
                $reviewerlinks[$assessment->reviewerid] = array();
            }
            $authorlinks[$assessment->authorid][]   = $assessment->reviewerid;
            $reviewerlinks[$assessment->reviewerid][] = $assessment->authorid;
            }
        return array($authorlinks, $reviewerlinks);
    }

    /**
     * Selects an element with the lowest workload
     *
     * If there are more elements with the same workload, choose one of them randomly. This may be
     * used to select a group or user.
     *
     * @param array $workload [groupid] => (int)workload
     * @return mixed int|bool id of the selected element or false if it is impossible to choose
     */
    protected function get_element_with_lowest_workload($workload) {
        $precision = 10;

        if (empty($workload)) {
            return false;
        }
        $minload = round(min($workload), $precision);
        $minkeys = array();
        foreach ($workload as $key => $val) {
            if (round($val, $precision) == $minload) {
                $minkeys[$key] = $val;
            }
        }
        return array_rand($minkeys);
    }

    /**
     * Shuffle the order of array elements preserving the key=>values
     *
     * @param array $array to be shuffled
     * @return true
     */
    protected function shuffle_assoc(&$array) {
        if (count($array) > 1) {
            // $keys needs to be an array, no need to shuffle 1 item or empty arrays, anyway
            $keys = array_keys($array);
            shuffle($keys);
            foreach($keys as $key) {
                $new[$key] = $array[$key];
            }
            $array = $new;
        }
        return true; // because this behaves like in-built shuffle(), which returns true
    }

    /**
     * Filter new allocations so that they do not contain an already existing assessment
     *
     * @param mixed $newallocations array of ('reviewerid' => 'authorid') tuples
     * @param array $assessments    array of assessment records
     * @return void
     */
    protected function filter_current_assessments(&$newallocations, $assessments) {
        foreach ($assessments as $assessment) {
            $allocation     = array($assessment->reviewerid => $assessment->authorid);
            $foundat        = array_keys($newallocations, $allocation);
            $newallocations = array_diff_key($newallocations, array_flip($foundat));
        }
    }
}


/**
 * Data object defining the settings structure for the random allocator
 */
class workshop_random_allocator_setting {

    /** aim to a number of reviews per one submission {@see self::$numper} */
    const NUMPER_SUBMISSION = 1;
    /** aim to a number of reviews per one reviewer {@see self::$numper} */
    const NUMPER_REVIEWER   = 2;

    /** @var int number of reviews */
    public $numofreviews;
    /** @var int either {@link self::NUMPER_SUBMISSION} or {@link self::NUMPER_REVIEWER} */
    public $numper;
    /** @var bool prevent reviews by peers from the same group */
    public $excludesamegroup;
    /** @var bool remove current allocations */
    public $removecurrent;
    /** @var bool participants can assess without having submitted anything */
    public $assesswosubmission;
    /** @var bool add self-assessments */
    public $addselfassessment;

    /**
     * Use the factory method {@link self::instance_from_object()}
     */
    protected function __construct() {
    }

    /**
     * Factory method making the instance from data in the passed object
     *
     * @param stdClass $data an object holding the values for our public properties
     * @return workshop_random_allocator_setting
     */
    public static function instance_from_object(stdClass $data) {
        $i = new self();

        if (!isset($data->numofreviews)) {
            throw new coding_exception('Missing value of the numofreviews property');
        } else {
            $i->numofreviews = (int)$data->numofreviews;
        }

        if (!isset($data->numper)) {
            throw new coding_exception('Missing value of the numper property');
        } else {
            $i->numper = (int)$data->numper;
            if ($i->numper !== self::NUMPER_SUBMISSION and $i->numper !== self::NUMPER_REVIEWER) {
                throw new coding_exception('Invalid value of the numper property');
            }
        }

        foreach (array('excludesamegroup', 'removecurrent', 'assesswosubmission', 'addselfassessment') as $k) {
            if (isset($data->$k)) {
                $i->$k = (bool)$data->$k;
            } else {
                $i->$k = false;
            }
        }

        return $i;
    }

    /**
     * Factory method making the instance from data in the passed text
     *
     * @param string $text as returned by {@link self::export_text()}
     * @return workshop_random_allocator_setting
     */
    public static function instance_from_text($text) {
        return self::instance_from_object(json_decode($text));
    }

    /**
     * Exports the instance data as a text for persistant storage
     *
     * The returned data can be later used by {@self::instance_from_text()} factory method
     * to restore the instance data. The current implementation uses JSON export format.
     *
     * @return string JSON representation of our public properties
     */
    public function export_text() {
        $getvars = function($obj) { return get_object_vars($obj); };
        return json_encode($getvars($this));
    }
}
