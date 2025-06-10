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
 *
 * @package    block_ues_logs
 * @copyright  2014 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/ues_logs/classes/lib.php');

abstract class ues_logs_event_handler {
    public static function ues_student_process($uesstudent) {
        try {
            $log = ues_log::add($uesstudent);

            $log->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function ues_student_release($uesstudent) {
        try {
            $log = ues_log::drop($uesstudent);

            $log->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function ues_section_drop($uessection) {
        return ues_log::delete_all(array('sectionid' => $uessection->id));
    }
}