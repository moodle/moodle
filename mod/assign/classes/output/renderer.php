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

namespace mod_assign\output;

use assign_files;
use html_writer;
use mod_assign\output\grading_app;
use portfolio_add_button;
use stored_file;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * A custom renderer class that extends the plugin_renderer_base and is used by the assign module.
 *
 * @package mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /** @var string a unique ID. */
    public $htmlid;

    /**
     * Rendering assignment files
     *
     * @param \context $context
     * @param int $userid
     * @param string $filearea
     * @param string $component
     * @param \stdClass $course
     * @param \stdClass $coursemodule
     * @return string
     */
    public function assign_files(\context $context, $userid, $filearea, $component, $course = null, $coursemodule = null) {
        return $this->render(new \assign_files($context, $userid, $filearea, $component, $course, $coursemodule));
    }

    /**
     * Rendering assignment files
     *
     * @param \assign_files $tree
     * @return string
     */
    public function render_assign_files(\assign_files $tree) {
        $this->htmlid = \html_writer::random_id('assign_files_tree');
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
     * Utility function to add a row of data to a table with 2 columns where the first column is the table's header.
     * Modified the table param and does not return a value.
     *
     * @param \html_table $table The table to append the row of data to
     * @param string $first The first column text
     * @param string $second The second column text
     * @param array $firstattributes The first column attributes (optional)
     * @param array $secondattributes The second column attributes (optional)
     * @return void
     */
    private function add_table_row_tuple(\html_table $table, $first, $second, $firstattributes = [],
            $secondattributes = []) {
        $row = new \html_table_row();
        $cell1 = new \html_table_cell($first);
        $cell1->header = true;
        if (!empty($firstattributes)) {
            $cell1->attributes = $firstattributes;
        }
        $cell2 = new \html_table_cell($second);
        if (!empty($secondattributes)) {
            $cell2->attributes = $secondattributes;
        }
        $row->cells = array($cell1, $cell2);
        $table->data[] = $row;
    }

    /**
     * Render a grading message notification
     * @param \assign_gradingmessage $result The result to render
     * @return string
     */
    public function render_assign_gradingmessage(\assign_gradingmessage $result) {
        $urlparams = array('id' => $result->coursemoduleid, 'action'=>'grading');
        if (!empty($result->page)) {
            $urlparams['page'] = $result->page;
        }
        $url = new \moodle_url('/mod/assign/view.php', $urlparams);
        $classes = $result->gradingerror ? 'notifyproblem' : 'notifysuccess';

        $o = '';
        $o .= $this->output->heading($result->heading, 4);
        $o .= $this->output->notification($result->message, $classes);
        $o .= $this->output->continue_button($url);
        return $o;
    }

    /**
     * Render the generic form
     * @param \assign_form $form The form to render
     * @return string
     */
    public function render_assign_form(\assign_form $form) {
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
     * @param \assign_user_summary $summary The user summary to render
     * @return string
     */
    public function render_assign_user_summary(\assign_user_summary $summary) {
        $o = '';
        $supendedclass = '';
        $suspendedicon = '';

        if (!$summary->user) {
            return;
        }

        if ($summary->suspendeduser) {
            $supendedclass = ' usersuspended';
            $suspendedstring = get_string('userenrolmentsuspended', 'grades');
            $suspendedicon = ' ' . $this->pix_icon('i/enrolmentsuspended', $suspendedstring);
        }
        $o .= $this->output->container_start('usersummary');
        $o .= $this->output->box_start('boxaligncenter usersummarysection'.$supendedclass);
        if ($summary->blindmarking) {
            $o .= get_string('hiddenuser', 'assign') . $summary->uniqueidforuser.$suspendedicon;
        } else {
            $o .= $this->output->user_picture($summary->user);
            $o .= $this->output->spacer(array('width'=>30));
            $urlparams = array('id' => $summary->user->id, 'course'=>$summary->courseid);
            $url = new \moodle_url('/user/view.php', $urlparams);
            $fullname = fullname($summary->user, $summary->viewfullnames);
            $extrainfo = array();
            foreach ($summary->extrauserfields as $extrafield) {
                $extrainfo[] = s($summary->user->$extrafield);
            }
            if (count($extrainfo)) {
                $fullname .= ' (' . implode(', ', $extrainfo) . ')';
            }
            $fullname .= $suspendedicon;
            $o .= $this->output->action_link($url, $fullname);
        }
        $o .= $this->output->box_end();
        $o .= $this->output->container_end();

        return $o;
    }

    /**
     * Render the submit for grading page
     *
     * @param \assign_submit_for_grading_page $page
     * @return string
     */
    public function render_assign_submit_for_grading_page($page) {
        $o = '';

        $o .= $this->output->container_start('submitforgrading');
        $o .= $this->output->heading(get_string('confirmsubmissionheading', 'assign'), 3);

        $cancelurl = new \moodle_url('/mod/assign/view.php', array('id' => $page->coursemoduleid));
        if (count($page->notifications)) {
            // At least one of the submission plugins is not ready for submission.

            $o .= $this->output->heading(get_string('submissionnotready', 'assign'), 4);

            foreach ($page->notifications as $notification) {
                $o .= $this->output->notification($notification);
            }

            $o .= $this->output->continue_button($cancelurl);
        } else {
            // All submission plugins ready - show the confirmation form.
            $o .= $this->moodleform($page->confirmform);
        }
        $o .= $this->output->container_end();

        return $o;
    }

    /**
     * Page is done - render the footer.
     *
     * @return void
     */
    public function render_footer() {
        return $this->output->footer();
    }

    /**
     * Render the header.
     *
     * @param assign_header $header
     * @return string
     */
    public function render_assign_header(assign_header $header) {
        if ($header->subpage) {
            $this->page->navbar->add($header->subpage, $header->subpageurl);
            $args = ['contextname' => $header->context->get_context_name(false, true), 'subpage' => $header->subpage];
            $title = get_string('subpagetitle', 'assign', $args);
        } else {
            $title = $header->context->get_context_name(false, true);
        }
        $courseshortname = $header->context->get_course_context()->get_context_name(false, true);
        $title = $courseshortname . ': ' . $title;
        $heading = format_string($header->assign->name, false, array('context' => $header->context));

        $this->page->set_title($title);
        $this->page->set_heading($this->page->course->fullname);

        $description = $header->preface;
        if ($header->showintro || $header->activity) {
            $description = $this->output->box_start('generalbox boxaligncenter');
            if ($header->showintro) {
                $description .= format_module_intro('assign', $header->assign, $header->coursemoduleid);
            }
            if ($header->activity) {
                $description .= $this->format_activity_text($header->assign, $header->coursemoduleid);
            }
            $description .= $header->postfix;
            $description .= $this->output->box_end();
        }

        $activityheader = $this->page->activityheader;
        $activityheader->set_attrs([
            'title' => $activityheader->is_title_allowed() ? $heading : '',
            'description' => $description
        ]);

        return $this->output->header();
    }

    /**
     * Render the header for an individual plugin.
     *
     * @param \assign_plugin_header $header
     * @return string
     */
    public function render_assign_plugin_header(\assign_plugin_header $header) {
        $o = $header->plugin->view_header();
        return $o;
    }

    /**
     * Render a table containing the current status of the grading process.
     *
     * @param \assign_grading_summary $summary
     * @return string
     */
    public function render_assign_grading_summary(\assign_grading_summary $summary) {
        // Create a table for the data.
        $o = '';
        $o .= $this->output->container_start('gradingsummary');
        $o .= $this->output->heading(get_string('gradingsummary', 'assign'), 3);

        if (isset($summary->cm)) {
            $currenturl = new \moodle_url('/mod/assign/view.php', array('id' => $summary->cm->id));
            $o .= groups_print_activity_menu($summary->cm, $currenturl->out(), true);
        }

        $o .= $this->output->box_start('boxaligncenter gradingsummarytable');
        $t = new \html_table();
        $t->attributes['class'] = 'generaltable table-bordered';

        // Visibility Status.
        $cell1content = get_string('hiddenfromstudents');
        $cell2content = (!$summary->isvisible) ? get_string('yes') : get_string('no');
        $this->add_table_row_tuple($t, $cell1content, $cell2content);

        // Status.
        if ($summary->teamsubmission) {
            if ($summary->warnofungroupedusers === \assign_grading_summary::WARN_GROUPS_REQUIRED) {
                $o .= $this->output->notification(get_string('ungroupedusers', 'assign'));
            } else if ($summary->warnofungroupedusers === \assign_grading_summary::WARN_GROUPS_OPTIONAL) {
                $o .= $this->output->notification(get_string('ungroupedusersoptional', 'assign'));
            }
            $cell1content = get_string('numberofteams', 'assign');
        } else {
            $cell1content = get_string('numberofparticipants', 'assign');
        }

        $cell2content = $summary->participantcount;
        $this->add_table_row_tuple($t, $cell1content, $cell2content);

        // Drafts count and dont show drafts count when using offline assignment.
        if ($summary->submissiondraftsenabled && $summary->submissionsenabled) {
            $cell1content = get_string('numberofdraftsubmissions', 'assign');
            $cell2content = $summary->submissiondraftscount;
            $this->add_table_row_tuple($t, $cell1content, $cell2content);
        }

        // Submitted for grading.
        if ($summary->submissionsenabled) {
            $cell1content = get_string('numberofsubmittedassignments', 'assign');
            $cell2content = $summary->submissionssubmittedcount;
            $this->add_table_row_tuple($t, $cell1content, $cell2content);

            if (!$summary->teamsubmission) {
                $cell1content = get_string('numberofsubmissionsneedgrading', 'assign');
                $cell2content = $summary->submissionsneedgradingcount;
                $this->add_table_row_tuple($t, $cell1content, $cell2content);
            }
        }

        $time = time();
        if ($summary->duedate) {
            // Time remaining.
            $duedate = $summary->duedate;
            $cell1content = get_string('timeremaining', 'assign');
            if ($summary->courserelativedatesmode) {
                $cell2content = get_string('relativedatessubmissiontimeleft', 'mod_assign');
            } else {
                if ($duedate - $time <= 0) {
                    $cell2content = get_string('assignmentisdue', 'assign');
                } else {
                    $cell2content = format_time($duedate - $time);
                }
            }

            $this->add_table_row_tuple($t, $cell1content, $cell2content);

            if ($duedate < $time) {
                $cell1content = get_string('latesubmissions', 'assign');
                $cutoffdate = $summary->cutoffdate;
                if ($cutoffdate) {
                    if ($cutoffdate > $time) {
                        $cell2content = get_string('latesubmissionsaccepted', 'assign', userdate($summary->cutoffdate));
                    } else {
                        $cell2content = get_string('nomoresubmissionsaccepted', 'assign');
                    }

                    $this->add_table_row_tuple($t, $cell1content, $cell2content);
                }
            }

        }

        // Add time limit info if there is one.
        $timelimitenabled = get_config('assign', 'enabletimelimit');
        if ($timelimitenabled && $summary->timelimit > 0) {
            $cell1content = get_string('timelimit', 'assign');
            $cell2content = format_time($summary->timelimit);
            $this->add_table_row_tuple($t, $cell1content, $cell2content, [], []);
        }

        // All done - write the table.
        $o .= \html_writer::table($t);
        $o .= $this->output->box_end();

        // Close the container and insert a spacer.
        $o .= $this->output->container_end();
        $o .= \html_writer::end_tag('center');

        return $o;
    }

    /**
     * Render a table containing all the current grades and feedback.
     *
     * @param \assign_feedback_status $status
     * @return string
     */
    public function render_assign_feedback_status(\assign_feedback_status $status) {
        $o = '';

        $o .= $this->output->container_start('feedback');
        $o .= $this->output->heading(get_string('feedback', 'assign'), 3);
        $o .= $this->output->box_start('boxaligncenter feedbacktable');
        $t = new \html_table();

        // Grade.
        if (isset($status->gradefordisplay)) {
            $cell1content = get_string('gradenoun');
            $cell2content = $status->gradefordisplay;
            $this->add_table_row_tuple($t, $cell1content, $cell2content);

            // Grade date.
            $cell1content = get_string('gradedon', 'assign');
            $cell2content = userdate($status->gradeddate);
            $this->add_table_row_tuple($t, $cell1content, $cell2content);
        }

        if ($status->grader) {
            // Grader.
            $cell1content = get_string('gradedby', 'assign');
            $cell2content = $this->output->user_picture($status->grader) .
                            $this->output->spacer(array('width' => 30)) .
                            fullname($status->grader, $status->canviewfullnames);
            $this->add_table_row_tuple($t, $cell1content, $cell2content);
        }

        foreach ($status->feedbackplugins as $plugin) {
            if ($plugin->is_enabled() &&
                    $plugin->is_visible() &&
                    $plugin->has_user_summary() &&
                    !empty($status->grade) &&
                    !$plugin->is_empty($status->grade)) {

                $displaymode = \assign_feedback_plugin_feedback::SUMMARY;
                $pluginfeedback = new \assign_feedback_plugin_feedback($plugin,
                                                                      $status->grade,
                                                                      $displaymode,
                                                                      $status->coursemoduleid,
                                                                      $status->returnaction,
                                                                      $status->returnparams);
                $cell1content = $plugin->get_name();
                $cell2content = $this->render($pluginfeedback);
                $this->add_table_row_tuple($t, $cell1content, $cell2content);
            }
        }

        $o .= \html_writer::table($t);
        $o .= $this->output->box_end();

        if (!empty($status->gradingcontrollergrade)) {
            $o .= $this->output->heading(get_string('gradebreakdown', 'assign'), 4);
            $o .= $status->gradingcontrollergrade;
        }

        $o .= $this->output->container_end();
        return $o;
    }

    /**
     * Render a compact view of the current status of the submission.
     *
     * @param \assign_submission_status_compact $status
     * @return string
     */
    public function render_assign_submission_status_compact(\assign_submission_status_compact $status) {
        $o = '';
        $o .= $this->output->container_start('submissionstatustable');
        $o .= $this->output->heading(get_string('submission', 'assign'), 3);

        if ($status->teamsubmissionenabled) {
            $group = $status->submissiongroup;
            if ($group) {
                $team = format_string($group->name, false, $status->context);
            } else if ($status->preventsubmissionnotingroup) {
                if (count($status->usergroups) == 0) {
                    $team = '<span class="alert alert-error">' . get_string('noteam', 'assign') . '</span>';
                } else if (count($status->usergroups) > 1) {
                    $team = '<span class="alert alert-error">' . get_string('multipleteams', 'assign') . '</span>';
                }
            } else {
                $team = get_string('defaultteam', 'assign');
            }
            $o .= $this->output->container(get_string('teamname', 'assign', $team), 'teamname');
        }

        if (!$status->teamsubmissionenabled) {
            if ($status->submission && $status->submission->status != ASSIGN_SUBMISSION_STATUS_NEW) {
                $statusstr = get_string('submissionstatus_' . $status->submission->status, 'assign');
                $o .= $this->output->container($statusstr, 'submissionstatus' . $status->submission->status);
            } else {
                if (!$status->submissionsenabled) {
                    $o .= $this->output->container(get_string('noonlinesubmissions', 'assign'), 'submissionstatus');
                } else {
                    $o .= $this->output->container(get_string('noattempt', 'assign'), 'submissionstatus');
                }
            }
        } else {
            $group = $status->submissiongroup;
            if (!$group && $status->preventsubmissionnotingroup) {
                $o .= $this->output->container(get_string('nosubmission', 'assign'), 'submissionstatus');
            } else if ($status->teamsubmission && $status->teamsubmission->status != ASSIGN_SUBMISSION_STATUS_NEW) {
                $teamstatus = $status->teamsubmission->status;
                $submissionsummary = get_string('submissionstatus_' . $teamstatus, 'assign');
                $groupid = 0;
                if ($status->submissiongroup) {
                    $groupid = $status->submissiongroup->id;
                }

                $members = $status->submissiongroupmemberswhoneedtosubmit;
                $userslist = array();
                foreach ($members as $member) {
                    $urlparams = array('id' => $member->id, 'course' => $status->courseid);
                    $url = new \moodle_url('/user/view.php', $urlparams);
                    if ($status->view == assign_submission_status::GRADER_VIEW && $status->blindmarking) {
                        $userslist[] = $member->alias;
                    } else {
                        $fullname = fullname($member, $status->canviewfullnames);
                        $userslist[] = $this->output->action_link($url, $fullname);
                    }
                }
                if (count($userslist) > 0) {
                    $userstr = join(', ', $userslist);
                    $formatteduserstr = get_string('userswhoneedtosubmit', 'assign', $userstr);
                    $submissionsummary .= $this->output->container($formatteduserstr);
                }
                $o .= $this->output->container($submissionsummary, 'submissionstatus' . $status->teamsubmission->status);
            } else {
                if (!$status->submissionsenabled) {
                    $o .= $this->output->container(get_string('noonlinesubmissions', 'assign'), 'submissionstatus');
                } else {
                    $o .= $this->output->container(get_string('nosubmission', 'assign'), 'submissionstatus');
                }
            }
        }

        // Is locked?
        if ($status->locked) {
            $o .= $this->output->container(get_string('submissionslocked', 'assign'), 'submissionlocked');
        }

        // Grading status.
        $statusstr = '';
        $classname = 'gradingstatus';
        if ($status->gradingstatus == ASSIGN_GRADING_STATUS_GRADED ||
            $status->gradingstatus == ASSIGN_GRADING_STATUS_NOT_GRADED) {
            $statusstr = get_string($status->gradingstatus, 'assign');
        } else {
            $gradingstatus = 'markingworkflowstate' . $status->gradingstatus;
            $statusstr = get_string($gradingstatus, 'assign');
        }
        if ($status->gradingstatus == ASSIGN_GRADING_STATUS_GRADED ||
            $status->gradingstatus == ASSIGN_MARKING_WORKFLOW_STATE_RELEASED) {
            $classname = 'submissiongraded';
        } else {
            $classname = 'submissionnotgraded';
        }

        $o .= $this->output->container($statusstr, $classname);

        $submission = $status->teamsubmission ? $status->teamsubmission : $status->submission;
        $duedate = $status->duedate;
        if ($duedate > 0) {

            if ($status->extensionduedate) {
                // Extension date.
                $duedate = $status->extensionduedate;
            }
        }

        // Time remaining.
        // Only add the row if there is a due date, or a countdown.
        if ($status->duedate > 0 || !empty($submission->timestarted)) {
            [$remaining, $classname] = $this->get_time_remaining($status);

            // If the assignment is not submitted, and there is a submission in progress,
            // Add a heading for the time limit.
            if (!empty($submission) &&
                $submission->status != ASSIGN_SUBMISSION_STATUS_SUBMITTED &&
                !empty($submission->timestarted)
            ) {
                $o .= $this->output->container(get_string('timeremaining', 'assign'));
            }
            $o .= $this->output->container($remaining, $classname);
        }

        // Show graders whether this submission is editable by students.
        if ($status->view == assign_submission_status::GRADER_VIEW) {
            if ($status->canedit) {
                $o .= $this->output->container(get_string('submissioneditable', 'assign'), 'submissioneditable');
            } else {
                $o .= $this->output->container(get_string('submissionnoteditable', 'assign'), 'submissionnoteditable');
            }
        }

        // Grading criteria preview.
        if (!empty($status->gradingcontrollerpreview)) {
            $o .= $this->output->container($status->gradingcontrollerpreview, 'gradingmethodpreview');
        }

        if ($submission) {

            if (!$status->teamsubmission || $status->submissiongroup != false || !$status->preventsubmissionnotingroup) {
                foreach ($status->submissionplugins as $plugin) {
                    $pluginshowsummary = !$plugin->is_empty($submission) || !$plugin->allow_submissions();
                    if ($plugin->is_enabled() &&
                        $plugin->is_visible() &&
                        $plugin->has_user_summary() &&
                        $pluginshowsummary
                    ) {

                        $displaymode = \assign_submission_plugin_submission::SUMMARY;
                        $pluginsubmission = new \assign_submission_plugin_submission($plugin,
                            $submission,
                            $displaymode,
                            $status->coursemoduleid,
                            $status->returnaction,
                            $status->returnparams);
                        $plugincomponent = $plugin->get_subtype() . '_' . $plugin->get_type();
                        $o .= $this->output->container($this->render($pluginsubmission), 'assignsubmission ' . $plugincomponent);
                    }
                }
            }
        }

        $o .= $this->output->container_end();
        return $o;
    }

    /**
     * Render a table containing the current status of the submission.
     *
     * @param assign_submission_status $status
     * @return string
     */
    public function render_assign_submission_status(assign_submission_status $status) {
        $o = '';
        $o .= $this->output->container_start('submissionstatustable');
        $o .= $this->output->heading(get_string('submissionstatusheading', 'assign'), 3);
        $time = time();

        $o .= $this->output->box_start('boxaligncenter submissionsummarytable');

        $t = new \html_table();
        $t->attributes['class'] = 'generaltable table-bordered';

        $warningmsg = '';
        if ($status->teamsubmissionenabled) {
            $cell1content = get_string('submissionteam', 'assign');
            $group = $status->submissiongroup;
            if ($group) {
                $cell2content = format_string($group->name, false, ['context' => $status->context]);
            } else if ($status->preventsubmissionnotingroup) {
                if (count($status->usergroups) == 0) {
                    $notification = new \core\output\notification(get_string('noteam', 'assign'), 'error');
                    $notification->set_show_closebutton(false);
                    $warningmsg = $this->output->notification(get_string('noteam_desc', 'assign'), 'error');
                } else if (count($status->usergroups) > 1) {
                    $notification = new \core\output\notification(get_string('multipleteams', 'assign'), 'error');
                    $notification->set_show_closebutton(false);
                    $warningmsg = $this->output->notification(get_string('multipleteams_desc', 'assign'), 'error');
                }
                $cell2content = $this->output->render($notification);
            } else {
                $cell2content = get_string('defaultteam', 'assign');
            }

            $this->add_table_row_tuple($t, $cell1content, $cell2content);
        }

        if ($status->attemptreopenmethod != ASSIGN_ATTEMPT_REOPEN_METHOD_NONE) {
            $currentattempt = 1;
            if (!$status->teamsubmissionenabled) {
                if ($status->submission) {
                    $currentattempt = $status->submission->attemptnumber + 1;
                }
            } else {
                if ($status->teamsubmission) {
                    $currentattempt = $status->teamsubmission->attemptnumber + 1;
                }
            }

            $cell1content = get_string('attemptnumber', 'assign');
            $maxattempts = $status->maxattempts;
            if ($maxattempts == ASSIGN_UNLIMITED_ATTEMPTS) {
                $cell2content = get_string('currentattempt', 'assign', $currentattempt);
            } else {
                $cell2content = get_string('currentattemptof', 'assign',
                    array('attemptnumber' => $currentattempt, 'maxattempts' => $maxattempts));
            }

            $this->add_table_row_tuple($t, $cell1content, $cell2content);
        }

        $cell1content = get_string('submissionstatus', 'assign');
        $cell2attributes = [];
        if (!$status->teamsubmissionenabled) {
            if ($status->submission && $status->submission->status != ASSIGN_SUBMISSION_STATUS_NEW) {
                $cell2content = get_string('submissionstatus_' . $status->submission->status, 'assign');
                $cell2attributes = array('class' => 'submissionstatus' . $status->submission->status);
            } else {
                if (!$status->submissionsenabled) {
                    $cell2content = get_string('noonlinesubmissions', 'assign');
                } else {
                    $cell2content = get_string('nosubmissionyet', 'assign');
                }
            }
        } else {
            $group = $status->submissiongroup;
            if (!$group && $status->preventsubmissionnotingroup) {
                $cell2content = get_string('nosubmission', 'assign');
            } else if ($status->teamsubmission && $status->teamsubmission->status != ASSIGN_SUBMISSION_STATUS_NEW) {
                $teamstatus = $status->teamsubmission->status;
                $cell2content = get_string('submissionstatus_' . $teamstatus, 'assign');

                $members = $status->submissiongroupmemberswhoneedtosubmit;
                $userslist = array();
                foreach ($members as $member) {
                    $urlparams = array('id' => $member->id, 'course'=>$status->courseid);
                    $url = new \moodle_url('/user/view.php', $urlparams);
                    if ($status->view == assign_submission_status::GRADER_VIEW && $status->blindmarking) {
                        $userslist[] = $member->alias;
                    } else {
                        $fullname = fullname($member, $status->canviewfullnames);
                        $userslist[] = $this->output->action_link($url, $fullname);
                    }
                }
                if (count($userslist) > 0) {
                    $userstr = join(', ', $userslist);
                    $formatteduserstr = get_string('userswhoneedtosubmit', 'assign', $userstr);
                    $cell2content .= $this->output->container($formatteduserstr);
                }

                $cell2attributes = array('class' => 'submissionstatus' . $status->teamsubmission->status);
            } else {
                if (!$status->submissionsenabled) {
                    $cell2content = get_string('noonlinesubmissions', 'assign');
                } else {
                    $cell2content = get_string('nosubmission', 'assign');
                }
            }
        }

        $this->add_table_row_tuple($t, $cell1content, $cell2content, [], $cell2attributes);

        // Is locked?
        if ($status->locked) {
            $cell1content = '';
            $cell2content = get_string('submissionslocked', 'assign');
            $cell2attributes = array('class' => 'submissionlocked');
            $this->add_table_row_tuple($t, $cell1content, $cell2content, [], $cell2attributes);
        }

        // Grading status.
        $cell1content = get_string('gradingstatus', 'assign');
        if ($status->gradingstatus == ASSIGN_GRADING_STATUS_GRADED ||
            $status->gradingstatus == ASSIGN_GRADING_STATUS_NOT_GRADED) {
            $cell2content = get_string($status->gradingstatus, 'assign');
        } else {
            $gradingstatus = 'markingworkflowstate' . $status->gradingstatus;
            $cell2content = get_string($gradingstatus, 'assign');
        }
        if ($status->gradingstatus == ASSIGN_GRADING_STATUS_GRADED ||
            $status->gradingstatus == ASSIGN_MARKING_WORKFLOW_STATE_RELEASED) {
            $cell2attributes = array('class' => 'submissiongraded');
        } else {
            $cell2attributes = array('class' => 'submissionnotgraded');
        }
        $this->add_table_row_tuple($t, $cell1content, $cell2content, [], $cell2attributes);

        $submission = $status->teamsubmission ? $status->teamsubmission : $status->submission;
        $duedate = $status->duedate;
        if ($duedate > 0) {
            if ($status->view == assign_submission_status::GRADER_VIEW) {
                if ($status->cutoffdate) {
                    // Cut off date.
                    $cell1content = get_string('cutoffdate', 'assign');
                    $cell2content = userdate($status->cutoffdate);
                    $this->add_table_row_tuple($t, $cell1content, $cell2content);
                }
            }

            if ($status->extensionduedate) {
                // Extension date.
                $cell1content = get_string('extensionduedate', 'assign');
                $cell2content = userdate($status->extensionduedate);
                $this->add_table_row_tuple($t, $cell1content, $cell2content);
                $duedate = $status->extensionduedate;
            }
        }

        // Time remaining.
        // Only add the row if there is a due date, or a countdown.
        if ($status->duedate > 0 || !empty($submission->timestarted)) {
            $cell1content = get_string('timeremaining', 'assign');
            [$cell2content, $cell2attributes] = $this->get_time_remaining($status);
            $this->add_table_row_tuple($t, $cell1content, $cell2content, [], ['class' => $cell2attributes]);
        }

        // Add time limit info if there is one.
        $timelimitenabled = get_config('assign', 'enabletimelimit') && $status->timelimit > 0;
        if ($timelimitenabled && $status->timelimit > 0) {
            $cell1content = get_string('timelimit', 'assign');
            $cell2content = format_time($status->timelimit);
            $this->add_table_row_tuple($t, $cell1content, $cell2content, [], []);
        }

        // Show graders whether this submission is editable by students.
        if ($status->view == assign_submission_status::GRADER_VIEW) {
            $cell1content = get_string('editingstatus', 'assign');
            if ($status->canedit) {
                $cell2content = get_string('submissioneditable', 'assign');
                $cell2attributes = array('class' => 'submissioneditable');
            } else {
                $cell2content = get_string('submissionnoteditable', 'assign');
                $cell2attributes = array('class' => 'submissionnoteditable');
            }
            $this->add_table_row_tuple($t, $cell1content, $cell2content, [], $cell2attributes);
        }

        // Last modified.
        if ($submission) {
            $cell1content = get_string('timemodified', 'assign');

            if ($submission->status != ASSIGN_SUBMISSION_STATUS_NEW) {
                $cell2content = userdate($submission->timemodified);
            } else {
                $cell2content = "-";
            }

            $this->add_table_row_tuple($t, $cell1content, $cell2content);

            if (!$status->teamsubmission || $status->submissiongroup != false || !$status->preventsubmissionnotingroup) {
                foreach ($status->submissionplugins as $plugin) {
                    $pluginshowsummary = !$plugin->is_empty($submission) || !$plugin->allow_submissions();
                    if ($plugin->is_enabled() &&
                        $plugin->is_visible() &&
                        $plugin->has_user_summary() &&
                        $pluginshowsummary
                    ) {

                        $cell1content = $plugin->get_name();
                        $displaymode = \assign_submission_plugin_submission::SUMMARY;
                        $pluginsubmission = new \assign_submission_plugin_submission($plugin,
                            $submission,
                            $displaymode,
                            $status->coursemoduleid,
                            $status->returnaction,
                            $status->returnparams);
                        $cell2content = $this->render($pluginsubmission);
                        $this->add_table_row_tuple($t, $cell1content, $cell2content);
                    }
                }
            }
        }

        $o .= $warningmsg;
        $o .= \html_writer::table($t);
        $o .= $this->output->box_end();

        // Grading criteria preview.
        if (!empty($status->gradingcontrollerpreview)) {
            $o .= $this->output->heading(get_string('gradingmethodpreview', 'assign'), 4);
            $o .= $status->gradingcontrollerpreview;
        }

        $o .= $this->output->container_end();
        return $o;
    }

    /**
     * Output the attempt history chooser for this assignment
     *
     * @param \assign_attempt_history_chooser $history
     * @return string
     */
    public function render_assign_attempt_history_chooser(\assign_attempt_history_chooser $history) {
        $o = '';

        $context = $history->export_for_template($this);
        $o .= $this->render_from_template('mod_assign/attempt_history_chooser', $context);

        return $o;
    }

    /**
     * Output the attempt history for this assignment
     *
     * @param \assign_attempt_history $history
     * @return string
     */
    public function render_assign_attempt_history(\assign_attempt_history $history) {
        $o = '';

        // Don't show the last one because it is the current submission.
        array_pop($history->submissions);

        // Show newest to oldest.
        $history->submissions = array_reverse($history->submissions);

        if (empty($history->submissions)) {
            return '';
        }

        $containerid = 'attempthistory' . uniqid();
        $o .= $this->output->heading(get_string('attempthistory', 'assign'), 3);
        $o .= $this->box_start('attempthistory', $containerid);

        foreach ($history->submissions as $i => $submission) {
            $grade = null;
            foreach ($history->grades as $onegrade) {
                if ($onegrade->attemptnumber == $submission->attemptnumber) {
                    if ($onegrade->grade != ASSIGN_GRADE_NOT_SET) {
                        $grade = $onegrade;
                    }
                    break;
                }
            }

            if ($submission) {
                $submissionsummary = userdate($submission->timemodified);
            } else {
                $submissionsummary = get_string('nosubmission', 'assign');
            }

            $attemptsummaryparams = array('attemptnumber'=>$submission->attemptnumber+1,
                                          'submissionsummary'=>$submissionsummary);
            $o .= $this->heading(get_string('attemptheading', 'assign', $attemptsummaryparams), 4);

            $t = new \html_table();

            if ($submission) {
                $cell1content = get_string('submissionstatus', 'assign');
                $cell2content = get_string('submissionstatus_' . $submission->status, 'assign');
                $this->add_table_row_tuple($t, $cell1content, $cell2content);

                foreach ($history->submissionplugins as $plugin) {
                    $pluginshowsummary = !$plugin->is_empty($submission) || !$plugin->allow_submissions();
                    if ($plugin->is_enabled() &&
                            $plugin->is_visible() &&
                            $plugin->has_user_summary() &&
                            $pluginshowsummary) {

                        $cell1content = $plugin->get_name();
                        $pluginsubmission = new \assign_submission_plugin_submission($plugin,
                                                                                    $submission,
                                                                                    \assign_submission_plugin_submission::SUMMARY,
                                                                                    $history->coursemoduleid,
                                                                                    $history->returnaction,
                                                                                    $history->returnparams);
                        $cell2content = $this->render($pluginsubmission);
                        $this->add_table_row_tuple($t, $cell1content, $cell2content);
                    }
                }
            }

            if ($grade) {
                // Heading 'feedback'.
                $title = get_string('feedback', 'assign', $i);
                $title .= $this->output->spacer(array('width'=>10));
                if ($history->cangrade) {
                    // Edit previous feedback.
                    $returnparams = http_build_query($history->returnparams);
                    $urlparams = array('id' => $history->coursemoduleid,
                                   'rownum'=>$history->rownum,
                                   'useridlistid'=>$history->useridlistid,
                                   'attemptnumber'=>$grade->attemptnumber,
                                   'action'=>'grade',
                                   'returnaction'=>$history->returnaction,
                                   'returnparams'=>$returnparams);
                    $url = new \moodle_url('/mod/assign/view.php', $urlparams);
                    $icon = new \pix_icon('gradefeedback',
                                            get_string('editattemptfeedback', 'assign', $grade->attemptnumber+1),
                                            'mod_assign');
                    $title .= $this->output->action_icon($url, $icon);
                }
                $cell = new \html_table_cell($title);
                $cell->attributes['class'] = 'feedbacktitle';
                $cell->colspan = 2;
                $t->data[] = new \html_table_row(array($cell));

                // Grade.
                $cell1content = get_string('gradenoun');
                $cell2content = $grade->gradefordisplay;
                $this->add_table_row_tuple($t, $cell1content, $cell2content);

                // Graded on.
                $cell1content = get_string('gradedon', 'assign');
                $cell2content = userdate($grade->timemodified);
                $this->add_table_row_tuple($t, $cell1content, $cell2content);

                // Graded by set to a real user. Not set can be empty or -1.
                if (!empty($grade->grader) && is_object($grade->grader)) {
                    $cell1content = get_string('gradedby', 'assign');
                    $cell2content = $this->output->user_picture($grade->grader) .
                                    $this->output->spacer(array('width' => 30)) . fullname($grade->grader);
                    $this->add_table_row_tuple($t, $cell1content, $cell2content);
                }

                // Feedback from plugins.
                foreach ($history->feedbackplugins as $plugin) {
                    if ($plugin->is_enabled() &&
                        $plugin->is_visible() &&
                        $plugin->has_user_summary() &&
                        !$plugin->is_empty($grade)) {

                        $pluginfeedback = new \assign_feedback_plugin_feedback(
                            $plugin, $grade, \assign_feedback_plugin_feedback::SUMMARY, $history->coursemoduleid,
                            $history->returnaction, $history->returnparams
                        );

                        $cell1content = $plugin->get_name();
                        $cell2content = $this->render($pluginfeedback);
                        $this->add_table_row_tuple($t, $cell1content, $cell2content);
                    }

                }

            }

            $o .= \html_writer::table($t);
        }
        $o .= $this->box_end();

        $this->page->requires->yui_module('moodle-mod_assign-history', 'Y.one("#' . $containerid . '").history');

        return $o;
    }

    /**
     * Render a submission plugin submission
     *
     * @param \assign_submission_plugin_submission $submissionplugin
     * @return string
     */
    public function render_assign_submission_plugin_submission(\assign_submission_plugin_submission $submissionplugin) {
        $o = '';

        if ($submissionplugin->view == \assign_submission_plugin_submission::SUMMARY) {
            $showviewlink = false;
            $summary = $submissionplugin->plugin->view_summary($submissionplugin->submission,
                                                               $showviewlink);

            $classsuffix = $submissionplugin->plugin->get_subtype() .
                           '_' .
                           $submissionplugin->plugin->get_type() .
                           '_' .
                           $submissionplugin->submission->id;

            $o .= $this->output->box_start('boxaligncenter plugincontentsummary summary_' . $classsuffix);

            $link = '';
            if ($showviewlink) {
                $previewstr = get_string('viewsubmission', 'assign');
                $icon = $this->output->pix_icon('t/preview', $previewstr);

                $expandstr = get_string('viewfull', 'assign');
                $expandicon = $this->output->pix_icon('t/switch_plus', $expandstr);
                $options = array(
                    'class' => 'expandsummaryicon expand_' . $classsuffix,
                    'aria-label' => $expandstr,
                    'role' => 'button',
                    'aria-expanded' => 'false'
                );
                $o .= \html_writer::link('', $expandicon, $options);

                $jsparams = array($submissionplugin->plugin->get_subtype(),
                                  $submissionplugin->plugin->get_type(),
                                  $submissionplugin->submission->id);

                $this->page->requires->js_init_call('M.mod_assign.init_plugin_summary', $jsparams);

                $action = 'viewplugin' . $submissionplugin->plugin->get_subtype();
                $returnparams = http_build_query($submissionplugin->returnparams);
                $link .= '<noscript>';
                $urlparams = array('id' => $submissionplugin->coursemoduleid,
                                   'sid'=>$submissionplugin->submission->id,
                                   'plugin'=>$submissionplugin->plugin->get_type(),
                                   'action'=>$action,
                                   'returnaction'=>$submissionplugin->returnaction,
                                   'returnparams'=>$returnparams);
                $url = new \moodle_url('/mod/assign/view.php', $urlparams);
                $link .= $this->output->action_link($url, $icon);
                $link .= '</noscript>';

                $link .= $this->output->spacer(array('width'=>15));
            }

            $o .= $link . $summary;
            $o .= $this->output->box_end();
            if ($showviewlink) {
                $o .= $this->output->box_start('boxaligncenter hidefull full_' . $classsuffix);
                $collapsestr = get_string('viewsummary', 'assign');
                $options = array(
                    'class' => 'expandsummaryicon contract_' . $classsuffix,
                    'aria-label' => $collapsestr,
                    'role' => 'button',
                    'aria-expanded' => 'true'
                );
                $collapseicon = $this->output->pix_icon('t/switch_minus', $collapsestr);
                $o .= \html_writer::link('', $collapseicon, $options);

                $o .= $submissionplugin->plugin->view($submissionplugin->submission);
                $o .= $this->output->box_end();
            }
        } else if ($submissionplugin->view == \assign_submission_plugin_submission::FULL) {
            $o .= $this->output->box_start('boxaligncenter submissionfull');
            $o .= $submissionplugin->plugin->view($submissionplugin->submission);
            $o .= $this->output->box_end();
        }

        return $o;
    }

    /**
     * Render the grading table.
     *
     * @param \assign_grading_table $table
     * @return string
     */
    public function render_assign_grading_table(\assign_grading_table $table) {
        $o = '';
        $o .= $this->output->box_start('boxaligncenter gradingtable position-relative');

        $this->page->requires->js_init_call('M.mod_assign.init_grading_table', array());
        $this->page->requires->string_for_js('nousersselected', 'assign');
        $this->page->requires->string_for_js('batchoperationconfirmgrantextension', 'assign');
        $this->page->requires->string_for_js('batchoperationconfirmlock', 'assign');
        $this->page->requires->string_for_js('batchoperationconfirmremovesubmission', 'assign');
        $this->page->requires->string_for_js('batchoperationconfirmreverttodraft', 'assign');
        $this->page->requires->string_for_js('batchoperationconfirmunlock', 'assign');
        $this->page->requires->string_for_js('batchoperationconfirmaddattempt', 'assign');
        $this->page->requires->string_for_js('batchoperationconfirmdownloadselected', 'assign');
        $this->page->requires->string_for_js('batchoperationconfirmsetmarkingworkflowstate', 'assign');
        $this->page->requires->string_for_js('batchoperationconfirmsetmarkingallocation', 'assign');
        $this->page->requires->string_for_js('editaction', 'assign');
        foreach ($table->plugingradingbatchoperations as $plugin => $operations) {
            foreach ($operations as $operation => $description) {
                $this->page->requires->string_for_js('batchoperationconfirm' . $operation,
                                                     'assignfeedback_' . $plugin);
            }
        }
        $o .= $this->flexible_table($table, $table->get_rows_per_page(), true);
        $o .= $this->output->box_end();

        return $o;
    }

    /**
     * Render a feedback plugin feedback
     *
     * @param \assign_feedback_plugin_feedback $feedbackplugin
     * @return string
     */
    public function render_assign_feedback_plugin_feedback(\assign_feedback_plugin_feedback $feedbackplugin) {
        $o = '';

        if ($feedbackplugin->view == \assign_feedback_plugin_feedback::SUMMARY) {
            $showviewlink = false;
            $summary = $feedbackplugin->plugin->view_summary($feedbackplugin->grade, $showviewlink);

            $classsuffix = $feedbackplugin->plugin->get_subtype() .
                           '_' .
                           $feedbackplugin->plugin->get_type() .
                           '_' .
                           $feedbackplugin->grade->id;
            $o .= $this->output->box_start('boxaligncenter plugincontentsummary summary_' . $classsuffix);

            $link = '';
            if ($showviewlink) {
                $previewstr = get_string('viewfeedback', 'assign');
                $icon = $this->output->pix_icon('t/preview', $previewstr);

                $expandstr = get_string('viewfull', 'assign');
                $expandicon = $this->output->pix_icon('t/switch_plus', $expandstr);
                $options = array(
                    'class' => 'expandsummaryicon expand_' . $classsuffix,
                    'aria-label' => $expandstr,
                    'role' => 'button',
                    'aria-expanded' => 'false'
                );
                $o .= \html_writer::link('', $expandicon, $options);

                $jsparams = array($feedbackplugin->plugin->get_subtype(),
                                  $feedbackplugin->plugin->get_type(),
                                  $feedbackplugin->grade->id);
                $this->page->requires->js_init_call('M.mod_assign.init_plugin_summary', $jsparams);

                $urlparams = array('id' => $feedbackplugin->coursemoduleid,
                                   'gid'=>$feedbackplugin->grade->id,
                                   'plugin'=>$feedbackplugin->plugin->get_type(),
                                   'action'=>'viewplugin' . $feedbackplugin->plugin->get_subtype(),
                                   'returnaction'=>$feedbackplugin->returnaction,
                                   'returnparams'=>http_build_query($feedbackplugin->returnparams));
                $url = new \moodle_url('/mod/assign/view.php', $urlparams);
                $link .= '<noscript>';
                $link .= $this->output->action_link($url, $icon);
                $link .= '</noscript>';

                $link .= $this->output->spacer(array('width'=>15));
            }

            $o .= $link . $summary;
            $o .= $this->output->box_end();
            if ($showviewlink) {
                $o .= $this->output->box_start('boxaligncenter hidefull full_' . $classsuffix);
                $collapsestr = get_string('viewsummary', 'assign');
                $options = array(
                    'class' => 'expandsummaryicon contract_' . $classsuffix,
                    'aria-label' => $collapsestr,
                    'role' => 'button',
                    'aria-expanded' => 'true'
                );
                $collapseicon = $this->output->pix_icon('t/switch_minus', $collapsestr);
                $o .= \html_writer::link('', $collapseicon, $options);

                $o .= $feedbackplugin->plugin->view($feedbackplugin->grade);
                $o .= $this->output->box_end();
            }
        } else if ($feedbackplugin->view == \assign_feedback_plugin_feedback::FULL) {
            $o .= $this->output->box_start('boxaligncenter feedbackfull');
            $o .= $feedbackplugin->plugin->view($feedbackplugin->grade);
            $o .= $this->output->box_end();
        }

        return $o;
    }

    /**
     * Render a course index summary
     *
     * @param \assign_course_index_summary $indexsummary
     * @return string
     */
    public function render_assign_course_index_summary(\assign_course_index_summary $indexsummary) {
        $o = '';

        $strplural = get_string('modulenameplural', 'assign');
        $strsectionname  = $indexsummary->courseformatname;
        $strduedate = get_string('duedate', 'assign');
        $strsubmission = get_string('submission', 'assign');
        $strgrade = get_string('gradenoun');

        $table = new \html_table();
        if ($indexsummary->usesections) {
            $table->head  = array ($strsectionname, $strplural, $strduedate, $strsubmission, $strgrade);
            $table->align = array ('left', 'left', 'center', 'right', 'right');
        } else {
            $table->head  = array ($strplural, $strduedate, $strsubmission, $strgrade);
            $table->align = array ('left', 'left', 'center', 'right');
        }
        $table->data = array();

        $currentsection = '';
        foreach ($indexsummary->assignments as $info) {
            $params = array('id' => $info['cmid']);
            $link = \html_writer::link(new \moodle_url('/mod/assign/view.php', $params),
                                      $info['cmname']);
            $due = $info['timedue'] ? userdate($info['timedue']) : '-';

            if ($info['cangrade']) {
                $params['action'] = 'grading';
                $gradeinfo = \html_writer::link(new \moodle_url('/mod/assign/view.php', $params),
                    get_string('numberofsubmissionsneedgradinglabel', 'assign', $info['gradeinfo']));
            } else {
                $gradeinfo = $info['gradeinfo'];
            }

            $printsection = '';
            if ($indexsummary->usesections) {
                if ($info['sectionname'] !== $currentsection) {
                    if ($info['sectionname']) {
                        $printsection = $info['sectionname'];
                    }
                    if ($currentsection !== '') {
                        $table->data[] = 'hr';
                    }
                    $currentsection = $info['sectionname'];
                }
            }

            if ($indexsummary->usesections) {
                $row = [$printsection, $link, $due, $info['submissioninfo'], $gradeinfo];
            } else {
                $row = [$link, $due, $info['submissioninfo'], $gradeinfo];
            }
            $table->data[] = $row;
        }

        $o .= \html_writer::table($table);

        return $o;
    }

    /**
     * Get the time remaining for a submission.
     *
     * @param \mod_assign\output\assign_submission_status $status
     * @return array The first element is the time remaining as a human readable
     *               string and the second is a CSS class.
     */
    protected function get_time_remaining(\mod_assign\output\assign_submission_status $status): array {
        $time = time();
        $submission = $status->teamsubmission ? $status->teamsubmission : $status->submission;
        $submissionstarted = $submission && property_exists($submission, 'timestarted') && $submission->timestarted;
        $timelimitenabled = get_config('assign', 'enabletimelimit') && $status->timelimit > 0 && $submissionstarted;
        // Define $duedate as latest between due date and extension - which is a possibility...
        $extensionduedate = intval($status->extensionduedate);
        $duedate = !empty($extensionduedate) ? max($status->duedate, $extensionduedate) : $status->duedate;
        $duedatereached = $duedate > 0 && $duedate - $time <= 0;
        $timelimitenabledbeforeduedate = $timelimitenabled && !$duedatereached;

        // There is a submission, display the relevant early/late message.
        if ($submission && $submission->status == ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
            $latecalculation = $submission->timemodified - ($timelimitenabledbeforeduedate ? $submission->timestarted : 0);
            $latethreshold = $timelimitenabledbeforeduedate ? $status->timelimit : $duedate;
            $earlystring = $timelimitenabledbeforeduedate ? 'submittedundertime' : 'submittedearly';
            $latestring = $timelimitenabledbeforeduedate ? 'submittedovertime' : 'submittedlate';
            $ontime = $latecalculation <= $latethreshold;
            return [
                get_string(
                    $ontime ? $earlystring : $latestring,
                    'assign',
                    format_time($latecalculation - $latethreshold)
                ),
                $ontime ? 'earlysubmission' : 'latesubmission'
            ];
        }

        // There is no submission, due date has passed, show assignment is overdue.
        if ($duedatereached) {
            return [
                get_string(
                    $status->submissionsenabled ? 'overdue' : 'duedatereached',
                    'assign',
                    format_time($time - $duedate)
                ),
                'overdue'
            ];
        }

        // An attempt has started and there is a time limit, display the time limit.
        if ($timelimitenabled && !empty($submission->timestarted)) {
            return [
                (new \assign($status->context, null, null))->get_timelimit_panel($submission),
                'timeremaining'
            ];
        }

        // Assignment is not overdue, and no submission has been made. Just display the due date.
        return [get_string('paramtimeremaining', 'assign', format_time($duedate - $time)), 'timeremaining'];
    }

    /**
     * Internal function - creates htmls structure suitable for YUI tree.
     *
     * @param \assign_files $tree
     * @param array $dir
     * @return string
     */
    protected function htmllize_tree(\assign_files $tree, $dir) {
        global $CFG;
        $yuiconfig = array();
        $yuiconfig['type'] = 'html';

        if (empty($dir['subdirs']) and empty($dir['files'])) {
            return '';
        }

        $result = '<ul>';
        foreach ($dir['subdirs'] as $subdir) {
            $image = $this->output->pix_icon(file_folder_icon(),
                                             $subdir['dirname'],
                                             'moodle',
                                             array('class'=>'icon'));
            $result .= '<li yuiConfig=\'' . json_encode($yuiconfig) . '\'>' .
                       '<div>' . $image . ' ' . s($subdir['dirname']) . '</div> ' .
                       $this->htmllize_tree($tree, $subdir) .
                       '</li>';
        }

        foreach ($dir['files'] as $file) {
            $filename = $file->get_filename();
            if ($CFG->enableplagiarism) {
                require_once($CFG->libdir.'/plagiarismlib.php');
                $plagiarismlinks = plagiarism_get_links(array('userid'=>$file->get_userid(),
                                                             'file'=>$file,
                                                             'cmid'=>$tree->cm->id,
                                                             'course'=>$tree->course));
            } else {
                $plagiarismlinks = '';
            }
            $image = $this->output->pix_icon(file_file_icon($file),
                                             $filename,
                                             'moodle',
                                             array('class'=>'icon'));
            $result .= '<li yuiConfig=\'' . json_encode($yuiconfig) . '\'>' .
                '<div>' .
                    '<div class="fileuploadsubmission">' . $image . ' ' .
                    html_writer::link($tree->get_file_url($file), $file->get_filename(), [
                        'target' => '_blank',
                    ]) . ' ' .
                    $plagiarismlinks . ' ' .
                    $this->get_portfolio_button($tree, $file) . ' ' .
                    '</div>' .
                    '<div class="fileuploadsubmissiontime">' . $tree->get_modified_time($file) . '</div>' .
                '</div>' .
            '</li>';
        }

        $result .= '</ul>';

        return $result;
    }

    /**
     * Get the portfolio button content for the specified file.
     *
     * @param assign_files $tree
     * @param stored_file $file
     * @return string
     */
    protected function get_portfolio_button(assign_files $tree, stored_file $file): string {
        global $CFG;
        if (empty($CFG->enableportfolios)) {
            return '';
        }

        if (!has_capability('mod/assign:exportownsubmission', $tree->context)) {
            return '';
        }

        require_once($CFG->libdir . '/portfoliolib.php');

        $button = new portfolio_add_button();
        $portfolioparams = [
            'cmid' => $tree->cm->id,
            'fileid' => $file->get_id(),
        ];
        $button->set_callback_options('assign_portfolio_caller', $portfolioparams, 'mod_assign');
        $button->set_format_by_file($file);

        return (string) $button->to_html(PORTFOLIO_ADD_ICON_LINK);
    }

    /**
     * Helper method dealing with the fact we can not just fetch the output of flexible_table
     *
     * @param \flexible_table $table The table to render
     * @param int $rowsperpage How many assignments to render in a page
     * @param bool $displaylinks - Whether to render links in the table
     *                             (e.g. downloads would not enable this)
     * @return string HTML
     */
    protected function flexible_table(\flexible_table $table, $rowsperpage, $displaylinks) {

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
     * @param \moodleform $mform
     * @return string HTML
     */
    protected function moodleform(\moodleform $mform) {

        $o = '';
        ob_start();
        $mform->display();
        $o = ob_get_contents();
        ob_end_clean();

        return $o;
    }

    /**
     * Defer to template.
     *
     * @param grading_app $app - All the data to render the grading app.
     */
    public function render_grading_app(grading_app $app) {
        $context = $app->export_for_template($this);
        return $this->render_from_template('mod_assign/grading_app', $context);
    }

    /**
     * Renders the submission action menu.
     *
     * @param \mod_assign\output\actionmenu $actionmenu The actionmenu
     * @return string Rendered action menu.
     */
    public function submission_actionmenu(\mod_assign\output\actionmenu $actionmenu): string {
        $context = $actionmenu->export_for_template($this);
        return $this->render_from_template('mod_assign/submission_actionmenu', $context);
    }

    /**
     * Renders the user submission action menu.
     *
     * @param \mod_assign\output\user_submission_actionmenu $actionmenu The actionmenu
     * @return string The rendered action menu.
     */
    public function render_user_submission_actionmenu(\mod_assign\output\user_submission_actionmenu $actionmenu): string {
        $context = $actionmenu->export_for_template($this);
        return $this->render_from_template('mod_assign/user_submission_actionmenu', $context);
    }

    /**
     * Renders the override action menu.
     *
     * @param \mod_assign\output\override_actionmenu $actionmenu The actionmenu
     * @return string The rendered override action menu.
     */
    public function render_override_actionmenu(\mod_assign\output\override_actionmenu $actionmenu): string {
        $context = $actionmenu->export_for_template($this);
        return $this->render_from_template('mod_assign/override_actionmenu', $context);
    }

    /**
     * Renders the grading action menu.
     *
     * @param \mod_assign\output\grading_actionmenu $actionmenu The actionmenu
     * @return string The rendered grading action menu.
     */
    public function render_grading_actionmenu(\mod_assign\output\grading_actionmenu $actionmenu): string {
        $context = $actionmenu->export_for_template($this);
        return $this->render_from_template('mod_assign/grading_actionmenu', $context);
    }

    /**
     * Formats activity intro text.
     *
     * @param object $assign Instance of assign.
     * @param int $cmid Course module ID.
     * @return string
     */
    public function format_activity_text($assign, $cmid) {
        global $CFG;
        require_once("$CFG->libdir/filelib.php");
        $context = \context_module::instance($cmid);
        $options = array('noclean' => true, 'para' => false, 'filter' => true, 'context' => $context, 'overflowdiv' => true);
        $activity = file_rewrite_pluginfile_urls(
            $assign->activity, 'pluginfile.php', $context->id, 'mod_assign', ASSIGN_ACTIVITYATTACHMENT_FILEAREA, 0);
        return trim(format_text($activity, $assign->activityformat, $options, null));
    }
}
