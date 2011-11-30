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
 * @package    mod
 * @subpackage workshop
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
            $author             = new stdclass();
            $author->id         = $submission->authorid;
            $author->firstname  = $submission->authorfirstname;
            $author->lastname   = $submission->authorlastname;
            $author->picture    = $submission->authorpicture;
            $author->imagealt   = $submission->authorimagealt;
            $author->email      = $submission->authoremail;
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

        $content = format_text($submission->content, $submission->contentformat, array('overflowdiv'=>true));
        $content = file_rewrite_pluginfile_urls($content, 'pluginfile.php', $this->page->context->id,
                                                        'mod_workshop', 'submission_content', $submission->id);
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
            $author->id         = $summary->authorid;
            $author->firstname  = $summary->authorfirstname;
            $author->lastname   = $summary->authorlastname;
            $author->picture    = $summary->authorpicture;
            $author->imagealt   = $summary->authorimagealt;
            $author->email      = $summary->authoremail;
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
        $o .= $this->output->heading(format_string($example->title), 3, 'title');
        $o .= $this->output->container_end(); // end of header

        $content = format_text($example->content, $example->contentformat, array('overflowdiv'=>true));
        $content = file_rewrite_pluginfile_urls($content, 'pluginfile.php', $this->page->context->id,
                                                        'mod_workshop', 'submission_content', $example->id);
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
        $table = new html_table();
        $table->attributes['class'] = 'userplan';
        $table->head = array();
        $table->colclasses = array();
        $row = new html_table_row();
        $row->attributes['class'] = 'phasetasks';
        foreach ($plan->phases as $phasecode => $phase) {
            $title = html_writer::tag('span', $phase->title);
            $actions = '';
            foreach ($phase->actions as $action) {
                switch ($action->type) {
                case 'switchphase':
                    $actions .= $this->output->action_icon($action->url, new pix_icon('i/marker', get_string('switchphase', 'workshop')));
                    break;
                }
            }
            if (!empty($actions)) {
                $actions = $this->output->container($actions, 'actions');
            }
            $table->head[] = $this->output->container($title . $actions);
            $classes = 'phase' . $phasecode;
            if ($phase->active) {
                $classes .= ' active';
            } else {
                $classes .= ' nonactive';
            }
            $table->colclasses[] = $classes;
            $cell = new html_table_cell();
            $cell->text = $this->helper_user_plan_tasks($phase->tasks);
            $row->cells[] = $cell;
        }
        $table->data = array($row);

        return html_writer::table($table);
    }

    /**
     * Renders the result of the submissions allocation process
     *
     * @param workshop_allocation_init_result
     * @return string html to be echoed
     */
    protected function render_workshop_allocation_init_result(workshop_allocation_init_result $result) {

        // start with the message
        $o = $this->render($result->get_message());

        // display the details about the process if available
        $info = $result->get_info();
        if (is_array($info) and !empty($info)) {
            $o .= html_writer::start_tag('ul', array('class' => 'allocation-init-results'));
            foreach ($info as $message) {
                $parts  = explode('::', $message);
                $text   = array_pop($parts);
                $class  = implode(' ', $parts);
                if (in_array('debug', $parts) && !debugging('', DEBUG_DEVELOPER)) {
                    // do not display allocation debugging messages
                    continue;
                }
                $o .= html_writer::tag('li', $text, array('class' => $class)) . "\n";
            }
            $o .= html_writer::end_tag('ul');
        }

        $o .= $this->output->continue_button($result->get_continue_url());

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
            return '';
        }

        $table = new html_table();
        $table->attributes['class'] = 'grading-report';

        $sortbyfirstname = $this->helper_sortable_heading(get_string('firstname'), 'firstname', $options->sortby, $options->sorthow);
        $sortbylastname = $this->helper_sortable_heading(get_string('lastname'), 'lastname', $options->sortby, $options->sorthow);
        if (self::fullname_format() == 'lf') {
            $sortbyname = $sortbylastname . ' / ' . $sortbyfirstname;
        } else {
            $sortbyname = $sortbyfirstname . ' / ' . $sortbylastname;
        }

        $table->head = array();
        $table->head[] = $sortbyname;
        $table->head[] = $this->helper_sortable_heading(get_string('submission', 'workshop'), 'submissiontitle',
                $options->sortby, $options->sorthow);
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
                    get_string('assessmentform', 'workshop'), '', false, true);
            $o .= $this->output->container(self::moodleform($assessment->form), 'assessment-form');
            $o .= print_collapsible_region_end(true);
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
            $fileurl    = file_encode_url($CFG->wwwroot . '/pluginfile.php',
                                '/' . $ctx->id . '/mod_workshop/submission_attachment/' . $submissionid . $filepath . $filename, true);
            $type       = $file->get_mimetype();
            $type       = mimeinfo_from_type('type', $type);
            $image      = html_writer::empty_tag('img', array('src'=>$this->output->pix_url(file_mimetype_icon($type)), 'alt'=>$type, 'class'=>'icon'));

            $linkhtml   = html_writer::link($fileurl, $image) . substr($filepath, 1) . html_writer::link($fileurl, $filename);
            $linktxt    = "$filename [$fileurl]";

            if ($format == 'html') {
                if (in_array($type, array('image/gif', 'image/jpeg', 'image/png'))) {
                    $preview     = html_writer::empty_tag('img', array('src' => $fileurl, 'alt' => '', 'class' => 'preview'));
                    $preview     = html_writer::tag('a', $preview, array('href' => $fileurl));
                    $outputimgs .= $this->output->container($preview);

                } else {
                    $outputfiles .= html_writer::tag('li', $linkhtml, array('class' => $type));
                }

            } else if ($format == 'text') {
                $outputfiles .= $linktxt . PHP_EOL;
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
            $icon = null;
            if ($task->completed === true) {
                $classes .= ' completed';
            } elseif ($task->completed === false) {
                $classes .= ' fail';
            } elseif ($task->completed === 'info') {
                $classes .= ' info';
            }
            if (is_null($task->link)) {
                $title = $task->title;
            } else {
                $title = html_writer::link($task->link, $task->title);
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
        global $PAGE;

        $out = html_writer::tag('span', $text, array('class'=>'text'));

        if (!is_null($sortid)) {
            if ($sortby !== $sortid or $sorthow !== 'ASC') {
                $url = new moodle_url($PAGE->url);
                $url->params(array('sortby' => $sortid, 'sorthow' => 'ASC'));
                $out .= $this->output->action_icon($url, new pix_icon('t/up', get_string('sortasc', 'workshop')), null, array('class' => 'sort asc'));
            }
            if ($sortby !== $sortid or $sorthow !== 'DESC') {
                $url = new moodle_url($PAGE->url);
                $url->params(array('sortby' => $sortid, 'sorthow' => 'DESC'));
                $out .= $this->output->action_icon($url, new pix_icon('t/down', get_string('sortdesc', 'workshop')), null, array('class' => 'sort desc'));
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
}
