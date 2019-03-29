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
 * Overriden gradereport_history renderer.
 *
 * @package    theme_bootstrapbase
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_bootstrapbase\output;
defined('MOODLE_INTERNAL') || die();

use gradereport_history\output\user_button;

/**
 * Overriden gradereport_history renderer.
 *
 * @package    theme_bootstrapbase
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradereport_history_renderer extends \gradereport_history\output\renderer {

    public function render_user_button(user_button $button) {
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

}
