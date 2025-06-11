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
 * Class containing data for learning plan template competencies page
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;
use context;
use core_competency\api as core_competency_api;
use core_competency\external\template_exporter;
use report_lpmonitoring\api as report_lpmonitoring_api;

/**
 * Class containing data for learning plan template competencies page
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report implements renderable, templatable {

    /** @var context The context in which everything is happening. */
    protected $pagecontext;

    /** @var array $learningplantemplates List of learning plan templates. */
    protected $learningplantemplates = [];

    /**
     * Construct this renderable.
     *
     * @param context $pagecontext The page context
     */
    public function __construct(context $pagecontext) {
        $this->pagecontext = $pagecontext;
        $this->learningplantemplates = core_competency_api::list_templates('shortname', 'ASC', 0, 0, $this->pagecontext,
                'children', true);
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $url = new \moodle_url('/report/lpmonitoring/bulkrating.php', ['pagecontextid' => $this->pagecontext->id]);
        $data = new stdClass();
        $data->pagecontextid = $this->pagecontext->id;
        $data->urlbulkrating = $url->out(true);
        $data->cmcompgradingenabled = report_lpmonitoring_api::is_cm_comptency_grading_enabled();

        $data->templates = [];
        foreach ($this->learningplantemplates as $template) {
            $exporter = new template_exporter($template);
            $data->templates[] = $exporter->export($output);
        }

        return $data;
    }
}
