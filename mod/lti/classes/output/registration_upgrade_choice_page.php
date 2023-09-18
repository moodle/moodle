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
 * Class containing data for rendering LTI upgrade choices page.
 *
 * @copyright  2021 Cengage
 * @package    mod_lti
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_lti\output;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/mod/lti/locallib.php');

use renderable;
use templatable;
use renderer_base;
use stdClass;

/**
 * Class containing data for rendering LTI upgrade choices page.
 *
 * @copyright  2021 Cengage
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class registration_upgrade_choice_page implements renderable, templatable {

    /** @var array array of tools. */
    protected array $tools = [];

    /** @var string tool URL. */
    protected string $startregurl;

    /**
     * Constructor
     *
     * @param array $tools array of tools that can be upgraded
     * @param string $startregurl tool URL to start the registration process
     */
    public function __construct(array $tools, string $startregurl) {
        $this->tools = $tools;
        $this->startregurl = $startregurl;
    }
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output The renderer
     * @return stdClass Data to be used by the template
     */
    public function export_for_template(renderer_base $output) {
        $renderdata = new stdClass();
        $renderdata->startregurlenc = urlencode($this->startregurl);
        $renderdata->sesskey = sesskey();
        $renderdata->tools = [];
        foreach ($this->tools as $tool) {
            $renderdata->tools[] = (object)$tool;
        }
        return $renderdata;
    }
}
