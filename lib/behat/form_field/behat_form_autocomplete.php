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
 * Auto complete form field class.
 *
 * @package    core_form
 * @category   test
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__  . '/behat_form_text.php');

/**
 * Auto complete form field.
 *
 * @package    core_form
 * @category   test
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_autocomplete extends behat_form_text {

    /**
     * Sets the value to a field.
     *
     * @param string $value
     * @return void
     */
    public function set_value($value) {
        if (!$this->running_javascript()) {
            throw new coding_exception('Setting the valid of an autocomplete field requires javascript.');
        }
        $this->field->setValue($value);
        // After the value is set, there is a 400ms throttle and then search. So adding 2 sec. delay to ensure both
        // throttle + search finishes.
        sleep(2);
        $id = $this->field->getAttribute('id');
        $js = ' require(["jquery"], function($) { $(document.getElementById("'.$id.'")).trigger("behat:set-value"); }); ';
        $this->session->executeScript($js);
    }
}
