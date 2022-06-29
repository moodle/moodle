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
 * Version information
 *
 * @package    tool
 * @subpackage iomadmerge
 * @copyright  Derick Turner
 * @author     Derick Turner
 * @basedon    admin tool merge by:
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author     Mike Holzer
 * @author     Forrest Gaston
 * @author     Juan Pablo Torres Herrera
 * @author     Jordi Pujol-Ahulló, SREd, Universitat Rovira i Virgili
 * @author     John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once __DIR__ . '/select_form.php';
require_once __DIR__ . '/review_form.php';
require_once($CFG->dirroot . '/'.$CFG->admin.'/tool/iomadmerge/lib.php');

/**
 * Renderer for the merge user plugin.
 *
 * @package    tool
 * @subpackage iomadmerge
 * @copyright  2013 Jordi Pujol-Ahulló, SREd, Universitat Rovira i Virgili
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_iomadmerge_renderer extends plugin_renderer_base
{
    /** On index page, show only the search form. */
    const INDEX_PAGE_SEARCH_STEP = 1;
    /** On index page, show both search and select forms. */
    const INDEX_PAGE_SEARCH_AND_SELECT_STEP = 2;
    /** On index page, show only the list of users to merge. */
    const INDEX_PAGE_CONFIRMATION_STEP = 3;
    /** On index page, show the merging results. */
    const INDEX_PAGE_RESULTS_STEP = 4;

    /**
     * Renderers a progress bar.
     * @param array $items An array of items
     * @return string
     */
    public function progress_bar(array $items)
    {
        foreach ($items as &$item) {
            $text = $item['text'];
            unset($item['text']);
            if (array_key_exists('link', $item)) {
                $link = $item['link'];
                unset($item['link']);
                $item = html_writer::link($link, $text, $item);
            } else {
                $item = html_writer::tag('span', $text, $item);
            }
        }
        return html_writer::tag('div', join(get_separator(), $items), array('class' => 'merge_progress clearfix'));
    }

    /**
     * Returns the HTML for the progress bar, according to the current step.
     * @param int $step current step
     * @return string HTML for the progress bar.
     */
    public function build_progress_bar($step)
    {
        $steps = array(
            array('text' => '1. ' . get_string('choose_users', 'tool_iomadmerge')),
            array('text' => '2. ' . get_string('review_users', 'tool_iomadmerge')),
            array('text' => '3. ' . get_string('results', 'tool_iomadmerge')),
        );

        switch ($step) {
            case self::INDEX_PAGE_SEARCH_STEP:
            case self::INDEX_PAGE_SEARCH_AND_SELECT_STEP:
                $steps[0]['class'] = 'bold';
                break;
            case self::INDEX_PAGE_CONFIRMATION_STEP:
                $steps[1]['class'] = 'bold';
                break;
            case self::INDEX_PAGE_RESULTS_STEP:
                $steps[2]['class'] = 'bold';
        }

        return $this->progress_bar($steps);
    }

    /**
     * Shows form for merging users.
     * @param moodleform $mform form for merging users.
     * @param int $step step to show in the index page.
     * @param UserSelectTable $ust table for users to merge after searching
     * @return string html to show on index page.
     */
    public function index_page(moodleform $mform, $step, UserSelectTable $ust = null)
    {
        $output = $this->header();
        $output .= $this->heading_with_help(get_string('iomadmerge', 'tool_iomadmerge'), 'header', 'tool_iomadmerge');

        $output .= $this->build_progress_bar($step);

        switch ($step) {
            case self::INDEX_PAGE_SEARCH_STEP:
                $output .= $this->moodleform($mform);
                break;
            case self::INDEX_PAGE_SEARCH_AND_SELECT_STEP:
                $output .= $this->moodleform($mform);
                // Render user select table if available
                if ($ust !== null) {
                    $this->page->requires->js_init_call('M.tool_iomadmerge.init_select_table', array());
                    $output .= $this->render_user_select_table($ust);
                }
                break;
            case self::INDEX_PAGE_CONFIRMATION_STEP:
                break;
        }

        $output .= $this->render_user_review_table($step);
        $output .= $this->footer();
        return $output;
    }

    /**
     * Renders user select table
     * @param UserSelectTable $ust the user select table
     *
     * @return string $tablehtml html string rendering
     */
    public function render_user_select_table(UserSelectTable $ust)
    {
        return $this->moodleform(new selectuserform($ust));
    }

    /**
     * Builds and renders a user review table
     *
     * @return string $reviewtable HTML of the review table section
     */
    public function render_user_review_table($step)
    {
        return $this->moodleform(
                new reviewuserform(
                    new UserReviewTable($this),
                    $this,
                    $step === self::INDEX_PAGE_CONFIRMATION_STEP));
    }

    /**
     * Displays merge users tool error message
     *
     * @param string $message The error message
     * @param bool $showreturn Shows a return button to the index page
     *
     */
    public function mu_error($message, $showreturn = true)
    {
        $errorhtml = '';

        echo $this->header();

        $errorhtml .= $this->output->box($message, 'generalbox align-center');
        if ($showreturn) {
            $returnurl = new moodle_url('/admin/tool/iomadmerge/index.php');
            $returnbutton = new single_button($returnurl, get_string('error_return', 'tool_iomadmerge'));

            $errorhtml .= $this->output->render($returnbutton);
        }

        echo $errorhtml;
        echo $this->footer();
    }

    /**
     * Shows the result of a merging action.
     * @param object $to stdClass with at least id and username fields.
     * @param object $from stdClass with at least id and username fields.
     * @param bool $success true if merging was ok; false otherwise.
     * @param array $data logs of actions done if success, or list of errors on failure.
     * @param id $logid id of the record with the whole detail of this merging action.
     * @return string html with the results.
     */
    public function results_page($to, $from, $success, array $data, $logid)
    {
        if ($success) {
            $resulttype = 'ok';
            $dbmessage = 'dbok';
            $notifytype = 'notifysuccess';
        } else {
            $transactions = (tool_iomadmerge_transactionssupported()) ?
                    '_transactions' :
                    '_no_transactions';

            $resulttype = 'ko';
            $dbmessage = 'dbko' . $transactions;
            $notifytype = 'notifyproblem';
        }


        $output = $this->header();
        $output .= $this->heading(get_string('iomadmerge', 'tool_iomadmerge'));
        $output .= $this->build_progress_bar(self::INDEX_PAGE_RESULTS_STEP);
        $output .= html_writer::empty_tag('br');
        $output .= html_writer::start_tag('div', array('class' => 'result'));
        $output .= html_writer::start_tag('div', array('class' => 'title'));
        $output .= get_string('merging', 'tool_iomadmerge');
        if (!is_null($to) && !is_null($from)) {
            $output .= ' ' . get_string('usermergingheader', 'tool_iomadmerge', $from) . ' ' .
                    get_string('into', 'tool_iomadmerge') . ' ' .
                    get_string('usermergingheader', 'tool_iomadmerge', $to);
        }
        $output .= html_writer::empty_tag('br') . html_writer::empty_tag('br');
        $output .= get_string('logid', 'tool_iomadmerge', $logid);
        $output .= html_writer::empty_tag('br');
        $output .= get_string('log' . $resulttype, 'tool_iomadmerge');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::empty_tag('br');

        $output .= html_writer::start_tag('div', array('class' => 'resultset' . $resulttype));
        foreach ($data as $item) {
            $output .= $item . html_writer::empty_tag('br');
        }
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::tag('div', html_writer::empty_tag('br'));
        $output .= $this->notification(html_writer::tag('center', get_string($dbmessage, 'tool_iomadmerge')), $notifytype);
        $output .= html_writer::tag('center', $this->single_button(new moodle_url('/admin/tool/iomadmerge/index.php'), get_string('continue'), 'get'));
        $output .= $this->footer();

        return $output;
    }

    /**
     * Helper method dealing with the fact we can not just fetch the output of moodleforms
     *
     * @param moodleform $mform
     * @return string HTML
     */
    protected function moodleform(moodleform $mform)
    {
        ob_start();
        $mform->display();
        $o = ob_get_contents();
        ob_end_clean();

        return $o;
    }

    /**
     * This method produces the HTML to show the details of a user.
     * @param int $userid user.id
     * @param object $user an object with firstname and lastname attributes.
     * @return string the corresponding HTML.
     */
    public function show_user($userid, $user)
    {
        return html_writer::link(
            new moodle_url('/user/view.php',
                array('id' => $userid, 'sesskey' => sesskey())),
                fullname($user) .
                ' (' . $user->username . ') ' .
                ' &lt;' . $user->email . '&gt;' .
                ' ' . $user->idnumber);
    }

    /**
     * Produces the page with the list of logs.
     * TODO: make pagination.
     * @global type $CFG
     * @param array $logs array of logs.
     * @return string the corresponding HTML.
     */
    public function logs_page($logs)
    {
        global $CFG;

        $output = $this->header();
        $output .= $this->heading(get_string('viewlog', 'tool_iomadmerge'));
        $output .= html_writer::start_tag('div', array('class' => 'result'));
        if (empty($logs)) {
            $output .= get_string('nologs', 'tool_iomadmerge');
        } else {
            $output .= html_writer::tag('div', get_string('loglist', 'tool_iomadmerge'), array('class' => 'title'));

            $flags = array();
            $flags[] = $this->pix_icon('i/invalid', get_string('eventusermergedfailure', 'tool_iomadmerge')); //failure icon
            $flags[] = $this->pix_icon('i/valid', get_string('eventusermergedsuccess', 'tool_iomadmerge')); //ok icon

            $table = new html_table();
            $table->align = array('center', 'center', 'center', 'center', 'center', 'center');
            $table->head = array(get_string('olduseridonlog', 'tool_iomadmerge'), get_string('newuseridonlog', 'tool_iomadmerge'), get_string('date'), get_string('status'), '');

            $rows = array();
            foreach ($logs as $i => $log) {
                $row = new html_table_row();
                $row->cells = array(
                    ($log->from)
                        ? $this->show_user($log->fromuserid, $log->from)
                        : get_string('deleted', 'tool_iomadmerge', $log->fromuserid),
                    ($log->to)
                        ? $this->show_user($log->touserid, $log->to)
                        : get_string('deleted', 'tool_iomadmerge', $log->touserid),
                    userdate($log->timemodified, get_string('strftimedaydatetime', 'langconfig')),
                    $flags[$log->success],
                    html_writer::link(
                        new moodle_url('/' . $CFG->admin . '/tool/iomadmerge/log.php',
                            array('id' => $log->id, 'sesskey' => sesskey())),
                        get_string('more'),
                        array('target' => '_blank')),
                );
                $rows[] = $row;
            }

            $table->data = $rows;
            $output .= html_writer::table($table);
        }

        $output .= html_writer::end_tag('div');
        $output .= $this->footer();

        return $output;
    }
}
