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
use moodle_exception;
use moodle_url;
use renderable;
use renderer_base;
use single_select;
use stdClass;
use templatable;
use tool_dataprivacy\data_request;
use tool_dataprivacy\local\helper;

/**
 * Class containing data for a user's data requests.
 *
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_deletion_page implements renderable, templatable {

    /** @var data_request[] $requests List of data requests. */
    protected $filter = null;

    /** @var data_request[] $requests List of data requests. */
    protected $expiredcontextstable = [];

    /**
     * Construct this renderable.
     *
     * @param \tool_dataprivacy\data_request[] $filter
     * @param expired_contexts_table $expiredcontextstable
     */
    public function __construct($filter, expired_contexts_table $expiredcontextstable) {
        $this->filter = $filter;
        $this->expiredcontextstable = $expiredcontextstable;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();

        $url = new moodle_url('/admin/tool/dataprivacy/datadeletion.php');
        $options = [
            CONTEXT_USER => get_string('user'),
            CONTEXT_COURSE => get_string('course'),
            CONTEXT_MODULE => get_string('activitiesandresources', 'tool_dataprivacy'),
            CONTEXT_BLOCK => get_string('blocks'),
        ];
        $filterselector = new single_select($url, 'filter', $options, $this->filter, null);
        $data->filter = $filterselector->export_for_template($output);

        ob_start();
        $this->expiredcontextstable->out(helper::DEFAULT_PAGE_SIZE, true);
        $expiredcontexts = ob_get_contents();
        ob_end_clean();
        $data->expiredcontexts = $expiredcontexts;

        $data->existingcontexts = $this->expiredcontextstable->rawdata ? true : false;

        return $data;
    }
}
