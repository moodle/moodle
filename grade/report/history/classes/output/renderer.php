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
 * Renderer for history grade report.
 *
 * @package    gradereport_history
 * @copyright  2013 NetSpot Pty Ltd (https://www.netspot.com.au)
 * @author     Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_history\output;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderer for history grade report.
 *
 * @since      Moodle 2.8
 * @package    gradereport_history
 * @copyright  2013 NetSpot Pty Ltd (https://www.netspot.com.au)
 * @author     Adam Olley <adam.olley@netspot.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Render for the select user button.
     *
     * @param user_button $button instance of  gradereport_history_user_button to render
     *
     * @return string HTML to display
     */
    protected function render_user_button(user_button $button) {
        $attributes = array('type'     => 'button',
                            'class'    => 'selectortrigger',
                            'value'    => $button->label,
                            'disabled' => $button->disabled ? 'disabled' : null,
                            'title'    => $button->tooltip);

        if ($button->actions) {
            $id = \html_writer::random_id('single_button');
            $attributes['id'] = $id;
            foreach ($button->actions as $action) {
                $this->add_action_handler($action, $id);
            }
        }
        // First the input element.
        $output = \html_writer::empty_tag('input', $attributes);

        // Then hidden fields.
        $params = $button->url->params();
        if ($button->method === 'post') {
            $params['sesskey'] = sesskey();
        }
        foreach ($params as $var => $val) {
            $output .= \html_writer::empty_tag('input', array('type' => 'hidden', 'name' => $var, 'value' => $val));
        }

        // Then div wrapper for xhtml strictness.
        $output = \html_writer::tag('div', $output);

        // Now the form itself around it.
        if ($button->method === 'get') {
            $url = $button->url->out_omit_querystring(true); // Url without params, the anchor part allowed.
        } else {
            $url = $button->url->out_omit_querystring();     // Url without params, the anchor part not allowed.
        }
        if ($url === '') {
            $url = '#'; // There has to be always some action.
        }
        $attributes = array('method' => $button->method,
                            'action' => $url,
                            'id'     => $button->formid);
        $output = \html_writer::tag('div', $output, $attributes);

        // Finally one more wrapper with class.
        return \html_writer::tag('div', $output, array('class' => $button->class));
    }

    /**
     * Get the html for the table.
     *
     * @param tablelog $tablelog table object.
     *
     * @return string table html
     */
    protected function render_tablelog(tablelog $tablelog) {
        $o = '';
        ob_start();
        $tablelog->out($tablelog->pagesize, false);
        $o = ob_get_contents();
        ob_end_clean();

        return $o;
    }

}
