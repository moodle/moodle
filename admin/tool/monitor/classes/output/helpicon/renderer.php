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
 * The file for the renderer class for the tool_monitor help icon.
 *
 * @package    tool_monitor
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_monitor\output\helpicon;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderer class for tool_monitor help icons.
 *
 * @since      Moodle 2.8
 * @package    tool_monitor
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Get the HTML for the help icon.
     *
     * @param renderable $renderable renderable widget
     *
     * @return string the HTML of the help icon to display.
     */
    protected function render_renderable(renderable $renderable) {
        global $CFG;

        // First get the help image icon.
        $src = $this->pix_url('help');

        if ($renderable->type == 'rule') {
            $title = get_string('rulehelp', 'tool_monitor');
        } else { // Must be a subscription.
            $title = get_string('subhelp', 'tool_monitor');
        }

        $alt = get_string('helpwiththis');

        $attributes = array('src' => $src, 'alt' => $alt, 'class' => 'iconhelp');
        $output = \html_writer::empty_tag('img', $attributes);

        // Now create the link around it - we need https on loginhttps pages.
        $urlparams = array();
        $urlparams['type'] = $renderable->type;
        $urlparams['id'] = $renderable->id;
        $urlparams['lang'] = current_language();
        $url = new \moodle_url($CFG->httpswwwroot . '/admin/tool/monitor/help.php', $urlparams);

        // Note: this title is displayed only if JS is disabled, otherwise the link will have the new ajax tooltip.
        $title = get_string('helpprefix2', '', trim($title, ". \t"));

        $attributes = array('href' => $url, 'title' => $title, 'aria-haspopup' => 'true', 'target' => '_blank');
        $output = \html_writer::tag('a', $output, $attributes);

        // Now, finally the span.
        return \html_writer::tag('span', $output, array('class' => 'helptooltip'));
    }
}
