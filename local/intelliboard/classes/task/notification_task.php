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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intelliboard
 * @copyright  2018 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace local_intelliboard\task;

class notification_task extends \core\task\adhoc_task {

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG;
        require_once($CFG->dirroot .'/local/intelliboard/locallib.php');

        $data = $this->get_custom_data();

        $notification = $data->notification;
        $recipient = $data->recipient;

        $sender = \get_admin();
        $sender->firstname = 'Intelliboard';
        $sender->lastname = '';
        $sender->maildisplay = true;
        $old = $CFG->emailfromvia;
        $oldCharset = $CFG->sitemailcharset;
        $CFG->emailfromvia = EMAIL_VIA_NEVER;
        $CFG->sitemailcharset = 'utf-8';

        $eventdata = new \core\message\message();

        $plaintext = format_text_email($notification->message, FORMAT_HTML);
        $eventdata->userfrom         = $sender;
        $eventdata->userto           = $recipient;
        $eventdata->subject          = $notification->subject;
        $eventdata->fullmessage      = $plaintext;
        $eventdata->fullmessageformat = FORMAT_HTML;
        $eventdata->fullmessagehtml  = $notification->message;
        $eventdata->smallmessage     = '';
        $eventdata->notification     = '1';
        $eventdata->component = 'local_intelliboard';
        $eventdata->name = 'intelliboard_notification';
        $eventdata->courseid = SITEID;

        if ($notification->attachment) {

            $filename = clean_filename('export' . round(microtime(true) * 1000) . '.' . (in_array($notification->attachmentType, ['xlsx', 'xls']) ? 'xlsx' : $notification->attachmentType));

            if (!empty($notification->attachment->body)) {
                $notification->attachment->body = array_map(function($row) {
                    return is_object($row)? (array) $row : $row;
                }, $notification->attachment->body);
            }

            $notification->attachment = @intelliboard_export_report($notification->attachment, $filename, $notification->attachmentType, 2);
            if ($notification->attachmentType === 'csv') {
                $notification->attachment = str_replace('"', '', $notification->attachment);
            }
            $usercontext = \context_user::instance($sender->id);
            $file = new \stdClass;
            $file->component = 'user';
            $file->filearea  = 'private';
            $file->itemid    = 0;
            $file->filepath  = '/';
            $file->filename  = $filename;
            $file->contextid = $usercontext->id;

            $filecontents = $notification->attachment;
            $fs = get_file_storage();
            $file = $fs->create_file_from_string($file, $filecontents);

            $eventdata->attachment = $file;
            $eventdata->attachname = $filename;
        }

        message_send($eventdata);

        \local_intelliboard_notification::save_history($recipient, $notification);

        $CFG->emailfromvia = $old;
        $CFG->sitemailcharset = $oldCharset;
    }

}
