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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\components;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\components\component;
use block_quickmail_string;
use moodle_url;

class sent_message_index_component extends component implements \renderable {

    public $messages;
    public $pagination;
    public $user;
    public $course_id;
    public $sort_by;
    public $sort_dir;
    public $user_course_array;
    public $sent_edit_mode;

    public function __construct($params = []) {
        parent::__construct($params);
        $this->messages = $this->get_param('messages');
        $this->pagination = $this->get_param('pagination');
        $this->user = $this->get_param('user');
        $this->course_id = $this->get_param('course_id');
        $this->sort_by = $this->get_param('sort_by');
        $this->sort_dir = $this->get_param('sort_dir');
        $this->user_course_array = $this->get_param('user_course_array');
        $this->sent_edit_mode = $this->get_param('sent_edit');
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template($output) {
        $data = (object)[];

        $data->userCourseArray = $this->transform_course_array($this->user_course_array, $this->course_id);
        $data->courseId = $this->course_id;
        $data->sortBy = $this->sort_by;
        $data->isSortedAsc = $this->sort_dir == 'asc';
        $data->courseIsSorted = $this->is_attr_sorted('course');
        $data->subjectIsSorted = $this->is_attr_sorted('subject');
        $data->sentAtIsSorted = $this->is_attr_sorted('sent');
        $data->toggleSentEdit = $this->sent_edit_mode ? $this->sent_edit_mode : null;
        $data = $this->include_pagination($data, $this->pagination);

        $data->tableRows = [];

        foreach ($this->messages as $message) {
            $data->tableRows[] = [
                'id' => $message->get('id'),
                'courseName' => $message->get_course_property('shortname', ''),
                'subjectPreview' => $message->get_subject_preview(24),
                'messagePreview' => $message->get_body_preview(),
                'attachmentTotal' => $message->cached_attachment_count(),
                'recipientTotal' => $message->cached_recipient_count(),
                'additionalEmailTotal' => $message->cached_additional_email_count(),
                'sentAt' => $message->get_readable_date('sent_at'),
                'openUrl' => (new moodle_url('/blocks/quickmail/message.php', [
                    'id' => $message->get('id'),
                ]))->out(false)
            ];
        }

        $data->urlBack = $this->course_id
            ? new moodle_url('/course/view.php', ['id' => $this->course_id])
            : new moodle_url('/my');

        $data->urlBackLabel = $this->course_id
            ? block_quickmail_string::get('back_to_course')
            : block_quickmail_string::get('back_to_mypage');

        return $data;
    }

}
