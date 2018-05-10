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
 * Purposes renderable.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use stdClass;
use templatable;
use tool_dataprivacy\external\purpose_exporter;

/**
 * Class containing the purposes page renderable.
 *
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class purposes extends crud_element implements renderable, templatable {

    /** @var array $purposes All system purposes. */
    protected $purposes = [];

    /**
     * Construct this renderable.
     *
     * @param \tool_dataprivacy\purpose[] $purposes
     */
    public function __construct($purposes) {
        $this->purposes = $purposes;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $PAGE;

        $context = \context_system::instance();

        $PAGE->requires->js_call_amd('tool_dataprivacy/purposesactions', 'init');
        $PAGE->requires->js_call_amd('tool_dataprivacy/add_purpose', 'getInstance', [$context->id]);

        $data = new stdClass();

        // Navigation links.
        $data->navigation = [];
        $navigationlinks = $this->get_navigation();
        foreach ($navigationlinks as $navlink) {
            $data->navigation[] = $navlink->export_for_template($output);
        }

        $data->purposes = [];
        foreach ($this->purposes as $purpose) {
            $exporter = new purpose_exporter($purpose, ['context' => \context_system::instance()]);
            $exportedpurpose = $exporter->export($output);

            $actionmenu = $this->action_menu('purpose', $exportedpurpose, $purpose);
            $exportedpurpose->actions = $actionmenu->export_for_template($output);
            $data->purposes[] = $exportedpurpose;
        }

        return $data;
    }
}
