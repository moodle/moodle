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
 * tool_admin_presets export and import option renderer
 *
 * @package   tool_admin_presets
 * @copyright  2021 Amaia Anabitarte <amaia@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_admin_presets\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;
/**
 * Class containing data for export and import template
 *
 * @copyright  2021 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class export_import implements renderable, templatable {
    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $context = new stdClass();
        $exportlink = new \moodle_url('/admin/tool/admin_presets/index.php', ['action' => 'export']);
        $exportbutton = new \single_button($exportlink, get_string('actionexport', 'tool_admin_presets'), 'get');
        $context->export = $exportbutton->export_for_template($output);

        $importlink = new \moodle_url('/admin/tool/admin_presets/index.php', ['action' => 'import']);
        $importbutton = new \single_button($importlink, get_string('actionimport', 'tool_admin_presets'), 'get');
        $context->import = $importbutton->export_for_template($output);
        return $context;
    }
}
