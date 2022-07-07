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
 * Renderer for 'tool_licensemanager' component.
 *
 * @package    tool_licensemanager
 * @copyright  Tom Dickman <tomdickman@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_licensemanager\output;

defined('MOODLE_INTERNAL') || die();

use license_manager;
use plugin_renderer_base;
use tool_licensemanager\helper;

/**
 * Renderer class for 'tool_licensemanager' component.
 *
 * @package    tool_licensemanager
 * @copyright  2019 Tom Dickman <tomdickman@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Render the headers for create license form.
     *
     * @return string html fragment for display.
     */
    public function render_create_licence_headers() : string {

        $this->page->navbar->add(get_string('createlicense', 'tool_licensemanager'),
            helper::get_create_license_url());

        $return = $this->header();
        $return .= $this->heading(get_string('createlicense', 'tool_licensemanager'));

        return $return;
    }

    /**
     * Render the headers for edit license form.
     *
     * @param string $licenseshortname the shortname of license to edit.
     *
     * @return string html fragment for display.
     */
    public function render_edit_licence_headers(string $licenseshortname) : string {

        $this->page->navbar->add(get_string('editlicense', 'tool_licensemanager'),
            helper::get_update_license_url($licenseshortname));

        $return = $this->header();
        $return .= $this->heading(get_string('editlicense', 'tool_licensemanager'));

        return $return;
    }

    /**
     * Render the license manager table.
     *
     * @param \renderable $table the renderable.
     *
     * @return string HTML.
     */
    public function render_table(\renderable $table) {
        $licenses = license_manager::get_licenses();

        // Add the create license button.
        $html = $table->create_license_link();

        // Add the table containing licenses for management.
        $html .= $this->box_start('generalbox editorsui');
        $html .= $table->create_license_manager_table($licenses, $this);
        $html .= $this->box_end();

        return $html;
    }
}
