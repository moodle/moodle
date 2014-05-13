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

class gradereport_history_renderer extends plugin_renderer_base {

    public function render_select_user_button(gradereport_history_user_button $button) {
        $attributes = array('type'     => 'button',
                            'class'    => 'selectortrigger',
                            'value'    => $button->label,
                            'disabled' => $button->disabled ? 'disabled' : null,
                            'title'    => $button->tooltip);

        if ($button->actions) {
            $id = html_writer::random_id('single_button');
            $attributes['id'] = $id;
            foreach ($button->actions as $action) {
                $this->add_action_handler($action, $id);
            }
        }
        $button->initialise_js($this->page);

        // first the input element
        $output = html_writer::empty_tag('input', $attributes);

        // then hidden fields
        $params = $button->url->params();
        if ($button->method === 'post') {
            $params['sesskey'] = sesskey();
        }
        foreach ($params as $var => $val) {
            $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => $var, 'value' => $val));
        }

        // then div wrapper for xhtml strictness
        $output = html_writer::tag('div', $output);

        // now the form itself around it
        if ($button->method === 'get') {
            $url = $button->url->out_omit_querystring(true); // url without params, the anchor part allowed
        } else {
            $url = $button->url->out_omit_querystring();     // url without params, the anchor part not allowed
        }
        if ($url === '') {
            $url = '#'; // there has to be always some action
        }
        $attributes = array('method' => $button->method,
                            'action' => $url,
                            'id'     => $button->formid);
        $output = html_writer::tag('div', $output, $attributes);

        // and finally one more wrapper with class
        return html_writer::tag('div', $output, array('class' => $button->class));
    }

    public function report_title($users = array()) {
        $name = get_string('pluginname', 'gradereport_history');
        if (empty($users)) {
            return $name;
        }

        $i = 0;
        $names = array();
        foreach ($users as $user) {
            $names[] = $user->firstname.' '.$user->lastname;
            $i++;
            if ($i > 4) {
                $count = count($users) - $i;
                if ($count > 0) {
                    $names[] = ' ... and '.$count.' others';
                }
                break;
            }
        }
        $name .= ': '.implode($names, ', ');

        return $name;
    }
}
