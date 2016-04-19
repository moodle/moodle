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

/**
 * Define all the restore steps that will be used by the restore_forum_activity_task
 */

/**
 * Structure step to restore one forum activity
 */
class restore_forum_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('forum', '/activity/forum');
        if ($userinfo) {
            $paths[] = new restore_path_element('forum_discussion', '/activity/forum/discussions/discussion');
            $paths[] = new restore_path_element('forum_post', '/activity/forum/discussions/discussion/posts/post');
            $paths[] = new restore_path_element('forum_discussion_sub', '/activity/forum/discussions/discussion/discussion_subs/discussion_sub');
            $paths[] = new restore_path_element('forum_rating', '/activity/forum/discussions/discussion/posts/post/ratings/rating');
            $paths[] = new restore_path_element('forum_subscription', '/activity/forum/subscriptions/subscription');
            $paths[] = new restore_path_element('forum_digest', '/activity/forum/digests/digest');
            $paths[] = new restore_path_element('forum_read', '/activity/forum/readposts/read');
            $paths[] = new restore_path_element('forum_track', '/activity/forum/trackedprefs/track');
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_forum($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->assesstimestart = $this->apply_date_offset($data->assesstimestart);
        $data->assesstimefinish = $this->apply_date_offset($data->assesstimefinish);
        if ($data->scale < 0) { // scale found, get mapping
            $data->scale = -($this->get_mappingid('scale', abs($data->scale)));
        }

        $newitemid = $DB->insert_record('forum', $data);
        $this->apply_activity_instance($newitemid);
    }

    protected function process_forum_discussion($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->forum = $this->get_new_parentid('forum');
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->timestart = $this->apply_date_offset($data->timestart);
        $data->timeend = $this->apply_date_offset($data->timeend);
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->groupid = $this->get_mappingid('group', $data->groupid);
        $data->usermodified = $this->get_mappingid('user', $data->usermodified);

        $newitemid = $DB->insert_record('forum_discussions', $data);
        $this->set_mapping('forum_discussion', $oldid, $newitemid);
    }

    protected function process_forum_post($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->discussion = $this->get_new_parentid('forum_discussion');
        $data->created = $this->apply_date_offset($data->created);
        $data->modified = $this->apply_date_offset($data->modified);
        $data->userid = $this->get_mappingid('user', $data->userid);
        // If post has parent, map it (it has been already restored)
        if (!empty($data->parent)) {
            $data->parent = $this->get_mappingid('forum_post', $data->parent);
        }

        $newitemid = $DB->insert_record('forum_posts', $data);
        $this->set_mapping('forum_post', $oldid, $newitemid, true);

        // If !post->parent, it's the 1st post. Set it in discussion
        if (empty($data->parent)) {
            $DB->set_field('forum_discussions', 'firstpost', $newitemid, array('id' => $data->discussion));
        }
    }

    protected function process_forum_rating($data) {
        global $DB;

        $data = (object)$data;

        // Cannot use ratings API, cause, it's missing the ability to specify times (modified/created)
        $data->contextid = $this->task->get_contextid();
        $data->itemid    = $this->get_new_parentid('forum_post');
        if ($data->scaleid < 0) { // scale found, get mapping
            $data->scaleid = -($this->get_mappingid('scale', abs($data->scaleid)));
        }
        $data->rating = $data->value;
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // We need to check that component and ratingarea are both set here.
        if (empty($data->component)) {
            $data->component = 'mod_forum';
        }
        if (empty($data->ratingarea)) {
            $data->ratingarea = 'post';
        }

        $newitemid = $DB->insert_record('rating', $data);
    }

    protected function process_forum_subscription($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->forum = $this->get_new_parentid('forum');
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('forum_subscriptions', $data);
        $this->set_mapping('forum_subscription', $oldid, $newitemid, true);

    }

    protected function process_forum_discussion_sub($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->discussion = $this->get_new_parentid('forum_discussion');
        $data->forum = $this->get_new_parentid('forum');
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('forum_discussion_subs', $data);
        $this->set_mapping('forum_discussion_sub', $oldid, $newitemid, true);
    }

    protected function process_forum_digest($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->forum = $this->get_new_parentid('forum');
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('forum_digests', $data);
    }

    protected function process_forum_read($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->forumid = $this->get_new_parentid('forum');
        $data->discussionid = $this->get_mappingid('forum_discussion', $data->discussionid);
        $data->postid = $this->get_mappingid('forum_post', $data->postid);
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('forum_read', $data);
    }

    protected function process_forum_track($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->forumid = $this->get_new_parentid('forum');
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('forum_track_prefs', $data);
    }

    protected function after_execute() {
        // Add forum related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_forum', 'intro', null);

        // Add post related files, matching by itemname = 'forum_post'
        $this->add_related_files('mod_forum', 'post', 'forum_post');
        $this->add_related_files('mod_forum', 'attachment', 'forum_post');
    }

    protected function after_restore() {
        global $DB;

        // If the forum is of type 'single' and no discussion has been ignited
        // (non-userinfo backup/restore) create the discussion here, using forum
        // information as base for the initial post.
        $forumid = $this->task->get_activityid();
        $forumrec = $DB->get_record('forum', array('id' => $forumid));
        if ($forumrec->type == 'single' && !$DB->record_exists('forum_discussions', array('forum' => $forumid))) {
            // Create single discussion/lead post from forum data
            $sd = new stdClass();
            $sd->course   = $forumrec->course;
            $sd->forum    = $forumrec->id;
            $sd->name     = $forumrec->name;
            $sd->assessed = $forumrec->assessed;
            $sd->message  = $forumrec->intro;
            $sd->messageformat = $forumrec->introformat;
            $sd->messagetrust  = true;
            $sd->mailnow  = false;
            $sdid = forum_add_discussion($sd, null, null, $this->task->get_userid());
            // Mark the post as mailed
            $DB->set_field ('forum_posts','mailed', '1', array('discussion' => $sdid));
            // Copy all the files from mod_foum/intro to mod_forum/post
            $fs = get_file_storage();
            $files = $fs->get_area_files($this->task->get_contextid(), 'mod_forum', 'intro');
            foreach ($files as $file) {
                $newfilerecord = new stdClass();
                $newfilerecord->filearea = 'post';
                $newfilerecord->itemid   = $DB->get_field('forum_discussions', 'firstpost', array('id' => $sdid));
                $fs->create_file_from_storedfile($newfilerecord, $file);
            }
        }
    }
}
