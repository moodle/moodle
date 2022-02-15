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
 * UI element representing the finalgrade column.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_singleview\local\ui;

defined('MOODLE_INTERNAL') || die;

use stdClass;
/**
 * UI element representing the finalgrade column.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class finalgrade extends grade_attribute_format implements unique_value, be_disabled {

    /** @var string $name Name of this input */
    public $name = 'finalgrade';

    /**
     * Get the value for this input.
     *
     * @return string The value based on the grade_grade.
     */
    public function get_value() {
        $this->label = $this->grade->grade_item->itemname;

        $val = $this->grade->finalgrade;
        if ($this->grade->grade_item->scaleid) {
            return $val ? (int)$val : -1;
        } else {
            return $val ? format_float($val, $this->grade->grade_item->get_decimals()) : '';
        }
    }

    /**
     * Get the label for this input.
     *
     * @return string The label for this form input.
     */
    public function get_label() {
        if (!isset($this->grade->label)) {
            $this->grade->label = '';
        }
        return $this->grade->label;
    }

    /**
     * Is this input field disabled.
     *
     * @return bool Set disabled on this input or not.
     */
    public function is_disabled() {
        $locked = 0;
        $gradeitemlocked = 0;
        $overridden = 0;

        // Disable editing if grade item or grade score is locked
        // if any of these items are set,  then we will disable editing
        // at some point, we might want to show the reason for the lock
        // this code could be simplified, but its more readable for steve's little mind.

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

    /**
     * Create the element for this column.
     *
     * @return element
     */
    public function determine_format() {
        if ($this->grade->grade_item->load_scale()) {
            $scale = $this->grade->grade_item->load_scale();

            $options = array(-1 => get_string('nograde'));

            foreach ($scale->scale_items as $i => $name) {
                $options[$i + 1] = $name;
            }

            return new dropdown_attribute(
                $this->get_name(),
                $options,
                $this->get_label(),
                $this->get_value(),
                $this->is_disabled()
            );
        } else {
            return new text_attribute(
                $this->get_name(),
                $this->get_value(),
                $this->get_label(),
                $this->is_disabled()
            );
        }
    }

    /**
     * Save the altered value for this field.
     *
     * @param string $value The new value.
     * @return string Any error string
     */
    public function set($value) {
        global $DB;

        $userid = $this->grade->userid;
        $gradeitem = $this->grade->grade_item;

        $feedback = false;
        $feedbackformat = false;
        if ($gradeitem->gradetype == GRADE_TYPE_SCALE) {
            $value = (int)unformat_float($value);
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
            $user = get_complete_user_data('id', $userid);
            $gradestr = new stdClass;
            if (has_capability('moodle/site:viewfullnames', \context_course::instance($gradeitem->courseid))) {
                $gradestr->username = fullname($user, true);
            } else {
                $gradestr->username = fullname($user);
            }
            $gradestr->itemname = $this->grade->grade_item->get_name();
            $errorstr = get_string($errorstr, 'grades', $gradestr);
            return $errorstr;
        }

        // Only update grades if there are no errors.
        $gradeitem->update_final_grade($userid, $finalgrade, 'singleview', $feedback, FORMAT_MOODLE,
            null, null, true);
        return '';
    }
}
