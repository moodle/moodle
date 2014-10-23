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
 * Renderer class for manage rules page.
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_monitor\output\managerules;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderer class for manage rules page.
 *
 * @since      Moodle 2.8
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Get html to display on the page.
     *
     * @param renderable $renderable renderable widget
     *
     * @return string to display on the mangerules page.
     */
    protected function render_renderable(renderable $renderable) {
        $o = $this->render_table($renderable);
        $o .= $this->render_add_button($renderable->courseid);

        return $o;
    }

    /**
     * Get html to display on the page.
     *
     * @param renderable $renderable renderable widget
     *
     * @return string to display on the mangerules page.
     */
    protected function render_table(renderable $renderable) {
        $o = '';
        ob_start();
        $renderable->out($renderable->pagesize, true);
        $o = ob_get_contents();
        ob_end_clean();

        return $o;
    }

    /**
     * Html to add a button for adding a new rule.
     *
     * @param int $courseid course id.
     *
     * @return string html for the button.
     */
    protected function render_add_button($courseid) {
        global $CFG;

        $button = \html_writer::tag('button', get_string('addrule', 'tool_monitor'));
        $addurl = new \moodle_url($CFG->wwwroot. '/admin/tool/monitor/edit.php', array('courseid' => $courseid));
        return \html_writer::link($addurl, $button);
    }

    /**
     * Html to add a link to go to the subscription page.
     *
     * @param moodle_url $manageurl The url of the subscription page.
     *
     * @return string html for the link to the subscription page.
     */
    public function render_subscriptions_link($manageurl) {
        echo \html_writer::start_div();
        $a = \html_writer::link($manageurl, get_string('managesubscriptions', 'tool_monitor'));
        $link = \html_writer::tag('span', get_string('managesubscriptionslink', 'tool_monitor', $a));
        echo $link;
        echo \html_writer::end_div();
    }
}
