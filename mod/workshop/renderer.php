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
 * Workshop module renderering methods are defined here
 *
 * @package    mod_workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Workshop module renderer class
 *
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_workshop_renderer extends plugin_renderer_base {

    ////////////////////////////////////////////////////////////////////////////
    // External API - methods to render workshop renderable components
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Renders the tertiary nav for the allocation pages
     *
     * @param \mod_workshop\output\actionbar $actionbar
     * @return bool|string the rendered output
     */
    public function render_allocation_menu(\mod_workshop\output\actionbar $actionbar): string {
        return $this->render_from_template('mod_workshop/action_bar', $actionbar->export_for_template($this));
    }

    /**
     * Renders workshop message
     *
     * @param workshop_message $message to display
     * @return string html code
     */
    protected function render_workshop_message(workshop_message $message) {

        $text   = $message->get_message();
        $url    = $message->get_action_url();
        $label  = $message->get_action_label();

        if (empty($text) and empty($label)) {
            return '';
        }

        switch ($message->get_type()) {
        case workshop_message::TYPE_OK:
            $sty = 'ok';
            break;
        case workshop_message::TYPE_ERROR:
            $sty = 'error';
            break;
        default:
            $sty = 'info';
        }

        $o = html_writer::tag('span', $message->get_message());

        if (!is_null($url) and !is_null($label)) {
            $o .= $this->output->single_button($url, $label, 'get');
        }

        return $this->output->container($o, array('message', $sty));
    }


    /**
     * Renders full workshop submission
     *
     * @param workshop_submission $submission
     * @return string HTML
     */
    protected function render_workshop_submission(workshop_submission $submission) {
        global $CFG;

        $o  = '';    // output HTML code
        $anonymous = $submission->is_anonymous();
        $classes = 'submission-full';
        if ($anonymous) {
            $classes .= ' anonymous';
        }
        $o .= $this->output->container_start($classes);
        $o .= $this->output->container_start('header');

        $title = format_string($submission->title);

        if ($this->page->url != $submission->url) {
            $title = html_writer::link($submission->url, $title);
        }

        $o .= $this->output->heading($title, 3, 'title');

        if (!$anonymous) {
            $author = new stdclass();
            $additionalfields = explode(',', implode(',', \core_user\fields::get_picture_fields()));
            $author = username_load_fields_from_object($author, $submission, 'author', $additionalfields);
            $userpic            = $this->output->user_picture($author, array('courseid' => $this->page->course->id, 'size' => 64));
            $userurl            = new moodle_url('/user/view.php',
                                            array('id' => $author->id, 'course' => $this->page->course->id));
            $a                  = new stdclass();
            $a->name            = fullname($author);
            $a->url             = $userurl->out();
            $byfullname         = get_string('byfullname', 'workshop', $a);
            $oo  = $this->output->container($userpic, 'picture');
            $oo .= $this->output->container($byfullname, 'fullname');

            $o .= $this->output->container($oo, 'author');
        }

        $created = get_string('userdatecreated', 'workshop', userdate($submission->timecreated));
        $o .= $this->output->container($created, 'userdate created');

        if ($submission->timemodified > $submission->timecreated) {
            $modified = get_string('userdatemodified', 'workshop', userdate($submission->timemodified));
            $o .= $this->output->container($modified, 'userdate modified');
        }

        $o .= $this->output->container_end(); // end of header

        $content = file_rewrite_pluginfile_urls($submission->content, 'pluginfile.php', $this->page->context->id,
                                                        'mod_workshop', 'submission_content', $submission->id);
        $content = format_text($content, $submission->contentformat, array('overflowdiv'=>true));
        if (!empty($content)) {
            if (!empty($CFG->enableplagiarism)) {
                require_once($CFG->libdir.'/plagiarismlib.php');
                $content .= plagiarism_get_links(array('userid' => $submission->authorid,
                    'content' => $submission->content,
                    'cmid' => $this->page->cm->id,
                    'course' => $this->page->course));
            }
        }
        $o .= $this->output->container($content, 'content');

        $o .= $this->helper_submission_attachments($submission->id, 'html');

        $o .= $this->output->container_end(); // end of submission-full

        return $o;
    }

    /**
     * Renders short summary of the submission
     *
     * @param workshop_submission_summary $summary
     * @return string text to be echo'ed
     */
    protected function render_workshop_submission_summary(workshop_submission_summary $summary) {

        $o  = '';    // output HTML code
        $anonymous = $summary->is_anonymous();
        $classes = 'submission-summary';

        if ($anonymous) {
            $classes .= ' anonymous';
        }

        $gradestatus = '';

        if ($summary->status == 'notgraded') {
            $classes    .= ' notgraded';
            $gradestatus = $this->output->container(get_string('nogradeyet', 'workshop'), 'grade-status');

        } else if ($summary->status == 'graded') {
            $classes    .= ' graded';
            $gradestatus = $this->output->container(get_string('alreadygraded', 'workshop'), 'grade-status');
        }

        $o .= $this->output->container_start($classes);  // main wrapper
        $o .= html_writer::link($summary->url, format_string($summary->title), array('class' => 'title'));

        if (!$anonymous) {
            $author             = new stdClass();
            $additionalfields = explode(',', implode(',', \core_user\fields::get_picture_fields()));
            $author = username_load_fields_from_object($author, $summary, 'author', $additionalfields);
            $userpic            = $this->output->user_picture($author, array('courseid' => $this->page->course->id, 'size' => 35));
            $userurl            = new moodle_url('/user/view.php',
                                            array('id' => $author->id, 'course' => $this->page->course->id));
            $a                  = new stdClass();
            $a->name            = fullname($author);
            $a->url             = $userurl->out();
            $byfullname         = get_string('byfullname', 'workshop', $a);

            $oo  = $this->output->container($userpic, 'picture');
            $oo .= $this->output->container($byfullname, 'fullname');
            $o  .= $this->output->container($oo, 'author');
        }

        $created = get_string('userdatecreated', 'workshop', userdate($summary->timecreated));
        $o .= $this->output->container($created, 'userdate created');

        if ($summary->timemodified > $summary->timecreated) {
            $modified = get_string('userdatemodified', 'workshop', userdate($summary->timemodified));
            $o .= $this->output->container($modified, 'userdate modified');
        }

        $o .= $gradestatus;
        $o .= $this->output->container_end(); // end of the main wrapper
        return $o;
    }

    /**
     * Renders full workshop example submission
     *
     * @param workshop_example_submission $example
     * @return string HTML
     */
    protected function render_workshop_example_submission(workshop_example_submission $example) {

        $o  = '';    // output HTML code
        $classes = 'submission-full example';
        $o .= $this->output->container_start($classes);
        $o .= $this->output->container_start('header');
        $o .= $this->output->container(format_string($example->title), array('class' => 'title'));
        $o .= $this->output->container_end(); // end of header

        $content = file_rewrite_pluginfile_urls($example->content, 'pluginfile.php', $this->page->context->id,
                                                        'mod_workshop', 'submission_content', $example->id);
        $content = format_text($content, $example->contentformat, array('overflowdiv'=>true));
        $o .= $this->output->container($content, 'content');

        $o .= $this->helper_submission_attachments($example->id, 'html');

        $o .= $this->output->container_end(); // end of submission-full

        return $o;
    }

    /**
     * Renders short summary of the example submission
     *
     * @param workshop_example_submission_summary $summary
     * @return string text to be echo'ed
     */
    protected function render_workshop_example_submission_summary(workshop_example_submission_summary $summary) {

        $o  = '';    // output HTML code

        // wrapping box
        $o .= $this->output->box_start('generalbox example-summary ' . $summary->status);

        // title
        $o .= $this->output->container_start('example-title');
        $o .= html_writer::link($summary->url, format_string($summary->title), array('class' => 'title'));

        if ($summary->editable) {
            $o .= $this->output->action_icon($summary->editurl, new pix_icon('i/edit', get_string('edit')));
        }
        $o .= $this->output->container_end();

        // additional info
        if ($summary->status == 'notgraded') {
            $o .= $this->output->container(get_string('nogradeyet', 'workshop'), 'example-info nograde');
        } else {
            $o .= $this->output->container(get_string('gradeinfo', 'workshop' , $summary->gradeinfo), 'example-info grade');
        }

        // button to assess
        $button = new single_button($summary->assessurl, $summary->assesslabel, 'get');
        $o .= $this->output->container($this->output->render($button), 'example-actions');

        // end of wrapping box
        $o .= $this->output->box_end();

        return $o;
    }

    /**
     * Renders the user plannner tool
     *
     * @param workshop_user_plan $plan prepared for the user
     * @return string html code to be displayed
     */
    protected function render_workshop_user_plan(workshop_user_plan $plan) {
        $o  = '';    // Output HTML code.
        $numberofphases = count($plan->phases);
        $o .= html_writer::start_tag('div', array(
            'class' => 'userplan',
            'aria-labelledby' => 'mod_workshop-userplanheading',
            'aria-describedby' => 'mod_workshop-userplanaccessibilitytitle',
        ));
        $o .= html_writer::span(get_string('userplanaccessibilitytitle', 'workshop', $numberofphases),
            'accesshide', array('id' => 'mod_workshop-userplanaccessibilitytitle'));
        $o .= html_writer::link('#mod_workshop-userplancurrenttasks', get_string('userplanaccessibilityskip', 'workshop'),
            array('class' => 'accesshide'));
        foreach ($plan->phases as $phasecode => $phase) {
            $o .= html_writer::start_tag('dl', array('class' => 'phase'));
            $actions = '';

            if ($phase->active) {
                // Mark the section as the current one.
                $icon = $this->output->pix_icon('i/marked', '', 'moodle', ['role' => 'presentation']);
                $actions .= get_string('userplancurrentphase', 'workshop').' '.$icon;

            } else {
                // Display a control widget to switch to the given phase or mark the phase as the current one.
                foreach ($phase->actions as $action) {
                    if ($action->type === 'switchphase') {
                        if ($phasecode == workshop::PHASE_ASSESSMENT && $plan->workshop->phase == workshop::PHASE_SUBMISSION
                                && $plan->workshop->phaseswitchassessment) {
                            $icon = new pix_icon('i/scheduled', get_string('switchphaseauto', 'mod_workshop'));
                        } else {
                            $icon = new pix_icon('i/marker', get_string('switchphase'.$phasecode, 'mod_workshop'));
                        }
                        $actions .= $this->output->action_icon($action->url, $icon, null, null, true);
                    }
                }
            }

            if (!empty($actions)) {
                $actions = $this->output->container($actions, 'actions');
            }
            $classes = 'phase' . $phasecode;
            if ($phase->active) {
                $title = html_writer::span($phase->title, 'phasetitle', ['id' => 'mod_workshop-userplancurrenttasks']);
                $classes .= ' active';
            } else {
                $title = html_writer::span($phase->title, 'phasetitle');
                $classes .= ' nonactive';
            }
            $o .= html_writer::start_tag('dt', array('class' => $classes));
            $o .= $this->output->container($title . $actions);
            $o .= html_writer::start_tag('dd', array('class' => $classes. ' phasetasks'));
            $o .= $this->helper_user_plan_tasks($phase->tasks);
            $o .= html_writer::end_tag('dd');
            $o .= html_writer::end_tag('dl');
        }
        $o .= html_writer::end_tag('div');
        return $o;
    }

    /**
     * Renders the result of the submissions allocation process
     *
     * @param workshop_allocation_result $result as returned by the allocator's init() method
     * @return string HTML to be echoed
     */
    protected function render_workshop_allocation_result(workshop_allocation_result $result) {
        global $CFG;

        $status = $result->get_status();

        if (is_null($status) or $status == workshop_allocation_result::STATUS_VOID) {
            debugging('Attempt to render workshop_allocation_result with empty status', DEBUG_DEVELOPER);
            return '';
        }

        switch ($status) {
        case workshop_allocation_result::STATUS_FAILED:
            if ($message = $result->get_message()) {
                $message = new workshop_message($message, workshop_message::TYPE_ERROR);
            } else {
                $message = new workshop_message(get_string('allocationerror', 'workshop'), workshop_message::TYPE_ERROR);
            }
            break;

        case workshop_allocation_result::STATUS_CONFIGURED:
            if ($message = $result->get_message()) {
                $message = new workshop_message($message, workshop_message::TYPE_INFO);
            } else {
                $message = new workshop_message(get_string('allocationconfigured', 'workshop'), workshop_message::TYPE_INFO);
            }
            break;

        case workshop_allocation_result::STATUS_EXECUTED:
            if ($message = $result->get_message()) {
                $message = new workshop_message($message, workshop_message::TYPE_OK);
            } else {
                $message = new workshop_message(get_string('allocationdone', 'workshop'), workshop_message::TYPE_OK);
            }
            break;

        default:
            throw new coding_exception('Unknown allocation result status', $status);
        }

        // start with the message
        $o = $this->render($message);

        // display the details about the process if available
        $logs = $result->get_logs();
        if (is_array($logs) and !empty($logs)) {
            $o .= html_writer::start_tag('ul', array('class' => 'allocation-init-results'));
            foreach ($logs as $log) {
                if ($log->type == 'debug' and !$CFG->debugdeveloper) {
                    // display allocation debugging messages for developers only
                    continue;
                }
                $class = $log->type;
                if ($log->indent) {
                    $class .= ' indent';
                }
                $o .= html_writer::tag('li', $log->message, array('class' => $class)).PHP_EOL;
            }
            $o .= html_writer::end_tag('ul');
        }

        return $o;
    }

    /**
     * Renders the workshop grading report
     *
     * @param workshop_grading_report $gradingreport
     * @return string html code
     */
    protected function render_workshop_grading_report(workshop_grading_report $gradingreport) {

        $data       = $gradingreport->get_data();
        $options    = $gradingreport->get_options();
        $grades     = $data->grades;
        $userinfo   = $data->userinfo;

        if (empty($grades)) {
            return $this->output->notification(get_string('nothingtodisplay'), 'success', false);
        }

        $table = new html_table();
        $table->attributes['class'] = 'grading-report table-striped table-hover';

        $sortbyfirstname = $this->helper_sortable_heading(get_string('firstname'), 'firstname', $options->sortby, $options->sorthow);
        $sortbylastname = $this->helper_sortable_heading(get_string('lastname'), 'lastname', $options->sortby, $options->sorthow);
        if (self::fullname_format() == 'lf') {
            $sortbyname = $sortbylastname . ' / ' . $sortbyfirstname;
        } else {
            $sortbyname = $sortbyfirstname . ' / ' . $sortbylastname;
        }

        $sortbysubmisstiontitle = $this->helper_sortable_heading(get_string('submission', 'workshop'), 'submissiontitle',
                $options->sortby, $options->sorthow);
        $sortbysubmisstionlastmodified = $this->helper_sortable_heading(get_string('submissionlastmodified', 'workshop'),
                'submissionmodified', $options->sortby, $options->sorthow);
        $sortbysubmisstion = $sortbysubmisstiontitle . ' / ' . $sortbysubmisstionlastmodified;

        $table->head = array();
        $table->head[] = $sortbyname;
        $table->head[] = $sortbysubmisstion;

        // If we are in submission phase ignore the following headers (columns).
        if ($options->workshopphase != workshop::PHASE_SUBMISSION) {
            $table->head[] = $this->helper_sortable_heading(get_string('receivedgrades', 'workshop'));
            if ($options->showsubmissiongrade) {
                $table->head[] = $this->helper_sortable_heading(get_string('submissiongradeof', 'workshop', $data->maxgrade),
                        'submissiongrade', $options->sortby, $options->sorthow);
            }
            $table->head[] = $this->helper_sortable_heading(get_string('givengrades', 'workshop'));
            if ($options->showgradinggrade) {
                $table->head[] = $this->helper_sortable_heading(get_string('gradinggradeof', 'workshop', $data->maxgradinggrade),
                        'gradinggrade', $options->sortby, $options->sorthow);
            }
        }
        $table->rowclasses  = array();
        $table->colclasses  = array();
        $table->data        = array();

        foreach ($grades as $participant) {
            $numofreceived  = count($participant->reviewedby);
            $numofgiven     = count($participant->reviewerof);
            $published      = $participant->submissionpublished;

            // compute the number of <tr> table rows needed to display this participant
            if ($numofreceived > 0 and $numofgiven > 0) {
                $numoftrs       = workshop::lcm($numofreceived, $numofgiven);
                $spanreceived   = $numoftrs / $numofreceived;
                $spangiven      = $numoftrs / $numofgiven;
            } elseif ($numofreceived == 0 and $numofgiven > 0) {
                $numoftrs       = $numofgiven;
                $spanreceived   = $numoftrs;
                $spangiven      = $numoftrs / $numofgiven;
            } elseif ($numofreceived > 0 and $numofgiven == 0) {
                $numoftrs       = $numofreceived;
                $spanreceived   = $numoftrs / $numofreceived;
                $spangiven      = $numoftrs;
            } else {
                $numoftrs       = 1;
                $spanreceived   = 1;
                $spangiven      = 1;
            }

            for ($tr = 0; $tr < $numoftrs; $tr++) {
                $row = new html_table_row();
                if ($published) {
                    $row->attributes['class'] = 'published';
                }
                // column #1 - participant - spans over all rows
                if ($tr == 0) {
                    $cell = new html_table_cell();
                    $cell->text = $this->helper_grading_report_participant($participant, $userinfo);
                    $cell->rowspan = $numoftrs;
                    $cell->attributes['class'] = 'participant';
                    $row->cells[] = $cell;
                }
                // column #2 - submission - spans over all rows
                if ($tr == 0) {
                    $cell = new html_table_cell();
                    $cell->text = $this->helper_grading_report_submission($participant);
                    $cell->rowspan = $numoftrs;
                    $cell->attributes['class'] = 'submission';
                    $row->cells[] = $cell;
                }

                // If we are in submission phase ignore the following columns.
                if ($options->workshopphase == workshop::PHASE_SUBMISSION) {
                    $table->data[] = $row;
                    continue;
                }

                // column #3 - received grades
                if ($tr % $spanreceived == 0) {
                    $idx = intval($tr / $spanreceived);
                    $assessment = self::array_nth($participant->reviewedby, $idx);
                    $cell = new html_table_cell();
                    $cell->text = $this->helper_grading_report_assessment($assessment, $options->showreviewernames, $userinfo,
                            get_string('gradereceivedfrom', 'workshop'));
                    $cell->rowspan = $spanreceived;
                    $cell->attributes['class'] = 'receivedgrade';
                    if (is_null($assessment) or is_null($assessment->grade)) {
                        $cell->attributes['class'] .= ' null';
                    } else {
                        $cell->attributes['class'] .= ' notnull';
                    }
                    $row->cells[] = $cell;
                }
                // column #4 - total grade for submission
                if ($options->showsubmissiongrade and $tr == 0) {
                    $cell = new html_table_cell();
                    $cell->text = $this->helper_grading_report_grade($participant->submissiongrade, $participant->submissiongradeover);
                    $cell->rowspan = $numoftrs;
                    $cell->attributes['class'] = 'submissiongrade';
                    $row->cells[] = $cell;
                }
                // column #5 - given grades
                if ($tr % $spangiven == 0) {
                    $idx = intval($tr / $spangiven);
                    $assessment = self::array_nth($participant->reviewerof, $idx);
                    $cell = new html_table_cell();
                    $cell->text = $this->helper_grading_report_assessment($assessment, $options->showauthornames, $userinfo,
                            get_string('gradegivento', 'workshop'));
                    $cell->rowspan = $spangiven;
                    $cell->attributes['class'] = 'givengrade';
                    if (is_null($assessment) or is_null($assessment->grade)) {
                        $cell->attributes['class'] .= ' null';
                    } else {
                        $cell->attributes['class'] .= ' notnull';
                    }
                    $row->cells[] = $cell;
                }
                // column #6 - total grade for assessment
                if ($options->showgradinggrade and $tr == 0) {
                    $cell = new html_table_cell();
                    $cell->text = $this->helper_grading_report_grade($participant->gradinggrade);
                    $cell->rowspan = $numoftrs;
                    $cell->attributes['class'] = 'gradinggrade';
                    $row->cells[] = $cell;
                }

                $table->data[] = $row;
            }
        }

        return html_writer::table($table);
    }

    /**
     * Renders the feedback for the author of the submission
     *
     * @param workshop_feedback_author $feedback
     * @return string HTML
     */
    protected function render_workshop_feedback_author(workshop_feedback_author $feedback) {
        return $this->helper_render_feedback($feedback);
    }

    /**
     * Renders the feedback for the reviewer of the submission
     *
     * @param workshop_feedback_reviewer $feedback
     * @return string HTML
     */
    protected function render_workshop_feedback_reviewer(workshop_feedback_reviewer $feedback) {
        return $this->helper_render_feedback($feedback);
    }

    /**
     * Helper method to rendering feedback
     *
     * @param workshop_feedback_author|workshop_feedback_reviewer $feedback
     * @return string HTML
     */
    private function helper_render_feedback($feedback) {

        $o  = '';    // output HTML code
        $o .= $this->output->container_start('feedback feedbackforauthor');
        $o .= $this->output->container_start('header');
        $o .= $this->output->heading(get_string('feedbackby', 'workshop', s(fullname($feedback->get_provider()))), 3, 'title');

        $userpic = $this->output->user_picture($feedback->get_provider(), array('courseid' => $this->page->course->id, 'size' => 32));
        $o .= $this->output->container($userpic, 'picture');
        $o .= $this->output->container_end(); // end of header

        $content = format_text($feedback->get_content(), $feedback->get_format(), array('overflowdiv' => true));
        $o .= $this->output->container($content, 'content');

        $o .= $this->output->container_end();

        return $o;
    }

    /**
     * Renders the full assessment
     *
     * @param workshop_assessment $assessment
     * @return string HTML
     */
    protected function render_workshop_assessment(workshop_assessment $assessment) {

        $o = ''; // output HTML code
        $anonymous = is_null($assessment->reviewer);
        $classes = 'assessment-full';
        if ($anonymous) {
            $classes .= ' anonymous';
        }

        $o .= $this->output->container_start($classes);
        $o .= $this->output->container_start('header');

        if (!empty($assessment->title)) {
            $title = s($assessment->title);
        } else {
            $title = get_string('assessment', 'workshop');
        }
        if (($assessment->url instanceof moodle_url) and ($this->page->url != $assessment->url)) {
            $o .= $this->output->container(html_writer::link($assessment->url, $title), 'title');
        } else {
            $o .= $this->output->container($title, 'title');
        }

        if (!$anonymous) {
            $reviewer   = $assessment->reviewer;
            $userpic    = $this->output->user_picture($reviewer, array('courseid' => $this->page->course->id, 'size' => 32));

            $userurl    = new moodle_url('/user/view.php',
                                       array('id' => $reviewer->id, 'course' => $this->page->course->id));
            $a          = new stdClass();
            $a->name    = fullname($reviewer);
            $a->url     = $userurl->out();
            $byfullname = get_string('assessmentby', 'workshop', $a);
            $oo         = $this->output->container($userpic, 'picture');
            $oo        .= $this->output->container($byfullname, 'fullname');

            $o .= $this->output->container($oo, 'reviewer');
        }

        if (is_null($assessment->realgrade)) {
            $o .= $this->output->container(
                get_string('notassessed', 'workshop'),
                'grade nograde'
            );
        } else {
            $a              = new stdClass();
            $a->max         = $assessment->maxgrade;
            $a->received    = $assessment->realgrade;
            $o .= $this->output->container(
                get_string('gradeinfo', 'workshop', $a),
                'grade'
            );

            if (!is_null($assessment->weight) and $assessment->weight != 1) {
                $o .= $this->output->container(
                    get_string('weightinfo', 'workshop', $assessment->weight),
                    'weight'
                );
            }
        }

        $o .= $this->output->container_start('actions');
        foreach ($assessment->actions as $action) {
            $o .= $this->output->single_button($action->url, $action->label, $action->method);
        }
        $o .= $this->output->container_end(); // actions

        $o .= $this->output->container_end(); // header

        if (!is_null($assessment->form)) {
            $o .= print_collapsible_region_start('assessment-form-wrapper', uniqid('workshop-assessment'),
                    get_string('assessmentform', 'workshop'), 'workshop-viewlet-assessmentform-collapsed', false, true);
            $o .= $this->output->container(self::moodleform($assessment->form), 'assessment-form');
            $o .= print_collapsible_region_end(true);

            if (!$assessment->form->is_editable()) {
                $o .= $this->overall_feedback($assessment);
            }
        }

        $o .= $this->output->container_end(); // main wrapper

        return $o;
    }

    /**
     * Renders the assessment of an example submission
     *
     * @param workshop_example_assessment $assessment
     * @return string HTML
     */
    protected function render_workshop_example_assessment(workshop_example_assessment $assessment) {
        return $this->render_workshop_assessment($assessment);
    }

    /**
     * Renders the reference assessment of an example submission
     *
     * @param workshop_example_reference_assessment $assessment
     * @return string HTML
     */
    protected function render_workshop_example_reference_assessment(workshop_example_reference_assessment $assessment) {
        return $this->render_workshop_assessment($assessment);
    }

    /**
     * Renders the overall feedback for the author of the submission
     *
     * @param workshop_assessment $assessment
     * @return string HTML
     */
    protected function overall_feedback(workshop_assessment $assessment) {

        $content = $assessment->get_overall_feedback_content();

        if ($content === false) {
            return '';
        }

        $o = '';

        if (!is_null($content)) {
            $o .= $this->output->container($content, 'content');
        }

        $attachments = $assessment->get_overall_feedback_attachments();

        if (!empty($attachments)) {
            $o .= $this->output->container_start('attachments');
            $images = '';
            $files = '';
            foreach ($attachments as $attachment) {
                $icon = $this->output->pix_icon(file_file_icon($attachment), get_mimetype_description($attachment),
                    'moodle', array('class' => 'icon'));
                $link = html_writer::link($attachment->fileurl, $icon.' '.substr($attachment->filepath.$attachment->filename, 1));
                if (file_mimetype_in_typegroup($attachment->mimetype, 'web_image')) {
                    $preview = html_writer::empty_tag('img', array('src' => $attachment->previewurl, 'alt' => '', 'class' => 'preview'));
                    $preview = html_writer::tag('a', $preview, array('href' => $attachment->fileurl));
                    $images .= $this->output->container($preview);
                } else {
                    $files .= html_writer::tag('li', $link, array('class' => $attachment->mimetype));
                }
            }
            if ($images) {
                $images = $this->output->container($images, 'images');
            }

            if ($files) {
                $files = html_writer::tag('ul', $files, array('class' => 'files'));
            }

            $o .= $images.$files;
            $o .= $this->output->container_end();
        }

        if ($o === '') {
            return '';
        }

        $o = $this->output->box($o, 'overallfeedback');
        $o = print_collapsible_region($o, 'overall-feedback-wrapper', uniqid('workshop-overall-feedback'),
                get_string('overallfeedback', 'workshop'), 'workshop-viewlet-overallfeedback-collapsed', false, true);

        return $o;
    }

    /**
     * Renders a perpage selector for workshop listings
     *
     * The scripts using this have to define the $PAGE->url prior to calling this
     * and deal with eventually submitted value themselves.
     *
     * @param int $current current value of the perpage parameter
     * @return string HTML
     */
    public function perpage_selector($current=10) {

        $options = array();
        foreach (array(10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200, 300, 400, 500, 1000) as $option) {
            if ($option != $current) {
                $options[$option] = $option;
            }
        }
        $select = new single_select($this->page->url, 'perpage', $options, '', array('' => get_string('showingperpagechange', 'mod_workshop')));
        $select->label = get_string('showingperpage', 'mod_workshop', $current);
        $select->method = 'post';

        return $this->output->container($this->output->render($select), 'perpagewidget');
    }

    /**
     * Render the initials bars for workshop.
     *
     * @param workshop $workshop the current workshop of initial bars.
     * @param moodle_url $url base URL object.
     * @return string HTML.
     */
    public function initials_bars(workshop $workshop, moodle_url $url): string {
        $ifirst = $workshop->get_initial_first();
        $ilast = $workshop->get_initial_last();

        $html = $this->output->initials_bar($ifirst, 'firstinitial', get_string('firstname'), 'ifirst', $url);
        $html .= $this->output->initials_bar($ilast, 'lastinitial', get_string('lastname'), 'ilast', $url);
        return $html;
    }

    /**
     * Renders the user's final grades
     *
     * @param workshop_final_grades $grades with the info about grades in the gradebook
     * @return string HTML
     */
    protected function render_workshop_final_grades(workshop_final_grades $grades) {

        $out = html_writer::start_tag('div', array('class' => 'finalgrades'));

        if (!empty($grades->submissiongrade)) {
            $cssclass = 'grade submissiongrade';
            if ($grades->submissiongrade->hidden) {
                $cssclass .= ' hiddengrade';
            }
            $out .= html_writer::tag(
                'div',
                html_writer::tag('div', get_string('submissiongrade', 'mod_workshop'), array('class' => 'gradetype')) .
                html_writer::tag('div', $grades->submissiongrade->str_long_grade, array('class' => 'gradevalue')),
                array('class' => $cssclass)
            );
        }

        if (!empty($grades->assessmentgrade)) {
            $cssclass = 'grade assessmentgrade';
            if ($grades->assessmentgrade->hidden) {
                $cssclass .= ' hiddengrade';
            }
            $out .= html_writer::tag(
                'div',
                html_writer::tag('div', get_string('gradinggrade', 'mod_workshop'), array('class' => 'gradetype')) .
                html_writer::tag('div', $grades->assessmentgrade->str_long_grade, array('class' => 'gradevalue')),
                array('class' => $cssclass)
            );
        }

        $out .= html_writer::end_tag('div');

        return $out;
    }

    ////////////////////////////////////////////////////////////////////////////
    // Internal rendering helper methods
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Renders a list of files attached to the submission
     *
     * If format==html, then format a html string. If format==text, then format a text-only string.
     * Otherwise, returns html for non-images and html to display the image inline.
     *
     * @param int $submissionid submission identifier
     * @param string format the format of the returned string - html|text
     * @return string formatted text to be echoed
     */
    protected function helper_submission_attachments($submissionid, $format = 'html') {
        global $CFG;
        require_once($CFG->libdir.'/filelib.php');

        $fs     = get_file_storage();
        $ctx    = $this->page->context;
        $files  = $fs->get_area_files($ctx->id, 'mod_workshop', 'submission_attachment', $submissionid);

        $outputimgs     = '';   // images to be displayed inline
        $outputfiles    = '';   // list of attachment files

        foreach ($files as $file) {
            if ($file->is_directory()) {
                continue;
            }

            $filepath   = $file->get_filepath();
            $filename   = $file->get_filename();
            $fileurl    = moodle_url::make_pluginfile_url($ctx->id, 'mod_workshop', 'submission_attachment',
                            $submissionid, $filepath, $filename, true);
            $embedurl   = moodle_url::make_pluginfile_url($ctx->id, 'mod_workshop', 'submission_attachment',
                            $submissionid, $filepath, $filename, false);
            $embedurl   = new moodle_url($embedurl, array('preview' => 'bigthumb'));
            $type       = $file->get_mimetype();
            $image      = $this->output->pix_icon(file_file_icon($file), get_mimetype_description($file), 'moodle', array('class' => 'icon'));

            $linkhtml   = html_writer::link($fileurl, $image . substr($filepath, 1) . $filename);
            $linktxt    = "$filename [$fileurl]";

            if ($format == 'html') {
                if (file_mimetype_in_typegroup($type, 'web_image')) {
                    $preview     = html_writer::empty_tag('img', array('src' => $embedurl, 'alt' => '', 'class' => 'preview'));
                    $preview     = html_writer::tag('a', $preview, array('href' => $fileurl));
                    $outputimgs .= $this->output->container($preview);

                } else {
                    $outputfiles .= html_writer::tag('li', $linkhtml, array('class' => $type));
                }

            } else if ($format == 'text') {
                $outputfiles .= $linktxt . PHP_EOL;
            }

            if (!empty($CFG->enableplagiarism)) {
                require_once($CFG->libdir.'/plagiarismlib.php');
                $outputfiles .= plagiarism_get_links(array('userid' => $file->get_userid(),
                    'file' => $file,
                    'cmid' => $this->page->cm->id,
                    'course' => $this->page->course->id));
            }
        }

        if ($format == 'html') {
            if ($outputimgs) {
                $outputimgs = $this->output->container($outputimgs, 'images');
            }

            if ($outputfiles) {
                $outputfiles = html_writer::tag('ul', $outputfiles, array('class' => 'files'));
            }

            return $this->output->container($outputimgs . $outputfiles, 'attachments');

        } else {
            return $outputfiles;
        }
    }

    /**
     * Renders the tasks for the single phase in the user plan
     *
     * @param stdClass $tasks
     * @return string html code
     */
    protected function helper_user_plan_tasks(array $tasks) {
        $out = '';
        foreach ($tasks as $taskcode => $task) {
            $classes = '';
            $accessibilitytext = '';
            $icon = null;
            if ($task->completed === true) {
                $classes .= ' completed';
                $accessibilitytext .= get_string('taskdone', 'workshop') . ' ';
            } else if ($task->completed === false) {
                $classes .= ' fail';
                $accessibilitytext .= get_string('taskfail', 'workshop') . ' ';
            } else if ($task->completed === 'info') {
                $classes .= ' info';
                $accessibilitytext .= get_string('taskinfo', 'workshop') . ' ';
            } else {
                $accessibilitytext .= get_string('tasktodo', 'workshop') . ' ';
            }
            if (is_null($task->link)) {
                $title = html_writer::tag('span', $accessibilitytext, array('class' => 'accesshide'));
                $title .= $task->title;
            } else {
                $title = html_writer::tag('span', $accessibilitytext, array('class' => 'accesshide'));
                $title .= html_writer::link($task->link, $task->title);
            }
            $title = $this->output->container($title, 'title');
            $details = $this->output->container($task->details, 'details');
            $out .= html_writer::tag('li', $title . $details, array('class' => $classes));
        }
        if ($out) {
            $out = html_writer::tag('ul', $out, array('class' => 'tasks'));
        }
        return $out;
    }

    /**
     * Renders a text with icons to sort by the given column
     *
     * This is intended for table headings.
     *
     * @param string $text    The heading text
     * @param string $sortid  The column id used for sorting
     * @param string $sortby  Currently sorted by (column id)
     * @param string $sorthow Currently sorted how (ASC|DESC)
     *
     * @return string
     */
    protected function helper_sortable_heading($text, $sortid=null, $sortby=null, $sorthow=null) {

        $out = html_writer::tag('span', $text, array('class'=>'text'));

        if (!is_null($sortid)) {
            if ($sortby !== $sortid or $sorthow !== 'ASC') {
                $url = new moodle_url($this->page->url);
                $url->params(array('sortby' => $sortid, 'sorthow' => 'ASC'));
                $out .= $this->output->action_icon($url, new pix_icon('t/sort_asc', get_string('sortasc', 'workshop')),
                    null, array('class' => 'iconsort sort asc'));
            }
            if ($sortby !== $sortid or $sorthow !== 'DESC') {
                $url = new moodle_url($this->page->url);
                $url->params(array('sortby' => $sortid, 'sorthow' => 'DESC'));
                $out .= $this->output->action_icon($url, new pix_icon('t/sort_desc', get_string('sortdesc', 'workshop')),
                    null, array('class' => 'iconsort sort desc'));
            }
        }
        return $out;
}

    /**
     * @param stdClass $participant
     * @param array $userinfo
     * @return string
     */
    protected function helper_grading_report_participant(stdclass $participant, array $userinfo) {
        $userid = $participant->userid;
        $out  = $this->output->user_picture($userinfo[$userid], array('courseid' => $this->page->course->id, 'size' => 35));
        $out .= html_writer::tag('span', fullname($userinfo[$userid]));

        return $out;
    }

    /**
     * @param stdClass $participant
     * @return string
     */
    protected function helper_grading_report_submission(stdclass $participant) {
        global $CFG;

        if (is_null($participant->submissionid)) {
            $out = $this->output->container(get_string('nosubmissionfound', 'workshop'), 'info');
        } else {
            $url = new moodle_url('/mod/workshop/submission.php',
                                  array('cmid' => $this->page->context->instanceid, 'id' => $participant->submissionid));
            $out = html_writer::link($url, format_string($participant->submissiontitle), array('class'=>'title'));

            $lastmodified = get_string('userdatemodified', 'workshop', userdate($participant->submissionmodified));
            $out .= html_writer::tag('div', $lastmodified, array('class' => 'lastmodified'));
        }

        return $out;
    }

    /**
     * @todo Highlight the nulls
     * @param stdClass|null $assessment
     * @param bool $shownames
     * @param string $separator between the grade and the reviewer/author
     * @return string
     */
    protected function helper_grading_report_assessment($assessment, $shownames, array $userinfo, $separator) {
        global $CFG;

        if (is_null($assessment)) {
            return get_string('nullgrade', 'workshop');
        }
        $a = new stdclass();
        $a->grade = is_null($assessment->grade) ? get_string('nullgrade', 'workshop') : $assessment->grade;
        $a->gradinggrade = is_null($assessment->gradinggrade) ? get_string('nullgrade', 'workshop') : $assessment->gradinggrade;
        $a->weight = $assessment->weight;
        // grrr the following logic should really be handled by a future language pack feature
        if (is_null($assessment->gradinggradeover)) {
            if ($a->weight == 1) {
                $grade = get_string('formatpeergrade', 'workshop', $a);
            } else {
                $grade = get_string('formatpeergradeweighted', 'workshop', $a);
            }
        } else {
            $a->gradinggradeover = $assessment->gradinggradeover;
            if ($a->weight == 1) {
                $grade = get_string('formatpeergradeover', 'workshop', $a);
            } else {
                $grade = get_string('formatpeergradeoverweighted', 'workshop', $a);
            }
        }
        $url = new moodle_url('/mod/workshop/assessment.php',
                              array('asid' => $assessment->assessmentid));
        $grade = html_writer::link($url, $grade, array('class'=>'grade'));

        if ($shownames) {
            $userid = $assessment->userid;
            $name   = $this->output->user_picture($userinfo[$userid], array('courseid' => $this->page->course->id, 'size' => 16));
            $name  .= html_writer::tag('span', fullname($userinfo[$userid]), array('class' => 'fullname'));
            $name   = $separator . html_writer::tag('span', $name, array('class' => 'user'));
        } else {
            $name   = '';
        }

        return $this->output->container($grade . $name, 'assessmentdetails');
    }

    /**
     * Formats the aggreagated grades
     */
    protected function helper_grading_report_grade($grade, $over=null) {
        $a = new stdclass();
        $a->grade = is_null($grade) ? get_string('nullgrade', 'workshop') : $grade;
        if (is_null($over)) {
            $text = get_string('formataggregatedgrade', 'workshop', $a);
        } else {
            $a->over = is_null($over) ? get_string('nullgrade', 'workshop') : $over;
            $text = get_string('formataggregatedgradeover', 'workshop', $a);
        }
        return $text;
    }

    ////////////////////////////////////////////////////////////////////////////
    // Static helpers
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Helper method dealing with the fact we can not just fetch the output of moodleforms
     *
     * @param moodleform $mform
     * @return string HTML
     */
    protected static function moodleform(moodleform $mform) {

        ob_start();
        $mform->display();
        $o = ob_get_contents();
        ob_end_clean();

        return $o;
    }

    /**
     * Helper function returning the n-th item of the array
     *
     * @param array $a
     * @param int   $n from 0 to m, where m is th number of items in the array
     * @return mixed the $n-th element of $a
     */
    protected static function array_nth(array $a, $n) {
        $keys = array_keys($a);
        if ($n < 0 or $n > count($keys) - 1) {
            return null;
        }
        $key = $keys[$n];
        return $a[$key];
    }

    /**
     * Tries to guess the fullname format set at the site
     *
     * @return string fl|lf
     */
    protected static function fullname_format() {
        $fake = new stdclass(); // fake user
        $fake->lastname = 'LLLL';
        $fake->firstname = 'FFFF';
        $fullname = get_string('fullnamedisplay', '', $fake);
        if (strpos($fullname, 'LLLL') < strpos($fullname, 'FFFF')) {
            return 'lf';
        } else {
            return 'fl';
        }
    }

    /**
     * Generates the action buttons.
     *
     * @param workshop $workshop The current workshop.
     * @param workshop_user_plan $userplan An individual workshop plan for the user.
     * @return string HTML to display.
     */
    public function render_action_buttons(workshop $workshop, workshop_user_plan $userplan): string {
        global $USER;
        $output = '';

        switch ($workshop->phase) {
            case workshop::PHASE_SUBMISSION:
                // Does the user have to assess examples before submitting their own work?
                $examplesmust = ($workshop->useexamples && $workshop->examplesmode == workshop::EXAMPLES_BEFORE_SUBMISSION);

                // Is the assessment of example submissions considered finished?
                $examplesdone = has_capability('mod/workshop:manageexamples', $workshop->context);

                if ($workshop->assessing_examples_allowed() && has_capability('mod/workshop:submit', $workshop->context) &&
                    !has_capability('mod/workshop:manageexamples', $workshop->context)) {
                    $examples = $userplan->get_examples();
                    $left = 0;
                    // Make sure the current user has all examples allocated.
                    foreach ($examples as $exampleid => $example) {
                        if (is_null($example->grade)) {
                            $left++;
                            break;
                        }
                    }
                    if ($left > 0 && $workshop->examplesmode != workshop::EXAMPLES_VOLUNTARY) {
                        $examplesdone = false;
                    } else {
                        $examplesdone = true;
                    }
                }

                if (has_capability('mod/workshop:submit', $this->page->context) && (!$examplesmust || $examplesdone)) {
                    if (!$workshop->get_submission_by_author($USER->id)) {
                        $btnurl = new moodle_url($workshop->submission_url(), ['edit' => 'on']);
                        $btntxt = get_string('createsubmission', 'workshop');
                        $output .= $this->single_button($btnurl, $btntxt, 'get', ['type' => single_button::BUTTON_PRIMARY]);
                    }
                }
                break;

            case workshop::PHASE_ASSESSMENT:
                if (has_capability('mod/workshop:submit', $this->page->context)) {
                    if (!$workshop->get_submission_by_author($USER->id)) {
                        if ($workshop->creating_submission_allowed($USER->id)) {
                            $btnurl = new moodle_url($workshop->submission_url(), ['edit' => 'on']);
                            $btntxt = get_string('createsubmission', 'workshop');
                            $output .= $this->single_button($btnurl, $btntxt, 'get', ['type' => single_button::BUTTON_PRIMARY]);
                        }
                    }
                }
        }

        return $output;
    }

    /**
     * Generates the view page.
     *
     * @param workshop $workshop The current workshop.
     * @param workshop_user_plan $userplan An individual workshop plan for the user.
     * @param string $currentphasetitle The current phase title.
     * @param int $page The current page (for the pagination).
     * @param string $sortby Lastname|firstname|submissiontitle|submissiongrade|gradinggrade.
     * @param string $sorthow ASC|DESC.
     * @return string HTML to display.
     */
    public function view_page(workshop $workshop, workshop_user_plan $userplan, string $currentphasetitle,
            int $page, string $sortby, string $sorthow): string {
        $output = '';

        $output .= $this->render_action_buttons($workshop, $userplan);
        $output .= $this->heading(format_string($currentphasetitle), 3, null, 'mod_workshop-userplanheading');
        $output .= $this->render($userplan);
        $output .= $this->view_submissions_report($workshop, $userplan, $page, $sortby, $sorthow);

        return $output;
    }

    /**
     * Generates the submission report.
     *
     * @param workshop $workshop The current workshop.
     * @param workshop_user_plan $userplan An individual workshop plan for the user.
     * @param int $page The current page (for the pagination).
     * @param string $sortby Lastname|firstname|submissiontitle|submissiongrade|gradinggrade.
     * @param string $sorthow ASC|DESC.
     * @return string HTML to display.
     */
    public function view_submissions_report(workshop $workshop, workshop_user_plan $userplan,
            int $page, string $sortby, string $sorthow): string {
        global $USER;
        $output = '';

        switch ($workshop->phase) {
            case workshop::PHASE_SETUP:
                if (trim($workshop->intro)) {
                    $output .= print_collapsible_region_start('', 'workshop-viewlet-intro', get_string('introduction', 'workshop'),
                        'workshop-viewlet-intro-collapsed', false, true);
                    $output .= $this->box(format_module_intro('workshop', $workshop, $workshop->cm->id), 'generalbox');
                    $output .= print_collapsible_region_end(true);
                }
                if ($workshop->useexamples && has_capability('mod/workshop:manageexamples', $this->page->context)) {
                    $output .= print_collapsible_region_start('', 'workshop-viewlet-allexamples',
                        get_string('examplesubmissions', 'workshop'), 'workshop-viewlet-allexamples-collapsed', false, true);
                    $output .= $this->box_start('generalbox examples');
                    if ($workshop->grading_strategy_instance()->form_ready()) {
                        if (!$examples = $workshop->get_examples_for_manager()) {
                            $output .= $this->container(get_string('noexamples', 'workshop'), 'noexamples');
                        }
                        foreach ($examples as $example) {
                            $summary = $workshop->prepare_example_summary($example);
                            $summary->editable = true;
                            $output .= $this->render($summary);
                        }
                        $aurl = new moodle_url($workshop->exsubmission_url(0), ['edit' => 'on']);
                        $output .= $this->single_button($aurl, get_string('exampleadd', 'workshop'), 'get');
                    } else {
                        $output .= $this->container(get_string('noexamplesformready', 'workshop'));
                    }
                    $output .= $this->box_end();
                    $output .= print_collapsible_region_end(true);
                }
                break;
            case workshop::PHASE_SUBMISSION:
                $examplesmust = ($workshop->useexamples && $workshop->examplesmode == workshop::EXAMPLES_BEFORE_SUBMISSION);
                $examplesdone = has_capability('mod/workshop:manageexamples', $workshop->context);
                if (trim($workshop->instructauthors)) {
                    $instructions = file_rewrite_pluginfile_urls($workshop->instructauthors,
                        'pluginfile.php', $this->page->context->id,
                        'mod_workshop', 'instructauthors', null, workshop::instruction_editors_options($this->page->context));
                    $output .= print_collapsible_region_start('', 'workshop-viewlet-instructauthors',
                        get_string('instructauthors', 'workshop'),
                        'workshop-viewlet-instructauthors-collapsed', false, true);
                    $output .= $this->box(format_text($instructions, $workshop->instructauthorsformat, ['overflowdiv' => true]),
                        ['generalbox', 'instructions']);
                    $output .= print_collapsible_region_end(true);
                }

                if ($workshop->assessing_examples_allowed()
                    && has_capability('mod/workshop:submit', $workshop->context)
                    && !has_capability('mod/workshop:manageexamples', $workshop->context)) {
                    $examples = $userplan->get_examples();
                    $total = count($examples);
                    $output .= print_collapsible_region_start('', 'workshop-viewlet-examples',
                        get_string('exampleassessments', 'workshop'),
                        'workshop-viewlet-examples-collapsed', $examplesdone, true);
                    $output .= $this->box_start('generalbox exampleassessments');
                    if ($total == 0) {
                        $output .= $this->heading(get_string('noexamples', 'workshop'), 3);
                    } else {
                        foreach ($examples as $example) {
                            $summary = $workshop->prepare_example_summary($example);
                            $output .= $this->render($summary);
                        }
                    }
                    $output .= $this->box_end();
                    $output .= print_collapsible_region_end(true);
                }

                if (has_capability('mod/workshop:submit', $this->page->context) && (!$examplesmust || $examplesdone)) {
                    $output .= print_collapsible_region_start('', 'workshop-viewlet-ownsubmission',
                        get_string('yoursubmission', 'workshop'),
                        'workshop-viewlet-ownsubmission-collapsed', false, true);
                    $output .= $this->box_start('generalbox ownsubmission');
                    if ($submission = $workshop->get_submission_by_author($USER->id)) {
                        $output .= $this->render($workshop->prepare_submission_summary($submission, true));
                    } else {
                        $output .= $this->container(get_string('noyoursubmission', 'workshop'));
                    }

                    $output .= $this->box_end();
                    $output .= print_collapsible_region_end(true);
                }

                if (has_capability('mod/workshop:viewallsubmissions', $this->page->context)) {
                    $groupmode = groups_get_activity_groupmode($workshop->cm);
                    $groupid = groups_get_activity_group($workshop->cm, true);

                    if ($groupmode == SEPARATEGROUPS && !has_capability('moodle/site:accessallgroups', $workshop->context)) {
                        $allowedgroups = groups_get_activity_allowed_groups($workshop->cm);
                        if (empty($allowedgroups)) {
                            $output .= $this->container(get_string('groupnoallowed', 'mod_workshop'), 'groupwidget error');
                            break;
                        }
                        if (!in_array($groupid, array_keys($allowedgroups))) {
                            $output .= $this->container(get_string('groupnotamember', 'core_group'), 'groupwidget error');
                            break;
                        }
                    }

                    $output .= print_collapsible_region_start('', 'workshop-viewlet-allsubmissions',
                        get_string('submissionsreport', 'workshop'),
                        'workshop-viewlet-allsubmissions-collapsed', false, true);

                    $perpage = get_user_preferences('workshop_perpage', 10);
                    $data = $workshop->prepare_grading_report_data($USER->id, $groupid, $page, $perpage, $sortby, $sorthow);
                    if ($data) {
                        $countparticipants = $workshop->count_participants();
                        $countsubmissions = $workshop->count_submissions(array_keys($data->grades), $groupid);
                        $a = new stdClass();
                        $a->submitted = $countsubmissions;
                        $a->notsubmitted = $data->totalcount - $countsubmissions;

                        $output .= html_writer::tag('div', get_string('submittednotsubmitted', 'workshop', $a));

                        $output .= $this->container(
                            groups_print_activity_menu($workshop->cm, $this->page->url, true), 'groupwidget');

                        // Prepare the paging bar.
                        $baseurl = new moodle_url($this->page->url, ['sortby' => $sortby, 'sorthow' => $sorthow]);
                        $pagingbar = new paging_bar($data->totalcount, $page, $perpage, $baseurl, 'page');

                        // Populate the display options for the submissions report.
                        $reportopts = new stdclass();
                        $reportopts->showauthornames = has_capability('mod/workshop:viewauthornames', $workshop->context);
                        $reportopts->showreviewernames = has_capability('mod/workshop:viewreviewernames', $workshop->context);
                        $reportopts->sortby = $sortby;
                        $reportopts->sorthow = $sorthow;
                        $reportopts->showsubmissiongrade = false;
                        $reportopts->showgradinggrade = false;
                        $reportopts->workshopphase = $workshop->phase;
                        $output .= $this->initials_bars($workshop, $baseurl);
                        $output .= $this->render($pagingbar);
                        $output .= $this->render(new workshop_grading_report($data, $reportopts));
                        $output .= $this->render($pagingbar);
                        $output .= $this->perpage_selector($perpage);
                    } else {
                        $output .= html_writer::tag('div', get_string('nothingfound', 'workshop'), ['class' => 'nothingfound']);
                    }
                    $output .= print_collapsible_region_end(true);
                }
                break;

            case workshop::PHASE_ASSESSMENT:

                $ownsubmissionexists = null;
                if (has_capability('mod/workshop:submit', $this->page->context)) {
                    if ($ownsubmission = $workshop->get_submission_by_author($USER->id)) {
                        $output .= print_collapsible_region_start('', 'workshop-viewlet-ownsubmission',
                            get_string('yoursubmission', 'workshop'),
                            'workshop-viewlet-ownsubmission-collapsed', true, true);
                        $output .= $this->box_start('generalbox ownsubmission');
                        $output .= $this->render($workshop->prepare_submission_summary($ownsubmission, true));
                        $ownsubmissionexists = true;
                    } else {
                        $output .= print_collapsible_region_start('', 'workshop-viewlet-ownsubmission',
                            get_string('yoursubmission', 'workshop'),
                            'workshop-viewlet-ownsubmission-collapsed', false, true);
                        $output .= $this->box_start('generalbox ownsubmission');
                        $output .= $this->container(get_string('noyoursubmission', 'workshop'));
                        $ownsubmissionexists = false;
                    }

                    $output .= $this->box_end();
                    $output .= print_collapsible_region_end(true);
                }

                if (has_capability('mod/workshop:viewallassessments', $this->page->context)) {
                    $perpage = get_user_preferences('workshop_perpage', 10);
                    $groupid = groups_get_activity_group($workshop->cm, true);
                    $data = $workshop->prepare_grading_report_data($USER->id, $groupid, $page, $perpage, $sortby, $sorthow);
                    if ($data) {
                        $showauthornames = has_capability('mod/workshop:viewauthornames', $workshop->context);
                        $showreviewernames = has_capability('mod/workshop:viewreviewernames', $workshop->context);

                        // Prepare paging bar.
                        $baseurl = new moodle_url($this->page->url, ['sortby' => $sortby, 'sorthow' => $sorthow]);
                        $pagingbar = new paging_bar($data->totalcount, $page, $perpage, $baseurl, 'page');

                        // Grading report display options.
                        $reportopts = new stdclass();
                        $reportopts->showauthornames = $showauthornames;
                        $reportopts->showreviewernames = $showreviewernames;
                        $reportopts->sortby = $sortby;
                        $reportopts->sorthow = $sorthow;
                        $reportopts->showsubmissiongrade = false;
                        $reportopts->showgradinggrade = false;
                        $reportopts->workshopphase = $workshop->phase;

                        $output .= print_collapsible_region_start('', 'workshop-viewlet-gradereport',
                            get_string('gradesreport', 'workshop'),
                            'workshop-viewlet-gradereport-collapsed', false, true);
                        $output .= $this->box_start('generalbox gradesreport');
                        $output .= $this->container(groups_print_activity_menu($workshop->cm,
                            $this->page->url, true), 'groupwidget');
                        $output .= $this->initials_bars($workshop, $baseurl);
                        $output .= $this->render($pagingbar);
                        $output .= $this->render(new workshop_grading_report($data, $reportopts));
                        $output .= $this->render($pagingbar);
                        $output .= $this->perpage_selector($perpage);
                        $output .= $this->box_end();
                        $output .= print_collapsible_region_end(true);
                    }
                }
                if (trim($workshop->instructreviewers)) {
                    $instructions = file_rewrite_pluginfile_urls($workshop->instructreviewers,
                        'pluginfile.php', $this->page->context->id,
                        'mod_workshop', 'instructreviewers', null, workshop::instruction_editors_options($this->page->context));
                    $output .= print_collapsible_region_start('', 'workshop-viewlet-instructreviewers',
                        get_string('instructreviewers', 'workshop'),
                        'workshop-viewlet-instructreviewers-collapsed', false, true);
                    $output .= $this->box(format_text($instructions, $workshop->instructreviewersformat,
                        ['overflowdiv' => true]), ['generalbox', 'instructions']);
                    $output .= print_collapsible_region_end(true);
                }

                // Does the user have to assess examples before assessing other's work?
                $examplesmust = ($workshop->useexamples && $workshop->examplesmode == workshop::EXAMPLES_BEFORE_ASSESSMENT);

                // Is the assessment of example submissions considered finished?
                $examplesdone = has_capability('mod/workshop:manageexamples', $workshop->context);

                // Can the examples be assessed?
                $examplesavailable = true;

                if (!$examplesdone && $examplesmust && ($ownsubmissionexists === false)) {
                    $output .= print_collapsible_region_start('', 'workshop-viewlet-examplesfail',
                        get_string('exampleassessments', 'workshop'),
                        'workshop-viewlet-examplesfail-collapsed', false, true);
                    $output .= $this->box(get_string('exampleneedsubmission', 'workshop'));
                    $output .= print_collapsible_region_end(true);
                    $examplesavailable = false;
                }

                if ($workshop->assessing_examples_allowed()
                    && has_capability('mod/workshop:submit', $workshop->context)
                    && !has_capability('mod/workshop:manageexamples', $workshop->context)
                    && $examplesavailable) {
                    $examples = $userplan->get_examples();
                    $total = count($examples);
                    $left = 0;
                    // Make sure the current user has all examples allocated.
                    foreach ($examples as $exampleid => $example) {
                        if (is_null($example->assessmentid)) {
                            $examples[$exampleid]->assessmentid = $workshop->add_allocation($example, $USER->id, 0);
                        }
                        if (is_null($example->grade)) {
                            $left++;
                        }
                    }
                    if ($left > 0 && $workshop->examplesmode != workshop::EXAMPLES_VOLUNTARY) {
                        $examplesdone = false;
                    } else {
                        $examplesdone = true;
                    }
                    $output .= print_collapsible_region_start('', 'workshop-viewlet-examples',
                        get_string('exampleassessments', 'workshop'),
                        'workshop-viewlet-examples-collapsed', $examplesdone, true);
                    $output .= $this->box_start('generalbox exampleassessments');
                    if ($total == 0) {
                        $output .= $this->heading(get_string('noexamples', 'workshop'), 3);
                    } else {
                        foreach ($examples as $example) {
                            $summary = $workshop->prepare_example_summary($example);
                            $output .= $this->render($summary);
                        }
                    }
                    $output .= $this->box_end();
                    $output .= print_collapsible_region_end(true);
                }
                if (!$examplesmust || $examplesdone) {
                    $output .= print_collapsible_region_start('', 'workshop-viewlet-assignedassessments',
                        get_string('assignedassessments', 'workshop'),
                        'workshop-viewlet-assignedassessments-collapsed', false, true);
                    if (!$assessments = $workshop->get_assessments_by_reviewer($USER->id)) {
                        $output .= $this->box_start('generalbox assessment-none');
                        $output .= $this->notification(get_string('assignedassessmentsnone', 'workshop'));
                        $output .= $this->box_end();
                    } else {
                        $shownames = has_capability('mod/workshop:viewauthornames', $this->page->context);
                        foreach ($assessments as $assessment) {
                            $submission = new stdClass();
                            $submission->id = $assessment->submissionid;
                            $submission->title = $assessment->submissiontitle;
                            $submission->timecreated = $assessment->submissioncreated;
                            $submission->timemodified = $assessment->submissionmodified;
                            $userpicturefields = explode(',', implode(',', \core_user\fields::get_picture_fields()));
                            foreach ($userpicturefields as $userpicturefield) {
                                $prefixedusernamefield = 'author' . $userpicturefield;
                                $submission->$prefixedusernamefield = $assessment->$prefixedusernamefield;
                            }

                            // Transform the submission object into renderable component.
                            $submission = $workshop->prepare_submission_summary($submission, $shownames);

                            if (is_null($assessment->grade)) {
                                $submission->status = 'notgraded';
                                $class = ' notgraded';
                                $buttontext = get_string('assess', 'workshop');
                            } else {
                                $submission->status = 'graded';
                                $class = ' graded';
                                $buttontext = get_string('reassess', 'workshop');
                            }

                            $output .= $this->box_start('generalbox assessment-summary' . $class);
                            $output .= $this->render($submission);
                            $aurl = $workshop->assess_url($assessment->id);
                            $output .= $this->single_button($aurl, $buttontext, 'get');
                            $output .= $this->box_end();
                        }
                    }
                    $output .= print_collapsible_region_end(true);
                }
                break;
            case workshop::PHASE_EVALUATION:
                if (has_capability('mod/workshop:viewallassessments', $this->page->context)) {
                    $perpage = get_user_preferences('workshop_perpage', 10);
                    $groupid = groups_get_activity_group($workshop->cm, true);
                    $data = $workshop->prepare_grading_report_data($USER->id, $groupid, $page, $perpage, $sortby, $sorthow);
                    if ($data) {
                        $showauthornames = has_capability('mod/workshop:viewauthornames', $workshop->context);
                        $showreviewernames = has_capability('mod/workshop:viewreviewernames', $workshop->context);

                        if (has_capability('mod/workshop:overridegrades', $this->page->context)) {
                            // Print a drop-down selector to change the current evaluation method.
                            $selector = new single_select($this->page->url, 'eval', workshop::available_evaluators_list(),
                                $workshop->evaluation, false, 'evaluationmethodchooser');
                            $selector->set_label(get_string('evaluationmethod', 'mod_workshop'));
                            $selector->set_help_icon('evaluationmethod', 'mod_workshop');
                            $selector->method = 'post';
                            $output .= $this->render($selector);
                            // Load the grading evaluator.
                            $evaluator = $workshop->grading_evaluation_instance();
                            $form = $evaluator->get_settings_form(new moodle_url($workshop->aggregate_url(),
                                compact('sortby', 'sorthow', 'page')));
                            $form->display();
                        }

                        // Prepare paging bar.
                        $baseurl = new moodle_url($this->page->url, ['sortby' => $sortby, 'sorthow' => $sorthow]);
                        $pagingbar = new paging_bar($data->totalcount, $page, $perpage, $baseurl, 'page');

                        // Grading report display options.
                        $reportopts = new stdclass();
                        $reportopts->showauthornames = $showauthornames;
                        $reportopts->showreviewernames = $showreviewernames;
                        $reportopts->sortby = $sortby;
                        $reportopts->sorthow = $sorthow;
                        $reportopts->showsubmissiongrade = true;
                        $reportopts->showgradinggrade = true;
                        $reportopts->workshopphase = $workshop->phase;

                        $output .= print_collapsible_region_start('', 'workshop-viewlet-gradereport',
                            get_string('gradesreport', 'workshop'),
                            'workshop-viewlet-gradereport-collapsed', false, true);
                        $output .= $this->box_start('generalbox gradesreport');
                        $output .= $this->container(groups_print_activity_menu($workshop->cm,
                            $this->page->url, true), 'groupwidget');
                        $output .= $this->initials_bars($workshop, $baseurl);
                        $output .= $this->render($pagingbar);
                        $output .= $this->render(new workshop_grading_report($data, $reportopts));
                        $output .= $this->render($pagingbar);
                        $output .= $this->perpage_selector($perpage);
                        $output .= $this->box_end();
                        $output .= print_collapsible_region_end(true);
                    }
                }
                if (has_capability('mod/workshop:overridegrades', $workshop->context)) {
                    $output .= print_collapsible_region_start('', 'workshop-viewlet-cleargrades', get_string('toolbox', 'workshop'),
                        'workshop-viewlet-cleargrades-collapsed', true, true);
                    $output .= $this->box_start('generalbox toolbox');

                    // Clear aggregated grades.
                    $url = new moodle_url($workshop->toolbox_url('clearaggregatedgrades'));
                    $btn = new single_button($url, get_string('clearaggregatedgrades', 'workshop'), 'post');
                    $btn->add_confirm_action(get_string('clearaggregatedgradesconfirm', 'workshop'));
                    $output .= $this->container_start('toolboxaction');
                    $output .= $this->render($btn);
                    $output .= $this->help_icon('clearaggregatedgrades', 'workshop');
                    $output .= $this->container_end();
                    // Clear assessments.
                    $url = new moodle_url($workshop->toolbox_url('clearassessments'));
                    $btn = new single_button($url, get_string('clearassessments', 'workshop'), 'post');
                    $btn->add_confirm_action(get_string('clearassessmentsconfirm', 'workshop'));
                    $output .= $this->container_start('toolboxaction');
                    $output .= $this->render($btn);
                    $output .= $this->help_icon('clearassessments', 'workshop');

                    $output .= $this->output->pix_icon('i/risk_dataloss', get_string('riskdatalossshort', 'admin'));
                    $output .= $this->container_end();

                    $output .= $this->box_end();
                    $output .= print_collapsible_region_end(true);
                }
                if (has_capability('mod/workshop:submit', $this->page->context)) {
                    $output .= print_collapsible_region_start('', 'workshop-viewlet-ownsubmission',
                        get_string('yoursubmission', 'workshop'),
                        'workshop-viewlet-ownsubmission-collapsed', false, true);
                    $output .= $this->box_start('generalbox ownsubmission');
                    if ($submission = $workshop->get_submission_by_author($USER->id)) {
                        $output .= $this->render($workshop->prepare_submission_summary($submission, true));
                    } else {
                        $output .= $this->container(get_string('noyoursubmission', 'workshop'));
                    }
                    $output .= $this->box_end();
                    $output .= print_collapsible_region_end(true);
                }
                if ($assessments = $workshop->get_assessments_by_reviewer($USER->id)) {
                    $output .= print_collapsible_region_start('', 'workshop-viewlet-assignedassessments',
                        get_string('assignedassessments', 'workshop'),
                        'workshop-viewlet-assignedassessments-collapsed', false, true);
                    $shownames = has_capability('mod/workshop:viewauthornames', $this->page->context);
                    foreach ($assessments as $assessment) {
                        $submission = new stdclass();
                        $submission->id = $assessment->submissionid;
                        $submission->title = $assessment->submissiontitle;
                        $submission->timecreated = $assessment->submissioncreated;
                        $submission->timemodified = $assessment->submissionmodified;
                        $userpicturefields = explode(',', implode(',', \core_user\fields::get_picture_fields()));
                        foreach ($userpicturefields as $userpicturefield) {
                            $prefixedusernamefield = 'author' . $userpicturefield;
                            $submission->$prefixedusernamefield = $assessment->$prefixedusernamefield;
                        }

                        if (is_null($assessment->grade)) {
                            $class = ' notgraded';
                            $submission->status = 'notgraded';
                            $buttontext = get_string('assess', 'workshop');
                        } else {
                            $class = ' graded';
                            $submission->status = 'graded';
                            $buttontext = get_string('reassess', 'workshop');
                        }
                        $output .= $this->box_start('generalbox assessment-summary' . $class);
                        $output .= $this->render($workshop->prepare_submission_summary($submission, $shownames));
                        $output .= $this->box_end();
                    }
                    $output .= print_collapsible_region_end(true);
                }
                break;
            case workshop::PHASE_CLOSED:
                if (trim($workshop->conclusion)) {
                    $conclusion = file_rewrite_pluginfile_urls($workshop->conclusion, 'pluginfile.php', $workshop->context->id,
                        'mod_workshop', 'conclusion', null, workshop::instruction_editors_options($workshop->context));
                    $output .= print_collapsible_region_start('', 'workshop-viewlet-conclusion',
                        get_string('conclusion', 'workshop'),
                        'workshop-viewlet-conclusion-collapsed', false, true);
                    $output .= $this->box(format_text($conclusion, $workshop->conclusionformat, ['overflowdiv' => true]),
                        ['generalbox', 'conclusion']);
                    $output .= print_collapsible_region_end(true);
                }
                $finalgrades = $workshop->get_gradebook_grades($USER->id);
                if (!empty($finalgrades)) {
                    $output .= print_collapsible_region_start('', 'workshop-viewlet-yourgrades',
                        get_string('yourgrades', 'workshop'),
                        'workshop-viewlet-yourgrades-collapsed', false, true);
                    $output .= $this->box_start('generalbox grades-yourgrades');
                    $output .= $this->render($finalgrades);
                    $output .= $this->box_end();
                    $output .= print_collapsible_region_end(true);
                }
                if (has_capability('mod/workshop:viewallassessments', $this->page->context)) {
                    $perpage = get_user_preferences('workshop_perpage', 10);
                    $groupid = groups_get_activity_group($workshop->cm, true);
                    $data = $workshop->prepare_grading_report_data($USER->id, $groupid, $page, $perpage, $sortby, $sorthow);
                    if ($data) {
                        $showauthornames = has_capability('mod/workshop:viewauthornames', $workshop->context);
                        $showreviewernames = has_capability('mod/workshop:viewreviewernames', $workshop->context);

                        // Prepare paging bar.
                        $baseurl = new moodle_url($this->page->url, ['sortby' => $sortby, 'sorthow' => $sorthow]);
                        $pagingbar = new paging_bar($data->totalcount, $page, $perpage, $baseurl, 'page');

                        // Grading report display options.
                        $reportopts = new stdclass();
                        $reportopts->showauthornames = $showauthornames;
                        $reportopts->showreviewernames = $showreviewernames;
                        $reportopts->sortby = $sortby;
                        $reportopts->sorthow = $sorthow;
                        $reportopts->showsubmissiongrade = true;
                        $reportopts->showgradinggrade = true;
                        $reportopts->workshopphase = $workshop->phase;

                        $output .= print_collapsible_region_start('', 'workshop-viewlet-gradereport',
                            get_string('gradesreport', 'workshop'),
                            'workshop-viewlet-gradereport-collapsed', false, true);
                        $output .= $this->box_start('generalbox gradesreport');
                        $output .= $this->container(groups_print_activity_menu($workshop->cm,
                            $this->page->url, true), 'groupwidget');
                        $output .= $this->initials_bars($workshop, $baseurl);
                        $output .= $this->render($pagingbar);
                        $output .= $this->render(new workshop_grading_report($data, $reportopts));
                        $output .= $this->render($pagingbar);
                        $output .= $this->perpage_selector($perpage);
                        $output .= $this->box_end();
                        $output .= print_collapsible_region_end(true);
                    }
                }
                if (has_capability('mod/workshop:submit', $this->page->context)) {
                    $output .= print_collapsible_region_start('', 'workshop-viewlet-ownsubmission',
                        get_string('yoursubmissionwithassessments', 'workshop'),
                        'workshop-viewlet-ownsubmission-collapsed', false, true);
                    $output .= $this->box_start('generalbox ownsubmission');
                    if ($submission = $workshop->get_submission_by_author($USER->id)) {
                        $output .= $this->render($workshop->prepare_submission_summary($submission, true));
                    } else {
                        $output .= $this->container(get_string('noyoursubmission', 'workshop'));
                    }
                    $output .= $this->box_end();

                    if (!empty($submission->gradeoverby) && strlen(trim($submission->feedbackauthor)) > 0) {
                        $output .= $this->render(new workshop_feedback_author($submission));
                    }

                    $output .= print_collapsible_region_end(true);
                }
                if (has_capability('mod/workshop:viewpublishedsubmissions', $workshop->context)) {
                    $shownames = has_capability('mod/workshop:viewauthorpublished', $workshop->context);
                    if ($submissions = $workshop->get_published_submissions()) {
                        $output .= print_collapsible_region_start('', 'workshop-viewlet-publicsubmissions',
                            get_string('publishedsubmissions', 'workshop'),
                            'workshop-viewlet-publicsubmissions-collapsed', false, true);
                        foreach ($submissions as $submission) {
                            $output .= $this->box_start('generalbox submission-summary');
                            $output .= $this->render($workshop->prepare_submission_summary($submission, $shownames));
                            $output .= $this->box_end();
                        }
                        $output .= print_collapsible_region_end();
                    }
                }
                if ($assessments = $workshop->get_assessments_by_reviewer($USER->id)) {
                    $output .= print_collapsible_region_start('', 'workshop-viewlet-assignedassessments',
                        get_string('assignedassessments', 'workshop'),
                        'workshop-viewlet-assignedassessments-collapsed', false, true);
                    $shownames = has_capability('mod/workshop:viewauthornames', $this->page->context);
                    foreach ($assessments as $assessment) {
                        $submission = new stdclass();
                        $submission->id = $assessment->submissionid;
                        $submission->title = $assessment->submissiontitle;
                        $submission->timecreated = $assessment->submissioncreated;
                        $submission->timemodified = $assessment->submissionmodified;
                        $userpicturefields = explode(',', implode(',', \core_user\fields::get_picture_fields()));
                        foreach ($userpicturefields as $userpicturefield) {
                            $prefixedusernamefield = 'author' . $userpicturefield;
                            $submission->$prefixedusernamefield = $assessment->$prefixedusernamefield;
                        }

                        if (is_null($assessment->grade)) {
                            $class = ' notgraded';
                            $submission->status = 'notgraded';
                            $buttontext = get_string('assess', 'workshop');
                        } else {
                            $class = ' graded';
                            $submission->status = 'graded';
                            $buttontext = get_string('reassess', 'workshop');
                        }
                        $output .= $this->box_start('generalbox assessment-summary' . $class);
                        $output .= $this->render($workshop->prepare_submission_summary($submission, $shownames));
                        $output .= $this->box_end();

                        if (!empty($assessment->feedbackreviewer) && strlen(trim($assessment->feedbackreviewer)) > 0) {
                            $output .= $this->render(new workshop_feedback_reviewer($assessment));
                        }
                    }
                    $output .= print_collapsible_region_end(true);
                }
                break;
            default:
        }

        return $output;
    }
}
