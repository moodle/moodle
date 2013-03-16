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
 * Output rendering for the plugin.
 *
 * @package     tool_installaddon
 * @category    output
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Implements the plugin renderer
 *
 * @copyright 2013 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_installaddon_renderer extends plugin_renderer_base {

    /** @var tool_installaddon_installer */
    protected $installer = null;

    /**
     * Sets the tool_installaddon_installer instance being used.
     *
     * @throws coding_exception if the installer has been already set
     * @param tool_installaddon_installer $installer
     */
    public function set_installer_instance(tool_installaddon_installer $installer) {
        if (is_null($this->installer)) {
            $this->installer = $installer;
        } else {
            throw new coding_exception('Attempting to reset the installer instance.');
        }
    }

    /**
     * Defines the index page layout
     *
     * @return string
     */
    public function index_page() {

        $out = $this->output->header();
        $out .= $this->index_page_heading();
        $out .= $this->index_page_repository();
        $out .= $this->index_page_upload();
        $out .= $this->output->footer();

        return $out;
    }

    /**
     * Renders the index page heading
     *
     * @return string
     */
    protected function index_page_heading() {
        return $this->output->heading(get_string('pluginname', 'tool_installaddon'));
    }

    /**
     * Renders the widget for browsing the add-on repository
     *
     * @return string
     */
    protected function index_page_repository() {

        $url = $this->installer->get_addons_repository_url();

        $out = $this->box(
            $this->output->single_button($url, get_string('installfromrepo', 'tool_installaddon'), 'get').
            $this->output->help_icon('installfromrepo', 'tool_installaddon'),
            'generalbox', 'installfromrepobox'
        );

        return $out;
    }

    /**
     * Renders the widget for uploading the add-on ZIP package
     *
     * @return string
     */
    protected function index_page_upload() {

        $form = $this->installer->get_installfromzip_form();

        ob_start();
        $form->display();
        $out = ob_get_clean();

        $out = $this->box($out, 'generalbox', 'installfromzipbox');

        return $out;
    }
}
