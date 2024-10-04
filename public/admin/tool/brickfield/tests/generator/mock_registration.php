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
 * PHPUnit tool_brickfield tests
 *
 * @package   tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @author     Mike Churchward (mike@brickfieldlabs.ie)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_brickfield;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/admin/tool/brickfield/tests/generator/mock_brickfieldconnect.php');

/**
 * Mock registration.
 */
class mock_registration extends registration {
    /**
     * Get registration connection.
     * @return brickfieldconnect
     */
    protected function get_registration_connection(): brickfieldconnect {
        return new mock_brickfieldconnect();
    }

    /**
     * Is not entered.
     * @return bool
     */
    public function is_not_entered() {
        return $this->status_is_not_entered();
    }

    /**
     * Invalidate validation time.
     * @return int
     * @throws \dml_exception
     */
    public function invalidate_validation_time() {
        $this->set_validation_time(time() - (7 * 24 * 60 * 60));
        return $this->get_validation_time();
    }

    /**
     * Invalidate summary time.
     * @return int
     * @throws \dml_exception
     */
    public function invalidate_summary_time() {
        $this->set_summary_time(time() - (7 * 24 * 60 * 60) - 1);
        return $this->get_summary_time();
    }
}