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
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC *
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}

class turnitin_submission {

    private $id;
    private $data;
    private $submissiondata;
    private $cm;

    public function __construct($id, $data = array()) {
        global $DB;

        $this->id = $id;
        $this->data = $data;
        $this->submissiondata = $DB->get_record('plagiarism_turnitin_files', array('id' => $id));
        $this->cm = get_coursemodule_from_id('', $this->submissiondata->cm);
    }

    /**
     * Get all relevant submission data to requeue submission for the cron to process.
     */
    public function recreate_submission_event() {
        global $DB;

        // Create module object.
        $moduleclass = "turnitin_".$this->cm->modname;
        $moduleobject = new $moduleclass;

        // Some data depends on submission type.
        switch ($this->submissiondata->submissiontype) {
            case 'file':
                $file = $this->get_file_info();

                // Collate data and trigger new event for the cron to process.
                $params = array(
                    'context' => context_module::instance($this->cm->id),
                    'courseid' => $this->cm->course,
                    'objectid' => $file->get_itemid(),
                    'userid' => $this->submissiondata->userid,
                    'other' => array(
                        'content' => '',
                        'pathnamehashes' => array($this->submissiondata->identifier)
                    )
                );
                // Forum attachments need the discussion id to be set.
                if ($this->cm->modname == "forum") {
                    $discussionid = $moduleobject->get_discussionid($this->data['forumdata']);
                    $params['other']['discussionid'] = $discussionid;
                    $params['other']['triggeredfrom'] = 'turnitin_recreate_submission_event';
                }

                $event = $moduleobject->create_file_event($params);
                if ($this->cm->modname != "forum") {
                    $event->set_legacy_files(array($this->submissiondata->identifier => $file));
                }
                $event->trigger();

                break;

            case 'text_content':
                // Get the actual text content.
                $onlinetextdata = $moduleobject->get_onlinetext($this->submissiondata->userid, $this->cm);

                // Collate data and trigger new event for the cron to process.
                $params = array(
                    'context' => context_module::instance($this->cm->id),
                    'courseid' => $this->cm->course,
                    'objectid' => $onlinetextdata->itemid,
                    'userid' => $this->submissiondata->userid,
                    'other' => array(
                        'pathnamehashes' => array(),
                        'content' => trim($onlinetextdata->onlinetext),
                        'format' => $onlinetextdata->onlineformat
                    )
                );

                $event = $moduleobject->create_text_event($params, $this->cm);
                $event->trigger();

                break;

            case 'forum_post':
                $discussionid = $moduleobject->get_discussionid($this->data['forumdata']);

                $forum = $DB->get_record("forum", array("id" => $this->cm->instance));

                // Some forum types don't pass in certain values on main forum page.
                if ((empty($discussionid)) && ($forum->type == 'blog' || $forum->type == 'single')) {
                    $discussion = $DB->get_record_sql('SELECT FD.id
                                                                FROM {forum_posts} FP JOIN {forum_discussions} FD
                                                                ON FP.discussion = FD.id
                                                                WHERE FD.forum = ? AND FD.course = ?
                                                                AND FP.userid = ? AND FP.message LIKE ? ',
                                                                array($forum->id, $forum->course,
                                                                    $this->submissiondata->userid, $this->data['forumpost'])
                                                                );
                    $discussionid = $discussion->id;
                }

                $submission = $DB->get_record_select('forum_posts',
                                                " userid = ? AND message LIKE ? AND discussion = ? ",
                                                array($this->submissiondata->userid, $this->data['forumpost'], $discussionid));

                // Collate data and trigger new event for the cron to process.
                $params = array(
                    'context' => context_module::instance($this->cm->id),
                    'courseid' => $this->cm->course,
                    'objectid' => $submission->id,
                    'userid' => $this->submissiondata->userid,
                    'other' => array(
                        'pathnamehashes' => '',
                        'content' => trim($this->data['forumpost']),
                        'discussionid' => $discussionid,
                        'triggeredfrom' => 'turnitin_recreate_submission_event'
                    )
                );
                $event = \mod_forum\event\assessable_uploaded::create($params);
                $event->trigger();

                break;
        }

        $submissiondata = new stdClass();
        $submissiondata->id = $this->id;
        $submissiondata->statuscode = 'queued';

        return $DB->update_record('plagiarism_turnitin_files', $submissiondata);
    }

    /**
     * Get the file information from Moodle. We really specifically only need the itemid.
     */
    public function get_file_info() {
        $fs = get_file_storage();

        if (!$file = $fs->get_file_by_hash($this->submissiondata->identifier)) {
            return false;
        }

        return $file;
    }
}