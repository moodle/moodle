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
 * Renderer class for the manual allocation UI is defined here
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Manual allocation renderer class
 */
class moodle_workshopallocation_manual_renderer extends moodle_renderer_base  {

    /** the underlying renderer to use */
    protected $output;

    /** the page we are doing output for */
    protected $page;

    /**
     * Workshop renderer constructor
     *
     * @param mixed $page the page we are doing output for
     * @param mixed $output lower-level renderer, typically moodle_core_renderer
     * @return void
     */
    public function __construct($page, $output) {
        $this->page   = $page;
        $this->output = $output;
    }

    /**
     * Display the table of all current allocations and widgets to modify them
     *
     * @param stdClass $data to be displayed - see the top of the function for the list of needed properties
     * @return string html code
     */
    public function display_allocations(stdClass $data) {
        $allocations        = $data->allocations;       // array prepared array of all allocations data
        $userinfo           = $data->userinfo;          // names and pictures of all required users
        $authors            = $data->authors;           // array potential reviewees
        $reviewers          = $data->reviewers;         // array potential submission reviewers
        $hlauthorid         = $data->hlauthorid;        // int id of the author to highlight
        $hlreviewerid       = $data->hlreviewerid;      // int id of the reviewer to highlight
        $selfassessment     = $data->selfassessment;    // bool is the self-assessment allowed in this workshop?

        if (empty($allocations)) {
            return '';
        }

        // convert user collections into drop down menus
        $authors    = array_map('fullname', $authors);
        $reviewers  =  array_map('fullname', $reviewers);

        $table              = new html_table();
        $table->set_classes('allocations');
        $table->head        = array(get_string('participantreviewedby', 'workshop'),
                                    get_string('participant', 'workshop'),
                                    get_string('participantrevierof', 'workshop'));
        $table->rowclasses  = array();
        $table->colclasses  = array('reviewedby', 'peer', 'reviewerof');
        $table->data        = array();
        foreach ($allocations as $allocation) {
            $row = array();
            $row[] = $this->reviewers_of_participant($allocation, $userinfo, $reviewers, $selfassessment);
            $row[] = $this->participant($allocation, $userinfo);
            $row[] = $this->reviewees_of_participant($allocation, $userinfo, $authors, $selfassessment);
            $thisrowclasses = array();
            if ($allocation->userid == $hlauthorid) {
                $thisrowclasses[] = 'highlightreviewedby';
            }
            if ($allocation->userid == $hlreviewerid) {
                $thisrowclasses[] = 'highlightreviewerof';
            }
            $table->rowclasses[] = implode(' ', $thisrowclasses);
            $table->data[] = $row;
        }

        return $this->output->container($this->output->table($table), 'manual-allocator');
    }

    /**
     * Returns information about the workshop participant
     *
     * @return string HTML code
     */
    protected function participant(stdClass $allocation, array $userinfo) {
        $o  = $this->output->user_picture($userinfo[$allocation->userid], $this->page->course->id);
        $o .= fullname($userinfo[$allocation->userid]);
        $o .= $this->output->container_start(array('submission'));
        if (is_null($allocation->submissionid)) {
            $o .= $this->output->container(get_string('nosubmissionfound', 'workshop'), 'info');
        } else {
            $o .= $this->output->container(format_string($allocation->submissiontitle), 'title');
            if (is_null($allocation->submissiongrade)) {
                $o .= $this->output->container(get_string('nogradeyet', 'workshop'), array('grade', 'missing'));
            } else {
                $o .= $this->output->container(get_string('alreadygraded', 'workshop'), array('grade', 'missing'));
            }
        }
        $o .= $this->output->container_end();
        return $o;
    }

    /**
     * Returns information about the current reviewers of the given participant and a selector do add new one
     *
     * @return string html code
     */
    protected function reviewers_of_participant(stdClass $allocation, array $userinfo, array $reviewers, $selfassessment) {
        $o = '';
        if (is_null($allocation->submissionid)) {
            $o .= $this->output->container(get_string('nothingtoreview', 'workshop'), 'info');
        } else {
            $exclude = array();
            if (! $selfassessment) {
                $exclude[$allocation->userid] = true;
            }
            // todo add an option to exclude users without own submission
            $options = array_diff_key($reviewers, $exclude);
            if ($options) {
                $handler = new moodle_url($this->page->url,
                                            array('mode' => 'new', 'of' => $allocation->userid, 'sesskey' => sesskey()));
                $select = html_select::make_popup_form($handler, 'by', $options, 'addreviewof' . $allocation->userid, '',
                                            get_string('addreviewer', 'workshopallocation_manual'));
                $select->nothinglabel = get_string('chooseuser', 'workshop');
                $select->set_label(get_string('addreviewer', 'workshopallocation_manual'), $select->id);
                $o .= $this->output->select($select);
            }
        }
        $o .= $this->output->output_start_tag('ul', array());
        foreach ($allocation->reviewedby as $reviewerid => $assessmentid) {
            $o .= $this->output->output_start_tag('li', array());
            $allocationpic = new moodle_user_picture();
            $allocationpic->user = $userinfo[$reviewerid];
            $allocationpic->courseid = $this->page->course->id;
            $allocationpic->size = 16;
            $o .= $this->output->user_picture($allocationpic);
            $o .= fullname($userinfo[$reviewerid]);

            // delete icon
            $handler = new moodle_url($this->page->url, array('mode' => 'del', 'what' => $assessmentid, 'sesskey' => sesskey()));
            $o .= $this->remove_allocation_icon($handler);

            $o .= $this->output->output_end_tag('li');
        }
        $o .= $this->output->output_end_tag('ul');
        return $o;
    }

    /**
     * Returns information about the current reviewees of the given participant and a selector do add new one
     *
     * @return string html code
     */
    protected function reviewees_of_participant(stdClass $allocation, array $userinfo, array $authors, $selfassessment) {
        $o = '';
        if (is_null($allocation->submissionid)) {
            $o .= $this->output->container(get_string('withoutsubmission', 'workshop'), 'info');
        }
        $exclude = array();
        if (! $selfassessment) {
            $exclude[$allocation->userid] = true;
            $o .= $this->output->container(get_string('selfassessmentdisabled', 'workshop'), 'info');
        }
        // todo add an option to exclude users without own submission
        $options = array_diff_key($authors, $exclude);
        if ($options) {
            $handler = new moodle_url($this->page->url,
                                        array('mode' => 'new', 'by' => $allocation->userid, 'sesskey' => sesskey()));
            $select = html_select::make_popup_form($handler, 'of', $options, 'addreviewby' . $allocation->userid, '',
                                        get_string('addreviewee', 'workshopallocation_manual'));
            $select->nothinglabel = get_string('chooseuser', 'workshop');
            $select->set_label(get_string('addreviewee', 'workshopallocation_manual'), $select->id);
            $o .= $this->output->select($select);
        } else {
            $o .= $this->output->container(get_string('nothingtoreview', 'workshop'), 'info');
        }
        $o .= $this->output->output_start_tag('ul', array());
        foreach ($allocation->reviewerof as $authorid => $assessmentid) {
            $o .= $this->output->output_start_tag('li', array());
            $allocationpic = new moodle_user_picture();
            $allocationpic->user = $userinfo[$authorid];
            $allocationpic->courseid = $this->page->course->id;
            $allocationpic->size = 16;
            $o .= $this->output->user_picture($allocationpic, $this->page->course->id);
            $o .= fullname($userinfo[$authorid]);

            // delete icon
            $handler = new moodle_url($this->page->url, array('mode' => 'del', 'what' => $assessmentid, 'sesskey' => sesskey()));
            $o .= $this->remove_allocation_icon($handler);

            $o .= $this->output->output_end_tag('li');
        }
        $o .= $this->output->output_end_tag('ul');
        return $o;
    }

    /**
     * Generates an icon link to remove the allocation
     *
     * @param moodle_url $link to the action
     * @return html code to be displayed
     */
    protected function remove_allocation_icon($link) {
        $icon = new moodle_action_icon();
        $icon->image->src = $this->old_icon_url('i/cross_red_big');
        $icon->image->alt = 'X';
        $icon->link->url = $link;

        return $this->output->action_icon($icon);

    }
}
