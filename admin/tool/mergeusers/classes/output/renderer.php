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
 * Plugin renderer.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @author    John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @copyright 2013 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\output;

use coding_exception;
use context_system;
use core\exception\moodle_exception;
use core_user;
use dml_exception;
use html_table;
use html_table_row;
use html_writer;
use moodle_url;
use moodleform;
use plugin_renderer_base;
use single_button;
use stdClass;
use tool_mergeusers\local\database_transactions;
use tool_mergeusers\local\last_merge;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/mergeusers/lib.php');

/**
 * Renderer for the merge user plugin.
 *
 * @package   tool_mergeuser
 * @author    Jordi Pujol-Ahulló
 * @copyright 2013 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {
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
    public function progress_bar(array $items): string {
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
        return html_writer::tag('div', join(get_separator(), $items), ['class' => 'merge_progress clearfix']);
    }

    /**
     * Returns the HTML for the progress bar, according to the current step.
     * @param int $step current step
     * @return string HTML for the progress bar.
     */
    public function build_progress_bar(int $step): string {
        $steps = [
            ['text' => '1. ' . get_string('choose_users', 'tool_mergeusers')],
            ['text' => '2. ' . get_string('review_users', 'tool_mergeusers')],
            ['text' => '3. ' . get_string('results', 'tool_mergeusers')],
        ];

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
     *
     * @param moodleform $mform form for merging users.
     * @param int $step step to show in the index page.
     * @param user_select_table|null $ust table for users to merge after searching
     * @return string html to show on index page.
     * @throws coding_exception
     */
    public function index_page(moodleform $mform, int $step, ?user_select_table $ust = null): string {
        $output = $this->header();
        $output .= $this->heading_with_help(get_string('mergeusers', 'tool_mergeusers'), 'header', 'tool_mergeusers');

        $output .= $this->build_progress_bar($step);

        switch ($step) {
            case self::INDEX_PAGE_SEARCH_STEP:
                $output .= $this->moodleform($mform);
                break;
            case self::INDEX_PAGE_SEARCH_AND_SELECT_STEP:
                $output .= $this->moodleform($mform);
                // Render user select table if available.
                if ($ust !== null) {
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
     *
     * @param user_select_table $ust the user select table
     *
     * @return string $tablehtml html string rendering
     */
    public function render_user_select_table(user_select_table $ust) {
        return $this->moodleform(new select_user_form($ust));
    }

    /**
     * Builds and renders a user review table
     *
     * @return string $reviewtable HTML of the review table section
     * @throws coding_exception
     */
    public function render_user_review_table($step) {
        return $this->moodleform(
            new review_user_form(
                new user_review_table($this),
                $this,
                $step === self::INDEX_PAGE_CONFIRMATION_STEP
            )
        );
    }

    /**
     * Displays merge users tool error message
     *
     * @param string $message The error message
     * @param bool $showreturn Shows a return button to the index page
     *
     * @throws coding_exception
     */
    public function mu_error($message, $showreturn = true) {
        $errorhtml = '';

        echo $this->header();

        $errorhtml .= $this->output->box($message, 'generalbox align-center');
        if ($showreturn) {
            $returnurl = new moodle_url('/admin/tool/mergeusers/index.php');
            $returnbutton = new single_button($returnurl, get_string('error_return', 'tool_mergeusers'));

            $errorhtml .= $this->output->render($returnbutton);
        }

        echo $errorhtml;
        echo $this->footer();
    }

    /**
     * Shows the result of a merging action.
     *
     * @param object $to stdClass with at least id and username fields.
     * @param object $from stdClass with at least id and username fields.
     * @param bool $success true if merging was ok; false otherwise.
     * @param array $data logs of actions done if success, or list of errors on failure.
     * @param int $logid id of the record with the whole detail of this merging action.
     * @return string html with the results.
     * @throws \coding_exception
     * @throws \ReflectionException
     */
    public function results_page(object $to, object $from, bool $success, array $data, int $logid): string {
        if ($success) {
            $resulttype = 'ok';
            $dbmessage = 'dbok';
            $notifytype = 'notifysuccess';
        } else {
            $dbmessage = (database_transactions::are_supported()) ?
                    'dbko_transactions' :
                    'dbko_no_transactions';

            $resulttype = 'ko';
            $notifytype = 'notifyproblem';
        }

        $output = $this->header();
        $output .= $this->heading(get_string('mergeusers', 'tool_mergeusers'));
        $output .= $this->build_progress_bar(self::INDEX_PAGE_RESULTS_STEP);
        $output .= html_writer::empty_tag('br');
        $output .= html_writer::start_tag('div', ['class' => 'result']);
        $output .= html_writer::start_tag('div', ['class' => 'title']);
        $output .= get_string('merging', 'tool_mergeusers') . ' ';

        $fromheader = (object)[
            'username' => $this->show_user($from->id, $from),
            'id' => $from->id,
        ];
        $toheader = (object)[
            'username' => $this->show_user($to->id, $to),
            'id' => $to->id,
        ];
        $output .= get_string('usermergingheader', 'tool_mergeusers', $fromheader);
        $output .= html_writer::empty_tag('br');
        $output .= get_string('into', 'tool_mergeusers') . ' ';
        $output .= get_string('usermergingheader', 'tool_mergeusers', $toheader);

        $output .= html_writer::empty_tag('br') . html_writer::empty_tag('br');
        $output .= get_string('logid', 'tool_mergeusers', $logid);
        $output .= html_writer::empty_tag('br');
        $output .= get_string('log' . $resulttype, 'tool_mergeusers');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::empty_tag('br');

        $output .= html_writer::start_tag('div', ['class' => 'resultset' . $resulttype]);
        foreach ($data as $item) {
            $output .= $item . html_writer::empty_tag('br');
        }
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::tag('div', html_writer::empty_tag('br'));
        $output .= $this->notification(
            html_writer::tag('center', get_string($dbmessage, 'tool_mergeusers')),
            $notifytype,
        );
        $output .= html_writer::tag(
            'center',
            $this->single_button(new moodle_url('/admin/tool/mergeusers/index.php'), get_string('continue'), 'get'),
        );
        $output .= $this->footer();

        return $output;
    }

    /**
     * Helper method dealing with the fact we can not just fetch the output of moodleforms
     *
     * @param moodleform $mform
     * @return string HTML
     */
    protected function moodleform(moodleform $mform) {
        ob_start();
        $mform->display();
        $o = ob_get_contents();
        ob_end_clean();

        return $o;
    }

    /**
     * This method produces the HTML to show the details of a user.
     *
     * @param int $userid user.id
     * @param object $user an object with firstname and lastname attributes.
     * @return string the corresponding HTML.
     * @throws moodle_exception
     */
    public function show_user($userid, $user) {
        $suspendedstr = '';
        $suspendedclass = '';
        $deleted = isset($user->deleted) && $user->deleted;
        if ($deleted) {
            $text = get_string('deleted');
        } else {
            $text = fullname($user);
            $text .= " &lt;{$user->email}&gt;";
            $text .= " ({$user->username})";
            $text .= " {$user->idnumber}";
            if ($user->suspended) {
                $suspendedstr = get_string('suspended', 'moodle');
            }
        }
        if (!empty($suspendedstr)) {
            // Found on core here: admin/classes/reportbuilder/local/systemreports/users.php.
            $text .= \html_writer::tag('span', $suspendedstr, ['class' => 'badge badge-secondary ms-1']);
            $suspendedclass = 'usersuspended';
        }

        $attributes = ['class' => $suspendedclass];
        if ($deleted) {
            return html_writer::tag('span', $text, $attributes);
        }
        return html_writer::link(new moodle_url('/user/view.php', ['id' => $userid]), $text, $attributes);
    }

    /**
     * Produces the page with the list of logs.
     * TODO: make pagination.
     *
     * @param array $logs array of logs.
     * @return string the corresponding HTML.
     * @throws \coding_exception
     * @throws moodle_exception
     */
    public function logs_page($logs) {
        global $CFG;

        $output = $this->header();
        $output .= $this->heading(get_string('viewlog', 'tool_mergeusers'));
        $output .= html_writer::start_tag('div', ['class' => 'result']);
        if (empty($logs)) {
            $output .= get_string('nologs', 'tool_mergeusers');
        } else {
            $output .= html_writer::tag('div', get_string('loglist', 'tool_mergeusers'), ['class' => 'title']);

            $flags = [];
            // Prepare failure icon.
            $flags[] = $this->pix_icon('i/invalid', get_string('eventusermergedfailure', 'tool_mergeusers'));
            // Prepare success icon.
            $flags[] = $this->pix_icon('i/valid', get_string('eventusermergedsuccess', 'tool_mergeusers'));

            $output .= html_writer::link(
                new moodle_url('/admin/tool/mergeusers/view.php', ['export' => 1]),
                get_string('exportlogs', 'tool_mergeusers')
            );

            $table = new html_table();
            $table->align = ['center', 'center', 'center', 'center', 'center', 'center'];
            $table->head = [
                get_string('olduseridonlog', 'tool_mergeusers'),
                get_string('newuseridonlog', 'tool_mergeusers'),
                get_string('mergedbyuseridonlog', 'tool_mergeusers'),
                get_string('date'),
                get_string('status'),
                '',
            ];

            $rows = [];
            foreach ($logs as $i => $log) {
                $row = new html_table_row();
                $row->cells = [
                    ($log->from)
                        ? $this->show_user($log->fromuserid, $log->from)
                        : get_string('deleted', 'tool_mergeusers', $log->fromuserid),
                    ($log->to)
                        ? $this->show_user($log->touserid, $log->to)
                        : get_string('deleted', 'tool_mergeusers', $log->touserid),
                    ($log->mergedby)
                        ? $this->show_user($log->mergedbyuserid, $log->mergedby)
                        : get_string('nomergedby', 'tool_mergeusers'),
                    userdate($log->timemodified, get_string('strftimedaydatetime', 'langconfig')),
                    $flags[$log->success],
                    html_writer::link(
                        new moodle_url(
                            '/' . $CFG->admin . '/tool/mergeusers/log.php',
                            ['id' => $log->id]
                        ),
                        get_string('more'),
                        ['target' => '_blank']
                    ),
                ];
                $rows[] = $row;
            }

            $table->data = $rows;
            $output .= html_writer::table($table);
        }

        $output .= html_writer::end_tag('div');
        $output .= $this->footer();

        return $output;
    }

    /**
     * Gathers detail data for merge detail display.
     *
     * @param int $userid
     * @param int $timemodified the time the merge occurred
     * @param int $logid id of log
     * @return array Containing profile link, formatted timestamp and log link.
     * @throws coding_exception
     * @throws moodle_exception
     */
    private function get_merge_detail_data(int $userid, int $timemodified, int $logid, bool $success): array {
        $profileuser = core_user::get_user($userid);
        $time = userdate($timemodified);
        $profilelink = !empty($profileuser) ? html_writer::link(
            new moodle_url('/user/profile.php', ['id' => $userid]),
            fullname($profileuser)
        ) : get_string('unknownprofile', 'tool_mergeusers', $userid);
        $loglink = html_writer::link(
            new moodle_url('/admin/tool/mergeusers/log.php', ['id' => $logid]),
            get_string('openlog', 'tool_mergeusers')
        );
        $successstring = ($success) ? 'success' : 'error';
        return [
            'profilelink' => $profilelink,
            'time' => $time,
            'loglink' => $loglink,
            'success' => strtolower(get_string($successstring)),
        ];
    }

    /**
     * Builds merge detail HTML.
     *
     * @param stdClass $user User object.
     * @param last_merge $lastmerge last merge
     * @return string HTML to display
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function get_merge_detail(stdClass $user, last_merge $lastmerge): string {
        $tome = $lastmerge->tome();
        $fromme = $lastmerge->fromme();
        $tohtml = $tome ? get_string(
            'tomedetail',
            'tool_mergeusers',
            $this->get_merge_detail_data($tome->fromuserid, $tome->timemodified, $tome->id, (bool)(int)$tome->success)
        ) : '';
        $fromhtml = $fromme ? get_string(
            'frommedetail',
            'tool_mergeusers',
            $this->get_merge_detail_data($fromme->touserid, $fromme->timemodified, $fromme->id, (bool)(int)$fromme->success)
        ) : '';
        $output = implode('<br/>', array_filter([$tohtml, $fromhtml]));

        // Ok, there is no merge related to this user.
        if (empty($output)) {
            return get_string('none');
        }
        // Go on, this user was involved in some merge.

        if (!has_capability('moodle/user:delete', context_system::instance())) {
            return $output;
        }

        // Only when the user who is viewing the user profile can delete users, show whether this user is deletable.
        // This calculation is based on the behaviour of this plugin and last successful merges related to this user.
        $deletablestring = ($lastmerge->is_this_user_deletable()) ? 'deletableuser' : 'nondeletableuser';

        return $output . '<br/><br/>' . get_string($deletablestring, 'tool_mergeusers');
    }
}
