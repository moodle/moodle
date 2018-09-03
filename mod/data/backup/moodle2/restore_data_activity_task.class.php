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
 * @package    mod_data
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/data/backup/moodle2/restore_data_stepslib.php'); // Because it exists (must)

/**
 * data restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */
class restore_data_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Data only has one structure step
        $this->add_step(new restore_data_activity_structure_step('data_structure', 'data.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('data', array(
                              'intro', 'singletemplate', 'listtemplate', 'listtemplateheader', 'listtemplatefooter',
                              'addtemplate', 'rsstemplate', 'rsstitletemplate', 'asearchtemplate'), 'data');
        $contents[] = new restore_decode_content('data_fields', array(
                              'description', 'param1', 'param2', 'param3',
                              'param4', 'param5', 'param6', 'param7',
                              'param8', 'param9', 'param10'), 'data_field');
        $contents[] = new restore_decode_content('data_content', array(
                              'content', 'content1', 'content2', 'content3', 'content4'));

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('DATAVIEWBYID', '/mod/data/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('DATAVIEWBYD', '/mod/data/index.php?d=$1', 'data');
        $rules[] = new restore_decode_rule('DATAINDEX', '/mod/data/index.php?id=$1', 'course');
        $rules[] = new restore_decode_rule('DATAVIEWRECORD', '/mod/data/view.php?d=$1&amp;rid=$2', array('data', 'data_record'));

        return $rules;

    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * data logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('data', 'add', 'view.php?d={data}&rid={data_record}', '{data}');
        $rules[] = new restore_log_rule('data', 'update', 'view.php?d={data}&rid={data_record}', '{data}');
        $rules[] = new restore_log_rule('data', 'view', 'view.php?id={course_module}', '{data}');
        $rules[] = new restore_log_rule('data', 'record delete', 'view.php?id={course_module}', '{data}');
        $rules[] = new restore_log_rule('data', 'fields add', 'field.php?d={data}&mode=display&fid={data_field}', '{data_field}');
        $rules[] = new restore_log_rule('data', 'fields update', 'field.php?d={data}&mode=display&fid={data_field}', '{data_field}');
        $rules[] = new restore_log_rule('data', 'fields delete', 'field.php?d={data}', '[name]');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule('data', 'view all', 'index.php?id={course}', null);

        return $rules;
    }

    /**
     * Given a commment area, return the itemname that contains the itemid mappings.
     *
     * @param string $commentarea Comment area name e.g. database_entry.
     * @return string name of the mapping used to determine the itemid.
     */
    public function get_comment_mapping_itemname($commentarea) {
        if ($commentarea == 'database_entry') {
            $itemname = 'data_record';
        } else {
            $itemname = parent::get_comment_mapping_itemname($commentarea);
        }
        return $itemname;
    }
}
