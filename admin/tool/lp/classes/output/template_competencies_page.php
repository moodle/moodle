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
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;
use moodle_url;
use context;
use tool_lp\api;

/**
 * Class containing data for learning plan template competencies page
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_competencies_page implements renderable, templatable {

    /** @var int $templateid Template id for this page. */
    protected $templateid = null;

    /** @var \tool_lp\competency[] $competencies List of competencies. */
    protected $competencies = array();

    /** @var bool $canmanagecompetencyframeworks Can the current user manage competency frameworks. */
    protected $canmanagecompetencyframeworks = false;

    /** @var bool $canmanagecoursecompetencies Can the current user manage course competency frameworks.. */
    protected $canmanagecoursecompetencies = false;

    /** @var string $manageurl manage url. */
    protected $manageurl = null;

    /** @var context $pagecontext The page context. */
    protected $pagecontext = null;

    /**
     * Construct this renderable.
     *
     * @param int $templateid The learning plan template id for this page.
     */
    public function __construct($templateid, context $pagecontext) {
        $this->pagecontext = $pagecontext;
        $this->templateid = $templateid;
        $this->competencies = api::list_competencies_in_template($templateid);
        $this->canmanagecompetencyframeworks = has_capability('tool/lp:competencymanage', $this->pagecontext);
        $this->canmanagetemplatecompetencies = has_capability('tool/lp:templatemanage', $this->pagecontext);
        $this->manageurl = new moodle_url('/admin/tool/lp/competencyframeworks.php',
            array('pagecontextid' => $this->pagecontext->id));
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->pagecontextid = $this->pagecontext->id;
        $data->templateid = $this->templateid;
        $data->competencies = array();
        foreach ($this->competencies as $competency) {
            $record = $competency->to_record();
            array_push($data->competencies, $record);
        }
        $data->canmanagecompetencyframeworks = $this->canmanagecompetencyframeworks;
        $data->canmanagetemplatecompetencies = $this->canmanagetemplatecompetencies;
        $data->manageurl = $this->manageurl->out(true);

        return $data;
    }
}
