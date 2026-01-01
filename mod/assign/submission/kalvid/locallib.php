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
 * This file contains the definition for the library class for kalvid submission plugin
 *
 * This class provides all the functionality for the new assign module.
 *
 * @package assignsubmission_kalvid
 * @copyright 2025 Kaltura Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * library class for kalvid submission plugin extending submission plugin base class
 *
 * @package assignsubmission_kalvid
 * @copyright 2025 Kaltura Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_kalvid extends assign_submission_plugin {

    /**
     * Get the name of the kaltura video submission plugin
     * @return string
     */
    public function get_name() {
        return get_string('kalvid', 'assignsubmission_kalvid');
    }

    /**
     * Get kalvid submission information from the database
     *
     * @param  int $submissionid
     * @return mixed
     */
    private function get_kalvid_submission($submissionid) {
        global $DB;

        return $DB->get_record('assignsubmission_kalvid', array('submission'=>$submissionid));
    }

    /**
     * Remove a submission.
     *
     * @param stdClass $submission The submission
     * @return boolean
     */
    public function remove(stdClass $submission) {
        global $DB;

        $submissionid = $submission ? $submission->id : 0;
        if ($submissionid) {
            $DB->delete_records('assignsubmission_kalvid', array('submission' => $submissionid));
        }
        return true;
    }

    /**
     * Add elements to user submission form
     *
     * @param mixed $submission can be null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return true if elements were added to the form
     */
    public function get_form_elements($submission, MoodleQuickForm $mform, stdClass $data) {
        global $COURSE, $PAGE;
        $submissionrec = null;
        if ($submission) {
            $submissionrec = $this->get_kalvid_submission($submission->id);
        }

        $entryid  = $submissionrec->entry_id  ?? '';

        $PAGE->requires->css('/mod/assign/submission/kalvid/styles.css');
        // include replace_video string from kalvidres
        // it is hardcoded in ltipanel.js and used here
        $PAGE->requires->string_for_js('replace_video', 'kalvidres');

        $pageclass = 'kaltura-kalvid-body';
        $PAGE->add_body_class($pageclass);

        $mform->addElement('hidden', 'entry_id', $entryid, array('id' => 'entry_id'));
        $mform->setType('entry_id', PARAM_NOTAGS);
        $mform->addElement('hidden', 'source', '', array('id' => 'source'));
        $mform->setType('source', PARAM_URL);
        $mform->addElement('hidden', 'width', '', array('id' => 'width'));
        $mform->setType('width', PARAM_TEXT);
        $mform->addElement('hidden', 'height', '', array('id' => 'height'));
        $mform->setType('height', PARAM_TEXT);

        $html = html_writer::start_tag('center', ['class' => 'm-t-2 m-b-1']);

        $source = new moodle_url('/local/kaltura/pix/vidThumb.png');
        $imgattr = [
            'id' => 'video_thumbnail',
            'src' => $source->out(),
            'alt' => get_string('video_thumbnail', 'assignsubmission_kalvid'),
            'title' => get_string('video_thumbnail', 'assignsubmission_kalvid'),
            'style' => empty($submissionrec->source) ? '' : 'display:none'
        ];

        // If the submission object contains a source URL then display the video as part of an LTI launch.
        if (!empty($submissionrec->source)) {
            $imgattr['style'] = 'display: none';

            $params = array(
                'courseid' => $COURSE->id,
                'height' => $submissionrec->height,
                'width' => $submissionrec->width,
                'withblocks' => 0,
                'source' => local_kaltura_add_kaf_uri_token($submissionrec->source),
                'cmid' => $this->assignment->get_course_module()->id
            );
            $url = new moodle_url('/mod/assign/submission/kalvid/lti_launch.php', $params);
        }

        $html .= html_writer::empty_tag('img', $imgattr);

        $iframeattr = [
            'id' => 'contentframe',
            'class' => 'kaltura-player-iframe',
            'src' => ($url instanceof moodle_url) ? $url->out(false) : '',
            'allowfullscreen' => 'true',
            'allow' => 'autoplay *; fullscreen *; encrypted-media *; camera *; microphone *; display-capture *; clipboard-write *;',
            'height' => '100%',
            'width' => !empty($submissionrec->width) ? $submissionrec->width : ''
        ];

        if (empty($submissionrec->source)) {
            $iframeattr['style'] = 'display: none';
        }

        $iframe = html_writer::tag('iframe', '', $iframeattr);
        $html .= html_writer::tag('div', $iframe, ['class' => 'kaltura-player-container']);

        $attr = array(
            'class' => 'btn btn-primary mr-2',
            'type' => 'button',
            'id' => 'id_add_video',
            'name' => 'add_video',
            'value' => empty($submissionrec->source) ? get_string('addmedia', 'assignsubmission_kalvid') : get_string('replace_video', 'kalvidres')
        );

        $html .= html_writer::empty_tag('input', $attr);

        $html .= html_writer::end_tag('center');

        $mform->addElement('static', 'kalvid', $this->get_name(), html_writer::tag('div', $html));

        $params = array(
            'withblocks' => 0,
            'courseid' => $COURSE->id,
            'width' => KALTURA_PANEL_WIDTH,
            'height' => KALTURA_PANEL_HEIGHT,
            'cmid' => $this->assignment->get_course_module()->id
        );

        $url = new moodle_url('/mod/assign/submission/kalvid/lti_launch.php', $params);

        $params = array(
            'addvidbtnid' => 'id_add_video',
            'ltilaunchurl' => $url->out(false),
            'height' => KALTURA_PANEL_HEIGHT,
            'width' => KALTURA_PANEL_WIDTH,
            // provide kalvidres as the modulename for ltipanel.js
            // to update the button text to 'Replace video' after media is added
            // see lti_panel_change_add_media_button_caption()
            'modulename' => 'kalvidres'
        );

        $PAGE->requires->yui_module('moodle-local_kaltura-ltipanel', 'M.local_kaltura.init', array($params), null, true);

        // Require a YUI module to make the object tag be as large as possible.
        $params = array(
            'bodyclass' => $pageclass,
            'lastheight' => null,
            'padding' => 15
        );

        if (isset($submissionrec->width) && isset($submissionrec->height))
        {
            $params['width'] = $submissionrec->width;
            $params['height'] = $submissionrec->height;
        }

        $PAGE->requires->yui_module('moodle-local_kaltura-lticontainer', 'M.local_kaltura.init', array($params), null, true);

        return true;
    }

    /**
     * Save submission data to the database
     *
     * @param stdClass $submission
     * @param stdClass $data
     * @return bool
     */
    public function save(stdClass $submission, stdClass $data) {
        global $USER, $DB;

        $submissionrec = $this->get_kalvid_submission($submission->id);

        if (empty($data->entry_id) || empty($data->source)) {
            $this->set_error(get_string('emptyentryid', 'assignsubmission_kalvid'));
            return false;
        }

        $params = array(
            'context' => context_module::instance($this->assignment->get_course_module()->id),
            'courseid' => $this->assignment->get_course()->id,
            'objectid' => $submission->id,
            'other' => array(
                'pathnamehashes' => [],
                'content' => '',
            )
        );
        if (!empty($submission->userid) && ($submission->userid != $USER->id)) {
            $params['relateduserid'] = $submission->userid;
        }
        if ($this->assignment->is_blind_marking()) {
            $params['anonymous'] = 1;
        }
        $event = \assignsubmission_kalvid\event\assessable_uploaded::create($params);
        $event->trigger();

        $groupname = null;
        $groupid = 0;
        // Get the group name as other fields are not transcribed in the logs and this information is important.
        if (empty($submission->userid) && !empty($submission->groupid)) {
            $groupname = $DB->get_field('groups', 'name', array('id' => $submission->groupid), MUST_EXIST);
            $groupid = $submission->groupid;
        } else {
            $params['relateduserid'] = $submission->userid;
        }

        // Unset the objectid and other field from params for use in submission events.
        unset($params['objectid']);
        unset($params['other']);
        $params['other'] = array(
            'submissionid' => $submission->id,
            'submissionattempt' => $submission->attemptnumber,
            'submissionstatus' => $submission->status,
            'groupid' => $groupid,
            'groupname' => $groupname
        );

        if ($submissionrec) {
            $submissionrec->entry_id = $data->entry_id;
            $submissionrec->source = $data->source;
            $submissionrec->width = $data->width;
            $submissionrec->height = $data->height;
            $params['objectid'] = $submissionrec->id;
            $updatestatus = $DB->update_record('assignsubmission_kalvid', $submissionrec);
            $event = \assignsubmission_kalvid\event\submission_updated::create($params);
            $event->set_assign($this->assignment);
            $event->trigger();
            return $updatestatus;
        } else {
            $submissionrec = new stdClass();
            $submissionrec->entry_id = $data->entry_id;
            $submissionrec->source = $data->source;
            $submissionrec->width = $data->width;
            $submissionrec->height = $data->height;

            $submissionrec->submission = $submission->id;
            $submissionrec->assignment = $this->assignment->get_instance()->id;
            $submissionrec->id = $DB->insert_record('assignsubmission_kalvid', $submissionrec);
            $params['objectid'] = $submissionrec->id;
            $event = \assignsubmission_kalvid\event\submission_created::create($params);
            $event->set_assign($this->assignment);
            $event->trigger();
            return $submissionrec->id > 0;
        }
    }

    /**
     * View summary.
     *
     * @param stdClass $submission
     * @param bool $showviewlink (Mutable)
     * @return string
     */
    public function view_summary(stdClass $submission, &$showviewlink) {
        return $this->view($submission);
    }

    /**
     * Display the media in the view table
     *
     * @param stdClass $submission
     * @return string
     */
    public function view(stdClass $submission) {
        global $COURSE;
        $result = '';

        $submissionrec = $this->get_kalvid_submission($submission->id);

        if ($submissionrec) {
            $params = array(
                'courseid' => $COURSE->id,
                'height' => $submissionrec->height,
                'width' => $submissionrec->width,
                'withblocks' => 0,
                'source' => local_kaltura_add_kaf_uri_token($submissionrec->source),
                'cmid' => $this->assignment->get_course_module()->id
            );
            $url = new moodle_url('/mod/assign/submission/kalvid/lti_launch.php', $params);

            $iframeattr = [
                'id' => 'contentframe',
                'class' => 'kaltura-player-iframe',
                'src' => $url->out(false),
                'allowfullscreen' => 'true',
                'allow' => 'autoplay *; fullscreen *; encrypted-media *; camera *; microphone *; display-capture *; clipboard-write *;',
                'height' => '203px',
                'width' => '360px'
            ];

            $result = html_writer::start_tag('center', ['class' => 'm-t-2 m-b-1']);
            $iframe = html_writer::tag('iframe', '', $iframeattr);
            $result .= html_writer::tag('div', $iframe, ['class' => 'kaltura-player-container']);
            $result .= html_writer::end_tag('center');
        }

        return $result;
    }

    /**
     * The assignment has been deleted - cleanup
     *
     * @return bool
     */
    public function delete_instance() {
        global $DB;
        $DB->delete_records('assignsubmission_kalvid',
            array('assignment'=>$this->assignment->get_instance()->id));

        return true;
    }

    /**
     * Check if submission has been made
     *
     * @param stdClass $submission
     * @return bool
     */
    public function is_empty(stdClass $submission) {
        $submissionrec = $this->get_kalvid_submission($submission->id);
        return empty($submissionrec);
    }

    /**
     * Copy the student's submission from a previous submission. Used when a student opts to base their resubmission
     * on the last submission.
     * @param stdClass $sourcesubmission
     * @param stdClass $destsubmission
     */
    public function copy_submission(stdClass $sourcesubmission, stdClass $destsubmission) {
        global $DB;

        // Copy the assignsubmission_kalvid record.
        $submissionrec = $this->get_kalvid_submission($sourcesubmission->id);
        if ($submissionrec) {
            unset($submissionrec->id);
            $submissionrec->submission = $destsubmission->id;
            $DB->insert_record('assignsubmission_kalvid', $submissionrec);
        }
        return true;
    }
}