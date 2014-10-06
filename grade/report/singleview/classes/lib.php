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
 * The gradebook simple view - base class for the table
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once $CFG->dirroot . '/grade/report/singleview/classes/uilib.php';

interface gradereport_selectable_items {
    public function description();

    public function options();

    public function item_type();
}

interface gradereport_item_filtering {
    public static function filter($item);
}

abstract class gradereport_singleview_screen {
    var $courseid;

    var $itemid;

    var $groupid;

    var $context;

    var $page;

    var $perpage;

    function __construct($courseid, $itemid, $groupid = null) {
        global $DB;

        $this->courseid = $courseid;
        $this->itemid = $itemid;
        $this->groupid = $groupid;

        $this->context = context_course::instance($this->courseid);
        $this->course = $DB->get_record('course', array('id' => $courseid));

        $this->page = optional_param('page', 0, PARAM_INT);
        $this->perpage = optional_param('perpage', 100, PARAM_INT);
        if ($this->perpage > 100) {
            $this->perpage = 100;
        }

        $this->init(empty($itemid));
    }

    public function setup_structure() {
        $this->structure = new grade_structure();
        $this->structure->modinfo = get_fast_modinfo($this->course);
    }

    public function format_link($screen, $itemid, $display = null) {
        $url = new moodle_url('/grade/report/singleview/index.php', array(
            'id' => $this->courseid,
            'item' => $screen,
            'itemid' => $itemid,
            'group' => $this->groupid,
        ));

        if ($display) {
            return html_writer::link($url, $display);
        } else {
            return $url;
        }
    }

    public function fetch_grade_or_default($item, $userid) {
        $grade = grade_grade::fetch(array(
            'itemid' => $item->id, 'userid' => $userid
        ));

        if (!$grade) {
            $default = new stdClass;

            $default->userid = $userid;
            $default->itemid = $item->id;
            $default->feedback = '';

            $grade = new grade_grade($default, false);
        }

        $grade->grade_item = $item;

        return $grade;
    }

    public function make_toggle($key) {
        $attrs = array('href' => '#');

        $all = html_writer::tag('a', get_string('all'), $attrs + array(
            'class' => 'include all ' . $key
        ));

        $none = html_writer::tag('a', get_string('none'), $attrs + array(
            'class' => 'include none ' . $key
        ));

        return html_writer::tag('span', "$all / $none", array(
            'class' => 'inclusion_links'
        ));
    }

    public function make_toggle_links($key) {
        return get_string($key, 'gradereport_singleview') . ' ' .
            $this->make_toggle($key);
    }

    public function heading() {
        return get_string('pluginname', 'gradereport_singleview');
    }

    public abstract function init($self_item_is_empty = false);

    public abstract function html();

    public function supports_paging() {
        return true;
    }

    public function pager() {
        if (!$this->supports_paging()) {
            return '';
        }

        global $OUTPUT;

        list($___, $item) = explode('singleview_', get_class($this));
        return $OUTPUT->paging_bar(
            count($this->items), $this->page, $this->perpage,
            new moodle_url('/grade/report/singleview/index.php', array(
                'perpage' => $this->perpage,
                'id' => $this->courseid,
                'groupid' => $this->groupid,
                'itemid' => $this->itemid,
                'item' => $item
            ))
        );
    }

    public function js() {
        global $PAGE;

        $module = array(
            'name' => 'gradereport_singleview',
            'fullpath' => '/grade/report/singleview/js/singleview.js',
            'requires' => array('base', 'dom', 'event', 'event-simulate', 'io-base')
        );

        $PAGE->requires->js_init_call('M.gradereport_singleview.init', array(), false, $module);
    }

    public function factory() {
        if (empty($this->__factory)) {
            $this->__factory = new gradereport_singleview_grade_ui_factory();
        }

        return $this->__factory;
    }

    public function process($data) {
        $warnings = array();

        $fields = $this->definition();

        foreach ($data as $varname => $throw) {
            if (preg_match("/(\w+)_(\d+)_(\d+)/", $varname, $matches)) {
                $itemid = $matches[2];
                $userid = $matches[3];
            } else {
                continue;
            }

            if (!in_array($matches[1], $fields)) {
                continue;
            }

            $grade_item = grade_item::fetch(array(
                'id' => $itemid, 'courseid' => $this->courseid
            ));

            if (!$grade_item) {
                continue;
            }

            $grade = $this->fetch_grade_or_default($grade_item, $userid);

            $element = $this->factory()->create($matches[1])->format($grade);

            $name = $element->get_name();
            $oldname = "old$name";

            $posted = $data->$name;

            $format = $element->determine_format();

            if ($format->is_textbox() and trim($data->$name) === '') {
                $data->$name = null;
            }

            // Same value; skip.
            if (isset($data->$oldname) && $data->$oldname == $posted) {
                continue;
            }

            $msg = $element->set($posted);

            // Optional type.
            if (!empty($msg)) {
                $warnings[] = $msg;
            }
        }

        // Some post-processing.
        $event_data = new stdClass;
        $event_data->warnings = $warnings;
        $event_data->post_data = $data;
        $event_data->instance = $this;

        return $event_data->warnings;
    }

    public function display_group_selector() {
        return true;
    }
}

abstract class gradereport_singleview_tablelike extends gradereport_singleview_screen implements tabbable {
    public $items;

    protected $headers = array();

    protected $definition = array();

    public abstract function format_line($item);

    public function headers() {
        return $this->headers;
    }

    public function set_headers($overwrite) {
        $this->headers = $overwrite;
        return $this;
    }

    public function definition() {
        return $this->definition;
    }

    public function set_definition($overwrite) {
        $this->definition = $overwrite;
        return $this;
    }

    public function get_tabindex() {
        return (count($this->definition()) * $this->total) + $this->index;
    }

    // Special injection for bulk operations.
    public function process($data) {
        $bulk = $this->factory()->create('bulk_insert')->format($this->item);
        // Bulk insert messages the data to be passed in
        // ie: for all grades of empty grades apply the specified value.
        if ($bulk->is_applied($data)) {
            $filter = $bulk->get_type($data);
            $insert_value = $bulk->get_insert_value($data);
            // Appropriately massage data that may not exist.
            if ($this->supports_paging()) {
                // TODO: this only works with the grade screen...
                $grade_item = grade_item::fetch(array(
                    'courseid' => $this->courseid,
                    'id' => $this->item->id
                ));

                $null = $grade_item->gradetype == GRADE_TYPE_SCALE ? -1 : '';

                foreach ($this->items as $itemid => $item) {
                    $field = "finalgrade_{$grade_item->id}_{$itemid}";
                    if (isset($data->$field)) {
                        continue;
                    }

                    $grade = grade_grade::fetch(array(
                        'itemid' => $grade_item->id,
                        'userid' => $itemid
                    ));

                    $data->$field = empty($grade) ? $null : $grade->finalgrade;
                    $data->{"old$field"} = $data->$field;
                }
            }

            foreach ($data as $varname => $value) {
                if (!preg_match('/^finalgrade_(\d+)_/', $varname, $matches)) {
                    continue;
                }

                $grade_item = grade_item::fetch(array(
                    'courseid' => $this->courseid,
                    'id' => $matches[1]
                ));

                $is_scale = ($grade_item->gradetype == GRADE_TYPE_SCALE);

                $empties = (trim($value) === '' or ($is_scale and $value == -1));

                if ($filter == 'all' or $empties) {
                    $data->$varname = ($is_scale and empty($insert_value)) ?
                        -1 : $insert_value;
                }
            }
        }

        return parent::process($data);
    }

    public function format_definition($line, $grade) {
        foreach ($this->definition() as $i => $field) {
            // Table tab index.
            $tab = ($i * $this->total) + $this->index;
            $html = $this->factory()->create($field)->format($grade, $tab);

            if ($field == 'finalgrade' and !empty($this->structure)) {
                $html .= $this->structure->get_grade_analysis_icon($grade);
            }

            $line[] = $html;
        }
        return $line;
    }

    public function html() {
        $table = new html_table();

        $table->head = $this->headers();

        // To be used for extra formatting.
        $this->index = 0;
        $this->total = count($this->items);

        foreach ($this->items as $item) {
            $this->index ++;
            $table->data[] = $this->format_line($item);
        }

        $underlying = get_class($this);

        $data = new stdClass;
        $data->table = $table;
        $data->instance = $this;

        $button_attr = array('class' => 'singleview_buttons submit');
        $button_html = implode(' ', $this->buttons());

        $buttons = html_writer::tag('div', $button_html, $button_attr);

        return html_writer::tag('form',
            $buttons . html_writer::table($table) . $this->bulk_insert() . $buttons,
            array('method' => 'POST')
        );
    }

    public function bulk_insert() {
        return html_writer::tag(
            'div',
            $this->factory()->create('bulk_insert')->format($this->item)->html(),
            array('class' => 'singleview_bulk')
        );
    }

    public function buttons() {
        $save = html_writer::empty_tag('input', array(
            'type' => 'submit',
            'value' => get_string('update'),
            'tabindex' => $this->get_tabindex(),
        ));

        return array($save);
    }
}
