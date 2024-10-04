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
 * Renderer class for template library.
 *
 * @package    mod_lti
 * @copyright  2015 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_lti\output;

defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;

/**
 * Renderer class for template library.
 *
 * @package    mod_lti
 * @copyright  2015 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Defer to template.
     *
     * @param tool_configure_page $page
     *
     * @return string html for the page
     */
    public function render_tool_configure_page($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('mod_lti/tool_configure', $data);
    }

    /**
     * Render the external registration return page
     *
     * @param tool_configure_page $page
     *
     * @return string html for the page
     */
    public function render_external_registration_return_page($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('mod_lti/external_registration_return', $data);
    }

    /**
     * Render the external registration return page
     *
     * @param tool_configure_page $page
     *
     * @return string html for the page
     */
    public function render_registration_upgrade_choice_page($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('mod_lti/registration_upgrade_choice_page', $data);
    }

    /**
     * Render the reposting of the cross site request.
     *
     * @param repost_crosssite_page $page the page renderable.
     *
     * @return string rendered html for the page.
     */
    public function render_repost_crosssite_page(repost_crosssite_page $page): string {
        $data = $page->export_for_template($this);
        return parent::render_from_template('mod_lti/repost_crosssite', $data);
    }

    /**
     * Render the course tools page header.
     *
     * @param course_tools_page $page the page renderable.
     * @return string the rendered html for the page.
     */
    protected function render_course_tools_page(course_tools_page $page): string {

        // Render the table header templatable + the report.
        $headerrenderable = $page->get_header();
        $table = $page->get_table();
        $headercontext = $headerrenderable->export_for_template($this);
        $headeroutput = parent::render_from_template('mod_lti/course_tools_page_header', $headercontext);

        return $headeroutput . $table->output();
    }
}
