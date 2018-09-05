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
 * Class that represents the exclude checkbox on a grade_grade.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_singleview\local\ui;

defined('MOODLE_INTERNAL') || die;

use grade_grade;

/**
 * Class that represents the exclude checkbox on a grade_grade.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exclude extends grade_attribute_format implements be_checked, be_disabled {

    /** @var string $name The name of the input */
    public $name = 'exclude';

    /** @var bool $disabled Is the checkbox disabled? */
    public $disabled = false;

    /**
     * Is it checked?
     *
     * @return bool
     */
    public function is_checked() {
        return $this->grade->is_excluded();
    }

    /**
     * Is it disabled?
     *
     * @return bool
     */
    public function is_disabled() {
        return $this->disabled;
    }

    /**
     * Generate the element used to render the UI
     *
     * @return element
     */
    public function determine_format() {
        return new checkbox_attribute(
            $this->get_name(),
            $this->get_label(),
            $this->is_checked(),
            $this->is_disabled()
        );
    }

    /**
     * Get the label for the form input
     *
     * @return string
     */
    public function get_label() {
        if (!isset($this->grade->label)) {
            $this->grade->label = '';
        }
        return $this->grade->label;
    }

    /**
     * Set the value that was changed in the form.
     *
     * @param string $value The value to set.
     * @return mixed string|bool An error message or false.
     */
    public function set($value) {
        if (empty($this->grade->id)) {
            if (empty($value)) {
                return false;
            }

            $gradeitem = $this->grade->grade_item;

            // Fill in arbitrary grade to be excluded.
            $gradeitem->update_final_grade(
                $this->grade->userid, null, 'singleview', null, FORMAT_MOODLE
            );

            $gradeparams = array(
                'userid' => $this->grade->userid,
                'itemid' => $this->grade->itemid
            );

            $this->grade = grade_grade::fetch($gradeparams);
            $this->grade->grade_item = $gradeitem;
        }

        $state = $value == 0 ? false : true;

        $this->grade->set_excluded($state);

        $this->grade->grade_item->get_parent_category()->force_regrading();
        return false;
    }
}
