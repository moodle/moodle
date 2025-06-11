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

class notification_index_component extends component implements \renderable {

    public $notifications;
    public $pagination;
    public $user;
    public $courseid;
    public $sortby;
    public $sortdir;

    public function __construct($params = []) {
        parent::__construct($params);
        $this->notifications = $this->get_param('notifications');
        $this->pagination = $this->get_param('pagination');
        $this->user = $this->get_param('user');
        $this->courseid = $this->get_param('courseid');
        $this->sort_by = $this->get_param('sort_by');
        $this->sort_dir = $this->get_param('sort_dir');
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template($output) {
        $data = (object)[];

        $data->courseId = $this->courseid;
        $data->sortBy = $this->sort_by;
        $data->isSortedAsc = $this->sort_dir == 'asc';
        $data->nameIsSorted = $this->is_attr_sorted('name');
        $data->modelIsSorted = $this->is_attr_sorted('model');
        $data->enabledIsSorted = $this->is_attr_sorted('enabled');

        $data = $this->include_pagination($data, $this->pagination);

        $data->tableRows = [];

        foreach ($this->notifications as $notification) {
            // Get the notification type interface for each notification.
            // @TODO - Possibly cache some of this data for better performance?
            $notificationinterface = $notification->get_notification_type_interface();

            $editurl = new moodle_url('/blocks/quickmail/edit_notification.php',
                                         ['courseid' => $this->courseid, 'id' => $notification->get('id')]);

            $data->tableRows[] = [
                'notificationId' => $notification->get('id'),
                'modelDescription' => $notificationinterface->get_title(),
                'name' => $notification->get('name'),
                'isEnabled' => $notification->is_notification_enabled(),
                'lastRunAt' => $notification->get('type') == 'reminder'
                    ? $notificationinterface->get_readable_date('last_run_at')
                    : $notificationinterface->cached_last_fired_at(true),
                'nextRunAt' => $notification->get('type') == 'reminder'
                               ? $notificationinterface->get_readable_date('next_run_at') : '',
                'editUrl' => $editurl->out(false),
            ];
        }

        $data->urlBack = new moodle_url('/course/view.php', ['id' => $this->courseid]);
        $data->urlBackLabel = block_quickmail_string::get('back_to_course');
        $data->urlCreateNew = new moodle_url('/blocks/quickmail/create_notification.php', ['courseid' => $this->courseid]);
        $data->urlCreateNewLabel = block_quickmail_string::get('create_notification');

        return $data;
    }

}
