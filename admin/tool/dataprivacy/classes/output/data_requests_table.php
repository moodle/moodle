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
 * Contains the class used for the displaying the data requests table.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy\output;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use action_menu;
use action_menu_link_secondary;
use coding_exception;
use dml_exception;
use html_writer;
use moodle_url;
use stdClass;
use table_sql;
use tool_dataprivacy\api;
use tool_dataprivacy\external\data_request_exporter;

defined('MOODLE_INTERNAL') || die;

/**
 * The class for displaying the data requests table.
 *
 * @copyright  2018 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_requests_table extends table_sql {

    /** @var int The user ID. */
    protected $userid = 0;

    /** @var int[] The status filters. */
    protected $statuses = [];

    /** @var int[] The request type filters.  */
    protected $types = [];

    /** @var bool Whether this table is being rendered for managing data requests. */
    protected $manage = false;

    /** @var \tool_dataprivacy\data_request[] Array of data request persistents. */
    protected $datarequests = [];

    /**
     * data_requests_table constructor.
     *
     * @param int $userid The user ID
     * @param int[] $statuses
     * @param int[] $types
     * @param bool $manage
     * @throws coding_exception
     */
    public function __construct($userid = 0, $statuses = [], $types = [], $manage = false) {
        parent::__construct('data-requests-table');

        $this->userid = $userid;
        $this->statuses = $statuses;
        $this->types = $types;
        $this->manage = $manage;

        $columnheaders = [
            'type' => get_string('requesttype', 'tool_dataprivacy'),
            'userid' => get_string('user', 'tool_dataprivacy'),
            'timecreated' => get_string('daterequested', 'tool_dataprivacy'),
            'requestedby' => get_string('requestby', 'tool_dataprivacy'),
            'status' => get_string('requeststatus', 'tool_dataprivacy'),
            'comments' => get_string('message', 'tool_dataprivacy'),
            'actions' => '',
        ];

        $this->define_columns(array_keys($columnheaders));
        $this->define_headers(array_values($columnheaders));
        $this->no_sorting('actions');
    }

    /**
     * The type column.
     *
     * @param stdClass $data The row data.
     * @return string
     */
    public function col_type($data) {
        if ($this->manage) {
            return $data->typenameshort;
        }
        return $data->typename;
    }

    /**
     * The user column.
     *
     * @param stdClass $data The row data.
     * @return mixed
     */
    public function col_userid($data) {
        $user = $data->foruser;
        return html_writer::link($user->profileurl, $user->fullname, ['title' => get_string('viewprofile')]);
    }

    /**
     * The context information column.
     *
     * @param stdClass $data The row data.
     * @return string
     */
    public function col_timecreated($data) {
        return userdate($data->timecreated);
    }

    /**
     * The requesting user's column.
     *
     * @param stdClass $data The row data.
     * @return mixed
     */
    public function col_requestedby($data) {
        $user = $data->requestedbyuser;
        return html_writer::link($user->profileurl, $user->fullname, ['title' => get_string('viewprofile')]);
    }

    /**
     * The status column.
     *
     * @param stdClass $data The row data.
     * @return mixed
     */
    public function col_status($data) {
        return html_writer::span($data->statuslabel, 'label ' . $data->statuslabelclass);
    }

    /**
     * The comments column.
     *
     * @param stdClass $data The row data.
     * @return string
     */
    public function col_comments($data) {
        return shorten_text($data->comments, 60);
    }

    /**
     * The actions column.
     *
     * @param stdClass $data The row data.
     * @return string
     */
    public function col_actions($data) {
        global $OUTPUT;

        $requestid = $data->id;
        $status = $data->status;

        // Prepare actions.
        $actions = [];

        // View action.
        $actionurl = new moodle_url('#');
        $actiondata = ['data-action' => 'view', 'data-requestid' => $requestid];
        $actiontext = get_string('viewrequest', 'tool_dataprivacy');
        $actions[] = new action_menu_link_secondary($actionurl, null, $actiontext, $actiondata);

        switch ($status) {
            case api::DATAREQUEST_STATUS_PENDING:
                // Add action to mark a general enquiry request as complete.
                if ($data->type == api::DATAREQUEST_TYPE_OTHERS) {
                    $actiondata['data-action'] = 'complete';
                    $nameemail = (object)[
                        'name' => $data->foruser->fullname,
                        'email' => $data->foruser->email
                    ];
                    $actiondata['data-requestid'] = $data->id;
                    $actiondata['data-replytoemail'] = get_string('nameemail', 'tool_dataprivacy', $nameemail);
                    $actiontext = get_string('markcomplete', 'tool_dataprivacy');
                    $actions[] = new action_menu_link_secondary($actionurl, null, $actiontext, $actiondata);
                }
                break;
            case api::DATAREQUEST_STATUS_AWAITING_APPROVAL:
                // Approve.
                $actiondata['data-action'] = 'approve';
                $actiontext = get_string('approverequest', 'tool_dataprivacy');
                $actions[] = new action_menu_link_secondary($actionurl, null, $actiontext, $actiondata);

                // Deny.
                $actiondata['data-action'] = 'deny';
                $actiontext = get_string('denyrequest', 'tool_dataprivacy');
                $actions[] = new action_menu_link_secondary($actionurl, null, $actiontext, $actiondata);
                break;
            case api::DATAREQUEST_STATUS_DOWNLOAD_READY:
                $userid = $data->foruser->id;
                $usercontext = \context_user::instance($userid, IGNORE_MISSING);
                // If user has permission to view download link, show relevant action item.
                if ($usercontext && api::can_download_data_request_for_user($userid, $data->requestedbyuser->id)) {
                    $actions[] = api::get_download_link($usercontext, $requestid);
                }
                break;
        }

        $actionsmenu = new action_menu($actions);
        $actionsmenu->set_menu_trigger(get_string('actions'));
        $actionsmenu->set_owner_selector('request-actions-' . $requestid);
        $actionsmenu->set_alignment(\action_menu::TL, \action_menu::BL);
        $actionsmenu->set_constraint('[data-region=data-requests-table] > .no-overflow');

        return $OUTPUT->render($actionsmenu);
    }

    /**
     * Query the database for results to display in the table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     * @throws dml_exception
     * @throws coding_exception
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        global $PAGE;

        // Set dummy page total until we fetch full result set.
        $this->pagesize($pagesize, $pagesize + 1);

        $sort = $this->get_sql_sort();

        // Get data requests from the given conditions.
        $datarequests = api::get_data_requests($this->userid, $this->statuses, $this->types, $sort,
                $this->get_page_start(), $this->get_page_size());

        // Count data requests from the given conditions.
        $total = api::get_data_requests_count($this->userid, $this->statuses, $this->types);
        $this->pagesize($pagesize, $total);

        $this->rawdata = [];
        $context = \context_system::instance();
        $renderer = $PAGE->get_renderer('tool_dataprivacy');

        foreach ($datarequests as $persistent) {
            $this->datarequests[$persistent->get('id')] = $persistent;
            $exporter = new data_request_exporter($persistent, ['context' => $context]);
            $this->rawdata[] = $exporter->export($renderer);
        }

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars($total > $pagesize);
        }
    }

    /**
     * Override default implementation to display a more meaningful information to the user.
     */
    public function print_nothing_to_display() {
        global $OUTPUT;
        echo $this->render_reset_button();
        $this->print_initials_bar();
        if (!empty($this->statuses) || !empty($this->types)) {
            $message = get_string('nodatarequestsmatchingfilter', 'tool_dataprivacy');
        } else {
            $message = get_string('nodatarequests', 'tool_dataprivacy');
        }
        echo $OUTPUT->notification($message, 'warning');
    }

    /**
     * Override the table's show_hide_link method to prevent the show/hide links from rendering.
     *
     * @param string $column the column name, index into various names.
     * @param int $index numerical index of the column.
     * @return string HTML fragment.
     */
    protected function show_hide_link($column, $index) {
        return '';
    }
}
