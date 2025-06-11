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

class broadcast_recipient_filter_results_component extends component implements \renderable {

    public $broadcastrecipientfilter;
    public $displayusers;
    public $sortby;
    public $sortdir;

    public function __construct($params = []) {
        parent::__construct($params);

        $this->broadcast_recipient_filter = $this->get_param('broadcast_recipient_filter');
        $this->result_user_count = $this->broadcast_recipient_filter->get_result_user_count();
        $this->display_users = $this->broadcast_recipient_filter->display_users;
        $this->draft_id = $this->broadcast_recipient_filter->get_draft_id();
        $this->page = $this->broadcast_recipient_filter->filter_params['page'];
        $this->sort_by = $this->broadcast_recipient_filter->filter_params['sort_by'];
        $this->sort_dir = $this->broadcast_recipient_filter->filter_params['sort_dir'];
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template($output) {
        $data = (object)[];

        $data->foundUsersHeadingText = block_quickmail_string::get('found_filtered_users', $this->result_user_count);
        $data->baseSortQueryString = '?draftid=' . $this->draft_id . '&page=' . $this->page;
        $data->sortBy = $this->sort_by;
        $data->isSortedAsc = $this->sort_dir == 'asc';
        $data->firstnameIsSorted = $this->is_attr_sorted('firstname');
        $data->lastnameIsSorted = $this->is_attr_sorted('lastname');
        $data->emailIsSorted = $this->is_attr_sorted('email');
        $data->cityIsSorted = $this->is_attr_sorted('city');
        $data->lastaccessIsSorted = $this->is_attr_sorted('lastaccess');

        $data->tableRows = [];

        foreach ($this->display_users as $user) {
            $data->tableRows[] = [
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'city' => $user->city,
                'lastaccess' => $this->format_last_access($user->lastaccess),
            ];
        }

        return $data;
    }

    private function format_last_access($lastaccess) {
        return isset($lastaccess) ? format_time(time() - $lastaccess) : get_string('never');
    }
}
