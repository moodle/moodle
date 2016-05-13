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
 * Renderer class for manage subscriptions page.
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_monitor\output\managesubs;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderer class for manage subscriptions page.
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
     * @param subs $renderable renderable widget
     *
     * @return string to display on the mangesubs page.
     */
    protected function render_subs(subs $renderable) {
        $o = $this->render_table($renderable);
        return $o;
    }

    /**
     * Get html to display on the page.
     *
     * @param rules $renderable renderable widget
     *
     * @return string to display on the mangesubs page.
     */
    protected function render_rules(rules $renderable) {
        $o = '';
        if (!empty($renderable->totalcount)) {
            $o .= $this->render_table($renderable);
        }
        return $o;
    }

    /**
     * Get html to display on the page for select dropdown..
     *
     * @param rules $renderable renderable widget
     *
     * @return string to display on the mangesubs page.
     */
    protected function render_course_select(rules $renderable) {
        if ($select = $renderable->get_user_courses_select()) {
            return $this->render($select);
        }
    }

    /**
     * Get html to display on the page.
     *
     * @param rules|subs $renderable renderable widget
     *
     * @return string to display on the mangesubs page.
     */
    protected function render_table($renderable) {
        $o = '';
        ob_start();
        $renderable->out($renderable->pagesize, true);
        $o = ob_get_contents();
        ob_end_clean();

        return $o;
    }

    /**
     * Html to add a link to go to the rule manager page.
     *
     * @param moodle_url $ruleurl The url of the rule manager page.
     *
     * @return string html for the link to the rule manager page.
     */
    public function render_rules_link($ruleurl) {
        echo \html_writer::start_div();
        $a = \html_writer::link($ruleurl, get_string('managerules', 'tool_monitor'));
        $link = \html_writer::tag('span', get_string('manageruleslink', 'tool_monitor', $a));
        echo $link;
        echo \html_writer::end_div();
    }
}
