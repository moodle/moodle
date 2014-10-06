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
 * The gradebook simple view - UI factory
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

abstract class gradereport_singleview_ui_factory {
    public abstract function create($type);

    protected function wrap($class) {
        return new gradereport_singleview_factory_class_wrap($class);
    }
}

class gradereport_singleview_grade_ui_factory extends gradereport_singleview_ui_factory {
    public function create($type) {
        return $this->wrap("gradereport_singleview_{$type}_ui");
    }
}

class gradereport_singleview_factory_class_wrap {
    public function __construct($class) {
        $this->class = $class;
    }

    public function format() {
        $args = func_get_args();

        $reflect = new ReflectionClass($this->class);
        return $reflect->newInstanceArgs($args);
    }
}

abstract class gradereport_singleview_ui_element {
    public $name;
    public $value;
    public $label;

    public function __construct($name, $value, $label) {
        $this->name = $name;
        $this->value = $value;
        $this->label = $label;
    }

    public function is_checkbox() {
        return false;
    }

    public function is_textbox() {
        return false;
    }

    public function is_dropdown() {
        return false;
    }

    abstract public function html();
}

class gradereport_singleview_empty_element extends gradereport_singleview_ui_element {
    public function __construct($msg = null) {
        if (is_null($msg)) {
            $this->text = get_string('notavailable', 'gradereport_singleview');
        } else {
            $this->text = $msg;
        }
    }

    public function html() {
        return $this->text;
    }
}

class gradereport_singleview_text_attribute extends gradereport_singleview_ui_element {
    private $isdisabled;
    private $tabindex;

    public function __construct($name, $value, $label, $isdisabled = false, $tabindex = null) {
        $this->isdisabled = $isdisabled;
        $this->tabindex = $tabindex;
        parent::__construct($name, $value, $label);
    }

    public function is_textbox() {
        return true;
    }

    public function html() {
        $attributes = array(
            'type' => 'text',
            'name' => $this->name,
            'value' => $this->value,
            'id' => $this->name
        );

        if (!empty($this->tabindex)) {
            $attributes['tabindex'] = $this->tabindex;
        }
        if ($this->isdisabled) {
            $attributes['disabled'] = 'DISABLED';
        }

        $hidden = array(
            'type' => 'hidden',
            'name' => 'old' . $this->name,
            'value' => $this->value
        );

        $label = '';
        if (preg_match("/^feedback/", $this->name)) {
            $labeltitle = get_string('feedbackfor', 'gradereport_singleview', $this->label);
            $label = html_writer::tag('label', $labeltitle,  array('for' => $this->name, 'class' => 'accesshide'));
        } else if (preg_match("/^finalgrade/", $this->name)) {
            $labeltitle = get_string('gradefor', 'gradereport_singleview', $this->label);
            $label = html_writer::tag('label', $labeltitle,  array('for' => $this->name, 'class' => 'accesshide'));
        }

        return (
            $label .
            html_writer::empty_tag('input', $attributes) .
            html_writer::empty_tag('input', $hidden)
        );
    }
}

class gradereport_singleview_checkbox_attribute extends gradereport_singleview_ui_element {
    private $ischecked;
    private $tabindex;

    public function __construct($name, $label, $ischecked = false, $tabindex = null, $locked=0) {
        $this->ischecked = $ischecked;
        $this->tabindex = $tabindex;
        $this->locked = $locked;
        parent::__construct($name, 1, $label);
    }

    public function is_checkbox() {
        return true;
    }

    public function html() {

        $attributes = array(
            'type' => 'checkbox',
            'name' => $this->name,
            'value' => 1,
            'id' => $this->name
        );

        // UCSB fixed user should not be able to override locked grade.
        if ( $this->locked) {
            $attributes['disabled'] = 'DISABLED';
        }

        $alt = array(
            'type' => 'hidden',
            'name' => $this->name,
            'value' => 0
        );

        $hidden = array(
            'type' => 'hidden',
            'name' => 'old' . $this->name
        );

        if (!empty($this->tabindex)) {
            $attributes['tabindex'] = $this->tabindex;
        }

        if ($this->ischecked) {
            $attributes['checked'] = 'CHECKED';
            $hidden['value'] = 1;
        }

        $type = "override";
        if (preg_match("/^exclude/", $this->name)) {
            $type = "exclude";
        }

        return (
            html_writer::empty_tag('input', $alt) .
            html_writer::tag('label', get_string($type . 'for', 'gradereport_singleview', $this->label), array('for' => $this->name, 'class' => 'accesshide')) .
            html_writer::empty_tag('input', $attributes) .
            html_writer::empty_tag('input', $hidden)
        );
    }
}

class gradereport_singleview_dropdown_attribute extends gradereport_singleview_ui_element {
    private $selected;
    private $options;
    private $isdisabled;

    public function __construct($name, $options, $label, $selected = '', $isdisabled = false, $tabindex = null) {
        $this->selected = $selected;
        $this->options = $options;
        $this->tabindex = $tabindex;
        $this->isdisabled = $isdisabled;
        parent::__construct($name, $selected, $label);
    }

    public function is_dropdown() {
        return true;
    }

    public function html() {
        $old = array(
            'type' => 'hidden',
            'name' => 'old' . $this->name,
            'value' => $this->selected
        );

        $attributes = array();
        if (!empty($this->tabindex)) {
            $attributes['tabindex'] = $this->tabindex;
        }

        if (!empty($this->isdisabled)) {
            $attributes['disabled'] = 'DISABLED';
        }

        $select = html_writer::select(
            $this->options, $this->name, $this->selected, false, $attributes
        );

        return ($select . html_writer::empty_tag('input', $old));
    }
}

abstract class gradereport_singleview_grade_attribute_format extends gradereport_singleview_attribute_format implements unique_name, tabbable {
    public $name;
    public $label;

    public function __construct() {
        $args = func_get_args();

        $this->get_arg_or_nothing($args, 0, 'grade');
        $this->get_arg_or_nothing($args, 1, 'tabindex');
    }

    public function get_name() {
        return "{$this->name}_{$this->grade->itemid}_{$this->grade->userid}";
    }

    public function get_tabindex() {
        return isset($this->tabindex) ? $this->tabindex : null;
    }

    private function get_arg_or_nothing($args, $index, $field) {
        if (isset($args[$index])) {
            $this->$field = $args[$index];
        }
    }

    public abstract function set($value);
}

interface unique_name {
    public function get_name();
}

interface unique_value {
    public function get_value();
}

interface be_disabled {
    public function is_disabled();
}

interface be_checked {
    public function is_checked();
}

interface tabbable {
    public function get_tabindex();
}

class gradereport_singleview_bulk_insert_ui extends gradereport_singleview_ui_element {
    public function __construct($item) {
        $this->name = 'bulk_' . $item->id;
        $this->applyname = $this->name_for('apply');
        $this->selectname = $this->name_for('type');
        $this->insertname = $this->name_for('value');
    }

    public function is_applied($data) {
        return isset($data->{$this->applyname});
    }

    public function get_type($data) {
        return $data->{$this->selectname};
    }

    public function get_insert_value($data) {
        return $data->{$this->insertname};
    }

    public function html() {
        $insertgrade = get_string('bulkinsertgrade', 'gradereport_singleview');
        $insertappliesto = get_string('bulkappliesto', 'gradereport_singleview');

        $apply = html_writer::checkbox($this->applyname, 1, false, $insertgrade);
        $insertoptions = array(
            'all' => get_string('all_grades', 'gradereport_singleview'),
            'blanks' => get_string('blanks', 'gradereport_singleview')
        );

        $select = html_writer::select(
            $insertoptions, $this->selectname, 'blanks', false
        );

        $label = html_writer::tag('label', $insertappliesto);
        $text = new gradereport_singleview_text_attribute($this->insertname, "0", 'bulk');
        return implode(' ', array($apply, $text->html(), $label, $select));
    }

    private function name_for($extend) {
        return "{$this->name}_$extend";
    }
}

abstract class gradereport_singleview_attribute_format {
    abstract public function determine_format();

    public function __toString() {
        return $this->determine_format()->html();
    }
}

class gradereport_singleview_finalgrade_ui extends gradereport_singleview_grade_attribute_format implements unique_value, be_disabled {

    public $name = 'finalgrade';

    public function get_value() {
        $this->label = $this->grade->grade_item->itemname;
        // Manual item raw grade support.
        $val = $this->grade->grade_item->is_manual_item() && (!is_null($this->grade->rawgrade)) ?
            $this->grade->rawgrade : $this->grade->finalgrade;

        if ($this->grade->grade_item->scaleid) {
            return $val ? (int)$val : -1;
        } else {
            return $val ? format_float($val, $this->grade->grade_item->get_decimals()) : '';
        }
    }

    public function get_label() {
        if (!isset($this->grade->label)) {
            $this->grade->label = '';
        }
        return $this->grade->label;
    }

    public function is_disabled() {
        $locked = 0;
        $gradeitemlocked = 0;
        $overridden = 0;
        /* Disable editing if grade item or grade score is locked
         * if any of these items are set,  then we will disable editing
         * at some point, we might want to show the reason for the lock
         * this code could be simplified, but its more readable for steve's little mind
         */
        if (!empty($this->grade->locked)) {
            $locked = 1;
        }
        if (!empty($this->grade->grade_item->locked)) {
            $gradeitemlocked = 1;
        }
        if ($this->grade->grade_item->is_overridable_item() and !$this->grade->is_overridden()) {
            $overridden = 1;
        }
        return ($locked || $gradeitemlocked || $overridden);
    }

    public function determine_format() {
        if ($this->grade->grade_item->load_scale()) {
            $scale = $this->grade->grade_item->load_scale();

            $options = array(-1 => get_string('nograde'));

            foreach ($scale->scale_items as $i => $name) {
                $options[$i + 1] = $name;
            }

            return new gradereport_singleview_dropdown_attribute(
                $this->get_name(),
                $options,
                $this->get_value(),
                $this->get_label(),
                $this->is_disabled(),
                $this->get_tabindex()
            );
        } else {
            return new gradereport_singleview_text_attribute(
                $this->get_name(),
                $this->get_value(),
                $this->get_label(),
                $this->is_disabled(),
                $this->get_tabindex()
            );
        }
    }

    public function set($value) {
        global $DB;

        $userid = $this->grade->userid;
        $gradeitem = $this->grade->grade_item;

        $feedback = false;
        $feedbackformat = false;
        if ($gradeitem->gradetype == GRADE_TYPE_SCALE) {
            if ($value == -1) {
                $finalgrade = null;
            } else {
                $finalgrade = $value;
            }
        } else {
            $finalgrade = unformat_float($value);
        }

        $errorstr = '';
        if ($finalgrade) {
            $bounded = $gradeitem->bounded_grade($finalgrade);
            if ($bounded > $finalgrade) {
                $errorstr = 'lessthanmin';
            } else if ($bounded < $finalgrade) {
                $errorstr = 'morethanmax';
            }
        }

        if ($errorstr) {
            $user = $DB->get_record('user', array('id' => $userid), 'id, firstname, alternatename, lastname');
            $gradestr = new stdClass;
            if (!empty($user->alternatename)) {
                $gradestr->username = $user->alternatename . ' (' . $user->firstname . ') ' . $user->lastname;
            } else {
                $gradestr->username = $user->firstname . ' ' . $user->lastname;
            }
            $gradestr->itemname = $this->grade->grade_item->get_name();
            $errorstr = get_string($errorstr, 'grades', $gradestr);
        }

        $gradeitem->update_final_grade($userid, $finalgrade, 'singleview', $feedback, FORMAT_MOODLE);
        return $errorstr;
    }
}

class gradereport_singleview_feedback_ui extends gradereport_singleview_grade_attribute_format implements unique_value, be_disabled {

    public $name = 'feedback';

    public function get_value() {
        return $this->grade->feedback ? $this->grade->feedback : '';
    }

    public function get_label() {
        if (!isset($this->grade->label)) {
            $this->grade->label = '';
        }
        return $this->grade->label;
    }

    public function is_disabled() {
        $locked = 0;
        $gradeitemlocked = 0;
        $overridden = 0;
        /* Disable editing if grade item or grade score is locked
        * if any of these items are set,  then we will disable editing
        * at some point, we might want to show the reason for the lock
        * this code could be simplified, but its more readable for steve's little mind
        */
        if (!empty($this->grade->locked)) {
            $locked = 1;
        }
        if (!empty($this->grade->grade_item->locked)) {
            $gradeitemlocked = 1;
        }
        if ($this->grade->grade_item->is_overridable_item() and !$this->grade->is_overridden()) {
            $overridden = 1;
        }
        return ($locked || $gradeitemlocked || $overridden);
    }

    public function determine_format() {
        return new gradereport_singleview_text_attribute(
            $this->get_name(),
            $this->get_value(),
            $this->get_label(),
            $this->is_disabled(),
            $this->get_tabindex()
        );
    }

    public function set($value) {
        $finalgrade = false;
        $trimmed = trim($value);
        if (empty($trimmed)) {
            $feedback = null;
        } else {
            $feedback = $value;
        }

        $this->grade->grade_item->update_final_grade(
            $this->grade->userid, $finalgrade, 'singleview',
            $feedback, FORMAT_MOODLE
        );
        return false;
    }
}

class gradereport_singleview_override_ui extends gradereport_singleview_grade_attribute_format implements be_checked, be_disabled {
    public $name = 'override';

    public function is_checked() {
        return $this->grade->is_overridden();
    }

    public function is_disabled() {
        $lockedgrade = $lockedgradeitem = 0;
        if (!empty($this->grade->locked)) {
            $lockedgrade = 1;
        }
        if (!empty($this->grade->grade_item->locked)) {
            $lockedgradeitem = 1;
        }
        return ($lockedgrade || $lockedgradeitem);
    }

    public function get_label() {
        if (!isset($this->grade->label)) {
            $this->grade->label = '';
        }
        return $this->grade->label;
    }

    function determine_format() {
        if (!$this->grade->grade_item->is_overridable_item()) {
            return new gradereport_singleview_empty_element();
        }
        return new gradereport_singleview_checkbox_attribute(
            $this->get_name(),
            $this->get_label(),
            $this->is_checked(),
            null,
            $this->is_disabled()
        );
    }

    function set($value) {
        if (empty($this->grade->id)) {
            return false;
        }

        $state = $value == 0 ? false : true;

        $this->grade->set_overridden($state);
        $this->grade->grade_item->get_parent_category()->force_regrading();
        return false;
    }
}

class gradereport_singleview_exclude_ui extends gradereport_singleview_grade_attribute_format implements be_checked {
    var $name = 'exclude';

    function is_checked() {
        return $this->grade->is_excluded();
    }

    function determine_format() {
        return new gradereport_singleview_checkbox_attribute(
            $this->get_name(),
            $this->get_label(),
            $this->is_checked()
        );
    }

    public function get_label() {
        if (!isset($this->grade->label)) {
            $this->grade->label = '';
        }
        return $this->grade->label;
    }

    function set($value) {
        if (empty($this->grade->id)) {
            if (empty($value)) {
                return false;
            }

            $gradeitem = $this->grade->grade_item;

            // Fill in arbitrary grade to be excluded.
            $gradeitem->update_final_grade(
                $this->grade->userid, null, 'singleview', null, FORMAT_MOODLE
            );

            $grade_params = array(
                'userid' => $this->grade->userid,
                'itemid' => $this->grade->itemid
            );

            $this->grade = grade_grade::fetch($grade_params);
            $this->grade->grade_item = $gradeitem;
        }

        $state = $value == 0 ? false : true;

        $this->grade->set_excluded($state);

        $this->grade->grade_item->get_parent_category()->force_regrading();
        return false;
    }
}

class gradereport_singleview_range_ui extends gradereport_singleview_attribute_format {
    function __construct($item) {
        $this->item = $item;
    }

    function determine_format() {
        $decimals = $this->item->get_decimals();

        $min = format_float($this->item->grademin, $decimals);
        $max = format_float($this->item->grademax, $decimals);

        return new gradereport_singleview_empty_element("$min - $max");
    }
}
