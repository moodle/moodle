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
 * User evidence page output.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\output;

use moodle_url;
use renderable;
use templatable;
use stdClass;
use core_competency\api;
use tool_lp\external\user_evidence_summary_exporter;

/**
 * User evidence page class.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_evidence_page implements renderable, templatable {

    /** @var context The context. */
    protected $context;

    /** @var userevidence The user evidence. */
    protected $userevidence;

    /**
     * Construct.
     *
     * @param user_evidence $userevidence
     */
    public function __construct($userevidence) {
        $this->userevidence = $userevidence;
        $this->context = $this->userevidence->get_context();
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(\renderer_base $output) {
        $data = new stdClass();

        $userevidencesummaryexporter = new user_evidence_summary_exporter($this->userevidence, array(
            'context' => $this->context));
        $data->userevidence = $userevidencesummaryexporter->export($output);
        $data->pluginbaseurl = (new moodle_url('/admin/tool/lp'))->out(true);

        return $data;
    }
}
