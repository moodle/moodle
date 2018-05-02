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

use coding_exception;
use dml_exception;
use moodle_exception;
use moodle_url;
use renderable;
use renderer_base;
use single_select;
use stdClass;
use templatable;
use tool_dataprivacy\api;
use tool_dataprivacy\local\helper;

/**
 * Class containing data for a user's data requests.
 *
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_requests_page implements renderable, templatable {

    /** @var data_requests_table $table The data requests table. */
    protected $table;

    /** @var int[] $filters The applied filters. */
    protected $filters = [];

    /**
     * Construct this renderable.
     *
     * @param data_requests_table $table The data requests table.
     * @param int[] $filters The applied filters.
     */
    public function __construct($table, $filters) {
        $this->table = $table;
        $this->filters = $filters;
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

        $url = new moodle_url('/admin/tool/dataprivacy/datarequests.php');
        $filteroptions = helper::get_request_filter_options();
        $filter = new request_filter($filteroptions, $this->filters, $url);
        $data->filter = $filter->export_for_template($output);

        ob_start();
        $this->table->out(helper::DEFAULT_PAGE_SIZE, true);
        $requests = ob_get_contents();
        ob_end_clean();

        $data->datarequests = $requests;
        return $data;
    }
}
