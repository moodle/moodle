<?php

require_once $CFG->dirroot . '/grade/report/single_view/classes/uilib.php';

interface selectable_items {
    public function description();

    public function options();

    public function item_type();
}

interface item_filtering {
    public static function filter($item);
}

abstract class single_view_screen {
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
        $this->perpage = optional_param('perpage', 300, PARAM_INT);

        $this->init(empty($itemid));
    }

    public function setup_structure() {
        $this->structure = new grade_structure();
        $this->structure->modinfo = get_fast_modinfo($this->course);
    }

    public function format_link($screen, $itemid, $display = null) {
        $url = new moodle_url('/grade/report/single_view/index.php', array(
            'id' => $this->courseid,
            'item' => $screen,
            'itemid' => $itemid,
            'group' => $this->groupid
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
        return get_string($key, 'gradereport_single_view') . ' ' .
            $this->make_toggle($key);
    }

    public function heading() {
        return get_string('pluginname', 'gradereport_single_view');
    }

    public abstract function init($self_item_is_empty = false);

    public abstract function html();

    public function supports_paging() {
        return false;
    }

    public function pager() {
        if (!$this->supports_paging()) {
            return '';
        }

        global $OUTPUT;

        list($___, $item) = explode('single_view_', get_class($this));

        return $OUTPUT->paging_bar(
            count($this->items), $this->page, $this->perpage,
            new moodle_url('/grade/report/single_view/index.php', array(
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
            'name' => 'gradereport_single_view',
            'fullpath' => '/grade/report/single_view/js/single_view.js',
            'requires' => array('base', 'dom', 'event', 'event-simulate', 'io-base')
        );

        $PAGE->requires->js_init_call('M.gradereport_single_view.init', array(), false, $module);
    }

    public function factory() {
        if (empty($this->__factory)) {
            $this->__factory = new single_view_grade_ui_factory();
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
            $oldvalue = $data->$oldname;

            $format = $element->determine_format();

            if ($format->is_textbox() and trim($data->$name) === '') {
                $data->$name = null;
            }

            // Same value; skip
            if ($oldvalue == $posted) {
                continue;
            }

            $msg = $element->set($posted);

            // Optional type
            if (!empty($msg)) {
                $warnings[] = $msg;
            }
        }

        // Some post-processing
        $event_data = new stdClass;
        $event_data->warnings = $warnings;
        $event_data->post_data = $data;
        $event_data->instance = $this;

        qe_events_trigger(get_class($this) . '_edited', $event_data);

        return $event_data->warnings;
    }

    public function display_group_selector() {
        return true;
    }
}

abstract class single_view_tablelike extends single_view_screen implements tabbable {
    var $items;

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

    // Special injection for bulk operations
    public function process($data) {
        $bulk = $this->factory()->create('bulk_insert')->format($this->item);

        // Bulk insert messages the data to be passed in
        // ie: for all grades of empty grades apply the specified value
        if ($bulk->is_applied($data)) {
            $filter = $bulk->get_type($data);
            $insert_value = $bulk->get_insert_value($data);

            // Appropriately massage data that may not exist
            if ($this->supports_paging()) {
                // TODO: this only works with the grade screen...
                $grade_item = grade_item::fetch(array(
                    'courseid' => $this->courseid,
                    'id' => $this->item->id
                ));

                $null = $grade_item->gradetype == GRADE_TYPE_SCALE ? -1 : '';

                foreach ($this->all_items as $itemid => $item) {
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
            // Table tab index
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

        // To be used for extra formatting
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

        qe_events_trigger($underlying . '_table_built', $data);

        $button_attr = array('class' => 'single_view_buttons submit');
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
            array('class' => 'single_view_bulk')
        );
    }

    public function buttons() {
        $save = html_writer::empty_tag('input', array(
            'type' => 'submit',
            'value' => get_string('update'),
            'tabindex' => $this->get_tabindex()
        ));

        return array($save);
    }
}
