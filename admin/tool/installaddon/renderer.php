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

        if (is_null($this->installer)) {
            throw new coding_exception('Installer instance has not been set.');
        }

        $permcheckurl = new moodle_url('/admin/tool/installaddon/permcheck.php');
        $this->page->requires->yui_module('moodle-tool_installaddon-permcheck', 'M.tool_installaddon.permcheck.init',
            array(array('permcheckurl' => $permcheckurl->out())));
        $this->page->requires->strings_for_js(
            array('permcheckprogress', 'permcheckresultno', 'permcheckresultyes', 'permcheckerror', 'permcheckrepeat'),
            'tool_installaddon');

        $out = $this->output->header();
        $out .= $this->index_page_heading();
        $out .= $this->index_page_repository();
        $out .= $this->index_page_upload();
        $out .= $this->output->footer();

        return $out;
    }

    /**
     * Inform the user that the ZIP is not a valid plugin package file.
     *
     * @param moodle_url $continueurl
     * @return string
     */
    public function zip_not_valid_plugin_package_page(moodle_url $continueurl) {

        $out = $this->output->header();
        $out .= $this->output->heading(get_string('installfromzip', 'tool_installaddon'));
        $out .= $this->output->box(get_string('installfromzipinvalid', 'tool_installaddon'), 'generalbox', 'notice');
        $out .= $this->output->continue_button($continueurl, 'get');
        $out .= $this->output->footer();

        return $out;
    }

    /**
     * Inform the user about invalid remote installation request.
     *
     * @param moodle_url $continueurl
     * @return string
     */
    public function remote_request_invalid_page(moodle_url $continueurl) {

        $out = $this->output->header();
        $out .= $this->output->heading(get_string('installfromrepo', 'tool_installaddon'));
        $out .= $this->output->box(get_string('remoterequestinvalid', 'tool_installaddon'), 'generalbox', 'notice');
        $out .= $this->output->continue_button($continueurl, 'get');
        $out .= $this->output->footer();

        return $out;
    }

    /**
     * Inform the user that such plugin is already installed
     *
     * @param stdClass $data decoded request data
     * @param moodle_url $continueurl
     * @return string
     */
    public function remote_request_alreadyinstalled_page(stdClass $data, moodle_url $continueurl) {

        $out = $this->output->header();
        $out .= $this->output->heading(get_string('installfromrepo', 'tool_installaddon'));
        $out .= $this->output->box(get_string('remoterequestalreadyinstalled', 'tool_installaddon', $data), 'generalbox', 'notice');
        $out .= $this->output->continue_button($continueurl, 'get');
        $out .= $this->output->footer();

        return $out;
    }

    /**
     * Let the user confirm the remote installation request.
     *
     * @param stdClass $data decoded request data
     * @param moodle_url $continueurl
     * @param moodle_url $cancelurl
     * @return string
     */
    public function remote_request_confirm_page(stdClass $data, moodle_url $continueurl, moodle_url $cancelurl) {

        $out = $this->output->header();
        $out .= $this->output->heading(get_string('installfromrepo', 'tool_installaddon'));
        $out .= $this->output->confirm(get_string('remoterequestconfirm', 'tool_installaddon', $data), $continueurl, $cancelurl);
        $out .= $this->output->footer();

        return $out;
    }

    /**
     * Inform the user that the target plugin type location is not writable.
     *
     * @param stdClass $data decoded request data
     * @param string $plugintypepath full path to the plugin type location
     * @param moodle_url $continueurl to repeat the write permission check
     * @param moodle_url $cancelurl to cancel the installation
     * @return string
     */
    public function remote_request_permcheck_page(stdClass $data, $plugintypepath, moodle_url $continueurl, moodle_url $cancelurl) {

        $data->typepath = $plugintypepath;

        $out = $this->output->header();
        $out .= $this->output->heading(get_string('installfromrepo', 'tool_installaddon'));
        $out .= $this->output->confirm(get_string('remoterequestpermcheck', 'tool_installaddon', $data), $continueurl, $cancelurl);
        $out .= $this->output->footer();

        return $out;
    }

    /**
     * Inform the user that the requested remote plugin is not installable.
     *
     * @param stdClass $data decoded request data with ->reason property added
     * @param moodle_url $continueurl
     * @return string
     */
    public function remote_request_non_installable_page(stdClass $data, moodle_url $continueurl) {

        $out = $this->output->header();
        $out .= $this->output->heading(get_string('installfromrepo', 'tool_installaddon'));
        $out .= $this->output->box(get_string('remoterequestnoninstallable', 'tool_installaddon', $data), 'generalbox', 'notice');
        $out .= $this->output->continue_button($continueurl, 'get');
        $out .= $this->output->footer();

        return $out;
    }

    // End of the external API /////////////////////////////////////////////////

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
