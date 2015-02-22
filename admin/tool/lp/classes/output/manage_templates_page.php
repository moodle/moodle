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

use renderable;
use templatable;
use renderer_base;
use single_button;
use stdClass;
use moodle_url;
use context_system;
use tool_lp\api;

/**
 * Class containing data for managecompetencyframeworks page
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manage_templates_page implements renderable, templatable {

    /** @var array $navigation List of links to display on the page. Each link contains a url and a title. */
    var $navigation = array();

    /** @var array $templates List of learning plan templates. */
    var $templates = array();

    /** @var bool $canmanage Result of permissions checks. */
    var $canmanage = false;

    /**
     * Construct this renderable.
     */
    public function __construct() {
        $addpage = new single_button(
           new moodle_url('/admin/tool/lp/edittemplate.php'),
           get_string('addnewtemplate', 'tool_lp')
        );
        $this->navigation[] = $addpage;

        $this->templates = api::list_templates(array(), 'sortorder', 'ASC', 0, 0);

        $context = context_system::instance();
        $this->canmanage = has_capability('tool/lp:learningplanmanage', $context);
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->canmanage = $this->canmanage;
        $data->templates = array();
        foreach ($this->templates as $template) {
            $record = $template->to_record();
            $data->templates[] = $record;
        }
        $data->pluginbaseurl = (new moodle_url('/admin/tool/lp'))->out(true);
        $data->navigation = array();
        foreach ($this->navigation as $button) {
            $data->navigation[] = $output->render($button);
        }

        return $data;
    }
}
