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
 * The main block file.
 *
 * @package    block_ues_people
 * @copyright  2014 Louisiana State University
 * @copyright  2014 Philip Cali, Jason Peak, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

abstract class ues_people {
    public static function primary_role() {
        return get_config('enrol_ues', 'editingteacher_role');
    }

    public static function nonprimary_role() {
        return get_config('enrol_ues', 'teacher_role');
    }

    public static function student_role() {
        return get_config('enrol_ues', 'student_role');
    }

    public static function ues_roles() {
        global $DB;

        $rolesql = ues::where()->id->in(
            self::primary_role(), self::nonprimary_role(), self::student_role()
        )->sql();

        return $DB->get_records_sql('SELECT * FROM {role} WHERE ' . $rolesql);
    }

    public static function defaults() {
        return explode(',', get_config('block_ues_people', 'outputs'));
    }

    public static function initial_bars($label, $name, $url) {
        $current = optional_param($name, 'all', PARAM_TEXT);

        $bar = html_writer::start_tag('div', array('class' => 'initialbar lastinitial'));
        $bar .= $label . ' : ';

        $letters = array('all' => get_string('all'));
        $alpha = explode(',', get_string('alphabet', 'langconfig'));

        $letters += array_combine($alpha, $alpha);

        foreach ($letters as $key => $letter) {
            if ($key == $current) {
                $bar .= html_writer::tag('strong', $letter);
            } else {
                $bar .= '<a href="'. $url . '&amp;' . $name . '=' . $key.'">'.$letter.'</a>';
            }
        }

        $bar .= html_writer::end_tag('div');

        return $bar;
    }

    public static function sortable($url, $label, $field) {
        $current = optional_param('meta', 'lastname', PARAM_TEXT);
        $dir = optional_param('dir', 'ASC', PARAM_TEXT);

        if ($current == $field) {
            global $OUTPUT;

            if ($dir == 'ASC') {
                $path = 'down';
                $newdir = 'DESC';
            } else {
                $path = 'up';
                $newdir = 'ASC';
            }
            $murl = new moodle_url($url, array('meta' => $field, 'dir' => $newdir));

            $link = html_writer::link($murl, $label);
            $link .= ' ' . $OUTPUT->pix_icon('t/'. $path, $dir);
            return $link;
        } else {
            $murl = new moodle_url($url, array('meta' => $field, 'dir' => 'ASC'));

            return html_writer::link($murl, $label);
        }
    }

    public static function outputs($course) {
        $defaults = self::defaults();

        $internal = array('sec_number', 'credit_hours');
        $metanames = array_merge($internal, ues_user::get_meta_names());

        $s = ues::gen_str('block_ues_people');

        $outputs = array();

        foreach ($metanames as $meta) {
            // Admin choice on limits.
            if (!in_array($meta, $defaults)) {
                continue;
            }

            $element = in_array($meta, $internal) ?
                new ues_people_element_output($meta, $s($meta)) :
                new ues_people_element_output($meta);

            $outputs[$meta] = $element;
        }

        // Little information about where the user is coming from.
        $data = new stdClass;
        $data->course = $course;
        $data->outputs = $outputs;

        // Refactoring of events_trigger_legacy.
        global $CFG;

        if (file_exists($CFG->dirroot.'/blocks/cps/classes/ues_people_handler.php')) {
            require_once($CFG->dirroot.'/blocks/cps/classes/ues_people_handler.php');
            $data = blocks_cps_ues_people_handler::ues_people_outputs($data);
        }

        if (file_exists($CFG->dirroot.'/blocks/post_grades/events.php')) {
            require_once($CFG->dirroot.'/blocks/post_grades/events.php');
            $data = post_grades_handler::ues_people_outputs($data);
        }

        return $data->outputs;
    }

    public static function control_elements($metanames) {
        $defaults = array(
            'fullname' => get_string('alternatename') . ' (' . get_string('firstname') . ') ' . get_string('lastname'),
            'username' => get_string('username'),
            'email' => get_string('email'),
            'idnumber' => get_string('idnumber')
        );

        $controls = array();
        foreach ($defaults as $field => $name) {
            $controls[$field] = new ues_people_element_output($field, $name);
        }

        $controls += $metanames;

        return $controls;
    }

    public static function get_filter($metaname) {
        return (int)get_user_preferences('block_ues_people_filter_'.$metaname, 1);
    }

    public static function set_filter($metaname, $value) {
        return set_user_preference('block_ues_people_filter_'.$metaname, (int)$value);
    }

    public static function show_links($params, $count, $perpage) {
        if ($count > DEFAULT_PAGE_SIZE) {
            if ($perpage == 5000) {
                $other = DEFAULT_PAGE_SIZE;
                $str = get_string('showonly', 'moodle') . ' ' . DEFAULT_PAGE_SIZE;
            } else {
                $other = 5000;
                $str = get_string('showall', 'moodle', $count);
            }

            $url = new moodle_url('/blocks/ues_people/index.php',
                $params + array('perpage' => $other));
            echo html_writer::link($url, $str);
        }
    }

    public static function set_perpage($course, $perpage) {
        $current = self::get_perpage($course);

        // Same... do nothing.
        if ($current != $perpage) {
            set_user_preference(
                'block_ues_people_perpage_' . $course->id,
                $perpage
            );
        }

        return $perpage;
    }

    public static function get_perpage($course) {
        return (int)get_user_preferences(
            'block_ues_people_perpage_' . $course->id, DEFAULT_PAGE_SIZE
        );
    }

    public static function is_filtered($metaname) {
        $pref = self::get_filter($metaname);
        return $pref === 0;
    }

    public static function ferpa_control($disagree) {
        // FERPA stuffs.
        $attr = array(
            'id' => 'ferpa-warning'
        );
        $attr['style'] = is_null($disagree) ? null : "color:red";

        $ferpawarning = html_writer::tag('span', get_string('downloadconfirm', 'block_ues_people'), $attr);
        unset($attr);

        // Build the checkbox.
        $attr = array(
            'id'        => 'ferpa',
            'type'      => 'checkbox',
            'class'     => "req",
            'value'     => 1,
            'name'      => 'FERPA'
        );

        $ferpacheck = html_writer::empty_tag('input', $attr);

        // Output the FERPA flag.
        $html  = html_writer::empty_tag('br');
        $html .= html_writer::tag('p', $ferpacheck." ".$ferpawarning, array('id' => 'id_ferpa_required'));
        $html .= html_writer::empty_tag('br');
        return $html;
    }

    public static function controls(array $params, $metanames, $disagree) {
        global $OUTPUT;

        $controls = self::control_elements($metanames);

        $table = new html_table();
        $head = array();
        $data = array();
        foreach ($controls as $meta => $control) {
            $head[] = $control->name;
            $attrs = array(
                'type' => 'checkbox',
                'value' => 1,
                'name' => $control->field
            );

            if (!self::is_filtered($meta)) {
                $attrs['checked'] = 'CHECKED';
            }

            $data[] = html_writer::empty_tag('input', $attrs);
        }

        $table->head = $head;
        $table->data[] = $data;

        $htmltable     = html_writer::table($table);

        $html = $OUTPUT->box_start();
        $html .= html_writer::start_tag('form', array('method' => 'POST'));
        $html .= $htmltable;
        $html .= self::ferpa_control($disagree);
        $html .= html_writer::start_tag('div', array('class' => 'export_button'));
        $html .= html_writer::empty_tag('input', array(
            'id'    => 'export',
            'type'  => 'submit',
            'name'  => 'export',
            'value' => get_string('export_entries', 'block_ues_people')
        ));
        $html .= ' ' . html_writer::empty_tag('input', array(
            'type' => 'submit',
            'name' => 'save',
            'value' => get_string('savechanges')
        ));
        $html .= html_writer::end_tag('div');

        foreach ($params as $name => $value) {
            $html .= html_writer::empty_tag('input', array(
                'type' => 'hidden',
                'name' => $name,
                'value' => $value
            ));
        }

        $html .= html_writer::end_tag('form');
        $html .= $OUTPUT->box_end();

        return $html;
    }
}

class ues_people_element_output {
    public $name;
    public $field;

    public function __construct($field, $name = '') {
        $this->field = $field;
        if (empty($name)) {
            $name = $field;
        }
        $this->name = $name;
    }

    public function format($user) {
        if (isset($user->{$this->field})) {
            return $user->{$this->field};
        } else {
            return '';
        }
    }
}
