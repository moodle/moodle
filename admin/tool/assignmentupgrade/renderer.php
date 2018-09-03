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
 * Defines the renderer for the assignment upgrade helper plugin.
 *
 * @package    tool_assignmentupgrade
 * @copyright  2012 NetSpot
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Renderer for the assignment upgrade helper plugin.
 *
 * @package    tool_assignmentupgrade
 * @copyright  2012 NetSpot
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_assignmentupgrade_renderer extends plugin_renderer_base {

    /**
     * Render the index page.
     * @param string $detected information about what sort of site was detected.
     * @param array $actions list of actions to show on this page.
     * @return string html to output.
     */
    public function index_page($detected, array $actions) {
        $output = '';
        $output .= $this->header();
        $output .= $this->heading(get_string('pluginname', 'tool_assignmentupgrade'));
        $output .= $this->box($detected);
        $output .= html_writer::start_tag('ul');
        foreach ($actions as $action) {
            $output .= html_writer::tag('li',
                    html_writer::link($action->url, $action->name) . ' - ' .
                    $action->description);
        }
        $output .= html_writer::end_tag('ul');
        $output .= $this->footer();
        return $output;
    }

    /**
     * Render a page that is just a simple message.
     * @param string $message the message to display.
     * @return string html to output.
     */
    public function simple_message_page($message) {
        $output = '';
        $output .= $this->header();
        $output .= $this->heading($message);
        $output .= $this->back_to_index();
        $output .= $this->footer();
        return $output;
    }

    /**
     * Render the confirm batch operation page
     * @param stdClass $data Submitted form data with list of assignments to upgrade
     * @return string html to output.
     */
    public function confirm_batch_operation_page(stdClass $data) {
        $output = '';
        $output .= $this->header();

        $output .= $this->heading(get_string('confirmbatchupgrade', 'tool_assignmentupgrade'));
        $output .= $this->output->spacer(array(), true);

        $output .= $this->container_start('tool_assignmentupgrade_confirmbatch');

        $output .= $this->render(new tool_assignmentupgrade_batchoperationconfirm($data));
        $output .= $this->container_end();

        $output .= $this->back_to_index();
        $output .= $this->footer();
        return $output;
    }

    /**
     * Render the confirm batch continue / cancel links
     * @param tool_assignmentupgrade_batchoperationconfirm $confirm Wrapper class to determine the continue message and url
     * @return string html to output.
     */
    public function render_tool_assignmentupgrade_batchoperationconfirm(tool_assignmentupgrade_batchoperationconfirm $confirm) {
        $output = '';

        if ($confirm->continueurl) {
            $output .= $this->output->confirm($confirm->continuemessage,
                                              $confirm->continueurl,
                                              tool_assignmentupgrade_url('listnotupgraded'));
        } else {
            $output .= $this->output->box($confirm->continuemessage);
            $output .= $this->output->continue_button(tool_assignmentupgrade_url('listnotupgraded'));
        }
        return $output;
    }

    /**
     * Render the list of assignments that still need to be upgraded page.
     * @param tool_assignmentupgrade_assignments_table $assignments of data about assignments.
     * @param tool_assignmentupgrade_batchoperations_form $batchform Submitted form with list of assignments to upgrade
     * @param tool_assignmentupgrade_pagination_form $paginationform Form which contains the preferences for paginating the table
     * @return string html to output.
     */
    public function assignment_list_page(tool_assignmentupgrade_assignments_table $assignments,
                                         tool_assignmentupgrade_batchoperations_form $batchform,
                                         tool_assignmentupgrade_pagination_form $paginationform) {
        $output = '';
        $output .= $this->header();
        $this->page->requires->js_init_call('M.tool_assignmentupgrade.init_upgrade_table', array());
        $this->page->requires->string_for_js('noassignmentsselected', 'tool_assignmentupgrade');

        $output .= $this->heading(get_string('notupgradedtitle', 'tool_assignmentupgrade'));
        $output .= $this->box(get_string('notupgradedintro', 'tool_assignmentupgrade'));
        $output .= $this->output->spacer(array(), true);

        $output .= $this->container_start('tool_assignmentupgrade_upgradetable');

        $output .= $this->container_start('tool_assignmentupgrade_paginationform');
        $output .= $this->moodleform($paginationform);
        $output .= $this->container_end();

        $output .= $this->flexible_table($assignments, $assignments->get_rows_per_page(), true);
        $output .= $this->container_end();

        if ($assignments->anyupgradableassignments) {
            $output .= $this->container_start('tool_assignmentupgrade_batchform');
            $output .= $this->moodleform($batchform);
            $output .= $this->container_end();
        }

        $output .= $this->back_to_index();
        $output .= $this->footer();
        return $output;
    }

    /**
     * Render the result of an assignment conversion
     * @param stdClass $assignmentsummary data about the assignment to upgrade.
     * @param bool $success Set to true if the outcome of the conversion was a success
     * @param string $log The log from the conversion
     * @return string html to output.
     */
    public function convert_assignment_result($assignmentsummary, $success, $log) {
        $output = '';

        $output .= $this->container_start('tool_assignmentupgrade_result');
        $output .= $this->container(get_string('upgradeassignmentsummary', 'tool_assignmentupgrade', $assignmentsummary));
        if (!$success) {
            $output .= $this->container(get_string('conversionfailed', 'tool_assignmentupgrade', $log));
        } else {
            $output .= $this->container(get_string('upgradeassignmentsuccess', 'tool_assignmentupgrade'));
            $url = new moodle_url('/course/view.php', array('id'=>$assignmentsummary->courseid));
            $output .= $this->container(html_writer::link($url, get_string('viewcourse', 'tool_assignmentupgrade')));
        }
        $output .= $this->container_end();

        return $output;
    }

    /**
     * Render the are-you-sure page to confirm a manual upgrade.
     * @param stdClass $assignmentsummary data about the assignment to upgrade.
     * @return string html to output.
     */
    public function convert_assignment_are_you_sure($assignmentsummary) {
        $output = '';
        $output .= $this->header();
        $output .= $this->heading(get_string('areyousure', 'tool_assignmentupgrade'));

        $params = array('id' => $assignmentsummary->id, 'confirmed' => 1, 'sesskey' => sesskey());
        $output .= $this->confirm(get_string('areyousuremessage', 'tool_assignmentupgrade', $assignmentsummary),
                new single_button(tool_assignmentupgrade_url('upgradesingle', $params), get_string('yes')),
                tool_assignmentupgrade_url('listnotupgraded'));

        $output .= $this->footer();
        return $output;
    }

    /**
     * Helper method dealing with the fact we can not just fetch the output of flexible_table
     *
     * @param flexible_table $table
     * @param int $rowsperpage
     * @param bool $displaylinks Show links in the table
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


    /**
     * Render a link in a div, such as the 'Back to plugin main page' link.
     * @param string|moodle_url $url the link URL.
     * @param string $text the link text.
     * @return string html to output.
     */
    public function end_of_page_link($url, $text) {
        return html_writer::tag('div', html_writer::link($url, $text), array('class' => 'mdl-align'));
    }

    /**
     * Output a link back to the plugin index page.
     * @return string html to output.
     */
    public function back_to_index() {
        return $this->end_of_page_link(tool_assignmentupgrade_url('index'), get_string('backtoindex', 'tool_assignmentupgrade'));
    }
}
