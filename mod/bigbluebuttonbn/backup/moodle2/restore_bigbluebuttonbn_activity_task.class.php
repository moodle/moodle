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
 * Class for restore BigBlueButtonBN.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Fred Dixon  (ffdixon [at] blindsidenetworks [dt] com)
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/bigbluebuttonbn/backup/moodle2/restore_bigbluebuttonbn_stepslib.php');

/**
 * Restore task that provides all the settings and steps to perform one complete restore of the activity.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_bigbluebuttonbn_activity_task extends restore_activity_task {
    /**
     * Define (add) particular settings this activity can have.
     *
     * @return void
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define (add) particular steps this activity can have.
     *
     * @return void
     */
    protected function define_my_steps() {
        // BigBlueButtonBN only has one structure step.
        $this->add_step(new restore_bigbluebuttonbn_activity_structure_step('bigbluebuttonbn_structure', 'bigbluebuttonbn.xml'));
    }

    /**
     * Define the contents in the activity that must be processed by the link decoder.
     *
     * @return array
     */
    public static function define_decode_contents() {
        $contents = [];
        $contents[] = new restore_decode_content('bigbluebuttonbn', ['intro'], 'bigbluebuttonbn');
        $contents[] = new restore_decode_content('bigbluebuttonbn_logs', ['log'], 'bigbluebuttonbn_logs');
        $contents[] = new restore_decode_content('bigbluebuttonbn_recordings', ['importeddata'], 'bigbluebuttonbn_recordings');
        return $contents;
    }

    /**
     * Define the decoding rules for links belonging to the activity to be executed by the link decoder.
     *
     * @return array
     */
    public static function define_decode_rules() {
        $rules = [];
        $rules[] = new restore_decode_rule('BIGBLUEBUTTONBNVIEWBYID', '/mod/bigbluebuttonbn/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('BIGBLUEBUTTONBNINDEX', '/mod/bigbluebuttonbn/index.php?id=$1', 'course');
        return $rules;
    }

    /**
     * Define the restoring rules for logs belonging to the activity to be executed by the link decoder.
     *
     * @return array
     */
    public static function define_restore_log_rules() {
        $rules = [];
        $rules[] = new restore_log_rule('bigbluebuttonbn', 'add', 'view.php?id={course_module}', '{bigbluebuttonbn}');
        $rules[] = new restore_log_rule('bigbluebuttonbn', 'update', 'view.php?id={course_module}', '{bigbluebuttonbn}');
        $rules[] = new restore_log_rule('bigbluebuttonbn', 'view', 'view.php?id={course_module}', '{bigbluebuttonbn}');
        $rules[] = new restore_log_rule('bigbluebuttonbn', 'report', 'report.php?id={course_module}', '{bigbluebuttonbn}');
        return $rules;
    }

    /**
     * Define the restoring rules for course associated to the activity to be executed by the link decoder.
     *
     * @return array
     */
    public static function define_restore_log_rules_for_course() {
        $rules = [];
        $rules[] = new restore_log_rule('bigbluebuttonbn', 'view all', 'index.php?id={course}', null);
        return $rules;
    }
}
