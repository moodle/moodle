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
 * Class containing data for a user's data requests.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy\output;
defined('MOODLE_INTERNAL') || die();

use action_menu;
use action_menu_link_secondary;
use coding_exception;
use context_system;
use dml_exception;
use moodle_exception;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use tool_dataprivacy\api;
use tool_dataprivacy\data_request;
use tool_dataprivacy\external\data_request_exporter;

/**
 * Class containing data for a user's data requests.
 *
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_requests_page implements renderable, templatable {

    /** @var data_request[] $requests List of data requests. */
    protected $requests = [];

    /**
     * Construct this renderable.
     *
     * @param data_request[] $requests
     */
    public function __construct($requests) {
        $this->requests = $requests;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->newdatarequesturl = new moodle_url('/admin/tool/dataprivacy/createdatarequest.php');
        $data->newdatarequesturl->param('manage', true);

        if (!is_https()) {
            $httpwarningmessage = get_string('httpwarning', 'tool_dataprivacy');
            $data->httpsite = array('message' => $httpwarningmessage, 'announce' => 1);
        }

        $requests = [];
        foreach ($this->requests as $request) {
            $requestid = $request->get('id');
            $status = $request->get('status');
            $requestexporter = new data_request_exporter($request, ['context' => context_system::instance()]);
            $item = $requestexporter->export($output);

            // Prepare actions.
            $actions = [];

            // View action.
            $actionurl = new moodle_url('#');
            $actiondata = ['data-action' => 'view', 'data-requestid' => $requestid];
            $actiontext = get_string('viewrequest', 'tool_dataprivacy');
            $actions[] = new action_menu_link_secondary($actionurl, null, $actiontext, $actiondata);

            if ($status == api::DATAREQUEST_STATUS_AWAITING_APPROVAL) {
                // Approve.
                $actiondata['data-action'] = 'approve';
                $actiontext = get_string('approverequest', 'tool_dataprivacy');
                $actions[] = new action_menu_link_secondary($actionurl, null, $actiontext, $actiondata);

                // Deny.
                $actiondata['data-action'] = 'deny';
                $actiontext = get_string('denyrequest', 'tool_dataprivacy');
                $actions[] = new action_menu_link_secondary($actionurl, null, $actiontext, $actiondata);
            }

            $actionsmenu = new action_menu($actions);
            $actionsmenu->set_menu_trigger(get_string('actions'));
            $actionsmenu->set_owner_selector('request-actions-' . $requestid);
            $actionsmenu->set_alignment(\action_menu::TL, \action_menu::BL);
            $item->actions = $actionsmenu->export_for_template($output);

            $requests[] = $item;
        }
        $data->requests = $requests;
        return $data;
    }
}
