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
        $wsoutput           = $data->wsoutput;          // moodle_mod_workshop_renderer
        $peers              = $data->peers;             // array prepared array of all allocations data
        $authors            = $data->authors;           // array submission authors
        $reviewers          = $data->reviewers;         // array potential submission reviewers
        $hlauthorid         = $data->hlauthorid;        // int id of the author to highlight
        $hlreviewerid       = $data->hlreviewerid;      // int id of the reviewer to highlight
        $useselfassessment  = $data->useselfassessment; // bool is the self-assessment allowed in this workshop?
        $msg                = $data->msg;               // stdClass message to display

        $wsoutput = $this->page->theme->get_renderer('mod_workshop', $this->page);
        if (empty($peers)) {
            return $wsoutput->status_message((object)array('text' => get_string('nosubmissions', 'workshop')));
        }

        $table              = new html_table();
        $table->set_classes('allocations');
        $table->head        = array(get_string('participantreviewedby', 'workshop'),
                                    get_string('participant', 'workshop'),
                                    get_string('participantrevierof', 'workshop'));
        $table->rowclasses  = array();
        $table->colclasses  = array('reviewedby', 'peer', 'reviewerof');
        $table->data        = array();
        foreach ($peers as $user) {
            $row = array();
            $row[] = $this->reviewers_of_participant($user, $peers, $reviewers, $useselfassessment);
            $row[] = $this->participant($user);
            $row[] = $this->reviewees_of_participant($user, $peers, $authors, $useselfassessment);
            $thisrowclasses = array();
            if ($user->id == $hlauthorid) {
                $thisrowclasses[] = 'highlightreviewedby';
            }
            if ($user->id == $hlreviewerid) {
                $thisrowclasses[] = 'highlightreviewerof';
            }
            $table->rowclasses[] = implode(' ', $thisrowclasses);
            $table->data[] = $row;
        }

        return $this->output->container($wsoutput->status_message($msg) . $this->output->table($table), 'manual-allocator');
    }

    /**
     * Returns information about the workshop participant
     *
     * @param stdClass $user participant data
     * @param workshop API
     * @return string HTML code
     */
    protected function participant(stdClass $user) {
        $o  = $this->output->user_picture($user, $this->page->course->id);
        $o .= fullname($user);
        $o .= $this->output->container_start(array('submission'));
        if (is_null($user->submissionid)) {
            $o .= $this->output->container(get_string('nosubmissionfound', 'workshop'), 'info');
        } else {
            $o .= $this->output->container(format_string($user->submissiontitle), 'title');
            if (is_null($user->submissiongrade)) {
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
     * @param stdClass $user          participant data
     * @param array $peers            objects with properties to display picture and fullname
     * @param array $reviewers        potential reviewers
     * @param bool $useselfassessment shall a user be offered as a reviewer of him/herself
     * @return string html code
     */
    protected function reviewers_of_participant(stdClass $user, array $peers, array $reviewers, $useselfassessment) {
        $o = '';
        if (is_null($user->submissionid)) {
            $o .= $this->output->container(get_string('nothingtoreview', 'workshop'), 'info');
        } else {
            $exclude = array();
            if (! $useselfassessment) {
                $exclude[$user->id] = true;
            }
            // todo add an option to exclude users without own submission
            $options = $this->users_to_menu_options($reviewers, $exclude);
            if ($options) {
                $handler = new moodle_url($this->page->url, array('mode' => 'new', 'of' => $user->id, 'sesskey' => sesskey()));
                $select = html_select::make_popup_form($handler, 'by', $options, 'addreviewof' . $user->id, '',
                    get_string('addreviewer', 'workshopallocation_manual'));
                $select->nothinglabel = get_string('chooseuser', 'workshop');
                $select->set_label(get_string('addreviewer', 'workshopallocation_manual'), $select->id);
                $o .= $this->output->select($select);
            }
        }
        $o .= $this->output->output_start_tag('ul', array());
        foreach ($user->reviewedby as $reviewerid => $assessmentid) {
            $o .= $this->output->output_start_tag('li', array());
            $userpic = new moodle_user_picture();
            $userpic->user = $peers[$reviewerid];
            $userpic->courseid = $this->page->course->id;
            $userpic->size = 16;
            $o .= $this->output->user_picture($userpic);
            $o .= fullname($peers[$reviewerid]);

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
     * @param stdClass $user          participant data
     * @param array $peers            objects with properties to display picture and fullname
     * @param array $authors          potential authors to be reviewed
     * @param bool $useselfassessment shall a user be offered as a reviewer of him/herself
     * @return string html code
     */
    protected function reviewees_of_participant(stdClass $user, array $peers, array $authors, $useselfassessment) {
        $o = '';
        if (is_null($user->submissionid)) {
            $o .= $this->output->container(get_string('withoutsubmission', 'workshop'), 'info');
        }
        $exclude = array();
        if (! $useselfassessment) {
            $exclude[$user->id] = true;
            $o .= $this->output->container(get_string('selfassessmentdisabled', 'workshop'), 'info');
        }
        // todo add an option to exclude users without own submission
        $options = $this->users_to_menu_options($authors, $exclude);
        if ($options) {
            $handler = new moodle_url($this->page->url, array('mode' => 'new', 'by' => $user->id, 'sesskey' => sesskey()));
            $select = html_select::make_popup_form($handler, 'of', $options, 'addreviewby' . $user->id, '',
                                                        get_string('addreviewee', 'workshopallocation_manual'));
            $select->nothinglabel = get_string('chooseuser', 'workshop');
            $select->set_label(get_string('addreviewee', 'workshopallocation_manual'), $select->id);
            $o .= $this->output->select($select);
        } else {
            $o .= $this->output->container(get_string('nothingtoreview', 'workshop'), 'info');
        }
        $o .= $this->output->output_start_tag('ul', array());
        foreach ($user->reviewerof as $authorid => $assessmentid) {
            $o .= $this->output->output_start_tag('li', array());
            $userpic = new moodle_user_picture();
            $userpic->user = $peers[$authorid];
            $userpic->courseid = $this->page->course->id;
            $userpic->size = 16;
            $o .= $this->output->user_picture($userpic, $this->page->course->id);
            $o .= fullname($peers[$authorid]);

            // delete icon
            $handler = new moodle_url($this->page->url, array('mode' => 'del', 'what' => $assessmentid, 'sesskey' => sesskey()));
            $o .= $this->remove_allocation_icon($handler);

            $o .= $this->output->output_end_tag('li');
        }
        $o .= $this->output->output_end_tag('ul');
        return $o;
    }

    /**
     * Given a list of users, returns an array suitable to render the HTML select field
     *
     * @param array $users array of users or array of groups of users
     * @return array of options to be passed to {@link html_select::make_ popup_form()}
     */
    protected function users_to_menu_options($users, array $exclude) {
        $options = array(); // to be returned
        foreach ($users as $user) {
            if (!isset($exclude[$user->id])) {
                $options[$user->id] = fullname($user);
            }
        }
        return $options;
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
