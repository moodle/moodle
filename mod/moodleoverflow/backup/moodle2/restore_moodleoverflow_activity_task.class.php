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
 * Provides the restore activity task class
 *
 * @package   mod_moodleoverflow
 * @category  backup
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/moodleoverflow/backup/moodle2/restore_moodleoverflow_stepslib.php');

/**
 * Restore task for the moodleoverflow activity module
 *
 * Provides all the settings and steps to perform complete restore of the activity.
 *
 * @package   mod_moodleoverflow
 * @category  backup
 * @copyright 2016 Your Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_moodleoverflow_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // We have just one structure step here.
        $this->add_step(new restore_moodleoverflow_activity_structure_step('moodleoverflow_structure', 'moodleoverflow.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    public static function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('moodleoverflow', array('intro'), 'moodleoverflow');
        $contents[] = new restore_decode_content('moodleoverflow_posts', array('message'), 'moodleoverflow_post');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    public static function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('MOODLEOVERFLOWVIEWBYID', '/mod/moodleoverflow/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('MOODLEOVERFLOWINDEX', '/mod/moodleoverflow/index.php?id=$1', 'course');

        $rules[] = new restore_decode_rule('MOODLEOVERFLOWVIEWBYF', '/mod/moodleoverflow/view.php?f=$1', 'moodleoverflow');
        // Link to forum discussion.
        $rules[] = new restore_decode_rule('MOODLEOVERFLOWDISCUSSIONVIEW',
            '/mod/moodleoverflow/discussion.php?d=$1',
            'moodleoverflow_discussion');
        // Link to discussion with parent and with anchor posts.
        $rules[] = new restore_decode_rule('MOODLEOVERFLOWDISCUSSIONVIEWPARENT',
            '/mod/moodleoverflow/discussion.php?d=$1&parent=$2',
            array('moodleoverflow_discussion', 'moodleoverflow_post'));
        $rules[] = new restore_decode_rule('MOODLEOVERFLOWDISCUSSIONVIEWINSIDE', '/mod/moodleoverflow/discussion.php?d=$1#$2',
            array('moodleoverflow_discussion', 'moodleoverflow_post'));

        return $rules;

    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * moodleoverflow logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    public static function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('moodleoverflow', 'add',
            'view.php?id={course_module}', '{moodleoverflow}');
        $rules[] = new restore_log_rule('moodleoverflow', 'update',
            'view.php?id={course_module}', '{moodleoverflow}');
        $rules[] = new restore_log_rule('moodleoverflow', 'view',
            'view.php?id={course_module}', '{moodleoverflow}');
        $rules[] = new restore_log_rule('moodleoverflow', 'view moodleoverflow',
            'view.php?id={course_module}', '{moodleoverflow}');
        $rules[] = new restore_log_rule('moodleoverflow', 'mark read',
            'view.php?f={moodleoverflow}', '{moodleoverflow}');
        $rules[] = new restore_log_rule('moodleoverflow', 'start tracking',
            'view.php?f={moodleoverflow}', '{moodleoverflow}');
        $rules[] = new restore_log_rule('moodleoverflow', 'stop tracking',
            'view.php?f={moodloeoverflow}', '{moodleoverflow}');
        $rules[] = new restore_log_rule('moodleoverflow', 'subscribe',
            'view.php?f={moodleoverflow}', '{moodleoverflow}');
        $rules[] = new restore_log_rule('moodleoverflow', 'unsubscribe',
            'view.php?f={moodleoverflow}', '{moodleoverflow}');
        $rules[] = new restore_log_rule('moodleoverflow', 'subscriber',
            'subscribers.php?id={moodleoverflow}', '{moodleoverflow}');
        $rules[] = new restore_log_rule('moodleoverflow', 'subscribers',
            'subscribers.php?id={moodleoverflow}', '{moodleoverflow}');
        $rules[] = new restore_log_rule('moodleoverflow', 'view subscribers',
            'subscribers.php?id={moodleoverflow}', '{moodleoverflow}');
        $rules[] = new restore_log_rule('moodleoverflow', 'add discussion',
            'discussion.php?d={moodleoverflow_discussion}', '{moodleoverflow_discussion}');
        $rules[] = new restore_log_rule('moodleoverflow', 'view discussion',
            'discussion.php?d={moodleoverflow_discussion}', '{moodleoverflow_discussion}');
        $rules[] = new restore_log_rule('moodleoverflow', 'move discussion',
            'discussion.php?d={moodleoverflow_discussion}', '{moodleoverflow_discussion}');
        $rules[] = new restore_log_rule('moodleoverflow', 'delete discussi',
            'view.php?id={course_module}', '{moodleoverflow}',
            null, 'delete discussion');
        $rules[] = new restore_log_rule('moodleoverflow', 'delete discussion',
            'view.php?id={course_module}', '{moodleoverflow}');
        $rules[] = new restore_log_rule('moodleoverflow', 'add post',
            'discussion.php?d={moodleoverflow_discussion}&parent={moodleoverflow_post}', '{moodleoverflow_post}');
        $rules[] = new restore_log_rule('moodleoverflow', 'update post',
            'discussion.php?d={moodleoverflow_discussion}&parent={moodleoverflow_post}', '{moodleoverflow_post}');
        $rules[] = new restore_log_rule('moodleoverflow', 'prune post',
            'discussion.php?d={moodleoverflow_discussion}', '{moodleoverflow_post}');
        $rules[] = new restore_log_rule('moodleoverflow', 'delete post',
            'discussion.php?d={moodleoverflow_discussion}', '[post]');

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
    public static function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule('moodleoverflow', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
