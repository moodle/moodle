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
 * Renderer for core_grading subsystem
 *
 * @package    core_grading
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Standard HTML output renderer for core_grading subsystem
 *
 * @package    core_grading
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @category   grading
 */
class core_grading_renderer extends plugin_renderer_base {

    /**
     * Renders the active method selector at the grading method management screen
     *
     * @param grading_manager $manager
     * @param moodle_url $targeturl
     * @return string
     */
    public function management_method_selector(grading_manager $manager, moodle_url $targeturl) {

        $method = $manager->get_active_method();
        $methods = $manager->get_available_methods(false);
        $methods['none'] = get_string('gradingmethodnone', 'core_grading');
        $selector = new single_select(new moodle_url($targeturl, array('sesskey' => sesskey())),
            'setmethod', $methods, empty($method) ? 'none' : $method, null, 'activemethodselector');
        $selector->set_label(get_string('changeactivemethod', 'core_grading'));
        $selector->set_help_icon('gradingmethod', 'core_grading');

        return $this->output->render($selector);
    }

    /**
     * Renders an action icon at the gradng method management screen
     *
     * @param moodle_url $url action URL
     * @param string $text action text
     * @param string $icon the name of the icon to use
     * @return string
     */
    public function management_action_icon(moodle_url $url, $text, $icon) {

        $img = html_writer::empty_tag('img', array('src' => $this->output->pix_url($icon), 'class' => 'action-icon'));
        $txt = html_writer::tag('div', $text, array('class' => 'action-text'));
        return html_writer::link($url, $img . $txt, array('class' => 'action'));
    }

    /**
     * Renders a message for the user, typically as an action result
     *
     * @param string $message
     * @return string
     */
    public function management_message($message) {
        $this->page->requires->strings_for_js(array('clicktoclose'), 'core_grading');
        $this->page->requires->yui_module('moodle-core_grading-manage', 'M.core_grading.init_manage');
        return $this->output->box(format_string($message) . ' - ' . html_writer::tag('span', ''), 'message',
                'actionresultmessagebox');
    }

    /**
     * Renders the template action icon
     *
     * @param moodle_url $url action URL
     * @param string $text action text
     * @param string $icon the name of the icon to use
     * @param string $class extra class of this action
     * @return string
     */
    public function pick_action_icon(moodle_url $url, $text, $icon = '', $class = '') {

        $img = html_writer::empty_tag('img', array('src' => $this->output->pix_url($icon), 'class' => 'action-icon'));
        $txt = html_writer::tag('div', $text, array('class' => 'action-text'));
        return html_writer::link($url, $img . $txt, array('class' => 'action '.$class));
    }
}
