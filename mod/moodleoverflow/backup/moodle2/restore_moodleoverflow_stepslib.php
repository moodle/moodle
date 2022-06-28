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
 * Define all the restore steps that will be used by the restore_moodleoverflow_activity_task
 *
 * @package   mod_moodleoverflow
 * @category  backup
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Structure step to restore one moodleoverflow activity
 *
 * @package   mod_moodleoverflow
 * @category  backup
 * @copyright 2016 Your Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_moodleoverflow_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines structure of path elements to be processed during the restore.
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_structure() {

        $paths    = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('moodleoverflow', '/activity/moodleoverflow');
        if ($userinfo) {
            $paths[] = new restore_path_element('moodleoverflow_discussion',
                '/activity/moodleoverflow/discussions/discussion');
            $paths[] = new restore_path_element('moodleoverflow_post',
                '/activity/moodleoverflow/discussions/discussion/posts/post');
            $paths[] = new restore_path_element('moodleoverflow_discuss_sub',
                '/activity/moodleoverflow/discussions/discussion/discuss_subs/discuss_sub');
            $paths[] = new restore_path_element('moodleoverflow_rating',
                '/activity/moodleoverflow/discussions/discussion/posts/post/ratings/rating');
            $paths[] = new restore_path_element('moodleoverflow_subscription',
                '/activity/moodleoverflow/subscriptions/subscription');
            $paths[] = new restore_path_element('moodleoverflow_read',
                '/activity/moodleoverflow/readposts/read');
            $paths[] = new restore_path_element('moodleoverflow_track',
                '/activity/moodleoverflow/tracking/track');
        }

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the given restore path element data
     *
     * @param array $data parsed element data
     */
    protected function process_moodleoverflow($data) {
        global $DB;

        $data         = (object) $data;
        $data->course = $this->get_courseid();

        if (empty($data->timecreated)) {
            $data->timecreated = time();
        }

        if (empty($data->timemodified)) {
            $data->timemodified = time();
        }

        // Create the moodleoverflow instance.
        $newitemid = $DB->insert_record('moodleoverflow', $data);
        $this->apply_activity_instance($newitemid);

        // Add current enrolled user subscriptions if necessary.
    }

    /**
     * Restores a moodleoverflow discussion from element data.
     *
     * @param array $data element data
     */
    protected function process_moodleoverflow_discussion($data) {
        global $DB;

        $data         = (object) $data;
        $oldid        = $data->id;
        $data->course = $this->get_courseid();

        $data->moodleoverflow = $this->get_new_parentid('moodleoverflow');
        $data->timemodified   = $this->apply_date_offset($data->timemodified);
        $data->timestart      = $this->apply_date_offset($data->timestart);
        $data->userid         = $this->get_mappingid('user', $data->userid);
        $data->usermodified   = $this->get_mappingid('user', $data->usermodified);

        $newitemid = $DB->insert_record('moodleoverflow_discussions', $data);
        $this->set_mapping('moodleoverflow_discussion', $oldid, $newitemid);
    }

    /**
     * Resotres a mooodleoverflow post from element data.
     *
     * @param array $data element data
     */
    protected function process_moodleoverflow_post($data) {
        global $DB;

        $data  = (object) $data;
        $oldid = $data->id;

        $data->discussion = $this->get_new_parentid('moodleoverflow_discussion');
        $data->created    = $this->apply_date_offset($data->created);
        $data->modified   = $this->apply_date_offset($data->modified);
        $data->userid     = $this->get_mappingid('user', $data->userid);
        // If post has parent, map it (it has been already restored).
        if (!empty($data->parent)) {
            $data->parent = $this->get_mappingid('moodleoverflow_post', $data->parent);
        }

        $newitemid = $DB->insert_record('moodleoverflow_posts', $data);
        $this->set_mapping('moodleoverflow_post', $oldid, $newitemid, true);

        // If !post->parent, it's the 1st post. Set it in discussion.
        if (empty($data->parent)) {
            $DB->set_field('moodleoverflow_discussions', 'firstpost', $newitemid, array('id' => $data->discussion));
        }
    }

    /**
     * Restores rating from element data.
     *
     * @param array $data element data
     */
    protected function process_moodleoverflow_rating($data) {
        global $DB;

        $data  = (object) $data;
        $oldid = $data->id;

        $data->userid           = $this->get_mappingid('user', $data->userid);
        $data->postid           = $this->get_new_parentid('moodleoverflow_post');
        $data->discussionid     = $this->get_new_parentid('moodleoverflow_discussion');
        $data->moodleoverflowid = $this->get_new_parentid('moodleoverflow');

        $newitemid = $DB->insert_record('moodleoverflow_ratings', $data);
        $this->set_mapping('moodleoverflow_rating', $oldid, $newitemid, true);
    }

    /**
     * Restores moodleoverflow subscriptions from element data.
     *
     * @param array $data element data
     */
    protected function process_moodleoverflow_subscription($data) {
        global $DB;

        $data  = (object) $data;
        $oldid = $data->id;

        $data->moodleoverflow = $this->get_new_parentid('moodleoverflow');
        $data->userid         = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('moodleoverflow_subscriptions', $data);
        $this->set_mapping('moodleoverflow_subscription', $oldid, $newitemid, true);

    }

    /**
     * Restores moodleoverflow disussion subscriptions from element data.
     *
     * @param array $data element data
     */
    protected function process_moodleoverflow_discuss_sub($data) {
        global $DB;

        $data  = (object) $data;
        $oldid = $data->id;

        $data->discussion     = $this->get_new_parentid('moodleoverflow_discussion');
        $data->moodleoverflow = $this->get_new_parentid('moodleoverflow');
        $data->userid         = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('moodleoverflow_discuss_subs', $data);
        $this->set_mapping('moodleoverflow_discuss_sub', $oldid, $newitemid, true);
    }

    /**
     * Restores moodleoverflow read records from element data.
     *
     * @param array $data element data
     */
    protected function process_moodleoverflow_read($data) {
        global $DB;

        $data = (object) $data;

        $data->moodleoverflowid = $this->get_new_parentid('moodleoverflow');
        $data->discussionid     = $this->get_mappingid('moodleoverflow_discussion', $data->discussionid);
        $data->postid           = $this->get_mappingid('moodleoverflow_post', $data->postid);
        $data->userid           = $this->get_mappingid('user', $data->userid);

        $DB->insert_record('moodleoverflow_read', $data);
    }

    /**
     * Restores tracking records from element data.
     *
     * @param array $data element data
     */
    protected function process_moodleoverflow_track($data) {
        global $DB;

        $data = (object) $data;

        $data->moodleoverflowid = $this->get_new_parentid('moodleoverflow');
        $data->userid           = $this->get_mappingid('user', $data->userid);

        $DB->insert_record('moodleoverflow_tracking', $data);
    }

    /**
     * Post-execution actions
     */
    protected function after_execute() {
        // Add moodleoverflow related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_moodleoverflow', 'intro', null);
        $this->add_related_files('mod_moodleoverflow', 'post', 'moodleoverflow_post');
        $this->add_related_files('mod_moodleoverflow', 'attachment', 'moodleoverflow_post');
    }
}
