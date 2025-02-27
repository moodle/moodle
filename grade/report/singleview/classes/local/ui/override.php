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
 * An override grade checkbox element
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_singleview\local\ui;

use context_course;

defined('MOODLE_INTERNAL') || die;

/**
 * An override grade checkbox element
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class override extends grade_attribute_format implements be_checked, be_disabled, be_readonly {

    /**
     * The name for this input
     * @var string $name
     */
    public $name = 'override';

    /**
     * Is this input checked
     *
     * @return bool
     */
    public function is_checked(): bool {
        return $this->grade->is_overridden();
    }

    /**
     * Is this input disabled
     *
     * @return bool
     */
    public function is_disabled(): bool {
        $lockedgrade = $lockedgradeitem = 0;
        if (!empty($this->grade->locked)) {
            $lockedgrade = 1;
        }
        if (!empty($this->grade->grade_item->locked)) {
            $lockedgradeitem = 1;
        }
        return ($lockedgrade || $lockedgradeitem);
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
     * Get the label for this form element.
     *
     * @return string
     */
    public function get_label(): string {
        if (!isset($this->grade->label)) {
            $this->grade->label = '';
        }
        return $this->grade->label;
    }

    /**
     * Generate the element for this form input.
     *
     * @return element
     */
    public function determine_format(): element {
        // If the grade is hidden and the user does not have permission to view hidden grades,
        // then we don't show the override checkbox.
        $allowhiddenoverride = (!$this->grade->is_hidden() && !$this->grade->grade_item->is_hidden()) ||
            has_capability('moodle/grade:viewhidden', context_course::instance($this->grade->grade_item->courseid));
        if (!$allowhiddenoverride || !$this->grade->grade_item->is_overridable_item()) {
            return new empty_element();
        }
        return new checkbox_attribute(
            $this->get_name(),
            $this->get_label(),
            $this->is_checked(),
            $this->is_disabled(),
            $this->is_readonly()
        );
    }

    /**
     * Save the modified value of this form element.
     *
     * @param string $value The new value to set
     * @return mixed string|false Any error message
     */
    public function set($value) {
        if (empty($this->grade->id)) {
            return false;
        }

        $state = !($value == 0);

        $this->grade->set_overridden($state);
        $this->grade->grade_item->force_regrading();
        return false;
    }
}
