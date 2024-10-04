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
 * User navigation class.
 *
 * @package    tool_lp
 * @copyright  2019 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use context_course;
use core_course\external\course_module_summary_exporter;
use stdClass;

/**
 * User course navigation class.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module_navigation implements renderable, templatable {

    /** @var courseid */
    protected $courseid;

    /** @var moduleid */
    protected $moduleid;

    /** @var baseurl */
    protected $baseurl;

    /**
     * Construct.
     *
     * @param int $courseid
     * @param int $moduleid
     * @param string $baseurl
     */
    public function __construct($courseid, $moduleid, $baseurl) {
        $this->courseid = $courseid;
        $this->moduleid = $moduleid;
        $this->baseurl = $baseurl;
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {

        $context = context_course::instance($this->courseid);

        $data = new stdClass();
        $data->courseid = $this->courseid;
        $data->moduleid = $this->moduleid;
        $data->baseurl = $this->baseurl;
        $data->hasmodules = false;
        $data->modules = array();

        $data->hasmodules = true;
        $data->modules = array();
        $empty = (object)['id' => 0, 'name' => get_string('nofiltersapplied')];
        $data->modules[] = $empty;

        $modinfo = get_fast_modinfo($this->courseid);
        foreach ($modinfo->get_cms() as $cm) {
            if ($cm->uservisible) {
                $exporter = new course_module_summary_exporter(null, ['cm' => $cm]);
                $module = $exporter->export($output);
                if ($module->id == $this->moduleid) {
                    $module->selected = true;
                }
                $data->modules[] = $module;
                $data->hasmodules = true;
            }
        }

        return $data;
    }
}
