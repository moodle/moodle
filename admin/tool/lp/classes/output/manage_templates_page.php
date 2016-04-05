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
 * Class containing data for managelearningplans page
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp\output;
defined('MOODLE_INTERNAL') || die();

use context;
use renderable;
use templatable;
use renderer_base;
use single_button;
use stdClass;
use moodle_url;
use context_system;
use core_competency\api;
use core_competency\template;
use core_competency\external\template_exporter;

/**
 * Class containing data for managecompetencyframeworks page
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manage_templates_page implements renderable, templatable {

    /** @var context The context in which everything is happening. */
    protected $pagecontext;

    /** @var array $navigation List of links to display on the page. Each link contains a url and a title. */
    protected $navigation = array();

    /** @var array $templates List of learning plan templates. */
    protected $templates = array();

    /**
     * Construct this renderable.
     * @param context $pagecontext
     */
    public function __construct(context $pagecontext) {
        $this->pagecontext = $pagecontext;

        if (template::can_manage_context($this->pagecontext)) {
            $addpage = new single_button(
               new moodle_url('/admin/tool/lp/edittemplate.php', array('pagecontextid' => $this->pagecontext->id)),
               get_string('addnewtemplate', 'tool_lp'),
               'get'
            );
            $this->navigation[] = $addpage;
        }

        $this->templates = api::list_templates('shortname', 'ASC', 0, 0, $this->pagecontext);
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->pagecontextid = $this->pagecontext->id;
        $data->templates = array();
        foreach ($this->templates as $template) {
            $exporter = new template_exporter($template);
            $data->templates[] = $exporter->export($output);
        }
        $data->pluginbaseurl = (new moodle_url('/admin/tool/lp'))->out(true);
        $data->navigation = array();
        foreach ($this->navigation as $button) {
            $data->navigation[] = $output->render($button);
        }
        $data->canmanage = template::can_manage_context($this->pagecontext);

        return $data;
    }
}
