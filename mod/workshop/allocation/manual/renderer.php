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
 * @package    workshopallocation
 * @subpackage manual
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Manual allocation renderer class
 */
class workshopallocation_manual_renderer extends mod_workshop_renderer  {

    /** @var workshop module instance */
    protected $workshop;

    ////////////////////////////////////////////////////////////////////////////
    // External rendering API
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Display the table of all current allocations and widgets to modify them
     *
     * @param workshopallocation_manual_allocations $data to be displayed
     * @return string html code
     */
    protected function render_workshopallocation_manual_allocations(workshopallocation_manual_allocations $data) {

        $this->workshop     = $data->workshop;

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
        $table->attributes['class'] = 'allocations';
        $table->head        = array(get_string('participantreviewedby', 'workshop'),
                                    get_string('participant', 'workshop'),
                                    get_string('participantrevierof', 'workshop'));
        $table->rowclasses  = array();
        $table->colclasses  = array('reviewedby', 'peer', 'reviewerof');
        $table->data        = array();
        foreach ($allocations as $allocation) {
            $row = array();
            $row[] = $this->helper_reviewers_of_participant($allocation, $userinfo, $reviewers, $selfassessment);
            $row[] = $this->helper_participant($allocation, $userinfo);
            $row[] = $this->helper_reviewees_of_participant($allocation, $userinfo, $authors, $selfassessment);
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

        return $this->output->container(html_writer::table($table), 'manual-allocator');
    }

    ////////////////////////////////////////////////////////////////////////////
    // Internal helper methods
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Returns information about the workshop participant
     *
     * @return string HTML code
     */
    protected function helper_participant(stdclass $allocation, array $userinfo) {
        $o  = $this->output->user_picture($userinfo[$allocation->userid], array('courseid' => $this->page->course->id));
        $o .= fullname($userinfo[$allocation->userid]);
        $o .= $this->output->container_start(array('submission'));
        if (is_null($allocation->submissionid)) {
            $o .= $this->output->container(get_string('nosubmissionfound', 'workshop'), 'info');
        } else {
            $link = $this->workshop->submission_url($allocation->submissionid);
            $o .= $this->output->container(html_writer::link($link, format_string($allocation->submissiontitle)), 'title');
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
    protected function helper_reviewers_of_participant(stdclass $allocation, array $userinfo, array $reviewers, $selfassessment) {
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
                $handler = new moodle_url($this->page->url, array('mode' => 'new', 'of' => $allocation->userid, 'sesskey' => sesskey()));
                $select = new single_select($handler, 'by', $options, '', array(''=>get_string('chooseuser', 'workshop')), 'addreviewof' . $allocation->userid);
                $select->set_label(get_string('addreviewer', 'workshopallocation_manual'));
                $o .= $this->output->render($select);
            }
        }
        $o .= html_writer::start_tag('ul', array());
        foreach ($allocation->reviewedby as $reviewerid => $assessmentid) {
            $o .= html_writer::start_tag('li', array());
            $o .= $this->output->user_picture($userinfo[$reviewerid], array('courseid' => $this->page->course->id, 'size' => 16));
            $o .= fullname($userinfo[$reviewerid]);

            // delete icon
            $handler = new moodle_url($this->page->url, array('mode' => 'del', 'what' => $assessmentid, 'sesskey' => sesskey()));
            $o .= $this->helper_remove_allocation_icon($handler);

            $o .= html_writer::end_tag('li');
        }
        $o .= html_writer::end_tag('ul');
        return $o;
    }

    /**
     * Returns information about the current reviewees of the given participant and a selector do add new one
     *
     * @return string html code
     */
    protected function helper_reviewees_of_participant(stdclass $allocation, array $userinfo, array $authors, $selfassessment) {
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
            $handler = new moodle_url($this->page->url, array('mode' => 'new', 'by' => $allocation->userid, 'sesskey' => sesskey()));
            $select = new single_select($handler, 'of', $options, '', array(''=>get_string('chooseuser', 'workshop')), 'addreviewby' . $allocation->userid);
            $select->set_label(get_string('addreviewee', 'workshopallocation_manual'));
            $o .= $this->output->render($select);
        } else {
            $o .= $this->output->container(get_string('nothingtoreview', 'workshop'), 'info');
        }
        $o .= html_writer::start_tag('ul', array());
        foreach ($allocation->reviewerof as $authorid => $assessmentid) {
            $o .= html_writer::start_tag('li', array());
            $o .= $this->output->user_picture($userinfo[$authorid], array('courseid' => $this->page->course->id, 'size' => 16));
            $o .= fullname($userinfo[$authorid]);

            // delete icon
            $handler = new moodle_url($this->page->url, array('mode' => 'del', 'what' => $assessmentid, 'sesskey' => sesskey()));
            $o .= $this->helper_remove_allocation_icon($handler);

            $o .= html_writer::end_tag('li');
        }
        $o .= html_writer::end_tag('ul');
        return $o;
    }

    /**
     * Generates an icon link to remove the allocation
     *
     * @param moodle_url $link to the action
     * @return html code to be displayed
     */
    protected function helper_remove_allocation_icon($link) {
        return $this->output->action_icon($link, new pix_icon('t/delete', 'X'));
    }
}
