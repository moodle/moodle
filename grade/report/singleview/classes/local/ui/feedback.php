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
 * Class used to render a feedback input box.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_singleview\local\ui;

defined('MOODLE_INTERNAL') || die;

/**
 * Class used to render a feedback input box.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feedback extends grade_attribute_format implements unique_value, be_disabled, be_readonly {

    /**
     * Name of this input
     * @var string $name
     */
    public $name = 'feedback';

    /**
     * Get the value for this input.
     *
     * @return string The value
     */
    public function get_value(): ?string {
        return $this->grade->feedback ? $this->grade->feedback : '';
    }

    /**
     * Get the string to use in the label for this input.
     *
     * @return string The label text
     */
    public function get_label(): string {
        if (!isset($this->grade->label)) {
            $this->grade->label = '';
        }
        return $this->grade->label;
    }

    /**
     * Determine if this input should be disabled based on the other settings.
     *
     * @return boolean Should this input be disabled when the page loads.
     */
    public function is_disabled(): bool {
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

    /**
     * Return true if this is read-only.
     *
     * @return bool
     */
    public function is_readonly(): bool {
        global $USER;
        return empty($USER->editing);
    }

    /**
     * Create a text_attribute for this ui element.
     *
     * @return element
     */
    public function determine_format(): element {
        return new text_attribute(
            $this->get_name(),
            $this->get_value(),
            $this->get_label(),
            $this->is_disabled(),
            $this->is_readonly()
        );
    }

    /**
     * Update the value for this input.
     *
     * @param string $value The new feedback value.
     * @return null|string Any error message
     */
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
    }
}
