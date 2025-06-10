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
 * A new admin setting that trims any input.
 *
 * @package block_panopto
 * @copyright Panopto 2009 - 2016 /With contributions from Spenser Jones (sjones@ambrose.edu),
 * Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Text input that trims any extra whitespace.
 * @copyright Panopto 2016
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configtext_trimmed extends admin_setting_configtext {

    /**
     * Write data to storage
     *
     * @param string $data the data being written.
     */
    public function write_setting($data) {
        if ($this->paramtype === PARAM_INT && $data === '') {
            // Do not complain if '' used instead of 0.
            $data = 0;
        }

        // The ...$data is a string.
        $trimmeddata = trim($data);
        $validated = $this->validate($trimmeddata);
        if ($validated !== true) {
            return $validated;
        }
        return ($this->config_write($this->name, $trimmeddata) ? '' : get_string('errorsetting', 'admin'));
    }
}
