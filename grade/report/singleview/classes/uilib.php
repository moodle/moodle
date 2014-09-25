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
 * @package   singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

abstract class singleview_ui_factory {
    public abstract function create($type);

    protected function wrap($class) {
        return new singleview_factory_class_wrap($class);
    }
}

class singleview_grade_ui_factory extends singleview_ui_factory {
    public function create($type) {
        return $this->wrap("singleview_{$type}_ui");
    }
}

class singleview_factory_class_wrap {
    function __construct($class) {
        $this->class = $class;
    }

    function format() {
        $args = func_get_args();

        $reflect = new ReflectionClass($this->class);
        return $reflect->newInstanceArgs($args);
    }
}

abstract class singleview_ui_element {
    var $name;
    var $value;

    function __construct($name, $value) {
        $this->name = $name;
        $this->value = $value;
    }

    function is_checkbox() {
        return false;
    }

    function is_textbox() {
        return false;
    }

    function is_dropdown() {
        return false;
    }

    abstract function html();
}

class singleview_empty_element extends singleview_ui_element {
    function __construct($msg = null) {
        if (is_null($msg)) {
            $this->text = get_string('notavailable', 'gradereport_singleview');
        } else {
            $this->text = $msg;
        }
    }

    function html() {
        return $this->text;
    }
}

class singleview_text_attribute extends singleview_ui_element {
    var $is_disabled;
    var $tabindex;

    function __construct($name, $value, $is_disabled = false, $tabindex = null) {
        $this->is_disabled = $is_disabled;
        $this->tabindex = $tabindex;
        parent::__construct($name, $value);
    }

    function is_textbox() {
        return true;
    }

    function html() {
        $attributes = array(
            'type' => 'text',
            'name' => $this->name,
            'value' => $this->value
        );

        if (!empty($this->tabindex)) {
            $attributes['tabindex'] = $this->tabindex;
        }
        if ($this->is_disabled) {
            $attributes['disabled'] = 'DISABLED';
        }

        $hidden = array(
            'type' => 'hidden',
            'name' => 'old' . $this->name,
            'value' => $this->value
        );

        return ( 
            html_writer::empty_tag('input', $attributes) . 
            html_writer::empty_tag('input', $hidden)
        );
    }
}

class singleview_checkbox_attribute extends singleview_ui_element {
    var $is_checked;
    var $tabindex;

    function __construct($name, $is_checked = false, $tabindex = null, $locked=0) {
        $this->is_checked = $is_checked;
        $this->tabindex = $tabindex;
        $this->locked = $locked;
        parent::__construct($name, 1);
    }

    function is_checkbox() {
        return true;
    }

    function html() {

        $attributes = array(
            'type' => 'checkbox',
            'name' => $this->name,
            'value' => 1
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

        if ($this->is_checked) {
            $attributes['checked'] = 'CHECKED';
            $hidden['value'] = 1;
        }

        return (
            html_writer::empty_tag('input', $alt) .
            html_writer::empty_tag('input', $attributes) .
            html_writer::empty_tag('input', $hidden)
        );
    }
}

class singleview_dropdown_attribute extends singleview_ui_element {
    var $selected;
    var $options;
    var $is_disabled;

    function __construct($name, $options, $selected = '', $is_disabled = false, $tabindex = null) {
        $this->selected = $selected;
        $this->options = $options;
        $this->tabindex = $tabindex;
        $this->is_disabled = $is_disabled;
        parent::__construct($name, $selected);
    }

    function is_dropdown() {
        return true;
    }

    function html() {
        $old = array(
            'type' => 'hidden',
            'name' => 'old' . $this->name,
            'value' => $this->selected
        );

        $attributes = array();
        if (!empty($this->tabindex)) {
            $attributes['tabindex'] = $this->tabindex;
        }

        if (!empty($this->is_disabled)) {
            $attributes['disabled'] = 'DISABLED';
        }

        $select = html_writer::select(
            $this->options, $this->name, $this->selected, false, $attributes
        );

        return ($select . html_writer::empty_tag('input', $old));
    }
}

abstract class singleview_grade_attribute_format extends singleview_attribute_format implements unique_name, tabbable {
    var $name;

    function __construct() {
        $args = func_get_args();

        $this->get_arg_or_nothing($args, 0, 'grade');
        $this->get_arg_or_nothing($args, 1, 'tabindex');
    }

    function get_name() {
        return "{$this->name}_{$this->grade->itemid}_{$this->grade->userid}";
    }

    function get_tabindex() {
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
    function get_name();
}

interface unique_value {
    function get_value();
}

interface be_disabled {
    function is_disabled();
}

interface be_checked {
    function is_checked();
}

interface tabbable {
    function get_tabindex();
}

class singleview_bulk_insert_ui extends singleview_ui_element {
    function __construct($item) {
        $this->name = 'bulk_' . $item->id;
        $this->applyname = $this->name_for('apply');
        $this->selectname = $this->name_for('type');
        $this->insertname = $this->name_for('value');
    }

    function is_applied($data) {
        return isset($data->{$this->applyname});
    }

    function get_type($data) {
        return $data->{$this->selectname};
    }

    function get_insert_value($data) {
        return $data->{$this->insertname};
    }

    function html() {
        $_s = function($key) {
            return get_string($key, 'gradereport_singleview');
        };

        $apply = html_writer::checkbox($this->applyname, 1, false, ' ' . $_s('bulk'));

        $insert_options = array(
            'all' => $_s('all_grades'),
            'blanks' => $_s('blanks')
        );

        $select = html_writer::select(
            $insert_options, $this->selectname, 'blanks', false
        );

        $label = html_writer::tag('label', $_s('for'));
        $text = new singleview_text_attribute($this->insertname, "0");
        return implode(' ', array($apply, $text->html(), $label, $select));
    }

    private function name_for($extend) {
        return "{$this->name}_$extend";
    }
}

abstract class singleview_attribute_format {
    abstract function determine_format();

    function __toString() {
        return $this->determine_format()->html();
    }
}

class singleview_finalgrade_ui extends singleview_grade_attribute_format implements unique_value, be_disabled {

    var $name = 'finalgrade';

    function get_value() {
        // Manual item raw grade support.
        $val = $this->grade->grade_item->is_manual_item() && (!is_null($this->grade->rawgrade)) ?
            $this->grade->rawgrade : $this->grade->finalgrade;

        if ($this->grade->grade_item->scaleid) {
            return $val ? (int)$val : -1;
        } else {
            return $val ? format_float($val, $this->grade->grade_item->get_decimals()) : '';
        }
    }

    function is_disabled() {
        $locked = 0;
        $gradeitemlocked = 0;
        $overridden = 0;
        /* Disable editing if grade item or grade score is locked
         * if any of these items are set,  then we will disable editing
         * at some point, we might want to show the reason for the lock
         * this code could be simplified, but its more readable for steve's little mind
         */
        if (!empty($this->grade->locked))  $locked = 1;
        if (!empty($this->grade->grade_item->locked))  $gradeitemlocked = 1;
        if ($this->grade->grade_item->is_overridable_item() and !$this->grade->is_overridden()) $overridden = 1;
        return ($locked || $gradeitemlocked || $overridden);
    }

    function determine_format() {
        if ($this->grade->grade_item->load_scale()) {
            $scale = $this->grade->grade_item->load_scale();

            $options = array(-1 => get_string('nograde'));

            foreach ($scale->scale_items as $i => $name) {
                $options[$i + 1] = $name;
            }

            return new singleview_dropdown_attribute(
                $this->get_name(),
                $options,
                $this->get_value(),
                $this->is_disabled(),
                $this->get_tabindex()
            );
        } else {
            return new singleview_text_attribute(
                $this->get_name(),
                $this->get_value(),
                $this->is_disabled(),
                $this->get_tabindex()
            );
        }
    }

    function set($value) {
        global $DB;

        $userid = $this->grade->userid;
        $grade_item = $this->grade->grade_item;

        $feedback = false;
        $feedbackformat = false;
        if ($grade_item->gradetype == GRADE_TYPE_SCALE) {
            if ($value == -1) {
                $finalgrade = null;
            } else {
                $finalgrade = $value;
            }
        } else {
            $finalgrade = unformat_float($value);
        }

        $errorstr = '';
        if (is_null($finalgrade)) {
        } else {
            $bounded = $grade_item->bounded_grade($finalgrade);
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

        $grade_item->update_final_grade($userid, $finalgrade, 'singleview', $feedback, FORMAT_MOODLE);
        return $errorstr;
    }
}

class singleview_feedback_ui extends singleview_grade_attribute_format implements unique_value, be_disabled {

    var $name = 'feedback';

    function get_value() {
        return $this->grade->feedback ? $this->grade->feedback : '';
    }

    function is_disabled() {
        $locked = 0;
        $gradeitemlocked = 0;
        $overridden = 0;
        /* Disable editing if grade item or grade score is locked
        * if any of these items are set,  then we will disable editing
        * at some point, we might want to show the reason for the lock
        * this code could be simplified, but its more readable for steve's little mind
        */
        if (!empty($this->grade->locked))  $locked = 1;
        if (!empty($this->grade->grade_item->locked))  $gradeitemlocked = 1;
        if ($this->grade->grade_item->is_overridable_item() and !$this->grade->is_overridden()) $overridden = 1;
        return ($locked || $gradeitemlocked || $overridden);
    }

    function determine_format() {
        return new singleview_text_attribute(
            $this->get_name(),
            $this->get_value(),
            $this->is_disabled(),
            $this->get_tabindex()
        );
    }

    function set($value) {
        $finalgrade = false;
        $trimmed = trim($value);
        if (empty($trimmed)) {
            $feedback = NULL;
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

class singleview_override_ui extends singleview_grade_attribute_format implements be_checked, be_disabled {
    var $name = 'override';

    function is_checked() {
        return $this->grade->is_overridden();
    }

    function is_disabled() {
        $locked_grade =  $locked_grade_item = 0; 
        if ( ! empty($this->grade->locked) )  $locked_grade = 1;
        if ( ! empty($this->grade->grade_item->locked) ) $locked_grade_item = 1;
        return ($locked_grade || $locked_grade_item);
    }

    function determine_format() {
        if (!$this->grade->grade_item->is_overridable_item()) {
            return new singleview_empty_element();
        }
        return new singleview_checkbox_attribute(
            $this->get_name(),
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

class singleview_exclude_ui extends singleview_grade_attribute_format implements be_checked {
    var $name = 'exclude';

    function is_checked() {
        return $this->grade->is_excluded();
    }

    function determine_format() {
        return new singleview_checkbox_attribute(
            $this->get_name(),
            $this->is_checked()
        );
    }

    function set($value) {
        if (empty($this->grade->id)) {
            if (empty($value)) {
                return false;
            }

            $grade_item = $this->grade->grade_item;

            // Fill in arbitrary grade to be excluded.
            $grade_item->update_final_grade(
                $this->grade->userid, null, 'singleview', null, FORMAT_MOODLE
            );

            $grade_params = array(
                'userid' => $this->grade->userid,
                'itemid' => $this->grade->itemid
            );

            $this->grade = grade_grade::fetch($grade_params);
            $this->grade->grade_item = $grade_item;
        }

        $state = $value == 0 ? false : true;

        $this->grade->set_excluded($state);

        $this->grade->grade_item->get_parent_category()->force_regrading();
        return false;
    }
}

class singleview_range_ui extends singleview_attribute_format {
    function __construct($item) {
        $this->item = $item;
    }

    function determine_format() {
        $decimals = $this->item->get_decimals();

        $min = format_float($this->item->grademin, $decimals);
        $max = format_float($this->item->grademax, $decimals);

        return new singleview_empty_element("$min - $max");
    }
}
