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
class moodle_mod_workshop_allocation_manual_renderer  {

    /** the underlying renderer to use */
    protected $output;

    /** the page we are doing output for */
    protected $page;

    /**
     * Workshop renderer constructor
     * 
     * @param mixed $page the page we are doing output for
     * @param mixed $output lower-level renderer, typically moodle_core_renderer
     * @access public
     * @return void
     */
    public function __construct($page, $output) {
        $this->page   = $page;
        $this->output = $output;
    }


    /**
     * Display the table of all current allocations and widgets to modify them
     * 
     * @param workshop $workshop workshop instance object 
     * @param array $peers prepared array of all allocations
     * @param int $hlauthorid highlight this author
     * @param int $hlreviewerid highlight this reviewer
     * @param object message to display
     * @return string html code
     */
    public function display_allocations(workshop $workshop, &$peers, $hlauthorid=null, $hlreviewerid=null, $msg=null) {

        if (empty($peers)) {
            return $this->status_message((object)array('text' => get_string('nosubmissions', 'workshop')));
        }

        $table              = new html_table();
        $table->set_classes = array('allocations');
        $table->head        = array(get_string('participantreviewedby', 'workshop'),
                                    get_string('participant', 'workshop'),
                                    get_string('participantrevierof', 'workshop'));
        $table->rowclasses  = array();
        $table->colclasses  = array('reviewedby', 'peer', 'reviewerof');
        $table->data        = array();
        foreach ($peers as $user) {
            $row = array();
            $row[] = $this->reviewers_of_participant($user, $workshop, $peers);
            $row[] = $this->participant($user);
            $row[] = $this->reviewees_of_participant($user, $workshop, $peers);
            $thisrowclasses = array();
            if ($user->id == $hlauthorid) {
                $thisrowclasses[] = 'highlightreviewedby';
            }
            if ($user->id == $hlreviewerid) {
                $thisrowclasses[] = 'highlightreviewerof';
            }
            $table->rowclass[] = implode(' ', $thisrowclasses);
            $table->data[] = $row;
        }

        return $this->output->container($this->status_message($msg) . $this->output->table($table), 'manual-allocator');
    }


    /**
     * Returns html code for a status message 
     * 
     * @param string $message to display
     * @return string html
     */
    protected function status_message(stdClass $message) {

        if (empty($message->text)) {
            return '';
        }
        $sty = $message->sty ? $message->sty : 'info';

        $o = '<span>' . $message->text . '</span>';
        $closer = '<a href="' . $this->page->url->out() . '">' . get_string('messageclose', 'workshop') . '</a>';
        $o .= $this->output->container($closer, 'status-message-closer');
        if (isset($message->extra)) {
            $o .= $message->extra;
        }
        return $this->output->container($o, array('status-message', $sty));
    }


    protected function participant(stdClass $user) {

        $o  = print_user_picture($user, $this->page->course->id, null, 35, true);
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
    
        return $o;
    }


    protected function reviewers_of_participant(stdClass $user, workshop $workshop, &$peers) {

        $o = '';
        if (is_null($user->submissionid)) {
            $o .= '<span class="info">' . "\n";
            $o .= get_string('nothingtoreview', 'workshop');
            $o .= '</span>' . "\n";
        } else {
            $options = array();
            foreach ($workshop->get_peer_reviewers() as $reviewer) {
                $options[$reviewer->id] = fullname($reviewer);
            }
            if (!$workshop->useselfassessment) {
                // students can not review their own submissions in this workshop
                if (isset($options[$user->id])) {
                    unset($options[$user->id]);
                }
            }   
            $handler = $this->page->url->out_action() . '&amp;mode=new&amp;of=' . $user->id . '&amp;by=';
            $o .= popup_form($handler, $options, 'addreviewof' . $user->id, '',
                     get_string('chooseuser', 'workshop'), '', '', true, 'self', get_string('addreviewer', 'workshop'));
        }
        $o .= '<ul>' . "\n";
        foreach ($user->reviewedby as $reviewerid => $assessmentid) {
            $o .= '<li>';
            $o .= print_user_picture($peers[$reviewerid], $this->page->course->id, null, 16, true);
            $o .= fullname($peers[$reviewerid]);

            $handler = $this->page->url->out_action(array('mode' => 'del', 'what' => $assessmentid));
            $o .= '<a class="action" href="' . $handler . '"> X </a>';

            $o .= '</li>';
        }
        $o .= '</ul>' . "\n";

        return $o;
    }


    protected function reviewees_of_participant(stdClass $user, workshop $workshop, &$peers) {

        $o = '';
        if (!$workshop->assesswosubmission && is_null($user->submissionid)) {
            $o .= '<span class="info">' . "\n";
            $o .= get_string('cantassesswosubmission', 'workshop');
            $o .= '</span>' . "\n";
        } else {
            $options = array();
            foreach ($workshop->get_peer_authors(true) as $author) {
                $options[$author->id] = fullname($author);
            }
            if (!$workshop->useselfassessment) {
                // students can not be reviewed by themselves in this workshop
                if (isset($options[$user->id])) {
                    unset($options[$user->id]);
                }
            }

            $handler = $this->page->url->out_action() . '&mode=new&amp;by=' . $user->id . '&amp;of=';
            $o .= popup_form($handler, $options, 'addreviewby' . $user->id, '', 
                        get_string('chooseuser', 'workshop'), '', '', true, 'self', get_string('addreviewee', 'workshop'));
            $o .= '<ul>' . "\n";
            foreach ($user->reviewerof as $authorid => $assessmentid) {
                $o .= '<li>';
                $o .= print_user_picture($peers[$authorid], $this->page->course->id, null, 16, true);
                $o .= fullname($peers[$authorid]);

                // delete
                $handler = $this->page->url->out_action(array('mode' => 'del', 'what' => $assessmentid));
                $o .= '<a class="action" href="' . $handler . '"> X </a>'; // todo icon and link title

                $o .= '</li>';
            }
            $o .= '</ul>' . "\n";
        }

        return $o;
    }

}







