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
 * Class containing data for the data registry defaults.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy\output;
defined('MOODLE_INTERNAL') || die();

use action_menu_link_primary;
use coding_exception;
use moodle_exception;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;
use tool_dataprivacy\data_registry;
use tool_dataprivacy\external\category_exporter;
use tool_dataprivacy\external\purpose_exporter;

/**
 * Class containing data for the data registry defaults.
 *
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class defaults_page implements renderable, templatable {

    /** @var int $mode The display mode. */
    protected $mode = null;

    /** @var int $category The default category for the given mode. */
    protected $category = null;

    /** @var int $purpose The default purpose for the given mode. */
    protected $purpose = null;

    /** @var stdClass[] $otherdefaults Other defaults for the given mode. */
    protected $otherdefaults = [];

    /** @var bool $canedit Whether editing is allowed. */
    protected $canedit = false;

    /**
     * Construct this renderable.
     *
     * @param int $mode The display mode.
     * @param int $category The default category for the given mode.
     * @param int $purpose The default purpose for the given mode.
     * @param stdClass[] $otherdefaults Other defaults for the given mode.
     * @param bool $canedit Whether editing is allowed.
     */
    public function __construct($mode, $category, $purpose, $otherdefaults = [], $canedit = false) {
        $this->mode = $mode;
        $this->category = $category;
        $this->purpose = $purpose;
        $this->otherdefaults = $otherdefaults;
        $this->canedit = $canedit;
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

        // Set tab URLs.
        $coursecaturl = new moodle_url('/admin/tool/dataprivacy/defaults.php', ['mode' => CONTEXT_COURSECAT]);
        $courseurl = new moodle_url('/admin/tool/dataprivacy/defaults.php', ['mode' => CONTEXT_COURSE]);
        $moduleurl = new moodle_url('/admin/tool/dataprivacy/defaults.php', ['mode' => CONTEXT_MODULE]);
        $blockurl = new moodle_url('/admin/tool/dataprivacy/defaults.php', ['mode' => CONTEXT_BLOCK]);
        $data->coursecaturl = $coursecaturl;
        $data->courseurl = $courseurl;
        $data->moduleurl = $moduleurl;
        $data->blockurl = $blockurl;

        // Set display mode.
        switch ($this->mode) {
            case CONTEXT_COURSECAT:
                $data->modecoursecat = true;
                break;
            case CONTEXT_COURSE:
                $data->modecourse = true;
                break;
            case CONTEXT_MODULE:
                $data->modemodule = true;
                break;
            case CONTEXT_BLOCK:
                $data->modeblock = true;
                break;
            default:
                $data->modecoursecat = true;
                break;
        }

        // Set config variables.
        $configname = \context_helper::get_class_for_level($this->mode);
        list($purposevar, $categoryvar) = data_registry::var_names_from_context($configname);
        $data->categoryvar = $categoryvar;
        $data->purposevar = $purposevar;

        // Set default category.
        $data->categoryid = $this->category;
        $data->category = category_exporter::get_name($this->category);

        // Set default purpose.
        $data->purposeid = $this->purpose;
        $data->purpose = purpose_exporter::get_name($this->purpose);

        // Set other defaults.
        $otherdefaults = [];
        $url = new moodle_url('#');
        foreach ($this->otherdefaults as $pluginname => $values) {
            $defaults = [
                'name' => $values->name,
                'category' => category_exporter::get_name($values->category),
                'purpose' => purpose_exporter::get_name($values->purpose),
            ];
            if ($this->canedit) {
                $actions = [];
                // Edit link.
                $editattrs = [
                    'data-action' => 'edit-activity-defaults',
                    'data-contextlevel' => $this->mode,
                    'data-activityname' => $pluginname,
                    'data-category' => $values->category,
                    'data-purpose' => $values->purpose,
                ];
                $editlink = new action_menu_link_primary($url, new \pix_icon('t/edit', get_string('edit')),
                    get_string('edit'), $editattrs);
                $actions[] = $editlink->export_for_template($output);

                // Delete link.
                $deleteattrs = [
                    'data-action' => 'delete-activity-defaults',
                    'data-contextlevel' => $this->mode,
                    'data-activityname' => $pluginname,
                    'data-activitydisplayname' => $values->name,
                ];
                $deletelink = new action_menu_link_primary($url, new \pix_icon('t/delete', get_string('delete')),
                    get_string('delete'), $deleteattrs);
                $actions[] = $deletelink->export_for_template($output);

                $defaults['actions'] = $actions;
            }
            $otherdefaults[] = (object)$defaults;
        }
        $data->otherdefaults = $otherdefaults;

        $data->canedit = $this->canedit;
        $data->contextlevel = $this->mode;

        return $data;
    }
}
