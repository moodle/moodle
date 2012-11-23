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
 * Behat tool renderer
 *
 * @package    tool_behat
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

/**
 * Renderer for behat tool web features
 *
 * @package    tool_behat
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_behat_renderer extends plugin_renderer_base {

    /**
     * Renders the list of available steps according to the submitted filters
     *
     * @param string     $stepsdefinitions HTML from behat with the available steps
     * @param moodleform $form
     * @return string HTML code
     */
    public function render_stepsdefinitions($stepsdefinitions, $form) {

        $title = get_string('pluginname', 'tool_behat');

        // Header.
        $html = $this->output->header();
        $html .= $this->output->heading($title);

        // Info.
        $installurl = tool_behat::$docsurl . '#Installation';
        $installlink = html_writer::tag('a', $installurl, array('href' => $installurl, 'target' => '_blank'));
        $writetestsurl = tool_behat::$docsurl . '#Writting_features';
        $writetestslink = html_writer::tag('a', $writetestsurl, array('href' => $writetestsurl, 'target' => '_blank'));
        $writestepsurl = tool_behat::$docsurl . '#Adding_steps_definitions';
        $writestepslink = html_writer::tag('a', $writestepsurl, array('href' => $writestepsurl, 'target' => '_blank'));
        $infos = array(
            'Read ' . $installlink . ' ' . get_string('installinfo', 'tool_behat'),
            'Read ' . $writetestslink . ' ' . get_string('newtestsinfo', 'tool_behat'),
            'Read ' . $writestepslink . ' ' . get_string('newstepsinfo', 'tool_behat')
        );
        $html .= $this->output->box_start();
        $html .= html_writer::tag('h1', 'Info');
        $html .= html_writer::tag('div', '<ul><li>' . implode('</li><li>', $infos) . '</li></ul>');
        $html .= $this->output->box_end();

        // Form.
        ob_start();
        $form->display();
        $html .= ob_get_contents();
        ob_end_clean();

        // Steps definitions.
        $html .= html_writer::tag('div', $stepsdefinitions, array('id' => 'steps-definitions'));

        $html .= $this->output->footer();

        return $html;
    }
}
