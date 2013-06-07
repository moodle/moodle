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
 * This file contains a renderer for the assignment class
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** Include locallib.php */
require_once($CFG->dirroot . '/mod/assign/locallib.php');


/**
 * A custom renderer class that extends the plugin_renderer_base and is used by the assign module.
 *
 * @package mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_renderer extends plugin_renderer_base {

    /**
     * rendering assignment files
     *
     * @param context $context
     * @param int $userid
     * @param string $filearea
     * @param string $component
     * @return string
     */
    public function assign_files(context $context, $userid, $filearea, $component) {
        return $this->render(new assign_files($context, $userid, $filearea, $component));
    }

    /**
     * rendering assignment files
     *
     * @param assign_files $tree
     * @return string
     */
    public function render_assign_files(assign_files $tree) {
        $this->htmlid = 'assign_files_tree_'.uniqid();
        $this->page->requires->js_init_call('M.mod_assign.init_tree', array(true, $this->htmlid));
        $html = '<div id="'.$this->htmlid.'">';
        $html .= $this->htmllize_tree($tree, $tree->dir);
        $html .= '</div>';

        if ($tree->portfolioform) {
            $html .= $tree->portfolioform;
        }
        return $html;
    }

    /**
     * Utility function to add a row of data to a table with 2 columns. Modified
     * the table param and does not return a value
     *
     * @param html_table $table The table to append the row of data to
     * @param string $first The first column text
     * @param string $second The second column text
     * @return void
     */
    private function add_table_row_tuple(html_table $table, $first, $second) {
        $row = new html_table_row();
        $cell1 = new html_table_cell($first);
        $cell2 = new html_table_cell($second);
        $row->cells = array($cell1, $cell2);
        $table->data[] = $row;
    }

    /**
     * Render a grading error notification
     * @param assign_quickgrading_result $result The result to render
     * @return string
     */
    public function render_assign_quickgrading_result(assign_quickgrading_result $result) {
        $url = new moodle_url('/mod/assign/view.php', array('id' => $result->coursemoduleid, 'action'=>'grading'));

        $o = '';
        $o .= $this->output->heading(get_string('quickgradingresult', 'assign'), 4);
        $o .= $this->output->notification($result->message);
        $o .= $this->output->continue_button($url);
        return $o;
    }

    /**
     * Render the generic form
     * @param assign_form $form The form to render
     * @return string
     */
    public function render_assign_form(assign_form $form) {
        $o = '';
        if ($form->jsinitfunction) {
            $this->page->requires->js_init_call($form->jsinitfunction, array());
        }
        $o .= $this->output->box_start('boxaligncenter ' . $form->classname);
        $o .= $this->moodleform($form->form);
        $o .= $this->output->box_end();
        return $o;
    }

    /**
     * Render the user summary
     *
     * @param assign_user_summary $summary The user summary to render
     * @return string
     */
    public function render_assign_user_summary(assign_user_summary $summary) {
        $o = '';

        if (!$summary->user) {
            return;
        }
        $o .= $this->output->container_start('usersummary');
        $o .= $this->output->box_start('boxaligncenter usersummarysection');
        $o .= $this->output->user_picture($summary->user);
        $o .= $this->output->spacer(array('width'=>30));
        $fullname = fullname($summary->user, $summary->viewfullnames);
        $extrainfo = array();
        foreach ($summary->extrauserfields as $extrafield) {
            $extrainfo[] = $summary->user->$extrafield;
        }
        if (count($extrainfo)) {
            $fullname .= ' (' . implode(', ', $extrainfo) . ')';
        }
        $o .= $this->output->action_link(new moodle_url('/user/view.php',
                                                        array('id' => $summary->user->id,
                                                              'course'=>$summary->courseid)),
                                                              $fullname);
        $o .= $this->output->box_end();
        $o .= $this->output->container_end();

        return $o;
    }

    /**
     * Render the submit for grading page
     *
     * @param assign_submit_for_grading_page $page
     * @return string
     */
    public function render_assign_submit_for_grading_page($page) {
        $o = '';

        $o .= $this->output->container_start('submitforgrading');
        $o .= $this->output->heading(get_string('submitassignment', 'assign'), 3);
        $o .= $this->output->spacer(array('height'=>30));

        $cancelurl = new moodle_url('/mod/assign/view.php', array('id' => $page->coursemoduleid));
        if (count($page->notifications)) {
            // At least one of the submission plugins is not ready for submission

            $o .= $this->output->heading(get_string('submissionnotready', 'assign'), 4);

            foreach ($page->notifications as $notification) {
                $o .= $this->output->notification($notification);
            }

            $o .= $this->output->continue_button($cancelurl);
        } else {
            // All submission plugins ready - confirm the student really does want to submit for marking
            $continueurl = new moodle_url('/mod/assign/view.php', array('id' => $page->coursemoduleid,
                                                                        'action' => 'confirmsubmit',
                                                                        'sesskey' => sesskey()));
            $o .= $this->output->confirm(get_string('confirmsubmission', 'mod_assign'), $continueurl, $cancelurl);
        }
        $o .= $this->output->container_end();


        return $o;
    }

    /**
     * Page is done - render the footer
     *
     * @return void
     */
    public function render_footer() {
        return $this->output->footer();
    }

    /**
     * render the header
     *
     * @param assign_header $header
     * @return string
     */
    public function render_assign_header(assign_header $header) {
        $o = '';

        if ($header->subpage) {
            $this->page->navbar->add($header->subpage);
        }

        $this->page->set_title(get_string('pluginname', 'assign'));
        $this->page->set_heading($header->assign->name);

        $o .= $this->output->header();
        if ($header->preface) {
            $o .= $header->preface;
        }
        $o .= $this->output->heading(format_string($header->assign->name,false, array('context' => $header->context)));

        if ($header->showintro) {
            $o .= $this->output->box_start('generalbox boxaligncenter', 'intro');
            $o .= format_module_intro('assign', $header->assign, $header->coursemoduleid);
            $o .= $this->output->box_end();
        }

        return $o;
    }

    /**
     * render a table containing the current status of the grading process
     *
     * @param assign_grading_summary $summary
     * @return string
     */
    public function render_assign_grading_summary(assign_grading_summary $summary) {
        // create a table for the data
        $o = '';
        $o .= $this->output->container_start('gradingsummary');
        $o .= $this->output->heading(get_string('gradingsummary', 'assign'), 3);
        $o .= $this->output->box_start('boxaligncenter gradingsummarytable');
        $t = new html_table();

        // status
        $this->add_table_row_tuple($t, get_string('numberofparticipants', 'assign'),
                                   $summary->participantcount);

        // Drafts count and dont show drafts count when using offline assignment.
        if ($summary->submissiondraftsenabled && $summary->submissionsenabled) {
            $this->add_table_row_tuple($t, get_string('numberofdraftsubmissions', 'assign'),
                                       $summary->submissiondraftscount);
        }

        // submitted for grading
        if ($summary->submissionsenabled) {
            $this->add_table_row_tuple($t, get_string('numberofsubmittedassignments', 'assign'),
                                       $summary->submissionssubmittedcount);
            $this->add_table_row_tuple($t, get_string('numberofsubmissionsneedgrading', 'assign'),
                                       $summary->submissionsneedgradingcount);
        }

        $time = time();
        if ($summary->duedate) {
            // due date
            // submitted for grading
            $duedate = $summary->duedate;
            $this->add_table_row_tuple($t, get_string('duedate', 'assign'),
                                       userdate($duedate));

            // time remaining
            $due = '';
            if ($duedate - $time <= 0) {
                $due = get_string('assignmentisdue', 'assign');
            } else {
                $due = format_time($duedate - $time);
            }
            $this->add_table_row_tuple($t, get_string('timeremaining', 'assign'), $due);
        }

        // all done - write the table
        $o .= html_writer::table($t);
        $o .= $this->output->box_end();

        // link to the grading page
        $o .= $this->output->container_start('submissionlinks');
        $o .= $this->output->action_link(new moodle_url('/mod/assign/view.php',
                                                          array('id' => $summary->coursemoduleid,
                                                                'action'=>'grading')),
                                                          get_string('viewgrading', 'assign'));
        $o .= $this->output->container_end();

        // close the container and insert a spacer
        $o .= $this->output->container_end();

        return $o;
    }

    /**
     * render a table containing all the current grades and feedback
     *
     * @param assign_feedback_status $status
     * @return string
     */
    public function render_assign_feedback_status(assign_feedback_status $status) {
        global $DB, $CFG;
        $o = '';

        $o .= $this->output->container_start('feedback');
        $o .= $this->output->heading(get_string('feedback', 'assign'), 3);
        $o .= $this->output->box_start('boxaligncenter feedbacktable');
        $t = new html_table();

        if (isset($status->gradefordisplay)) {
            $row = new html_table_row();
            $cell1 = new html_table_cell(get_string('grade'));
            $cell2 = new html_table_cell($status->gradefordisplay);
            $row->cells = array($cell1, $cell2);
            $t->data[] = $row;

            $row = new html_table_row();
            $cell1 = new html_table_cell(get_string('gradedon', 'assign'));
            $cell2 = new html_table_cell(userdate($status->gradeddate));
            $row->cells = array($cell1, $cell2);
            $t->data[] = $row;
        }

        if ($status->grader) {
            $row = new html_table_row();
            $cell1 = new html_table_cell(get_string('gradedby', 'assign'));
            $cell2 = new html_table_cell($this->output->user_picture($status->grader) . $this->output->spacer(array('width'=>30)) . fullname($status->grader));
            $row->cells = array($cell1, $cell2);
            $t->data[] = $row;
        }

        foreach ($status->feedbackplugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible() && !empty($status->grade) && !$plugin->is_empty($status->grade)) {
                $row = new html_table_row();
                $cell1 = new html_table_cell($plugin->get_name());
                $pluginfeedback = new assign_feedback_plugin_feedback($plugin, $status->grade, assign_feedback_plugin_feedback::SUMMARY, $status->coursemoduleid, $status->returnaction, $status->returnparams);
                $cell2 = new html_table_cell($this->render($pluginfeedback));
                $row->cells = array($cell1, $cell2);
                $t->data[] = $row;
            }
        }


        $o .= html_writer::table($t);
        $o .= $this->output->box_end();

        $o .= $this->output->container_end();
        return $o;
    }

    /**
     * render a table containing the current status of the submission
     *
     * @param assign_submission_status $status
     * @return string
     */
    public function render_assign_submission_status(assign_submission_status $status) {
        $o = '';
        $o .= $this->output->container_start('submissionstatustable');
        $o .= $this->output->heading(get_string('submissionstatusheading', 'assign'), 3);
        $time = time();

        if ($status->allowsubmissionsfromdate &&
                $time <= $status->allowsubmissionsfromdate) {
            $o .= $this->output->box_start('generalbox boxaligncenter submissionsalloweddates');
            if ($status->alwaysshowdescription) {
                $o .= get_string('allowsubmissionsfromdatesummary', 'assign', userdate($status->allowsubmissionsfromdate));
            } else {
                $o .= get_string('allowsubmissionsanddescriptionfromdatesummary', 'assign', userdate($status->allowsubmissionsfromdate));
            }
            $o .= $this->output->box_end();
        }
        $o .= $this->output->box_start('boxaligncenter submissionsummarytable');

        $t = new html_table();

        $row = new html_table_row();
        $cell1 = new html_table_cell(get_string('submissionstatus', 'assign'));
        if ($status->submission) {
            $cell2 = new html_table_cell(get_string('submissionstatus_' . $status->submission->status, 'assign'));
            $cell2->attributes = array('class'=>'submissionstatus' . $status->submission->status);
        } else {
            if (!$status->submissionsenabled) {
                $cell2 = new html_table_cell(get_string('noonlinesubmissions', 'assign'));
            } else {
                $cell2 = new html_table_cell(get_string('nosubmission', 'assign'));
            }
        }
        $row->cells = array($cell1, $cell2);
        $t->data[] = $row;

        // status
        if ($status->locked) {
            $row = new html_table_row();
            $cell1 = new html_table_cell();
            $cell2 = new html_table_cell(get_string('submissionslocked', 'assign'));
            $cell2->attributes = array('class'=>'submissionlocked');
            $row->cells = array($cell1, $cell2);
            $t->data[] = $row;
        }

        // grading status
        $row = new html_table_row();
        $cell1 = new html_table_cell(get_string('gradingstatus', 'assign'));

        if ($status->graded) {
            $cell2 = new html_table_cell(get_string('graded', 'assign'));
            $cell2->attributes = array('class'=>'submissiongraded');
        } else {
            $cell2 = new html_table_cell(get_string('notgraded', 'assign'));
            $cell2->attributes = array('class'=>'submissionnotgraded');
        }
        $row->cells = array($cell1, $cell2);
        $t->data[] = $row;


        $duedate = $status->duedate;
        if ($duedate >= 1) {
            $row = new html_table_row();
            $cell1 = new html_table_cell(get_string('duedate', 'assign'));
            $cell2 = new html_table_cell(userdate($duedate));
            $row->cells = array($cell1, $cell2);
            $t->data[] = $row;

            // time remaining
            $row = new html_table_row();
            $cell1 = new html_table_cell(get_string('timeremaining', 'assign'));
            if ($duedate - $time <= 0) {
                if (!$status->submission || $status->submission->status != ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
                    if ($status->submissionsenabled) {
                        $cell2 = new html_table_cell(get_string('overdue', 'assign', format_time($time - $duedate)));
                        $cell2->attributes = array('class'=>'overdue');
                    } else {
                        $cell2 = new html_table_cell(get_string('duedatereached', 'assign'));
                    }
                } else {
                    if ($status->submission->timemodified > $duedate) {
                        $cell2 = new html_table_cell(get_string('submittedlate', 'assign', format_time($status->submission->timemodified - $duedate)));
                        $cell2->attributes = array('class'=>'latesubmission');
                    } else {
                        $cell2 = new html_table_cell(get_string('submittedearly', 'assign', format_time($status->submission->timemodified - $duedate)));
                        $cell2->attributes = array('class'=>'earlysubmission');
                    }
                }
            } else {
                $cell2 = new html_table_cell(format_time($duedate - $time));
            }
            $row->cells = array($cell1, $cell2);
            $t->data[] = $row;
        }

        // last modified
        if ($status->submission) {
            $row = new html_table_row();
            $cell1 = new html_table_cell(get_string('timemodified', 'assign'));
            $cell2 = new html_table_cell(userdate($status->submission->timemodified));
            $row->cells = array($cell1, $cell2);
            $t->data[] = $row;

            foreach ($status->submissionplugins as $plugin) {
                $pluginshowsummary = !$plugin->is_empty($status->submission) || !$plugin->allow_submissions();
                if ($plugin->is_enabled() &&
                    $plugin->is_visible() &&
                    $pluginshowsummary) {

                    $row = new html_table_row();
                    $cell1 = new html_table_cell($plugin->get_name());
                    $pluginsubmission = new assign_submission_plugin_submission($plugin, $status->submission, assign_submission_plugin_submission::SUMMARY, $status->coursemoduleid, $status->returnaction, $status->returnparams);
                    $cell2 = new html_table_cell($this->render($pluginsubmission));
                    $row->cells = array($cell1, $cell2);
                    $t->data[] = $row;
                }
            }
        }


        $o .= html_writer::table($t);
        $o .= $this->output->box_end();

        // links
        if ($status->canedit) {
            if (!$status->submission) {
                $o .= $this->output->single_button(new moodle_url('/mod/assign/view.php',
                    array('id' => $status->coursemoduleid, 'action' => 'editsubmission')), get_string('addsubmission', 'assign'), 'get');
            } else {
                $o .= $this->output->single_button(new moodle_url('/mod/assign/view.php',
                    array('id' => $status->coursemoduleid, 'action' => 'editsubmission')), get_string('editsubmission', 'assign'), 'get');
            }
        }

        if ($status->cansubmit) {
            // submission.php
            $o .= $this->output->single_button(new moodle_url('/mod/assign/view.php',
                    array('id' => $status->coursemoduleid, 'action'=>'submit')), get_string('submitassignment', 'assign'), 'get');
            $o .= $this->output->box_start('boxaligncenter submithelp');
            $o .= get_string('submitassignment_help', 'assign');
            $o .= $this->output->box_end();
        }

        $o .= $this->output->container_end();
        return $o;
    }

    /**
     * render a submission plugin submission
     *
     * @param assign_submission_plugin_submission $submissionplugin
     * @return string
     */
    public function render_assign_submission_plugin_submission(assign_submission_plugin_submission $submissionplugin) {
        $o = '';

        if ($submissionplugin->view == assign_submission_plugin_submission::SUMMARY) {
            $icon = $this->output->pix_icon('t/preview', get_string('view' . substr($submissionplugin->plugin->get_subtype(), strlen('assign')), 'mod_assign'));
            $link = '';
            $showviewlink = false;
            $summary = $submissionplugin->plugin->view_summary($submissionplugin->submission, $showviewlink);
            if ($showviewlink) {
                $link = $this->output->action_link(
                                new moodle_url('/mod/assign/view.php',
                                               array('id' => $submissionplugin->coursemoduleid,
                                                     'sid'=>$submissionplugin->submission->id,
                                                     'plugin'=>$submissionplugin->plugin->get_type(),
                                                     'action'=>'viewplugin' . $submissionplugin->plugin->get_subtype(),
                                                     'returnaction'=>$submissionplugin->returnaction,
                                                     'returnparams'=>http_build_query($submissionplugin->returnparams))),
                                $icon);

                $link .= $this->output->spacer(array('width'=>15));
            }

            $o .= $link . $summary;
        } else if ($submissionplugin->view == assign_submission_plugin_submission::FULL) {
            $o .= $this->output->box_start('boxaligncenter submissionfull');
            $o .= $submissionplugin->plugin->view($submissionplugin->submission);
            $o .= $this->output->box_end();
        }

        return $o;
    }

    /**
     * render the grading table
     *
     * @param assign_grading_table $table
     * @return string
     */
    public function render_assign_grading_table(assign_grading_table $table) {
        $o = '';
        $o .= $this->output->box_start('boxaligncenter gradingtable');
        $this->page->requires->js_init_call('M.mod_assign.init_grading_table', array());
        $this->page->requires->string_for_js('nousersselected', 'assign');
        $this->page->requires->string_for_js('batchoperationconfirmlock', 'assign');
        $this->page->requires->string_for_js('batchoperationconfirmunlock', 'assign');
        $this->page->requires->string_for_js('batchoperationconfirmreverttodraft', 'assign');
        $this->page->requires->string_for_js('editaction', 'assign');
        // need to get from prefs
        $o .= $this->flexible_table($table, $table->get_rows_per_page(), true);
        $o .= $this->output->box_end();

        return $o;
   }

    /**
     * Render a feedback plugin feedback
     *
     * @param assign_feedback_plugin_feedback $feedbackplugin
     * @return string
     */
    public function render_assign_feedback_plugin_feedback(assign_feedback_plugin_feedback $feedbackplugin) {
        $o = '';

        if ($feedbackplugin->view == assign_feedback_plugin_feedback::SUMMARY) {
            $icon = $this->output->pix_icon('t/preview', get_string('view' . substr($feedbackplugin->plugin->get_subtype(), strlen('assign')), 'mod_assign'));
            $link = '';
            $showviewlink = false;
            $summary = $feedbackplugin->plugin->view_summary($feedbackplugin->grade, $showviewlink);
            if ($showviewlink) {
                $link = $this->output->action_link(
                                new moodle_url('/mod/assign/view.php',
                                               array('id' => $feedbackplugin->coursemoduleid,
                                                     'gid'=>$feedbackplugin->grade->id,
                                                     'plugin'=>$feedbackplugin->plugin->get_type(),
                                                     'action'=>'viewplugin' . $feedbackplugin->plugin->get_subtype(),
                                                     'returnaction'=>$feedbackplugin->returnaction,
                                                     'returnparams'=>http_build_query($feedbackplugin->returnparams))),
                                $icon);
                $link .= $this->output->spacer(array('width'=>15));
            }

            $o .= $link . $summary;
        } else if ($feedbackplugin->view == assign_feedback_plugin_feedback::FULL) {
            $o .= $this->output->box_start('boxaligncenter feedbackfull');
            $o .= $feedbackplugin->plugin->view($feedbackplugin->grade);
            $o .= $this->output->box_end();
        }

        return $o;
    }



    /**
     * Internal function - creates htmls structure suitable for YUI tree.
     *
     * @param assign_files $tree
     * @param array $dir
     * @return string
     */
    protected function htmllize_tree(assign_files $tree, $dir) {
        global $CFG;
        $yuiconfig = array();
        $yuiconfig['type'] = 'html';

        if (empty($dir['subdirs']) and empty($dir['files'])) {
            return '';
        }

        $result = '<ul>';
        foreach ($dir['subdirs'] as $subdir) {
            $image = $this->output->pix_icon(file_folder_icon(), $subdir['dirname'], 'moodle', array('class'=>'icon'));
            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div>'.$image.' '.s($subdir['dirname']).'</div> '.$this->htmllize_tree($tree, $subdir).'</li>';
        }

        foreach ($dir['files'] as $file) {
            $filename = $file->get_filename();
            if ($CFG->enableplagiarism) {
                require_once($CFG->libdir.'/plagiarismlib.php');
                $plagiarsmlinks = plagiarism_get_links(array('userid'=>$file->get_userid(), 'file'=>$file, 'cmid'=>$tree->cm->id, 'course'=>$tree->course));
            } else {
                $plagiarsmlinks = '';
            }
            $image = $this->output->pix_icon(file_file_icon($file), $filename, 'moodle', array('class'=>'icon'));
            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div>'.$image.' '.$file->fileurl.' '.$plagiarsmlinks.$file->portfoliobutton.'</div></li>';
        }

        $result .= '</ul>';

        return $result;
    }

    /**
     * Helper method dealing with the fact we can not just fetch the output of flexible_table
     *
     * @param flexible_table $table The table to render
     * @param int $rowsperpage How many assignments to render in a page
     * @param bool $displaylinks - Whether to render links in the table (e.g. downloads would not enable this)
     * @return string HTML
     */
    protected function flexible_table(flexible_table $table, $rowsperpage, $displaylinks) {

        $o = '';
        ob_start();
        $table->out($rowsperpage, $displaylinks);
        $o = ob_get_contents();
        ob_end_clean();

        return $o;
    }

    /**
     * Helper method dealing with the fact we can not just fetch the output of moodleforms
     *
     * @param moodleform $mform
     * @return string HTML
     */
    protected function moodleform(moodleform $mform) {

        $o = '';
        ob_start();
        $mform->display();
        $o = ob_get_contents();
        ob_end_clean();

        return $o;
    }

}

