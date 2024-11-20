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
 * @package    mod_forum
 * @subpackage backup-moodle2
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/forum/backup/moodle2/restore_forum_stepslib.php'); // Because it exists (must)

/**
 * forum restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */
class restore_forum_activity_task extends restore_activity_task {

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
        // Choice only has one structure step
        $this->add_step(new restore_forum_activity_structure_step('forum_structure', 'forum.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    public static function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('forum', array('intro'), 'forum');
        $contents[] = new restore_decode_content('forum_posts', array('message'), 'forum_post');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    public static function define_decode_rules() {
        $rules = array();

        // List of forums in course
        $rules[] = new restore_decode_rule('FORUMINDEX', '/mod/forum/index.php?id=$1', 'course');
        // Forum by cm->id and forum->id
        $rules[] = new restore_decode_rule('FORUMVIEWBYID', '/mod/forum/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('FORUMVIEWBYF', '/mod/forum/view.php?f=$1', 'forum');
        // Link to forum discussion
        $rules[] = new restore_decode_rule('FORUMDISCUSSIONVIEW', '/mod/forum/discuss.php?d=$1', 'forum_discussion');
        // Link to discussion with parent and with anchor posts
        $rules[] = new restore_decode_rule('FORUMDISCUSSIONVIEWPARENT', '/mod/forum/discuss.php?d=$1&parent=$2',
                                           array('forum_discussion', 'forum_post'));
        $rules[] = new restore_decode_rule('FORUMDISCUSSIONVIEWINSIDE', '/mod/forum/discuss.php?d=$1#$2',
                                           array('forum_discussion', 'forum_post'));

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * forum logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    public static function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('forum', 'add', 'view.php?id={course_module}', '{forum}');
        $rules[] = new restore_log_rule('forum', 'update', 'view.php?id={course_module}', '{forum}');
        $rules[] = new restore_log_rule('forum', 'view', 'view.php?id={course_module}', '{forum}');
        $rules[] = new restore_log_rule('forum', 'view forum', 'view.php?id={course_module}', '{forum}');
        $rules[] = new restore_log_rule('forum', 'mark read', 'view.php?f={forum}', '{forum}');
        $rules[] = new restore_log_rule('forum', 'start tracking', 'view.php?f={forum}', '{forum}');
        $rules[] = new restore_log_rule('forum', 'stop tracking', 'view.php?f={forum}', '{forum}');
        $rules[] = new restore_log_rule('forum', 'subscribe', 'view.php?f={forum}', '{forum}');
        $rules[] = new restore_log_rule('forum', 'unsubscribe', 'view.php?f={forum}', '{forum}');
        $rules[] = new restore_log_rule('forum', 'subscriber', 'subscribers.php?id={forum}', '{forum}');
        $rules[] = new restore_log_rule('forum', 'subscribers', 'subscribers.php?id={forum}', '{forum}');
        $rules[] = new restore_log_rule('forum', 'view subscribers', 'subscribers.php?id={forum}', '{forum}');
        $rules[] = new restore_log_rule('forum', 'add discussion', 'discuss.php?d={forum_discussion}', '{forum_discussion}');
        $rules[] = new restore_log_rule('forum', 'view discussion', 'discuss.php?d={forum_discussion}', '{forum_discussion}');
        $rules[] = new restore_log_rule('forum', 'move discussion', 'discuss.php?d={forum_discussion}', '{forum_discussion}');
        $rules[] = new restore_log_rule('forum', 'delete discussi', 'view.php?id={course_module}', '{forum}',
                                        null, 'delete discussion');
        $rules[] = new restore_log_rule('forum', 'delete discussion', 'view.php?id={course_module}', '{forum}');
        $rules[] = new restore_log_rule('forum', 'add post', 'discuss.php?d={forum_discussion}&parent={forum_post}', '{forum_post}');
        $rules[] = new restore_log_rule('forum', 'update post', 'discuss.php?d={forum_discussion}#p{forum_post}&parent={forum_post}', '{forum_post}');
        $rules[] = new restore_log_rule('forum', 'update post', 'discuss.php?d={forum_discussion}&parent={forum_post}', '{forum_post}');
        $rules[] = new restore_log_rule('forum', 'prune post', 'discuss.php?d={forum_discussion}', '{forum_post}');
        $rules[] = new restore_log_rule('forum', 'delete post', 'discuss.php?d={forum_discussion}', '[post]');

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

        $rules[] = new restore_log_rule('forum', 'view forums', 'index.php?id={course}', null);
        $rules[] = new restore_log_rule('forum', 'subscribeall', 'index.php?id={course}', '{course}');
        $rules[] = new restore_log_rule('forum', 'unsubscribeall', 'index.php?id={course}', '{course}');
        $rules[] = new restore_log_rule('forum', 'user report', 'user.php?course={course}&id={user}&mode=[mode]', '{user}');
        $rules[] = new restore_log_rule('forum', 'search', 'search.php?id={course}&search=[searchenc]', '[search]');

        return $rules;
    }
}
